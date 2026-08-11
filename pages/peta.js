/* =====================================================================
 * HALAMAN PETA — Map View (FR-3)
 *  - 12 poligon kabupaten/kota dari Area_Kab_Riau.geojson (tidak diganti)
 *  - Warna per wilayah memakai logika getColorForKabupaten yang sudah ada;
 *    wilayah tanpa data ditampilkan abu-abu agar mudah dibedakan
 *  - Popup saat klik: nama wilayah + jumlah guru + jumlah siswa
 *  - Polling otomatis (pola setInterval yang sudah ada, 15 detik)
 *  - Basemap OSM tampil secara default (label tombol jelas)
 *  - Pemilih wilayah (select) sebagai jalur akses keyboard
 *  - Responsif desktop/mobile
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
import { getCenter } from 'ol/extent';

import { GEOJSON_PATH, GEOJSON_PROP_NAMA, REFRESH_INTERVAL, KABUPATEN_LIST } from '../config.js';
import {
  fetchJumlahGuruPerKabupaten,
  fetchJumlahSiswaPerKabupaten,
} from '../api.js';
import { escapeHtml } from '../ui.js';
import { footerHtml } from '../footer.js';
import { getColorForKabupaten } from '../colors.js';

// Warna poligon untuk wilayah yang belum punya data sama sekali.
const NO_DATA_COLOR = 'rgba(178, 184, 190, 0.55)';
const NO_DATA_SWATCH = '#b2b8be';

let jumlahGuru = {};
let jumlahSiswa = {};
let guruFailed = false;
let siswaFailed = false;

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

  const hasData =
    (jumlahGuru[namaKabupaten] || 0) + (jumlahSiswa[namaKabupaten] || 0) > 0;

  const polygonStyle = new Style({
    fill: new Fill({ color: hasData ? getColorForKabupaten(namaKabupaten) : NO_DATA_COLOR }),
    stroke: new Stroke({ color: hasData ? '#2f6b4f' : '#9aa0a6', width: 1 }),
  });

  const textStyle = new Style({
    geometry: labelGeometry || undefined,
    text: new Text({
      text: namaKabupaten || '',
      font: 'bold 12px Calibri, sans-serif',
      fill: new Fill({ color: '#ffffff' }),
      stroke: new Stroke({ color: hasData ? '#000000' : '#3c4043', width: 2 }),
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
        <a class="back-link" href="/" data-route="/">← Kembali ke Beranda</a>
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

        <div class="region-picker">
          <label for="region-select">Pilih wilayah</label>
          <select id="region-select" aria-label="Pilih kabupaten/kota untuk dilihat di peta">
            <option value="">— Pilih kabupaten/kota —</option>
            ${KABUPATEN_LIST.map((n) => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`).join('')}
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
    ${footerHtml()}
  `;

  const mapEl = document.getElementById('map');

  // Basemap OSM — tampil secara default (label tombol mengikuti statusnya).
  const baseLayer = new TileLayer({ source: new OSM(), visible: true });

  const vectorSource = new VectorSource();

  // Hanya render wilayah resmi (KABUPATEN_LIST): geojson dimuat manual
  // lalu difilter + dideduplikasi agar poligon hanya mencakup 12 daerah
  // yang sudah ditetapkan (konsisten dengan legenda & dropdown).
  fetch(GEOJSON_PATH)
    .then((res) => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then((geojson) => {
      const features = new GeoJSON().readFeatures(geojson, {
        dataProjection: 'EPSG:4326',
        featureProjection: 'EPSG:3857',
      });
      const seen = new Set();
      const valid = [];
      for (const feature of features) {
        const nama = feature.get(GEOJSON_PROP_NAMA);
        if (KABUPATEN_LIST.includes(nama) && !seen.has(nama)) {
          seen.add(nama);
          valid.push(feature);
        }
      }
      vectorSource.addFeatures(valid);
      const extent = vectorSource.getExtent();
      if (extent && extent.some((v) => typeof v === 'number' && !Number.isNaN(v))) {
        map.getView().fit(extent, { padding: [20, 20, 20, 20], maxZoom: 9 });
      }
    })
    .catch((err) => {
      console.error('Gagal memuat GeoJSON:', err);
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

  /** Isi konten popup untuk sebuah nama wilayah. */
  function setPopupContent(name) {
    const guru = jumlahGuru[name] || 0;
    const siswa = jumlahSiswa[name] || 0;
    popupContent.innerHTML = `
      <p class="popup-title">${escapeHtml(name)}</p>
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
  }

  /** Tampilkan data & posisikan popup untuk sebuah wilayah bernama. */
  function showRegion(name) {
    if (!name) return;
    const feature = vectorSource.getFeatures().find((f) => f.get(GEOJSON_PROP_NAMA) === name);
    // GeoJSON belum siap — jangan tampilkan popup tanpa posisi.
    if (!feature) return;

    setPopupContent(name);
    popupElement.style.display = 'block';

    const geometry = feature.getGeometry();
    if (geometry && typeof geometry.getExtent === 'function') {
      const extent = geometry.getExtent();
      map.getView().fit(extent, { padding: [40, 40, 40, 40], maxZoom: 9 });
      popupOverlay.setPosition(getCenter(extent));
    }
  }

  map.on('click', (evt) => {
    const feature = map.forEachFeatureAtPixel(evt.pixel, (f) => f);
    if (!feature) {
      popupOverlay.setPosition(undefined);
      popupElement.style.display = 'none';
      return;
    }
    setPopupContent(feature.get(GEOJSON_PROP_NAMA));
    popupElement.style.display = 'block';
    popupOverlay.setPosition(evt.coordinate);
  });

  map.on('pointermove', (evt) => {
    map.getTargetElement().style.cursor = map.hasFeatureAtPixel(evt.pixel) ? 'pointer' : '';
  });

  /* ------------------- Pemilih wilayah (keyboard) ------------------- */
  document.getElementById('region-select').addEventListener('change', (e) => {
    showRegion(e.target.value);
  });

  /* ------------------- Kontrol (FR-3.6) ------------------- */
  const toggleBaseButton = document.getElementById('toggle-base');
  const togglePolygonsButton = document.getElementById('toggle-polygons');

  toggleBaseButton.addEventListener('click', () => {
    const visible = !baseLayer.getVisible();
    baseLayer.setVisible(visible);
    toggleBaseButton.textContent = visible ? 'Sembunyikan Peta Dasar' : 'Tampilkan Peta Dasar';
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

    // Baris "Belum ada data" hanya tampil bila ada wilayah yang belum terisi.
    const adaWilayahKosong = names.length < KABUPATEN_LIST.length;

    legendBody.innerHTML =
      names
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
        .join('') +
      (adaWilayahKosong
        ? `
          <div class="legend-row legend-row--nodata">
            <span class="legend-swatch" style="background:${NO_DATA_SWATCH}" aria-hidden="true"></span>
            <span class="legend-name">Belum ada data</span>
          </div>
        `
        : '');
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
