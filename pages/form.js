/* =====================================================================
 * HALAMAN FORM — Input Data (FR-4)
 *  - Pilihan jenis data: Guru / Siswa / Komunitas / Umum
 *  - Field dibangun dinamis dari daftar kolom di config.js (FR-5.3),
 *    sehingga form selalu cocok dengan skema tabel di Supabase.
 *  - Dropdown kabupaten/kota hanya untuk tabel guru (yang punya kolom kabupaten)
 *  - Validasi dasar (field wajib + kabupaten harus dari daftar valid untuk guru)
 *  - POST ke Supabase memakai struktur API key yang sama (FR-5.2)
 *  - Notifikasi sukses + form ter-reset setelah submit (FR-4.6)
 * ===================================================================== */

import {
  TABEL_GURU, TABEL_SISWA, TABEL_KOMUNITAS, TABEL_UMUM,
  KOLOM_GURU, KOLOM_SISWA, KOLOM_KOMUNITAS, KOLOM_UMUM,
  KABUPATEN_LIST, SEMUA_TABEL,
} from '../config.js';
import { insertBaris, insertBanyakBaris } from '../api.js';
import { getKabupatenList, escapeHtml, showToast } from '../ui.js';
import { parseCsv, csvField } from '../csv.js';
import { icon } from '../icons.js';
import { footerHtml } from '../footer.js';
import { pageReady } from '../pageReadyManager.js';

// Kolom yang wajib diisi per jenis data.
const REQUIRED = {
  guru: new Set(['nama_guru', 'kabupaten', 'asal_sekolah']),
  siswa: new Set(['nama_siswa', 'sekolah']),
  komunitas: new Set(['nama_individu', 'nama_komunitas']),
  umum: new Set(['nama_umum', 'pekerjaan']),
};

// Metadata tampilan & aturan per kolom (key = nama kolom di Supabase).
const FIELD_META = {
  // Guru
  nama_guru: { label: 'Nama Guru', placeholder: 'Nama lengkap guru', autocomplete: 'name' },
  nuptk: { label: 'NUPTK', placeholder: 'Nomor NUPTK (opsional)', inputType: 'number', numeric: true },
  asal_sekolah: { label: 'Asal Sekolah', placeholder: 'Nama sekolah / instansi', autocomplete: 'organization' },
  kelurahan: { label: 'Kelurahan / Desa', placeholder: 'Kelurahan/desa tempat mengajar (opsional)' },
  kabupaten: { label: 'Kabupaten / Kota', type: 'select' },
  provinsi: { label: 'Provinsi', placeholder: 'Provinsi (opsional, contoh: Riau)' },
  nama_guru_utama: { label: 'Nama Guru Utama', placeholder: 'Nama guru utama / penanggung jawab (opsional)' },
  // Siswa
  nama_siswa: { label: 'Nama Siswa', placeholder: 'Nama lengkap siswa', autocomplete: 'name' },
  sekolah: { label: 'Nama Sekolah', placeholder: 'Nama sekolah / instansi', autocomplete: 'organization' },
  nis: { label: 'NIS', placeholder: 'Nomor Induk Siswa (opsional)', inputType: 'number', numeric: true },
  alamat_sekolah: { label: 'Alamat Sekolah', placeholder: 'Alamat sekolah (opsional)' },
  // Komunitas
  nama_individu: { label: 'Nama Individu', placeholder: 'Nama lengkap individu', autocomplete: 'name' },
  nama_komunitas: { label: 'Nama Komunitas', placeholder: 'Nama komunitas / organisasi' },
  alamat_afiliasi: { label: 'Alamat Afiliasi', placeholder: 'Alamat afiliasi (opsional)' },
  // Umum
  nama_umum: { label: 'Nama', placeholder: 'Nama lengkap', autocomplete: 'name' },
  pekerjaan: { label: 'Pekerjaan', placeholder: 'Pekerjaan / profesi' },
  alamat: { label: 'Alamat', placeholder: 'Alamat lengkap (opsional)' },
};

const JENIS = {
  guru: { table: TABEL_GURU, label: 'guru', columns: KOLOM_GURU, hasKabupaten: true },
  siswa: { table: TABEL_SISWA, label: 'siswa', columns: KOLOM_SISWA, hasKabupaten: false },
  komunitas: { table: TABEL_KOMUNITAS, label: 'komunitas', columns: KOLOM_KOMUNITAS, hasKabupaten: false },
  umum: { table: TABEL_UMUM, label: 'umum', columns: KOLOM_UMUM, hasKabupaten: false },
};

