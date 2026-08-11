/* =====================================================================
 * HALAMAN FORM — Input Data (FR-4)
 *  - Pilihan jenis data: Guru / Siswa
 *  - Dropdown kabupaten/kota diambil dari geojson (konsisten dengan peta)
 *  - Validasi dasar (field wajib + kabupaten harus dari daftar valid)
 *  - POST ke Supabase memakai struktur API key yang sama (FR-5.2)
 *  - Notifikasi sukses + form ter-reset setelah submit (FR-4.6)
 * ===================================================================== */

import {
  TABEL_GURU,
  TABEL_SISWA,
  KOLOM_KABUPATEN,
  KOLOM_NAMA,
  KOLOM_SEKOLAH,
  KOLOM_JENJANG,
} from '../config.js';
import { insertBaris } from '../api.js';
import { getKabupatenList, escapeHtml, showToast } from '../ui.js';

let kabupatenValid = [];
let jenisAktif = 'guru';

export function renderForm(container) {
  container.innerHTML = `
    <div class="page-form">
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
          <div class="form-field" data-field="nama">
            <label for="input-nama">Nama <span class="req" aria-hidden="true">*</span></label>
            <input id="input-nama" type="text" maxlength="120" placeholder="Nama lengkap" autocomplete="name" />
            <span class="error-msg">Nama wajib diisi.</span>
          </div>

          <div class="form-field" data-field="kabupaten">
            <label for="input-kabupaten">Kabupaten/Kota <span class="req" aria-hidden="true">*</span></label>
            <select id="input-kabupaten">
              <option value="">— Pilih kabupaten/kota —</option>
            </select>
            <span class="error-msg">Pilih kabupaten/kota dari daftar.</span>
          </div>

          <div class="form-field" data-field="sekolah">
            <label for="input-sekolah">Nama Sekolah <span class="req" aria-hidden="true">*</span></label>
            <input id="input-sekolah" type="text" maxlength="160" placeholder="Nama sekolah / instansi" autocomplete="organization" />
            <span class="error-msg">Nama sekolah wajib diisi.</span>
          </div>

          <div class="form-field" data-field="jenjang" id="field-jenjang" hidden>
            <label for="input-jenjang">Jenjang / Kelas <span class="opt" aria-hidden="true">(opsional)</span></label>
            <input id="input-jenjang" type="text" maxlength="60" placeholder="Contoh: SD, SMP, SMA, SMK, kelas 5" />
          </div>

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
  const fieldJenjang = document.getElementById('field-jenjang');
  const inputNama = document.getElementById('input-nama');
  const inputKabupaten = document.getElementById('input-kabupaten');
  const inputSekolah = document.getElementById('input-sekolah');
  const inputJenjang = document.getElementById('input-jenjang');

  /* ------- Dropdown kabupaten dari geojson (FR-4.2, FR-7) ------- */
  getKabupatenList().then((names) => {
    kabupatenValid = names;
    inputKabupaten.innerHTML =
      '<option value="">— Pilih kabupaten/kota —</option>' +
      names.map((n) => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`).join('');
  });

  /* ------- Pilihan jenis data: Guru / Siswa (FR-4.1) ------- */
  function setJenis(jenis) {
    jenisAktif = jenis;
    document.querySelectorAll('.segmented-btn').forEach((b) => {
      const active = b.dataset.jenis === jenis;
      b.classList.toggle('active', active);
      b.setAttribute('aria-selected', String(active));
    });
    fieldJenjang.hidden = jenis !== 'siswa';
    document.getElementById('note-table').textContent = jenis === 'guru' ? 'guru' : 'siswa';
  }

  document.querySelectorAll('.segmented-btn').forEach((b) => {
    b.addEventListener('click', () => setJenis(b.dataset.jenis));
  });

  /* ------- Validasi dasar (FR-4.3) ------- */
  function validateField(fieldEl, value, validList) {
    const empty = !String(value || '').trim();
    const notInList = Array.isArray(validList) && validList.length > 0 && !validList.includes(String(value).trim());
    const ok = !empty && !notInList;
    fieldEl.classList.toggle('invalid', !ok);
    return ok;
  }

  // Hapus status error begitu pengguna memperbaiki isian.
  form.querySelectorAll('input, select').forEach((input) => {
    const handler = () => input.closest('.form-field')?.classList.remove('invalid');
    input.addEventListener('input', handler);
    input.addEventListener('change', handler);
  });

  /* ------- Submit: POST ke Supabase (FR-4.4) ------- */
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const fieldNama = form.querySelector('[data-field="nama"]');
    const fieldKabupaten = form.querySelector('[data-field="kabupaten"]');
    const fieldSekolah = form.querySelector('[data-field="sekolah"]');

    const okNama = validateField(fieldNama, inputNama.value);
    const okKabupaten = validateField(fieldKabupaten, inputKabupaten.value, kabupatenValid);
    const okSekolah = validateField(fieldSekolah, inputSekolah.value);

    if (!okNama || !okKabupaten || !okSekolah) {
      const firstInvalid = form.querySelector('.form-field.invalid input, .form-field.invalid select');
      firstInvalid?.focus();
      return;
    }

    const tableName = jenisAktif === 'guru' ? TABEL_GURU : TABEL_SISWA;
    const payload = {
      [KOLOM_NAMA]: inputNama.value.trim(),
      [KOLOM_KABUPATEN]: inputKabupaten.value.trim(),
      [KOLOM_SEKOLAH]: inputSekolah.value.trim(),
    };
    // Kolom opsional: hanya dikirim bila diisi (menghindari error kolom kosong).
    if (jenisAktif === 'siswa' && KOLOM_JENJANG) {
      const jenjang = inputJenjang.value.trim();
      if (jenjang) payload[KOLOM_JENJANG] = jenjang;
    }

    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Menyimpan…';
    try {
      await insertBaris(tableName, payload);
      showToast(`Data ${jenisAktif === 'guru' ? 'guru' : 'siswa'} berhasil disimpan ke peta ✓`);
      form.reset();
      setJenis('guru');
      form.querySelectorAll('.form-field').forEach((f) => f.classList.remove('invalid'));
      inputNama.focus();
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

  setJenis('guru');
  inputNama.focus();
}
