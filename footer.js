/* =====================================================================
 * FOOTER BERSAMA — dirender di semua halaman (Beranda, Tentang, Form, Peta)
 * agar footer selalu tampil dan konsisten di setiap rute.
 * ===================================================================== */

import { icon } from './icons.js';

export function footerHtml() {
  return `
    <footer class="footer footer--dark">
      <div class="footer-inner">
        <div class="footer-col footer-col--brand">
          <div class="footer-brand">
            <span class="footer-brand-icon" aria-hidden="true">${icon('map')}</span>
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
        </div>

        <div class="footer-col">
          <h3 class="footer-col-title">Navigasi</h3>
          <nav class="footer-links" aria-label="Navigasi footer">
            <a href="/" data-route="/">Beranda</a>
            <a href="/peta" data-route="/peta">Peta</a>
            <a href="/form" data-route="/form">Input Data</a>
            <a href="/tentang" data-route="/tentang">Tentang</a>
          </nav>
        </div>

        <div class="footer-col">
          <h3 class="footer-col-title">Alamat &amp; Lokasi</h3>
          <div class="footer-address">
            <span class="footer-address-icon" aria-hidden="true">${icon('pin')}</span>
            <p>Jalan Binawidya, Kompleks Universitas Riau, Panam, Pekanbaru, Riau 28292</p>
          </div>
          <div class="footer-map">
            <iframe
              title="Lokasi Balai Bahasa Riau"
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
}
