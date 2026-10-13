<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Kegiatan;
use App\Models\Maestro;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruUtamaSeeder extends Seeder
{
    /**
     * Seed contoh Guru Utama beserta data akun login, kegiatan, guru terimbas, dan siswa terimbas.
     */
    public function run(): void
    {
        // 1. Akun & Profil Guru Utama: Siak
        $userSiak = User::firstOrCreate(
            ['email' => 'guru.utama@beringin.id'],
            [
                'name' => 'Raja Zulkarnain, S.Pd., M.Pd.',
                'role' => 'guru_utama',
                'password' => Hash::make('password123'),
            ]
        );

        $guruUtamaSiak = GuruUtama::firstOrCreate(
            ['user_id' => $userSiak->id],
            [
                'nama' => 'Raja Zulkarnain, S.Pd., M.Pd.',
                'asal_sekolah' => 'SMAN 1 Siak',
                'kabupaten' => 'Siak',
                'nuptk' => '198205122005011003',
                'nip' => '198205122005011003',
                'no_hp' => '081365112233',
            ]
        );

        // 2. Akun & Profil Guru Utama: Pekanbaru
        $userPku = User::firstOrCreate(
            ['email' => 'guru.pekanbaru@beringin.id'],
            [
                'name' => 'Dra. Hj. Nurhasanah, M.Pd.',
                'role' => 'guru_utama',
                'password' => Hash::make('password123'),
            ]
        );

        $guruUtamaPku = GuruUtama::firstOrCreate(
            ['user_id' => $userPku->id],
            [
                'nama' => 'Dra. Hj. Nurhasanah, M.Pd.',
                'asal_sekolah' => 'SMAN 1 Pekanbaru',
                'kabupaten' => 'Pekanbaru',
                'nuptk' => '197508152000032001',
                'nip' => '197508152000032001',
                'no_hp' => '081276543210',
            ]
        );

        // 3. Maestro Pendamping
        $maestro = Maestro::firstOrCreate(
            ['nama' => 'Tenas Effendy'],
            [
                'bidang_seni' => 'Tunjuk Ajar & Sastra Melayu',
                'kabupaten' => 'Pelalawan',
                'kontak' => '081275001122',
                'deskripsi' => 'Budayawan besar Riau perumus Butir-butir Tunjuk Ajar Melayu.',
            ]
        );

        // 4. Kegiatan Pengimbasan
        $kegiatan = Kegiatan::firstOrCreate(
            ['nama_kegiatan' => 'Workshop Revitalisasi Bahasa & Pantun Melayu 2026'],
            [
                'deskripsi' => 'Pelatihan berjenjang pengimbasan bahasa Melayu Riau tingkat Kabupaten Siak.',
                'kabupaten' => 'Siak',
                'tahun' => 2026,
                'tempat' => 'Aula Balai Kerapatan Tinggi Siak',
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addDays(3)->toDateString(),
                'status' => 'berlangsung',
            ]
        );

        $kegiatan->maestros()->syncWithoutDetaching([$maestro->id]);
        $kegiatan->guruUtamas()->syncWithoutDetaching([$guruUtamaSiak->id, $guruUtamaPku->id]);

        // 5. Contoh Data Guru yang Diinputkan oleh Guru Utama Siak
        $daftarGuru = [
            [
                'nama_guru' => 'Siti Aminah, S.Pd.',
                'asal_sekolah' => 'SMPN 2 Siak',
                'kabupaten' => 'Siak',
                'nuptk' => '2345678901234567',
                'kelurahan' => 'Kampung Dalam',
            ],
            [
                'nama_guru' => 'Tengku Arifin, M.Pd.',
                'asal_sekolah' => 'SMAN 2 Mempura',
                'kabupaten' => 'Siak',
                'nuptk' => '3456789012345678',
                'kelurahan' => 'Mempura',
            ],
            [
                'nama_guru' => 'Bambang Irawan, S.Pd.I',
                'asal_sekolah' => 'SMPN 1 Sungai Apit',
                'kabupaten' => 'Siak',
                'nuptk' => '4567890123456789',
                'kelurahan' => 'Sungai Apit',
            ],
        ];

        foreach ($daftarGuru as $g) {
            Guru::firstOrCreate(
                [
                    'nama_guru' => $g['nama_guru'],
                    'guru_utama_id' => $guruUtamaSiak->id,
                ],
                [
                    'asal_sekolah' => $g['asal_sekolah'],
                    'kabupaten' => $g['kabupaten'],
                    'nuptk' => $g['nuptk'],
                    'kelurahan' => $g['kelurahan'],
                    'provinsi' => 'Riau',
                    'nama_guru_utama' => $guruUtamaSiak->nama,
                    'kegiatan_id' => $kegiatan->id,
                ]
            );
        }

        // 6. Contoh Data Siswa yang Diinputkan oleh Guru Utama Siak
        $daftarSiswa = [
            [
                'nama_siswa' => 'Ahmad Raihan Pratama',
                'sekolah' => 'SMAN 1 Siak',
                'kabupaten' => 'Siak',
                'nis' => '20261001',
                'alamat_sekolah' => 'Jl. Raja Kecik No. 1, Siak',
            ],
            [
                'nama_siswa' => 'Putri Balqis Salsabila',
                'sekolah' => 'SMAN 1 Siak',
                'kabupaten' => 'Siak',
                'nis' => '20261002',
                'alamat_sekolah' => 'Jl. Raja Kecik No. 1, Siak',
            ],
            [
                'nama_siswa' => 'Muhammad Zaky Al-Faris',
                'sekolah' => 'SMPN 2 Siak',
                'kabupaten' => 'Siak',
                'nis' => '20261003',
                'alamat_sekolah' => 'Jl. Sutomo No. 12, Siak',
            ],
        ];

        foreach ($daftarSiswa as $s) {
            Siswa::firstOrCreate(
                [
                    'nama_siswa' => $s['nama_siswa'],
                    'guru_utama_id' => $guruUtamaSiak->id,
                ],
                [
                    'sekolah' => $s['sekolah'],
                    'kabupaten' => $s['kabupaten'],
                    'nis' => $s['nis'],
                    'alamat_sekolah' => $s['alamat_sekolah'],
                    'nama_guru_utama' => $guruUtamaSiak->nama,
                    'kegiatan_id' => $kegiatan->id,
                ]
            );
        }
    }
}
