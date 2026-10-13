@extends('layouts.portal')

@section('portal_header_title')
  <div class="portal-header-meta">
    <span class="role-badge role-badge--guru-utama">Guru Utama</span>
    <h2 class="portal-header-heading">Portal Pengimbasan Guru Utama</h2>
  </div>
@endsection

@section('portal_body')
  @yield('portal_content')
@endsection
