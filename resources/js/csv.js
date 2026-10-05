/* =====================================================================
 * UTILITAS CSV — parsing (upload) & escaping field (ekspor)
 *  - parseCsv: parser RFC 4180 yang toleran (BOM, CRLF, delimiter "," atau ";",
 *    kutip ganda, baris baru di dalam kutip) — dipakai upload massal
 *  - csvField: amankan satu nilai menjadi sel CSV (koma, kutip, baris baru)
 * ===================================================================== */

/** Parse teks CSV menjadi { headers, data } (data = array objek per header). */
export function parseCsv(text) {
  const src = String(text || '')
    .replace(/^\uFEFF/, '')
    .replace(/\r\n/g, '\n')
    .replace(/\r/g, '\n');

  // Deteksi delimiter dari baris pertama (dukungan ekspor Excel regional ";").
  const firstEol = src.indexOf('\n');
  const firstLine = firstEol === -1 ? src : src.slice(0, firstEol);
  const commaCount = (firstLine.match(/,/g) || []).length;
  const semiCount = (firstLine.match(/;/g) || []).length;
  const delim = semiCount > commaCount ? ';' : ',';

  // Tokenizer sederhana RFC 4180.
  const grid = [];
  let field = '';
  let row = [];
  let inQuotes = false;
  for (let i = 0; i < src.length; i += 1) {
    const ch = src[i];
    if (inQuotes) {
      if (ch === '"') {
        if (src[i + 1] === '"') {
          field += '"';
          i += 1;
        } else {
          inQuotes = false;
        }
      } else {
        field += ch;
      }
    } else if (ch === '"' && field === '') {
      // Kutip hanya dianggap pembuka saat berada di awal field (RFC 4180).
      inQuotes = true;
    } else if (ch === delim) {
      row.push(field);
      field = '';
    } else if (ch === '\n') {
      row.push(field);
      grid.push(row);
      field = '';
      row = [];
    } else {
      field += ch;
    }
  }
  if (field !== '' || row.length > 0) {
    row.push(field);
    grid.push(row);
  }

  // Buang baris kosong; baris pertama dijadikan header.
  const rows = grid.filter((r) => !(r.length === 1 && r[0].trim() === ''));
  if (rows.length === 0) return { headers: [], data: [] };

  const headers = rows[0].map((h) => h.trim());
  const data = rows.slice(1).map((r) => {
    const obj = {};
    headers.forEach((h, i) => {
      obj[h] = (r[i] ?? '').trim();
    });
    return obj;
  });
  return { headers, data };
}

/** Keluarkan satu nilai menjadi sel CSV yang aman (koma, kutip, baris baru). */
export function csvField(value) {
  const v = value === null || value === undefined ? '' : String(value);
  return /[",\r\n]/.test(v) ? `"${v.replace(/"/g, '""')}"` : v;
}
