<?php

namespace Tests\Feature;

use App\Helpers\HijriHelper;
use App\Models\Setting;
use App\Models\LiburMassal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HijriCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_gregorian_to_hijri_conversion()
    {
        Setting::setByKey('hilal_correction', '0');

        $result = HijriHelper::gregorianToHijri('2026-06-11');
        $this->assertEquals(25, $result['day']);
        $this->assertEquals(12, $result['month']); // Dzulhijjah
        $this->assertEquals('Dzulhijjah', $result['month_name']);
        $this->assertEquals(1447, $result['year']);
    }

    public function test_hilal_correction_mutations()
    {
        // Test +1 day
        Setting::setByKey('hilal_correction', '1');
        $resultPlus = HijriHelper::gregorianToHijri('2026-06-11');
        $this->assertEquals(26, $resultPlus['day']);

        // Test -1 day
        Setting::setByKey('hilal_correction', '-1');
        $resultMinus = HijriHelper::gregorianToHijri('2026-06-11');
        $this->assertEquals(24, $resultMinus['day']);
    }

    public function test_weekly_locking_rules()
    {
        // 2026-06-11 is Thursday. Thursday Malam is locked.
        $this->assertTrue(HijriHelper::isSessionLocked('2026-06-11', 'malam'));
        
        // Thursday Pagi is not locked.
        $this->assertFalse(HijriHelper::isSessionLocked('2026-06-11', 'pagi'));

        // 2026-06-12 is Friday. Friday Pagi is locked.
        $this->assertTrue(HijriHelper::isSessionLocked('2026-06-12', 'pagi'));
        
        // Friday Malam is not locked.
        $this->assertFalse(HijriHelper::isSessionLocked('2026-06-12', 'malam'));

        // 2026-06-14 is Sunday. Sunday is not locked.
        $this->assertFalse(HijriHelper::isSessionLocked('2026-06-14', 'pagi'));
        $this->assertFalse(HijriHelper::isSessionLocked('2026-06-14', 'malam'));
    }

    public function test_manual_holiday_exceptions()
    {
        // Create a manual holiday: 2026-06-15 to 2026-06-17
        LiburMassal::create([
            'nama_libur' => 'Libur Semester',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-17',
        ]);

        $this->assertTrue(HijriHelper::isSessionLocked('2026-06-16', 'pagi'));
        $this->assertTrue(HijriHelper::isSessionLocked('2026-06-16', 'malam'));
        
        // 2026-06-14 is before holiday, so not locked
        $this->assertFalse(HijriHelper::isSessionLocked('2026-06-14', 'pagi'));
    }
}
