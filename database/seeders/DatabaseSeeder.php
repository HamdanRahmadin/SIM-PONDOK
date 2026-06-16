<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\KonfigurasiKeuangan;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
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
        // 1. Seed Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@pondok.com'],
            [
                'name' => 'Admin Pondok',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $ustaz = User::updateOrCreate(
            ['email' => 'ustaz@pondok.com'],
            [
                'name' => 'Ustaz Ahmad',
                'password' => Hash::make('password'),
                'role' => 'ustaz',
            ]
        );

        $bendahara = User::updateOrCreate(
            ['email' => 'bendahara@pondok.com'],
            [
                'name' => 'Bendahara Syarif',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
            ]
        );

        // 2. Seed Kamar 1 s.d. Kamar 10
        $kamarList = [];
        for ($i = 1; $i <= 10; $i++) {
            $kamarList[] = Kamar::create([
                'nama_kamar' => "Kamar {$i}",
                'keterangan' => "Kamar Asrama santri nomor {$i}",
            ]);
        }

        // 5. Seed Kelas 1 s.d. Kelas 4
        $kelasA = Kelas::create(['nama_kelas' => 'Kelas 1', 'ustaz_id' => $ustaz->id, 'urutan' => 1]);
        $kelasB = Kelas::create(['nama_kelas' => 'Kelas 2', 'ustaz_id' => null, 'urutan' => 2]);
        $kelasC = Kelas::create(['nama_kelas' => 'Kelas 3', 'ustaz_id' => null, 'urutan' => 3]);
        $kelasD = Kelas::create(['nama_kelas' => 'Kelas 4', 'ustaz_id' => null, 'urutan' => 4]);

        $classes = [$kelasA, $kelasB, $kelasC, $kelasD];

        // 6. Seed Tahun Ajaran Aktif 1447H
        $tahunAjaran = TahunAjaran::create([
            'nama' => '1447H',
            'tahun_hijri' => 1447,
            'is_aktif' => true,
            'koreksi_hilal' => 0,
        ]);

        // 7. Seed Konfigurasi Keuangan 1447H
        $financeConfig = KonfigurasiKeuangan::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nominal_daftar_ulang' => 500000,
            'nominal_syahriah_sem1' => 1200000,
            'nominal_syahriah_sem2' => 1000000,
            'nominal_majeg_makan' => 300000,
            'catatan' => 'Konfigurasi biaya pendaftaran dan operasional 1447H',
        ]);

        // 8. Seed 10 Santri Aktif
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
            $kamar = $kamarList[$index % 10];

            $santri = Santri::create([
                'nis' => '14470'.($index + 1),
                'nama_lengkap' => $data['nama_lengkap'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'alamat' => $data['alamat'],
                'status' => 'aktif',
                'kelas_id' => $class->id,
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => now()->subMonths(2),
            ]);

            // 9. Generate 14 Rows of Tagihan per Santri
            // A. Daftar Ulang
            Tagihan::create([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kategori' => 'daftar_ulang',
                'bulan_hijri' => null,
                'tahun_hijri' => 1447,
                'nominal' => $financeConfig->nominal_daftar_ulang,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);

            // B. Syahriah Sem 1
            Tagihan::create([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kategori' => 'syahriah_sem1',
                'bulan_hijri' => null,
                'tahun_hijri' => 1447,
                'nominal' => $financeConfig->nominal_syahriah_sem1,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);

            // C. Syahriah Sem 2
            Tagihan::create([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kategori' => 'syahriah_sem2',
                'bulan_hijri' => null,
                'tahun_hijri' => 1447,
                'nominal' => $financeConfig->nominal_syahriah_sem2,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);

            // D. Majeg Makan (11 active Hijri months)
            $activeMonths = [11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            foreach ($activeMonths as $month) {
                Tagihan::create([
                    'santri_id' => $santri->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kategori' => 'majeg_makan',
                    'bulan_hijri' => $month,
                    'tahun_hijri' => 1447,
                    'nominal' => $financeConfig->nominal_majeg_makan,
                    'status' => 'belum_bayar',
                    'nominal_terbayar' => 0,
                ]);
            }
        }
    }
}
