<?php

namespace App\Services;

use App\Helpers\HijriHelper;
use App\Models\LiburMassal;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class HijriCalendarService
{
    /**
     * Dapatkan offset kalender (koreksi_hilal dari tahun_ajaran aktif)
     */
    public function getHijriOffset(): int
    {
        $aktif = TahunAjaran::getAktif();

        return $aktif ? (int) $aktif->koreksi_hilal : 0;
    }

    /**
     * Konversi tanggal hari ini ke Hijriah
     */
    public function today(): array
    {
        return $this->convertToHijri(Carbon::today());
    }

    /**
     * Konversi dengan mempertimbangkan koreksi_hilal
     */
    public function convertToHijri(Carbon $date): array
    {
        $offset = $this->getHijriOffset();

        return HijriHelper::gregorianToHijri($date->format('Y-m-d'), $offset);
    }

    /**
     * Cek apakah hari ini di dalam range libur massal
     */
    public function isHoliday(Carbon $date): bool
    {
        $formattedDate = $date->format('Y-m-d');

        $aktif = TahunAjaran::getAktif();
        if (! $aktif) {
            return false;
        }

        return LiburMassal::where('tahun_ajaran_id', $aktif->id)
            ->where('start_date', '<=', $formattedDate)
            ->where('end_date', '>=', $formattedDate)
            ->exists();
    }

    /**
     * Cek apakah tanggal tertentu adalah hari presensi valid (tidak dikunci)
     */
    public function isValidAttendanceDay(Carbon $date, string $sesi): bool
    {
        $dayOfWeek = (int) $date->format('w'); // 0 (Sunday) to 6 (Saturday)

        // 1. Pengecualian Mingguan Otomatis:
        // Kamis Sesi Malam (day 4 = Thursday, sesi = malam)
        if ($dayOfWeek === 4 && strtolower($sesi) === 'malam') {
            return false;
        }
        // Jum'at Sesi Pagi (day 5 = Friday, sesi = pagi)
        if ($dayOfWeek === 5 && strtolower($sesi) === 'pagi') {
            return false;
        }

        // 2. Cek bulan Syawal (bulan 10):
        $hijri = $this->convertToHijri($date);
        if ((int) $hijri['month'] === 10) {
            return false;
        }

        // 3. Cek Libur Massal:
        if ($this->isHoliday($date)) {
            return false;
        }

        return true;
    }
}
