<?php

namespace App\Livewire\Ustaz;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Input Hafalan Bulanan')]
class HafalanForm extends Component
{
    public int $selectedKelasId = 0;
    public int $currentMonth = 0;
    public int $currentYear = 0;
    public string $monthName = '';

    // Inputs mapped by santri_id
    public array $inputs = [];

    // History data mapping
    public array $histories = [];

    public function mount()
    {
        $hijri = HijriHelper::gregorianToHijri(date('Y-m-d'));
        $this->currentMonth = $hijri['month'];
        $this->currentYear = (int) Setting::getByKey('current_tahun_ajaran', 1447);
        $this->monthName = $hijri['month_name'];

        $firstKelas = Kelas::first();
        if ($firstKelas) {
            $this->selectedKelasId = $firstKelas->id;
        }

        $this->loadHafalan();
    }

    public function updatedSelectedKelasId()
    {
        $this->loadHafalan();
    }

    public function loadHafalan()
    {
        $this->inputs = [];
        $this->histories = [];

        if (!$this->selectedKelasId) {
            return;
        }

        $santris = Santri::where('kelas_id', $this->selectedKelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $monthNames = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awwal', 4 => 'Rabiul Akhir',
            5 => 'Jumadil Awwal', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => 'Sya\'ban',
            9 => 'Ramadhan', 10 => 'Syawal', 11 => 'Dzulqa\'dah', 12 => 'Dzulhijjah'
        ];

        foreach ($santris as $s) {
            // Load current month's record
            $record = Hafalan::where('santri_id', $s->id)
                ->where('bulan_hijriah', $this->currentMonth)
                ->where('tahun_hijriah', $this->currentYear)
                ->first();

            $this->inputs[$s->id] = $record ? $record->hafalan_text : '';

            // Load histories: past months of current year, or previous years
            $pastRecords = Hafalan::where('santri_id', $s->id)
                ->where(function($query) {
                    $query->where('tahun_hijriah', '<', $this->currentYear)
                          ->orWhere(function($sub) {
                              $sub->where('tahun_hijriah', $this->currentYear)
                                  ->where('bulan_hijriah', '<', $this->currentMonth);
                          });
                })
                ->orderBy('tahun_hijriah', 'desc')
                ->orderBy('bulan_hijriah', 'desc')
                ->get();

            $this->histories[$s->id] = $pastRecords->map(function($r) use ($monthNames) {
                return [
                    'label' => ($monthNames[$r->bulan_hijriah] ?? 'Bulan') . ' ' . $r->tahun_hijriah,
                    'text' => $r->hafalan_text
                ];
            })->toArray();
        }
    }

    public function saveHafalan(int $santriId)
    {
        $hafalanText = $this->inputs[$santriId] ?? '';
        
        $santri = Santri::findOrFail($santriId);

        Hafalan::updateOrCreate([
            'santri_id' => $santriId,
            'bulan_hijriah' => $this->currentMonth,
            'tahun_hijriah' => $this->currentYear
        ], [
            'hafalan_text' => $hafalanText
        ]);

        ActivityLog::log("Input Capaian Hafalan", "Mengisi hafalan santri {$santri->nama_lengkap} untuk bulan {$this->monthName} {$this->currentYear}");

        $this->dispatch('alert', ['type' => 'success', 'message' => "Hafalan {$santri->nama_lengkap} berhasil disimpan."]);
        
        $this->loadHafalan(); // Refresh history list
    }

    public function render()
    {
        $santris = [];
        if ($this->selectedKelasId > 0) {
            $santris = Santri::where('kelas_id', $this->selectedKelasId)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap', 'asc')
                ->get();
        }

        return view('livewire.ustaz.hafalan-form', [
            'kelases' => Kelas::all(),
            'santris' => $santris
        ]);
    }
}
