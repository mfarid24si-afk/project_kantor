
import { KABUPATEN_LIST } from '../config.js';
import { fetchJumlahGuruPerKabupaten, fetchJumlahSiswaPerKabupaten } from '../api.js';
import { getKabupatenList } from '../ui.js';
 
const FEATURES = [
  {
    icon: '🗺️',
    title: 'Peta Interaktif',
    desc: '12 poligon kabupaten/kota dengan batas administratif akurat. Klik wilayah mana pun untuk melihat rincian guru dan siswa yang telah terimbas.',
    color: 'blue',
    detail: [
      'Batas wilayah mengikuti data administratif resmi',
      'Klik poligon untuk melihat rincian guru & siswa terimbas secara instan',
    ],
  },
  {
    icon: '🔄',
    title: 'Data Realtime',
    desc: 'Data yang diinput lewat Form langsung tampil di peta secara otomatis, tanpa perlu reload manual oleh pengguna lain.',
    color: 'orange',
    detail: [
      'Sinkron otomatis lewat Supabase, tanpa refresh manual',
      'Perubahan langsung terlihat oleh semua pengguna lain',
    ],
  },
  {
    icon: '📝',
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
    icon: '🗺️',
    title: 'Buka Peta',
    desc: 'Telusuri 12 kabupaten/kota Riau pada peta interaktif dengan batas wilayah administratif.',
  },
  {
    icon: '👆',
    title: 'Klik Wilayah',
    desc: 'Klik poligon mana pun untuk melihat jumlah guru & siswa terimbas di wilayah tersebut.',
  },
  {
    icon: '📝',
    title: 'Input Data',
    desc: 'Tambah data guru atau siswa yang telah terimbas lewat Form — dalam beberapa detik langsung tampil di peta.',
  },
];
 
const HIGHLIGHTS = [
  { icon: '⚡', text: 'Data realtime dari Supabase' },
  { icon: '🔄', text: 'Update otomatis tiap 15 detik' },
  { icon: '📖', text: 'Bagian dari Revitalisasi Bahasa Daerah' },
];
 
