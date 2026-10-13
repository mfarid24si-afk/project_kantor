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

  <!-- GRAFIK & VISUALISASI CAPAIAN PENGIMBASAN -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- GRAFIK 1: KOMPOSISI PENGIMBASAN -->
    <div class="admin-card" style="margin-bottom: 0;">
      <div class="admin-card-head" style="margin-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.05rem;">Komposisi Pengimbasan</h3>
          <p class="admin-page-desc" style="margin: 0;">Perbandingan proporsi guru vs murid</p>
        </div>
      </div>

      <div style="padding: 0.25rem 0;">
        <div style="height: 18px; width: 100%; background: #e5e7eb; border-radius: 999px; overflow: hidden; display: flex; margin-bottom: 1.25rem;">
          @if($totalPeserta > 0)
            <div style="width: {{ $guruPct }}%; background: #2563eb; transition: width 0.3s ease;" title="Guru: {{ $guruPct }}%"></div>
            <div style="width: {{ $siswaPct }}%; background: #0d9488; transition: width 0.3s ease;" title="Siswa: {{ $siswaPct }}%"></div>
          @else
            <div style="width: 100%; background: #d1d5db;" title="Belum ada data"></div>
          @endif
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div style="padding: 12px; background: #eff6ff; border-radius: 8px; border-left: 4px solid #2563eb;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
              <span style="font-size: 0.8rem; font-weight: 600; color: #1e40af;">Guru Terimbas</span>
              <span style="font-size: 0.75rem; font-weight: 700; color: #2563eb;">{{ $guruPct }}%</span>
            </div>
            <div style="font-size: 1.35rem; font-weight: 700; color: #1e3a8a; margin-top: 4px;">{{ number_format($totalGuruDiajar, 0, ',', '.') }}</div>
            <span style="font-size: 0.72rem; color: #6b7280;">rekan guru sejawat</span>
          </div>

          <div style="padding: 12px; background: #f0fdfa; border-radius: 8px; border-left: 4px solid #0d9488;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
              <span style="font-size: 0.8rem; font-weight: 600; color: #115e59;">Murid Terimbas</span>
              <span style="font-size: 0.75rem; font-weight: 700; color: #0d9488;">{{ $siswaPct }}%</span>
            </div>
            <div style="font-size: 1.35rem; font-weight: 700; color: #134e4a; margin-top: 4px;">{{ number_format($totalSiswaDiajar, 0, ',', '.') }}</div>
            <span style="font-size: 0.72rem; color: #6b7280;">siswa di sekolah</span>
          </div>
        </div>

        <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--line); display: flex; justify-content: space-between; font-size: 0.82rem;">
          <span class="text-muted">Total Keseluruhan Binaan:</span>
          <b style="color: var(--ink);">{{ number_format($totalPeserta, 0, ',', '.') }} orang</b>
        </div>
      </div>
    </div>

    <!-- GRAFIK 2: SEBARAN PER KEGIATAN -->
    <div class="admin-card" style="margin-bottom: 0;">
      <div class="admin-card-head" style="margin-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.05rem;">Sebaran per Kegiatan</h3>
          <p class="admin-page-desc" style="margin: 0;">Peserta pada tiap penugasan</p>
        </div>
      </div>

      <div style="padding: 0.25rem 0;">
        @if($kegiatanStats->isEmpty())
          <div class="empty-state" style="padding: 1.5rem 1rem;">Belum ada kegiatan pengimbasan yang ditugaskan kepada Anda.</div>
        @else
          <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            @foreach($kegiatanStats as $ks)
              <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 2px;">
                  <b style="color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;" title="{{ $ks['nama'] }}">{{ $ks['nama'] }}</b>
                  <span style="font-weight: 600; color: #143026;">{{ $ks['total'] }} peserta</span>
                </div>
                @if(!empty($ks['maestro_names']))
                  <small style="display: block; font-size: 0.72rem; color: #b45309; margin-bottom: 4px;">
                    ⭐ Maestro: {{ $ks['maestro_names'] }}
                  </small>
                @endif
                <div style="height: 10px; width: 100%; background: #e5e7eb; border-radius: 6px; overflow: hidden; display: flex;">
                  @if($ks['total'] > 0)
                    @php
                      $gPart = round(($ks['guru_count'] / $maxKegiatanTotal) * 100);
                      $sPart = round(($ks['siswa_count'] / $maxKegiatanTotal) * 100);
                    @endphp
                    <div style="width: {{ $gPart }}%; background: #2563eb;" title="Guru: {{ $ks['guru_count'] }}"></div>
                    <div style="width: {{ $sPart }}%; background: #0d9488;" title="Siswa: {{ $ks['siswa_count'] }}"></div>
                  @endif
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.72rem; color: #6b7280; margin-top: 2px;">
                  <span>Guru: {{ $ks['guru_count'] }}</span>
                  <span>Siswa: {{ $ks['siswa_count'] }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- GRAFIK 3: TOP SEKOLAH SASARAN -->
    <div class="admin-card" style="margin-bottom: 0;">
      <div class="admin-card-head" style="margin-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.05rem;">Sekolah Sasaran Utama</h3>
          <p class="admin-page-desc" style="margin: 0;">Lembaga binaan (Total: {{ $totalSekolahUnik }} sekolah)</p>
        </div>
      </div>

      <div style="padding: 0.25rem 0;">
        @if(empty($topSekolah))
          <div class="empty-state" style="padding: 1.5rem 1rem;">Belum ada data sekolah yang tercatat.</div>
        @else
          <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            @foreach($topSekolah as $sekolahName => $count)
              @php
                $sekPct = round(($count / $maxSekolahTotal) * 100);
              @endphp
              <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 4px;">
                  <span style="font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;" title="{{ $sekolahName }}">
                    🏫 {{ $sekolahName }}
                  </span>
                  <b style="color: #0d9488;">{{ $count }} orang</b>
                </div>
                <div style="height: 8px; width: 100%; background: #e5e7eb; border-radius: 6px; overflow: hidden;">
                  <div style="height: 100%; width: {{ $sekPct }}%; background: linear-gradient(90deg, #10b981, #0d9488); border-radius: 6px;"></div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
