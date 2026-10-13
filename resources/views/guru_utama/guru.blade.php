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
            value="{{ old('nama_guru') }}"
            placeholder="Contoh: Siti Rahmah, S.Pd"
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

  <!-- FORM UPLOAD MASSAL GURU -->
  <div class="admin-card form-card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 class="form-section-title">Import Data Guru Massal (Excel / CSV)</h3>
        <p class="form-section-desc">Unggah data guru sekaligus menggunakan file <b>.xlsx</b> atau <b>.csv</b>. Data otomatis terhubung ke profil Anda.</p>
      </div>
      <a href="{{ route('guru_utama.guru.template') }}" class="btn btn-outline" style="white-space: nowrap;">
        Unduh Contoh Template CSV
      </a>
    </div>

    <form method="POST" action="{{ route('guru_utama.guru.import') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Kegiatan &amp; Sekolah Tujuan (Terkunci)</label>
          <div style="padding: 9px 12px; background: #f8faf9; border: 1px solid var(--line); border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: #143026;">
            🔒 {{ $assignedKegiatan?->nama_kegiatan ?? 'Kegiatan Mandiri' }} · {{ $guruUtama->asal_sekolah }}
          </div>
        </div>

        <div class="form-group">
          <label for="import_file_guru" class="form-label required">Pilih File (.xlsx atau .csv)</label>
          <input
            type="file"
            id="import_file_guru"
            name="file"
            accept=".xlsx,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            class="form-control @error('file') is-invalid @enderror"
            required
          />
          @error('file')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-secondary">
          Mulai Import Guru
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

  <div class="admin-filter-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <form method="GET" action="{{ route('guru_utama.guru.index') }}" class="admin-search-form" style="flex: 1; min-width: 260px;">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama guru atau NUPTK…"
        class="form-control"
      />
      <button type="submit" class="btn btn-outline">Cari</button>
      @if(request('q'))
        <a href="{{ route('guru_utama.guru.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
    @if(!$gurus->isEmpty())
      <a href="{{ route('guru_utama.guru.export') }}" class="btn btn-outline" style="white-space: nowrap;">
        Unduh Rekap CSV
      </a>
    @endif
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
                <td style="text-align: center; white-space: nowrap;">
                  <a href="{{ route('guru_utama.guru.edit', $g) }}" class="btn btn-outline btn--xs" style="margin-right: 0.25rem;">Edit</a>
                  <form method="POST" action="{{ route('guru_utama.guru.destroy', $g) }}" onsubmit="return confirm('Hapus data guru {{ $g->nama_guru }}?');" style="display: inline;">
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
