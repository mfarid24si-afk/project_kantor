/* =====================================================================
 * UTILITAS UI BERSAMA — toast, daftar kabupaten, escape HTML
 * ===================================================================== */

import { GEOJSON_PATH, GEOJSON_PROP_NAMA, KABUPATEN_LIST } from './config.js';

let toastTimer = null;

/** Notifikasi singkat di bagian bawah layar. */
export function showToast(message, type = 'success') {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = message;
  el.className = `toast toast--${type} toast--show`;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('toast--show'), 4000);
}

/**
 * Daftar 12 kabupaten/kota, diambil langsung dari properti "Keterangan"
 * pada Area_Kab_Riau.geojson agar selalu konsisten dengan peta.
 * Jika fetch gagal (offline), fallback ke KABUPATEN_LIST dari config.
 */
let kabupatenCache = null;

export async function getKabupatenList() {
  if (kabupatenCache) return kabupatenCache;
  try {
    const res = await fetch(GEOJSON_PATH);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const geojson = await res.json();
    const names = [];
    for (const feature of geojson.features || []) {
      const name = String(feature.properties?.[GEOJSON_PROP_NAMA] || '').trim();
      if (name && !names.includes(name)) names.push(name);
    }
    if (names.length > 0) {
      names.sort((a, b) => a.localeCompare(b, 'id'));
      kabupatenCache = names;
      return names;
    }
  } catch (err) {
    console.warn('Gagal membaca geojson untuk daftar kabupaten:', err);
  }
  kabupatenCache = [...KABUPATEN_LIST];
  return kabupatenCache;
}

/** Amankan teks sebelum disisipkan ke innerHTML. */
export function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  }[c]));
}
