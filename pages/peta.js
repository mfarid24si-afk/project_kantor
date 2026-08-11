/* =====================================================================
 * HALAMAN PETA — Map View (FR-3)
 *  - 12 poligon kabupaten/kota dari Area_Kab_Riau.geojson (tidak diganti)
 *  - Warna per wilayah memakai logika getColorForKabupaten yang sudah ada
 *  - Popup saat klik: nama wilayah + jumlah guru + jumlah siswa
 *  - Polling otomatis (pola setInterval yang sudah ada, 15 detik)
 *  - Tombol kontrol yang sudah ada dipertahankan (tampil/sembunyikan
 *    basemap OSM & poligon), legenda ringkas, responsif desktop/mobile.
 * ===================================================================== */

import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import Overlay from 'ol/Overlay';
import OSM from 'ol/source/OSM';
import VectorSource from 'ol/source/Vector';
import GeoJSON from 'ol/format/GeoJSON';
import { Fill, Stroke, Style, Text } from 'ol/style';
import Point from 'ol/geom/Point';
import { fromLonLat } from 'ol/proj';
import { defaults as defaultInteractions } from 'ol/interaction';

import { GEOJSON_PATH, GEOJSON_PROP_NAMA, REFRESH_INTERVAL, KABUPATEN_LIST } from '../config.js';
import {
  fetchJumlahGuruPerKabupaten,
  fetchJumlahSiswaPerKabupaten,
} from '../api.js';
import { escapeHtml } from '../ui.js';

let jumlahGuru = {};
let jumlahSiswa = {};
let guruFailed = false;
let siswaFailed = false;

const CATEGORY_COLORS = [
  'rgba(52, 152, 219, 0.6)',
  'rgba(231, 76, 60, 0.6)',
  'rgba(46, 204, 113, 0.6)',
  'rgba(155, 89, 182, 0.6)',
  'rgba(241, 196, 15, 0.6)',
  'rgba(230, 126, 34, 0.6)',
  'rgba(52, 73, 94, 0.6)',
  'rgba(26, 188, 156, 0.6)',
  'rgba(142, 68, 173, 0.6)',
  'rgba(22, 160, 133, 0.6)',
  'rgba(192, 57, 43, 0.6)',
  'rgba(44, 62, 80, 0.6)',
];

// Logika warna per wilayah yang sudah ada — dipertahankan (FR-3.2).
function getColorForKabupaten(name) {
  if (!name) return 'rgba(200, 200, 200, 0.3)';
  let hash = 0;
  for (let i = 0; i < name.length; i += 1) {
    hash = (hash * 31 + name.charCodeAt(i)) % CATEGORY_COLORS.length;
  }
  return CATEGORY_COLORS[hash];
}

