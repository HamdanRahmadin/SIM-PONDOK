<?php

namespace App\Livewire\Admin;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Keuangan;
use App\Models\Presensi;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Manajemen Santri')]
class SantriManager extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterStatus = 'aktif';
    public int $filterKelasId = 0;

    // Form Properties
    public bool $isOpen = false;
    public bool $isEdit = false;
    public ?int $santriId = null;
    
    public string $nama_lengkap = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $alamat = '';
    public string $status = 'aktif';
    public ?int $kelas_id = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'semua'],
        'filterKelasId' => ['except' => 0],
    ];

    protected array $rules = [
        'nama_lengkap' => 'required|string|max:255',
        'tempat_lahir' => 'required|string|max:255',
        'tanggal_lahir' => 'required|date',
        'alamat' => 'required|string',
        'status' => 'required|in:aktif,nonaktif,lulus',
        'kelas_id' => 'required|exists:kelas,id',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->isEdit = false;
        $this->santriId = null;
        $this->nama_lengkap = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->alamat = '';
        $this->status = 'aktif';
        
        $firstKelas = Kelas::first();
        $this->kelas_id = $firstKelas ? $firstKelas->id : null;
        
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function edit(int $id)
    {
        $this->resetForm();
        $santri = Santri::findOrFail($id);
        
        $this->santriId = $santri->id;
        $this->nama_lengkap = $santri->nama_lengkap;
        $this->tempat_lahir = $santri->tempat_lahir;
        $this->tanggal_lahir = $santri->tanggal_lahir->format('Y-m-d');
        $this->alamat = $santri->alamat;
        $this->status = $santri->status;
        $this->kelas_id = $santri->kelas_id;

        $this->isEdit = true;
        $this->openModal();
    }

    public function save()
    {
        $this->validate();

        $currentYear = (int) Setting::getByKey('current_tahun_ajaran', 1447);

        if ($this->isEdit) {
            $santri = Santri::findOrFail($this->santriId);
            
            // Check if status is changed
            $oldStatus = $santri->status;
            
            // Enforce Read-Only if old status was Lulus (Arsip Alumni)
            if ($oldStatus === 'lulus' && $this->status !== 'lulus') {
                $this->addError('status', 'Santri yang sudah Lulus tidak dapat diubah statusnya (Arsip Alumni bersifat Read-Only).');
                return;
            }

            $santri->update([
                'nama_lengkap' => $this->nama_lengkap,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'alamat' => $this->alamat,
                'status' => $this->status,
                'kelas_id' => $this->kelas_id,
            ]);

            if ($oldStatus !== $this->status) {
                $this->handleStatusTransition($santri, $oldStatus, $this->status, $currentYear);
            } else {
                ActivityLog::log("Update Data Santri", "Mengubah profil santri {$santri->nama_lengkap}");
            }
        } else {
            // Check if there's an active class
            if (!$this->kelas_id) {
                $this->addError('kelas_id', 'Harap daftarkan kelas terlebih dahulu.');
                return;
            }

            $santri = Santri::create([
                'nama_lengkap' => $this->nama_lengkap,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'alamat' => $this->alamat,
                'status' => $this->status,
                'kelas_id' => $this->kelas_id,
            ]);

            // If new student is active, generate billing structure for current academic year
            if ($this->status === 'aktif') {
                $this->generateBilling($santri, $currentYear);
            }

            ActivityLog::log("Tambah Santri Baru", "Menambahkan santri baru bernama {$santri->nama_lengkap} di {$santri->kelas->nama_kelas}");
        }

        $this->closeModal();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Data santri berhasil disimpan.']);
    }

    private function handleStatusTransition(Santri $santri, string $old, string $new, int $year)
    {
        $currentHijri = HijriHelper::gregorianToHijri(date('Y-m-d'));
        $currentMonth = $currentHijri['month'];

        if ($new === 'nonaktif') {
            // Remove from presensi for current month and onwards
            Presensi::where('santri_id', $santri->id)
                ->where('tahun_hijriah', $year)
                ->where('bulan_hijriah', '>=', $currentMonth)
                ->delete();

            // Cancel unpaid bills (tidak berkewajiban bayar - delete unpaid lines)
            Keuangan::where('santri_id', $santri->id)
                ->where('tahun_ajaran', $year)
                ->where('status', 'belum_bayar')
                ->delete();

            ActivityLog::log("Ubah Status Santri (Nonaktif)", "Menonaktifkan {$santri->nama_lengkap}. Presensi berjalan dibersihkan & sisa tagihan dibekukan.");
        } elseif ($new === 'aktif') {
            $this->generateBilling($santri, $year);
            ActivityLog::log("Ubah Status Santri (Aktif)", "Mengaktifkan kembali {$santri->nama_lengkap} dan menginisiasi tagihan.");
        } elseif ($new === 'lulus') {
            // Moving to alumni (Read-Only)
            ActivityLog::log("Ubah Status Santri (Lulus)", "Memindahkan {$santri->nama_lengkap} ke mode Arsip Alumni (Read-Only).");
        }
    }

    private function generateBilling(Santri $santri, int $year)
    {
        $categories = [
            'daftar_ulang',
            'syahriah_dzulqadah',
            'syahriah_semester_1',
            'syahriah_semester_2',
        ];
        for ($m = 1; $m <= 10; $m++) {
            $categories[] = "majeg_makan_$m";
        }
        foreach ($categories as $cat) {
            Keuangan::firstOrCreate([
                'santri_id' => $santri->id,
                'tahun_ajaran' => $year,
                'kategori' => $cat
            ], [
                'status' => 'belum_bayar',
                'nominal_bayar' => 0,
                'catatan' => null
            ]);
        }
    }

    public function delete(int $id)
    {
        $santri = Santri::findOrFail($id);
        
        // Block delete if Lulus (Read-Only)
        if ($santri->status === 'lulus') {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Santri yang sudah Lulus tidak dapat dihapus (Arsip Alumni).']);
            return;
        }

        $name = $santri->nama_lengkap;
        $santri->delete();
        
        ActivityLog::log("Hapus Santri", "Menghapus data santri bernama {$name}");
        $this->dispatch('alert', ['type' => 'success', 'message' => "Data santri {$name} berhasil dihapus."]);
    }

    public function render()
    {
        $query = Santri::query();

        // Apply filters
        if (!empty($this->search)) {
            $query->where('nama_lengkap', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterKelasId > 0) {
            $query->where('kelas_id', $this->filterKelasId);
        }

        $santris = $query->with('kelas')->orderBy('nama_lengkap', 'asc')->paginate(10);

        return view('livewire.admin.santri-manager', [
            'santris' => $santris,
            'kelases' => Kelas::all(),
        ]);
    }
}
