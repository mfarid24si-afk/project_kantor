import './style.css';
import { initRouter } from './router.js';

/* ---------------- Navbar hamburger (FR-1.3) ---------------- */
const navbarToggle = document.getElementById('navbar-toggle');
const navbarNav = document.getElementById('navbar-nav');

navbarToggle.addEventListener('click', () => {
  const open = navbarNav.classList.toggle('open');
  navbarToggle.classList.toggle('open', open);
  navbarToggle.setAttribute('aria-expanded', String(open));
});

// Tutup menu saat sebuah tautan navigasi diklik (router menangani pindah halaman).
document.addEventListener('click', (e) => {
  if (e.target.closest('.nav-link')) {
    navbarNav.classList.remove('open');
    navbarToggle.classList.remove('open');
    navbarToggle.setAttribute('aria-expanded', 'false');
  }
});

/* ---------------- Router (/, /peta, /form) ---------------- */
initRouter();
