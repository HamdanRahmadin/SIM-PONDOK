<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Setting;
use App\Models\Keuangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Roles: admin, ustaz, bendahara)
        User::create([
            'name' => 'Admin Pondok',
            'email' => 'admin@pondok.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Ustaz Ahmad',
            'email' => 'ustaz@pondok.com',
            'password' => Hash::make('password'),
            'role' => 'ustaz',
        ]);

        User::create([
            'name' => 'Bendahara Syarif',
            'email' => 'bendahara@pondok.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
        ]);

        // 2. Seed Settings
        Setting::setByKey('hilal_correction', '0');
        Setting::setByKey('current_tahun_ajaran', '1447');

        // 3. Seed 4 Classes (Kelas A, B, C, D)
        $kelasA = Kelas::create(['nama_kelas' => 'Kelas A']);
        $kelasB = Kelas::create(['nama_kelas' => 'Kelas B']);
        $kelasC = Kelas::create(['nama_kelas' => 'Kelas C']);
        $kelasD = Kelas::create(['nama_kelas' => 'Kelas D']);

        $classes = [$kelasA, $kelasB, $kelasC, $kelasD];

        // 4. Seed Santri Data
        $santriData = [
            ['nama_lengkap' => 'Muhammad Ali', 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '2012-05-10', 'alamat' => 'Jl. Kebagusan No. 12'],
            ['nama_lengkap' => 'Ahmad Fadhil', 'tempat_lahir' => 'Surabaya', 'tanggal_lahir' => '2011-08-22', 'alamat' => 'Jl. Jemursari Gg 4'],
            ['nama_lengkap' => 'Zainuddin', 'tempat_lahir' => 'Gresik', 'tanggal_lahir' => '2012-01-15', 'alamat' => 'Jl. Manyar No. 45'],
            ['nama_lengkap' => 'Fatimah Az-Zahra', 'tempat_lahir' => 'Malang', 'tanggal_lahir' => '2013-03-30', 'alamat' => 'Jl. Dinoyo No. 89'],
            ['nama_lengkap' => 'Aisyah Humaira', 'tempat_lahir' => 'Solo', 'tanggal_lahir' => '2012-11-12', 'alamat' => 'Jl. Slamet Riyadi No. 10'],
            ['nama_lengkap' => 'Hasan Basri', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '2011-04-18', 'alamat' => 'Jl. Dago No. 102'],
            ['nama_lengkap' => 'Husain Kamil', 'tempat_lahir' => 'Semarang', 'tanggal_lahir' => '2010-09-05', 'alamat' => 'Jl. Pandanaran No. 7'],
            ['nama_lengkap' => 'Maimunah', 'tempat_lahir' => 'Cirebon', 'tanggal_lahir' => '2012-07-28', 'alamat' => 'Jl. Siliwangi No. 34'],
            ['nama_lengkap' => 'Yusuf Mansur', 'tempat_lahir' => 'Yogyakarta', 'tanggal_lahir' => '2011-12-01', 'alamat' => 'Jl. Malioboro Gg 2'],
            ['nama_lengkap' => 'Khomsah', 'tempat_lahir' => 'Madura', 'tanggal_lahir' => '2013-06-14', 'alamat' => 'Jl. Bangkalan Raya No. 1'],
        ];

        foreach ($santriData as $index => $data) {
            $class = $classes[$index % 4];
            
            $santri = Santri::create([
                'nama_lengkap' => $data['nama_lengkap'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'alamat' => $data['alamat'],
                'status' => 'aktif',
                'kelas_id' => $class->id,
            ]);

            // 5. Generate Billing Structure for the active Santri for year 1447
            $categories = [
                'daftar_ulang',
                'syahriah_dzulqadah',
                'syahriah_semester_1',
                'syahriah_semester_2',
            ];
            // add 10 months of majeg makan
            for ($m = 1; $m <= 10; $m++) {
                $categories[] = "majeg_makan_$m";
            }

            foreach ($categories as $cat) {
                Keuangan::create([
                    'santri_id' => $santri->id,
                    'tahun_ajaran' => 1447,
                    'kategori' => $cat,
                    'status' => 'belum_bayar',
                    'nominal_bayar' => 0,
                    'catatan' => null
                ]);
            }
        }
    }
}
