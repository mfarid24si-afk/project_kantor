<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FormController extends Controller
{
    /**
     * Tampilkan halaman Form Input Data.
     */
    public function index(): View
    {
        return view('form', [
            'title' => 'Form Input — Guru & Siswa Riau',
        ]);
    }
}
