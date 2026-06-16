<?php

namespace App\Livewire\Ustaz;

use App\Models\PresensiHalaqoh as PresensiHalaqohModel;
use App\Models\Santri;
use App\Services\HijriCalendarService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Presensi Halaqoh - Ribathul Qur'an")]
class PresensiHalaqoh extends Component
{
    public string $tanggal = '';

    public $santris = [];

    public $presensiData = [];

    public bool $isLocked = false;

    public function mount()
    {
        Carbon::setLocale('id');
        $this->tanggal = date('Y-m-d');

        // Halaqoh only valid on Wednesday
        if (! Carbon::parse($this->tanggal)->isWednesday()) {
            $this->isLocked = true;
        }

        $this->loadSantri();
    }

    public function loadSantri()
    {
        // Halaqoh shows ALL active santri — no kelas filter
        $this->santris = Santri::where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Load existing presensi for today
        $existing = PresensiHalaqohModel::where('tanggal_masehi', $this->tanggal)
            ->get()
            ->keyBy('santri_id');

        $this->presensiData = [];
        foreach ($this->santris as $santri) {
            $this->presensiData[$santri->id] = [
                'status' => $existing->has($santri->id) ? $existing[$santri->id]->status : 'alfa',
                'catatan' => $existing->has($santri->id) ? $existing[$santri->id]->catatan : '',
            ];
        }
    }

    public function savePresensi()
    {
        if ($this->isLocked) {
            return;
        }

        $hijriService = app(HijriCalendarService::class);
        $hijri = $hijriService->convertToHijri(Carbon::today());

        foreach ($this->presensiData as $santriId => $data) {
            PresensiHalaqohModel::updateOrCreate(
                [
                    'santri_id' => $santriId,
                    'tanggal_masehi' => $this->tanggal,
                ],
                [
                    'bulan_hijri' => $hijri['month'],
                    'tahun_hijri' => $hijri['year'],
                    'status' => $data['status'],
                    'catatan' => $data['catatan'],
                    'dicatat_oleh' => auth()->id(),
                ]
            );
        }

        session()->flash('success', 'Presensi Halaqoh berhasil disimpan.');
    }

    public function markAllHadir()
    {
        if ($this->isLocked) {
            return;
        }

        foreach ($this->presensiData as $santriId => $data) {
            $this->presensiData[$santriId]['status'] = 'hadir';
        }
    }

    public function render()
    {
        return view('livewire.ustaz.presensi-halaqoh')
            ->layout('components.layouts.app');
    }
}
