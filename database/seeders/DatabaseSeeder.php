<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Komunitas;
use App\Models\Siswa;
use App\Models\Umum;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $kabupatens = [
            'Bengkalis',
            'Dumai',
            'Indragiri Hilir',
            'Indragiri Hulu',
            'Kampar',
            'Kuantansingingi',
            'Meranti',
            'Pekanbaru',
            'Pelalawan',
            'Rohil',
            'Rohul',
            'Siak',
        ];

        // Seed data guru untuk 12 kabupaten
        foreach ($kabupatens as $index => $kab) {
            $numGurus = rand(3, 8);
            for ($i = 1; $i <= $numGurus; $i++) {
                Guru::create([
                    'nama_guru' => 'Guru '.($index * 10 + $i).' '.$kab,
                    'kabupaten' => $kab,
                    'asal_sekolah' => 'SMAN '.$i.' '.$kab,
                    'nuptk' => '19'.rand(10000000, 99999999),
                    'kelurahan' => 'Kelurahan '.$kab,
                    'provinsi' => 'Riau',
                    'nama_guru_utama' => 'Koordinator '.$kab,
                ]);
            }
        }

        // Seed data siswa
        $sekolahList = [
            'SMAN 1 Pekanbaru',
            'SMKN 2 Pekanbaru',
            'SMAN 1 Bengkalis',
            'SMAN 1 Dumai',
            'SMAN 2 Bangkinang',
            'SMAN 1 Siak',
            'SMAN 1 Rengat',
            'SMAN 1 Tembilahan',
        ];

        for ($i = 1; $i <= 35; $i++) {
            Siswa::create([
                'nama_siswa' => 'Siswa Melayu '.$i,
                'sekolah' => $sekolahList[array_rand($sekolahList)],
                'nis' => 'NIS'.(2024000 + $i),
                'alamat_sekolah' => 'Jl. Pendidikan No. '.$i.', Provinsi Riau',
            ]);
        }

        // Seed data komunitas
        $komunitasNames = [
            'Komunitas Peduli Bahasa Melayu Riau',
            'Sanggar Seni Pantun Riau',
            'Lembaga Adat Melayu Riau (LAMR)',
            'Forum Guru Bahasa Daerah',
            'Komunitas Gurindam 12',
        ];

        foreach ($komunitasNames as $kIdx => $kName) {
            Komunitas::create([
                'nama_individu' => 'Penggiat '.($kIdx + 1),
                'nama_komunitas' => $kName,
                'alamat_afiliasi' => 'Pekanbaru, Riau',
            ]);
        }

        // Seed data umum
        $umumJobs = ['Pegawai Swasta', 'Wiraswasta', 'Peneliti Budaya', 'Mahasiswa', 'Pensiunan ASN'];
        for ($i = 1; $i <= 10; $i++) {
            Umum::create([
                'nama_umum' => 'Masyarakat Umum '.$i,
                'pekerjaan' => $umumJobs[array_rand($umumJobs)],
                'alamat' => 'Provinsi Riau',
            ]);
        }
    }
}
