/* =====================================================================
 * WARNA PER WILAYAH — dipakai bersama Home & Peta agar konsisten.
 * Logika getColorForKabupaten persis sama dengan implementasi awal
 * (FR-3.2: warna berbeda per wilayah, dipertahankan — tidak diubah).
 * ===================================================================== */

export const CATEGORY_COLORS = [
  'rgba(52, 152, 219, 0.6)',
  'rgba(231, 76, 60, 0.6)',
  'rgba(46, 204, 113, 0.6)',
  'rgba(155, 89, 182, 0.6)',
  'rgba(241, 196, 15, 0.6)',
  'rgba(230, 126, 34, 0.6)',
  'rgba(52, 73, 94, 0.6)',
  'rgba(26, 188, 156, 0.6)',
  'rgba(142, 68, 173, 0.6)',
  'rgba(22, 160, 133, 0.6)',
  'rgba(192, 57, 43, 0.6)',
  'rgba(44, 62, 80, 0.6)',
];

export function getColorForKabupaten(name) {
  if (!name) return 'rgba(200, 200, 200, 0.3)';
  let hash = 0;
  for (let i = 0; i < name.length; i += 1) {
    hash = (hash * 31 + name.charCodeAt(i)) % CATEGORY_COLORS.length;
  }
  return CATEGORY_COLORS[hash];
}
