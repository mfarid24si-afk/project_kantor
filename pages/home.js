import { KABUPATEN_LIST } from '../config.js';
import { fetchJumlahGuruPerKabupaten, fetchJumlahSiswaPerKabupaten } from '../api.js';
import { getKabupatenList } from '../ui.js';
import { icon } from '../icons.js';
import { footerHtml } from '../footer.js';

const FEATURES = [
  {
    icon: 'map',
    title: 'Peta Interaktif',
    desc: '12 poligon kabupaten/kota dengan batas administratif akurat. Klik wilayah mana pun untuk melihat rincian guru dan siswa yang telah terimbas.',
    color: 'blue',
    detail: [
      'Batas wilayah mengikuti data administratif resmi',
      'Klik poligon untuk melihat rincian guru & siswa terimbas secara instan',
    ],
  },
  {
    icon: 'refresh',
    title: 'Data Realtime',
    desc: 'Data yang diinput lewat Form langsung tampil di peta secara otomatis, tanpa perlu reload manual oleh pengguna lain.',
    color: 'orange',
    detail: [
      'Sinkron otomatis lewat Supabase, tanpa refresh manual',
      'Perubahan langsung terlihat oleh semua pengguna lain',
    ],
  },
  {
    icon: 'pen',
    title: 'Form Input',
    desc: 'Entri data guru & siswa yang telah menerima pengimbasan cukup lewat form sederhana, tersimpan ke Supabase dan langsung terhitung ke wilayah yang tepat.',
    color: 'green',
    detail: [
      'Validasi otomatis sebelum data tersimpan',
      'Notifikasi konfirmasi begitu data berhasil dikirim',
    ],
  },
];

const STEPS = [
  {
    icon: 'map',
    title: 'Buka Peta',
    desc: 'Telusuri 12 kabupaten/kota Riau pada peta interaktif dengan batas wilayah administratif.',
  },
  {
    icon: 'click',
    title: 'Klik Wilayah',
    desc: 'Klik poligon mana pun untuk melihat jumlah guru & siswa terimbas di wilayah tersebut.',
  },
  {
    icon: 'pen',
    title: 'Input Data',
    desc: 'Tambah data guru atau siswa yang telah terimbas lewat Form — dalam beberapa detik langsung tampil di peta.',
  },
];

const HIGHLIGHTS = [
  { icon: 'zap', text: 'Data realtime dari Supabase' },
  { icon: '🔄', text: 'Update otomatis tiap 15 detik' },
  { icon: 'book', text: 'Bagian dari Revitalisasi Bahasa Daerah' },
];

const FAQS = [
  {
    q: 'Apa itu Revitalisasi Bahasa Daerah?',
    a: 'Program nasional untuk menghidupkan kembali bahasa daerah — termasuk Bahasa Melayu Riau — yang penggunaannya makin jarang di kalangan anak muda, lewat pelatihan, materi ajar, dan kegiatan di sekolah agar bahasa dan budaya lokal tetap lestari.',
  },
  {
    q: 'Apa itu pengimbasan?',
    a: 'Model penyebaran ilmu berjenjang: guru yang sudah dilatih (Guru Utama) menularkan materi ke guru lain di sekolahnya (Guru Imbas), lalu bersama-sama mengajarkannya ke siswa (Siswa Imbas), sehingga jangkauan revitalisasi bahasa terus meluas.',
  },
  {
    q: 'Apakah data di peta ini real-time?',
    a: 'Ya. Begitu data guru atau siswa terimbas ditambahkan lewat Form, peta dan statistik diperbarui otomatis dari Supabase tanpa perlu memuat ulang halaman.',
  },
  {
    q: 'Bagaimana cara menambahkan data?',
    a: 'Buka halaman Form, pilih jenis data (Guru atau Siswa), lengkapi field yang diperlukan, lalu kirim. Data akan langsung terhitung ke wilayah yang sesuai.',
  },
];

