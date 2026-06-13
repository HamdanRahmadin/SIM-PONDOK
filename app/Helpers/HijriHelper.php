<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\LiburMassal;
use DateTime;
use IntlCalendar;

class HijriHelper
{
    /**
     * Convert Gregorian date to Hijri date array
     */
    public static function gregorianToHijri($dateString, ?int $adjustment = null): array
    {
        $date = new DateTime($dateString);
        
        if ($adjustment === null) {
            // Read from DB setting, default to 0
            $adjustment = (int) Setting::getByKey('hilal_correction', 0);
        }

        if ($adjustment !== 0) {
            $date->modify(($adjustment > 0 ? '+' : '') . $adjustment . ' days');
        }

        $intlCal = IntlCalendar::createInstance('UTC', 'id_ID@calendar=islamic-umalqura');
        $intlCal->setTime($date->getTimestamp() * 1000);

        $hijriYear = $intlCal->get(IntlCalendar::FIELD_YEAR);
        $hijriMonth = $intlCal->get(IntlCalendar::FIELD_MONTH) + 1; // 0-indexed in IntlCalendar
        $hijriDay = $intlCal->get(IntlCalendar::FIELD_DAY_OF_MONTH);

        $monthNames = [
            1 => 'Muharram',
            2 => 'Safar',
            3 => 'Rabiul Awwal',
            4 => 'Rabiul Akhir',
            5 => 'Jumadil Awwal',
            6 => 'Jumadil Akhir',
            7 => 'Rajab',
            8 => 'Sya\'ban',
            9 => 'Ramadhan',
            10 => 'Syawal',
            11 => 'Dzulqa\'dah',
            12 => 'Dzulhijjah'
        ];

        $monthName = $monthNames[$hijriMonth] ?? 'Unknown';

        return [
            'year' => $hijriYear,
            'month' => $hijriMonth,
            'month_name' => $monthName,
            'day' => $hijriDay,
            'formatted' => $hijriDay . ' ' . $monthName . ' ' . $hijriYear
        ];
    }

    /**
     * Check if a given session is locked/disabled due to global holidays or exceptions
     */
    public static function isSessionLocked($dateString, string $sesi): bool
    {
        $date = new DateTime($dateString);
        $dayOfWeek = (int) $date->format('w'); // 0 (Sunday) to 6 (Saturday)
        
        // 1. Check Automatic Weekly Exceptions:
        // Kamis Sesi Malam (day 4 = Thursday, sesi = malam)
        if ($dayOfWeek === 4 && $sesi === 'malam') {
            return true;
        }
        // Jum'at Sesi Pagi (day 5 = Friday, sesi = pagi)
        if ($dayOfWeek === 5 && $sesi === 'pagi') {
            return true;
        }

        // 2. Check Hijri Month Exception:
        // Month Syawal (10) is global holiday (system does not process attendance)
        $hijri = self::gregorianToHijri($dateString);
        if ($hijri['month'] === 10) {
            return true;
        }

        // 3. Check Manual Exceptions (Libur Massal)
        $formattedDate = $date->format('Y-m-d');
        $isManualLibur = LiburMassal::where('start_date', '<=', $formattedDate)
            ->where('end_date', '>=', $formattedDate)
            ->exists();

        if ($isManualLibur) {
            return true;
        }

        return false;
    }
}
