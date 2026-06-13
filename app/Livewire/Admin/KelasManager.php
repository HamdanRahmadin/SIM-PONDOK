<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Manajemen Kelas')]
class KelasManager extends Component
{
    // Class CRUD properties
    public string $nama_kelas = '';
    public ?int $kelasId = null;
    public bool $isEditMode = false;
    public bool $isModalOpen = false;

    // Promotion properties
    public int $promoSourceKelasId = 0;
    public array $selectedSantriIds = [];
    public ?int $targetKelasId = null;

    protected array $rules = [
        'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas',
    ];

    public function mount()
    {
        $firstKelas = Kelas::first();
        if ($firstKelas) {
            $this->promoSourceKelasId = $firstKelas->id;
            
            $secondKelas = Kelas::where('id', '!=', $firstKelas->id)->first();
            $this->targetKelasId = $secondKelas ? $secondKelas->id : $firstKelas->id;
        }
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->nama_kelas = '';
        $this->kelasId = null;
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function editKelas(int $id)
    {
        $kelas = Kelas::findOrFail($id);
        $this->kelasId = $kelas->id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->isEditMode = true;
        $this->openModal();
    }

    public function saveKelas()
    {
        if ($this->isEditMode) {
            $this->validate([
                'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas,' . $this->kelasId,
            ]);
            $kelas = Kelas::findOrFail($this->kelasId);
            $oldName = $kelas->nama_kelas;
            $kelas->update(['nama_kelas' => $this->nama_kelas]);
            ActivityLog::log("Ubah Kelas", "Mengubah nama kelas dari {$oldName} menjadi {$this->nama_kelas}");
        } else {
            $this->validate();
            $kelas = Kelas::create(['nama_kelas' => $this->nama_kelas]);
            ActivityLog::log("Tambah Kelas Baru", "Menambahkan kelas baru bernama {$this->nama_kelas}");
        }

        $this->closeModal();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Data kelas berhasil disimpan.']);
    }

    public function deleteKelas(int $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        // Check if class has students
        if ($kelas->santris()->exists()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Kelas tidak dapat dihapus karena masih memiliki santri aktif.']);
            return;
        }

        $name = $kelas->nama_kelas;
        $kelas->delete();
        
        ActivityLog::log("Hapus Kelas", "Menghapus kelas {$name}");
        $this->dispatch('alert', ['type' => 'success', 'message' => "Kelas {$name} berhasil dihapus."]);
    }

    public function promoteMassal()
    {
        if (empty($this->selectedSantriIds)) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Harap pilih minimal satu santri untuk kenaikan kelas.']);
            return;
        }

        if (!$this->targetKelasId) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Harap pilih kelas tujuan.']);
            return;
        }

        $targetKelas = Kelas::findOrFail($this->targetKelasId);
        
        // Bulk update class IDs
        Santri::whereIn('id', $this->selectedSantriIds)
            ->update(['kelas_id' => $targetKelas->id]);

        ActivityLog::log(
            "Kenaikan Kelas Massal", 
            "Memindahkan " . count($this->selectedSantriIds) . " santri ke {$targetKelas->nama_kelas}"
        );

        $this->selectedSantriIds = [];
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Kenaikan kelas massal berhasil diproses!']);
    }

    public function render()
    {
        // Fetch students in source class who are active or non-active (excluding graduated/lulus)
        $promotionSantris = [];
        if ($this->promoSourceKelasId > 0) {
            $promotionSantris = Santri::where('kelas_id', $this->promoSourceKelasId)
                ->whereIn('status', ['aktif', 'nonaktif'])
                ->orderBy('nama_lengkap', 'asc')
                ->get();
        }

        return view('livewire.admin.kelas-manager', [
            'kelases' => Kelas::withCount('santris')->get(),
            'promotionSantris' => $promotionSantris
        ]);
    }
}
