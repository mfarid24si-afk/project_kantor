@extends('admin.layout')

@section('admin_content')
<div class="admin-dashboard">
  <!-- STATS OVERVIEW -->
  <div class="admin-stats-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--blue">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($stats['total_kegiatan'], 0, ',', '.') }}</span>
        <span class="admin-stat-label">Total Kegiatan</span>
      </div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--amber">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($stats['total_maestro'], 0, ',', '.') }}</span>
        <span class="admin-stat-label">Maestro Terdaftar</span>
      </div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--purple">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($stats['total_guru_utama'], 0, ',', '.') }}</span>
        <span class="admin-stat-label">Guru Utama</span>
      </div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--green">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($stats['total_guru_terimbas'], 0, ',', '.') }}</span>
        <span class="admin-stat-label">Guru Terimbas</span>
      </div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--teal">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($stats['total_siswa_terimbas'], 0, ',', '.') }}</span>
        <span class="admin-stat-label">Siswa Terimbas</span>
      </div>
    </div>
  </div>

  <!-- QUICK ACTIONS -->
  <div class="admin-quick-actions">
    <div class="admin-actions-title">Tindakan Cepat:</div>
    <div class="admin-actions-buttons">
      <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary btn--sm">
        + Buat Kegiatan Baru
      </a>
      <a href="{{ route('admin.maestro.create') }}" class="btn btn-outline btn--sm">
        + Tambah Maestro
      </a>
      <a href="{{ route('admin.guru_utama.create') }}" class="btn btn-outline btn--sm">
        + Daftarkan Guru Utama
      </a>
    </div>
  </div>

  <!-- TWO COLUMN RECENT TABLES -->
  <div class="admin-columns-grid">
    <!-- RECENT KEGIATAN -->
    <div class="admin-card">
      <div class="admin-card-head">
        <h3>Kegiatan Pengimbasan Terbaru</h3>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn-link">Lihat Semua →</a>
      </div>

      @if($recentKegiatan->isEmpty())
        <div class="empty-state">Belum ada kegiatan pengimbasan yang dibuat.</div>
      @else
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama Kegiatan</th>
                <th>Wilayah</th>
                <th>Maestro</th>
                <th>Guru Utama</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentKegiatan as $keg)
                <tr>
                  <td>
                    <b>{{ $keg->nama_kegiatan }}</b>
                    <small class="text-muted d-block">{{ $keg->tanggal_mulai ? $keg->tanggal_mulai->format('d M Y') : 'Jadwal fleksibel' }}</small>
                  </td>
                  <td>{{ $keg->kabupaten }}</td>
                  <td>
                    @if($keg->maestros->isEmpty())
                      <span class="text-muted">—</span>
                    @else
                      {{ $keg->maestros->pluck('nama')->implode(', ') }}
                    @endif
                  </td>
                  <td>
                    <span class="badge badge--pill">{{ $keg->guruUtamas->count() }} Guru Utama</span>
                  </td>
                  <td>
                    <a href="{{ route('admin.kegiatan.show', $keg) }}" class="btn btn-outline btn--xs">Detail</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    <!-- RECENT GURU UTAMA -->
    <div class="admin-card">
      <div class="admin-card-head">
        <h3>Guru Utama Terdaftar</h3>
        <a href="{{ route('admin.guru_utama.index') }}" class="btn-link">Lihat Semua →</a>
      </div>

      @if($recentGuruUtama->isEmpty())
        <div class="empty-state">Belum ada guru utama yang didaftarkan.</div>
      @else
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama &amp; Akun</th>
                <th>Asal Sekolah &amp; Kab</th>
                <th>Guru Diajar</th>
                <th>Siswa Diajar</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentGuruUtama as $gu)
                <tr>
                  <td>
                    <b>{{ $gu->nama }}</b>
                    <small class="text-muted d-block">{{ $gu->user?->email }}</small>
                  </td>
                  <td>
                    {{ $gu->asal_sekolah }}
                    <small class="text-muted d-block">{{ $gu->kabupaten }}</small>
                  </td>
                  <td>
                    <b>{{ $gu->gurus->count() }}</b> guru
                  </td>
                  <td>
                    <b>{{ $gu->siswas->count() }}</b> siswa
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
