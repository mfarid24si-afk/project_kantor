/* =====================================================================
 * HALAMAN HOME — landing page (FR-2)
 *  - Hero + ringkasan live (12 kabupaten, total guru, total siswa)
 *  - Sebaran data per wilayah (bar chart live, warna sama dengan peta)
 *  - Cara menggunakan, fitur, CTA
 *  - Animasi scroll-reveal & count-up; gradasi hanya pada elemen
 *    non-teks; teks warna solid sesuai panduan tipografi (Bagian 9 PRD).
 * ===================================================================== */

import { KABUPATEN_LIST } from '../config.js';
import { fetchJumlahGuruPerKabupaten, fetchJumlahSiswaPerKabupaten } from '../api.js';
import { getKabupatenList, escapeHtml } from '../ui.js';
import { getColorForKabupaten } from '../colors.js';

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

const STEPS = [
  {
    icon: '🗺️',
    title: 'Buka Peta',
    desc: 'Telusuri 12 kabupaten/kota Riau pada peta interaktif dengan batas wilayah administratif.',
  },
  {
    icon: '👆',
    title: 'Klik Wilayah',
    desc: 'Klik poligon mana pun untuk melihat jumlah guru & siswa terdata di wilayah tersebut.',
  },
  {
    icon: '📝',
    title: 'Input Data',
    desc: 'Tambah data guru atau siswa lewat Form — dalam beberapa detik langsung tampil di peta.',
  },
];

const HIGHLIGHTS = [
  { icon: '⚡', text: 'Data realtime dari Supabase' },
  { icon: '🔄', text: 'Update otomatis tiap 15 detik' },
  { icon: '🏛️', text: '12 kabupaten/kota se-Riau' },
];

