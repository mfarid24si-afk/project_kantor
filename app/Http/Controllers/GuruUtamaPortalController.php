<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        $recentGurus = $guruUtama->gurus()->with('kegiatan')->latest()->take(5)->get();
        $recentSiswas = $guruUtama->siswas()->with('kegiatan')->latest()->take(5)->get();

        return view('guru_utama.dashboard', [
            'title' => 'Portal Guru Utama — Beringin',
            'guruUtama' => $guruUtama,
            'kegiatans' => $kegiatans,
            'totalGuruDiajar' => $totalGuruDiajar,
            'totalSiswaDiajar' => $totalSiswaDiajar,
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
        $kegiatans = $guruUtama->kegiatans()->orderBy('nama_kegiatan')->get();

        $query = $guruUtama->gurus()->with('kegiatan');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_guru', 'like', "%{$q}%")
                    ->orWhere('asal_sekolah', 'like', "%{$q}%")
                    ->orWhere('nuptk', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->query('kegiatan_id'));
        }

        $gurus = $query->latest()->paginate(15)->withQueryString();

        return view('guru_utama.guru', [
            'title' => 'Input Guru yang Diajarkan — Guru Utama',
            'guruUtama' => $guruUtama,
            'kegiatans' => $kegiatans,
            'gurus' => $gurus,
        ]);
    }

    public function guruStore(Request $request): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        $validated = $request->validate([
            'kegiatan_id' => ['nullable', 'exists:kegiatans,id'],
            'nama_guru' => ['required', 'string', 'max:255'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'kabupaten' => ['required', 'string'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
        ], [
            'nama_guru.required' => 'Nama guru yang diajarkan wajib diisi.',
            'asal_sekolah.required' => 'Asal sekolah guru wajib diisi.',
            'kabupaten.required' => 'Kabupaten wajib diisi.',
        ]);

        Guru::create([
            'nama_guru' => $validated['nama_guru'],
            'asal_sekolah' => $validated['asal_sekolah'],
            'kabupaten' => $validated['kabupaten'],
            'nuptk' => $validated['nuptk'] ?? null,
            'kelurahan' => $validated['kelurahan'] ?? null,
            'provinsi' => 'Riau',
            'nama_guru_utama' => $guruUtama->nama,
            'guru_utama_id' => $guruUtama->id,
            'kegiatan_id' => $validated['kegiatan_id'] ?? null,
        ]);

        return redirect()->route('guru_utama.guru.index')
            ->with('success', "Data guru {$validated['nama_guru']} berhasil dicatat ke sistem.");
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

    /* =========================================================================
     * SISWA / MURID YANG DIAJARKAN
     * ========================================================================= */

    public function siswaIndex(Request $request): View
    {
        $guruUtama = $this->getGuruUtama();
        $kegiatans = $guruUtama->kegiatans()->orderBy('nama_kegiatan')->get();

        $query = $guruUtama->siswas()->with('kegiatan');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_siswa', 'like', "%{$q}%")
                    ->orWhere('sekolah', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->query('kegiatan_id'));
        }

        $siswas = $query->latest()->paginate(15)->withQueryString();

        return view('guru_utama.siswa', [
            'title' => 'Input Murid / Siswa yang Diajarkan — Guru Utama',
            'guruUtama' => $guruUtama,
            'kegiatans' => $kegiatans,
            'siswas' => $siswas,
        ]);
    }

    public function siswaStore(Request $request): RedirectResponse
    {
        $guruUtama = $this->getGuruUtama();

        $validated = $request->validate([
            'kegiatan_id' => ['nullable', 'exists:kegiatans,id'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'sekolah' => ['required', 'string', 'max:255'],
            'kabupaten' => ['required', 'string'],
            'nis' => ['nullable', 'string', 'max:50'],
            'alamat_sekolah' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_siswa.required' => 'Nama murid / siswa wajib diisi.',
            'sekolah.required' => 'Nama sekolah murid wajib diisi.',
            'kabupaten.required' => 'Kabupaten wajib diisi.',
        ]);

        Siswa::create([
            'nama_siswa' => $validated['nama_siswa'],
            'sekolah' => $validated['sekolah'],
            'kabupaten' => $validated['kabupaten'],
            'nis' => $validated['nis'] ?? null,
            'alamat_sekolah' => $validated['alamat_sekolah'] ?? null,
            'guru_utama_id' => $guruUtama->id,
            'nama_guru_utama' => $guruUtama->nama,
            'kegiatan_id' => $validated['kegiatan_id'] ?? null,
        ]);

        return redirect()->route('guru_utama.siswa.index')
            ->with('success', "Data murid / siswa {$validated['nama_siswa']} berhasil dicatat ke sistem.");
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
}
