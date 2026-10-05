/* =====================================================================
 * HELPER API LOKAL LARAVEL (MySQL Local Storage)
 * Menggantikan remote Supabase dengan endpoint API internal Laravel.
 * Semua data guru, siswa, komunitas, dan umum tersimpan di MySQL lokal.
 * ===================================================================== */

const API_BASE = '/api';

function defaultHeaders() {
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };
}

/** Jumlah guru per kabupaten (agregasi langsung dari database lokal). */
export async function fetchJumlahGuruPerKabupaten() {
  const res = await fetch(`${API_BASE}/guru/counts`, { headers: defaultHeaders() });
  if (!res.ok) {
    throw new Error(`Gagal mengambil data jumlah guru: ${res.status}`);
  }
  return res.json();
}

/** Jumlah siswa per kabupaten. */
export async function fetchJumlahSiswaPerKabupaten() {
  const res = await fetch(`${API_BASE}/siswa/counts`, { headers: defaultHeaders() });
  if (!res.ok) {
    throw new Error(`Gagal mengambil data jumlah siswa: ${res.status}`);
  }
  return res.json();
}

/** Ambil baris data lengkap untuk sebuah kabupaten. */
export async function fetchRowsByKabupaten(tableName, kabupaten) {
  const url = `${API_BASE}/data/${tableName}?kabupaten=${encodeURIComponent(kabupaten)}`;
  const res = await fetch(url, { headers: defaultHeaders() });
  if (!res.ok) {
    throw new Error(`Gagal mengambil data ${tableName}: ${res.status}`);
  }
  return res.json();
}

/** Ambil semua baris dari sebuah tabel tanpa filter kabupaten. */
export async function fetchAllRows(tableName) {
  const url = `${API_BASE}/data/${tableName}`;
  const res = await fetch(url, { headers: defaultHeaders() });
  if (!res.ok) {
    throw new Error(`Gagal mengambil data ${tableName}: ${res.status}`);
  }
  return res.json();
}

/** Kirim satu baris data ke tabel (POST) — dipakai Form. */
export async function insertBaris(tableName, payload) {
  const res = await fetch(`${API_BASE}/data/${tableName}`, {
    method: 'POST',
    headers: defaultHeaders(),
    body: JSON.stringify(payload),
  });
  if (!res.ok) {
    const detail = await res.text();
    throw new Error(`Gagal menyimpan data: ${res.status} — ${detail.slice(0, 200)}`);
  }
  return true;
}

/** Kirim banyak baris sekaligus (bulk insert untuk upload CSV). */
export async function insertBanyakBaris(tableName, rows) {
  const res = await fetch(`${API_BASE}/data/${tableName}/batch`, {
    method: 'POST',
    headers: defaultHeaders(),
    body: JSON.stringify(rows),
  });
  if (!res.ok) {
    const detail = await res.text();
    throw new Error(`Gagal upload batch: ${res.status} — ${detail.slice(0, 200)}`);
  }
  return true;
}
