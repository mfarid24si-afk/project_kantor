<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuruUtama extends Model
{
    use HasFactory;

    protected $table = 'guru_utamas';

    protected $fillable = [
        'user_id',
        'nama',
        'nuptk',
        'nip',
        'asal_sekolah',
        'kabupaten',
        'no_hp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kegiatans(): BelongsToMany
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_guru_utama')
            ->withTimestamps();
    }

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class, 'guru_utama_id');
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'guru_utama_id');
    }
}
