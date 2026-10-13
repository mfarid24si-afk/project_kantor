<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Literata:opsz,wght@7..72,400;7..72,600;7..72,700;7..72,800&family=Hanken+Grotesk:wght@400;600;700;800&display=swap" />
    <title>{{ $title ?? 'Portal — Beringin Riau' }}</title>

    @vite(['resources/css/style.css', 'resources/js/app.js'])
    @stack('styles')
  </head>

  <body class="portal-body">
    <div class="portal-wrapper">
      <!-- SIDEBAR -->
      <aside class="portal-sidebar" id="portal-sidebar">
        <div class="portal-sidebar-header">
          <a href="{{ url('/') }}" class="portal-sidebar-brand" title="Ke Beranda Publik">
            <img src="{{ asset('assets/images/logo-bbpr.png') }}" alt="Logo BBPR" height="34" />
          </a>
        </div>

        <div class="portal-sidebar-user">
          <div class="portal-user-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <div class="portal-user-meta">
            <span class="portal-user-name">{{ auth()->user()->name }}</span>
            <span class="portal-user-role">
              @if(auth()->user()->isSuperAdmin())
                Super Admin
              @else
                Guru Utama ({{ auth()->user()->guruUtama?->kabupaten ?? 'Riau' }})
              @endif
            </span>
          </div>
        </div>

        <nav class="portal-sidebar-nav" aria-label="Navigasi Menu Portal">
          @if(auth()->user()->isSuperAdmin())
            <div class="portal-nav-group-label">Panel Super Admin</div>
            <a class="portal-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
              <span>Dashboard Admin</span>
            </a>
            <a class="portal-nav-item {{ request()->routeIs('admin.kegiatan*') ? 'active' : '' }}" href="{{ route('admin.kegiatan.index') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
              <span>Kelola Kegiatan</span>
            </a>
            <a class="portal-nav-item {{ request()->routeIs('admin.maestro*') ? 'active' : '' }}" href="{{ route('admin.maestro.index') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>Data Maestro</span>
            </a>
            <a class="portal-nav-item {{ request()->routeIs('admin.guru_utama*') ? 'active' : '' }}" href="{{ route('admin.guru_utama.index') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              <span>Akun Guru Utama</span>
            </a>
          @else
            <div class="portal-nav-group-label">Menu Guru Utama</div>
            <a class="portal-nav-item {{ request()->routeIs('guru_utama.dashboard') ? 'active' : '' }}" href="{{ route('guru_utama.dashboard') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
              <span>Dashboard &amp; Tugas</span>
            </a>
            <a class="portal-nav-item {{ request()->routeIs('guru_utama.guru*') ? 'active' : '' }}" href="{{ route('guru_utama.guru.index') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <span>Guru yang Diajarkan</span>
            </a>
            <a class="portal-nav-item {{ request()->routeIs('guru_utama.siswa*') ? 'active' : '' }}" href="{{ route('guru_utama.siswa.index') }}">
              <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
              <span>Murid yang Diajarkan</span>
            </a>
          @endif

        </nav>

        <div class="portal-sidebar-footer">
          <form method="POST" action="{{ route('logout') }}" style="margin: 0; width: 100%;">
            @csrf
            <button type="submit" class="portal-logout-btn">
              <svg class="ic" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
              <span>Keluar Akun</span>
            </button>
          </form>
        </div>
      </aside>

      <!-- MAIN CONTENT AREA -->
      <div class="portal-main">
        <header class="portal-topbar">
          <button type="button" class="portal-sidebar-toggle" id="portal-sidebar-toggle" aria-label="Toggle Sidebar">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
          </button>
          
          <div class="portal-topbar-title">
            @yield('portal_header_title')
          </div>

          <div class="portal-topbar-actions">
            <a href="{{ url('/') }}" class="portal-home-link" title="Kunjungi Website Publik">
              Lihat Beranda Publik
            </a>
          </div>
        </header>

        <main class="portal-content-wrap">
          @if(session('success'))
            <div class="alert alert--success admin-alert">
              {{ session('success') }}
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert--error admin-alert">
              {{ session('error') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert--error admin-alert">
              <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @yield('portal_body')
        </main>
      </div>
    </div>

    <script>
      // Toggle sidebar pada mobile
      document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('portal-sidebar-toggle');
        const sidebar = document.getElementById('portal-sidebar');
        if (toggleBtn && sidebar) {
          toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
          });
          document.addEventListener('click', function (e) {
            if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
              sidebar.classList.remove('active');
            }
          });
        }
      });
    </script>
    @stack('scripts')
  </body>
</html>
