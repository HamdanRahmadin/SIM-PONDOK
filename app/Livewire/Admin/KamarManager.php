<?php

namespace App\Livewire\Admin;

use App\Models\Kamar;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manajemen Kamar')]
class KamarManager extends Component
{
    public string $nama_kamar = '';

    public ?string $keterangan = null;

    public ?int $kamarId = null;

    public bool $isEditMode = false;

    public bool $isModalOpen = false;

    protected array $rules = [
        'nama_kamar' => 'required|string|max:100|unique:kamar,nama_kamar',
        'keterangan' => 'nullable|string',
    ];

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->nama_kamar = '';
        $this->keterangan = null;
        $this->kamarId = null;
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function editKamar(int $id)
    {
        $kamar = Kamar::findOrFail($id);
        $this->kamarId = $kamar->id;
        $this->nama_kamar = $kamar->nama_kamar;
        $this->keterangan = $kamar->keterangan;
        $this->isEditMode = true;
        $this->openModal();
    }

    public function saveKamar()
    {
        if ($this->isEditMode) {
            $this->validate([
                'nama_kamar' => 'required|string|max:100|unique:kamar,nama_kamar,'.$this->kamarId,
                'keterangan' => 'nullable|string',
            ]);
            $kamar = Kamar::findOrFail($this->kamarId);
            $kamar->update([
                'nama_kamar' => $this->nama_kamar,
                'keterangan' => $this->keterangan,
            ]);
        } else {
            $this->validate();
            Kamar::create([
                'nama_kamar' => $this->nama_kamar,
                'keterangan' => $this->keterangan,
            ]);
        }

        $this->closeModal();
        session()->flash('success', 'Data kamar berhasil disimpan.');
    }

    public function deleteKamar(int $id)
    {
        $kamar = Kamar::findOrFail($id);

        // Check if room has students
        if ($kamar->santris()->exists()) {
            session()->flash('error', 'Kamar tidak dapat dihapus karena masih memiliki santri aktif.');

            return;
        }

        $name = $kamar->nama_kamar;
        $kamar->delete();

        session()->flash('success', "Kamar {$name} berhasil dihapus.");
    }

    public function render()
    {
        return view('livewire.admin.kamar-manager', [
            'kamars' => Kamar::withCount('santris')->get()->sortBy('nama_kamar', SORT_NATURAL | SORT_FLAG_CASE)->values(),
        ]);
    }
}
