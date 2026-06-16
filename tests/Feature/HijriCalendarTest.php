<?php

namespace Tests\Feature;

use App\Models\LiburMassal;
use App\Models\TahunAjaran;
use App\Services\HijriCalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HijriCalendarTest extends TestCase
{
    use RefreshDatabase;

    private $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create([
            'nama' => '1447H',
            'tahun_hijri' => 1447,
            'is_aktif' => true,
            'koreksi_hilal' => 0,
        ]);
    }

    public function test_gregorian_to_hijri_conversion()
    {
        $service = app(HijriCalendarService::class);
        $result = $service->convertToHijri(Carbon::parse('2026-06-11'));

        $this->assertEquals(25, $result['day']);
        $this->assertEquals(12, $result['month']); // Dzulhijjah
        $this->assertEquals('Dzulhijjah', $result['month_name']);
        $this->assertEquals(1447, $result['year']);
    }

    public function test_hilal_correction_mutations()
    {
        $service = app(HijriCalendarService::class);

        // Test +1 day
        $this->ta->update(['koreksi_hilal' => 1]);
        $resultPlus = $service->convertToHijri(Carbon::parse('2026-06-11'));
        $this->assertEquals(26, $resultPlus['day']);

        // Test -1 day
        $this->ta->update(['koreksi_hilal' => -1]);
        $resultMinus = $service->convertToHijri(Carbon::parse('2026-06-11'));
        $this->assertEquals(24, $resultMinus['day']);
    }

    public function test_weekly_locking_rules()
    {
        $service = app(HijriCalendarService::class);

        // 2026-06-11 is Thursday. Thursday Malam is locked.
        $this->assertFalse($service->isValidAttendanceDay(Carbon::parse('2026-06-11'), 'malam'));

        // Thursday Pagi is not locked.
        $this->assertTrue($service->isValidAttendanceDay(Carbon::parse('2026-06-11'), 'pagi'));

        // 2026-06-12 is Friday. Friday Pagi is locked.
        $this->assertFalse($service->isValidAttendanceDay(Carbon::parse('2026-06-12'), 'pagi'));

        // Friday Malam is not locked.
        $this->assertTrue($service->isValidAttendanceDay(Carbon::parse('2026-06-12'), 'malam'));

        // 2026-06-14 is Sunday. Sunday is not locked.
        $this->assertTrue($service->isValidAttendanceDay(Carbon::parse('2026-06-14'), 'pagi'));
        $this->assertTrue($service->isValidAttendanceDay(Carbon::parse('2026-06-14'), 'malam'));
    }

    public function test_manual_holiday_exceptions()
    {
        $service = app(HijriCalendarService::class);

        // Create a manual holiday: 2026-06-15 to 2026-06-17
        LiburMassal::create([
            'tahun_ajaran_id' => $this->ta->id,
            'nama_libur' => 'Libur Semester',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-17',
        ]);

        $this->assertFalse($service->isValidAttendanceDay(Carbon::parse('2026-06-16'), 'pagi'));
        $this->assertFalse($service->isValidAttendanceDay(Carbon::parse('2026-06-16'), 'malam'));

        // 2026-06-14 is before holiday, so not locked
        $this->assertTrue($service->isValidAttendanceDay(Carbon::parse('2026-06-14'), 'pagi'));
    }
}
