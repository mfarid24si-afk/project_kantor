/* =====================================================================
 * HALAMAN TENTANG — /tentang
 * Konten program dipindahkan dari homepage agar homepage lebih
 * data-first: hero → statistik → perbandingan → fitur → FAQ.
 * Semua gaya memakai kelas yang sudah ada di style.css (about-*, cascade-*).
 * ===================================================================== */

import { icon } from '../icons.js';
import { footerHtml } from '../footer.js';

const ABOUT_CARDS = [
  {
    icon: 'sprout',
    title: 'Revitalisasi Bahasa Daerah',
    desc: 'Upaya bersama menghidupkan kembali bahasa daerah yang makin jarang dipakai anak muda, lewat pelatihan, materi ajar, dan kegiatan di sekolah — supaya kekayaan bahasa dan budaya lokal tidak punah.',
  },
  {
    icon: 'handshake',
    title: 'Apa Itu Pengimbasan?',
    desc: 'Model penyebaran ilmu berjenjang: guru yang sudah dilatih menularkan materi ke guru lain di sekolahnya, lalu bersama-sama mengajarkannya ke siswa — sehingga jangkauan revitalisasi bahasa terus meluas.',
  },
];

const CASCADE = [
  {
    icon: 'award',
    title: 'Guru Utama',
    desc: 'Mengikuti pelatihan langsung dalam program Revitalisasi Bahasa Daerah.',
  },
  {
    icon: 'user',
    title: 'Guru Imbas',
    desc: 'Menerima pengimbasan materi Bahasa Melayu Riau dari Guru Utama di sekolahnya.',
  },
  {
    icon: 'graduation',
    title: 'Siswa Imbas',
    desc: 'Belajar Bahasa Melayu Riau di kelas bersama guru yang telah terimbas.',
  },
];

// FAQ di halaman Tentang dibuat BEDA dari FAQ beranda (tidak ada pertanyaan
// kembar): beranda fokus ke perkenalan program + cara pakai platform,
// sedangkan halaman ini fokus ke pendalaman program & peran platform.
const FAQS = [
  {
    q: 'Mengapa bahasa daerah perlu direvitalisasi?',
    a: 'Karena penggunaan bahasa daerah — termasuk Bahasa Melayu Riau — terus menurun di kalangan generasi muda. Revitalisasi menjaga agar bahasa dan budaya lokal tetap hidup dan diwariskan lewat jalur pendidikan.',
  },
  {
    q: 'Bagaimana platform ini membantu program?',
    a: 'Platform ini memetakan sejauh mana pengimbasan telah menjangkau guru dan siswa di 12 kabupaten/kota se-Provinsi Riau, sehingga perkembangan revitalisasi bahasa bisa dipantau bersama oleh pendidik dan pemangku kepentingan.',
  },
  {
    q: 'Di mana saja pengimbasan sudah berjalan?',
    a: 'Pengimbasan telah berjalan di 12 kabupaten/kota se-Provinsi Riau. Buka halaman Peta untuk melihat sebaran serta jumlah guru dan siswa terimbas di setiap wilayah.',
  },
];

// Section FAQ hanya dirender bila ada pertanyaan (dikondisikan).
const faqSection = FAQS.length
  ? `
      <section class="section-block reveal" aria-label="Pertanyaan umum">
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

export function renderTantang(container) {
  container.innerHTML = `
    <div class="page-tantang">
      <section class="tantang-hero">
        <span class="hero-badge"><span class="live-dot" aria-hidden="true"></span>${icon('book')}Revitalisasi Bahasa Daerah · Bahasa Melayu Riau</span>
        <h1>Tentang Program &amp; Pengimbasan</h1>
        <p class="tantang-lead">
          Platform ini dibangun untuk mendukung <b>Revitalisasi Bahasa Daerah</b> —
          program nasional menjaga bahasa daerah, termasuk <b>Bahasa Melayu Riau</b>,
          agar tetap hidup dan diwariskan ke generasi berikutnya lewat jalur pendidikan.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="/peta" data-route="/peta">Buka Peta</a>
          <a class="btn btn-outline" href="/form" data-route="/form">Input Data</a>
        </div>
      </section>

      <div class="motif-divider" aria-hidden="true"></div>

      <section class="about-program reveal" aria-label="Konsep dan alur pengimbasan">
        <div class="about-pattern" aria-hidden="true"></div>
        <div class="about-inner">
          <span class="about-eyebrow">Kenali Lebih Jauh</span>
          <h2 class="section-title">Konsep &amp; Alur Pengimbasan</h2>
          <p class="about-lead">
            Platform ini memetakan sejauh mana pengimbasan telah menjangkau
            <b>guru dan siswa</b> di <b>12 kabupaten/kota</b> se-Provinsi Riau —
            dari pelatihan Guru Utama hingga pembelajaran di kelas.
          </p>

          <div class="about-grid">
            ${ABOUT_CARDS.map(
              (c) => `
              <article class="about-card">
                <span class="about-icon" aria-hidden="true">${icon(c.icon)}</span>
                <h3>${c.title}</h3>
                <p>${c.desc}</p>
              </article>
            `
            ).join('')}
          </div>

          <div class="cascade-flow">
            ${CASCADE.map(
              (c, i) => `
              ${i > 0 ? '<span class="cascade-arrow" aria-hidden="true">→</span>' : ''}
              <div class="cascade-step">
                <span class="cascade-icon" aria-hidden="true">${icon(c.icon)}</span>
                <b>${c.title}</b>
                <span>${c.desc}</span>
              </div>
            `
            ).join('')}
          </div>

          <p class="about-note">Peta &amp; statistik di platform ini menunjukkan sejauh mana pengimbasan tersebut telah menjangkau guru dan siswa di 12 kabupaten/kota se-Riau.</p>
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
  setupFaq(container);
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
