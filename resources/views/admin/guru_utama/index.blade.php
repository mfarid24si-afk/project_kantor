@extends('admin.layout')

@section('admin_content')
<div class="admin-page">
  <div class="admin-page-header">
    <div>
      <h2>Data Guru Utama</h2>
      <p class="admin-page-desc">Kelola Guru Utama pembina yang bertugas mengimbaskan materi maestro kepada guru dan siswa.</p>
    </div>
    <a href="{{ route('admin.guru_utama.create') }}" class="btn btn-primary">
      + Tambah Guru Utama Baru
    </a>
  </div>

  <!-- SEARCH & FILTER -->
  <div class="admin-filter-bar">
    <form method="GET" action="{{ route('admin.guru_utama.index') }}" class="admin-search-form">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama, NUPTK, sekolah, email…"
        class="form-control"
      />
      <select name="kabupaten" class="form-control" onchange="this.form.submit()">
        <option value="">-- Semua Kabupaten --</option>
        @foreach($kabupatens as $kab)
          <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>
            {{ $kab }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-outline">Filter</button>
      @if(request('q') || request('kabupaten'))
        <a href="{{ route('admin.guru_utama.index') }}" class="btn btn-link">Reset</a>
      @endif
    </form>
  </div>

  <!-- TABLE -->
  <div class="admin-card">
    @if($guruUtamas->isEmpty())
      <div class="empty-state">
        Tidak ada data guru utama ditemukan.
        <a href="{{ route('admin.guru_utama.create') }}" class="btn-link">Tambah sekarang</a>
      </div>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama &amp; Akun Login</th>
              <th>Sekolah &amp; Kabupaten</th>
              <th>NUPTK / NIP</th>
              <th>Kontak</th>
              <th style="text-align: center;">Tugas Kegiatan</th>
              <th style="text-align: center;">Hasil Pengimbasan</th>
              <th style="width: 140px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($guruUtamas as $gu)
              <tr>
                <td>
                  <b>{{ $gu->nama }}</b>
                  <div class="table-sub">{{ $gu->user?->email ?? '—' }}</div>
                </td>
                <td>
                  <div>{{ $gu->asal_sekolah }}</div>
                  <span class="badge badge--pill badge--blue">{{ $gu->kabupaten }}</span>
                </td>
                <td>
                  @if($gu->nuptk)
                    <div><small class="text-muted">NUPTK:</small> {{ $gu->nuptk }}</div>
                  @endif
                  @if($gu->nip)
                    <div><small class="text-muted">NIP:</small> {{ $gu->nip }}</div>
                  @endif
                  @if(!$gu->nuptk && !$gu->nip)
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>{{ $gu->no_hp ?: '—' }}</td>
                <td style="text-align: center;">
                  <span class="badge badge--pill badge--purple">{{ $gu->kegiatans_count }} kegiatan</span>
                </td>
                <td style="text-align: center;">
                  <div style="font-size: 0.85rem;">
                    <span class="badge badge--pill badge--green">{{ $gu->gurus_count }} guru</span>
                    <span class="badge badge--pill badge--teal">{{ $gu->siswas_count }} siswa</span>
                  </div>
                </td>
                <td style="text-align: center;">
                  <div class="table-actions">
                    <a href="{{ route('admin.guru_utama.edit', $gu) }}" class="btn btn-outline btn--xs">Edit</a>
                    <form method="POST" action="{{ route('admin.guru_utama.destroy', $gu) }}" onsubmit="return confirm('Yakin ingin menghapus guru utama {{ $gu->nama }} dan akun loginnya?');" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn--xs">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="admin-pagination">
        {{ $guruUtamas->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
