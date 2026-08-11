# Peta Sebaran Guru & Siswa Provinsi Riau

Aplikasi web interaktif berbasis **Vite + OpenLayers + Supabase** untuk memantau sebaran
tenaga pendidik (guru) dan peserta didik (siswa) di **12 kabupaten/kota Provinsi Riau**.

## Halaman

| Rute | Keterangan |
|------|------------|
| `/` | Homepage — penjelasan produk + ringkasan statistik live (jumlah kabupaten, guru, siswa) |
| `/peta` | Peta interaktif 12 poligon kabupaten/kota; klik wilayah untuk melihat jumlah guru & siswa |
| `/form` | Form input data guru / siswa — tersimpan ke Supabase, langsung tampil di peta (polling otomatis 15 detik) |

## Struktur Kode

```
config.js          → Konfigurasi terpusat (FR-5.3): API key Supabase, tabel, kolom, geojson, interval
api.js             → Helper Supabase: hitung jumlah per kabupaten (GET) + insert data (POST)
router.js          → Routing client-side history mode (/, /peta, /form)
ui.js              → Toast notifikasi, daftar kabupaten dari geojson, escapeHtml
pages/home.js      → Homepage (hero, statistik live, CTA)
pages/peta.js      → Halaman peta (popup guru+siswa, legenda, kontrol, polling)
pages/form.js      → Halaman form (validasi, dropdown kabupaten, submit ke Supabase)
Area_Kab_Riau.geojson → Batas wilayah 12 kabupaten/kota (dipakai apa adanya)
peta-guru-riau.html   → Versi standalone lama (CDN), di luar lingkup PRD — tidak diubah
```

## Menjalankan

```bash
npm install     # install dependensi
npm start       # dev server → http://localhost:5173
npm run build   # build produksi → dist/
npm run serve   # preview hasil build
```

## Konfigurasi API Key (JANGAN diubah)

Struktur koneksi Supabase dipertahankan dari implementasi awal:

- URL: `${SUPABASE_URL}/rest/v1/<table>`
- Header: `apikey: <SUPABASE_ANON_KEY>` dan `Authorization: Bearer <SUPABASE_ANON_KEY>`

Nilai `SUPABASE_URL` dan `SUPABASE_ANON_KEY` ada di `config.js` — jangan diubah.
Pola ini dipakai untuk baca (peta, statistik homepage) maupun tulis (form).

## Setup Supabase

Tabel yang dipakai:

| Tabel | Kolom |
|-------|-------|
| `guru` (sudah ada) | `nama`, `kabupaten`, `sekolah` |
| `siswa` (baru) | `nama`, `kabupaten`, `sekolah`, `jenjang` (opsional) |

Jika tabel `siswa` perlu dibuat, jalankan SQL ini di **SQL Editor** Supabase:

```sql
create table public.siswa (
  id uuid primary key default gen_random_uuid(),
  created_at timestamptz not null default now(),
  nama text not null,
  kabupaten text not null,
  sekolah text not null,
  jenjang text
);

alter table public.siswa enable row level security;

-- Izinkan baca (peta) & tulis (form) untuk anon key
create policy "siswa public select" on public.siswa for select to anon using (true);
create policy "siswa public insert" on public.siswa for insert to anon with check (true);
```

Agar **Form dapat mengirim data**, tabel `guru` juga perlu policy insert untuk `anon`:

```sql
create policy "guru public insert" on public.guru for insert to anon with check (true);
```

> Catatan: jika policy RLS belum ada, peta tetap bisa membaca, tetapi submit Form akan
> gagal dengan pesan 403/401 — pastikan policy di atas sudah dijalankan.

Nama kabupaten pada dropdown Form **persis sama** dengan properti `Keterangan` di
`Area_Kab_Riau.geojson` (termasuk `Kuantansingingi`, `Rohil`, `Rohul`) agar setiap entri
pasti terhitung ke poligon yang benar.

## Catatan Hosting (routing history mode)

Aplikasi memakai routing history (`/peta`, `/form`) sehingga hosting statis perlu
aturan fallback agar tidak 404 saat halaman dibuka/direfresh langsung:

- **Netlify**: file `public/_redirects` sudah disertakan (`/* /index.html 200`) — otomatis terbawa saat build.
- **Vercel**: tambahkan `vercel.json`:
  ```json
  { "rewrites": [{ "source": "/(.*)", "destination": "/index.html" }] }
  ```
- **GitHub Pages**: default tidak mendukung fallback; gunakan trik `404.html` yang
  mengarahkan ke `index.html`, atau beralih ke hash routing (`#/peta`).
- **Vite dev server / `vite preview`**: sudah mendukung fallback otomatis.

## Kriteria Penerimaan (ringkas)

- [x] Peta menampilkan 12 poligon kabupaten/kota dari `Area_Kab_Riau.geojson`
- [x] Klik poligon menampilkan jumlah guru **dan** siswa
- [x] Data dari Form muncul di peta dalam satu siklus polling (15 detik)
- [x] Navbar dengan tombol Peta & Form responsif di mobile (hamburger)
- [x] Tidak ada teks bergradasi (`background-clip: text` tidak dipakai di mana pun)
- [x] Skala font mengikuti panduan Bagian 9 PRD (H1 28–32px, H2 20–24px, body 15–16px, label 12–13px)
- [x] Konfigurasi API key peta tidak diubah dari implementasi awal
