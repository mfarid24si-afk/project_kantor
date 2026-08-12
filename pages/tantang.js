import { icon } from "../icons.js";
import { footerHtml } from "../footer.js";

/*
 * Sesuaikan path gambar dengan folder asset project kamu.
 * Contoh:
 * /assets/images/pcr.jpg
 * /assets/images/bbpr.jpg
 */
const ABOUT_IMAGES = {
  pcr: "/assets/images/pcr.jpg",
  bbpr: "/assets/images/bbpr.jpeg",
  logoPcr: "/assets/images/logo-pcr.webp",
  logoBbpr: "/assets/images/logo-bbpr.png",
};

export function renderTantang(container) {
  container.innerHTML = `
    <div class="page-tentang">

      <!-- =========================
           HERO
      ========================== -->
      <section class="tentang-hero">

        <div class="hero-watermark" aria-hidden="true">
          Tentang
        </div>

        <div class="tentang-hero-content">

          <span class="tentang-eyebrow">
            Tentang Kami
          </span>

          <h1>
            Tentang Kami
          </h1>

          <p>
            <strong>SABARA</strong> merupakan hasil kolaborasi antara
            <strong>Balai Bahasa Provinsi Riau</strong> dan
            <strong>Politeknik Caltex Riau.</strong>
            Kami berkomitmen menghadirkan platform untuk mendukung
            <strong>Revitalisasi Bahasa Melayu Riau</strong>
            melalui teknologi digital yang modern dan mudah diakses.
          </p>

        </div>

      </section>


      <!-- =========================
           KOLABORASI
      ========================== -->
      <section class="kolaborasi-section reveal">

        <div class="kolaborasi-card">

          <!-- IMAGE -->
          <div class="kolaborasi-image">

            <div class="image-collage">

              <div class="collage-item">
                <img
                  src="${ABOUT_IMAGES.pcr}"
                  alt="Politeknik Caltex Riau"
                />

                <span class="image-label">
                  KAMPUS PCR
                </span>
              </div>

              <div class="collage-item">
                <img
                  src="${ABOUT_IMAGES.bbpr}"
                  alt="Balai Bahasa Provinsi Riau"
                />

                <span class="image-label">
                  KANTOR BBPR
                </span>
              </div>

            </div>

          </div>


          <!-- CONTENT -->
          <div class="kolaborasi-content">

            <span class="section-badge">
              Kolaborasi
            </span>

            <h2>
              Kolaborasi Akademik
              <br />
              &amp;
              <span>Pelestarian Budaya</span>
            </h2>

            <p>
              Pengembangan <strong>KEMALA</strong> merupakan platform memetakan sejauh mana 
              revitalisasi bahasa melayu menjangkau seluruh kabupaten/kota se-Provinsi Riau untuk
              menampilkan pemetaan dan data revitalisasi bahasa melayu yang modern.
            </p>

            <p>
              Proyek ini merupakan wujud nyata integrasi antara
              <strong>Politeknik Caltex Riau</strong> dan
              <strong>Balai Bahasa Provinsi Riau</strong> dalam
              mendigitalisasi kekayaan bahasa daerah.
            </p>


            <!-- DAFTAR DEVELOPER -->
<div class="developers-grid">

  <!-- DEVELOPER 1 -->
  <div class="developer-info">
    <div class="developer-icon">
      ${icon("user")}
    </div>

    <div class="developer-text">
      <span>DEVELOPER</span>
      <strong>Noval Nugraha</strong>
    </div>
  </div>

  <!-- DEVELOPER 2 -->
  <div class="developer-info">
    <div class="developer-icon">
      ${icon("user")}
    </div>

    <div class="developer-text">
      <span>DEVELOPER</span>
      <strong>Muhammad Majid Avindra</strong>
    </div>
  </div>

  <!-- DEVELOPER 3 -->
  <div class="developer-info">
    <div class="developer-icon">
      ${icon("user")}
    </div>

    <div class="developer-text">
      <span>DEVELOPER</span>
      <strong>Rifky Faerana Alfarizi</strong>
    </div>
  </div>

  <!-- DEVELOPER 4 -->
  <div class="developer-info">
    <div class="developer-icon">
      ${icon("user")}
    </div>

    <div class="developer-text">
      <span>DEVELOPER</span>
      <strong>M.Farid Fadillah</strong>
    </div>
  </div>

</div>


            <!-- LOGOS -->
            <div class="institution-logos">

              <div class="logo-wrapper">
                <img
                  src="${ABOUT_IMAGES.logoPcr}"
                  alt="Logo Politeknik Caltex Riau"
                />
              </div>

              <div class="logo-divider"></div>

              <div class="logo-wrapper logo-bbpr">
                <img
                  src="${ABOUT_IMAGES.logoBbpr}"
                  alt="Logo Balai Bahasa Provinsi Riau"
                />
              </div>

            </div>

          </div>

        </div>

      </section>


    </div>

    ${footerHtml()}
  `;

  observeReveals(container);
}

/* ==========================================
   SCROLL REVEAL
========================================== */

function observeReveals(root) {
  const els = root.querySelectorAll(".reveal");

  if (!("IntersectionObserver" in window)) {
    els.forEach((el) => {
      el.classList.add("is-visible");
    });

    return;
  }

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");

          io.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.12,
    },
  );

  els.forEach((el) => {
    io.observe(el);
  });
}
