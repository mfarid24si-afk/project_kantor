@extends('layouts.app')

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <span class="auth-badge">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Portal Masuk
      </span>
      <h2>Masuk ke Sistem</h2>
      <p class="auth-sub">
        Masuk untuk Super Admin atau Guru Utama Pengimbasan Bahasa Melayu Riau.
      </p>
    </div>

    @if(session('success'))
      <div class="alert alert--success">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert--error">
        @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="auth-form">
      @csrf

      <div class="form-group">
        <label for="email">Alamat Email</label>
        <input
          type="email"
          id="email"
          name="email"
          value="{{ old('email') }}"
          required
          autofocus
          placeholder="nama@contoh.id"
          class="form-control"
        />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          required
          placeholder="••••••••"
          class="form-control"
        />
      </div>

      <div class="form-group form-group--checkbox">
        <label class="checkbox-label">
          <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
          <span>Ingat saya di perangkat ini</span>
        </label>
      </div>

      <button type="submit" class="btn btn-primary btn--block">
        Masuk Sekarang →
      </button>

      <div class="auth-footer-help">
        <p>
          Akun <b>Guru Utama</b> didaftarkan oleh <b>Super Admin</b>.<br />
          Hubungi koordinator jika belum memiliki akses.
        </p>
      </div>
    </form>
  </div>
</div>
@endsection
