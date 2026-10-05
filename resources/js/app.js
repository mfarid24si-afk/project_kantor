import '../css/style.css';
import { initHome } from './pages/home.js';
import { initPeta } from './pages/peta.js';
import { initForm } from './pages/form.js';
import { initKabupaten } from './pages/kabupaten.js';
import { initTentang } from './pages/tentang.js';

/* ---------------- Navbar hamburger (FR-1.3) ---------------- */
function setupNavbar() {
  const navbarToggle = document.getElementById('navbar-toggle');
  const navbarNav = document.getElementById('navbar-nav');

  if (navbarToggle && navbarNav) {
    navbarToggle.addEventListener('click', () => {
      const open = navbarNav.classList.toggle('open');
      navbarToggle.classList.toggle('open', open);
      navbarToggle.setAttribute('aria-expanded', String(open));
    });

    document.addEventListener('click', (e) => {
      if (e.target.closest('.nav-link')) {
        navbarNav.classList.remove('open');
        navbarToggle.classList.remove('open');
        navbarToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
}

/* ---------------- Ripple effect pada link navbar ---------------- */
function setupRipple() {
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
      ripple.style.background = link.classList.contains('active')
        ? 'rgba(255, 255, 255, 0.35)'
        : 'rgba(28, 53, 89, 0.18)';
      link.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove());
    });
  }
}

/* ---------------- Halaman Aktif Dispatcher ---------------- */
function initCurrentPage() {
  setupNavbar();
  setupRipple();

  if (document.getElementById('page-home')) {
    initHome(document);
  } else if (document.getElementById('page-peta')) {
    initPeta(document);
  } else if (document.getElementById('page-form')) {
    initForm(document);
  } else if (document.getElementById('page-kabupaten')) {
    initKabupaten(document);
  } else if (document.getElementById('page-tentang')) {
    initTentang(document);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCurrentPage);
} else {
  initCurrentPage();
}
