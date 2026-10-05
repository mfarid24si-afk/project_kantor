<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Peta interaktif sebaran guru dan siswa terimbas Revitalisasi Bahasa Daerah (Bahasa Melayu Riau) di 12 kabupaten/kota Provinsi Riau."
    />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Literata:opsz,wght@7..72,400;7..72,600;7..72,700;7..72,800&family=Hanken+Grotesk:wght@400;600;700;800&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Literata:opsz,wght@7..72,400;7..72,600;7..72,700;7..72,800&family=Hanken+Grotesk:wght@400;600;700;800&display=swap" />
    <title>{{ $title ?? 'Peta Sebaran Guru & Siswa — Provinsi Riau' }}</title>

    @vite(['resources/css/style.css', 'resources/js/app.js'])
    @stack('styles')
  </head>

  <body data-page="{{ $pageId ?? '' }}">
    <a class="skip-link" href="#app">Lewati ke konten</a>

    <nav class="navbar" aria-label="Navigasi utama">
      <a class="navbar-brand" href="{{ url('/') }}" aria-label="Beranda — Peta Guru dan Siswa Riau">
        <img
          src="{{ asset('assets/images/logo-bbpr.png') }}"
          alt="Logo Balai Bahasa Provinsi Riau"
          width="140"
          height="38"
        />
      </a>

      <div class="navbar-nav" id="navbar-nav">
        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}" data-route="/">Beranda</a>
        <a class="nav-link {{ request()->is('peta') || request()->is('kabupaten*') ? 'active' : '' }}" href="{{ url('/peta') }}" data-route="/peta">Peta</a>
        <a class="nav-link {{ request()->is('form') ? 'active' : '' }}" href="{{ url('/form') }}" data-route="/form">Input Data</a>
        <a class="nav-link {{ request()->is('tentang') ? 'active' : '' }}" href="{{ url('/tentang') }}" data-route="/tentang">Tentang</a>
      </div>

      <button
        class="navbar-toggle"
        id="navbar-toggle"
        type="button"
        aria-label="Buka menu navigasi"
        aria-expanded="false"
        aria-controls="navbar-nav"
      >
        <span class="hamburger"></span>
        <span class="hamburger"></span>
        <span class="hamburger"></span>
      </button>
    </nav>

    <main id="app">
      @yield('content')
    </main>

    @include('partials.footer')

    <div id="page-loader" class="page-loader" role="status" aria-label="Memuat halaman" aria-live="polite" style="display:none">
      <div class="page-loader__spinner"></div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    @stack('scripts')
  </body>
</html>
