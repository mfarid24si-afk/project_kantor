<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Kegiatan;
use App\Models\Maestro;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Daftar 12 Kabupaten/Kota resmi Provinsi Riau.
     */
    protected array $kabupatens = [
        'Bengkalis', 'Dumai', 'Indragiri Hilir', 'Indragiri Hulu',
        'Kampar', 'Kuantansingingi', 'Meranti', 'Pekanbaru',
        'Pelalawan', 'Rohil', 'Rohul', 'Siak',
    ];

    /**
     * Dashboard Super Admin.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_kegiatan' => Kegiatan::count(),
            'total_maestro' => Maestro::count(),
            'total_guru_utama' => GuruUtama::count(),
            'total_guru_terimbas' => Guru::count(),
            'total_siswa_terimbas' => Siswa::count(),
        ];

        $recentKegiatan = Kegiatan::with(['maestros', 'guruUtamas'])
            ->latest()
            ->take(5)
            ->get();

        $recentGuruUtama = GuruUtama::with(['user', 'gurus', 'siswas'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard Super Admin — Beringin',
            'stats' => $stats,
            'recentKegiatan' => $recentKegiatan,
            'recentGuruUtama' => $recentGuruUtama,
        ]);
    }

    /* =========================================================================
     * MAESTRO
     * ========================================================================= */

    public function maestroIndex(Request $request): View
    {
        $query = Maestro::query()->withCount('kegiatans');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('bidang_seni', 'like', "%{$q}%")
                    ->orWhere('kabupaten', 'like', "%{$q}%");
            });
        }

        $maestros = $query->latest()->paginate(10)->withQueryString();

        return view('admin.maestro.index', [
            'title' => 'Kelola Maestro — Super Admin',
            'maestros' => $maestros,
        ]);
    }

    public function maestroCreate(): View
    {
        return view('admin.maestro.form', [
            'title' => 'Tambah Maestro Baru',
            'maestro' => new Maestro,
            'kabupatens' => $this->kabupatens,
            'isEdit' => false,
        ]);
    }

    public function maestroStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'bidang_seni' => ['required', 'string', 'max:255'],
            'kabupaten' => ['nullable', 'string', 'in:'.implode(',', $this->kabupatens)],
            'kontak' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama.required' => 'Nama maestro wajib diisi.',
            'bidang_seni.required' => 'Bidang seni / keahlian wajib diisi.',
        ]);

        Maestro::create($validated);

        return redirect()->route('admin.maestro.index')
            ->with('success', "Maestro {$validated['nama']} berhasil ditambahkan.");
    }

    public function maestroEdit(Maestro $maestro): View
    {
        return view('admin.maestro.form', [
            'title' => 'Edit Maestro: '.$maestro->nama,
            'maestro' => $maestro,
            'kabupatens' => $this->kabupatens,
            'isEdit' => true,
        ]);
    }

    public function maestroUpdate(Request $request, Maestro $maestro): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'bidang_seni' => ['required', 'string', 'max:255'],
            'kabupaten' => ['nullable', 'string', 'in:'.implode(',', $this->kabupatens)],
            'kontak' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama.required' => 'Nama maestro wajib diisi.',
            'bidang_seni.required' => 'Bidang seni / keahlian wajib diisi.',
        ]);

        $maestro->update($validated);

        return redirect()->route('admin.maestro.index')
            ->with('success', "Data maestro {$maestro->nama} berhasil diperbarui.");
    }

    public function maestroDestroy(Maestro $maestro): RedirectResponse
    {
        $nama = $maestro->nama;
        $maestro->delete();

        return redirect()->route('admin.maestro.index')
            ->with('success', "Maestro {$nama} berhasil dihapus.");
    }

    /* =========================================================================
     * GURU UTAMA
     * ========================================================================= */

    public function guruUtamaIndex(Request $request): View
    {
        $query = GuruUtama::with(['user'])->withCount(['kegiatans', 'gurus', 'siswas']);

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('asal_sekolah', 'like', "%{$q}%")
                    ->orWhere('kabupaten', 'like', "%{$q}%")
                    ->orWhere('nuptk', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->query('kabupaten'));
        }

        $guruUtamas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.guru_utama.index', [
            'title' => 'Kelola Guru Utama — Super Admin',
            'guruUtamas' => $guruUtamas,
            'kabupatens' => $this->kabupatens,
        ]);
    }

    public function guruUtamaCreate(): View
    {
        return view('admin.guru_utama.form', [
            'title' => 'Tambah Guru Utama Baru',
            'guruUtama' => new GuruUtama,
            'kabupatens' => $this->kabupatens,
            'isEdit' => false,
        ]);
    }

    public function guruUtamaStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nip' => ['nullable', 'string', 'max:50'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'kabupaten' => ['required', 'string', 'in:'.implode(',', $this->kabupatens)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nama.required' => 'Nama guru utama wajib diisi.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'kabupaten.required' => 'Kabupaten/kota wajib dipilih.',
            'email.required' => 'Email untuk login wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'role' => 'guru_utama',
                'password' => Hash::make($validated['password']),
            ]);

            GuruUtama::create([
                'user_id' => $user->id,
                'nama' => $validated['nama'],
                'nuptk' => $validated['nuptk'] ?? null,
                'nip' => $validated['nip'] ?? null,
                'asal_sekolah' => $validated['asal_sekolah'],
                'kabupaten' => $validated['kabupaten'],
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('admin.guru_utama.index')
            ->with('success', "Guru Utama {$validated['nama']} beserta akun login berhasil dibuat.");
    }

    public function guruUtamaEdit(GuruUtama $guruUtama): View
    {
        $guruUtama->load('user');

        return view('admin.guru_utama.form', [
            'title' => 'Edit Guru Utama: '.$guruUtama->nama,
            'guruUtama' => $guruUtama,
            'kabupatens' => $this->kabupatens,
            'isEdit' => true,
        ]);
    }

    public function guruUtamaUpdate(Request $request, GuruUtama $guruUtama): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nip' => ['nullable', 'string', 'max:50'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'kabupaten' => ['required', 'string', 'in:'.implode(',', $this->kabupatens)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$guruUtama->user_id],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'nama.required' => 'Nama guru utama wajib diisi.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'kabupaten.required' => 'Kabupaten/kota wajib dipilih.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar oleh pengguna lain.',
            'password.min' => 'Password baru minimal 6 karakter jika ingin diubah.',
        ]);

        DB::transaction(function () use ($validated, $guruUtama) {
            $userUpdate = [
                'name' => $validated['nama'],
                'email' => $validated['email'],
            ];
            if (! empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }
            $guruUtama->user->update($userUpdate);

            $guruUtama->update([
                'nama' => $validated['nama'],
                'nuptk' => $validated['nuptk'] ?? null,
                'nip' => $validated['nip'] ?? null,
                'asal_sekolah' => $validated['asal_sekolah'],
                'kabupaten' => $validated['kabupaten'],
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('admin.guru_utama.index')
            ->with('success', "Data Guru Utama {$guruUtama->nama} berhasil diperbarui.");
    }

    public function guruUtamaDestroy(GuruUtama $guruUtama): RedirectResponse
    {
        $nama = $guruUtama->nama;
        $user = $guruUtama->user;

        DB::transaction(function () use ($guruUtama, $user) {
            $guruUtama->delete();
            $user?->delete();
        });

        return redirect()->route('admin.guru_utama.index')
            ->with('success', "Guru Utama {$nama} dan akun loginnya berhasil dihapus.");
    }

    /* =========================================================================
     * KEGIATAN
     * ========================================================================= */

    public function kegiatanIndex(Request $request): View
    {
        $query = Kegiatan::with(['maestros', 'guruUtamas'])->withCount(['gurus', 'siswas']);

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_kegiatan', 'like', "%{$q}%")
                    ->orWhere('kabupaten', 'like', "%{$q}%")
                    ->orWhere('tempat', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->query('kabupaten'));
        }

        $kegiatans = $query->latest('tanggal_mulai')->paginate(10)->withQueryString();

        return view('admin.kegiatan.index', [
            'title' => 'Kelola Kegiatan Pengimbasan — Super Admin',
            'kegiatans' => $kegiatans,
            'kabupatens' => $this->kabupatens,
        ]);
    }

    public function kegiatanCreate(): View
    {
        return view('admin.kegiatan.form', [
            'title' => 'Tambah Kegiatan Pengimbasan Baru',
            'kegiatan' => new Kegiatan,
            'maestros' => Maestro::orderBy('nama')->get(),
            'guruUtamas' => GuruUtama::orderBy('nama')->get(),
            'selectedMaestros' => [],
            'selectedGuruUtamas' => [],
            'kabupatens' => $this->kabupatens,
            'isEdit' => false,
        ]);
    }

    public function kegiatanStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'kabupaten' => ['required', 'string', 'in:'.implode(',', $this->kabupatens)],
            'tempat' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'string', 'in:direncanakan,berlangsung,selesai'],
            'maestro_ids' => ['nullable', 'array'],
            'maestro_ids.*' => ['exists:maestros,id'],
            'guru_utama_ids' => ['nullable', 'array'],
            'guru_utama_ids.*' => ['exists:guru_utamas,id'],
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'kabupaten.required' => 'Kabupaten kegiatan wajib dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        DB::transaction(function () use ($validated) {
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'kabupaten' => $validated['kabupaten'],
                'tempat' => $validated['tempat'] ?? null,
                'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'status' => $validated['status'],
            ]);

            if (! empty($validated['maestro_ids'])) {
                $kegiatan->maestros()->sync($validated['maestro_ids']);
            }

            if (! empty($validated['guru_utama_ids'])) {
                $kegiatan->guruUtamas()->sync($validated['guru_utama_ids']);
            }
        });

        return redirect()->route('admin.kegiatan.index')
            ->with('success', "Kegiatan {$validated['nama_kegiatan']} berhasil dibuat beserta penugasan Maestro & Guru Utama.");
    }

    public function kegiatanShow(Kegiatan $kegiatan): View
    {
        $kegiatan->load([
            'maestros',
            'guruUtamas.user',
            'gurus.guruUtama',
            'siswas.guruUtama',
        ]);

        return view('admin.kegiatan.show', [
            'title' => 'Detail Kegiatan: '.$kegiatan->nama_kegiatan,
            'kegiatan' => $kegiatan,
        ]);
    }

    public function kegiatanEdit(Kegiatan $kegiatan): View
    {
        $kegiatan->load(['maestros', 'guruUtamas']);

        return view('admin.kegiatan.form', [
            'title' => 'Edit Kegiatan: '.$kegiatan->nama_kegiatan,
            'kegiatan' => $kegiatan,
            'maestros' => Maestro::orderBy('nama')->get(),
            'guruUtamas' => GuruUtama::orderBy('nama')->get(),
            'selectedMaestros' => $kegiatan->maestros->pluck('id')->toArray(),
            'selectedGuruUtamas' => $kegiatan->guruUtamas->pluck('id')->toArray(),
            'kabupatens' => $this->kabupatens,
            'isEdit' => true,
        ]);
    }

    public function kegiatanUpdate(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'kabupaten' => ['required', 'string', 'in:'.implode(',', $this->kabupatens)],
            'tempat' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'string', 'in:direncanakan,berlangsung,selesai'],
            'maestro_ids' => ['nullable', 'array'],
            'maestro_ids.*' => ['exists:maestros,id'],
            'guru_utama_ids' => ['nullable', 'array'],
            'guru_utama_ids.*' => ['exists:guru_utamas,id'],
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'kabupaten.required' => 'Kabupaten kegiatan wajib dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        DB::transaction(function () use ($validated, $kegiatan) {
            $kegiatan->update([
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'kabupaten' => $validated['kabupaten'],
                'tempat' => $validated['tempat'] ?? null,
                'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'status' => $validated['status'],
            ]);

            $kegiatan->maestros()->sync($validated['maestro_ids'] ?? []);
            $kegiatan->guruUtamas()->sync($validated['guru_utama_ids'] ?? []);
        });

        return redirect()->route('admin.kegiatan.index')
            ->with('success', "Kegiatan {$kegiatan->nama_kegiatan} berhasil diperbarui.");
    }

    public function kegiatanDestroy(Kegiatan $kegiatan): RedirectResponse
    {
        $nama = $kegiatan->nama_kegiatan;
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')
            ->with('success', "Kegiatan {$nama} berhasil dihapus.");
    }
}
