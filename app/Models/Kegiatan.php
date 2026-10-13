<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatans';

    protected $fillable = [
        'nama_kegiatan',
        'deskripsi',
        'kabupaten',
        'tahun',
        'tempat',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function maestros(): BelongsToMany
    {
        return $this->belongsToMany(Maestro::class, 'kegiatan_maestro')
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function guruUtamas(): BelongsToMany
    {
        return $this->belongsToMany(GuruUtama::class, 'kegiatan_guru_utama')
            ->withTimestamps();
    }

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class, 'kegiatan_id');
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kegiatan_id');
    }
}