// Section FAQ hanya dirender bila ada pertanyaan (dikondisikan).
const faqSection = FAQS.length
  ? `
      <section class="section-block reveal">
        <h2 class="section-title">Pertanyaan Umum</h2>
        <div class="faq-list">
          ${FAQS.map(
            (f) => `
            <div class="faq-item">
              <button type="button" class="faq-q" aria-expanded="false">
                <span>${f.q}</span>
                <span class="faq-icon" aria-hidden="true">+</span>
              </button>
              <div class="faq-a"><p>${f.a}</p></div>
            </div>
          `
          ).join('')}
        </div>
      </section>`
  : '';

export function renderHome(container) {
  container.innerHTML = `
    <div class="page-home">
      <section class="hero">
        <div class="hero-pattern" aria-hidden="true"></div>
        <div class="blob blob-1" aria-hidden="true"></div>
        <div class="blob blob-2" aria-hidden="true"></div>
        <div class="blob blob-3" aria-hidden="true"></div>
        <div class="hero-float hero-float-1" aria-hidden="true">${icon('building')}<b>12</b> Wilayah Terpetakan</div>
        <div class="hero-float hero-float-2" aria-hidden="true">${icon('zap')}<b>Live</b> dari Supabase</div>
        <div class="hero-inner">
          <span class="hero-badge"><span class="live-dot" aria-hidden="true"></span>${icon('book')}Revitalisasi Bahasa Daerah · Bahasa Melayu Riau</span>
          <h1>Peta Pengimbasan Guru &amp; Siswa Bahasa Melayu Riau</h1>
          <p class="hero-lead">
            Pantau sejauh mana pengimbasan Revitalisasi Bahasa Daerah
            menjangkau guru dan siswa di seluruh kabupaten/kota se-Riau
            dalam satu peta interaktif. Data selalu diperbarui otomatis
            dari Supabase.
          </p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="/peta" data-route="/peta">Buka Peta</a>
            <a class="btn btn-outline" href="/form" data-route="/form">Input Data</a>
          </div>
          <div class="hero-highlights">
            ${HIGHLIGHTS.map(
              (h) => `
              <span class="highlight">${icon(h.icon)}${h.text}</span>
            `
            ).join('')}
          </div>
          <div class="scroll-cue" aria-hidden="true">↓</div>
        </div>
      </section>

      <div class="motif-divider" aria-hidden="true"></div>

      <section class="stats reveal" aria-label="Ringkasan data">
        <div class="stat-card stat-card--orange">
          <span class="stat-icon" aria-hidden="true">${icon('building')}</span>
          <div class="stat-number" id="stat-kab">—</div>
          <div class="stat-label">Kabupaten/Kota</div>
        </div>
        <div class="stat-card stat-card--blue">
          <span class="stat-icon" aria-hidden="true">${icon('user')}</span>
          <div class="stat-number" id="stat-guru">—</div>
          <div class="stat-label">Guru Terimbas</div>
        </div>
        <div class="stat-card stat-card--green">
          <span class="stat-icon" aria-hidden="true">${icon('graduation')}</span>
          <div class="stat-number" id="stat-siswa">—</div>
          <div class="stat-label">Siswa Terimbas</div>
        </div>
        <p class="stats-live"><span class="live-dot" aria-hidden="true"></span>Progres pengimbasan, diperbarui otomatis dari Supabase</p>
      </section>

      <section class="section-block reveal" aria-label="Perbandingan guru dan siswa terimbas">
        <div class="section-head">
          <h2 class="section-title">Perbandingan Guru &amp; Siswa Terimbas</h2>
          <a class="section-link" href="/peta" data-route="/peta">Lihat di Peta →</a>
        </div>
        <p class="section-desc">
          Proporsi guru dan siswa yang telah menerima pengimbasan se-Provinsi
          Riau. Klik salah satu label untuk menyorotnya.
        </p>
        <div class="split-compare" id="split-compare">
          <div class="split-track" aria-hidden="true">
            <div class="split-fill split-fill--guru" id="split-guru"></div>
            <div class="split-fill split-fill--siswa" id="split-siswa"></div>
          </div>
          <div class="split-legend">
            <button type="button" class="split-item split-item--guru" data-target="guru">
              <span class="split-dot" aria-hidden="true"></span>
              <span class="split-item-label">${icon('user')} Guru</span>
              <b id="split-guru-pct">—</b>
            </button>
            <button type="button" class="split-item split-item--siswa" data-target="siswa">
              <span class="split-dot" aria-hidden="true"></span>
              <span class="split-item-label">${icon('graduation')} Siswa</span>
              <b id="split-siswa-pct">—</b>
            </button>
          </div>
        </div>
      </section>

      <section class="section-block reveal">
        <h2 class="section-title">Cara Menggunakan</h2>
        <div class="steps-grid">
          ${STEPS.map(
            (s, i) => `
            <article class="step-card">
              <span class="step-number" aria-hidden="true">${i + 1}</span>
              <span class="step-icon" aria-hidden="true">${icon(s.icon)}</span>
              <h3>${s.title}</h3>
              <p>${s.desc}</p>
            </article>
          `
          ).join('')}
        </div>
      </section>

      <section class="features reveal">
        <h2 class="section-title">Fitur Utama</h2>
        <p class="section-desc section-desc--center">Klik kartu untuk melihat detail masing-masing fitur.</p>
        <div class="feature-grid">
          ${FEATURES.map(
            (f) => `
            <article class="feature-card feature-card--${f.color}">
              <span class="feature-icon" aria-hidden="true">${icon(f.icon)}</span>
              <h3>${f.title}</h3>
              <p>${f.desc}</p>
              <button type="button" class="feature-toggle" aria-expanded="false">
                <span>Lihat detail</span>
                <span class="feature-toggle-icon" aria-hidden="true">⌄</span>
              </button>
              <div class="feature-detail">
                <ul>
                  ${f.detail.map((d) => `<li>${d}</li>`).join('')}
                </ul>
              </div>
            </article>
          `
          ).join('')}
        </div>
      </section>

      ${faqSection}

      <section class="cta-band reveal">
        <h2>Siap melihat datanya?</h2>
        <p>Buka peta untuk menjelajah, atau isi form untuk menambah data pengimbasan.</p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="/peta" data-route="/peta">Buka Peta</a>
          <a class="btn btn-light" href="/form" data-route="/form">Input Data</a>
        </div>
      </section>
    </div>
    ${footerHtml()}
  `;

  observeReveals(container);
  setupTilt(container);
  setupSplitCompare(container);
  setupFeatureAccordion(container);
  setupFaq(container);
  loadHomeData(container);
}

