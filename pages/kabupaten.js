/* =====================================================================
 * HALAMAN DETAIL KABUPATEN — tabel Guru & Siswa
 *  - Tabel interaktif per jenis data (guru / siswa)
 *  - Pencarian (search) mencakup semua kolom
 *  - Filter per kolom + nilai (dropdown generik sesuai skema tabel)
 *  - Paginasi 10 baris per halaman
 * ===================================================================== */

import { KABUPATEN_LIST, KOLOM_GURU, KOLOM_SISWA, TABEL_GURU, TABEL_SISWA } from '../config.js';
import { fetchRowsByKabupaten } from '../api.js';
import { escapeHtml, showToast } from '../ui.js';
import { csvField } from '../csv.js';
import { footerHtml } from '../footer.js';
import { icon } from '../icons.js';

/** Jumlah baris per halaman untuk semua tabel detail. */
const PER_PAGE = 10;

function renderRowValue(value) {
  if (value === null || value === undefined || value === '') return '—';
  return escapeHtml(String(value));
}

function labelColumn(col) {
  return col.replace(/_/g, ' ');
}


/** Daftar halaman yang ditampilkan: [1, '…', 4, 5, 6, '…', 12]. */
function pageWindow(current, total) {
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
  const pages = [1];
  const start = Math.max(2, current - 1);
  const end = Math.min(total - 1, current + 1);
  if (start > 2) pages.push('…');
  for (let p = start; p <= end; p += 1) pages.push(p);
  if (end < total - 1) pages.push('…');
  pages.push(total);
  return pages;
}

/**
 * Pasang komponen tabel interaktif ke dalam `root`:
 * toolbar pencarian + filter kolom/nilai + tabel + paginasi.
 * Tiga kondisi tanpa tabel ditangani dengan kartu status:
 * error, data tidak tersedia, dan daftar kosong.
 */
