<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Presensi;
use App\Models\Santri;
use App\Models\Setting;
use App\Models\Keuangan;
use App\Models\ActivityLog;
use App\Helpers\HijriHelper;

// 1. Auto-Alfa Trigger - Sesi Pagi (08:01 Daily)
Schedule::call(function () {
    $today = date('Y-m-d');
    $sesi = 'pagi';

    // Check if session is locked due to global holidays or manual exceptions
    if (HijriHelper::isSessionLocked($today, $sesi)) {
        ActivityLog::log("Scheduler Auto-Alfa Dilewati", "Sesi pagi hari ini ({$today}) terdeteksi libur/pengecualian.");
        return;
    }

    $hijri = HijriHelper::gregorianToHijri($today);
    $currentYear = (int) Setting::getByKey('current_tahun_ajaran', 1447);
    $activeSantriIds = Santri::where('status', 'aktif')->pluck('id');

    $updatedCount = 0;
    foreach ($activeSantriIds as $santriId) {
        $presensi = Presensi::firstOrCreate([
            'santri_id' => $santriId,
            'tanggal_gregorian' => $today,
            'sesi' => $sesi
        ], [
            'tanggal_hijriah' => $hijri['formatted'],
            'bulan_hijriah' => $hijri['month'],
            'tahun_hijriah' => $currentYear,
            'status' => null,
            'catatan_setoran' => null
        ]);

        if ($presensi->status === null) {
            $presensi->update(['status' => 'alfa']);
            $updatedCount++;
        }
    }

    if ($updatedCount > 0) {
        ActivityLog::log("Scheduler Auto-Alfa Sukses", "Berhasil mengubah {$updatedCount} presensi kosong menjadi Alfa pada Sesi Pagi.");
    }
})->dailyAt('08:01')->name('auto-alfa-pagi');

// 2. Auto-Alfa Trigger - Sesi Malam (22:01 Daily)
Schedule::call(function () {
    $today = date('Y-m-d');
    $sesi = 'malam';

    if (HijriHelper::isSessionLocked($today, $sesi)) {
        ActivityLog::log("Scheduler Auto-Alfa Dilewati", "Sesi malam hari ini ({$today}) terdeteksi libur/pengecualian.");
        return;
    }

    $hijri = HijriHelper::gregorianToHijri($today);
    $currentYear = (int) Setting::getByKey('current_tahun_ajaran', 1447);
    $activeSantriIds = Santri::where('status', 'aktif')->pluck('id');

    $updatedCount = 0;
    foreach ($activeSantriIds as $santriId) {
        $presensi = Presensi::firstOrCreate([
            'santri_id' => $santriId,
            'tanggal_gregorian' => $today,
            'sesi' => $sesi
        ], [
            'tanggal_hijriah' => $hijri['formatted'],
            'bulan_hijriah' => $hijri['month'],
            'tahun_hijriah' => $currentYear,
            'status' => null,
            'catatan_setoran' => null
        ]);

        if ($presensi->status === null) {
            $presensi->update(['status' => 'alfa']);
            $updatedCount++;
        }
    }

    if ($updatedCount > 0) {
        ActivityLog::log("Scheduler Auto-Alfa Sukses", "Berhasil mengubah {$updatedCount} presensi kosong menjadi Alfa pada Sesi Malam.");
    }
})->dailyAt('22:01')->name('auto-alfa-malam');

// 3. Generate New Financial Sheets (Daily check, runs at 00:05)
// Check if Hijri date is 1 Dzulqa'dah (Month 11, Day 1)
Schedule::call(function () {
    $today = date('Y-m-d');
    $hijri = HijriHelper::gregorianToHijri($today);

    if ($hijri['day'] === 1 && $hijri['month'] === 11) {
        $newYear = $hijri['year'];
        Setting::setByKey('current_tahun_ajaran', (string) $newYear);
        
        $activeSantris = Santri::where('status', 'aktif')->get();
        $generatedCount = 0;

        $categories = [
            'daftar_ulang',
            'syahriah_dzulqadah',
            'syahriah_semester_1',
            'syahriah_semester_2',
        ];
        for ($m = 1; $m <= 10; $m++) {
            $categories[] = "majeg_makan_$m";
        }

        foreach ($activeSantris as $santri) {
            foreach ($categories as $cat) {
                $created = Keuangan::firstOrCreate([
                    'santri_id' => $santri->id,
                    'tahun_ajaran' => $newYear,
                    'kategori' => $cat
                ], [
                    'status' => 'belum_bayar',
                    'nominal_bayar' => 0,
                    'catatan' => null
                ]);
                
                if ($created->wasRecentlyCreated) {
                    $generatedCount++;
                }
            }
        }

        ActivityLog::log(
            "Inisiasi Tahun Ajaran Baru", 
            "Berhasil men-generate {$generatedCount} baris lembar tagihan baru untuk Tahun Ajaran {$newYear} H."
        );
    }
})->dailyAt('00:05')->name('generate-new-year-billing');
