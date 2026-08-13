import { KABUPATEN_LIST } from "../config.js";
import {
  fetchJumlahGuruPerKabupaten,
  fetchJumlahSiswaPerKabupaten,
} from "../api.js";
import { getKabupatenList } from "../ui.js";
import { icon } from "../icons.js";
import { footerHtml } from "../footer.js";

/* ==========================================
   DATA
========================================== */

const FEATURES = [
  {
    icon: "map",
    title: "Peta Interaktif",
    desc: "12 poligon kabupaten/kota dengan batas administratif akurat. Klik wilayah untuk melihat rincian guru dan siswa yang telah terimbas.",
    color: "blue",
    detail: [
      "Batas wilayah mengikuti data administratif resmi",
      "Klik wilayah untuk melihat rincian guru & siswa",
    ],
  },
  {
    icon: "refresh",
    title: "Data Realtime",
    desc: "Data yang dimasukkan melalui form langsung diperbarui pada sistem sehingga informasi pengimbasan selalu terkini.",
    color: "orange",
    detail: [
      "Sinkron otomatis melalui Supabase",
      "Perubahan dapat dilihat pengguna tanpa reload manual",
    ],
  },
  {
    icon: "pen",
    title: "Form Input",
    desc: "Masukkan data guru dan siswa yang telah menerima pengimbasan melalui form yang sederhana dan mudah digunakan.",
    color: "green",
    detail: [
      "Validasi otomatis sebelum data disimpan",
      "Notifikasi setelah data berhasil dikirim",
    ],
  },
];

const STEPS = [
  {
    icon: "map",
    title: "Buka Peta",
    desc: "Telusuri kabupaten/kota di Provinsi Riau melalui peta interaktif.",
  },
  {
    icon: "click",
    title: "Pilih Wilayah",
    desc: "Klik wilayah untuk melihat jumlah guru dan siswa yang telah terimbas.",
  },
  {
    icon: "pen",
    title: "Input Data",
    desc: "Tambahkan data guru atau siswa melalui halaman form pengimbasan.",
  },
];

const HIGHLIGHTS = [
  {
    icon: "zap",
    text: "Data realtime dari Supabase",
  },
  {
    icon: "refresh",
    text: "Update otomatis",
  },
  {
    icon: "book",
    text: "Revitalisasi Bahasa Melayu Riau",
  },
];

const FAQS = [
  {
    q: "Apa itu Revitalisasi Bahasa Daerah?",
    a: "Program untuk menghidupkan kembali bahasa daerah yang penggunaannya semakin berkurang melalui pelatihan, materi ajar, dan kegiatan pembelajaran.",
  },
  {
    q: "Apa itu pengimbasan?",
    a: "Pengimbasan merupakan proses penyebaran pengetahuan secara berjenjang dari guru yang telah mendapatkan pelatihan kepada guru dan siswa lainnya.",
  },
  {
    q: "Apakah data di peta diperbarui secara realtime?",
    a: "Ya. Data guru dan siswa yang ditambahkan melalui form akan diperbarui pada sistem secara otomatis melalui Supabase.",
  },
  {
    q: "Bagaimana cara menambahkan data?",
    a: "Buka halaman Form, pilih jenis data Guru atau Siswa, lengkapi data yang diperlukan, kemudian kirim data tersebut.",
  },
];


/* ==========================================
   FAQ SECTION
========================================== */

const faqSection = FAQS.length
  ? `
      <section class="home-content-section reveal">

        <div class="section-heading">

          <span class="section-badge">
            Informasi
          </span>

          <h2 class="section-title">
            Pertanyaan Umum
          </h2>

          <p class="section-desc">
            Beberapa pertanyaan yang sering ditanyakan mengenai
            sistem pengimbasan Bahasa Melayu Riau.
          </p>

        </div>

        <div class="faq-list">

          ${FAQS.map(
            (f) => `
              <div class="faq-item">

                <button
                  type="button"
                  class="faq-q"
                  aria-expanded="false"
                >

                  <span>${f.q}</span>

                  <span
                    class="faq-icon"
                    aria-hidden="true"
                  >
                    +
                  </span>

                </button>

                <div class="faq-a">
                  <p>${f.a}</p>
                </div>

              </div>
            `,
          ).join("")}

        </div>

      </section>
    `
  : "";


/* ==========================================
   RENDER HOME
========================================== */

