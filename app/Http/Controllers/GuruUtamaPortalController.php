<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Siswa;
use App\Services\SimpleSpreadsheetReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruUtamaPortalController extends Controller
{
    /**
     * Dapatkan model GuruUtama dari user yang sedang login.
     */
    protected function getGuruUtama(): GuruUtama
    {
        $guruUtama = Auth::user()->guruUtama;

        if (! $guruUtama) {
            abort(403, 'Profil Guru Utama tidak ditemukan untuk akun ini.');
        }

        return $guruUtama;
    }

    /**
     * Dashboard Guru Utama.
     */
    public function dashboard(): View
    {
        $guruUtama = $this->getGuruUtama();

        $kegiatans = $guruUtama->kegiatans()
            ->with('maestros')
            ->latest('tanggal_mulai')
            ->get();

        $totalGuruDiajar = $guruUtama->gurus()->count();
        $totalSiswaDiajar = $guruUtama->siswas()->count();
        $totalPeserta = $totalGuruDiajar + $totalSiswaDiajar;

        $guruPct = $totalPeserta > 0 ? round(($totalGuruDiajar / $totalPeserta) * 100, 1) : 0;
        $siswaPct = $totalPeserta > 0 ? round(($totalSiswaDiajar / $totalPeserta) * 100, 1) : 0;

        // 1. Distribusi per Kegiatan
        $kegiatanStats = $guruUtama->kegiatans()
            ->with('maestros')
            ->withCount([
                'gurus' => fn ($q) => $q->where('guru_utama_id', $guruUtama->id),
                'siswas' => fn ($q) => $q->where('guru_utama_id', $guruUtama->id),
            ])
            ->get()
            ->map(fn ($k) => [
                'nama' => $k->nama_kegiatan,
                'kabupaten' => $k->kabupaten,
                'maestro_names' => $k->maestros->pluck('nama')->implode(', '),
                'guru_count' => $k->gurus_count,
                'siswa_count' => $k->siswas_count,
                'total' => $k->gurus_count + $k->siswas_count,
            ]);
        $mandiriGuru = $guruUtama->gurus()->whereNull('kegiatan_id')->count();
        $mandiriSiswa = $guruUtama->siswas()->whereNull('kegiatan_id')->count();
        if ($mandiriGuru > 0 || $mandiriSiswa > 0) {
            $kegiatanStats->push([
                'nama' => 'Pengimbasan Mandiri',
                'kabupaten' => $guruUtama->kabupaten,
                'guru_count' => $mandiriGuru,
                'siswa_count' => $mandiriSiswa,
                'total' => $mandiriGuru + $mandiriSiswa,
            ]);
        }

        $maxKegiatanTotal = $kegiatanStats->max('total') ?: 1;

        // 2. Distribusi Sekolah Binaan (Top 5)
        $sekolahGurus = $guruUtama->gurus()
            ->select('asal_sekolah as sekolah', DB::raw('count(*) as count'))
            ->whereNotNull('asal_sekolah')
            ->groupBy('asal_sekolah')
            ->pluck('count', 'sekolah')
            ->toArray();

        $sekolahSiswas = $guruUtama->siswas()
            ->select('sekolah', DB::raw('count(*) as count'))
            ->whereNotNull('sekolah')
            ->groupBy('sekolah')
            ->pluck('count', 'sekolah')
            ->toArray();

        $sekolahMap = [];
        foreach ($sekolahGurus as $s => $c) {
            $sekolahMap[$s] = ($sekolahMap[$s] ?? 0) + $c;
        }
        foreach ($sekolahSiswas as $s => $c) {
            $sekolahMap[$s] = ($sekolahMap[$s] ?? 0) + $c;
        }
        arsort($sekolahMap);
        $topSekolah = array_slice($sekolahMap, 0, 5, true);
        $maxSekolahTotal = ! empty($topSekolah) ? max($topSekolah) : 1;
        $totalSekolahUnik = count($sekolahMap);

        $recentGurus = $guruUtama->gurus()->with('kegiatan')->latest()->take(5)->get();
        $recentSiswas = $guruUtama->siswas()->with('kegiatan')->latest()->take(5)->get();

        return view('guru_utama.dashboard', [
            'title' => 'Portal Guru Utama — Beringin',
            'guruUtama' => $guruUtama,
            'kegiatans' => $kegiatans,
            'totalGuruDiajar' => $totalGuruDiajar,
            'totalSiswaDiajar' => $totalSiswaDiajar,
            'totalPeserta' => $totalPeserta,
            'guruPct' => $guruPct,
            'siswaPct' => $siswaPct,
            'kegiatanStats' => $kegiatanStats,
            'maxKegiatanTotal' => $maxKegiatanTotal,
            'topSekolah' => $topSekolah,
            'maxSekolahTotal' => $maxSekolahTotal,
            'totalSekolahUnik' => $totalSekolahUnik,
            'recentGurus' => $recentGurus,
            'recentSiswas' => $recentSiswas,
        ]);
    }

    /* =========================================================================
     * GURU YANG DIAJARKAN
     * ========================================================================= */

    public function guruIndex(Request $request): View
    {
        $guruUtama = $this->getGuruUtama();
        $assignedKegiatan = $guruUtama->kegiatans()->first();

        $query = $guruUtama->gurus()->with('kegiatan');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_guru', 'like', "%{$q}%")
                    ->orWhere('nuptk', 'like', "%{$q}%");
            });
        }

        $gurus = $query->latest()->paginate(15)->withQueryString();

        return view('guru_utama.guru', [
            'title' => 'Input Guru yang Diajarkan — Guru Utama',
            'guruUtama' => $guruUtama,
            'assignedKegiatan' => $assignedKegiatan,
            'kegiatans' => $guruUtama->kegiatans()->get(),
            'gurus' => $gurus,
        ]);
    }

    public function guruStore(Request $request): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();
        $assignedKegiatan = $guruUtama->kegiatans()->first();

        $validated = $request->validate([
            'nama_guru' => ['required', 'string', 'max:255'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
        ], [
            'nama_guru.required' => 'Nama guru yang diajarkan wajib diisi.',
        ]);

        Guru::create([
            'nama_guru' => $validated['nama_guru'],
            'asal_sekolah' => $guruUtama->asal_sekolah,
            'kabupaten' => $guruUtama->kabupaten,
            'nuptk' => $validated['nuptk'] ?? null,
            'kelurahan' => $validated['kelurahan'] ?? null,
            'provinsi' => 'Riau',
            'nama_guru_utama' => $guruUtama->nama,
            'guru_utama_id' => $guruUtama->id,
            'kegiatan_id' => $assignedKegiatan?->id,
        ]);

        return redirect()->route('guru_utama.guru.index')
            ->with('success', "Data guru {$validated['nama_guru']} berhasil dicatat di {$guruUtama->asal_sekolah}.");
    }

    public function guruEdit(Guru $guru): View
    {
        $guruUtama = $this->getGuruUtama();

        if ($guru->guru_utama_id !== $guruUtama->id) {
            abort(403, 'Anda tidak berhak mengubah data guru ini.');
        }

        $assignedKegiatan = $guruUtama->kegiatans()->first();

        return view('guru_utama.guru_edit', [
            'title' => 'Edit Guru Terimbas: '.$guru->nama_guru,
            'guruUtama' => $guruUtama,
            'assignedKegiatan' => $assignedKegiatan,
            'guru' => $guru,
        ]);
    }

    public function guruUpdate(Request $request, Guru $guru): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        if ($guru->guru_utama_id !== $guruUtama->id) {
            abort(403, 'Anda tidak berhak mengubah data guru ini.');
        }

        $assignedKegiatan = $guruUtama->kegiatans()->first();

        $validated = $request->validate([
            'nama_guru' => ['required', 'string', 'max:255'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
        ], [
            'nama_guru.required' => 'Nama guru yang diajarkan wajib diisi.',
        ]);

        $guru->update([
            'nama_guru' => $validated['nama_guru'],
            'asal_sekolah' => $guruUtama->asal_sekolah,
            'kabupaten' => $guruUtama->kabupaten,
            'nuptk' => $validated['nuptk'] ?? null,
            'kelurahan' => $validated['kelurahan'] ?? null,
            'kegiatan_id' => $assignedKegiatan?->id,
        ]);

        return redirect()->route('guru_utama.guru.index')
            ->with('success', "Data guru {$guru->nama_guru} berhasil diperbarui.");
    }

    public function guruDestroy(Guru $guru): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        // Pastikan hanya bisa menghapus guru yang diinput oleh dirinya
        if ($guru->guru_utama_id !== $guruUtama->id) {
            abort(403, 'Anda tidak berhak menghapus data ini.');
        }

        $nama = $guru->nama_guru;
        $guru->delete();

        return redirect()->route('guru_utama.guru.index')
            ->with('success', "Data guru {$nama} berhasil dihapus.");
    }

    /**
     * Ekspor data Guru yang diajarkan ke file CSV.
     */
    public function guruExport(Request $request): StreamedResponse
    {
        $guruUtama = $this->getGuruUtama();
        $query = $guruUtama->gurus()->with('kegiatan');

        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->query('kegiatan_id'));
        }

        $gurus = $query->orderBy('nama_guru')->get();
        $filename = 'rekap_guru_terimbas_'.str()->slug($guruUtama->nama).'_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($gurus, $guruUtama) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['REKAPITULASI GURU TERIMBAS — REVITALISASI BAHASA DAERAH']);
            fputcsv($handle, ['Guru Utama Pembina:', $guruUtama->nama]);
            fputcsv($handle, ['Wilayah Kabupaten:', $guruUtama->kabupaten]);
            fputcsv($handle, ['Tanggal Unduh:', now()->translatedFormat('d F Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, ['No', 'Nama Guru', 'Asal Sekolah', 'Kabupaten', 'NUPTK', 'Kelurahan', 'Kegiatan', 'Tanggal Dicatat']);

            foreach ($gurus as $index => $g) {
                fputcsv($handle, [
                    $index + 1,
                    $g->nama_guru,
                    $g->asal_sekolah,
                    $g->kabupaten,
                    $g->nuptk ?: '—',
                    $g->kelurahan ?: '—',
                    $g->kegiatan?->nama_kegiatan ?: 'Mandiri',
                    $g->created_at?->format('Y-m-d') ?: '—',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Download template CSV/Excel untuk import Guru.
     */
    public function guruTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_guru.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Tulis BOM UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nama_guru', 'asal_sekolah', 'kabupaten', 'nuptk', 'kelurahan']);
            fputcsv($handle, ['Siti Rahmah, S.Pd', 'SMPN 2 Siak', 'Siak', '1234567890123456', 'Mempura']);
            fputcsv($handle, ['Ahmad Dahlan, M.Pd', 'SMAN 1 Bengkalis', 'Bengkalis', '', 'Damas']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import data Guru massal dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function guruImport(Request $request, SimpleSpreadsheetReader $reader): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:xlsx,csv,txt'],
            'kegiatan_id' => ['nullable', 'exists:kegiatans,id'],
        ], [
            'file.required' => 'File Excel / CSV wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx atau .csv.',
            'file.max' => 'Ukuran file maksimal adalah 10 MB.',
        ]);

        $uploadedFile = $request->file('file');
        $ext = $uploadedFile->getClientOriginalExtension();

        try {
            $rows = $reader->readFile($uploadedFile->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return redirect()->route('guru_utama.guru.index')
                ->withErrors(['file' => 'Gagal membaca isi file: '.$e->getMessage()]);
        }

        if (empty($rows)) {
            return redirect()->route('guru_utama.guru.index')
                ->withErrors(['file' => 'File tidak memiliki baris data atau kosong.']);
        }

        $assignedKegiatan = $guruUtama->kegiatans()->first();
        $kegiatanId = $assignedKegiatan?->id;
        $asalSekolah = $guruUtama->asal_sekolah;
        $kabupaten = $guruUtama->kabupaten;

        $successCount = 0;
        $skippedCount = 0;
        $duplicateCount = 0;
        $now = now();
        $insertBatch = [];

        $existingNuptks = Guru::where('guru_utama_id', $guruUtama->id)
            ->whereNotNull('nuptk')
            ->pluck('nuptk')
            ->flip()
            ->toArray();

        $existingNames = Guru::where('guru_utama_id', $guruUtama->id)
            ->selectRaw('LOWER(TRIM(nama_guru)) as key_col')
            ->pluck('key_col')
            ->flip()
            ->toArray();

        foreach ($rows as $row) {
            $namaGuru = trim($row['nama_guru'] ?? $row['nama'] ?? '');
            $nuptk = isset($row['nuptk']) ? trim((string) $row['nuptk']) : null;
            $kelurahan = isset($row['kelurahan']) ? trim((string) $row['kelurahan']) : null;

            if ($namaGuru === '') {
                $skippedCount++;

                continue;
            }

            if ($nuptk && isset($existingNuptks[$nuptk])) {
                $duplicateCount++;

                continue;
            }

            $nameKey = strtolower($namaGuru);
            if (isset($existingNames[$nameKey])) {
                $duplicateCount++;

                continue;
            }

            if ($nuptk) {
                $existingNuptks[$nuptk] = true;
            }
            $existingNames[$nameKey] = true;

            $insertBatch[] = [
                'nama_guru' => $namaGuru,
                'asal_sekolah' => $asalSekolah,
                'kabupaten' => $kabupaten,
                'nuptk' => $nuptk ?: null,
                'kelurahan' => $kelurahan ?: null,
                'provinsi' => 'Riau',
                'nama_guru_utama' => $guruUtama->nama,
                'guru_utama_id' => $guruUtama->id,
                'kegiatan_id' => $kegiatanId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $successCount++;
        }

        if (! empty($insertBatch)) {
            foreach (array_chunk($insertBatch, 250) as $chunk) {
                Guru::insert($chunk);
            }
        }

        $pesan = "Berhasil mengimpor {$successCount} data guru.";
        if ($duplicateCount > 0) {
            $pesan .= " ({$duplicateCount} data dilewati karena sudah terdaftar/duplikat)";
        }
        if ($skippedCount > 0) {
            $pesan .= " ({$skippedCount} baris dilewati karena format tidak lengkap)";
        }

        return redirect()->route('guru_utama.guru.index')->with('success', $pesan);
    }

    /* =========================================================================
     * SISWA / MURID YANG DIAJARKAN
     * ========================================================================= */

    public function siswaIndex(Request $request): View
    {
        $guruUtama = $this->getGuruUtama();
        $assignedKegiatan = $guruUtama->kegiatans()->first();

        $query = $guruUtama->siswas()->with('kegiatan');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_siswa', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        $siswas = $query->latest()->paginate(15)->withQueryString();

        return view('guru_utama.siswa', [
            'title' => 'Input Murid / Siswa yang Diajarkan — Guru Utama',
            'guruUtama' => $guruUtama,
            'assignedKegiatan' => $assignedKegiatan,
            'kegiatans' => $guruUtama->kegiatans()->get(),
            'siswas' => $siswas,
        ]);
    }

    public function siswaStore(Request $request): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();
        $assignedKegiatan = $guruUtama->kegiatans()->first();

        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nis' => ['nullable', 'string', 'max:50'],
            'alamat_sekolah' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_siswa.required' => 'Nama murid / siswa wajib diisi.',
        ]);

        Siswa::create([
            'nama_siswa' => $validated['nama_siswa'],
            'sekolah' => $guruUtama->asal_sekolah,
            'kabupaten' => $guruUtama->kabupaten,
            'nis' => $validated['nis'] ?? null,
            'alamat_sekolah' => $validated['alamat_sekolah'] ?? null,
            'guru_utama_id' => $guruUtama->id,
            'nama_guru_utama' => $guruUtama->nama,
            'kegiatan_id' => $assignedKegiatan?->id,
        ]);

        return redirect()->route('guru_utama.siswa.index')
            ->with('success', "Data murid / siswa {$validated['nama_siswa']} berhasil dicatat di {$guruUtama->asal_sekolah}.");
    }

    public function siswaEdit(Siswa $siswa): View
    {
        $guruUtama = $this->getGuruUtama();

        if ($siswa->guru_utama_id !== $guruUtama->id) {
            abort(403, 'Anda tidak berhak mengubah data murid ini.');
        }

        $assignedKegiatan = $guruUtama->kegiatans()->first();

        return view('guru_utama.siswa_edit', [
            'title' => 'Edit Murid/Siswa: '.$siswa->nama_siswa,
            'guruUtama' => $guruUtama,
            'assignedKegiatan' => $assignedKegiatan,
            'siswa' => $siswa,
        ]);
    }

    public function siswaUpdate(Request $request, Siswa $siswa): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        if ($siswa->guru_utama_id !== $guruUtama->id) {
            abort(403, 'Anda tidak berhak mengubah data murid ini.');
        }

        $assignedKegiatan = $guruUtama->kegiatans()->first();

        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nis' => ['nullable', 'string', 'max:50'],
            'alamat_sekolah' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_siswa.required' => 'Nama murid / siswa wajib diisi.',
        ]);

        $siswa->update([
            'nama_siswa' => $validated['nama_siswa'],
            'sekolah' => $guruUtama->asal_sekolah,
            'kabupaten' => $guruUtama->kabupaten,
            'nis' => $validated['nis'] ?? null,
            'alamat_sekolah' => $validated['alamat_sekolah'] ?? null,
            'kegiatan_id' => $assignedKegiatan?->id,
        ]);

        return redirect()->route('guru_utama.siswa.index')
            ->with('success', "Data murid {$siswa->nama_siswa} berhasil diperbarui.");
    }

    public function siswaDestroy(Siswa $siswa): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        // Pastikan hanya bisa menghapus murid yang diinput oleh dirinya
        if ($siswa->guru_utama_id !== $guruUtama->id) {
            abort(403, 'Anda tidak berhak menghapus data ini.');
        }

        $nama = $siswa->nama_siswa;
        $siswa->delete();

        return redirect()->route('guru_utama.siswa.index')
            ->with('success', "Data murid {$nama} berhasil dihapus.");
    }

    /**
     * Ekspor data Murid/Siswa yang diajarkan ke file CSV.
     */
    public function siswaExport(Request $request): StreamedResponse
    {
        $guruUtama = $this->getGuruUtama();
        $query = $guruUtama->siswas()->with('kegiatan');

        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->query('kegiatan_id'));
        }

        $siswas = $query->orderBy('nama_siswa')->get();
        $filename = 'rekap_siswa_terimbas_'.str()->slug($guruUtama->nama).'_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($siswas, $guruUtama) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['REKAPITULASI SISWA TERIMBAS — REVITALISASI BAHASA DAERAH']);
            fputcsv($handle, ['Guru Utama Pembina:', $guruUtama->nama]);
            fputcsv($handle, ['Wilayah Kabupaten:', $guruUtama->kabupaten]);
            fputcsv($handle, ['Tanggal Unduh:', now()->translatedFormat('d F Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, ['No', 'Nama Siswa', 'Asal Sekolah', 'Kabupaten', 'NIS/NISN', 'Alamat Sekolah', 'Kegiatan', 'Tanggal Dicatat']);

            foreach ($siswas as $index => $s) {
                fputcsv($handle, [
                    $index + 1,
                    $s->nama_siswa,
                    $s->sekolah,
                    $s->kabupaten,
                    $s->nis ?: '—',
                    $s->alamat_sekolah ?: '—',
                    $s->kegiatan?->nama_kegiatan ?: 'Mandiri',
                    $s->created_at?->format('Y-m-d') ?: '—',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Download template CSV/Excel untuk import Siswa.
     */
    public function siswaTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nama_siswa', 'sekolah', 'kabupaten', 'nis', 'alamat_sekolah']);
            fputcsv($handle, ['Muhammad Farhan', 'SMPN 1 Pekanbaru', 'Pekanbaru', '2024001', 'Jl. Sudirman No. 10']);
            fputcsv($handle, ['Aisyah Putri', 'SMPN 2 Siak', 'Siak', '2024002', 'Jl. Raja Kecik']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import data Siswa massal dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function siswaImport(Request $request, SimpleSpreadsheetReader $reader): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:xlsx,csv,txt'],
            'kegiatan_id' => ['nullable', 'exists:kegiatans,id'],
        ], [
            'file.required' => 'File Excel / CSV wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx atau .csv.',
            'file.max' => 'Ukuran file maksimal adalah 10 MB.',
        ]);

        $uploadedFile = $request->file('file');
        $ext = $uploadedFile->getClientOriginalExtension();

        try {
            $rows = $reader->readFile($uploadedFile->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return redirect()->route('guru_utama.siswa.index')
                ->withErrors(['file' => 'Gagal membaca isi file: '.$e->getMessage()]);
        }

        if (empty($rows)) {
            return redirect()->route('guru_utama.siswa.index')
                ->withErrors(['file' => 'File tidak memiliki baris data atau kosong.']);
        }

        $assignedKegiatan = $guruUtama->kegiatans()->first();
        $kegiatanId = $assignedKegiatan?->id;
        $sekolah = $guruUtama->asal_sekolah;
        $kabupaten = $guruUtama->kabupaten;

        $successCount = 0;
        $skippedCount = 0;
        $duplicateCount = 0;
        $now = now();
        $insertBatch = [];

        $existingNis = Siswa::where('guru_utama_id', $guruUtama->id)
            ->whereNotNull('nis')
            ->pluck('nis')
            ->flip()
            ->toArray();

        $existingNames = Siswa::where('guru_utama_id', $guruUtama->id)
            ->selectRaw('LOWER(TRIM(nama_siswa)) as key_col')
            ->pluck('key_col')
            ->flip()
            ->toArray();

        foreach ($rows as $row) {
            $namaSiswa = trim($row['nama_siswa'] ?? $row['nama'] ?? '');
            $nis = isset($row['nis']) ? trim((string) $row['nis']) : null;
            $alamatSekolah = isset($row['alamat_sekolah']) ? trim((string) $row['alamat_sekolah']) : (isset($row['alamat']) ? trim((string) $row['alamat']) : null);

            if ($namaSiswa === '') {
                $skippedCount++;

                continue;
            }

            if ($nis && isset($existingNis[$nis])) {
                $duplicateCount++;

                continue;
            }

            $nameKey = strtolower($namaSiswa);
            if (isset($existingNames[$nameKey])) {
                $duplicateCount++;

                continue;
            }

            if ($nis) {
                $existingNis[$nis] = true;
            }
            $existingNames[$nameKey] = true;

            $insertBatch[] = [
                'nama_siswa' => $namaSiswa,
                'sekolah' => $sekolah,
                'kabupaten' => $kabupaten,
                'nis' => $nis ?: null,
                'alamat_sekolah' => $alamatSekolah ?: null,
                'guru_utama_id' => $guruUtama->id,
                'nama_guru_utama' => $guruUtama->nama,
                'kegiatan_id' => $kegiatanId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $successCount++;
        }

        if (! empty($insertBatch)) {
            foreach (array_chunk($insertBatch, 250) as $chunk) {
                Siswa::insert($chunk);
            }
        }

        $pesan = "Berhasil mengimpor {$successCount} data murid/siswa.";
        if ($duplicateCount > 0) {
            $pesan .= " ({$duplicateCount} data dilewati karena sudah terdaftar/duplikat)";
        }
        if ($skippedCount > 0) {
            $pesan .= " ({$skippedCount} baris dilewati karena format tidak lengkap)";
        }

        return redirect()->route('guru_utama.siswa.index')->with('success', $pesan);
    }
}