function mountDataTable(root, { columns, rows, label, emptyText, error, fileSlug = label.toLowerCase().replace(/\s+/g, '-') }) {
  if (error) {
    root.innerHTML = `<div class="status-card status-card--warn">${escapeHtml(error)}</div>`;
    return;
  }
  if (!Array.isArray(rows)) {
    root.innerHTML = `<div class="status-card status-card--warn">${escapeHtml(label)} tidak dapat ditampilkan.</div>`;
    return;
  }
  if (rows.length === 0) {
    root.innerHTML = `<div class="status-card">${escapeHtml(emptyText)}</div>`;
    return;
  }

  const labelLc = label.toLowerCase();
  let search = '';
  let filterColumn = '';
  let filterValue = '';
  let page = 1;

  root.innerHTML = `
    <div class="dt-toolbar">
      <div class="dt-search">
        <span class="dt-search-icon" aria-hidden="true">${icon('search')}</span>
        <input
          type="search"
          class="dt-search-input"
          placeholder="Cari data ${escapeHtml(labelLc)}…"
          aria-label="Cari ${escapeHtml(labelLc)}"
          autocomplete="off"
        />
      </div>
      <div class="dt-filters">
        <span class="dt-select">
          <select class="dt-filter dt-filter-column" aria-label="Filter ${escapeHtml(labelLc)} berdasarkan kolom">
            <option value="">Filter kolom…</option>
            ${columns.map((col) => `<option value="${escapeHtml(col)}">${escapeHtml(labelColumn(col))}</option>`).join('')}
          </select>
          ${icon('chevron-down')}
        </span>
        <span class="dt-select">
          <select class="dt-filter dt-filter-value" aria-label="Filter ${escapeHtml(labelLc)} berdasarkan nilai" disabled>
            <option value="">Semua nilai…</option>
          </select>
          ${icon('chevron-down')}
        </span>
        <button type="button" class="dt-reset" aria-label="Reset pencarian dan filter">${icon('x')} Reset</button>
        <button type="button" class="dt-export" aria-label="Unduh CSV hasil saat ini" title="Unduh data sesuai pencarian/filter saat ini">${icon('download')} Unduh CSV</button>
      </div>
    </div>
    <div class="dt-body"></div>
  `;

  const searchInput = root.querySelector('.dt-search-input');
  const columnSelect = root.querySelector('.dt-filter-column');
  const valueSelect = root.querySelector('.dt-filter-value');
  const resetBtn = root.querySelector('.dt-reset');
  const bodyEl = root.querySelector('.dt-body');

  /** Baris yang lolos pencarian + filter saat ini. */
  function filteredRows() {
    const q = search.trim().toLowerCase();
    return rows.filter((row) => {
      if (q && !columns.some((col) => String(row[col] ?? '').toLowerCase().includes(q))) return false;
      if (filterColumn && filterValue && String(row[filterColumn] ?? '').trim() !== filterValue) return false;
      return true;
    });
  }

  /** Nilai unik (urut alfabet) untuk sebuah kolom — sumber dropdown filter. */
  function distinctValues(col) {
    const seen = new Set();
    for (const row of rows) {
      const v = String(row[col] ?? '').trim();
      if (v) seen.add(v);
    }
    return [...seen].sort((a, b) => a.localeCompare(b, 'id'));
  }

  /** Isi ulang dropdown nilai sesuai kolom yang dipilih. */
  function refreshValueOptions() {
    valueSelect.disabled = !filterColumn;
    valueSelect.innerHTML =
      `<option value="">${filterColumn ? `Semua ${escapeHtml(labelColumn(filterColumn))}…` : 'Semua nilai…'}</option>` +
      distinctValues(filterColumn)
        .map((v) => `<option value="${escapeHtml(v)}">${escapeHtml(v)}</option>`)
        .join('');
  }

  function resetAll() {
    search = '';
    filterColumn = '';
    filterValue = '';
    page = 1;
    searchInput.value = '';
    columnSelect.value = '';
    refreshValueOptions();
    render();
  }

  function render() {
    const list = filteredRows();
    const totalPages = Math.max(1, Math.ceil(list.length / PER_PAGE));
    if (page > totalPages) page = totalPages;
    if (page < 1) page = 1;

    if (list.length === 0) {
      bodyEl.innerHTML = `
        <div class="status-card">
          Tidak ada data yang cocok dengan pencarian/filter saat ini.
          <button type="button" class="dt-reset dt-empty-reset" aria-label="Reset pencarian dan filter">${icon('x')} Reset filter</button>
        </div>
      `;
      bodyEl.querySelector('.dt-empty-reset').addEventListener('click', resetAll);
      return;
    }

    const start = (page - 1) * PER_PAGE;
    const pageRows = list.slice(start, start + PER_PAGE);

    bodyEl.innerHTML = `
      <div class="table-wrap">
        <table class="data-table" aria-label="${escapeHtml(label)}">
          <caption>${escapeHtml(label)}</caption>
          <thead>
            <tr>
              ${columns.map((col) => `<th scope="col">${escapeHtml(labelColumn(col))}</th>`).join('')}
            </tr>
          </thead>
          <tbody>
            ${pageRows
              .map(
                (row) => `
                  <tr>
                    ${columns.map((col) => `<td>${renderRowValue(row[col])}</td>`).join('')}
                  </tr>
                `
              )
              .join('')}
          </tbody>
        </table>
      </div>
      <div class="dt-pagination">
        <span class="dt-info">
          Menampilkan <b>${(start + 1).toLocaleString('id-ID')}–${(start + pageRows.length).toLocaleString('id-ID')}</b>
          dari <b>${list.length.toLocaleString('id-ID')}</b> data
        </span>
        <nav class="dt-pages" aria-label="Paginasi ${escapeHtml(label)}">
          <button type="button" class="dt-page-btn dt-page-nav" data-page="prev" aria-label="Halaman sebelumnya" ${page === 1 ? 'disabled' : ''}>${icon('chevron-left')}</button>
          ${pageWindow(page, totalPages)
            .map((p) =>
              p === '…'
                ? '<span class="dt-page-ellipsis" aria-hidden="true">…</span>'
                : `<button type="button" class="dt-page-btn${p === page ? ' is-active' : ''}" data-page="${p}" aria-label="Halaman ${p}"${p === page ? ' aria-current="page"' : ''}>${p}</button>`
            )
            .join('')}
          <button type="button" class="dt-page-btn dt-page-nav" data-page="next" aria-label="Halaman berikutnya" ${page === totalPages ? 'disabled' : ''}>${icon('chevron-right')}</button>
        </nav>
      </div>
    `;
  }

  searchInput.addEventListener('input', () => {
    search = searchInput.value;
    page = 1;
    render();
  });

  columnSelect.addEventListener('change', () => {
    filterColumn = columnSelect.value;
    filterValue = '';
    refreshValueOptions();
    page = 1;
    render();
  });

  valueSelect.addEventListener('change', () => {
    filterValue = valueSelect.value;
    page = 1;
    render();
  });

  resetBtn.addEventListener('click', resetAll);

  // Ekspor CSV — mengunduh baris sesuai pencarian/filter saat ini (BOM utk Excel).
  const exportBtn = root.querySelector('.dt-export');
  exportBtn.addEventListener('click', () => {
    const list = filteredRows();
    if (list.length === 0) {
      showToast('Tidak ada data yang bisa diekspor.', 'error');
      return;
    }
    const csv =
      '\uFEFF' +
      [
        columns.map((col) => labelColumn(col)).join(','),
        ...list.map((row) => columns.map((col) => csvField(row[col])).join(',')),
      ].join('\r\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${fileSlug}.csv`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
    showToast(`Berhasil mengunduh ${list.length.toLocaleString('id-ID')} baris (CSV).`);
  });

  // Delegasi klik tombol halaman (konten tabel di-render ulang tiap kali).
  root.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-page]');
    if (!btn) return;
    const list = filteredRows();
    const totalPages = Math.max(1, Math.ceil(list.length / PER_PAGE));
    if (btn.dataset.page === 'prev') page = Math.max(1, page - 1);
    else if (btn.dataset.page === 'next') page = Math.min(totalPages, page + 1);
    else page = Math.min(totalPages, Math.max(1, Number(btn.dataset.page)));
    render();
  });

  refreshValueOptions();
  render();
}