export function renderHome(container) {

  container.innerHTML = `

    <div class="page-tentang page-home">


      <!-- ==================================
           HERO
      =================================== -->

      <section class="tentang-hero home-hero">

        <div
          class="hero-watermark"
          aria-hidden="true"
        >
          SABARA
        </div>


        <div class="tentang-hero-content home-hero-content">

          <span class="tentang-eyebrow">

            <span class="live-dot" aria-hidden="true"></span>

            ${icon("book")}

            Revitalisasi Bahasa Daerah

          </span>


          <h1>
            Peta Pengimbasan
            <br />
            <span>Bahasa Melayu Riau</span>
          </h1>


          <p>

            Pantau sejauh mana pengimbasan Revitalisasi Bahasa Daerah
            menjangkau guru dan siswa di seluruh kabupaten/kota
            Provinsi Riau melalui satu platform digital yang
            informatif dan mudah diakses.

          </p>


          <!-- ACTION -->

          <div class="hero-actions">

            <a
              class="btn btn-primary"
              href="/peta"
              data-route="/peta"
            >
              Buka Peta
            </a>

          </div>


          

      </section>
<!-- FLOATING LOGO KABUPATEN / KOTA RIAU -->
<div class="floating-logos" aria-hidden="true">

  <!-- KIRI -->
  <div class="floating-logo fl-1">
    <img src="/assets/images/logo-bengkalis.png" alt="">
  </div>

  <div class="floating-logo fl-2">
    <img src="/assets/images/logo-indragiri-hilir.png" alt="">
  </div>

  <div class="floating-logo fl-3">
    <img src="/assets/images/logo-indragiri-hulu.png" alt="">
  </div>

  <div class="floating-logo fl-4">
    <img src="/assets/images/logo-kampar.png" alt="">
  </div>

  <div class="floating-logo fl-5">
    <img src="/assets/images/logo-meranti.png" alt="">
  </div>

  <div class="floating-logo fl-6">
    <img src="/assets/images/logo-kuansing.png" alt="">
  </div>


  <!-- KANAN -->
  <div class="floating-logo fl-7">
    <img src="/assets/images/logo-pelalawan.png" alt="">
  </div>

  <div class="floating-logo fl-8">
    <img src="/assets/images/logo-rohil.png" alt="">
  </div>

  <div class="floating-logo fl-9">
    <img src="/assets/images/logo-rohul.png" alt="">
  </div>

  <div class="floating-logo fl-10">
    <img src="/assets/images/logo-siak.png" alt="">
  </div>

  <div class="floating-logo fl-11">
    <img src="/assets/images/logo-pekanbaru.png" alt="">
  </div>

  <div class="floating-logo fl-12">
    <img src="/assets/images/logo-dumai.png" alt="">
  </div>

</div>
      <!-- ==================================
           STATISTIK
      =================================== -->

      <section
        class="home-content-section stats-section reveal"
        aria-label="Ringkasan data"
      >

        <div class="section-heading">

          <span class="section-badge">
            Statistik Pengimbasan
          </span>

          <h2 class="section-title">
            Capaian Pengimbasan
          </h2>

          <p class="section-desc">
            Ringkasan jumlah wilayah, guru, dan siswa yang telah
            menerima pengimbasan Bahasa Melayu Riau.
          </p>

        </div>


        <div class="stats-grid">


          <!-- KABUPATEN -->

          <div class="stat-card stat-card--orange">

            <div class="stat-icon">
              ${icon("building")}
            </div>

            <div
              class="stat-number"
              id="stat-kab"
            >
              —
            </div>

            <div class="stat-label">
              Kabupaten/Kota
            </div>

          </div>


          <!-- GURU -->

          <div class="stat-card stat-card--blue">

            <div class="stat-icon">
              ${icon("user")}
            </div>

            <div
              class="stat-number"
              id="stat-guru"
            >
              —
            </div>

            <div class="stat-label">
              Guru Terimbas
            </div>

          </div>


          <!-- SISWA -->

          <div class="stat-card stat-card--green">

            <div class="stat-icon">
              ${icon("graduation")}
            </div>

            <div
              class="stat-number"
              id="stat-siswa"
            >
              —
            </div>

            <div class="stat-label">
              Siswa Terimbas
            </div>

          </div>


        </div>


        

      </section>



      <!-- ==================================
           PERBANDINGAN
      =================================== -->

      <section
        class="home-content-section reveal"
        aria-label="Perbandingan guru dan siswa"
      >

        <div class="section-heading">

          <span class="section-badge">
            Data Pengimbasan
          </span>

          <h2 class="section-title">
            Perbandingan Guru &amp; Siswa
          </h2>

          <p class="section-desc">
            Perbandingan jumlah guru dan siswa yang telah menerima
            pengimbasan di seluruh Provinsi Riau.
          </p>

        </div>


        <div
          class="split-compare"
          id="split-compare"
        >

          <div
            class="split-track"
            aria-hidden="true"
          >

            <div
              class="split-fill split-fill--guru"
              id="split-guru"
            ></div>

            <div
              class="split-fill split-fill--siswa"
              id="split-siswa"
            ></div>

          </div>


          <div class="split-legend">


            <!-- GURU -->

            <button
              type="button"
              class="split-item split-item--guru"
              data-target="guru"
            >

              <span
                class="split-dot"
                aria-hidden="true"
              ></span>

              <span class="split-item-label">

                ${icon("user")}

                Guru

              </span>

              <b id="split-guru-pct">
                —
              </b>

            </button>


            <!-- SISWA -->

            <button
              type="button"
              class="split-item split-item--siswa"
              data-target="siswa"
            >

              <span
                class="split-dot"
                aria-hidden="true"
              ></span>

              <span class="split-item-label">

                ${icon("graduation")}

                Siswa

              </span>

              <b id="split-siswa-pct">
                —
              </b>

            </button>


          </div>

        </div>

      </section>



      <!-- ==================================
           CARA MENGGUNAKAN
      =================================== -->

      <section class="home-content-section reveal">

        <div class="section-heading">

          <span class="section-badge">
            Panduan
          </span>

          <h2 class="section-title">
            Cara Menggunakan
          </h2>

          <p class="section-desc">
            Ikuti beberapa langkah sederhana untuk melihat dan
            menambahkan data pengimbasan.
          </p>

        </div>


        <div class="steps-grid">

          ${STEPS.map(
            (s, i) => `
              <article class="step-card">

                <span
                  class="step-number"
                  aria-hidden="true"
                >
                  0${i + 1}
                </span>

                <span
                  class="step-icon"
                  aria-hidden="true"
                >
                  ${icon(s.icon)}
                </span>

                <h3>
                  ${s.title}
                </h3>

                <p>
                  ${s.desc}
                </p>

              </article>
            `,
          ).join("")}

        </div>

      </section>



      <!-- ==================================
           FITUR
      =================================== -->

      <section class="home-content-section reveal">

        <div class="section-heading">

          <span class="section-badge">
            Fitur Platform
          </span>

          <h2 class="section-title">
            Fitur Utama
          </h2>

          <p class="section-desc">
            Fitur yang tersedia untuk membantu pemantauan
            pengimbasan Bahasa Melayu Riau.
          </p>

        </div>


        <div class="feature-grid">

          ${FEATURES.map(
            (f) => `
              <article
                class="feature-card feature-card--${f.color}"
              >

                <span
                  class="feature-icon"
                  aria-hidden="true"
                >
                  ${icon(f.icon)}
                </span>


                <h3>
                  ${f.title}
                </h3>


                <p>
                  ${f.desc}
                </p>


                <button
                  type="button"
                  class="feature-toggle"
                  aria-expanded="false"
                >

                  <span>
                    Lihat detail
                  </span>

                  <span
                    class="feature-toggle-icon"
                    aria-hidden="true"
                  >
                    ⌄
                  </span>

                </button>


                <div class="feature-detail">

                  <ul>

                    ${f.detail
                      .map(
                        (d) => `
                          <li>
                            ${d}
                          </li>
                        `,
                      )
                      .join("")}

                  </ul>

                </div>

              </article>
            `,
          ).join("")}

        </div>

      </section>



      <!-- ==================================
           FAQ
      =================================== -->

      ${faqSection}



      <!-- ==================================
           CTA
      =================================== -->

      <section class="cta-band reveal">

        <div class="cta-content">

          <span class="section-badge">
            Mulai Menjelajah
          </span>

          <h2>
            Jelajahi Data
            <br />
            Pengimbasan Bahasa Melayu Riau
          </h2>

          <p>
            Lihat persebaran data pada peta atau tambahkan
            data pengimbasan baru melalui form.
          </p>


          <div class="hero-actions">

            <a
              class="btn btn-primary"
              href="/peta"
              data-route="/peta"
            >
              Buka Peta
            </a>

            <a
              class="btn btn-light"
              href="/form"
              data-route="/form"
            >
              Input Data
            </a>

          </div>

        </div>

      </section>


    </div>


    ${footerHtml()}

  `;


  /* ==========================================
     INITIALIZATION
  ========================================== */

  observeReveals(container);

  setupTilt(container);

  setupSplitCompare(container);

  setupFeatureAccordion(container);

  setupFaq(container);

  loadHomeData(container);
}


