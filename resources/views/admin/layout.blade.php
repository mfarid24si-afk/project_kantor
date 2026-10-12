@extends('layouts.app')

@section('content')
<div class="admin-shell">
  <div class="admin-topbar">
    <div class="admin-topbar-brand">
      <span class="role-badge role-badge--admin">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Super Admin
      </span>
      <h1>Panel Kendali Pengimbasan</h1>
    </div>

    <div class="admin-user-info">
      <span class="admin-user-name">{{ auth()->user()->name }}</span>
      <span class="admin-user-role">Administrator</span>
    </div>
  </div>

  <nav class="admin-nav" aria-label="Navigasi admin">
    <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
      Dashboard
    </a>
    <a class="admin-nav-link {{ request()->routeIs('admin.kegiatan*') ? 'active' : '' }}" href="{{ route('admin.kegiatan.index') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
      Kegiatan
    </a>
    <a class="admin-nav-link {{ request()->routeIs('admin.maestro*') ? 'active' : '' }}" href="{{ route('admin.maestro.index') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      Maestro
    </a>
    <a class="admin-nav-link {{ request()->routeIs('admin.guru_utama*') ? 'active' : '' }}" href="{{ route('admin.guru_utama.index') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Guru Utama
    </a>
  </nav>

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
      <ul>
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="admin-content">
    @yield('admin_content')
  </div>
</div>
@endsection
