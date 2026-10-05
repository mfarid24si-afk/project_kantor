<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FormController;
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
Route::get('/form', [FormController::class, 'index'])->name('form');
Route::get('/kabupaten', [KabupatenController::class, 'index'])->name('kabupaten.index');
Route::get('/kabupaten/{name}', [KabupatenController::class, 'show'])->name('kabupaten.show');
Route::get('/tentang', [TentangController::class, 'index'])->name('tentang');

/*
|--------------------------------------------------------------------------
| Internal API Routes — Database Lokal MySQL
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/guru/counts', [ApiController::class, 'jumlahGuru'])->name('api.guru.counts');
    Route::get('/siswa/counts', [ApiController::class, 'jumlahSiswa'])->name('api.siswa.counts');
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