/* ==========================================
   LOAD DATA
========================================== */

async function loadHomeData(root) {

  const [
    guruRes,
    siswaRes,
    kabRes,
  ] = await Promise.allSettled([

    fetchJumlahGuruPerKabupaten(),

    fetchJumlahSiswaPerKabupaten(),

    getKabupatenList(),

  ]);


  if (guruRes.status === "rejected") {

    console.warn(
      "Gagal memuat statistik guru:",
      guruRes.reason,
    );

  }


  if (siswaRes.status === "rejected") {

    console.warn(
      "Gagal memuat statistik siswa:",
      siswaRes.reason,
    );

  }


  const homeData = {

    guru:
      guruRes.status === "fulfilled"
        ? guruRes.value
        : {},

    siswa:
      siswaRes.status === "fulfilled"
        ? siswaRes.value
        : {},

    kab:
      kabRes.status === "fulfilled"
        ? kabRes.value
        : KABUPATEN_LIST,

    guruOk:
      guruRes.status === "fulfilled",

    siswaOk:
      siswaRes.status === "fulfilled",

  };


  renderStats(root, homeData);

  renderSplitCompare(root, homeData);
}


/* ==========================================
   STATISTICS
========================================== */

function renderStats(root, data) {

  const statKab =
    root.querySelector("#stat-kab");

  const statGuru =
    root.querySelector("#stat-guru");

  const statSiswa =
    root.querySelector("#stat-siswa");


  if (
    !statKab ||
    !statGuru ||
    !statSiswa
  ) {
    return;
  }


  const {
    guru,
    siswa,
    kab,
    guruOk,
    siswaOk,
  } = data;


  const totalGuru =
    Object.values(guru).reduce(
      (a, b) => a + b,
      0,
    );


  const totalSiswa =
    Object.values(siswa).reduce(
      (a, b) => a + b,
      0,
    );


  animateNumber(
    statKab,
    kab.length,
  );


  if (guruOk) {

    animateNumber(
      statGuru,
      totalGuru,
    );

  } else {

    statGuru.textContent = "—";

  }


  if (siswaOk) {

    animateNumber(
      statSiswa,
      totalSiswa,
    );

  } else {

    statSiswa.textContent = "—";

  }
}


