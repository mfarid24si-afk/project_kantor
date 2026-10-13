@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>{{ $isEdit ? 'Edit Kegiatan Pengimbasan' : 'Tambah Kegiatan Baru' }}</h2>
      <p class="admin-page-desc">
        Atur kegiatan, jadwal pelaksanaan, serta tugaskan Maestro dan Guru Utama pendamping.
      </p>
    </div>
    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline">
      ← Kembali ke Daftar
    </a>
  </div>

  <div class="admin-card form-card">
    <form method="POST" action="{{ $isEdit ? route('admin.kegiatan.update', $kegiatan) : route('admin.kegiatan.store') }}">
      @csrf
      @if($isEdit)
        @method('PUT')
      @endif

      <h3 class="form-section-title">Informasi Kegiatan</h3>
      <div class="form-grid">
        <div class="form-group form-group--span-2">
          <label for="nama_kegiatan" class="form-label required">Nama Kegiatan Pengimbasan</label>
          <input
            type="text"
            id="nama_kegiatan"
            name="nama_kegiatan"
            class="form-control @error('nama_kegiatan') is-invalid @enderror"
            value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}"
            placeholder="Contoh: Workshop Pengimbasan Seni Tari Zapin Tradisi Riau 2026"
            required
          />
          @error('nama_kegiatan')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="kabupaten" class="form-label required">Kabupaten / Kota Wilayah Kegiatan</label>
          <select id="kabupaten" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" required>
            <option value="">-- Pilih Wilayah --</option>
            @foreach($kabupatens as $kab)
              <option value="{{ $kab }}" {{ old('kabupaten', $kegiatan->kabupaten) == $kab ? 'selected' : '' }}>
                {{ $kab }}
              </option>
            @endforeach
          </select>
          @error('kabupaten')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="tahun" class="form-label">Tahun Pelaksanaan Kegiatan</label>
          <input
            type="number"
            id="tahun"
            name="tahun"
            class="form-control @error('tahun') is-invalid @enderror"
            value="{{ old('tahun', $kegiatan->tahun ?? date('Y')) }}"
            placeholder="Contoh: 2026"
            min="2000"
            max="2099"
          />
          @error('tahun')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="tempat" class="form-label">Lokasi / Tempat Pelaksanaan</label>
          <input
            type="text"
            id="tempat"
            name="tempat"
            class="form-control @error('tempat') is-invalid @enderror"
            value="{{ old('tempat', $kegiatan->tempat) }}"
            placeholder="Contoh: Gedung Guru Riau / Aula SMAN 1"
          />
          @error('tempat')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
          <input
            type="date"
            id="tanggal_mulai"
            name="tanggal_mulai"
            class="form-control @error('tanggal_mulai') is-invalid @enderror"
            value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai ? \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('Y-m-d') : '') }}"
          />
          @error('tanggal_mulai')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
          <input
            type="date"
            id="tanggal_selesai"
            name="tanggal_selesai"
            class="form-control @error('tanggal_selesai') is-invalid @enderror"
            value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai ? \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('Y-m-d') : '') }}"
          />
          @error('tanggal_selesai')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="status" class="form-label required">Status Kegiatan</label>
          <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="direncanakan" {{ old('status', $kegiatan->status ?? 'direncanakan') == 'direncanakan' ? 'selected' : '' }}>Direncanakan</option>
            <option value="berlangsung" {{ old('status', $kegiatan->status) == 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
            <option value="selesai" {{ old('status', $kegiatan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
          </select>
          @error('status')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="deskripsi" class="form-label">Deskripsi &amp; Petunjuk Pengimbasan</label>
          <textarea
            id="deskripsi"
            name="deskripsi"
            rows="3"
            class="form-control @error('deskripsi') is-invalid @enderror"
            placeholder="Tuliskan tujuan kegiatan, cakupan materi yang diajarkan, atau arahan khusus..."
          >{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
          @error('deskripsi')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- PENUGASAN MAESTRO -->
      <h3 class="form-section-title" style="margin-top: 2rem;">Penugasan Maestro</h3>
      <p class="form-section-desc">Pilih satu atau lebih maestro yang terlibat sebagai narasumber / pembina dalam kegiatan ini.</p>

      <div class="selection-box">
        @if($maestros->isEmpty())
          <div class="empty-state">
            Belum ada data Maestro. <a href="{{ route('admin.maestro.create') }}" target="_blank">Tambah Maestro dulu</a>.
          </div>
        @else
          <div class="checkbox-grid">
            @foreach($maestros as $m)
              @php
                $checked = in_array($m->id, old('maestro_ids', $selectedMaestros ?? []));
              @endphp
              <label class="checkbox-card {{ $checked ? 'checked' : '' }}">
                <input
                  type="checkbox"
                  name="maestro_ids[]"
                  value="{{ $m->id }}"
                  {{ $checked ? 'checked' : '' }}
                />
                <div class="checkbox-card-content">
                  <span class="checkbox-card-title">{{ $m->nama }}</span>
                  <span class="checkbox-card-sub">{{ $m->bidang_seni }} {{ $m->kabupaten ? '• '.$m->kabupaten : '' }}</span>
                </div>
              </label>
            @endforeach
          </div>
        @endif
        @error('maestro_ids')
          <span class="invalid-feedback" style="display:block; margin-top:0.5rem;">{{ $message }}</span>
        @enderror
      </div>

      <!-- PENUGASAN GURU UTAMA -->
      <h3 class="form-section-title" style="margin-top: 2rem;">Penugasan Guru Utama</h3>
      <p class="form-section-desc">Pilih Guru Utama yang ditugaskan dalam kegiatan ini untuk kemudian menginput guru atau murid binaannya.</p>

      <div class="selection-box">
        @if($guruUtamas->isEmpty())
          <div class="empty-state">
            Belum ada Guru Utama. <a href="{{ route('admin.guru_utama.create') }}" target="_blank">Tambah Guru Utama dulu</a>.
          </div>
        @else
          <div class="checkbox-grid">
            @foreach($guruUtamas as $gu)
              @php
                $checkedGu = in_array($gu->id, old('guru_utama_ids', $selectedGuruUtamas ?? []));
              @endphp
              <label class="checkbox-card {{ $checkedGu ? 'checked' : '' }}">
                <input
                  type="checkbox"
                  name="guru_utama_ids[]"
                  value="{{ $gu->id }}"
                  {{ $checkedGu ? 'checked' : '' }}
                />
                <div class="checkbox-card-content">
                  <span class="checkbox-card-title">{{ $gu->nama }}</span>
                  <span class="checkbox-card-sub">{{ $gu->asal_sekolah }} ({{ $gu->kabupaten }})</span>
                </div>
              </label>
            @endforeach
          </div>
        @endif
        @error('guru_utama_ids')
          <span class="invalid-feedback" style="display:block; margin-top:0.5rem;">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-actions" style="margin-top: 2rem;">
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
          {{ $isEdit ? 'Simpan Perubahan Kegiatan' : 'Simpan Kegiatan &amp; Penugasan' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