// Upload CSV: jumlah baris maksimal per request (batch).
const BATCH_CSV = 500;

// Sinonim header CSV → nama kolom tabel (dibandingkan setelah dinormalisasi).
const HEADER_ALIASES = {
  nama_guru: ['nama_guru', 'nama guru', 'guru'],
  nuptk: ['nuptk'],
  asal_sekolah: ['asal_sekolah', 'asal sekolah', 'sekolah asal', 'instansi'],
  kelurahan: ['kelurahan', 'desa', 'kelurahan desa'],
  kabupaten: ['kabupaten', 'kabupaten kota', 'kab', 'kota'],
  provinsi: ['provinsi'],
  nama_guru_utama: ['nama_guru_utama', 'guru utama', 'penanggung jawab'],
  nama_siswa: ['nama_siswa', 'nama siswa', 'siswa', 'nama'],
  sekolah: ['sekolah', 'nama sekolah'],
  nis: ['nis', 'nomor induk siswa', 'nomor induk'],
  alamat_sekolah: ['alamat_sekolah', 'alamat sekolah'],
  nama_individu: ['nama_individu', 'nama individu', 'individu', 'nama'],
  nama_komunitas: ['nama_komunitas', 'nama komunitas', 'komunitas'],
  alamat_afiliasi: ['alamat_afiliasi', 'alamat afiliasi', 'afiliasi'],
  nama_umum: ['nama_umum', 'nama umum', 'nama'],
  pekerjaan: ['pekerjaan', 'profesi', 'job'],
  alamat: ['alamat'],
};

let kabupatenValid = [];
let jenisAktif = 'guru';
let uploadFiles = [];

