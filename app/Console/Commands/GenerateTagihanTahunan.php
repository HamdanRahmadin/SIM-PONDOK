<?php

namespace App\Console\Commands;

use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Console\Command;

class GenerateTagihanTahunan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:generate {tahun_ajaran_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 14 baris tagihan tahunan untuk semua santri aktif berdasarkan konfigurasi keuangan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $taId = (int) $this->argument('tahun_ajaran_id');
        $ta = TahunAjaran::find($taId);

        if (! $ta) {
            $this->error("Tahun Ajaran dengan ID {$taId} tidak ditemukan.");

            return 1;
        }

        $config = $ta->konfigurasiKeuangan;
        if (! $config) {
            $this->error("Konfigurasi Keuangan untuk Tahun Ajaran {$ta->nama} belum diatur.");

            return 1;
        }

        $activeSantris = Santri::where('status', 'aktif')->get();
        if ($activeSantris->isEmpty()) {
            $this->info('Tidak ada santri aktif.');

            return 0;
        }

        $this->info('Memulai pembuatan tagihan untuk '.$activeSantris->count().' santri...');

        $duNominal = $config->nominal_daftar_ulang;
        $s1Nominal = $config->nominal_syahriah_sem1;
        $s2Nominal = $config->nominal_syahriah_sem2;
        $mmNominal = $config->nominal_majeg_makan;

        $generatedCount = 0;

        foreach ($activeSantris as $santri) {
            // 1. Daftar Ulang
            $tagihanDu = Tagihan::firstOrCreate([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $ta->id,
                'kategori' => 'daftar_ulang',
                'bulan_hijri' => null,
            ], [
                'tahun_hijri' => $ta->tahun_hijri,
                'nominal' => $duNominal,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);
            if ($tagihanDu->wasRecentlyCreated) {
                $generatedCount++;
            }

            // 2. Syahriah Sem 1
            $tagihanS1 = Tagihan::firstOrCreate([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $ta->id,
                'kategori' => 'syahriah_sem1',
                'bulan_hijri' => null,
            ], [
                'tahun_hijri' => $ta->tahun_hijri,
                'nominal' => $s1Nominal,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);
            if ($tagihanS1->wasRecentlyCreated) {
                $generatedCount++;
            }

            // 3. Syahriah Sem 2
            $tagihanS2 = Tagihan::firstOrCreate([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $ta->id,
                'kategori' => 'syahriah_sem2',
                'bulan_hijri' => null,
            ], [
                'tahun_hijri' => $ta->tahun_hijri,
                'nominal' => $s2Nominal,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);
            if ($tagihanS2->wasRecentlyCreated) {
                $generatedCount++;
            }

            // 4. Majeg Makan Bulanan (11 bulan)
            $activeMonths = [11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            foreach ($activeMonths as $month) {
                $tagihanMm = Tagihan::firstOrCreate([
                    'santri_id' => $santri->id,
                    'tahun_ajaran_id' => $ta->id,
                    'kategori' => 'majeg_makan',
                    'bulan_hijri' => $month,
                ], [
                    'tahun_hijri' => $ta->tahun_hijri,
                    'nominal' => $mmNominal,
                    'status' => 'belum_bayar',
                    'nominal_terbayar' => 0,
                ]);
                if ($tagihanMm->wasRecentlyCreated) {
                    $generatedCount++;
                }
            }
        }

        $this->info("Selesai! Berhasil membuat {$generatedCount} baris tagihan.");

        return 0;
    }
}
