@extends('layouts.app')

@section('content')
<div class="page-form" id="page-form">
  <a class="back-link" href="{{ url('/') }}" data-route="/">← Kembali ke Beranda</a>

  <div class="form-card">
    <h2>Input Data</h2>
    <p class="form-desc">
      Simpan data ke database. Pilih jenis data yang ingin dimasukkan.
    </p>

    <div class="segmented" role="tablist" aria-label="Jenis data">
      <button type="button" class="segmented-btn active" data-jenis="guru" role="tab" aria-selected="true">Guru</button>
      <button type="button" class="segmented-btn" data-jenis="siswa" role="tab" aria-selected="false">Siswa</button>
      <button type="button" class="segmented-btn" data-jenis="komunitas" role="tab" aria-selected="false">Komunitas</button>
      <button type="button" class="segmented-btn" data-jenis="umum" role="tab" aria-selected="false">Umum</button>
    </div>

    <form id="data-form" novalidate>
      <div id="form-fields"></div>
      <button type="submit" class="btn btn-primary form-submit" id="btn-submit">Simpan Data</button>
      <p class="form-note">
        Data yang diisi akan tercatat pada tabel
        <b id="note-table">guru</b> di Supabase.
      </p>
    </form>
  </div>

  <div class="form-card upload-card">
    <h2>Upload Data Massal (CSV)</h2>
    <p class="form-desc">
      Unggah banyak baris sekaligus ke tabel <b id="upload-target">guru</b>.
      Cocokkan header CSV dengan kolom tabel; baris yang tidak valid dilaporkan
      tanpa menghentikan sisanya.
    </p>

    <div
      class="upload-drop"
      id="upload-drop"
      role="button"
      tabindex="0"
      aria-label="Pilih atau seret file CSV untuk diunggah"
    >
      <input type="file" id="upload-input" accept=".csv,text/csv" multiple hidden />
      <span class="upload-icon" aria-hidden="true">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
      </span>
      <p class="upload-hint">Klik untuk pilih file, atau seret &amp; lepas file <b>.csv</b> ke sini</p>
      <p class="upload-sub">Boleh banyak file sekaligus</p>
      <button type="button" class="btn btn-outline" id="btn-template">Unduh contoh CSV</button>
    </div>

    <div id="upload-list" class="upload-list"></div>

    <div class="upload-actions" id="upload-actions" hidden>
      <button type="button" class="btn btn-primary" id="btn-upload">Mulai Upload</button>
      <button type="button" class="btn btn-outline" id="btn-clear-upload">Bersihkan Daftar</button>
      <span class="upload-progress" id="upload-progress" aria-live="polite"></span>
    </div>

    <p class="form-note" id="upload-note">Target tabel: <b>guru</b> · dikirim per batch 500 baris.</p>
  </div>
</div>
@endsection
