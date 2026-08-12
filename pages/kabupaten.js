import { KABUPATEN_LIST, KOLOM_GURU, KOLOM_SISWA, TABEL_GURU, TABEL_SISWA } from '../config.js';
import { fetchRowsByKabupaten } from '../api.js';
import { escapeHtml } from '../ui.js';
import { footerHtml } from '../footer.js';
import { icon } from '../icons.js';

function renderRowValue(value) {
  if (value === null || value === undefined || value === '') return '—';
  return escapeHtml(String(value));
}

function renderTable(rows, columns, caption) {
  if (!Array.isArray(rows)) {
    return `<div class="status-card status-card--warn">${escapeHtml(caption)} tidak dapat ditampilkan.</div>`;
  }

  if (rows.length === 0) {
    return `<div class="status-card">Tidak ada data ${escapeHtml(caption)} untuk kabupaten ini.</div>`;
  }

  return `
    <div class="table-wrap">
      <table class="data-table" aria-label="${escapeHtml(caption)}">
        <caption>${escapeHtml(caption)}</caption>
        <thead>
          <tr>
            ${columns.map((col) => `<th scope="col">${escapeHtml(col.replace(/_/g, ' '))}</th>`).join('')}
          </tr>
        </thead>
        <tbody>
          ${rows
            .map(
              (row) => `
                <tr>
                  ${columns
                    .map((col) => `<td>${renderRowValue(row[col])}</td>`)
                    .join('')}
                </tr>
              `
            )
            .join('')}
        </tbody>
      </table>
    </div>
  `;
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
            ? 'Menampilkan tabel Guru dan Siswa secara terpisah per kabupaten.'
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

    const guruStatus =
      guruRes.status === 'rejected'
        ? `<div class="status-card status-card--warn">Gagal memuat data guru: ${escapeHtml(guruRes.reason?.message || 'Terjadi kesalahan')}</div>`
        : '';
    const siswaStatus =
      siswaRes.status === 'rejected'
        ? `<div class="status-card status-card--warn">Gagal memuat data siswa: ${escapeHtml(siswaRes.reason?.message || 'Terjadi kesalahan')}</div>`
        : '';

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
      ${guruStatus}${siswaStatus}
      <div class="detail-section">
        <h2>Data Guru di ${escapeHtml(kabupaten)}</h2>
        ${renderTable(guruRows, KOLOM_GURU, 'Tabel Guru')}
      </div>
      <div class="detail-section">
        <h2>Data Siswa di ${escapeHtml(kabupaten)}</h2>
        ${renderTable(siswaRows, KOLOM_SISWA, 'Tabel Siswa')}
      </div>
    `;
  } catch (err) {
    contentEl.innerHTML = `
      <div class="status-card status-card--warn">
        Terjadi kesalahan saat memuat data: ${escapeHtml(err.message || 'Tidak diketahui')}.
      </div>
    `;
  }
}
