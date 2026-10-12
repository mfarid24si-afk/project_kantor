@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>{{ $isEdit ? 'Edit Data Guru Utama' : 'Tambah Guru Utama Baru' }}</h2>
      <p class="admin-page-desc">
        {{ $isEdit ? 'Perbarui data diri dan akun login Guru Utama.' : 'Daftarkan Guru Utama beserta akun pengguna agar dapat login dan menginput data guru/siswa binaan.' }}
      </p>
    </div>
    <a href="{{ route('admin.guru_utama.index') }}" class="btn btn-outline">
      ← Kembali ke Daftar
    </a>
  </div>

  <div class="admin-card form-card">
    <form method="POST" action="{{ $isEdit ? route('admin.guru_utama.update', $guruUtama) : route('admin.guru_utama.store') }}">
      @csrf
      @if($isEdit)
        @method('PUT')
      @endif

      <h3 class="form-section-title">Data Pendidik</h3>
      <div class="form-grid">
        <div class="form-group form-group--span-2">
          <label for="nama" class="form-label required">Nama Guru Utama Lengkap (dengan gelar jika ada)</label>
          <input
            type="text"
            id="nama"
            name="nama"
            class="form-control @error('nama') is-invalid @enderror"
            value="{{ old('nama', $guruUtama->nama) }}"
            placeholder="Contoh: Drs. H. Ahmad Dahlan, M.Pd"
            required
          />
          @error('nama')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="asal_sekolah" class="form-label required">Asal Sekolah / Satuan Pendidikan</label>
          <input
            type="text"
            id="asal_sekolah"
            name="asal_sekolah"
            class="form-control @error('asal_sekolah') is-invalid @enderror"
            value="{{ old('asal_sekolah', $guruUtama->asal_sekolah) }}"
            placeholder="Contoh: SMAN 1 Pekanbaru"
            required
          />
          @error('asal_sekolah')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="kabupaten" class="form-label required">Kabupaten / Kota</label>
          <select id="kabupaten" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" required>
            <option value="">-- Pilih Kabupaten / Kota --</option>
            @foreach($kabupatens as $kab)
              <option value="{{ $kab }}" {{ old('kabupaten', $guruUtama->kabupaten) == $kab ? 'selected' : '' }}>
                {{ $kab }}
              </option>
            @endforeach
          </select>
          @error('kabupaten')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="nuptk" class="form-label">NUPTK</label>
          <input
            type="text"
            id="nuptk"
            name="nuptk"
            class="form-control @error('nuptk') is-invalid @enderror"
            value="{{ old('nuptk', $guruUtama->nuptk) }}"
            placeholder="16 digit NUPTK (opsional)"
          />
          @error('nuptk')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="nip" class="form-label">NIP</label>
          <input
            type="text"
            id="nip"
            name="nip"
            class="form-control @error('nip') is-invalid @enderror"
            value="{{ old('nip', $guruUtama->nip) }}"
            placeholder="18 digit NIP (opsional)"
          />
          @error('nip')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="no_hp" class="form-label">Nomor WhatsApp / HP Aktif</label>
          <input
            type="text"
            id="no_hp"
            name="no_hp"
            class="form-control @error('no_hp') is-invalid @enderror"
            value="{{ old('no_hp', $guruUtama->no_hp) }}"
            placeholder="Contoh: 08123456789"
          />
          @error('no_hp')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <h3 class="form-section-title" style="margin-top: 1.5rem;">Kredensial Akun Login</h3>
      <p class="form-section-desc">Akun ini digunakan oleh Guru Utama untuk mengakses Portal Pengimbasan.</p>

      <div class="form-grid">
        <div class="form-group">
          <label for="email" class="form-label required">Alamat Email (Username Login)</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $guruUtama->user?->email) }}"
            placeholder="guru.utama@sekolah.sch.id"
            required
          />
          @error('email')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="password" class="form-label {{ $isEdit ? '' : 'required' }}">
            {{ $isEdit ? 'Ubah Password (Kosongkan jika tidak diganti)' : 'Password Akun' }}
          </label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="{{ $isEdit ? 'Biarkan kosong untuk mempertahankan password lama' : 'Minimal 6 karakter' }}"
            {{ $isEdit ? '' : 'required' }}
          />
          @error('password')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('admin.guru_utama.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
          {{ $isEdit ? 'Simpan Perubahan' : 'Buat Guru Utama &amp; Akun' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