async function loadHomeData(root) {
  // Satu kali pengambilan data, dipakai bersama statistik & perbandingan
  // guru/siswa (menghindari request Supabase ganda di homepage).
  const [guruRes, siswaRes, kabRes] = await Promise.allSettled([
    fetchJumlahGuruPerKabupaten(),
    fetchJumlahSiswaPerKabupaten(),
    getKabupatenList(),
  ]);

  if (guruRes.status === 'rejected') console.warn('Gagal memuat statistik guru:', guruRes.reason);
  if (siswaRes.status === 'rejected') console.warn('Gagal memuat statistik siswa:', siswaRes.reason);

  const homeData = {
    guru: guruRes.status === 'fulfilled' ? guruRes.value : {},
    siswa: siswaRes.status === 'fulfilled' ? siswaRes.value : {},
    kab: kabRes.status === 'fulfilled' ? kabRes.value : KABUPATEN_LIST,
    guruOk: guruRes.status === 'fulfilled',
    siswaOk: siswaRes.status === 'fulfilled',
  };

  renderStats(root, homeData);
  renderSplitCompare(root, homeData);
}

function renderStats(root, data) {
  const statKab = root.querySelector('#stat-kab');
  const statGuru = root.querySelector('#stat-guru');
  const statSiswa = root.querySelector('#stat-siswa');
  if (!statKab || !statGuru || !statSiswa) return;

  const { guru, siswa, kab, guruOk, siswaOk } = data;
  const totalGuru = Object.values(guru).reduce((a, b) => a + b, 0);
  const totalSiswa = Object.values(siswa).reduce((a, b) => a + b, 0);

  animateNumber(statKab, kab.length);
  // "—" bila sumber gagal dimuat (membedakan dari 0 yang sebenarnya).
  if (guruOk) animateNumber(statGuru, totalGuru);
  else statGuru.textContent = '—';
  if (siswaOk) animateNumber(statSiswa, totalSiswa);
  else statSiswa.textContent = '—';
}

