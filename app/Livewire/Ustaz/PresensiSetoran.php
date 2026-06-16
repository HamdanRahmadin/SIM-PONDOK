<?php

namespace App\Livewire\Ustaz;

use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Santri;
use App\Services\HijriCalendarService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Presensi Setoran - Ribathul Qur'an")]
class PresensiSetoran extends Component
{
    public int $kelas_id = 0;

    public string $sesi = 'pagi';

    public string $tanggal = '';

    public $santris = [];

    public $presensiData = [];

    public bool $isLocked = false;

    public function mount()
    {
        Carbon::setLocale('id');
        $this->tanggal = date('Y-m-d');

        $hour = (int) date('H');
        $this->sesi = ($hour >= 5 && $hour < 12) ? 'pagi' : 'malam';

        // Retrieve from session or default to first class
        $this->kelas_id = session('active_kelas_id', Kelas::orderBy('urutan')->value('id') ?? 0);

        $hijriService = app(HijriCalendarService::class);
        $this->isLocked = ! $hijriService->isValidAttendanceDay(Carbon::today(), $this->sesi);

        $this->loadSantri();
    }

    public function updatedKelasId($value)
    {
        session(['active_kelas_id' => $value]);
        $this->loadSantri();
    }

    public function updatedSesi($value)
    {
        $hijriService = app(HijriCalendarService::class);
        $this->isLocked = ! $hijriService->isValidAttendanceDay(Carbon::today(), $this->sesi);
        $this->loadSantri();
    }

    public function loadSantri()
    {
        if ($this->kelas_id == 0) {
            return;
        }

        $this->santris = Santri::where('kelas_id', $this->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Load existing presensi
        $existing = Presensi::where('kelas_id', $this->kelas_id)
            ->where('tanggal_masehi', $this->tanggal)
            ->where('sesi', $this->sesi)
            ->get()->keyBy('santri_id');

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
            Presensi::updateOrCreate(
                [
                    'santri_id' => $santriId,
                    'tanggal_masehi' => $this->tanggal,
                    'sesi' => $this->sesi,
                ],
                [
                    'kelas_id' => $this->kelas_id,
                    'bulan_hijri' => $hijri['month'],
                    'tahun_hijri' => $hijri['year'],
                    'status' => $data['status'],
                    'catatan' => $data['catatan'],
                    'dicatat_oleh' => auth()->id(),
                ]
            );
        }

        session()->flash('success', 'Presensi berhasil disimpan.');
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
        $kelasList = Kelas::orderBy('urutan')->get();

        return view('livewire.ustaz.presensi-setoran', compact('kelasList'))
            ->layout('components.layouts.app');
    }
}
