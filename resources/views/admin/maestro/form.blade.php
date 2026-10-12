@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>{{ $isEdit ? 'Edit Data Maestro' : 'Tambah Maestro Baru' }}</h2>
      <p class="admin-page-desc">
        {{ $isEdit ? 'Perbarui informasi maestro budaya Melayu.' : 'Daftarkan tokoh atau maestro yang akan memandu pengimbasan kebudayaan.' }}
      </p>
    </div>
    <a href="{{ route('admin.maestro.index') }}" class="btn btn-outline">
      ← Kembali ke Daftar
    </a>
  </div>

  <div class="admin-card form-card">
    <form method="POST" action="{{ $isEdit ? route('admin.maestro.update', $maestro) : route('admin.maestro.store') }}">
      @csrf
      @if($isEdit)
        @method('PUT')
      @endif

      <div class="form-grid">
        <div class="form-group form-group--span-2">
          <label for="nama" class="form-label required">Nama Maestro Lengkap</label>
          <input
            type="text"
            id="nama"
            name="nama"
            class="form-control @error('nama') is-invalid @enderror"
            value="{{ old('nama', $maestro->nama) }}"
            placeholder="Contoh: Tenas Effendy / Tokoh Budaya..."
            required
          />
          @error('nama')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="bidang_seni" class="form-label required">Bidang Seni / Keahlian</label>
          <input
            type="text"
            id="bidang_seni"
            name="bidang_seni"
            class="form-control @error('bidang_seni') is-invalid @enderror"
            value="{{ old('bidang_seni', $maestro->bidang_seni) }}"
            placeholder="Contoh: Zapin, Syair Melayu, Pantun, Teater Makyong..."
            required
          />
          @error('bidang_seni')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="kabupaten" class="form-label">Kabupaten / Asal Wilayah</label>
          <select id="kabupaten" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror">
            <option value="">-- Pilih Kabupaten (Opsional) --</option>
            @foreach($kabupatens as $kab)
              <option value="{{ $kab }}" {{ old('kabupaten', $maestro->kabupaten) == $kab ? 'selected' : '' }}>
                {{ $kab }}
              </option>
            @endforeach
          </select>
          @error('kabupaten')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="kontak" class="form-label">Nomor Kontak / WhatsApp</label>
          <input
            type="text"
            id="kontak"
            name="kontak"
            class="form-control @error('kontak') is-invalid @enderror"
            value="{{ old('kontak', $maestro->kontak) }}"
            placeholder="Contoh: 08123456789"
          />
          @error('kontak')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="deskripsi" class="form-label">Biografi Singkat / Catatan Portofolio</label>
          <textarea
            id="deskripsi"
            name="deskripsi"
            rows="4"
            class="form-control @error('deskripsi') is-invalid @enderror"
            placeholder="Catatan prestasi, karya, atau riwayat seni maestro..."
          >{{ old('deskripsi', $maestro->deskripsi) }}</textarea>
          @error('deskripsi')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('admin.maestro.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
          {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Maestro' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
