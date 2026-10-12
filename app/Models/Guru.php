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
        'guru_utama_id',
        'kegiatan_id',
    ];

    public function guruUtama()
    {
        return $this->belongsTo(GuruUtama::class, 'guru_utama_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
