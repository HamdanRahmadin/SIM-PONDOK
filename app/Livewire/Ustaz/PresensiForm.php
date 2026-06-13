<?php

namespace App\Livewire\Ustaz;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Presensi;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Presensi Harian')]
class PresensiForm extends Component
{
    public int $selectedKelasId = 0;
    public string $selectedDate = '';
    public string $selectedSesi = 'pagi';
    public bool $isLocked = false;
    public string $lockReason = '';

    // Model inputs mapped by santri_id
    public array $statuses = [];
    public array $notes = [];

    public function mount()
    {
        $this->selectedDate = date('Y-m-d');
        $hour = (int) date('H');
        $this->selectedSesi = $hour < 12 ? 'pagi' : 'malam';

        $firstKelas = Kelas::first();
        if ($firstKelas) {
            $this->selectedKelasId = $firstKelas->id;
        }

        $this->loadPresensi();
    }

    public function updatedSelectedKelasId()
    {
        $this->loadPresensi();
    }

    public function updatedSelectedDate()
    {
        $this->loadPresensi();
    }

    public function updatedSelectedSesi()
    {
        $this->loadPresensi();
    }

    public function loadPresensi()
    {
        $this->statuses = [];
        $this->notes = [];
        $this->isLocked = false;
        $this->lockReason = '';

        if (!$this->selectedKelasId || empty($this->selectedDate)) {
            return;
        }

        // 1. Check Holiday/Lock exceptions
        $hijri = HijriHelper::gregorianToHijri($this->selectedDate);
        $dayOfWeek = (int) date('w', strtotime($this->selectedDate)); // 0 (Sunday) to 6 (Saturday)

        if ($dayOfWeek === 4 && $this->selectedSesi === 'malam') {
            $this->isLocked = true;
            $this->lockReason = 'Terkunci otomatis: Malam Jum\'at (Kamis Sesi Malam) adalah waktu libur presensi.';
        } elseif ($dayOfWeek === 5 && $this->selectedSesi === 'pagi') {
            $this->isLocked = true;
            $this->lockReason = 'Terkunci otomatis: Jum\'at Sesi Pagi adalah waktu libur presensi.';
        } elseif ($hijri['month'] === 10) {
            $this->isLocked = true;
            $this->lockReason = 'Terkunci otomatis: Bulan Syawal adalah libur global pondok.';
        } else {
            // Check manual Libur Massal range
            $manualLibur = \App\Models\LiburMassal::where('start_date', '<=', $this->selectedDate)
                ->where('end_date', '>=', $this->selectedDate)
                ->first();
            if ($manualLibur) {
                $this->isLocked = true;
                $this->lockReason = "Terkunci otomatis: Sedang masa libur massal ({$manualLibur->nama_libur}).";
            }
        }

        // 2. Fetch active students for this class
        $santris = Santri::where('kelas_id', $this->selectedKelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 3. Initialize/fetch records
        $currentYear = Setting::getByKey('current_tahun_ajaran', 1447);

        foreach ($santris as $s) {
            $presensi = Presensi::firstOrCreate([
                'santri_id' => $s->id,
                'tanggal_gregorian' => $this->selectedDate,
                'sesi' => $this->selectedSesi
            ], [
                'tanggal_hijriah' => $hijri['formatted'],
                'bulan_hijriah' => $hijri['month'],
                'tahun_hijriah' => $currentYear,
                'status' => null,
                'catatan_setoran' => null
            ]);

            $this->statuses[$s->id] = $presensi->status;
            $this->notes[$s->id] = $presensi->catatan_setoran ?? '';
        }
    }

    public function changeStatus(int $santriId, ?string $status)
    {
        if ($this->isLocked) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal mengubah status: Sesi presensi ini dikunci.']);
            return;
        }

        Presensi::where('santri_id', $santriId)
            ->where('tanggal_gregorian', $this->selectedDate)
            ->where('sesi', $this->selectedSesi)
            ->update(['status' => $status]);

        $this->statuses[$santriId] = $status;
    }

    public function saveNote(int $santriId)
    {
        if ($this->isLocked) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal menyimpan catatan: Sesi presensi ini dikunci.']);
            return;
        }

        $note = $this->notes[$santriId] ?? '';
        Presensi::where('santri_id', $santriId)
            ->where('tanggal_gregorian', $this->selectedDate)
            ->where('sesi', $this->selectedSesi)
            ->update(['catatan_setoran' => empty($note) ? null : $note]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Catatan setoran berhasil disimpan.']);
    }

    public function setAllPresent()
    {
        if ($this->isLocked) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal melakukan aksi: Sesi presensi ini dikunci.']);
            return;
        }

        $santriIds = array_keys($this->statuses);
        if (empty($santriIds)) {
            return;
        }

        // Update in database: null or alfa becomes hadir
        Presensi::whereIn('santri_id', $santriIds)
            ->where('tanggal_gregorian', $this->selectedDate)
            ->where('sesi', $this->selectedSesi)
            ->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', 'alfa');
            })
            ->update(['status' => 'hadir']);

        ActivityLog::log("Set All Present", "Mengubah semua presensi kosong/alfa menjadi Hadir di Sesi {$this->selectedSesi} tanggal {$this->selectedDate}");

        $this->loadPresensi();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Semua santri yang kosong / Alfa berhasil di-set Hadir.']);
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

        return view('livewire.ustaz.presensi-form', [
            'kelases' => Kelas::all(),
            'santris' => $santris
        ]);
    }
}
