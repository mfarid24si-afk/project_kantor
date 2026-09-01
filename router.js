/* =====================================================================
 * ROUTER CLIENT-SIDE (history mode) — FR-1
 * Rute:  /  → Homepage |  /peta  → Peta |  /form  → Form |  /tentang → Tentang
 *
 * Loading flow:
 *   1. showLoader() → browser paints loader (solid, opaque)
 *   2. requestAnimationFrame → browser sudah cat loader
 *   3. render page → konten baru muncul di BALIK loader (tertutup)
 *   4. pageReady() → halaman selesai load data
 *   5. hideLoader() → loader hilang, konten sudah siap
 *
 * Dengan cara ini, user tidak akan melihat flash konten mentah.
 * ===================================================================== */

import { renderHome } from './pages/home.js';
import { renderPeta } from './pages/peta.js';
import { renderForm } from './pages/form.js';
import { renderTantang } from './pages/tantang.js';
import { renderKabupaten } from './pages/kabupaten.js';
import { resetPageReady, waitForPageReady } from './pageReadyManager.js';

const routes = [
  { path: '/', render: renderHome, title: 'Peta Guru & Siswa — Provinsi Riau' },
  { path: '/peta', render: renderPeta, title: 'Peta — Guru & Siswa Riau' },
  { path: '/form', render: renderForm, title: 'Form Input — Guru & Siswa Riau' },
  { path: '/tentang', render: renderTantang, title: 'Tentang — Guru & Siswa Riau' },
  { path: '/kabupaten/:name', render: renderKabupaten, title: (params) => `Detail ${params.name} — Guru & Siswa Riau` },
];

let currentCleanup = null;

/* ------------------------------------------------------------------
   PAGE LOADER
   ------------------------------------------------------------------ */
function getLoader() {
  return document.getElementById('page-loader');
}

function showLoader() {
  const el = getLoader();
  if (el) el.style.display = 'flex';
}

function hideLoader() {
  const el = getLoader();
  if (el) el.style.display = 'none';
}

function normalizePath(path) {
  let p = path || '/';
  if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
  return p;
}

function escapeRegex(value) {
  return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function matchRoute(path) {
  for (const route of routes) {
    if (route.path === path) {
      return { route, params: {} };
    }

    if (route.path.includes('/:')) {
      const parts = route.path.split('/');
      const pattern = parts
        .map((segment) => (segment.startsWith(':') ? '([^/]+)' : escapeRegex(segment)))
        .join('/');
      const regex = new RegExp(`^${pattern}$`);
      const match = path.match(regex);
      if (match) {
        const params = {};
        const keys = parts.filter((segment) => segment.startsWith(':')).map((segment) => segment.slice(1));
        keys.forEach((key, index) => {
          params[key] = decodeURIComponent(match[index + 1]);
        });
        return { route, params };
      }
    }
  }
  return { route: routes[0], params: {} };
}

function updateActiveNav(path) {
  document.querySelectorAll('[data-route]').forEach((link) => {
    link.classList.toggle('active', normalizePath(link.getAttribute('href')) === path);
  });
}

async function renderRoute() {
  // Bersihkan navigasi sebelumnya
  if (typeof currentCleanup === 'function') {
    try { currentCleanup(); } catch (err) {
      console.error('Gagal membersihkan halaman sebelumnya:', err);
    }
    currentCleanup = null;
  }

  const path = normalizePath(window.location.pathname);
  const { route, params } = matchRoute(path);

  // Step 1: Tampilkan loader (opaque, menutupi semua konten)
  resetPageReady();
  showLoader();

  // Step 2: Tunggu browser CAT loader dulu (1 frame)
  await new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));

  // Step 3: Render konten baru — konten muncul di BALIK loader (tertutup)
  const container = document.getElementById('app');
  container.innerHTML = '';
  const cleanup = route.render(container, params);
  if (typeof cleanup === 'function') currentCleanup = cleanup;

  document.title = typeof route.title === 'function' ? route.title(params) : route.title;
  updateActiveNav(path);
  window.scrollTo(0, 0);

  // Step 4: Tunggu halaman selesai load data
  await Promise.race([
    waitForPageReady(),
    new Promise((resolve) => setTimeout(resolve, 4000)),
  ]);

  // Step 5: Sembunyikan loader — konten sudah siap, tidak ada flash
  hideLoader();
}

/** Navigasi programatik tanpa reload. */
export function navigateTo(path) {
  if (normalizePath(window.location.pathname) !== normalizePath(path)) {
    history.pushState({}, '', path);
    renderRoute();
  } else {
    hideLoader();
  }
}

/** Inisialisasi router (panggil sekali saat aplikasi dimuat). */
export function initRouter() {
  hideLoader();

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
