@extends('layouts.app')

@section('content')
<div class="page-kabupaten" id="page-kabupaten" data-kabupaten="{{ $kabupatenName }}">
  <a class="back-link" href="{{ url('/peta') }}" data-route="/peta">← Kembali ke Peta</a>

  <section class="kabupaten-hero">
    <span class="hero-badge">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/><path d="M15 5.764v15"/><path d="M9 3.236v15"/></svg>
      Data Per Kabupaten
    </span>
    <h1>Detail {{ $kabupatenName }}</h1>
    <p class="kabupaten-lead">
      Pilih tab di bawah untuk melihat data Guru, Siswa, Komunitas, atau Umum. Gunakan pencarian, filter, dan paginasi untuk menjelajah data.
    </p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="{{ url('/peta') }}" data-route="/peta">Kembali ke Peta</a>
      <a class="btn btn-outline" href="{{ url('/form') }}" data-route="/form">Input Data</a>
    </div>
  </section>

  <!-- TABBED NAVBAR untuk switch tabel -->
  <nav class="detail-tabs" aria-label="Navigasi tabel detail">
    <button type="button" class="detail-tab active" data-tab="guru" aria-selected="true">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Guru
    </button>
    <button type="button" class="detail-tab" data-tab="siswa" aria-selected="false">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
      Siswa
    </button>
    <button type="button" class="detail-tab" data-tab="komunitas" aria-selected="false">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Komunitas
    </button>
    <button type="button" class="detail-tab" data-tab="umum" aria-selected="false">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
      Umum
    </button>
  </nav>

  <section class="section-block">
    <div id="kabupaten-content">
      <div class="status-card">Memuat data kabupaten…</div>
    </div>
  </section>
</div>
@endsection
