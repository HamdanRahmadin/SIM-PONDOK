<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\Santri;
use App\Models\TahunAjaran;
use Livewire\Attributes\Title;
use Livewire\Component;

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
                'nama_kelas' => 'required|string|max:105|unique:kelas,nama_kelas,'.$this->kelasId,
            ]);
            $kelas = Kelas::findOrFail($this->kelasId);
            $kelas->update(['nama_kelas' => $this->nama_kelas]);
        } else {
            $this->validate();
            $kelas = Kelas::create(['nama_kelas' => $this->nama_kelas]);
        }

        $this->closeModal();
        session()->flash('success', 'Data kelas berhasil disimpan.');
    }

    public function deleteKelas(int $id)
    {
        $kelas = Kelas::findOrFail($id);

        // Check if class has students
        if ($kelas->santris()->exists()) {
            session()->flash('error', 'Kelas tidak dapat dihapus karena masih memiliki santri aktif.');

            return;
        }

        $name = $kelas->nama_kelas;
        $kelas->delete();

        session()->flash('success', "Kelas {$name} berhasil dihapus.");
    }

    public function promoteMassal()
    {
        if (empty($this->selectedSantriIds)) {
            session()->flash('error', 'Harap pilih minimal satu santri untuk kenaikan kelas.');

            return;
        }

        if (! $this->targetKelasId) {
            session()->flash('error', 'Harap pilih kelas tujuan.');

            return;
        }

        $aktifTA = TahunAjaran::getAktif();
        $taId = $aktifTA ? $aktifTA->id : 1;
        $targetKelas = Kelas::findOrFail($this->targetKelasId);

        $students = Santri::whereIn('id', $this->selectedSantriIds)->get();
        foreach ($students as $student) {
            $oldKelasId = $student->kelas_id;
            $student->update(['kelas_id' => $targetKelas->id]);

            RiwayatKelas::create([
                'santri_id' => $student->id,
                'kelas_lama_id' => $oldKelasId,
                'kelas_baru_id' => $targetKelas->id,
                'tahun_ajaran_id' => $taId,
                'dipindah_oleh' => auth()->id(),
            ]);
        }

        $this->selectedSantriIds = [];
        session()->flash('success', 'Kenaikan kelas massal berhasil diproses!');
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
            'promotionSantris' => $promotionSantris,
        ]);
    }
}
