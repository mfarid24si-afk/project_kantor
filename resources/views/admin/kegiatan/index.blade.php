@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>Data Kegiatan Pengimbasan</h2>
      <p class="admin-page-desc">Kelola agenda pengimbasan seni &amp; budaya Melayu beserta penugasan Maestro dan Guru Utama.</p>
    </div>
    <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary">
      + Buat Kegiatan Baru
    </a>
  </div>

  <!-- SEARCH & FILTER -->
  <div class="admin-filter-bar">
    <form method="GET" action="{{ route('admin.kegiatan.index') }}" class="admin-search-form">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama kegiatan, tempat, wilayah…"
        class="form-control"
      />
      <select name="status" class="form-control" onchange="this.form.submit()">
        <option value="">-- Semua Status --</option>
        <option value="direncanakan" {{ request('status') == 'direncanakan' ? 'selected' : '' }}>Direncanakan</option>
        <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
      </select>
      <select name="kabupaten" class="form-control" onchange="this.form.submit()">
        <option value="">-- Semua Kabupaten --</option>
        @foreach($kabupatens as $kab)
          <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>
            {{ $kab }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-outline">Filter</button>
      @if(request('q') || request('status') || request('kabupaten'))
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
  </div>

  <!-- TABLE -->
  <div class="admin-card">
    @if($kegiatans->isEmpty())
      <div class="empty-state">
        Tidak ada data kegiatan ditemukan.
        <a href="{{ route('admin.kegiatan.create') }}" class="btn-link">Buat kegiatan baru sekarang</a>
      </div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Kegiatan &amp; Lokasi</th>
              <th>Jadwal Pelaksanaan</th>
              <th>Maestro Terlibat</th>
              <th>Guru Utama Bertugas</th>
              <th style="text-align: center;">Hasil Pengimbasan</th>
              <th>Status</th>
              <th style="width: 170px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kegiatans as $keg)
              <tr>
                <td>
                  <a href="{{ route('admin.kegiatan.show', $keg) }}" class="table-link">
                    <b>{{ $keg->nama_kegiatan }}</b>
                  </a>
                  <div class="table-sub">
                    <span class="badge badge--pill badge--blue">{{ $keg->kabupaten }}</span>
                    @if($keg->tempat)
                      <span>• {{ $keg->tempat }}</span>
                    @endif
                  </div>
                </td>
                <td>
                  @if($keg->tanggal_mulai)
                    <div>{{ \Carbon\Carbon::parse($keg->tanggal_mulai)->translatedFormat('d M Y') }}</div>
                    @if($keg->tanggal_selesai && $keg->tanggal_selesai !== $keg->tanggal_mulai)
                      <small class="text-muted">s.d. {{ \Carbon\Carbon::parse($keg->tanggal_selesai)->translatedFormat('d M Y') }}</small>
                    @endif
                  @else
                    <span class="text-muted">Jadwal belum ditentukan</span>
                  @endif
                </td>
                <td>
                  @if($keg->maestros->isEmpty())
                    <span class="text-muted">Belum ada</span>
                  @else
                    <div class="tag-list">
                      @foreach($keg->maestros as $m)
                        <span class="badge badge--pill badge--amber" title="{{ $m->bidang_seni }}">{{ $m->nama }}</span>
                      @endforeach
                    </div>
                  @endif
                </td>
                <td>
                  @if($keg->guruUtamas->isEmpty())
                    <span class="text-muted">Belum ditugaskan</span>
                  @else
                    <div class="tag-list">
                      @foreach($keg->guruUtamas as $gu)
                        <span class="badge badge--pill badge--purple">{{ $gu->nama }}</span>
                      @endforeach
                    </div>
                  @endif
                </td>
                <td style="text-align: center;">
                  <span class="badge badge--pill badge--green">{{ $keg->gurus_count }} guru</span>
                  <span class="badge badge--pill badge--teal">{{ $keg->siswas_count }} siswa</span>
                </td>
                <td>
                  @php
                    $statusBadge = match($keg->status) {
                      'berlangsung' => 'badge--success',
                      'selesai' => 'badge--neutral',
                      default => 'badge--warning',
                    };
                  @endphp
                  <span class="badge {{ $statusBadge }}">{{ ucfirst($keg->status) }}</span>
                </td>
                <td style="text-align: center;">
                  <div class="table-actions">
                    <a href="{{ route('admin.kegiatan.show', $keg) }}" class="btn btn-outline btn--xs" title="Lihat Detail">Detail</a>
                    <a href="{{ route('admin.kegiatan.edit', $keg) }}" class="btn btn-outline btn--xs" title="Edit Kegiatan">Edit</a>
                    <form method="POST" action="{{ route('admin.kegiatan.destroy', $keg) }}" onsubmit="return confirm('Yakin ingin menghapus kegiatan {{ $keg->nama_kegiatan }}?');" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn--xs" title="Hapus">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="admin-pagination">
        {{ $kegiatans->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