/* ==========================================
   SPLIT COMPARISON
========================================== */

function renderSplitCompare(root, data) {

  const fillGuru =
    root.querySelector("#split-guru");

  const fillSiswa =
    root.querySelector("#split-siswa");

  const pctGuru =
    root.querySelector("#split-guru-pct");

  const pctSiswa =
    root.querySelector("#split-siswa-pct");


  if (
    !fillGuru ||
    !fillSiswa ||
    !pctGuru ||
    !pctSiswa
  ) {
    return;
  }


  const {
    guru,
    siswa,
    guruOk,
    siswaOk,
  } = data;


  if (!guruOk || !siswaOk) {

    pctGuru.textContent = "—";

    pctSiswa.textContent = "—";

    fillGuru.style.width = "50%";

    fillSiswa.style.width = "50%";

    return;
  }


  const totalGuru =
    Object.values(guru).reduce(
      (a, b) => a + b,
      0,
    );


  const totalSiswa =
    Object.values(siswa).reduce(
      (a, b) => a + b,
      0,
    );


  const total =
    totalGuru + totalSiswa;


  if (total === 0) {

    pctGuru.textContent = "0%";

    pctSiswa.textContent = "0%";

    fillGuru.style.width = "50%";

    fillSiswa.style.width = "50%";

    return;
  }


  const guruPct =
    Math.round(
      (totalGuru / total) * 100,
    );


  const siswaPct =
    100 - guruPct;


  pctGuru.textContent =
    `${guruPct}%`;

  pctSiswa.textContent =
    `${siswaPct}%`;


  requestAnimationFrame(() => {

    fillGuru.style.width =
      `${guruPct}%`;

    fillSiswa.style.width =
      `${siswaPct}%`;

  });
}


/* ==========================================
   SPLIT INTERACTION
========================================== */

