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

  <!-- PETA MONITORING WILAYAH RIAU (STANDALONE FRAMED MAP) -->
  <div class="admin-card" style="padding: 0; overflow: hidden; margin-top: 1.5rem; border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); background: #ffffff;">
    <div style="padding: 16px 22px; border-bottom: 1px solid var(--line); background: #fafdfb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="margin: 0; font-size: 1.15rem; color: #143026; display: flex; align-items: center; gap: 8px;">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #0d9488;"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" x2="8" y1="2" y2="18"/><line x1="16" x2="16" y1="6" y2="22"/></svg>
          Peta Monitoring Sebaran Wilayah Riau
        </h3>
        <p class="admin-page-desc" style="margin: 3px 0 0; font-size: 0.85rem;">
          Klik poligon kabupaten/kota untuk melihat rincian jumlah guru, siswa, komunitas, dan umum.
        </p>
      </div>

      <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <select id="year-select" class="form-control" style="width: auto; padding: 5px 10px; font-size: 0.82rem; border-radius: 6px;" aria-label="Filter Periode Tahun">
          <option value="">Semua Periode (Kumulatif)</option>
        </select>
        <button id="toggle-base" class="control-btn" type="button" style="padding: 5px 10px; font-size: 0.8rem; border-radius: 6px; border: 1px solid #d1d5db; background: #fff; cursor: pointer;">
          Peta Dasar
        </button>
        <button id="toggle-polygons" class="control-btn" type="button" style="padding: 5px 10px; font-size: 0.8rem; border-radius: 6px; border: 1px solid #d1d5db; background: #fff; cursor: pointer;">
          Poligon
        </button>
      </div>
    </div>

    <div class="page-peta" id="page-peta" style="height: 560px; margin: 0; position: relative; background: #e5e7eb; display: block;">
      <div id="map" style="width: 100%; height: 100%; position: absolute; inset: 0;"></div>
      <div id="popup" class="ol-popup" style="display:none;">
        <button id="popup-closer" class="ol-popup-closer" aria-label="Tutup popup">✕</button>
        <div id="popup-content"></div>
      </div>

    </div>
  </div>
</div>
@endsection
