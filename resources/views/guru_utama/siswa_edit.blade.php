@extends('guru_utama.layout')

@section('portal_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <a href="{{ route('guru_utama.siswa.index') }}" class="back-link">← Kembali ke Daftar Murid</a>
      <h2>Edit Data Murid / Siswa Terimbas</h2>
      <p class="admin-page-desc">Perbarui data murid/siswa yang telah diajarkan dalam program pengimbasan.</p>
    </div>
  </div>

  <div class="admin-card form-card">
    <form method="POST" action="{{ route('guru_utama.siswa.update', $siswa) }}">
      @csrf
      @method('PUT')

      <div class="form-grid">
        <div class="form-group form-group--span-2">
          <label class="form-label">Kegiatan Pengimbasan Ditugaskan (Terkunci)</label>
          @if($assignedKegiatan)
            <input type="text" class="form-control" value="{{ $assignedKegiatan->nama_kegiatan }} ({{ $assignedKegiatan->kabupaten }})" readonly style="background: #f8faf9; cursor: not-allowed; font-weight: 600; color: #143026;" />
            <small class="text-muted" style="display: block; margin-top: 4px;">🔒 Anda secara otomatis terikat pada 1 kegiatan pengimbasan resmi yang ditugaskan oleh Admin.</small>
          @else
            <input type="text" class="form-control" value="Belum ada penugasan kegiatan resmi dari Admin" readonly style="background: #fef2f2; color: #b91c1c; font-weight: 600;" />
          @endif
        </div>

        <div class="form-group">
          <label for="nama_siswa" class="form-label required">Nama Murid / Siswa</label>
          <input
            type="text"
            id="nama_siswa"
            name="nama_siswa"
            class="form-control @error('nama_siswa') is-invalid @enderror"
            value="{{ old('nama_siswa', $siswa->nama_siswa) }}"
            required
          />
          @error('nama_siswa')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Sekolah Murid (Terkunci)</label>
          <input
            type="text"
            class="form-control"
            value="{{ $guruUtama->asal_sekolah }}"
            readonly
            style="background: #f8faf9; cursor: not-allowed; font-weight: 600; color: #143026;"
          />
          <small class="text-muted" style="display: block; margin-top: 4px;">🔒 Terkunci: Pengimbasan khusus di sekolah induk tempat tugas Anda ({{ $guruUtama->asal_sekolah }}).</small>
        </div>

        <div class="form-group">
          <label class="form-label">Kabupaten / Wilayah (Terkunci)</label>
          <input
            type="text"
            class="form-control"
            value="{{ $guruUtama->kabupaten }}"
            readonly
            style="background: #f8faf9; cursor: not-allowed; font-weight: 600; color: #143026;"
          />
        </div>

        <div class="form-group">
          <label for="nis" class="form-label">NIS / NISN Murid (Opsional)</label>
          <input
            type="text"
            id="nis"
            name="nis"
            class="form-control @error('nis') is-invalid @enderror"
            value="{{ old('nis', $siswa->nis) }}"
          />
          @error('nis')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="alamat_sekolah" class="form-label">Alamat Sekolah / Keterangan (Opsional)</label>
          <input
            type="text"
            id="alamat_sekolah"
            name="alamat_sekolah"
            class="form-control @error('alamat_sekolah') is-invalid @enderror"
            value="{{ old('alamat_sekolah', $siswa->alamat_sekolah) }}"
          />
          @error('alamat_sekolah')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions" style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('guru_utama.siswa.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
