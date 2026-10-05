<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TentangController extends Controller
{
    /**
     * Tampilkan halaman Tentang Kami.
     */
    public function index(): View
    {
        return view('tentang', [
            'title' => 'Tentang — Guru & Siswa Riau',
        ]);
    }
}
