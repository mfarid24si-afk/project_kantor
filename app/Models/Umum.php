<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umum extends Model
{
    use HasFactory;

    protected $table = 'umum';

    protected $fillable = [
        'nama_umum',
        'pekerjaan',
        'alamat',
    ];
}
