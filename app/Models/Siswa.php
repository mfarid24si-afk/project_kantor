<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nama_siswa',
        'kabupaten',
        'sekolah',
        'nis',
        'alamat_sekolah',
        'guru_utama_id',
        'nama_guru_utama',
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
