<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Models\LiburMassal;
use App\Models\ActivityLog;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\Attributes\Title;

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
        $this->correction = (int) Setting::getByKey('hilal_correction', 0);
    }

    public function updateCorrection(int $days)
    {
        $this->correction = $days;
        Setting::setByKey('hilal_correction', (string) $this->correction);
        
        $op = $days >= 0 ? '+' : '';
        ActivityLog::log("Koreksi Hilal Global", "Mengubah koreksi hilal menjadi {$op}{$this->correction} Hari");
        
        $this->dispatch('alert', ['type' => 'success', 'message' => "Koreksi Hilal berhasil diubah menjadi {$op}{$this->correction} hari."]);
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

        if ($this->isEditMode) {
            $libur = LiburMassal::findOrFail($this->liburId);
            $libur->update([
                'nama_libur' => $this->nama_libur,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);
            ActivityLog::log("Ubah Libur Massal", "Mengubah pengecualian libur {$this->nama_libur}");
        } else {
            LiburMassal::create([
                'nama_libur' => $this->nama_libur,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);
            ActivityLog::log("Tambah Libur Massal", "Membuat pengecualian libur baru: {$this->nama_libur}");
        }

        $this->closeModal();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Pengecualian libur berhasil disimpan.']);
    }

    public function deleteLibur(int $id)
    {
        $libur = LiburMassal::findOrFail($id);
        $name = $libur->nama_libur;
        $libur->delete();

        ActivityLog::log("Hapus Libur Massal", "Menghapus pengecualian libur {$name}");
        $this->dispatch('alert', ['type' => 'success', 'message' => "Pengecualian libur {$name} berhasil dihapus."]);
    }

    public function render()
    {
        $currentHijri = HijriHelper::gregorianToHijri(date('Y-m-d'));

        return view('livewire.admin.kalender-manager', [
            'currentHijri' => $currentHijri,
            'liburs' => LiburMassal::orderBy('start_date', 'asc')->get()
        ]);
    }
}
