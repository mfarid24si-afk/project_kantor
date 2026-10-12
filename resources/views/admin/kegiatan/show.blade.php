@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
        <span class="badge badge--pill badge--blue">{{ $kegiatan->kabupaten }}</span>
        @php
          $statusBadge = match($kegiatan->status) {
            'berlangsung' => 'badge--success',
            'selesai' => 'badge--neutral',
            default => 'badge--warning',
          };
        @endphp
        <span class="badge {{ $statusBadge }}">{{ ucfirst($kegiatan->status) }}</span>
      </div>
      <h2>{{ $kegiatan->nama_kegiatan }}</h2>
      <p class="admin-page-desc">
        @if($kegiatan->tempat)
          Lokasi: <b>{{ $kegiatan->tempat }}</b> &bull;
        @endif
        @if($kegiatan->tanggal_mulai)
          {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->translatedFormat('d F Y') }}
          @if($kegiatan->tanggal_selesai && $kegiatan->tanggal_selesai !== $kegiatan->tanggal_mulai)
            s.d. {{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->translatedFormat('d F Y') }}
          @endif
        @endif
      </p>
    </div>
    <div style="display:flex; gap:0.5rem;">
      <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="btn btn-primary">
        Edit Kegiatan
      </a>
      <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline">
        ← Kembali
      </a>
    </div>
  </div>

  @if($kegiatan->deskripsi)
    <div class="admin-card" style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: var(--ink);">Deskripsi / Petunjuk Pengimbasan:</h3>
      <p style="margin: 0; line-height: 1.6; color: var(--muted);">{{ $kegiatan->deskripsi }}</p>
    </div>
  @endif

  <!-- SUMMARY CARDS: MAESTRO & GURU UTAMA -->
  <div class="admin-columns-grid" style="margin-bottom: 1.5rem;">
    <!-- MAESTROS -->
    <div class="admin-card">
      <div class="admin-card-head">
        <h3>Maestro Pembina ({{ $kegiatan->maestros->count() }})</h3>
      </div>
      @if($kegiatan->maestros->isEmpty())
        <div class="empty-state">Belum ada maestro yang ditugaskan pada kegiatan ini.</div>
      @else
        <ul class="assigned-list">
          @foreach($kegiatan->maestros as $m)
            <li class="assigned-item">
              <div class="assigned-avatar assigned-avatar--amber">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>
              <div class="assigned-info">
                <b>{{ $m->nama }}</b>
                <span class="assigned-sub">{{ $m->bidang_seni }} {{ $m->kabupaten ? '('.$m->kabupaten.')' : '' }}</span>
                @if($m->kontak)
                  <small class="text-muted">Kontak: {{ $m->kontak }}</small>
                @endif
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    <!-- GURU UTAMA -->
    <div class="admin-card">
      <div class="admin-card-head">
        <h3>Guru Utama Bertugas ({{ $kegiatan->guruUtamas->count() }})</h3>
      </div>
      @if($kegiatan->guruUtamas->isEmpty())
        <div class="empty-state">Belum ada guru utama yang ditugaskan pada kegiatan ini.</div>
      @else
        <ul class="assigned-list">
          @foreach($kegiatan->guruUtamas as $gu)
            <li class="assigned-item">
              <div class="assigned-avatar assigned-avatar--purple">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <div class="assigned-info">
                <b>{{ $gu->nama }}</b>
                <span class="assigned-sub">{{ $gu->asal_sekolah }} &bull; {{ $gu->kabupaten }}</span>
                <small class="text-muted">Akun: {{ $gu->user?->email }}</small>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>

  <!-- HASIL PENGIMBASAN: GURU DIAJAR -->
  <div class="admin-card" style="margin-bottom: 1.5rem;">
    <div class="admin-card-head">
      <div>
        <h3>Daftar Guru yang Diajarkan</h3>
        <p class="admin-page-desc" style="margin: 0;">Total {{ $kegiatan->gurus->count() }} guru terimbas dari kegiatan ini.</p>
      </div>
    </div>

    @if($kegiatan->gurus->isEmpty())
      <div class="empty-state">Belum ada guru yang dicatat oleh Guru Utama pada kegiatan ini.</div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Guru</th>
              <th>Asal Sekolah</th>
              <th>Kabupaten</th>
              <th>NUPTK</th>
              <th>Guru Utama Pencatat</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kegiatan->gurus as $g)
              <tr>
                <td><b>{{ $g->nama_guru }}</b></td>
                <td>{{ $g->asal_sekolah }}</td>
                <td><span class="badge badge--pill badge--blue">{{ $g->kabupaten }}</span></td>
                <td>{{ $g->nuptk ?: '—' }}</td>
                <td>
                  <span class="badge badge--pill badge--purple">
                    {{ $g->guruUtama?->nama ?? $g->nama_guru_utama ?? '—' }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <!-- HASIL PENGIMBASAN: SISWA DIAJAR -->
  <div class="admin-card">
    <div class="admin-card-head">
      <div>
        <h3>Daftar Siswa / Murid yang Diajarkan</h3>
        <p class="admin-page-desc" style="margin: 0;">Total {{ $kegiatan->siswas->count() }} siswa terimbas dari kegiatan ini.</p>
      </div>
    </div>

    @if($kegiatan->siswas->isEmpty())
      <div class="empty-state">Belum ada murid / siswa yang dicatat oleh Guru Utama pada kegiatan ini.</div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Siswa</th>
              <th>Asal Sekolah</th>
              <th>Kabupaten</th>
              <th>NIS</th>
              <th>Guru Utama Pencatat</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kegiatan->siswas as $s)
              <tr>
                <td><b>{{ $s->nama_siswa }}</b></td>
                <td>{{ $s->sekolah }}</td>
                <td><span class="badge badge--pill badge--blue">{{ $s->kabupaten }}</span></td>
                <td>{{ $s->nis ?: '—' }}</td>
                <td>
                  <span class="badge badge--pill badge--purple">
                    {{ $s->guruUtama?->nama ?? $s->nama_guru_utama ?? '—' }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
