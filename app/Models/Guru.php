<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'nama_guru',
        'kabupaten',
        'asal_sekolah',
        'nuptk',
        'kelurahan',
        'provinsi',
        'nama_guru_utama',
    ];
}
