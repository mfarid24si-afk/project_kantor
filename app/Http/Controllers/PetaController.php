<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PetaController extends Controller
{
    /**
     * Tampilkan halaman Peta Interaktif.
     */
    public function index(): View
    {
        return view('peta', [
            'title' => 'Peta — Guru & Siswa Riau',
        ]);
    }
}