export async function renderKabupaten(container, params) {
  const kabupaten = String(params.name || '').trim();
  const validKabupaten = KABUPATEN_LIST.includes(kabupaten);

  container.innerHTML = `
    <div class="page-kabupaten">
      <a class="back-link" href="/peta" data-route="/peta">← Kembali ke Peta</a>
      <section class="kabupaten-hero">
        <span class="hero-badge">${icon('map')}Data Per Kabupaten</span>
        <h1>Detail ${escapeHtml(kabupaten || 'Kabupaten')}</h1>
        <p class="kabupaten-lead">
          ${validKabupaten
            ? 'Menampilkan tabel Guru dan Siswa secara terpisah per kabupaten. Gunakan pencarian, filter, dan paginasi untuk menjelajah data.'
            : 'Kabupaten tidak valid atau belum didukung. Pilih wilayah pada peta untuk melihat detail yang tersedia.'}
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="/peta" data-route="/peta">Kembali ke Peta</a>
          <a class="btn btn-outline" href="/form" data-route="/form">Input Data</a>
        </div>
      </section>

      <section class="section-block">
        <div id="kabupaten-content">
          <div class="status-card">Memuat data kabupaten…</div>
        </div>
      </section>
    </div>
    ${footerHtml()}
  `;

  const contentEl = document.getElementById('kabupaten-content');
  if (!validKabupaten) {
    contentEl.innerHTML = `
      <div class="status-card status-card--warn">
        Nama kabupaten tidak dikenali.
      </div>
    `;
    return;
  }

  try {
    const [guruRes, siswaRes] = await Promise.allSettled([
      fetchRowsByKabupaten(TABEL_GURU, kabupaten),
      fetchRowsByKabupaten(TABEL_SISWA, kabupaten),
    ]);

    const guruRows = guruRes.status === 'fulfilled' ? guruRes.value : null;
    const siswaRows = siswaRes.status === 'fulfilled' ? siswaRes.value : null;

    const totalGuru = Array.isArray(guruRows) ? guruRows.length : 0;
    const totalSiswa = Array.isArray(siswaRows) ? siswaRows.length : 0;

    contentEl.innerHTML = `
      <div class="kabupaten-summary">
        <div class="mini-stat">
          <b>${totalGuru}</b>
          <span>Guru</span>
        </div>
        <div class="mini-stat">
          <b>${totalSiswa}</b>
          <span>Siswa</span>
        </div>
      </div>

      <div class="detail-section">
        <h2>Data Guru di ${escapeHtml(kabupaten)}</h2>
        <div class="dt-root" id="dt-root-guru"></div>
      </div>

      <div class="detail-section">
        <h2>Data Siswa di ${escapeHtml(kabupaten)}</h2>
        <div class="dt-root" id="dt-root-siswa"></div>
      </div>
    `;

    mountDataTable(document.getElementById('dt-root-guru'), {
      columns: KOLOM_GURU,
      rows: guruRows,
      label: 'Tabel Guru',
      fileSlug: `guru-${kabupaten.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`,
      emptyText: `Tidak ada data guru untuk ${kabupaten}.`,
      error: guruRes.status === 'rejected' ? `Gagal memuat data guru: ${escapeHtml(guruRes.reason?.message || 'Terjadi kesalahan')}` : null,
    });

    mountDataTable(document.getElementById('dt-root-siswa'), {
      columns: KOLOM_SISWA,
      rows: siswaRows,
      label: 'Tabel Siswa',
      fileSlug: `siswa-${kabupaten.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`,
      emptyText: `Tidak ada data siswa untuk ${kabupaten}.`,
      error: siswaRes.status === 'rejected' ? `Gagal memuat data siswa: ${escapeHtml(siswaRes.reason?.message || 'Terjadi kesalahan')}` : null,
    });
  } catch (err) {
    contentEl.innerHTML = `
      <div class="status-card status-card--warn">
        Terjadi kesalahan saat memuat data: ${escapeHtml(err.message || 'Tidak diketahui')}.
      </div>
    `;
  }
}
