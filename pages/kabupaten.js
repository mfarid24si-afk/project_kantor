/* =====================================================================
 * HALAMAN DETAIL KABUPATEN — 4 tabel dengan tabbed navbar
 *  - Tabbed navbar untuk switch antar tabel (Guru, Siswa, Komunitas, Umum)
 *  - Guru: data difilter per kabupaten
 *  - Siswa, Komunitas, Umum: semua data (tidak punya kolom kabupaten)
 *  - Pencarian, filter, paginasi untuk setiap tabel
 * ===================================================================== */

import {
  KABUPATEN_LIST,
  KOLOM_GURU, KOLOM_SISWA, KOLOM_KOMUNITAS, KOLOM_UMUM,
  TABEL_GURU, TABEL_SISWA, TABEL_KOMUNITAS, TABEL_UMUM,
} from '../config.js';
import { fetchRowsByKabupaten, fetchAllRows } from '../api.js';
import { escapeHtml, showToast } from '../ui.js';
import { csvField } from '../csv.js';
import { footerHtml } from '../footer.js';
import { icon } from '../icons.js';

/** Jumlah baris per halaman untuk semua tabel detail. */
const PER_PAGE = 10;

/** Konfigurasi 4 tabel. */
const TABLES = [
  { key: 'guru', label: 'Guru', table: TABEL_GURU, columns: KOLOM_GURU, hasKabupaten: true },
  { key: 'siswa', label: 'Siswa', table: TABEL_SISWA, columns: KOLOM_SISWA, hasKabupaten: false },
  { key: 'komunitas', label: 'Komunitas', table: TABEL_KOMUNITAS, columns: KOLOM_KOMUNITAS, hasKabupaten: false },
  { key: 'umum', label: 'Umum', table: TABEL_UMUM, columns: KOLOM_UMUM, hasKabupaten: false },
];

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
 */