export function renderHome(container) {
  container.innerHTML = `
    <div class="page-home">
      <section class="hero">
        <div class="blob blob-1" aria-hidden="true"></div>
        <div class="blob blob-2" aria-hidden="true"></div>
        <div class="blob blob-3" aria-hidden="true"></div>
        <div class="hero-inner">
          <span class="hero-badge">🗺️ Provinsi Riau · 12 Kabupaten/Kota</span>
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
          <div class="hero-highlights">
            ${HIGHLIGHTS.map(
              (h) => `
              <span class="highlight"><span aria-hidden="true">${h.icon}</span>${h.text}</span>
            `
            ).join('')}
          </div>
        </div>
      </section>

      <section class="stats reveal" aria-label="Ringkasan data">
        <div class="stat-card">
          <span class="stat-icon" aria-hidden="true">🏙️</span>
          <div class="stat-number" id="stat-kab">—</div>
          <div class="stat-label">Kabupaten/Kota</div>
        </div>
        <div class="stat-card">
          <span class="stat-icon" aria-hidden="true">👩‍🏫</span>
          <div class="stat-number" id="stat-guru">—</div>
          <div class="stat-label">Guru Terdata</div>
        </div>
        <div class="stat-card">
          <span class="stat-icon" aria-hidden="true">🎓</span>
          <div class="stat-number" id="stat-siswa">—</div>
          <div class="stat-label">Siswa Terdata</div>
        </div>
      </section>

      <section class="section-block reveal">
        <div class="section-head">
          <h2 class="section-title">Sebaran Data per Wilayah</h2>
          <a class="section-link" href="/peta" data-route="/peta">Lihat di Peta →</a>
        </div>
        <p class="section-desc">
          Total data terdata (guru + siswa) setiap kabupaten/kota, diurutkan
          dari yang terbanyak — diperbarui otomatis dari Supabase.
        </p>
        <div class="region-bars" id="region-bars">
          <p class="region-empty">Memuat data wilayah…</p>
        </div>
      </section>

      <section class="section-block reveal">
        <h2 class="section-title">Cara Menggunakan</h2>
        <div class="steps-grid">
          ${STEPS.map(
            (s, i) => `
            <article class="step-card">
              <span class="step-number" aria-hidden="true">${i + 1}</span>
              <span class="step-icon" aria-hidden="true">${s.icon}</span>
              <h3>${s.title}</h3>
              <p>${s.desc}</p>
            </article>
          `
          ).join('')}
        </div>
      </section>

      <section class="features reveal">
        <h2 class="section-title">Fitur Utama</h2>
        <div class="feature-grid">
          ${FEATURES.map(
            (f) => `
            <article class="feature-card">
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
    <footer class="footer">
      <b>Peta Sebaran Guru &amp; Siswa — Provinsi Riau</b> · © 2026 · Politeknik Caltex Riau
    </footer>
  `;

  observeReveals(container);
  loadHomeData(container);
}

async function loadHomeData(root) {
  // Satu kali pengambilan data, dipakai bersama statistik & bar chart
  // (menghindari request Supabase ganda di homepage).
  const [guruRes, siswaRes, kabRes] = await Promise.allSettled([
    fetchJumlahGuruPerKabupaten(),
    fetchJumlahSiswaPerKabupaten(),
    getKabupatenList(),
  ]);
  renderStats(root, guruRes, siswaRes, kabRes);
  renderBars(root, guruRes, siswaRes, kabRes);
}

function renderStats(root, guruRes, siswaRes, kabRes) {
  const statKab = root.querySelector('#stat-kab');
  const statGuru = root.querySelector('#stat-guru');
  const statSiswa = root.querySelector('#stat-siswa');
  if (!statKab || !statGuru || !statSiswa) return;

  const guru = guruRes.status === 'fulfilled' ? guruRes.value : {};
  const siswa = siswaRes.status === 'fulfilled' ? siswaRes.value : {};
  const kab = kabRes.status === 'fulfilled' ? kabRes.value : KABUPATEN_LIST;
  if (guruRes.status === 'rejected') console.warn('Gagal memuat statistik guru:', guruRes.reason);
  if (siswaRes.status === 'rejected') console.warn('Gagal memuat statistik siswa:', siswaRes.reason);

  const totalGuru = Object.values(guru).reduce((a, b) => a + b, 0);
  const totalSiswa = Object.values(siswa).reduce((a, b) => a + b, 0);

  animateNumber(statKab, kab.length);
  // "—" bila sumber gagal dimuat (membedakan dari 0 yang sebenarnya).
  if (guruRes.status === 'fulfilled') animateNumber(statGuru, totalGuru);
  else statGuru.textContent = '—';
  if (siswaRes.status === 'fulfilled') animateNumber(statSiswa, totalSiswa);
  else statSiswa.textContent = '—';
}

function renderBars(root, guruRes, siswaRes, kabRes) {
  const barsEl = root.querySelector('#region-bars');
  if (!barsEl) return;

  const guru = guruRes.status === 'fulfilled' ? guruRes.value : {};
  const siswa = siswaRes.status === 'fulfilled' ? siswaRes.value : {};
  const kab = kabRes.status === 'fulfilled' ? kabRes.value : KABUPATEN_LIST;

  const bothKnown = guruRes.status === 'fulfilled' && siswaRes.status === 'fulfilled';
  const rows = kab
    .map((name) => {
      const g = guru[name] || 0;
      const s = siswa[name] || 0;
      return {
        name,
        guru: g,
        siswa: s,
        total: g + s,
        guruKnown: guruRes.status === 'fulfilled',
        siswaKnown: siswaRes.status === 'fulfilled',
      };
    })
    .sort((a, b) => b.total - a.total);

  const max = Math.max(1, ...rows.map((r) => r.total));

  barsEl.innerHTML = rows
    .map((r) => {
      const guruText = r.guruKnown ? r.guru.toLocaleString('id-ID') : '—';
      const siswaText = r.siswaKnown ? r.siswa.toLocaleString('id-ID') : '—';
      // "—" bila kedua sumber tidak tersedia (jangan tampilkan 0 yang menyesatkan).
      const totalText = bothKnown ? (r.total > 0 ? r.total.toLocaleString('id-ID') : '0') : '—';
      const width = Math.round((r.total / max) * 100);
      return `
        <div class="region-bar" title="${escapeHtml(r.name)} — ${guruText} guru · ${siswaText} siswa">
          <span class="legend-swatch" style="background:${getColorForKabupaten(r.name)}" aria-hidden="true"></span>
          <span class="region-name">${escapeHtml(r.name)}</span>
          <span class="region-track" aria-hidden="true">
            <span class="region-fill" style="width:${width}%"></span>
          </span>
          <span class="region-count">${totalText}</span>
        </div>
      `;
    })
    .join('');
}

/** Animasi hitung naik untuk angka statistik (micro-interaction). */
function animateNumber(el, target) {
  if (!Number.isFinite(target)) {
    el.textContent = target;
    return;
  }
  const duration = 800;
  const start = performance.now();
  function step(now) {
    const p = Math.min(1, (now - start) / duration);
    const eased = 1 - Math.pow(1 - p, 3);
    el.textContent = Math.round(target * eased).toLocaleString('id-ID');
    if (p < 1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
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