const CASCADE = [
  {
    icon: '🎓',
    title: 'Guru Utama',
    desc: 'Mengikuti pelatihan langsung dalam program Revitalisasi Bahasa Daerah.',
  },
  {
    icon: '👩‍🏫',
    title: 'Guru Imbas',
    desc: 'Menerima pengimbasan materi Bahasa Melayu Riau dari Guru Utama di sekolahnya.',
  },
  {
    icon: '🧑‍🎓',
    title: 'Siswa Imbas',
    desc: 'Belajar Bahasa Melayu Riau di kelas bersama guru yang telah terimbas.',
  },
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
 
export function renderHome(container) {
  container.innerHTML = `
    <div class="page-home">
      <section class="hero">
        <div class="hero-pattern" aria-hidden="true"></div>
        <div class="blob blob-1" aria-hidden="true"></div>
        <div class="blob blob-2" aria-hidden="true"></div>
        <div class="blob blob-3" aria-hidden="true"></div>
        <div class="hero-float hero-float-1" aria-hidden="true">🏙️ <b>12</b> Wilayah Terpetakan</div>
        <div class="hero-float hero-float-2" aria-hidden="true">⚡ <b>Live</b> dari Supabase</div>
        <div class="hero-inner">
          <span class="hero-badge"><span class="live-dot" aria-hidden="true"></span>📖 Revitalisasi Bahasa Daerah · Bahasa Melayu Riau</span>
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
              <span class="highlight"><span aria-hidden="true">${h.icon}</span>${h.text}</span>
            `
            ).join('')}
          </div>
          <div class="scroll-cue" aria-hidden="true">↓</div>
        </div>
      </section>
 
      <div class="motif-divider" aria-hidden="true"></div>
 
      <section class="about-program reveal" aria-label="Tentang program">
        <div class="about-pattern" aria-hidden="true"></div>
        <div class="about-inner">
          <span class="about-eyebrow">Tentang Program</span>
          <h2 class="section-title">Revitalisasi Bahasa Daerah &amp; Pengimbasan</h2>
          <p class="about-lead">
            Platform ini dibangun untuk mendukung <b>Revitalisasi Bahasa
            Daerah</b> — program nasional untuk menjaga bahasa daerah,
            termasuk <b>Bahasa Melayu Riau</b>, agar tetap hidup dan
            diwariskan ke generasi berikutnya lewat jalur pendidikan.
          </p>
 
          <div class="about-grid">
            <article class="about-card">
              <span class="about-icon" aria-hidden="true">🌾</span>
              <h3>Revitalisasi Bahasa Daerah</h3>
              <p>Upaya bersama menghidupkan kembali bahasa daerah yang makin jarang dipakai anak muda, lewat pelatihan, materi ajar, dan kegiatan di sekolah — supaya kekayaan bahasa dan budaya lokal tidak punah.</p>
            </article>
            <article class="about-card">
              <span class="about-icon" aria-hidden="true">🤝</span>
              <h3>Apa Itu Pengimbasan?</h3>
              <p>Model penyebaran ilmu berjenjang: guru yang sudah dilatih menularkan materi ke guru lain di sekolahnya, lalu bersama-sama mengajarkannya ke siswa — sehingga jangkauan revitalisasi bahasa terus meluas.</p>
            </article>
          </div>
 
          <div class="cascade-flow">
            ${CASCADE.map(
              (c, i) => `
              ${i > 0 ? '<span class="cascade-arrow" aria-hidden="true">→</span>' : ''}
              <div class="cascade-step">
                <span class="cascade-icon" aria-hidden="true">${c.icon}</span>
                <b>${c.title}</b>
                <span>${c.desc}</span>
              </div>
            `
            ).join('')}
          </div>
 
          <p class="about-note">Peta &amp; statistik di halaman ini menunjukkan sejauh mana pengimbasan tersebut telah menjangkau guru dan siswa di 12 kabupaten/kota se-Riau.</p>
        </div>
      </section>
 
      <section class="stats reveal" aria-label="Ringkasan data">
        <div class="stat-card stat-card--orange">
          <span class="stat-icon" aria-hidden="true">🏙️</span>
          <div class="stat-number" id="stat-kab">—</div>
          <div class="stat-label">Kabupaten/Kota</div>
        </div>
        <div class="stat-card stat-card--blue">
          <span class="stat-icon" aria-hidden="true">👩‍🏫</span>
          <div class="stat-number" id="stat-guru">—</div>
          <div class="stat-label">Guru Terimbas</div>
        </div>
        <div class="stat-card stat-card--green">
          <span class="stat-icon" aria-hidden="true">🎓</span>
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
              <span class="split-item-label">👩‍🏫 Guru</span>
              <b id="split-guru-pct">—</b>
            </button>
            <button type="button" class="split-item split-item--siswa" data-target="siswa">
              <span class="split-dot" aria-hidden="true"></span>
              <span class="split-item-label">🎓 Siswa</span>
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
        <p class="section-desc section-desc--center">Klik kartu untuk melihat detail masing-masing fitur.</p>
        <div class="feature-grid">
          ${FEATURES.map(
            (f) => `
            <article class="feature-card feature-card--${f.color}">
              <span class="feature-icon" aria-hidden="true">${f.icon}</span>
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
      </section>
 
      <section class="cta-band reveal">
        <h2>Siap melihat datanya?</h2>
        <p>Buka peta untuk menjelajah, atau isi form untuk menambah data pengimbasan.</p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="/peta" data-route="/peta">Buka Peta</a>
          <a class="btn btn-light" href="/form" data-route="/form">Input Data</a>
        </div>
      </section>
    </div>
    <footer class="footer footer--dark">
      
 
      <div class="footer-inner">
        <div class="footer-col footer-col--brand">
          <div class="footer-brand">
            <span class="footer-brand-icon" aria-hidden="true">🗺️</span>
            <div class="footer-brand-text">
              <b>Peta Guru &amp; Siswa</b>
              <span>PROVINSI RIAU</span>
            </div>
          </div>
          <p class="footer-desc">
            Bagian dari program Revitalisasi Bahasa Daerah — memetakan
            sejauh mana pengimbasan Bahasa Melayu Riau telah menjangkau
            guru dan siswa di seluruh Provinsi Riau.
          </p>
          <div class="footer-social">
            <a class="footer-social-btn" href="#" aria-label="Facebook" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.6h2.6l.4-3h-3v-1.9c0-.87.25-1.46 1.5-1.46h1.6V4.35C15.9 4.24 15 4.16 13.98 4.16c-2.34 0-3.94 1.4-3.94 3.98v2.26H7.5v3h2.54V21h3.46z"/></svg>
            </a>
            <a class="footer-social-btn" href="#" aria-label="Instagram" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8.6a3.4 3.4 0 1 0 0 6.8 3.4 3.4 0 0 0 0-6.8zm0 5.6a2.2 2.2 0 1 1 0-4.4 2.2 2.2 0 0 1 0 4.4zM16.9 4H7.1A3.1 3.1 0 0 0 4 7.1v9.8A3.1 3.1 0 0 0 7.1 20h9.8a3.1 3.1 0 0 0 3.1-3.1V7.1A3.1 3.1 0 0 0 16.9 4zm1.9 12.9a1.9 1.9 0 0 1-1.9 1.9H7.1a1.9 1.9 0 0 1-1.9-1.9V7.1a1.9 1.9 0 0 1 1.9-1.9h9.8a1.9 1.9 0 0 1 1.9 1.9v9.8zM17.1 6.4a.9.9 0 1 0 0 1.8.9.9 0 0 0 0-1.8z"/></svg>
            </a>
            <a class="footer-social-btn" href="#" aria-label="YouTube" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.6 8.2a2.8 2.8 0 0 0-2-2C17.9 5.7 12 5.7 12 5.7s-5.9 0-7.6.5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2 12a29 29 0 0 0 .4 3.8 2.8 2.8 0 0 0 2 2c1.7.5 7.6.5 7.6.5s5.9 0 7.6-.5a2.8 2.8 0 0 0 2-2A29 29 0 0 0 22 12a29 29 0 0 0-.4-3.8zM10 15V9l5.2 3-5.2 3z"/></svg>
            </a>
          </div>
        </div>
 
        <div class="footer-col">
          <h3 class="footer-col-title">Navigasi</h3>
          <nav class="footer-links">
            <a href="/" data-route="/">Beranda</a>
            <a href="/peta" data-route="/peta">Peta</a>
            <a href="/form" data-route="/form">Input Data</a>
          </nav>
        </div>
 
        <div class="footer-col">
          <h3 class="footer-col-title">Alamat &amp; Lokasi</h3>
          <div class="footer-address">
            <span class="footer-address-icon" aria-hidden="true">📍</span>
            <p>Jalan Binawidya, Kompleks Universitas Riau, Panam, Pekanbaru, Riau 28292</p>
          </div>
          <div class="footer-map">
            <iframe
              title="Lokasi Balai Bahasa Riau "
              src="https://www.google.com/maps?q=Balai+Bahasa+Riau&output=embed"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        </div>
      </div>
 
      <div class="footer-bottom">
        <span>© 2026 Peta Pengimbasan Guru &amp; Siswa, Politeknik Caltex Riau.</span>
        <span class="footer-badge"><span class="live-dot" aria-hidden="true"></span>Revitalisasi Bahasa Daerah</span>
      </div>
    </footer>
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