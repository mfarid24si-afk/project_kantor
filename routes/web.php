<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\TentangController;

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
