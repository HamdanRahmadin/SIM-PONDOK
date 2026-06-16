<?php

namespace App\Livewire\Ustaz;

use App\Models\Presensi;
use App\Models\PresensiHalaqoh;
use App\Models\Santri;
use App\Services\HijriCalendarService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Dashboard Ustaz - Ribathul Qur'an")]
class Dashboard extends Component
{
    public string $currentSesi = 'pagi';

    public string $currentDate = '';

    public string $formattedHijriDate = '';

    // Dual-session progress (semua kelas)
    public int $totalActiveSantri = 0;

    public int $pagiFilledCount = 0;

    public int $pagiPercent = 0;

    public bool $pagiIsFilled = false;

    public int $malamFilledCount = 0;

    public int $malamPercent = 0;

    public bool $malamIsFilled = false;

    public bool $isSessionLocked = false;

    // Status Halaqoh (Rabu)
    public bool $isHalaqohDay = false;

    public bool $isHalaqohFilled = false;

    public $recentActivities = [];

    public function mount()
    {
        Carbon::setLocale('id');
        $this->currentDate = date('Y-m-d');

        $hour = (int) date('H');
        $this->currentSesi = ($hour >= 5 && $hour < 12) ? 'pagi' : 'malam';

        $hijriService = app(HijriCalendarService::class);
        $hijri = $hijriService->convertToHijri(Carbon::today());
        $this->formattedHijriDate = $hijri['formatted'];

        $this->isSessionLocked = ! $hijriService->isValidAttendanceDay(Carbon::today(), $this->currentSesi);

        // Status Halaqoh
        $this->isHalaqohDay = Carbon::today()->isWednesday();
        if ($this->isHalaqohDay) {
            $this->isHalaqohFilled = PresensiHalaqoh::where('tanggal_masehi', $this->currentDate)->exists();
        }

        $this->calculateProgress();
        $this->loadRecentActivities();
    }

    public function calculateProgress(): void
    {
        // Total santri aktif dari SEMUA kelas
        $this->totalActiveSantri = Santri::where('status', 'aktif')->count();

        if ($this->totalActiveSantri === 0) {
            return;
        }

        // Sesi Pagi
        $this->pagiFilledCount = Presensi::where('tanggal_masehi', $this->currentDate)
            ->where('sesi', 'pagi')
            ->distinct('santri_id')
            ->count('santri_id');
        $this->pagiPercent = min(100, round(($this->pagiFilledCount / $this->totalActiveSantri) * 100));
        $this->pagiIsFilled = $this->pagiFilledCount > 0;

        // Sesi Malam
        $this->malamFilledCount = Presensi::where('tanggal_masehi', $this->currentDate)
            ->where('sesi', 'malam')
            ->distinct('santri_id')
            ->count('santri_id');
        $this->malamPercent = min(100, round(($this->malamFilledCount / $this->totalActiveSantri) * 100));
        $this->malamIsFilled = $this->malamFilledCount > 0;
    }

    public function loadRecentActivities(): void
    {
        // Presensi Setoran terbaru (lintas kelas)
        $latestPresensi = Presensi::orderBy('created_at', 'desc')->first();
        if ($latestPresensi) {
            $this->recentActivities[] = [
                'title' => 'Presensi Setoran',
                'date' => Carbon::parse($latestPresensi->tanggal_masehi)->translatedFormat('d F Y'),
                'desc' => 'Telah Diinput',
                'type' => 'setoran',
            ];
        }

        // Presensi Halaqoh terbaru
        $latestHalaqoh = PresensiHalaqoh::orderBy('created_at', 'desc')->first();
        if ($latestHalaqoh) {
            $this->recentActivities[] = [
                'title' => 'Presensi Halaqoh',
                'date' => Carbon::parse($latestHalaqoh->tanggal_masehi)->translatedFormat('d F Y'),
                'desc' => 'Telah Diinput',
                'type' => 'halaqoh',
            ];
        }
    }

    public function render()
    {
        return view('livewire.ustaz.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Ustaz']);
    }
}
