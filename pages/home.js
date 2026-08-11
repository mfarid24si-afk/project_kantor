/* =====================================================================
 * HALAMAN HOME — landing page (FR-2)
 *  - Menjelaskan kegunaan peta
 *  - Statistik ringkas live dari Supabase (12 kabupaten, total guru, total siswa)
 *  - CTA menuju /peta dan /form
 *  - Animasi scroll-reveal & dekorasi gradasi hanya pada elemen non-teks;
 *    teks memakai warna solid sesuai panduan tipografi (Bagian 9 PRD).
 * ===================================================================== */

import { KABUPATEN_LIST } from '../config.js';
import { fetchJumlahGuruPerKabupaten, fetchJumlahSiswaPerKabupaten } from '../api.js';
import { getKabupatenList } from '../ui.js';

const FEATURES = [
  {
    icon: '🗺️',
    title: 'Peta Interaktif',
    desc: '12 poligon kabupaten/kota dengan batas administratif akurat. Klik wilayah mana pun untuk melihat rincian data guru dan siswa.',
  },
  {
    icon: '🔄',
    title: 'Data Realtime',
    desc: 'Data yang diinput lewat Form langsung tampil di peta secara otomatis, tanpa perlu reload manual oleh pengguna lain.',
  },
  {
    icon: '📝',
    title: 'Form Input',
    desc: 'Entri data guru & siswa cukup lewat form sederhana. Tersimpan ke Supabase dan langsung terhitung ke wilayah yang tepat.',
  },
];

export function renderHome(container) {
  container.innerHTML = `
    <div class="page-home">
      <section class="hero">
        <div class="blob blob-1" aria-hidden="true"></div>
        <div class="blob blob-2" aria-hidden="true"></div>
        <div class="blob blob-3" aria-hidden="true"></div>
        <div class="hero-inner">
          <span class="hero-badge">Provinsi Riau · 12 Kabupaten/Kota</span>
          <h1>Peta Sebaran Guru &amp; Siswa Provinsi Riau</h1>
          <p class="hero-lead">
            Pantau sebaran tenaga pendidik dan peserta didik di seluruh
            kabupaten/kota se-Riau dalam satu peta interaktif. Data selalu
            diperbarui otomatis dari Supabase.
          </p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="/peta" data-route="/peta">Buka Peta</a>
            <a class="btn btn-outline" href="/form" data-route="/form">Input Data</a>
          </div>
        </div>
      </section>

      <section class="stats reveal" aria-label="Ringkasan data">
        <div class="stat-card">
          <div class="stat-number" id="stat-kab">—</div>
          <div class="stat-label">Kabupaten/Kota</div>
        </div>
        <div class="stat-card">
          <div class="stat-number" id="stat-guru">—</div>
          <div class="stat-label">Guru Terdata</div>
        </div>
        <div class="stat-card">
          <div class="stat-number" id="stat-siswa">—</div>
          <div class="stat-label">Siswa Terdata</div>
        </div>
      </section>

      <section class="features">
        <h2 class="section-title reveal">Apa yang bisa Anda lakukan?</h2>
        <div class="feature-grid">
          ${FEATURES.map(
            (f, i) => `
            <article class="feature-card reveal">
              <span class="feature-icon" aria-hidden="true">${f.icon}</span>
              <h3>${f.title}</h3>
              <p>${f.desc}</p>
            </article>
          `
          ).join('')}
        </div>
      </section>

      <section class="cta-band reveal">
        <h2>Siap melihat datanya?</h2>
        <p>Buka peta untuk menjelajah, atau isi form untuk menambah data.</p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="/peta" data-route="/peta">Buka Peta</a>
          <a class="btn btn-light" href="/form" data-route="/form">Input Data</a>
        </div>
      </section>
    </div>
  `;

  observeReveals(container);
  loadStats(container);
}

async function loadStats(root) {
  const statKab = root.querySelector('#stat-kab');
  const statGuru = root.querySelector('#stat-guru');
  const statSiswa = root.querySelector('#stat-siswa');

  // Dijalankan independen: kegagalan satu sumber tidak menghapus data lain.
  const [guruRes, siswaRes, kabRes] = await Promise.allSettled([
    fetchJumlahGuruPerKabupaten(),
    fetchJumlahSiswaPerKabupaten(),
    getKabupatenList(),
  ]);
  const guru = guruRes.status === 'fulfilled' ? guruRes.value : {};
  const siswa = siswaRes.status === 'fulfilled' ? siswaRes.value : {};
  const kab = kabRes.status === 'fulfilled' ? kabRes.value : KABUPATEN_LIST;
  if (guruRes.status === 'rejected') console.warn('Gagal memuat statistik guru:', guruRes.reason);
  if (siswaRes.status === 'rejected') console.warn('Gagal memuat statistik siswa:', siswaRes.reason);

  const totalGuru = Object.values(guru).reduce((a, b) => a + b, 0);
  const totalSiswa = Object.values(siswa).reduce((a, b) => a + b, 0);
  statKab.textContent = kab.length;
  // "—" bila sumber gagal dimuat (membedakan dari 0 yang sebenarnya).
  statGuru.textContent = guruRes.status === 'fulfilled' ? totalGuru.toLocaleString('id-ID') : '—';
  statSiswa.textContent = siswaRes.status === 'fulfilled' ? totalSiswa.toLocaleString('id-ID') : '—';
}

/** Animasi scroll-reveal: fade + slide masuk saat elemen terlihat. */
function observeReveals(root) {
  const els = root.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window)) {
    els.forEach((el) => el.classList.add('is-visible'));
    return;
  }
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );
  els.forEach((el) => io.observe(el));
}
