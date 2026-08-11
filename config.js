/* =====================================================================
 * KONFIGURASI TERPUSAT APLIKASI (FR-5.3)
 * Semua konstanta dipusatkan di satu tempat agar halaman Form dan Peta
 * membaca sumber yang sama — menghindari duplikasi nama tabel/kolom.
 *
 * PENTING: nilai SUPABASE_URL dan SUPABASE_ANON_KEY TIDAK BOLEH DIUBAH
 * dari implementasi awal. Struktur API key (header apikey / Authorization)
 * dipertahankan apa adanya — hanya diperluas untuk tabel `siswa`.
 * ===================================================================== */

// --- Supabase (API key dari implementasi awal, jangan diubah) ---
export const SUPABASE_URL = 'https://lvorvfhdrlagcpsimteo.supabase.co';
export const SUPABASE_ANON_KEY =
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx2b3J2ZmhkcmxhZ2Nwc2ltdGVvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODYzMzIwNTAsImV4cCI6MjEwMTkwODA1MH0.gM7BvTL1wNKVx6Oxozry_s8bazxTNtRbU7u0p7Lx3pA';

// --- Tabel & kolom Supabase ---
export const TABEL_GURU = 'guru';
export const TABEL_SISWA = 'siswa';
// Nama kolom kabupaten (sama di kedua tabel).
export const KOLOM_KABUPATEN = 'kabupaten';

// Kolom tabel `guru` — SKEMA ASLI di Supabase (hasil verifikasi langsung):
// id, nama_guru, nuptk, asal_sekolah, kelurahan, kabupaten, provinsi,
// nama_guru_utama, created_at. Form memakai daftar ini apa adanya.
// Urutan di sini = urutan field pada Form. Catatan: `nuptk` dikirim sebagai
// angka (kolomnya bertipe integer).
export const KOLOM_GURU = [
  'nama_guru',
  'kabupaten',
  'asal_sekolah',
  'nuptk',
  'kelurahan',
  'provinsi',
  'nama_guru_utama',
];

// Kolom tabel `siswa` (belum dibuat di Supabase — struktur mengikuti PRD).
// Saat tabel siswa sudah dibuat, sesuaikan daftar ini dengan skema aslinya.
export const KOLOM_SISWA = ['nama', 'sekolah', 'kabupaten', 'jenjang'];

// --- GeoJSON batas wilayah (dipakai apa adanya, tidak digambar ulang) ---
export const GEOJSON_PATH = 'Area_Kab_Riau.geojson';
export const GEOJSON_PROP_NAMA = 'Keterangan';

// 12 nama kabupaten/kota — PERSIS sama dengan nilai properti "Keterangan"
// pada Area_Kab_Riau.geojson (FR-7: konsistensi wilayah). Dipakai sebagai
// fallback daftar dropdown Form & validasi agar data tidak "nyasar".
export const KABUPATEN_LIST = [
  'Bengkalis',
  'Dumai',
  'Indragiri Hilir',
  'Indragiri Hulu',
  'Kampar',
  'Kuantansingingi',
  'Meranti',
  'Pekanbaru',
  'Pelalawan',
  'Rohil',
  'Rohul',
  'Siak',
];

// --- Interval polling pembaruan data peta (ms).
// Mempertahankan pola setInterval yang sudah ada (FR-3.5). ---
export const REFRESH_INTERVAL = 15000;
