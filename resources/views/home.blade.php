@extends('layouts.app')

@section('content')
<div class="page-tentang page-home" id="page-home">
  <!-- HERO -->
  <section class="tentang-hero home-hero">
    <div class="hero-watermark" aria-hidden="true">SABARA</div>

    <div class="tentang-hero-content home-hero-content">
      <span class="tentang-eyebrow">
        <span class="live-dot" aria-hidden="true"></span>
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/></svg>
        Revitalisasi Bahasa Daerah
      </span>

      <h1>
        Peta Pengimbasan<br />
        <span>Bahasa Melayu Riau</span>
      </h1>

      <p>
        Pantau sejauh mana pengimbasan Revitalisasi Bahasa Daerah menjangkau guru dan siswa di seluruh kabupaten/kota Provinsi Riau melalui satu platform digital yang informatif dan mudah diakses.
      </p>

      <div class="hero-actions">
        <a class="btn btn-primary" href="{{ url('/peta') }}" data-route="/peta">
          Buka Peta
        </a>
      </div>
    </div>
  </section>

  <!-- STATISTIK -->
  <section class="home-content-section stats-section reveal" aria-label="Ringkasan data">
    <div class="section-heading">
      <span class="section-badge">Statistik Pengimbasan</span>
      <h2 class="section-title">Capaian Pengimbasan</h2>
      <p class="section-desc">
        Ringkasan jumlah wilayah, guru, dan siswa yang telah menerima pengimbasan Bahasa Melayu Riau.
      </p>
    </div>

    <div class="stats-grid">
      <!-- KABUPATEN -->
      <div class="stat-card stat-card--orange">
        <div class="stat-icon">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
        </div>
        <div class="stat-number" id="stat-kab">—</div>
        <div class="stat-label">Kabupaten/Kota</div>
      </div>

      <!-- GURU -->
      <div class="stat-card stat-card--blue">
        <div class="stat-icon">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="stat-number" id="stat-guru">—</div>
        <div class="stat-label">Guru Terimbas</div>
      </div>

      <!-- SISWA -->
      <div class="stat-card stat-card--green">
        <div class="stat-icon">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
        </div>
        <div class="stat-number" id="stat-siswa">—</div>
        <div class="stat-label">Siswa Terimbas</div>
      </div>
    </div>
  </section>

  <!-- PERBANDINGAN -->
  <section class="home-content-section reveal" aria-label="Perbandingan guru dan siswa">
    <div class="section-heading">
      <span class="section-badge">Data Pengimbasan</span>
      <h2 class="section-title">Perbandingan Guru &amp; Siswa</h2>
      <p class="section-desc">
        Perbandingan jumlah guru dan siswa yang telah menerima pengimbasan di seluruh Provinsi Riau.
      </p>
    </div>

    <div class="split-compare" id="split-compare">
      <div class="split-track" aria-hidden="true">
        <div class="split-fill split-fill--guru" id="split-guru"></div>
        <div class="split-fill split-fill--siswa" id="split-siswa"></div>
      </div>

      <div class="split-legend">
        <!-- GURU -->
        <button type="button" class="split-item split-item--guru" data-target="guru">
          <span class="split-dot" aria-hidden="true"></span>
          <span class="split-item-label">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Guru
          </span>
          <b id="split-guru-pct">—</b>
        </button>

        <!-- SISWA -->
        <button type="button" class="split-item split-item--siswa" data-target="siswa">
          <span class="split-dot" aria-hidden="true"></span>
          <span class="split-item-label">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
            Siswa
          </span>
          <b id="split-siswa-pct">—</b>
        </button>
      </div>
    </div>
  </section>

  <!-- CARA MENGGUNAKAN -->
  <section class="home-content-section reveal">
    <div class="section-heading">
      <span class="section-badge">Panduan</span>
      <h2 class="section-title">Cara Menggunakan</h2>
      <p class="section-desc">
        Ikuti beberapa langkah sederhana untuk melihat dan menambahkan data pengimbasan.
      </p>
    </div>

    <div class="steps-grid">
      <article class="step-card">
        <span class="step-number" aria-hidden="true">01</span>
        <span class="step-icon" aria-hidden="true">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/><path d="M15 5.764v15"/><path d="M9 3.236v15"/></svg>
        </span>
        <h3>Buka Peta</h3>
        <p>Telusuri kabupaten/kota di Provinsi Riau melalui peta interaktif.</p>
      </article>

      <article class="step-card">
        <span class="step-number" aria-hidden="true">02</span>
        <span class="step-icon" aria-hidden="true">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 9 5 12 1.8-5.2L21 14Z"/><path d="M7.2 2.2 8 5.1"/><path d="m5.1 8-2.9-.8"/><path d="M14 4.1 12 6"/><path d="m6 12-1.9 2"/></svg>
        </span>
        <h3>Pilih Wilayah</h3>
        <p>Klik wilayah untuk melihat jumlah guru dan siswa yang telah terimbas.</p>
      </article>

      <article class="step-card">
        <span class="step-number" aria-hidden="true">03</span>
        <span class="step-icon" aria-hidden="true">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
        </span>
        <h3>Input Data</h3>
        <p>Tambahkan data guru atau siswa melalui halaman form pengimbasan.</p>
      </article>
    </div>
  </section>

  <!-- FITUR -->
  <section class="home-content-section reveal">
    <div class="section-heading">
      <span class="section-badge">Fitur Platform</span>
      <h2 class="section-title">Fitur Utama</h2>
      <p class="section-desc">
        Fitur yang tersedia untuk membantu pemantauan pengimbasan Bahasa Melayu Riau.
      </p>
    </div>

    <div class="feature-grid">
      <!-- Peta Interaktif -->
      <article class="feature-card feature-card--blue">
        <span class="feature-icon" aria-hidden="true">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/><path d="M15 5.764v15"/><path d="M9 3.236v15"/></svg>
        </span>
        <h3>Peta Interaktif</h3>
        <p>12 poligon kabupaten/kota dengan batas administratif akurat. Klik wilayah untuk melihat rincian guru dan siswa yang telah terimbas.</p>
        <button type="button" class="feature-toggle" aria-expanded="false">
          <span>Lihat detail</span>
          <span class="feature-toggle-icon" aria-hidden="true">⌄</span>
        </button>
        <div class="feature-detail">
          <ul>
            <li>Batas wilayah mengikuti data administratif resmi</li>
            <li>Klik wilayah untuk melihat rincian guru &amp; siswa</li>
          </ul>
        </div>
      </article>

      <!-- Data Realtime -->
      <article class="feature-card feature-card--orange">
        <span class="feature-icon" aria-hidden="true">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
        </span>
        <h3>Data Realtime</h3>
        <p>Data yang dimasukkan melalui form langsung diperbarui pada sistem sehingga informasi pengimbasan selalu terkini.</p>
        <button type="button" class="feature-toggle" aria-expanded="false">
          <span>Lihat detail</span>
          <span class="feature-toggle-icon" aria-hidden="true">⌄</span>
        </button>
        <div class="feature-detail">
          <ul>
            <li>Sinkron otomatis melalui Supabase</li>
            <li>Perubahan dapat dilihat pengguna tanpa reload manual</li>
          </ul>
        </div>
      </article>

      <!-- Form Input -->
      <article class="feature-card feature-card--green">
        <span class="feature-icon" aria-hidden="true">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
        </span>
        <h3>Form Input</h3>
        <p>Masukkan data guru dan siswa yang telah menerima pengimbasan melalui form yang sederhana dan mudah digunakan.</p>
        <button type="button" class="feature-toggle" aria-expanded="false">
          <span>Lihat detail</span>
          <span class="feature-toggle-icon" aria-hidden="true">⌄</span>
        </button>
        <div class="feature-detail">
          <ul>
            <li>Validasi otomatis sebelum data disimpan</li>
            <li>Notifikasi setelah data berhasil dikirim</li>
          </ul>
        </div>
      </article>
    </div>
  </section>

  <!-- FAQ -->
  <section class="home-content-section reveal">
    <div class="section-heading">
      <span class="section-badge">Informasi</span>
      <h2 class="section-title">Pertanyaan Umum</h2>
      <p class="section-desc">
        Beberapa pertanyaan yang sering ditanyakan mengenai sistem pengimbasan Bahasa Melayu Riau.
      </p>
    </div>

    <div class="faq-list">
      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false">
          <span>Apa itu Revitalisasi Bahasa Daerah?</span>
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-a">
          <p>Program untuk menghidupkan kembali bahasa daerah yang penggunaannya semakin berkurang melalui pelatihan, materi ajar, dan kegiatan pembelajaran.</p>
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false">
          <span>Apa itu pengimbasan?</span>
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-a">
          <p>Pengimbasan merupakan proses penyebaran pengetahuan secara berjenjang dari guru yang telah mendapatkan pelatihan kepada guru dan siswa lainnya.</p>
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false">
          <span>Apakah data di peta diperbarui secara realtime?</span>
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-a">
          <p>Ya. Data guru dan siswa yang ditambahkan melalui form akan diperbarui pada sistem secara otomatis melalui Supabase.</p>
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false">
          <span>Bagaimana cara menambahkan data?</span>
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-a">
          <p>Buka halaman Form, pilih jenis data Guru atau Siswa, lengkapi data yang diperlukan, kemudian kirim data tersebut.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-band reveal">
    <div class="cta-content">
      <span class="section-badge">Mulai Menjelajah</span>
      <h2>
        Jelajahi Data<br />
        Pengimbasan Bahasa Melayu Riau
      </h2>
      <p>
        Lihat persebaran data pada peta atau tambahkan data pengimbasan baru melalui form.
      </p>

      <div class="hero-actions">
        <a class="btn btn-primary" href="{{ url('/peta') }}" data-route="/peta">
          Buka Peta
        </a>
        <a class="btn btn-light" href="{{ url('/form') }}" data-route="/form">
          Input Data
        </a>
      </div>
    </div>
  </section>
</div>
@endsection
