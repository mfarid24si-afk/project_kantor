/* =====================================================================
 * ROUTER CLIENT-SIDE (history mode) — FR-1
 * Rute:  /  → Homepage |  /peta  → Peta |  /form  → Form
 * Setiap halaman mengembalikan fungsi cleanup yang dipanggil saat
 * berpindah halaman (mis. menghentikan interval polling peta).
 * ===================================================================== */

import { renderHome } from './pages/home.js';
import { renderPeta } from './pages/peta.js';
import { renderForm } from './pages/form.js';

const routes = {
  '/': renderHome,
  '/peta': renderPeta,
  '/form': renderForm,
};

const titles = {
  '/': 'Peta Guru & Siswa — Provinsi Riau',
  '/peta': 'Peta — Guru & Siswa Riau',
  '/form': 'Form Input — Guru & Siswa Riau',
};

let currentCleanup = null;

function normalizePath(path) {
  let p = path || '/';
  if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
  return p;
}

function updateActiveNav(path) {
  document.querySelectorAll('[data-route]').forEach((link) => {
    link.classList.toggle('active', normalizePath(link.getAttribute('href')) === path);
  });
}

function renderRoute() {
  if (typeof currentCleanup === 'function') {
    try {
      currentCleanup();
    } catch (err) {
      console.error('Gagal membersihkan halaman sebelumnya:', err);
    }
    currentCleanup = null;
  }

  const path = normalizePath(window.location.pathname);
  const render = routes[path] || routes['/'];

  const container = document.getElementById('app');
  container.innerHTML = '';
  const cleanup = render(container);
  if (typeof cleanup === 'function') currentCleanup = cleanup;

  document.title = titles[path] || titles['/'];
  updateActiveNav(path);
  window.scrollTo(0, 0);
}

/** Navigasi programatik tanpa reload. */
export function navigateTo(path) {
  if (normalizePath(window.location.pathname) !== normalizePath(path)) {
    history.pushState({}, '', path);
    renderRoute();
  }
}

/** Inisialisasi router (panggil sekali saat aplikasi dimuat). */
export function initRouter() {
  // Tombol back/forward browser.
  window.addEventListener('popstate', renderRoute);

  // Intercept klik pada tautan navbar agar memakai pushState.
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[data-route]');
    if (!link) return;
    e.preventDefault();
    navigateTo(link.getAttribute('href'));
  });

  // Normalisasi akses langsung ke index.html → "/".
  if (window.location.pathname.endsWith('index.html')) {
    history.replaceState({}, '', '/');
  }

  renderRoute();
}