export function renderForm(container) {
  container.innerHTML = `
    <div class="page-form">
      <a class="back-link" href="/" data-route="/">← Kembali ke Beranda</a>
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
          <span class="upload-icon" aria-hidden="true">${icon('upload')}</span>
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
    ${footerHtml()}
  `;

  const form = document.getElementById('data-form');
  const btnSubmit = document.getElementById('btn-submit');
  const fieldsContainer = document.getElementById('form-fields');

  /* ------- Daftar kabupaten dari geojson (FR-4.2, FR-7) ------- */
  getKabupatenList().then((names) => {
    kabupatenValid = names;
    fillKabupatenOptions();
    pageReady();
  });

  function fillKabupatenOptions() {
    document.querySelectorAll('select[data-kabupaten]').forEach((sel) => {
      const current = sel.value;
      sel.innerHTML =
        '<option value="">— Pilih kabupaten/kota —</option>' +
        kabupatenValid
          .map((n) => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`)
          .join('');
      sel.value = current && kabupatenValid.includes(current) ? current : '';
    });
  }

  /* ------- Render field sesuai jenis ------- */
  function fieldHtml(key) {
    const meta = FIELD_META[key] || {};
    const required = REQUIRED[jenisAktif].has(key);
    const isSelect = meta.type === 'select' || key === 'kabupaten';
    const inputType = meta.inputType || 'text';

    const label = `
      <label for="input-${key}">${meta.label || key}
        ${required ? '<span class="req" aria-hidden="true">*</span>' : '<span class="opt" aria-hidden="true">(opsional)</span>'}
      </label>
    `;

    if (isSelect) {
      return `
        <div class="form-field" data-field="${key}">
          ${label}
          <select id="input-${key}" data-kabupaten>
            <option value="">— Pilih kabupaten/kota —</option>
          </select>
          <span class="error-msg">Pilih kabupaten/kota dari daftar.</span>
        </div>
      `;
    }

    const maxlength = inputType === 'number' ? '' : ' maxlength="120"';
    return `
      <div class="form-field" data-field="${key}">
        ${label}
        <input id="input-${key}" type="${inputType}"${maxlength}
          placeholder="${meta.placeholder || ''}" ${meta.autocomplete ? `autocomplete="${meta.autocomplete}"` : ''} />
        <span class="error-msg">Wajib diisi.</span>
      </div>
    `;
  }

  function renderFields(jenis) {
    fieldsContainer.innerHTML = JENIS[jenis].columns.map(fieldHtml).join('');
    form.querySelectorAll('input, select').forEach((input) => {
      const handler = () => input.closest('.form-field')?.classList.remove('invalid');
      input.addEventListener('input', handler);
      input.addEventListener('change', handler);
    });
    fillKabupatenOptions();
  }

  /* ------- Pilihan jenis data ------- */
  function setJenis(jenis) {
    jenisAktif = jenis;
    document.querySelectorAll('.segmented-btn').forEach((b) => {
      const active = b.dataset.jenis === jenis;
      b.classList.toggle('active', active);
      b.setAttribute('aria-selected', String(active));
    });
    renderFields(jenis);
    document.getElementById('note-table').textContent = JENIS[jenis].label;
    updateUploadTarget();
  }

  document.querySelectorAll('.segmented-btn').forEach((b) => {
    b.addEventListener('click', () => setJenis(b.dataset.jenis));
  });

  /* ------- Validasi dasar ------- */
  function validateForm() {
    let valid = true;
    for (const key of JENIS[jenisAktif].columns) {
      const fieldEl = form.querySelector(`[data-field="${key}"]`);
      if (!fieldEl) continue;
      const input = fieldEl.querySelector('input, select');
      const value = input.value.trim();
      const required = REQUIRED[jenisAktif].has(key);
      const meta = FIELD_META[key] || {};

      let ok = true;
      let msg = '';

      if (required && value === '') {
        ok = false;
        msg = 'Wajib diisi.';
      }
      if (key === 'kabupaten' && (value === '' || !kabupatenValid.includes(value))) {
        ok = false;
        msg = 'Pilih kabupaten/kota dari daftar.';
      }
      if (ok && meta.numeric && value !== '' && Number.isNaN(Number(value))) {
        ok = false;
        msg = 'Harus berupa angka.';
      }

      fieldEl.classList.toggle('invalid', !ok);
      const errEl = fieldEl.querySelector('.error-msg');
      if (errEl) errEl.textContent = ok ? '' : msg;
      if (!ok) valid = false;
    }
    return valid;
  }

  /* ------- Submit: POST ke Supabase ------- */
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validateForm()) {
      const firstInvalid = form.querySelector('.form-field.invalid input, .form-field.invalid select');
      firstInvalid?.focus();
      return;
    }

    const jenis = JENIS[jenisAktif];
    const payload = {};
    for (const key of jenis.columns) {
      const input = form.querySelector(`[data-field="${key}"] input, [data-field="${key}"] select`);
      const value = input.value.trim();
      if (value === '') continue;
      const meta = FIELD_META[key] || {};
      payload[key] = meta.numeric ? Number(value) : value;
    }

    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Menyimpan…';
    try {
      await insertBaris(jenis.table, payload);
      showToast(`Data ${jenis.label} berhasil disimpan ✓`);
      form.reset();
      renderFields(jenisAktif);
      inputNamaFokus();
    } catch (err) {
      console.error(err);
      showToast(
        `Gagal menyimpan: ${err.message}. Periksa izin RLS tabel di Supabase.`,
        'error'
      );
    } finally {
      btnSubmit.disabled = false;
      btnSubmit.textContent = 'Simpan Data';
    }
  });

  function inputNamaFokus() {
    const first = form.querySelector('.form-field input, .form-field select');
    first?.focus();
  }

  /* ================= Upload Data Massal (CSV) ================= */
  const uploadDrop = document.getElementById('upload-drop');
  const uploadInput = document.getElementById('upload-input');
  const uploadListEl = document.getElementById('upload-list');
  const uploadActions = document.getElementById('upload-actions');
  const btnUpload = document.getElementById('btn-upload');
  const btnClearUpload = document.getElementById('btn-clear-upload');
  const uploadProgress = document.getElementById('upload-progress');
  const uploadTarget = document.getElementById('upload-target');
  const uploadNote = document.getElementById('upload-note');
  const btnTemplate = document.getElementById('btn-template');

  function setUploadEnabled(enabled) {
    uploadDrop.classList.toggle('is-disabled', !enabled);
    uploadInput.disabled = !enabled;
    btnTemplate.disabled = !enabled;
    btnUpload.disabled = !enabled;
  }

  function updateUploadTarget() {
    const jenis = JENIS[jenisAktif];
    uploadTarget.textContent = jenis.label;
    uploadNote.innerHTML = `Target tabel: <b>${escapeHtml(jenis.label)}</b> · dikirim per batch 500 baris.`;
    setUploadEnabled(true);
  }

  function normalizeHeader(h) {
    return String(h)
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '_')
      .replace(/^_+|_+$/g, '');
  }

  function headerToColumn(header, jenis) {
    const norm = normalizeHeader(header);
    for (const col of JENIS[jenis].columns) {
      const aliases = HEADER_ALIASES[col] || [col];
      if (aliases.some((a) => normalizeHeader(a) === norm)) return col;
    }
    return null;
  }

  /** Parse + validasi satu file CSV → daftar baris siap kirim + daftar masalah. */
  function processCsv(text, name) {
    const jenis = jenisAktif;
    const jenisDef = JENIS[jenis];
    const { headers, data } = parseCsv(text);
    const errors = [];

    if (headers.length === 0) {
      return {
        name,
        validRows: [],
        errors: [{ row: 0, msg: 'File kosong atau format CSV tidak dikenali.' }],
        ok: 0,
        fail: 0,
        status: 'siap',
        errorMsg: '',
      };
    }

    const mapping = {};
    const missing = [];
    for (const col of jenisDef.columns) {
      const found = headers.find((h) => headerToColumn(h, jenis) === col);
      if (found) mapping[col] = found;
      else if (REQUIRED[jenis].has(col)) missing.push(col);
    }
    if (missing.length > 0) {
      errors.push({ row: 0, msg: `Kolom wajib tidak ditemukan: ${missing.join(', ')}.` });
    }

    const validRows = [];
    data.forEach((obj, idx) => {
      const lineNo = idx + 2;
      let okRow = true;
      const row = {};
      for (const col of jenisDef.columns) {
        const h = mapping[col];
        if (h == null) continue;
        let v = obj[h] != null ? String(obj[h]).trim() : '';
        const meta = FIELD_META[col] || {};
        if (meta.numeric) {
          if (v !== '' && Number.isNaN(Number(v))) {
            errors.push({ row: lineNo, msg: `Kolom ${col}: '${v}' bukan angka.` });
            okRow = false;
            continue;
          }
          v = v === '' ? '' : Number(v);
        }
        row[col] = v;
      }
      for (const req of REQUIRED[jenis]) {
        if (row[req] == null || String(row[req]).trim() === '') {
          errors.push({ row: lineNo, msg: `Kolom wajib '${req}' kosong.` });
          okRow = false;
        }
      }
      if (row.kabupaten != null && String(row.kabupaten) !== '' && !KABUPATEN_LIST.includes(String(row.kabupaten))) {
        errors.push({ row: lineNo, msg: `Kabupaten '${row.kabupaten}' tidak dikenal.` });
        okRow = false;
      }
      if (okRow) {
        const payload = {};
        for (const col of jenisDef.columns) {
          if (row[col] !== '' && row[col] != null) payload[col] = row[col];
        }
        validRows.push(payload);
      }
    });

    return { name, validRows, errors, ok: 0, fail: 0, status: 'siap', errorMsg: '' };
  }

  function renderUploadList() {
    if (uploadFiles.length === 0) {
      uploadListEl.innerHTML = '';
      uploadActions.hidden = true;
      return;
    }
    uploadActions.hidden = false;
    uploadListEl.innerHTML = uploadFiles
      .map((f) => {
        const badge =
          f.status === 'sukses'
            ? '<span class="up-badge up-badge--ok">Berhasil</span>'
            : f.status === 'gagal'
              ? '<span class="up-badge up-badge--err">Gagal</span>'
              : f.status === 'proses'
                ? '<span class="up-badge">Mengirim…</span>'
                : '<span class="up-badge">Siap</span>';
        const errPreview = f.errors
          .slice(0, 3)
          .map((e) => `<li>${e.row ? `Baris ${e.row}: ` : ''}${escapeHtml(e.msg)}</li>`)
          .join('');
        const more = f.errors.length > 3 ? `<li class="up-more">… dan ${f.errors.length - 3} masalah lain</li>` : '';
        const hasil =
          f.ok + f.fail > 0
            ? ` · <b>${f.ok.toLocaleString('id-ID')}</b> tersimpan, <b>${f.fail.toLocaleString('id-ID')}</b> gagal`
            : '';
        return `
          <div class="up-file">
            <div class="up-file-head">
              <b>${escapeHtml(f.name)}</b>
              ${badge}
            </div>
            <p class="up-file-sum">${f.validRows.length.toLocaleString('id-ID')} baris siap · ${f.errors.length} masalah${hasil}</p>
            ${f.errorMsg ? `<p class="up-file-err">${escapeHtml(f.errorMsg)}</p>` : ''}
            ${errPreview ? `<ul class="up-errors">${errPreview}${more}</ul>` : ''}
          </div>
        `;
      })
      .join('');
  }

  function readFiles(fileList) {
    const files = Array.from(fileList || []).filter(
      (f) => /\.csv$/i.test(f.name) || f.type === 'text/csv'
    );
    if (files.length === 0) {
      showToast('Pilih file dengan ekstensi .csv.', 'error');
      return;
    }
    files.forEach((file) => {
      const reader = new FileReader();
      reader.onload = () => {
        uploadFiles.push(processCsv(reader.result, file.name));
        renderUploadList();
      };
      reader.readAsText(file, 'utf-8');
    });
  }

  uploadInput.addEventListener('change', () => {
    readFiles(uploadInput.files);
    uploadInput.value = '';
  });

  uploadDrop.addEventListener('click', (e) => {
    if (e.target.closest('#btn-template') || uploadInput.disabled) return;
    uploadInput.click();
  });

  uploadDrop.addEventListener('keydown', (e) => {
    if ((e.key === 'Enter' || e.key === ' ') && !uploadInput.disabled) {
      e.preventDefault();
      uploadInput.click();
    }
  });

  ['dragover', 'dragenter'].forEach((ev) =>
    uploadDrop.addEventListener(ev, (e) => {
      if (uploadInput.disabled) return;
      e.preventDefault();
      uploadDrop.classList.add('is-dragover');
    })
  );
  ['dragleave', 'drop'].forEach((ev) =>
    uploadDrop.addEventListener(ev, (e) => {
      if (uploadInput.disabled) return;
      e.preventDefault();
      uploadDrop.classList.remove('is-dragover');
    })
  );
  uploadDrop.addEventListener('drop', (e) => {
    if (uploadInput.disabled) return;
    e.preventDefault();
    readFiles(e.dataTransfer.files);
  });

  // Unduh template CSV sesuai tab aktif.
  btnTemplate.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    const jenis = JENIS[jenisAktif];
    const sample = {};
    for (const col of jenis.columns) {
      const meta = FIELD_META[col] || {};
      if (col === 'kabupaten') sample[col] = 'Pekanbaru';
      else sample[col] = `Contoh ${meta.label || col}`;
    }
    const csv =
      '\uFEFF' +
      [jenis.columns.join(','), jenis.columns.map((c) => csvField(sample[c] ?? '')).join(',')].join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `contoh-upload-${jenis.label}.csv`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
  });

  // Kirim batch demi batch.
  btnUpload.addEventListener('click', async () => {
    const jenis = JENIS[jenisAktif];
    const files = uploadFiles.filter((f) => f.validRows.length > 0);
    if (files.length === 0) {
      showToast('Tidak ada baris valid untuk diunggah.', 'error');
      return;
    }
    btnUpload.disabled = true;
    let totalOk = 0;
    let totalFail = 0;
    for (const f of files) {
      f.status = 'proses';
      f.errorMsg = '';
      renderUploadList();
      const batchTotal = Math.ceil(f.validRows.length / BATCH_CSV);
      let fileOk = 0;
      let fileFail = 0;
      for (let i = 0; i < f.validRows.length; i += BATCH_CSV) {
        const chunk = f.validRows.slice(i, i + BATCH_CSV);
        uploadProgress.textContent = `${f.name}: batch ${Math.floor(i / BATCH_CSV) + 1}/${batchTotal}…`;
        try {
          await insertBanyakBaris(jenis.table, chunk);
          fileOk += chunk.length;
        } catch (err) {
          fileFail += chunk.length;
          f.errorMsg = err.message;
          break;
        }
      }
      f.ok = fileOk;
      f.fail = fileFail;
      f.status = fileFail > 0 ? 'gagal' : 'sukses';
      totalOk += fileOk;
      totalFail += fileFail;
      renderUploadList();
    }
    uploadProgress.textContent = '';
    btnUpload.disabled = false;
    showToast(
      totalFail === 0
        ? `Upload selesai: ${totalOk.toLocaleString('id-ID')} baris tersimpan ✓`
        : `Upload selesai: ${totalOk.toLocaleString('id-ID')} tersimpan, ${totalFail.toLocaleString('id-ID')} gagal.`,
      totalFail === 0 ? 'success' : 'error'
    );
  });

  btnClearUpload.addEventListener('click', () => {
    uploadFiles = [];
    renderUploadList();
    btnUpload.disabled = false;
  });

  setJenis('guru');
  inputNamaFokus();
}
