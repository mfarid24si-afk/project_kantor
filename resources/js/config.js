/* =====================================================================
 * KONFIGURASI TERPUSAT APLIKASI
 * Konstanta tabel dan kolom untuk integrasi API internal Laravel.
 * Data tersimpan di database lokal MySQL via endpoint /api.
 * ===================================================================== */

// --- Tabel & kolom Supabase (SKEMA ASLI, hasil verifikasi langsung) ---
export const TABEL_GURU = 'guru';
export const TABEL_SISWA = 'siswa';
export const TABEL_KOMUNITAS = 'komunitas';
export const TABEL_UMUM = 'umum';

// Nama kolom kabupaten.
export const KOLOM_KABUPATEN = 'kabupaten';

// Daftar 4 tabel utama untuk form & detail.
export const SEMUA_TABEL = [
  { key: 'guru', table: TABEL_GURU, label: 'Guru', hasKabupaten: true },
  { key: 'siswa', table: TABEL_SISWA, label: 'Siswa', hasKabupaten: true },
  { key: 'komunitas', table: TABEL_KOMUNITAS, label: 'Komunitas', hasKabupaten: true },
  { key: 'umum', table: TABEL_UMUM, label: 'Umum', hasKabupaten: true },
];

// Kolom tabel `guru`:
export const KOLOM_GURU = [
  'nama_guru',
  'kabupaten',
  'asal_sekolah',
  'nuptk',
  'kelurahan',
  'provinsi',
  'nama_guru_utama',
];

// Kolom tabel `siswa`:
export const KOLOM_SISWA = ['nama_siswa', 'kabupaten', 'sekolah', 'nis', 'alamat_sekolah'];

// Kolom tabel `komunitas`:
export const KOLOM_KOMUNITAS = ['nama_individu', 'nama_komunitas', 'kabupaten', 'alamat_afiliasi'];

// Kolom tabel `umum`:
export const KOLOM_UMUM = ['nama_umum', 'pekerjaan', 'kabupaten', 'alamat'];

// --- GeoJSON batas wilayah (dipakai apa adanya, tidak digambar ulang) ---
export const GEOJSON_PATH = '/geojson/Area_Kab_Riau.geojson';
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
