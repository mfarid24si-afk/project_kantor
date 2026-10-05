@extends('layouts.app')

@section('content')
<div class="page-peta" id="page-peta">
  <div class="peta-map">
    <div id="map"></div>
    <div id="popup" class="ol-popup" style="display:none;">
      <button id="popup-closer" class="ol-popup-closer" aria-label="Tutup popup">✕</button>
      <div id="popup-content"></div>
    </div>
  </div>

  <aside class="peta-panel" aria-label="Panel informasi peta">
    <a class="back-link" href="{{ url('/') }}" data-route="/">← Kembali ke Beranda</a>
    <h2>Sebaran Data Riau</h2>
    <p class="panel-sub" id="panel-updated">Memuat data…</p>

    <div class="panel-total">
      <div class="mini-stat">
        <b id="total-guru">—</b>
        <span>Guru</span>
      </div>
      <div class="mini-stat">
        <b id="total-siswa">—</b>
        <span>Siswa</span>
      </div>
    </div>

    <div class="region-picker">
      <label for="region-select">Pilih wilayah</label>
      <select id="region-select" aria-label="Pilih kabupaten/kota untuk dilihat di peta">
        <option value="">— Pilih kabupaten/kota —</option>
        @php
          $kabupatenList = [
            'Bengkalis', 'Dumai', 'Indragiri Hilir', 'Indragiri Hulu',
            'Kampar', 'Kuantansingingi', 'Meranti', 'Pekanbaru',
            'Pelalawan', 'Rohil', 'Rohul', 'Siak'
          ];
        @endphp
        @foreach($kabupatenList as $kab)
          <option value="{{ $kab }}">{{ $kab }}</option>
        @endforeach
      </select>
    </div>

    <div class="controls">
      <button id="toggle-base" class="control-btn" type="button">Sembunyikan Peta Dasar</button>
      <button id="toggle-polygons" class="control-btn" type="button">Sembunyikan Poligon</button>
    </div>

    <div class="legend">
      <div class="legend-head">
        <div class="legend-title">Wilayah</div>
        <button id="toggle-legend" class="legend-toggle" type="button">Sembunyikan</button>
      </div>
      <p class="legend-sub">Angka pada legenda = guru · siswa</p>
      <div id="legend-body"></div>
    </div>
  </aside>
</div>
@endsection
