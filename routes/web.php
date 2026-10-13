<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruUtamaPortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\TentangController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Beringin (Peta Sebaran Guru & Siswa Riau)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/peta', [PetaController::class, 'index'])->name('peta');
Route::get('/kabupaten', [KabupatenController::class, 'index'])->name('kabupaten.index');
Route::get('/kabupaten/{name}', [KabupatenController::class, 'show'])->name('kabupaten.show');
Route::get('/tentang', [TentangController::class, 'index'])->name('tentang');

// Redirect /form ke portal guru-utama jika login guru-utama, atau ke login jika publik
Route::get('/form', function () {
    if (auth()->check() && auth()->user()->isGuruUtama()) {
        return redirect()->route('guru_utama.guru.index');
    }

    return redirect()->route('login');
})->name('form');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Super Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.super_admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Maestro
    Route::get('/maestro', [AdminController::class, 'maestroIndex'])->name('maestro.index');
    Route::get('/maestro/tambah', [AdminController::class, 'maestroCreate'])->name('maestro.create');
    Route::post('/maestro', [AdminController::class, 'maestroStore'])->name('maestro.store');
    Route::get('/maestro/{maestro}/edit', [AdminController::class, 'maestroEdit'])->name('maestro.edit');
    Route::put('/maestro/{maestro}', [AdminController::class, 'maestroUpdate'])->name('maestro.update');
    Route::delete('/maestro/{maestro}', [AdminController::class, 'maestroDestroy'])->name('maestro.destroy');

    // Guru Utama
    Route::get('/guru-utama', [AdminController::class, 'guruUtamaIndex'])->name('guru_utama.index');
    Route::get('/guru-utama/tambah', [AdminController::class, 'guruUtamaCreate'])->name('guru_utama.create');
    Route::post('/guru-utama', [AdminController::class, 'guruUtamaStore'])->name('guru_utama.store');
    Route::get('/guru-utama/{guruUtama}/edit', [AdminController::class, 'guruUtamaEdit'])->name('guru_utama.edit');
    Route::put('/guru-utama/{guruUtama}', [AdminController::class, 'guruUtamaUpdate'])->name('guru_utama.update');
    Route::delete('/guru-utama/{guruUtama}', [AdminController::class, 'guruUtamaDestroy'])->name('guru_utama.destroy');

    // Kegiatan
    Route::get('/kegiatan', [AdminController::class, 'kegiatanIndex'])->name('kegiatan.index');
    Route::get('/kegiatan/tambah', [AdminController::class, 'kegiatanCreate'])->name('kegiatan.create');
    Route::post('/kegiatan', [AdminController::class, 'kegiatanStore'])->name('kegiatan.store');
    Route::get('/kegiatan/{kegiatan}', [AdminController::class, 'kegiatanShow'])->name('kegiatan.show');
    Route::get('/kegiatan/{kegiatan}/edit', [AdminController::class, 'kegiatanEdit'])->name('kegiatan.edit');
    Route::put('/kegiatan/{kegiatan}', [AdminController::class, 'kegiatanUpdate'])->name('kegiatan.update');
    Route::delete('/kegiatan/{kegiatan}', [AdminController::class, 'kegiatanDestroy'])->name('kegiatan.destroy');
});

/*
|--------------------------------------------------------------------------
| Guru Utama Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('guru-utama')->name('guru_utama.')->middleware(['auth', 'role.guru_utama'])->group(function () {
    Route::get('/', [GuruUtamaPortalController::class, 'dashboard'])->name('dashboard');

    // Input Guru yang diajarkan
    Route::get('/guru', [GuruUtamaPortalController::class, 'guruIndex'])->name('guru.index');
    Route::post('/guru', [GuruUtamaPortalController::class, 'guruStore'])->name('guru.store');
    Route::get('/guru/{guru}/edit', [GuruUtamaPortalController::class, 'guruEdit'])->name('guru.edit');
    Route::put('/guru/{guru}', [GuruUtamaPortalController::class, 'guruUpdate'])->name('guru.update');
    Route::post('/guru/import', [GuruUtamaPortalController::class, 'guruImport'])->name('guru.import');
    Route::get('/guru/template', [GuruUtamaPortalController::class, 'guruTemplate'])->name('guru.template');
    Route::get('/guru/export', [GuruUtamaPortalController::class, 'guruExport'])->name('guru.export');
    Route::delete('/guru/{guru}', [GuruUtamaPortalController::class, 'guruDestroy'])->name('guru.destroy');

    // Input Murid/Siswa yang diajarkan
    Route::get('/siswa', [GuruUtamaPortalController::class, 'siswaIndex'])->name('siswa.index');
    Route::post('/siswa', [GuruUtamaPortalController::class, 'siswaStore'])->name('siswa.store');
    Route::get('/siswa/{siswa}/edit', [GuruUtamaPortalController::class, 'siswaEdit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [GuruUtamaPortalController::class, 'siswaUpdate'])->name('siswa.update');
    Route::post('/siswa/import', [GuruUtamaPortalController::class, 'siswaImport'])->name('siswa.import');
    Route::get('/siswa/template', [GuruUtamaPortalController::class, 'siswaTemplate'])->name('siswa.template');
    Route::get('/siswa/export', [GuruUtamaPortalController::class, 'siswaExport'])->name('siswa.export');
    Route::delete('/siswa/{siswa}', [GuruUtamaPortalController::class, 'siswaDestroy'])->name('siswa.destroy');
});
/*
|--------------------------------------------------------------------------
| Internal API Routes — Database Lokal MySQL
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/tahun', [ApiController::class, 'getTahunList'])->name('api.tahun');
    Route::get('/guru/counts', [ApiController::class, 'jumlahGuru'])->name('api.guru.counts');
    Route::get('/siswa/counts', [ApiController::class, 'jumlahSiswa'])->name('api.siswa.counts');
    Route::get('/komunitas/counts', [ApiController::class, 'jumlahKomunitas'])->name('api.komunitas.counts');
    Route::get('/umum/counts', [ApiController::class, 'jumlahUmum'])->name('api.umum.counts');
    Route::get('/data/{table}', [ApiController::class, 'getTableData'])->name('api.table.data');
    Route::post('/data/{table}', [ApiController::class, 'insertRow'])->name('api.table.insert');
    Route::post('/data/{table}/batch', [ApiController::class, 'insertBatch'])->name('api.table.batch');
});

/*
|--------------------------------------------------------------------------
| Static GeoJSON Route
|--------------------------------------------------------------------------
*/
Route::get('/geojson/{file}', function (string $file) {
    $path = public_path("geojson/{$file}");
    abort_unless(file_exists($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/geo+json',
    ]);
})->name('geojson.show');
