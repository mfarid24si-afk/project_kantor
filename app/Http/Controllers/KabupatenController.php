<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KabupatenController extends Controller
{
    /**
     * Redirect jika rute /kabupaten diakses tanpa parameter.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('peta');
    }

    /**
     * Tampilkan detail kabupaten berdasarkan nama wilayah.
     */
    public function show(string $name): View
    {
        $decodedName = urldecode($name);

        return view('kabupaten', [
            'kabupatenName' => $decodedName,
            'title' => "Detail {$decodedName} — Guru & Siswa Riau",
        ]);
    }
}
