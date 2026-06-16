<?php

use App\Models\Presensi;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Services\HijriCalendarService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// 1. Auto-Alfa Trigger - Sesi Pagi (08:01 Daily)
Schedule::call(function () {
    $today = now();
    $hijriService = app(HijriCalendarService::class);

    // Check if session is locked
    if (! $hijriService->isValidAttendanceDay($today, 'pagi')) {
        return;
    }

    $hijri = $hijriService->convertToHijri($today);
    $activeSantriIds = Santri::where('status', 'aktif')->pluck('id');

    foreach ($activeSantriIds as $santriId) {
        Presensi::firstOrCreate([
            'santri_id' => $santriId,
            'tanggal_masehi' => $today->toDateString(),
            'sesi' => 'pagi',
        ], [
            'kelas_id' => Santri::find($santriId)?->kelas_id,
            'bulan_hijri' => $hijri['month'],
            'tahun_hijri' => $hijri['year'],
            'status' => 'alfa',
            'catatan' => 'Auto-Alfa Sesi Pagi oleh Sistem',
        ]);
    }
})->dailyAt('08:01')->name('auto-alfa-pagi');

// 2. Auto-Alfa Trigger - Sesi Malam (22:01 Daily)
Schedule::call(function () {
    $today = now();
    $hijriService = app(HijriCalendarService::class);

    // Check if session is locked
    if (! $hijriService->isValidAttendanceDay($today, 'malam')) {
        return;
    }

    $hijri = $hijriService->convertToHijri($today);
    $activeSantriIds = Santri::where('status', 'aktif')->pluck('id');

    foreach ($activeSantriIds as $santriId) {
        Presensi::firstOrCreate([
            'santri_id' => $santriId,
            'tanggal_masehi' => $today->toDateString(),
            'sesi' => 'malam',
        ], [
            'kelas_id' => Santri::find($santriId)?->kelas_id,
            'bulan_hijri' => $hijri['month'],
            'tahun_hijri' => $hijri['year'],
            'status' => 'alfa',
            'catatan' => 'Auto-Alfa Sesi Malam oleh Sistem',
        ]);
    }
})->dailyAt('22:01')->name('auto-alfa-malam');

// 3. Generate New Financial Sheets (Daily check, runs at 00:05)
// Check if Hijri date is 1 Dzulqa'dah (Month 11, Day 1)
Schedule::call(function () {
    $hijriService = app(HijriCalendarService::class);
    $hijri = $hijriService->today();

    if ($hijri['day'] == 1 && $hijri['month'] == 11) {
        $aktifTA = TahunAjaran::getAktif();
        if ($aktifTA) {
            Artisan::call('tagihan:generate', ['tahun_ajaran_id' => $aktifTA->id]);
        }
    }
})->dailyAt('00:05')->name('generate-new-year-billing');

// 4. Update Harian Status Nonaktif Ke Keuangan (Runs at 00:01 Daily)
Schedule::call(function () {
    $aktifTA = TahunAjaran::getAktif();
    if ($aktifTA) {
        $hijriService = app(HijriCalendarService::class);
        $currentHijri = $hijriService->today();
        $currentMonth = (int) $currentHijri['month'];

        // Find all nonactive students
        $nonactiveSantriIds = Santri::where('status', 'nonaktif')->pluck('id');

        Tagihan::whereIn('santri_id', $nonactiveSantriIds)
            ->where('tahun_ajaran_id', $aktifTA->id)
            ->where('kategori', 'majeg_makan')
            ->where('bulan_hijri', '>=', $currentMonth)
            ->where('status', '!=', 'lunas')
            ->update([
                'status' => 'pulang',
                'catatan' => 'Pembaruan otomatis status nonaktif harian',
            ]);
    }
})->dailyAt('00:01')->name('update-nonaktif-billing');
