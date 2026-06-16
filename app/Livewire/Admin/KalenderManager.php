<?php

namespace App\Livewire\Admin;

use App\Models\LiburMassal;
use App\Models\TahunAjaran;
use App\Services\HijriCalendarService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kalender & Hilal')]
class KalenderManager extends Component
{
    // Hilal correction property
    public int $correction = 0;

    // Manual holiday CRUD properties
    public string $nama_libur = '';

    public string $start_date = '';

    public string $end_date = '';

    public ?int $liburId = null;

    public bool $isEditMode = false;

    public bool $isModalOpen = false;

    protected array $rules = [
        'nama_libur' => 'required|string|max:150',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    public function mount()
    {
        $aktifTA = TahunAjaran::getAktif();
        $this->correction = $aktifTA ? (int) $aktifTA->koreksi_hilal : 0;
    }

    public function updateCorrection(int $days)
    {
        $aktifTA = TahunAjaran::getAktif();
        if ($aktifTA) {
            $newOffset = max(-3, min(3, $days));
            $aktifTA->update(['koreksi_hilal' => $newOffset]);
            $this->correction = $newOffset;
            session()->flash('success', 'Koreksi Hilal berhasil diubah menjadi '.($newOffset >= 0 ? '+' : '')."{$newOffset} hari.");
        } else {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
        }
    }

    public function incrementCorrection()
    {
        $this->updateCorrection($this->correction + 1);
    }

    public function decrementCorrection()
    {
        $this->updateCorrection($this->correction - 1);
    }

    public function resetCorrection()
    {
        $this->updateCorrection(0);
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->nama_libur = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->liburId = null;
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function editLibur(int $id)
    {
        $libur = LiburMassal::findOrFail($id);
        $this->liburId = $libur->id;
        $this->nama_libur = $libur->nama_libur;
        $this->start_date = $libur->start_date->format('Y-m-d');
        $this->end_date = $libur->end_date->format('Y-m-d');
        $this->isEditMode = true;
        $this->openModal();
    }

    public function saveLibur()
    {
        $this->validate();

        $aktifTA = TahunAjaran::getAktif();
        $taId = $aktifTA ? $aktifTA->id : 1;

        if ($this->isEditMode) {
            $libur = LiburMassal::findOrFail($this->liburId);
            $libur->update([
                'nama_libur' => $this->nama_libur,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);
        } else {
            LiburMassal::create([
                'tahun_ajaran_id' => $taId,
                'nama_libur' => $this->nama_libur,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'created_by' => auth()->id(),
            ]);
        }

        $this->closeModal();
        session()->flash('success', 'Pengecualian libur berhasil disimpan.');
    }

    public function deleteLibur(int $id)
    {
        $libur = LiburMassal::findOrFail($id);
        $name = $libur->nama_libur;
        $libur->delete();

        session()->flash('success', "Pengecualian libur {$name} berhasil dihapus.");
    }

    public function render()
    {
        $currentHijri = app(HijriCalendarService::class)->today();

        return view('livewire.admin.kalender-manager', [
            'currentHijri' => $currentHijri,
            'liburs' => LiburMassal::orderBy('start_date', 'asc')->get(),
        ]);
    }
}
