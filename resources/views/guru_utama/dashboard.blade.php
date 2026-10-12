@extends('guru_utama.layout')

@section('portal_content')
<div class="admin-dashboard">
  <!-- STATS OVERVIEW -->
  <div class="admin-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--purple">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ $kegiatans->count() }}</span>
        <span class="admin-stat-label">Kegiatan Ditugaskan</span>
      </div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--green">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($totalGuruDiajar, 0, ',', '.') }}</span>
        <span class="admin-stat-label">Guru Diajarkan</span>
      </div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-icon admin-stat-icon--teal">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
      </div>
      <div class="admin-stat-body">
        <span class="admin-stat-num">{{ number_format($totalSiswaDiajar, 0, ',', '.') }}</span>
        <span class="admin-stat-label">Murid / Siswa Diajarkan</span>
      </div>
    </div>
  </div>

  <!-- QUICK ACTIONS -->
  <div class="admin-quick-actions">
    <div class="admin-actions-title">Aksi Cepat:</div>
    <div class="admin-actions-buttons">
      <a href="{{ route('guru_utama.guru.index') }}" class="btn btn-primary btn--sm">
        + Input Guru yang Diajarkan
      </a>
      <a href="{{ route('guru_utama.siswa.index') }}" class="btn btn-outline btn--sm">
        + Input Murid / Siswa
      </a>
    </div>
  </div>

  <!-- KEGIATAN YANG DITUGASKAN OLEH SUPER ADMIN -->
  <div class="admin-card" style="margin-bottom: 2rem;">
    <div class="admin-card-head">
      <div>
        <h3>Kegiatan Pengimbasan yang Ditugaskan</h3>
        <p class="admin-page-desc" style="margin: 0;">Kegiatan yang diberikan Super Admin kepada Anda bersama Maestro pembina.</p>
      </div>
    </div>

    @if($kegiatans->isEmpty())
      <div class="empty-state">
        Saat ini belum ada kegiatan pengimbasan yang ditugaskan kepada Anda oleh Super Admin.
      </div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Kegiatan &amp; Lokasi</th>
              <th>Maestro Pembina</th>
              <th>Jadwal Pelaksanaan</th>
              <th>Status</th>
              <th style="text-align: center;">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kegiatans as $k)
              <tr>
                <td>
                  <b>{{ $k->nama_kegiatan }}</b>
                  <div class="table-sub">
                    <span class="badge badge--pill badge--blue">{{ $k->kabupaten }}</span>
                    @if($k->tempat)
                      <span>• {{ $k->tempat }}</span>
                    @endif
                  </div>
                </td>
                <td>
                  @if($k->maestros->isEmpty())
                    <span class="text-muted">—</span>
                  @else
                    <div class="tag-list">
                      @foreach($k->maestros as $m)
                        <span class="badge badge--pill badge--amber" title="{{ $m->bidang_seni }}">
                          ⭐ {{ $m->nama }} ({{ $m->bidang_seni }})
                        </span>
                      @endforeach
                    </div>
                  @endif
                </td>
                <td>
                  @if($k->tanggal_mulai)
                    {{ \Carbon\Carbon::parse($k->tanggal_mulai)->translatedFormat('d M Y') }}
                    @if($k->tanggal_selesai && $k->tanggal_selesai !== $k->tanggal_mulai)
                      <br><small class="text-muted">s.d. {{ \Carbon\Carbon::parse($k->tanggal_selesai)->translatedFormat('d M Y') }}</small>
                    @endif
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @php
                    $statusBadge = match($k->status) {
                      'berlangsung' => 'badge--success',
                      'selesai' => 'badge--neutral',
                      default => 'badge--warning',
                    };
                  @endphp
                  <span class="badge {{ $statusBadge }}">{{ ucfirst($k->status) }}</span>
                </td>
                <td style="text-align: center;">
                  <div class="table-actions">
                    <a href="{{ route('guru_utama.guru.index', ['kegiatan_id' => $k->id]) }}" class="btn btn-outline btn--xs">
                      + Input Guru
                    </a>
                    <a href="{{ route('guru_utama.siswa.index', ['kegiatan_id' => $k->id]) }}" class="btn btn-outline btn--xs">
                      + Input Murid
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <!-- RECENT INPUTS GRID -->
  <div class="admin-columns-grid">
    <!-- GURU TERAKHIR DIINPUT -->
    <div class="admin-card">
      <div class="admin-card-head">
        <h3>Guru Baru Diajarkan</h3>
        <a href="{{ route('guru_utama.guru.index') }}" class="btn-link">Lihat Semua →</a>
      </div>

      @if($recentGurus->isEmpty())
        <div class="empty-state">Belum ada data guru yang Anda input.</div>
      @else
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama Guru</th>
                <th>Sekolah</th>
                <th>Kabupaten</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentGurus as $rg)
                <tr>
                  <td><b>{{ $rg->nama_guru }}</b></td>
                  <td>{{ $rg->asal_sekolah }}</td>
                  <td><span class="badge badge--pill badge--blue">{{ $rg->kabupaten }}</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    <!-- SISWA TERAKHIR DIINPUT -->
    <div class="admin-card">
      <div class="admin-card-head">
        <h3>Murid Baru Diajarkan</h3>
        <a href="{{ route('guru_utama.siswa.index') }}" class="btn-link">Lihat Semua →</a>
      </div>

      @if($recentSiswas->isEmpty())
        <div class="empty-state">Belum ada data murid yang Anda input.</div>
      @else
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama Murid</th>
                <th>Sekolah</th>
                <th>Kabupaten</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentSiswas as $rs)
                <tr>
                  <td><b>{{ $rs->nama_siswa }}</b></td>
                  <td>{{ $rs->sekolah }}</td>
                  <td><span class="badge badge--pill badge--blue">{{ $rs->kabupaten }}</span></td>
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
