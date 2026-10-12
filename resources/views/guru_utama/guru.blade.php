@extends('guru_utama.layout')

@section('portal_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>Pencatatan Guru yang Diajarkan</h2>
      <p class="admin-page-desc">
        Catat guru-guru yang telah Anda latih atau imbaskan dalam kegiatan seni &amp; budaya Melayu.
      </p>
    </div>
  </div>

  <!-- FORM INPUT GURU -->
  <div class="admin-card form-card" style="margin-bottom: 2rem;">
    <h3 class="form-section-title">Form Tambah Guru Terimbas</h3>
    <p class="form-section-desc">Data yang diinputkan akan otomatis terhubung dengan profil Anda sebagai Guru Utama pembina.</p>

    <form method="POST" action="{{ route('guru_utama.guru.store') }}">
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
          <label for="nama_guru" class="form-label required">Nama Guru yang Diajarkan</label>
          <input
            type="text"
            id="nama_guru"
            name="nama_guru"
            class="form-control @error('nama_guru') is-invalid @enderror"
            value="{{ old('nama_guru') }}"
            placeholder="Contoh: Siti Rahmah, S.Pd"
            required
          />
          @error('nama_guru')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="asal_sekolah" class="form-label required">Asal Sekolah Guru</label>
          <input
            type="text"
            id="asal_sekolah"
            name="asal_sekolah"
            class="form-control @error('asal_sekolah') is-invalid @enderror"
            value="{{ old('asal_sekolah') }}"
            placeholder="Contoh: SMPN 2 Siak"
            required
          />
          @error('asal_sekolah')
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
          <label for="nuptk" class="form-label">NUPTK Guru (Opsional)</label>
          <input
            type="text"
            id="nuptk"
            name="nuptk"
            class="form-control @error('nuptk') is-invalid @enderror"
            value="{{ old('nuptk') }}"
            placeholder="16 digit NUPTK"
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
            value="{{ old('kelurahan') }}"
            placeholder="Contoh: Mempura"
          />
          @error('kelurahan')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          + Simpan Data Guru
        </button>
      </div>
    </form>
  </div>

  <!-- DAFTAR GURU YANG SUDAH DIINPUT -->
  <div class="admin-page-header">
    <div>
      <h3>Daftar Guru yang Telah Anda Ajarkan</h3>
      <p class="admin-page-desc">Total {{ $gurus->total() }} guru terdata dalam rekam jejak pengimbasan Anda.</p>
    </div>
  </div>

  <div class="admin-filter-bar">
    <form method="GET" action="{{ route('guru_utama.guru.index') }}" class="admin-search-form">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama guru, asal sekolah, NUPTK…"
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
        <a href="{{ route('guru_utama.guru.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
  </div>

  <div class="admin-card">
    @if($gurus->isEmpty())
      <div class="empty-state">
        Belum ada data guru yang dicatat. Silakan gunakan form di atas untuk menambahkan data guru binaan Anda.
      </div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Guru</th>
              <th>Asal Sekolah &amp; Wilayah</th>
              <th>NUPTK</th>
              <th>Kegiatan Terkait</th>
              <th>Waktu Input</th>
              <th style="width: 100px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($gurus as $g)
              <tr>
                <td><b>{{ $g->nama_guru }}</b></td>
                <td>
                  <div>{{ $g->asal_sekolah }}</div>
                  <span class="badge badge--pill badge--blue">{{ $g->kabupaten }}</span>
                </td>
                <td>{{ $g->nuptk ?: '—' }}</td>
                <td>
                  @if($g->kegiatan)
                    <span class="badge badge--pill badge--purple">{{ $g->kegiatan->nama_kegiatan }}</span>
                  @else
                    <span class="text-muted">Mandiri</span>
                  @endif
                </td>
                <td>
                  <small class="text-muted">{{ $g->created_at?->translatedFormat('d M Y') ?? '—' }}</small>
                </td>
                <td style="text-align: center;">
                  <form method="POST" action="{{ route('guru_utama.guru.destroy', $g) }}" onsubmit="return confirm('Hapus data guru {{ $g->nama_guru }}?');">
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
        {{ $gurus->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
