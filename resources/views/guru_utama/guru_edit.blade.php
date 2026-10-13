@extends('guru_utama.layout')

@section('portal_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <a href="{{ route('guru_utama.guru.index') }}" class="back-link">← Kembali ke Daftar Guru</a>
      <h2>Edit Data Guru Terimbas</h2>
      <p class="admin-page-desc">Perbarui data guru binaan yang telah Anda latih dalam program pengimbasan.</p>
    </div>
  </div>

  <div class="admin-card form-card">
    <form method="POST" action="{{ route('guru_utama.guru.update', $guru) }}">
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
          <label for="nama_guru" class="form-label required">Nama Guru yang Diajarkan</label>
          <input
            type="text"
            id="nama_guru"
            name="nama_guru"
            class="form-control @error('nama_guru') is-invalid @enderror"
            value="{{ old('nama_guru', $guru->nama_guru) }}"
            required
          />
          @error('nama_guru')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Sekolah Tempat Pengimbasan (Terkunci)</label>
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
          <label for="nuptk" class="form-label">NUPTK Guru (Opsional)</label>
          <input
            type="text"
            id="nuptk"
            name="nuptk"
            class="form-control @error('nuptk') is-invalid @enderror"
            value="{{ old('nuptk', $guru->nuptk) }}"
          />
          @error('nuptk')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="kelurahan" class="form-label">Kecamatan / Kelurahan (Opsional)</label>
          <input
            type="text"
            id="kelurahan"
            name="kelurahan"
            class="form-control @error('kelurahan') is-invalid @enderror"
            value="{{ old('kelurahan', $guru->kelurahan) }}"
          />
          @error('kelurahan')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions" style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('guru_utama.guru.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
