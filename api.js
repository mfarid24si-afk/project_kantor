/* =====================================================================
 * HELPER SUPABASE (FR-5)
 * Menggunakan pola API key yang sama persis dengan implementasi awal:
 *   - URL : `${SUPABASE_URL}/rest/v1/<table>`
 *   - Header : `apikey` + `Authorization: Bearer <SUPABASE_ANON_KEY>`
 * Struktur ini TIDAK diubah; hanya diperluas untuk tabel `siswa`
 * (baca: hitung jumlah per kabupaten) dan untuk tulis dari Form (POST).
 * ===================================================================== */

import {
  SUPABASE_URL,
  SUPABASE_ANON_KEY,
  TABEL_GURU,
  TABEL_SISWA,
  KOLOM_KABUPATEN,
} from './config.js';

function supabaseHeaders() {
  return {
    apikey: SUPABASE_ANON_KEY,
    Authorization: `Bearer ${SUPABASE_ANON_KEY}`,
    'Content-Type': 'application/json',
  };
}

/** Ambil satu kolom dari sebuah tabel (GET). */
async function fetchKolom(tableName, kolom) {
  const url = `${SUPABASE_URL}/rest/v1/${tableName}?select=${kolom}`;
  const res = await fetch(url, { headers: supabaseHeaders() });
  if (!res.ok) {
    throw new Error(`Supabase fetch gagal: ${res.status}`);
  }
  return res.json();
}

/** Hitung jumlah baris per kabupaten dari daftar baris hasil query. */
function hitungPerKabupaten(rows) {
  const counts = {};
  for (const row of rows) {
    const key = (row[KOLOM_KABUPATEN] || '').trim();
    if (!key) continue;
    counts[key] = (counts[key] || 0) + 1;
  }
  return counts;
}

/** Jumlah guru per kabupaten (pola `fetchJumlahGuruPerKabupaten` yang sudah ada). */
export async function fetchJumlahGuruPerKabupaten() {
  return hitungPerKabupaten(await fetchKolom(TABEL_GURU, KOLOM_KABUPATEN));
}

/** Jumlah siswa per kabupaten — pola yang sama dengan guru (FR-3.4). */
export async function fetchJumlahSiswaPerKabupaten() {
  return hitungPerKabupaten(await fetchKolom(TABEL_SISWA, KOLOM_KABUPATEN));
}

/** Kirim satu baris data ke tabel (POST) — header identik dengan GET (FR-5.2). */
export async function insertBaris(tableName, payload) {
  const res = await fetch(`${SUPABASE_URL}/rest/v1/${tableName}`, {
    method: 'POST',
    headers: supabaseHeaders(),
    body: JSON.stringify(payload),
  });
  if (!res.ok) {
    const detail = await res.text();
    throw new Error(`Supabase insert gagal: ${res.status} — ${detail.slice(0, 200)}`);
  }
  return res.json();
}
