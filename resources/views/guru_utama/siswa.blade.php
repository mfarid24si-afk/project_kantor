@extends('guru_utama.layout')

@section('portal_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>Pencatatan Murid / Siswa yang Diajarkan</h2>
      <p class="admin-page-desc">
        Catat murid atau siswa yang telah Anda bina dan ajarkan materi seni &amp; budaya Melayu.
      </p>
    </div>
  </div>

  <!-- FORM INPUT SISWA -->
  <div class="admin-card form-card" style="margin-bottom: 2rem;">
    <h3 class="form-section-title">Form Tambah Murid / Siswa Terimbas</h3>
    <p class="form-section-desc">Data yang diinputkan akan otomatis terhubung dengan profil Anda sebagai Guru Utama pembina.</p>

    <form method="POST" action="{{ route('guru_utama.siswa.store') }}">
      @csrf

      <div class="form-grid">
        <div class="form-group form-group--span-2">
          <label for="kegiatan_id" class="form-label">Kegiatan Terkait (Opsional)</label>
          <select id="kegiatan_id" name="kegiatan_id" class="form-control @error('kegiatan_id') is-invalid @enderror">
            <option value="">-- Tanpa / Di Luar Kegiatan Khusus --</option>
            @foreach($kegiatans as $k)
              <option value="{{ $k->id }}" {{ (old('kegiatan_id', request('kegiatan_id')) == $k->id) ? 'selected' : '' }}>
                {{ $k->nama_kegiatan }} ({{ $k->kabupaten }})
              </option>
            @endforeach
          </select>
          @error('kegiatan_id')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="nama_siswa" class="form-label required">Nama Lengkap Murid / Siswa</label>
          <input
            type="text"
            id="nama_siswa"
            name="nama_siswa"
            class="form-control @error('nama_siswa') is-invalid @enderror"
            value="{{ old('nama_siswa') }}"
            placeholder="Contoh: Muhammad Farhan"
            required
          />
          @error('nama_siswa')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="sekolah" class="form-label required">Nama Sekolah Siswa</label>
          <input
            type="text"
            id="sekolah"
            name="sekolah"
            class="form-control @error('sekolah') is-invalid @enderror"
            value="{{ old('sekolah') }}"
            placeholder="Contoh: SMPN 1 Pekanbaru"
            required
          />
          @error('sekolah')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        @php
          $daftarKabupaten = [
            'Bengkalis', 'Dumai', 'Indragiri Hilir', 'Indragiri Hulu',
            'Kampar', 'Kuantansingingi', 'Meranti', 'Pekanbaru',
            'Pelalawan', 'Rohil', 'Rohul', 'Siak',
          ];
        @endphp

        <div class="form-group">
          <label for="kabupaten" class="form-label required">Kabupaten / Kota</label>
          <select id="kabupaten" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" required>
            <option value="">-- Pilih Kabupaten --</option>
            @foreach($daftarKabupaten as $kab)
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
          <label for="nis" class="form-label">NIS / NISN (Opsional)</label>
          <input
            type="text"
            id="nis"
            name="nis"
            class="form-control @error('nis') is-invalid @enderror"
            value="{{ old('nis') }}"
            placeholder="NIS atau NISN siswa"
          />
          @error('nis')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group form-group--span-2">
          <label for="alamat_sekolah" class="form-label">Alamat / Keterangan Sekolah (Opsional)</label>
          <input
            type="text"
            id="alamat_sekolah"
            name="alamat_sekolah"
            class="form-control @error('alamat_sekolah') is-invalid @enderror"
            value="{{ old('alamat_sekolah') }}"
            placeholder="Alamat singkat sekolah atau kelas..."
          />
          @error('alamat_sekolah')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          + Simpan Data Murid / Siswa
        </button>
      </div>
    </form>
  </div>

  <!-- DAFTAR SISWA YANG SUDAH DIINPUT -->
  <div class="admin-page-header">
    <div>
      <h3>Daftar Murid yang Telah Anda Ajarkan</h3>
      <p class="admin-page-desc">Total {{ $siswas->total() }} murid terdata dalam rekam jejak pengimbasan Anda.</p>
    </div>
  </div>

  <div class="admin-filter-bar">
    <form method="GET" action="{{ route('guru_utama.siswa.index') }}" class="admin-search-form">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama murid, sekolah, NIS…"
        class="form-control"
      />
      <select name="kegiatan_id" class="form-control" onchange="this.form.submit()">
        <option value="">-- Semua Kegiatan --</option>
        @foreach($kegiatans as $k)
          <option value="{{ $k->id }}" {{ request('kegiatan_id') == $k->id ? 'selected' : '' }}>
            {{ $k->nama_kegiatan }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-outline">Cari</button>
      @if(request('q') || request('kegiatan_id'))
        <a href="{{ route('guru_utama.siswa.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
  </div>

  <div class="admin-card">
    @if($siswas->isEmpty())
      <div class="empty-state">
        Belum ada data murid yang dicatat. Silakan gunakan form di atas untuk menambahkan data murid binaan Anda.
      </div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Siswa</th>
              <th>Sekolah &amp; Wilayah</th>
              <th>NIS / NISN</th>
              <th>Kegiatan Terkait</th>
              <th>Waktu Input</th>
              <th style="width: 100px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($siswas as $s)
              <tr>
                <td><b>{{ $s->nama_siswa }}</b></td>
                <td>
                  <div>{{ $s->sekolah }}</div>
                  <span class="badge badge--pill badge--blue">{{ $s->kabupaten }}</span>
                </td>
                <td>{{ $s->nis ?: '—' }}</td>
                <td>
                  @if($s->kegiatan)
                    <span class="badge badge--pill badge--purple">{{ $s->kegiatan->nama_kegiatan }}</span>
                  @else
                    <span class="text-muted">Mandiri</span>
                  @endif
                </td>
                <td>
                  <small class="text-muted">{{ $s->created_at?->translatedFormat('d M Y') ?? '—' }}</small>
                </td>
                <td style="text-align: center;">
                  <form method="POST" action="{{ route('guru_utama.siswa.destroy', $s) }}" onsubmit="return confirm('Hapus data murid {{ $s->nama_siswa }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn--xs">Hapus</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="admin-pagination">
        {{ $siswas->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
