/* =====================================================================
 * HALAMAN FORM — Input Data (FR-4)
 *  - Pilihan jenis data: Guru / Siswa
 *  - Field dibangun dinamis dari daftar kolom di config.js (FR-5.3),
 *    sehingga form selalu cocok dengan skema tabel di Supabase.
 *  - Dropdown kabupaten/kota diambil dari geojson (konsisten dengan peta)
 *  - Validasi dasar (field wajib + kabupaten harus dari daftar valid)
 *  - POST ke Supabase memakai struktur API key yang sama (FR-5.2)
 *  - Notifikasi sukses + form ter-reset setelah submit (FR-4.6)
 * ===================================================================== */

import { TABEL_GURU, TABEL_SISWA, KOLOM_GURU, KOLOM_SISWA } from '../config.js';
import { insertBaris } from '../api.js';
import { getKabupatenList, escapeHtml, showToast } from '../ui.js';

// Kolom yang wajib diisi per jenis data.
const REQUIRED = {
  guru: new Set(['nama_guru', 'kabupaten', 'asal_sekolah']),
  siswa: new Set(['nama', 'kabupaten', 'sekolah']),
};

// Metadata tampilan & aturan per kolom (key = nama kolom di Supabase).
const FIELD_META = {
  nama_guru: { label: 'Nama Guru', placeholder: 'Nama lengkap guru', autocomplete: 'name' },
  nuptk: { label: 'NUPTK', placeholder: 'Nomor NUPTK (opsional)', inputType: 'number', numeric: true },
  asal_sekolah: { label: 'Asal Sekolah', placeholder: 'Nama sekolah / instansi', autocomplete: 'organization' },
  kelurahan: { label: 'Kelurahan / Desa', placeholder: 'Kelurahan/desa tempat mengajar (opsional)' },
  kabupaten: { label: 'Kabupaten / Kota', type: 'select' },
  provinsi: { label: 'Provinsi', placeholder: 'Provinsi (opsional, contoh: Riau)' },
  nama_guru_utama: { label: 'Nama Guru Utama', placeholder: 'Nama guru utama / penanggung jawab (opsional)' },
  nama: { label: 'Nama', placeholder: 'Nama lengkap siswa', autocomplete: 'name' },
  sekolah: { label: 'Nama Sekolah', placeholder: 'Nama sekolah / instansi', autocomplete: 'organization' },
  jenjang: { label: 'Jenjang / Kelas', placeholder: 'Contoh: SD, SMP, SMA, SMK, kelas 5' },
};

const JENIS = {
  guru: { table: TABEL_GURU, label: 'guru', columns: KOLOM_GURU },
  siswa: { table: TABEL_SISWA, label: 'siswa', columns: KOLOM_SISWA },
};

let kabupatenValid = [];
let jenisAktif = 'guru';

export function renderForm(container) {
  container.innerHTML = `
    <div class="page-form">
      <a class="back-link" href="/" data-route="/">← Kembali ke Beranda</a>
      <div class="form-card">
        <h2>Input Data</h2>
        <p class="form-desc">
          Simpan data guru atau siswa ke peta. Data baru akan tampil di peta
          secara otomatis dalam beberapa detik tanpa reload manual.
        </p>

        <div class="segmented" role="tablist" aria-label="Jenis data">
          <button type="button" class="segmented-btn active" data-jenis="guru" role="tab" aria-selected="true">Guru</button>
          <button type="button" class="segmented-btn" data-jenis="siswa" role="tab" aria-selected="false">Siswa</button>
        </div>

        <form id="data-form" novalidate>
          <div id="form-fields"></div>
          <button type="submit" class="btn btn-primary form-submit" id="btn-submit">Simpan Data</button>
          <p class="form-note">
            Data yang diisi akan tercatat pada tabel
            <b id="note-table">guru</b> di Supabase dan langsung terhitung di peta.
          </p>
        </form>
      </div>
    </div>
  `;

  const form = document.getElementById('data-form');
  const btnSubmit = document.getElementById('btn-submit');
  const fieldsContainer = document.getElementById('form-fields');

  /* ------- Daftar kabupaten dari geojson (FR-4.2, FR-7) ------- */
  getKabupatenList().then((names) => {
    kabupatenValid = names;
    fillKabupatenOptions();
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

  /* ------- Render field sesuai jenis (Guru / Siswa) ------- */
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

    // maxlength tidak berlaku untuk input angka, jadi hanya untuk type text.
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
    // Hapus status error lama & pasang pembersih error saat diketik.
    form.querySelectorAll('input, select').forEach((input) => {
      const handler = () => input.closest('.form-field')?.classList.remove('invalid');
      input.addEventListener('input', handler);
      input.addEventListener('change', handler);
    });
    fillKabupatenOptions();
  }

  /* ------- Pilihan jenis data: Guru / Siswa (FR-4.1) ------- */
  function setJenis(jenis) {
    jenisAktif = jenis;
    document.querySelectorAll('.segmented-btn').forEach((b) => {
      const active = b.dataset.jenis === jenis;
      b.classList.toggle('active', active);
      b.setAttribute('aria-selected', String(active));
    });
    renderFields(jenis);
    document.getElementById('note-table').textContent = JENIS[jenis].label;
  }

  document.querySelectorAll('.segmented-btn').forEach((b) => {
    b.addEventListener('click', () => setJenis(b.dataset.jenis));
  });

  /* ------- Validasi dasar (FR-4.3) ------- */
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

  /* ------- Submit: POST ke Supabase (FR-4.4) ------- */
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validateForm()) {
      const firstInvalid = form.querySelector('.form-field.invalid input, .form-field.invalid select');
      firstInvalid?.focus();
      return;
    }

    const jenis = JENIS[jenisAktif];
    // Hanya kolom yang diisi yang dikirim; NUPTK dikonversi ke angka.
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
      showToast(`Data ${jenis.label} berhasil disimpan ke peta ✓`);
      form.reset();
      setJenis('guru');
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

  setJenis('guru');
  inputNamaFokus();
}
