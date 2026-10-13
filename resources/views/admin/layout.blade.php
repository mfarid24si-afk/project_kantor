@extends('layouts.portal')

@section('portal_header_title')
  <div class="portal-header-meta">
    <span class="role-badge role-badge--admin">Super Admin</span>
    <h2 class="portal-header-heading">Panel Kendali Pengimbasan</h2>
  </div>
@endsection

@section('portal_body')
  <div class="admin-content">
    @yield('admin_content')
  </div>
@endsection