function mountDataTable(root, { columns, rows, label, emptyText, error, fileSlug }) {
  if (!fileSlug) fileSlug = label.toLowerCase().replace(/\s+/g, '-');
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

  function filteredRows() {
    const q = search.trim().toLowerCase();
    return rows.filter((row) => {
      if (q && !columns.some((col) => String(row[col] ?? '').toLowerCase().includes(q))) return false;
      if (filterColumn && filterValue && String(row[filterColumn] ?? '').trim() !== filterValue) return false;
      return true;
    });
  }

  function distinctValues(col) {
    const seen = new Set();
    for (const row of rows) {
      const v = String(row[col] ?? '').trim();
      if (v) seen.add(v);
    }
    return [...seen].sort((a, b) => a.localeCompare(b, 'id'));
  }

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

  // Ekspor CSV
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

  // Delegasi klik tombol halaman
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
            ? 'Pilih tab di bawah untuk melihat data Guru, Siswa, Komunitas, atau Umum. Gunakan pencarian, filter, dan paginasi untuk menjelajah data.'
            : 'Kabupaten tidak valid atau belum didukung. Pilih wilayah pada peta untuk melihat detail yang tersedia.'}
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="/peta" data-route="/peta">Kembali ke Peta</a>
          <a class="btn btn-outline" href="/form" data-route="/form">Input Data</a>
        </div>
      </section>

      ${validKabupaten ? `
      <!-- TABBED NAVBAR untuk switch tabel -->
      <nav class="detail-tabs" aria-label="Navigasi tabel detail">
        <button type="button" class="detail-tab active" data-tab="guru" aria-selected="true">
          ${icon('user')} Guru
        </button>
        <button type="button" class="detail-tab" data-tab="siswa" aria-selected="false">
          ${icon('graduation')} Siswa
        </button>
        <button type="button" class="detail-tab" data-tab="komunitas" aria-selected="false">
          ${icon('users')} Komunitas
        </button>
        <button type="button" class="detail-tab" data-tab="umum" aria-selected="false">
          ${icon('building')} Umum
        </button>
      </nav>
      ` : ''}

      <section class="section-block">
        <div id="kabupaten-content">
          <div class="status-card">${validKabupaten ? 'Memuat data kabupaten…' : 'Kabupaten tidak valid.'}</div>
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

  // Load semua data sekaligus
  const allData = {};

  try {
    const results = await Promise.allSettled([
      fetchRowsByKabupaten(TABEL_GURU, kabupaten),     // guru: filter by kabupaten
      fetchAllRows(TABEL_SISWA),                        // siswa: semua data
      fetchAllRows(TABEL_KOMUNITAS),                    // komunitas: semua data
      fetchAllRows(TABEL_UMUM),                         // umum: semua data
    ]);

    const labels = ['Guru', 'Siswa', 'Komunitas', 'Umum'];
    results.forEach((res, i) => {
      allData[TABLES[i].key] = {
        rows: res.status === 'fulfilled' ? res.value : null,
        error: res.status === 'rejected' ? res.reason?.message || 'Gagal memuat data' : null,
      };
    });
  } catch (err) {
    contentEl.innerHTML = `
      <div class="status-card status-card--warn">
        Terjadi kesalahan saat memuat data: ${escapeHtml(err.message || 'Tidak diketahui')}.
      </div>
    `;
    return;
  }

  // Render summary
  function renderSummary() {
    const guruCount = allData.guru.rows ? allData.guru.rows.length : 0;
    const siswaCount = allData.siswa.rows ? allData.siswa.rows.length : 0;
    const komunitasCount = allData.komunitas.rows ? allData.komunitas.rows.length : 0;
    const umumCount = allData.umum.rows ? allData.umum.rows.length : 0;

    return `
      <div class="kabupaten-summary">
        <div class="mini-stat">
          <b>${guruCount}</b>
          <span>Guru</span>
        </div>
        <div class="mini-stat">
          <b>${siswaCount}</b>
          <span>Siswa</span>
        </div>
        <div class="mini-stat">
          <b>${komunitasCount}</b>
          <span>Komunitas</span>
        </div>
        <div class="mini-stat">
          <b>${umumCount}</b>
          <span>Umum</span>
        </div>
      </div>
    `;
  }

  // Render tabel berdasarkan tab aktif
  function renderTabContent(tabKey) {
    const tbl = TABLES.find((t) => t.key === tabKey);
    if (!tbl) return;

    const data = allData[tabKey];
    const slug = `${tabKey}-${kabupaten.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`;

    let description = '';
    if (tbl.hasKabupaten) {
      description = `Data ${tbl.label.toLowerCase()} di ${kabupaten}.`;
    } else {
      description = `Semua data ${tbl.label.toLowerCase()} (tidak difilter per kabupaten).`;
    }

    contentEl.innerHTML = `
      ${renderSummary()}
      <div class="detail-section">
        <h2>Data ${tbl.label} ${tbl.hasKabupaten ? `di ${escapeHtml(kabupaten)}` : ''}</h2>
        <p class="detail-section-desc">${escapeHtml(description)}</p>
        <div class="dt-root" id="dt-root-active"></div>
      </div>
    `;

    mountDataTable(document.getElementById('dt-root-active'), {
      columns: tbl.columns,
      rows: data?.rows,
      label: `Tabel ${tbl.label}`,
      fileSlug: slug,
      emptyText: tbl.hasKabupaten
        ? `Tidak ada data ${tbl.label.toLowerCase()} untuk ${kabupaten}.`
        : `Tidak ada data ${tbl.label.toLowerCase()}.`,
      error: data?.error ? `Gagal memuat data ${tbl.label.toLowerCase()}: ${escapeHtml(data.error)}` : null,
    });
  }

  // Setup tab switching
  let activeTab = 'guru';
  const tabs = document.querySelectorAll('.detail-tab');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const tabKey = tab.dataset.tab;
      if (tabKey === activeTab) return;
      activeTab = tabKey;
      tabs.forEach((t) => {
        const isActive = t.dataset.tab === tabKey;
        t.classList.toggle('active', isActive);
        t.setAttribute('aria-selected', String(isActive));
      });
      renderTabContent(tabKey);
    });
  });

  // Render default tab (Guru)
  renderTabContent('guru');
}
