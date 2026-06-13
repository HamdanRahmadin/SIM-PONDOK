<?php

namespace App\Livewire\Ustaz;

use App\Models\Santri;
use App\Models\Presensi;
use App\Models\Hafalan;
use App\Models\Setting;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Title('Ustaz Dashboard')]
class Dashboard extends Component
{
    public string $currentSesi = 'pagi';
    public string $currentDate = '';
    public int $totalActive = 0;
    public int $filledCount = 0;
    public int $progressPercent = 0;
    public bool $isLate = false;

    public function mount()
    {
        $this->currentDate = date('Y-m-d');
        $hour = (int) date('H');
        
        // Determine session based on current hour
        // Sesi Pagi: 05:00 - 08:00. Sesi Malam: 18:00 - 22:00.
        // We set Sesi Pagi if hour is < 12, otherwise Sesi Malam
        if ($hour < 12) {
            $this->currentSesi = 'pagi';
            // Late if current time is past 08:00
            $this->isLate = ($hour >= 8);
        } else {
            $this->currentSesi = 'malam';
            // Late if current time is past 22:00
            $this->isLate = ($hour >= 22);
        }

        $this->calculateProgress();
    }

    public function calculateProgress()
    {
        $this->totalActive = Santri::where('status', 'aktif')->count();
        
        $this->filledCount = Presensi::where('tanggal_gregorian', $this->currentDate)
            ->where('sesi', $this->currentSesi)
            ->whereNotNull('status')
            ->count();

        $this->progressPercent = $this->totalActive > 0 
            ? round(($this->filledCount / $this->totalActive) * 100) 
            : 0;
    }

    public function getEwsSantris()
    {
        // Early Warning System: list active students with 3+ consecutive Alfa records.
        $ews = [];
        $activeSantris = Santri::where('status', 'aktif')->get();

        foreach ($activeSantris as $s) {
            $latestPresensis = Presensi::where('santri_id', $s->id)
                ->orderBy('tanggal_gregorian', 'desc')
                ->orderBy('sesi', 'desc')
                ->take(3)
                ->get();

            if ($latestPresensis->count() >= 3) {
                $consecutiveAlfas = 0;
                foreach ($latestPresensis as $p) {
                    if ($p->status === 'alfa') {
                        $consecutiveAlfas++;
                    }
                }
                if ($consecutiveAlfas === 3) {
                    $ews[] = [
                        'santri' => $s,
                        'latest_dates' => $latestPresensis->map(fn($p) => $p->tanggal_gregorian->format('d/m') . ' (' . ucfirst($p->sesi) . ')')->toArray()
                    ];
                }
            }
        }

        return $ews;
    }

    public function getHafalanWidgetStats()
    {
        $hijriToday = HijriHelper::gregorianToHijri($this->currentDate);
        $currentMonth = $hijriToday['month'];
        $currentYear = Setting::getByKey('current_tahun_ajaran', 1447);

        $totalActive = Santri::where('status', 'aktif')->count();
        
        $sudahInputCount = Hafalan::where('bulan_hijriah', $currentMonth)
            ->where('tahun_hijriah', $currentYear)
            ->whereHas('santri', function($query) {
                $query->where('status', 'aktif');
            })
            ->count();

        $belumInputCount = max(0, $totalActive - $sudahInputCount);

        return [
            'sudah' => $sudahInputCount,
            'belum' => $belumInputCount,
            'month_name' => $hijriToday['month_name']
        ];
    }

    public function render()
    {
        return view('livewire.ustaz.dashboard', [
            'ewsList' => $this->getEwsSantris(),
            'hafalanStats' => $this->getHafalanWidgetStats()
        ]);
    }
}