function setupSplitCompare(root) {

  const wrap =
    root.querySelector(
      "#split-compare",
    );


  if (!wrap) return;


  wrap
    .querySelectorAll(".split-item")
    .forEach((btn) => {

      btn.addEventListener(
        "click",
        () => {

          const alreadyActive =
            btn.classList.contains(
              "is-active",
            );


          wrap
            .querySelectorAll(
              ".split-item",
            )
            .forEach((b) =>
              b.classList.remove(
                "is-active",
              ),
            );


          wrap.classList.remove(
            "has-active",
          );


          delete wrap.dataset.active;


          if (!alreadyActive) {

            btn.classList.add(
              "is-active",
            );

            wrap.classList.add(
              "has-active",
            );

            wrap.dataset.active =
              btn.dataset.target;

          }

        },
      );

    });
}


/* ==========================================
   FEATURE ACCORDION
========================================== */

function setupFeatureAccordion(root) {

  root
    .querySelectorAll(
      ".feature-toggle",
    )
    .forEach((btn) => {

      btn.addEventListener(
        "click",
        () => {

          const card =
            btn.closest(
              ".feature-card",
            );


          if (!card) return;


          const isOpen =
            card.classList.toggle(
              "is-open",
            );


          btn.setAttribute(
            "aria-expanded",
            String(isOpen),
          );

        },
      );

    });
}


/* ==========================================
   FAQ
========================================== */

function setupFaq(root) {

  const items =
    root.querySelectorAll(
      ".faq-item",
    );


  items.forEach((item) => {

    const q =
      item.querySelector(
        ".faq-q",
      );


    if (!q) return;


    q.addEventListener(
      "click",
      () => {

        const isOpen =
          item.classList.contains(
            "is-open",
          );


        items.forEach(
          (other) => {

            other.classList.remove(
              "is-open",
            );


            const otherQ =
              other.querySelector(
                ".faq-q",
              );


            if (otherQ) {

              otherQ.setAttribute(
                "aria-expanded",
                "false",
              );

            }

          },
        );


        if (!isOpen) {

          item.classList.add(
            "is-open",
          );

          q.setAttribute(
            "aria-expanded",
            "true",
          );

        }

      },
    );

  });
}


/* ==========================================
   CARD TILT
========================================== */

function setupTilt(root) {

  const cards =
    root.querySelectorAll(
      ".stat-card",
    );


  if (
    !window.matchMedia(
      "(hover: hover) and (pointer: fine)",
    ).matches
  ) {
    return;
  }


  cards.forEach((card) => {

    card.addEventListener(
      "mousemove",
      (e) => {

        const rect =
          card.getBoundingClientRect();


        const x =
          e.clientX - rect.left;


        const y =
          e.clientY - rect.top;


        const rotateX =
          ((y / rect.height) - 0.5) *
          -8;


        const rotateY =
          ((x / rect.width) - 0.5) *
          8;


        card.style.transform =
          `perspective(700px)
           rotateX(${rotateX}deg)
           rotateY(${rotateY}deg)
           translateY(-4px)`;

      },
    );


    card.addEventListener(
      "mouseleave",
      () => {

        card.style.transform = "";

      },
    );

  });
}


/* ==========================================
   NUMBER ANIMATION
========================================== */

function animateNumber(
  el,
  target,
) {

  if (!Number.isFinite(target)) {

    el.textContent = target;

    return;
  }


  const duration = 800;

  const start =
    performance.now();


  function step(now) {

    const p =
      Math.min(
        1,
        (now - start) /
          duration,
      );


    const eased =
      1 -
      Math.pow(
        1 - p,
        3,
      );


    el.textContent =
      Math.round(
        target * eased,
      ).toLocaleString(
        "id-ID",
      );


    if (p < 1) {

      requestAnimationFrame(
        step,
      );

    }

  }


  requestAnimationFrame(step);
}


/* ==========================================
   SCROLL REVEAL
========================================== */

function observeReveals(root) {

  const els =
    root.querySelectorAll(
      ".reveal",
    );


  if (
    !(
      "IntersectionObserver"
      in window
    )
  ) {

    els.forEach((el) =>
      el.classList.add(
        "is-visible",
      ),
    );

    return;
  }


  const io =
    new IntersectionObserver(
      (entries) => {

        entries.forEach(
          (entry) => {

            if (
              entry.isIntersecting
            ) {

              entry.target.classList.add(
                "is-visible",
              );


              io.unobserve(
                entry.target,
              );

            }

          },
        );

      },
      {
        threshold: 0.12,
      },
    );


  els.forEach((el) =>
    io.observe(el),
  );
}