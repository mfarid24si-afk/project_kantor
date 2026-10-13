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
          <label class="form-label">Kegiatan Pengimbasan Ditugaskan (Terkunci)</label>
          @if($assignedKegiatan)
            <input type="text" class="form-control" value="{{ $assignedKegiatan->nama_kegiatan }} ({{ $assignedKegiatan->kabupaten }})" readonly style="background: #f8faf9; cursor: not-allowed; font-weight: 600; color: #143026;" />
            <small class="text-muted" style="display: block; margin-top: 4px;">🔒 Anda secara otomatis terikat pada 1 kegiatan pengimbasan resmi yang ditugaskan oleh Admin.</small>
          @else
            <input type="text" class="form-control" value="Belum ada penugasan kegiatan resmi dari Admin" readonly style="background: #fef2f2; color: #b91c1c; font-weight: 600;" />
          @endif
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

  <!-- FORM UPLOAD MASSAL SISWA -->
  <div class="admin-card form-card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 class="form-section-title">Import Data Murid / Siswa Massal (Excel / CSV)</h3>
        <p class="form-section-desc">Unggah data murid sekaligus menggunakan file <b>.xlsx</b> atau <b>.csv</b>. Data otomatis terhubung ke profil Anda.</p>
      </div>
      <a href="{{ route('guru_utama.siswa.template') }}" class="btn btn-outline" style="white-space: nowrap;">
        Unduh Contoh Template CSV
      </a>
    </div>

    <form method="POST" action="{{ route('guru_utama.siswa.import') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Kegiatan &amp; Sekolah Tujuan (Terkunci)</label>
          <div style="padding: 9px 12px; background: #f8faf9; border: 1px solid var(--line); border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: #143026;">
            🔒 {{ $assignedKegiatan?->nama_kegiatan ?? 'Kegiatan Mandiri' }} · {{ $guruUtama->asal_sekolah }}
          </div>
        </div>

        <div class="form-group">
          <label for="import_file_siswa" class="form-label required">Pilih File (.xlsx atau .csv)</label>
          <input
            type="file"
            id="import_file_siswa"
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
          Mulai Import Murid / Siswa
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

  <div class="admin-filter-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <form method="GET" action="{{ route('guru_utama.siswa.index') }}" class="admin-search-form" style="flex: 1; min-width: 260px;">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama murid atau NIS…"
        class="form-control"
      />
      <button type="submit" class="btn btn-outline">Cari</button>
      @if(request('q'))
        <a href="{{ route('guru_utama.siswa.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
    @if(!$siswas->isEmpty())
      <a href="{{ route('guru_utama.siswa.export') }}" class="btn btn-outline" style="white-space: nowrap;">
        Unduh Rekap CSV
      </a>
    @endif
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
                <td style="text-align: center; white-space: nowrap;">
                  <a href="{{ route('guru_utama.siswa.edit', $s) }}" class="btn btn-outline btn--xs" style="margin-right: 0.25rem;">Edit</a>
                  <form method="POST" action="{{ route('guru_utama.siswa.destroy', $s) }}" onsubmit="return confirm('Hapus data murid {{ $s->nama_siswa }}?');" style="display: inline;">
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
