<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Maestro extends Model
{
    use HasFactory;

    protected $table = 'maestros';

    protected $fillable = [
        'nama',
        'bidang_seni',
        'kabupaten',
        'deskripsi',
        'kontak',
    ];

    public function kegiatans(): BelongsToMany
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_maestro')
            ->withPivot('peran')
            ->withTimestamps();
    }
}
