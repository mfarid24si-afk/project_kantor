@extends('layouts.app')

@section('content')
<div class="admin-shell">
  <div class="admin-topbar">
    <div class="admin-topbar-brand">
      <span class="role-badge role-badge--guru-utama">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Guru Utama
      </span>
      <h1>Portal Pengimbasan Guru Utama</h1>
    </div>

    <div class="admin-user-info">
      @php
        $guProfile = auth()->user()->guruUtama;
      @endphp
      <span class="admin-user-name">{{ $guProfile?->nama ?? auth()->user()->name }}</span>
      <span class="admin-user-role">{{ $guProfile?->asal_sekolah ?? 'Pendidik' }} ({{ $guProfile?->kabupaten ?? 'Riau' }})</span>
    </div>
  </div>

  <nav class="admin-nav" aria-label="Navigasi Guru Utama">
    <a class="admin-nav-link {{ request()->routeIs('guru_utama.dashboard') ? 'active' : '' }}" href="{{ route('guru_utama.dashboard') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
      Dashboard &amp; Tugas
    </a>
    <a class="admin-nav-link {{ request()->routeIs('guru_utama.guru*') ? 'active' : '' }}" href="{{ route('guru_utama.guru.index') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Guru yang Diajarkan
    </a>
    <a class="admin-nav-link {{ request()->routeIs('guru_utama.siswa*') ? 'active' : '' }}" href="{{ route('guru_utama.siswa.index') }}">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
      Murid / Siswa yang Diajarkan
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

  @yield('portal_content')
</div>
@endsection
