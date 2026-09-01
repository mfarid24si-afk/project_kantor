/* =====================================================================
 * PAGE READY MANAGER
 * Setiap halaman memanggil pageReady() setelah kontennya selesai dimuat.
 * Router akan menunggu sinyal ini sebelum menyembunyikan loader.
 * ===================================================================== */

let resolveReady = null;
let readyPromise = null;

/** Reset state — dipanggil router sebelum render halaman baru. */
export function resetPageReady() {
  readyPromise = new Promise((resolve) => {
    resolveReady = resolve;
  });
}

/** Halaman memanggil ini setelah konten selesai dimuat. */
export function pageReady() {
  if (resolveReady) {
    resolveReady();
    resolveReady = null;
  }
}

/**
 * Return Promise yang resolve ketika halaman siap.
 * Router await ini sebelum hide loader.
 */
export function waitForPageReady() {
  return readyPromise || Promise.resolve();
}
