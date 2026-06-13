<?php

namespace App\Livewire\Bendahara;

use App\Models\Keuangan;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Kelola Keuangan Santri')]
class KeuanganManager extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public int $filterKelasId = 0;
    public int $selectedYear = 1447;

    // Modal properties
    public bool $isModalOpen = false;
    public ?int $selectedSantriId = null;
    
    // Installment editor properties
    public ?int $editBillingId = null;
    public string $installmentNote = '';
    public int $installmentAmount = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKelasId' => ['except' => 0],
        'selectedYear' => ['except' => 1447],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectSantri(int $id)
    {
        $this->selectedSantriId = $id;
        $this->isModalOpen = true;
        $this->closeInstallmentForm();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedSantriId = null;
        $this->closeInstallmentForm();
    }

    public function openInstallmentForm(int $id)
    {
        $bill = Keuangan::findOrFail($id);
        $this->editBillingId = $bill->id;
        $this->installmentNote = $bill->catatan ?? '';
        $this->installmentAmount = $bill->nominal_bayar ?? 0;
    }

    public function closeInstallmentForm()
    {
        $this->editBillingId = null;
        $this->installmentNote = '';
        $this->installmentAmount = 0;
    }

    public function saveInstallment()
    {
        if (!$this->editBillingId) return;

        $bill = Keuangan::findOrFail($this->editBillingId);
        
        // Enforce Read-Only if Lulus
        if ($bill->santri->status === 'lulus') {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal: Santri Lulus bersifat Read-Only.']);
            return;
        }

        $bill->update([
            'status' => 'dicicil',
            'nominal_bayar' => $this->installmentAmount,
            'catatan' => empty($this->installmentNote) ? null : $this->installmentNote
        ]);

        ActivityLog::log(
            "Pembayaran Dicicil",
            "Mencatat cicilan {$bill->kategori} santri {$bill->santri->nama_lengkap}: Nominal masuk {$this->installmentAmount}, Catatan: {$this->installmentNote}"
        );

        $this->closeInstallmentForm();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Status cicilan berhasil dicatat.']);
    }

    public function markAsLunas(int $id)
    {
        $bill = Keuangan::findOrFail($id);

        // Enforce Read-Only if Lulus
        if ($bill->santri->status === 'lulus') {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal: Santri Lulus bersifat Read-Only.']);
            return;
        }

        // Check if category is Syahriah Sem 2 and is locked
        if ($bill->kategori === 'syahriah_semester_2' && !$this->isSemester2Open()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal: Syahriah Semester 2 dikunci karena belum memasuki paruh kedua tahun ajaran.']);
            return;
        }

        $bill->update([
            'status' => 'lunas',
            'nominal_bayar' => 0, // reset nominal bayar check if not needed
            'catatan' => null
        ]);

        ActivityLog::log("Pembayaran Lunas", "Menandai {$bill->kategori} santri {$bill->santri->nama_lengkap} Lunas");

        // Background Trigger for Daftar Ulang
        if ($bill->kategori === 'daftar_ulang') {
            // Auto pay: syahriah_dzulqadah and syahriah_semester_1
            Keuangan::where('santri_id', $bill->santri_id)
                ->where('tahun_ajaran', $bill->tahun_ajaran)
                ->whereIn('kategori', ['syahriah_dzulqadah', 'syahriah_semester_1'])
                ->update([
                    'status' => 'lunas',
                    'nominal_bayar' => 0,
                    'catatan' => 'Pelunasan otomatis via Daftar Ulang'
                ]);

            ActivityLog::log(
                "Trigger Pelunasan Otomatis",
                "Daftar Ulang Lunas memicu pelunasan Syahriah Dzulqa'dah & Semester 1 untuk {$bill->santri->nama_lengkap}"
            );
        }

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Status pembayaran berhasil diubah menjadi Lunas.']);
    }

    public function resetBill(int $id)
    {
        $bill = Keuangan::findOrFail($id);

        // Enforce Read-Only if Lulus
        if ($bill->santri->status === 'lulus') {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal: Santri Lulus bersifat Read-Only.']);
            return;
        }

        $bill->update([
            'status' => 'belum_bayar',
            'nominal_bayar' => 0,
            'catatan' => null
        ]);

        ActivityLog::log("Reset Pembayaran", "Mereset status pembayaran {$bill->kategori} santri {$bill->santri->nama_lengkap} kembali ke Belum Bayar.");
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Status pembayaran berhasil di-reset kembali ke Belum Bayar.']);
    }

    /**
     * Check if Semester 2 is open (current Hijri month is in 4, 5, 6, 7, 8, 9)
     */
    public function isSemester2Open(): bool
    {
        $currentHijri = HijriHelper::gregorianToHijri(date('Y-m-d'));
        // Semester 2 active months: Rabiul Akhir (4) to Ramadhan (9)
        return in_array($currentHijri['month'], [4, 5, 6, 7, 8, 9]);
    }

    public function render()
    {
        $query = Santri::query();

        if (!empty($this->search)) {
            $query->where('nama_lengkap', 'like', '%' . $this->search . '%');
        }

        if ($this->filterKelasId > 0) {
            $query->where('kelas_id', $this->filterKelasId);
        }

        $santris = $query->with('kelas')->orderBy('nama_lengkap', 'asc')->paginate(10);

        // Load billing detail if modal is open
        $selectedSantri = null;
        $billings = [];
        if ($this->isModalOpen && $this->selectedSantriId) {
            $selectedSantri = Santri::with('kelas')->findOrFail($this->selectedSantriId);
            
            // Get all billing records for this student and selected year
            $billings = Keuangan::where('santri_id', $this->selectedSantriId)
                ->where('tahun_ajaran', $this->selectedYear)
                ->get();
        }

        return view('livewire.bendahara.keuangan-manager', [
            'santris' => $santris,
            'kelases' => Kelas::all(),
            'selectedSantri' => $selectedSantri,
            'billings' => $billings
        ]);
    }
}