function getLabelGeometry(feature) {
  const geometry = feature.getGeometry();
  if (!geometry || typeof geometry.getExtent !== 'function') return null;
  const extent = geometry.getExtent();
  const center = [(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2];
  return new Point(center);
}

function styleFeature(feature) {
  const namaKabupaten = feature.get(GEOJSON_PROP_NAMA);
  const labelGeometry = getLabelGeometry(feature);

  const polygonStyle = new Style({
    fill: new Fill({ color: getColorForKabupaten(namaKabupaten) }),
    stroke: new Stroke({ color: '#2f6b4f', width: 1 }),
  });

  const textStyle = new Style({
    geometry: labelGeometry || undefined,
    text: new Text({
      text: namaKabupaten || '',
      font: 'bold 12px Calibri, sans-serif',
      fill: new Fill({ color: '#ffffff' }),
      stroke: new Stroke({ color: '#000000', width: 2 }),
      overflow: true,
      placement: 'point',
      scale: 1.1,
      offsetY: 0,
    }),
  });

  return [polygonStyle, textStyle];
}

export function renderPeta(container) {
  jumlahGuru = {};
  jumlahSiswa = {};

  container.innerHTML = `
    <div class="page-peta">
      <div class="peta-map">
        <div id="map"></div>
        <div id="popup" class="ol-popup" style="display:none;">
          <button id="popup-closer" class="ol-popup-closer" aria-label="Tutup popup">✕</button>
          <div id="popup-content"></div>
        </div>
      </div>

      <aside class="peta-panel" aria-label="Panel informasi peta">
        <h2>Sebaran Data Riau</h2>
        <p class="panel-sub" id="panel-updated">Memuat data…</p>

        <div class="panel-total">
          <div class="mini-stat">
            <b id="total-guru">–</b>
            <span>Guru</span>
          </div>
          <div class="mini-stat">
            <b id="total-siswa">–</b>
            <span>Siswa</span>
          </div>
        </div>

        <div class="controls">
          <button id="toggle-base" class="control-btn" type="button">Tampilkan Peta</button>
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
  `;

  const mapEl = document.getElementById('map');

  // Basemap OSM — sesuai perilaku awal: awalnya disembunyikan.
  const baseLayer = new TileLayer({ source: new OSM(), visible: false });

  const vectorSource = new VectorSource({
    url: GEOJSON_PATH,
    format: new GeoJSON(),
  });

  const vectorLayer = new VectorLayer({
    source: vectorSource,
    style: styleFeature,
  });

  const map = new Map({
    target: mapEl,
    layers: [baseLayer, vectorLayer],
    interactions: defaultInteractions({
      dragPan: true,
      mouseWheelZoom: true,
      doubleClickZoom: true,
      pinchRotate: false,
      pinchZoom: true,
      shiftDragZoom: false,
      dragRotate: false,
    }),
    view: new View({
      center: fromLonLat([101.7, 0.6]),
      zoom: 7,
    }),
  });

  /* ------------------- Popup (FR-3.3) ------------------- */
  const popupElement = document.getElementById('popup');
  const popupCloseButton = document.getElementById('popup-closer');
  const popupContent = document.getElementById('popup-content');

  const popupOverlay = new Overlay({
    element: popupElement,
    autoPan: { animation: { duration: 250 } },
    positioning: 'bottom-center',
    offset: [0, -12],
  });
  map.addOverlay(popupOverlay);

  popupCloseButton.addEventListener('click', () => {
    popupOverlay.setPosition(undefined);
    popupElement.style.display = 'none';
  });

  map.on('click', (evt) => {
    const feature = map.forEachFeatureAtPixel(evt.pixel, (f) => f);
    if (!feature) {
      popupOverlay.setPosition(undefined);
      popupElement.style.display = 'none';
      return;
    }

    const namaKabupaten = feature.get(GEOJSON_PROP_NAMA);
    const guru = jumlahGuru[namaKabupaten] || 0;
    const siswa = jumlahSiswa[namaKabupaten] || 0;

    popupContent.innerHTML = `
      <p class="popup-title">${escapeHtml(namaKabupaten)}</p>
      <div class="popup-grid">
        <div class="popup-item">
          <b>${guru.toLocaleString('id-ID')}</b>
          <span>Guru</span>
        </div>
        <div class="popup-item">
          <b>${siswa.toLocaleString('id-ID')}</b>
          <span>Siswa</span>
        </div>
      </div>
      <p class="popup-total">Total <b>${(guru + siswa).toLocaleString('id-ID')}</b> data terdata</p>
    `;
    popupElement.style.display = 'block';
    popupOverlay.setPosition(evt.coordinate);
  });

  map.on('pointermove', (evt) => {
    map.getTargetElement().style.cursor = map.hasFeatureAtPixel(evt.pixel) ? 'pointer' : '';
  });

  vectorSource.on('change', () => {
    if (vectorSource.getState() === 'ready') {
      const extent = vectorSource.getExtent();
      if (extent && extent.some((v) => typeof v === 'number' && !Number.isNaN(v))) {
        map.getView().fit(extent, {
          padding: [20, 20, 20, 20],
          maxZoom: 9,
        });
      }
    }
  });

  /* ------------------- Kontrol (FR-3.6) ------------------- */
  const toggleBaseButton = document.getElementById('toggle-base');
  const togglePolygonsButton = document.getElementById('toggle-polygons');

  toggleBaseButton.addEventListener('click', () => {
    const visible = !baseLayer.getVisible();
    baseLayer.setVisible(visible);
    toggleBaseButton.textContent = visible ? 'Sembunyikan Peta' : 'Tampilkan Peta';
  });

  togglePolygonsButton.addEventListener('click', () => {
    const visible = !vectorLayer.getVisible();
    vectorLayer.setVisible(visible);
    togglePolygonsButton.textContent = visible ? 'Sembunyikan Poligon' : 'Tampilkan Poligon';
  });

  /* ------------------- Legenda (FR-3.7) ------------------- */
  const legendBody = document.getElementById('legend-body');
  const toggleLegendButton = document.getElementById('toggle-legend');

  toggleLegendButton.addEventListener('click', () => {
    const hidden = legendBody.style.display === 'none';
    legendBody.style.display = hidden ? '' : 'none';
    toggleLegendButton.textContent = hidden ? 'Sembunyikan' : 'Tampilkan';
  });

  /* ------------------- Ambil & tampilkan data ------------------- */
  let refreshing = false;
  let disposed = false;

  async function refreshData() {
    if (refreshing) return;
    refreshing = true;
    try {
      // Dijalankan independen: jika salah satu tabel gagal (mis. tabel siswa
      // belum dibuat), data tabel lain tetap tampil.
      const [guruRes, siswaRes] = await Promise.allSettled([
        fetchJumlahGuruPerKabupaten(),
        fetchJumlahSiswaPerKabupaten(),
      ]);
      // Halaman sudah ditinggalkan — jangan sentuh DOM yang sudah dibuang.
      if (disposed) return;

      if (guruRes.status === 'fulfilled') {
        jumlahGuru = guruRes.value;
        guruFailed = false;
      } else {
        guruFailed = true;
        console.warn('Gagal memuat data guru:', guruRes.reason);
      }
      if (siswaRes.status === 'fulfilled') {
        jumlahSiswa = siswaRes.value;
        siswaFailed = false;
      } else {
        siswaFailed = true;
        console.warn('Gagal memuat data siswa (apakah tabel siswa sudah dibuat?):', siswaRes.reason);
      }
      renderPanel();
      vectorLayer.changed();
    } finally {
      refreshing = false;
    }
  }

  function renderPanel() {
    const totalGuru = Object.values(jumlahGuru).reduce((a, b) => a + b, 0);
    const totalSiswa = Object.values(jumlahSiswa).reduce((a, b) => a + b, 0);
    // Tampilkan "—" bila sumber data gagal dimuat (bukan 0 yang menyesatkan).
    document.getElementById('total-guru').textContent = guruFailed ? '—' : totalGuru.toLocaleString('id-ID');
    document.getElementById('total-siswa').textContent = siswaFailed ? '—' : totalSiswa.toLocaleString('id-ID');
    document.getElementById('panel-updated').textContent =
      `Perbarui otomatis tiap ${Math.round(REFRESH_INTERVAL / 1000)} detik · ${new Date().toLocaleTimeString('id-ID')}`;

    // Legenda: warna + nama wilayah + jumlah guru/siswa.
    // Hanya tampilkan 12 wilayah resmi dari geojson — abaikan data
    // dengan nama kabupaten yang tidak cocok poligon mana pun (FR-7).
    const names = Array.from(
      new Set([...Object.keys(jumlahGuru), ...Object.keys(jumlahSiswa)])
    )
      .filter((n) => KABUPATEN_LIST.includes(n))
      .sort((a, b) => a.localeCompare(b, 'id'));

    legendBody.innerHTML = names
      .map((name) => {
        const guru = guruFailed ? '—' : (jumlahGuru[name] || 0).toLocaleString('id-ID');
        const siswa = siswaFailed ? '—' : (jumlahSiswa[name] || 0).toLocaleString('id-ID');
        return `
          <div class="legend-row">
            <span class="legend-swatch" style="background:${getColorForKabupaten(name)}" aria-hidden="true"></span>
            <span class="legend-name">${escapeHtml(name)}</span>
            <span class="legend-count">${guru} · ${siswa}</span>
          </div>
        `;
      })
      .join('');
  }

  // Polling otomatis — pola setInterval yang sudah ada (FR-3.5).
  refreshData();
  const interval = setInterval(refreshData, REFRESH_INTERVAL);

  // Segarkan data begitu tab kembali aktif (tanpa menunggu siklus polling).
  const onVisible = () => {
    if (document.visibilityState === 'visible') refreshData();
  };
  document.addEventListener('visibilitychange', onVisible);

  /* Cleanup saat pindah halaman (router memanggil ini). */
  return () => {
    disposed = true;
    clearInterval(interval);
    document.removeEventListener('visibilitychange', onVisible);
    map.setTarget(undefined);
    container.innerHTML = '';
  };
}
