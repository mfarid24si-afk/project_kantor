<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama (Beranda).
     */
    public function index(): View
    {
        return view('home', [
            'title' => 'Peta Guru & Siswa — Provinsi Riau',
        ]);
    }
}
