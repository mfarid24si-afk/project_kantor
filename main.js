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

/* ---------------- Ripple effect pada link navbar ---------------- */
// Lingkaran tinta muncul dari titik klik (Material-style), dihormati
// prefers-reduced-motion.
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  document.addEventListener('click', (e) => {
    const link = e.target.closest('.nav-link');
    if (!link) return;

    const rect = link.getBoundingClientRect();
    const diameter = Math.max(rect.width, rect.height);
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    ripple.style.width = ripple.style.height = `${diameter}px`;
    ripple.style.left = `${e.clientX - rect.left - diameter / 2}px`;
    ripple.style.top = `${e.clientY - rect.top - diameter / 2}px`;
    // Warna tinta mengikuti keadaan tautan (aktif = putih di atas pil biru).
    ripple.style.background = link.classList.contains('active')
      ? 'rgba(255, 255, 255, 0.35)'
      : 'rgba(28, 53, 89, 0.18)';
    link.appendChild(ripple);
    ripple.addEventListener('animationend', () => ripple.remove());
  });
}

/* ---------------- Router (/, /peta, /form, /tentang) ---------------- */
initRouter();