/** Isi & animasikan split bar perbandingan total guru vs total siswa terimbas. */
function renderSplitCompare(root, data) {
  const fillGuru = root.querySelector('#split-guru');
  const fillSiswa = root.querySelector('#split-siswa');
  const pctGuru = root.querySelector('#split-guru-pct');
  const pctSiswa = root.querySelector('#split-siswa-pct');
  if (!fillGuru || !fillSiswa || !pctGuru || !pctSiswa) return;

  const { guru, siswa, guruOk, siswaOk } = data;

  if (!guruOk || !siswaOk) {
    pctGuru.textContent = '—';
    pctSiswa.textContent = '—';
    fillGuru.style.width = '50%';
    fillSiswa.style.width = '50%';
    return;
  }

  const totalGuru = Object.values(guru).reduce((a, b) => a + b, 0);
  const totalSiswa = Object.values(siswa).reduce((a, b) => a + b, 0);
  const total = totalGuru + totalSiswa;

  if (total === 0) {
    pctGuru.textContent = '0%';
    pctSiswa.textContent = '0%';
    fillGuru.style.width = '50%';
    fillSiswa.style.width = '50%';
    return;
  }

  const guruPct = Math.round((totalGuru / total) * 100);
  const siswaPct = 100 - guruPct;

  pctGuru.textContent = `${guruPct}%`;
  pctSiswa.textContent = `${siswaPct}%`;
  // requestAnimationFrame agar transisi width CSS ikut ter-trigger dengan mulus.
  requestAnimationFrame(() => {
    fillGuru.style.width = `${guruPct}%`;
    fillSiswa.style.width = `${siswaPct}%`;
  });
}

/** Klik label Guru/Siswa untuk menyorot salah satu sisi split bar. */
function setupSplitCompare(root) {
  const wrap = root.querySelector('#split-compare');
  if (!wrap) return;

  wrap.querySelectorAll('.split-item').forEach((btn) => {
    btn.addEventListener('click', () => {
      const alreadyActive = btn.classList.contains('is-active');
      wrap.querySelectorAll('.split-item').forEach((b) => b.classList.remove('is-active'));
      wrap.classList.remove('has-active');
      delete wrap.dataset.active;

      if (!alreadyActive) {
        btn.classList.add('is-active');
        wrap.classList.add('has-active');
        wrap.dataset.active = btn.dataset.target;
      }
    });
  });
}

/** Klik kartu fitur untuk membuka/menutup detail (accordion, boleh lebih dari satu terbuka). */
function setupFeatureAccordion(root) {
  root.querySelectorAll('.feature-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.feature-card');
      if (!card) return;
      const isOpen = card.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', String(isOpen));
    });
  });
}

/** Accordion FAQ: satu pertanyaan terbuka dalam satu waktu. */
function setupFaq(root) {
  const items = root.querySelectorAll('.faq-item');
  items.forEach((item) => {
    const q = item.querySelector('.faq-q');
    if (!q) return;
    q.addEventListener('click', () => {
      const isOpen = item.classList.contains('is-open');
      items.forEach((other) => {
        other.classList.remove('is-open');
        const otherQ = other.querySelector('.faq-q');
        if (otherQ) otherQ.setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('is-open');
        q.setAttribute('aria-expanded', 'true');
      }
    });
  });
}

/** Efek tilt 3D halus pada kartu statistik saat mouse bergerak di atasnya. */
function setupTilt(root) {
  const cards = root.querySelectorAll('.stat-card');
  if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

  cards.forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const rotateX = ((y / rect.height) - 0.5) * -8;
      const rotateY = ((x / rect.width) - 0.5) * 8;
      card.style.transform = `perspective(700px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });
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
