@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>Data Maestro</h2>
      <p class="admin-page-desc">Kelola data tokoh budaya dan maestro yang memandu kegiatan pengimbasan bahasa &amp; seni budaya Melayu.</p>
    </div>
    <a href="{{ route('admin.maestro.create') }}" class="btn btn-primary">
      + Tambah Maestro Baru
    </a>
  </div>

  <!-- SEARCH & FILTER -->
  <div class="admin-filter-bar">
    <form method="GET" action="{{ route('admin.maestro.index') }}" class="admin-search-form">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama maestro, bidang seni, atau kabupaten…"
        class="form-control"
      />
      <button type="submit" class="btn btn-outline">Cari</button>
      @if(request('q'))
        <a href="{{ route('admin.maestro.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
  </div>

  <!-- TABLE -->
  <div class="admin-card">
    @if($maestros->isEmpty())
      <div class="empty-state">
        Tidak ada data maestro ditemukan.
        <a href="{{ route('admin.maestro.create') }}" class="btn-link">Tambah sekarang</a>
      </div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Maestro</th>
              <th>Bidang Seni / Keahlian</th>
              <th>Kabupaten</th>
              <th>Kontak</th>
              <th>Kegiatan Diikuti</th>
              <th style="width: 140px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($maestros as $m)
              <tr>
                <td>
                  <b>{{ $m->nama }}</b>
                  @if($m->deskripsi)
                    <p class="table-desc">{{ Str::limit($m->deskripsi, 60) }}</p>
                  @endif
                </td>
                <td>
                  <span class="badge badge--pill badge--amber">{{ $m->bidang_seni }}</span>
                </td>
                <td>{{ $m->kabupaten ?: '—' }}</td>
                <td>{{ $m->kontak ?: '—' }}</td>
                <td>
                  <b>{{ $m->kegiatans_count }}</b> kegiatan
                </td>
                <td style="text-align: center;">
                  <div class="table-actions">
                    <a href="{{ route('admin.maestro.edit', $m) }}" class="btn btn-outline btn--xs">Edit</a>
                    <form method="POST" action="{{ route('admin.maestro.destroy', $m) }}" onsubmit="return confirm('Yakin ingin menghapus maestro {{ $m->nama }}?');" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn--xs">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="admin-pagination">
        {{ $maestros->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
