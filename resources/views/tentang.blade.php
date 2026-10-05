@extends('layouts.app')

@section('content')
<div class="page-tentang" id="page-tentang">
  <!-- HERO -->
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

  <!-- KOLABORASI -->
  <section class="kolaborasi-section reveal">
    <div class="kolaborasi-card">
      <!-- IMAGE -->
      <div class="kolaborasi-image">
        <div class="image-collage">
          <div class="collage-item">
            <img
              src="{{ asset('assets/images/pcr.jpg') }}"
              alt="Politeknik Caltex Riau"
              width="600"
              height="400"
              loading="eager"
              decoding="async"
            />
            <span class="image-label">KAMPUS PCR</span>
          </div>

          <div class="collage-item">
            <img
              src="{{ asset('assets/images/bbpr.jpeg') }}"
              alt="Balai Bahasa Provinsi Riau"
              width="600"
              height="400"
              loading="eager"
              decoding="async"
            />
            <span class="image-label">KANTOR BBPR</span>
          </div>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="kolaborasi-content">
        <span class="section-badge">Kolaborasi</span>

        <h2>
          Kolaborasi Akademik<br />
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
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="developer-text">
              <span>DEVELOPER</span>
              <strong>Noval Nugraha</strong>
            </div>
          </div>

          <!-- DEVELOPER 2 -->
          <div class="developer-info">
            <div class="developer-icon">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="developer-text">
              <span>DEVELOPER</span>
              <strong>Muhammad Majid Avindra</strong>
            </div>
          </div>

          <!-- DEVELOPER 3 -->
          <div class="developer-info">
            <div class="developer-icon">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="developer-text">
              <span>DEVELOPER</span>
              <strong>Rifky Faerana Alfarizi</strong>
            </div>
          </div>

          <!-- DEVELOPER 4 -->
          <div class="developer-info">
            <div class="developer-icon">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
              src="{{ asset('assets/images/logo-pcr.webp') }}"
              alt="Logo Politeknik Caltex Riau"
              width="120"
              height="44"
              loading="lazy"
              decoding="async"
            />
          </div>

          <div class="logo-divider"></div>

          <div class="logo-wrapper logo-bbpr">
            <img
              src="{{ asset('assets/images/logo-bbpr.png') }}"
              alt="Logo Balai Bahasa Provinsi Riau"
              width="260"
              height="44"
              loading="lazy"
              decoding="async"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
