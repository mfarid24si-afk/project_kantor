<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Kegiatan;
use App\Models\Komunitas;
use App\Models\Maestro;
use App\Models\Siswa;
use App\Models\Umum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        $sekolahMap = [
            'SMAN 1 Pekanbaru' => 'Pekanbaru',
            'SMKN 2 Pekanbaru' => 'Pekanbaru',
            'SMAN 1 Bengkalis' => 'Bengkalis',
            'SMAN 1 Dumai' => 'Dumai',
            'SMAN 2 Bangkinang' => 'Kampar',
            'SMAN 1 Siak' => 'Siak',
            'SMAN 1 Rengat' => 'Indragiri Hulu',
            'SMAN 1 Tembilahan' => 'Indragiri Hilir',
        ];
        $sekolahList = array_keys($sekolahMap);

        for ($i = 1; $i <= 35; $i++) {
            $sekolah = $sekolahList[array_rand($sekolahList)];
            Siswa::create([
                'nama_siswa' => 'Siswa Melayu '.$i,
                'kabupaten' => $sekolahMap[$sekolah] ?? $kabupatens[array_rand($kabupatens)],
                'sekolah' => $sekolah,
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
            $kab = $kabupatens[$kIdx % count($kabupatens)];
            Komunitas::create([
                'nama_individu' => 'Penggiat '.($kIdx + 1),
                'nama_komunitas' => $kName,
                'kabupaten' => $kab,
                'alamat_afiliasi' => $kab.', Riau',
            ]);
        }

        // Seed data umum
        $umumJobs = ['Pegawai Swasta', 'Wiraswasta', 'Peneliti Budaya', 'Mahasiswa', 'Pensiunan ASN'];
        for ($i = 1; $i <= 10; $i++) {
            $kab = $kabupatens[($i - 1) % count($kabupatens)];
            Umum::create([
                'nama_umum' => 'Masyarakat Umum '.$i,
                'kabupaten' => $kab,
                'pekerjaan' => $umumJobs[array_rand($umumJobs)],
                'alamat' => 'Kabupaten '.$kab.', Provinsi Riau',
            ]);
        }

        // =====================================================================
        // SEED SUPER ADMIN, MAESTRO, GURU UTAMA, & KEGIATAN
        // =====================================================================

        // 1. Super Admin
        User::firstOrCreate(
            ['email' => 'admin@beringin.id'],
            [
                'name' => 'Super Administrator',
                'role' => 'super_admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Maestros
        $maestro1 = Maestro::firstOrCreate(
            ['nama' => 'Tenas Effendy'],
            [
                'bidang_seni' => 'Tunjuk Ajar & Sastra Melayu',
                'kabupaten' => 'Pelalawan',
                'kontak' => '081275001122',
                'deskripsi' => 'Budayawan besar Riau perumus Butir-butir Tunjuk Ajar Melayu.',
            ]
        );

        $maestro2 = Maestro::firstOrCreate(
            ['nama' => 'Idrus Tintin'],
            [
                'bidang_seni' => 'Teater Tradisi & Puisi Melayu',
                'kabupaten' => 'Pekanbaru',
                'kontak' => '081275003344',
                'deskripsi' => 'Pelopor pembaharuan teater dan pembacaan puisi modern bernafas Melayu di Riau.',
            ]
        );

        $maestro3 = Maestro::firstOrCreate(
            ['nama' => 'H. Al azhar'],
            [
                'bidang_seni' => 'Kearifan Tradisi & Bahasa Melayu',
                'kabupaten' => 'Kampar',
                'kontak' => '081275005566',
                'deskripsi' => 'Tokoh adat Lembaga Adat Melayu Riau (LAMR) pengembang kurikulum budaya daerah.',
            ]
        );

        // 3. Guru Utama
        $guruUser = User::firstOrCreate(
            ['email' => 'guru.utama@beringin.id'],
            [
                'name' => 'Raja Zulkarnain, S.Pd., M.Pd.',
                'role' => 'guru_utama',
                'password' => Hash::make('password123'),
            ]
        );

        $guruUtama = GuruUtama::firstOrCreate(
            ['user_id' => $guruUser->id],
            [
                'nama' => 'Raja Zulkarnain, S.Pd., M.Pd.',
                'asal_sekolah' => 'SMAN 1 Siak',
                'kabupaten' => 'Siak',
                'nuptk' => '198205122005011003',
                'nip' => '198205122005011003',
                'no_hp' => '081365112233',
            ]
        );

        // 3b. Guru Utama (bang Noval - Pekanbaru)
        $novalUser = User::firstOrCreate(
            ['email' => 'noval@gmail.com'],
            [
                'name' => 'bang Noval',
                'role' => 'guru_utama',
                'password' => Hash::make('password123'),
            ]
        );

        $guruUtamaNoval = GuruUtama::firstOrCreate(
            ['user_id' => $novalUser->id],
            [
                'nama' => 'bang Noval',
                'asal_sekolah' => 'SMAN 1 Pekanbaru',
                'kabupaten' => 'Pekanbaru',
                'nuptk' => '198801012015011002',
                'nip' => '198801012015011002',
                'no_hp' => '081270001122',
            ]
        );

        // 4. Kegiatan Pengimbasan
        $kegiatan = Kegiatan::firstOrCreate(
            ['nama_kegiatan' => 'Workshop Pengimbasan Tunjuk Ajar & Pantun Melayu 2026'],
            [
                'deskripsi' => 'Program pengimbasan sastra dan tradisi lisan Melayu Riau bersama Maestro dan Guru Utama.',
                'kabupaten' => 'Siak',
                'tempat' => 'Gedung Daerah Sultan Syarif Kasim II, Siak Sri Indrapura',
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addDays(3)->toDateString(),
                'status' => 'berlangsung',
            ]
        );

        // Hubungkan Maestro dan Guru Utama ke Kegiatan
        $kegiatan->maestros()->syncWithoutDetaching([$maestro1->id, $maestro2->id]);
        $kegiatan->guruUtamas()->syncWithoutDetaching([$guruUtama->id]);

        // Hubungkan beberapa guru & siswa ke Guru Utama & Kegiatan ini
        Guru::where('kabupaten', 'Siak')->take(3)->update([
            'guru_utama_id' => $guruUtama->id,
            'nama_guru_utama' => $guruUtama->nama,
            'kegiatan_id' => $kegiatan->id,
        ]);

        Siswa::where('kabupaten', 'Siak')->take(5)->update([
            'guru_utama_id' => $guruUtama->id,
            'nama_guru_utama' => $guruUtama->nama,
            'kegiatan_id' => $kegiatan->id,
        ]);
    }
}
