<?php

namespace App\Livewire\Admin;

use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Services\HijriCalendarService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title("Manajemen Santri - RIBATHUL QUR'AN")]
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

    public ?int $kamar_id = null;

    public string $tanggal_masuk = '';

    public string $catatan = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'semua'],
        'filterKelasId' => ['except' => 0],
    ];

    protected function rules()
    {
        return [
            'nama_lengkap' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'kamar_id' => 'nullable|exists:kamar,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'status' => 'required|in:aktif,nonaktif,lulus',
            'tanggal_masuk' => 'nullable|date',
            'catatan' => 'nullable|string',
        ];
    }

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

        $firstKelas = Kelas::orderBy('urutan', 'asc')->first();
        $this->kelas_id = $firstKelas ? $firstKelas->id : null;

        $firstKamar = Kamar::first();
        $this->kamar_id = $firstKamar ? $firstKamar->id : null;

        $this->tanggal_masuk = date('Y-m-d');
        $this->catatan = '';

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
        $this->tempat_lahir = $santri->tempat_lahir ?? '';
        $this->tanggal_lahir = $santri->tanggal_lahir ? $santri->tanggal_lahir->format('Y-m-d') : '';
        $this->alamat = $santri->alamat ?? '';
        $this->status = $santri->status;
        $this->kelas_id = $santri->kelas_id;
        $this->kamar_id = $santri->kamar_id;
        $this->tanggal_masuk = $santri->tanggal_masuk ? $santri->tanggal_masuk->format('Y-m-d') : '';
        $this->catatan = $santri->catatan ?? '';

        $this->isEdit = true;
        $this->openModal();
    }

    public function save()
    {
        $this->validate();

        $aktifTA = TahunAjaran::getAktif();
        $yearId = $aktifTA ? $aktifTA->id : 0;

        if ($this->isEdit) {
            $santri = Santri::findOrFail($this->santriId);
            $oldStatus = $santri->status;

            // Arsip Alumni bersifat Read-Only
            if ($oldStatus === 'lulus' && $this->status !== 'lulus') {
                $this->addError('status', 'Santri yang sudah Lulus tidak dapat diubah statusnya (Arsip Alumni bersifat Read-Only).');

                return;
            }

            DB::transaction(function () use ($santri, $oldStatus, $yearId) {
                $santri->update([
                    'nama_lengkap' => $this->nama_lengkap,
                    'tempat_lahir' => $this->tempat_lahir ?: null,
                    'tanggal_lahir' => $this->tanggal_lahir ?: null,
                    'alamat' => $this->alamat ?: null,
                    'kamar_id' => $this->kamar_id,
                    'kelas_id' => $this->kelas_id,
                    'status' => $this->status,
                    'tanggal_masuk' => $this->tanggal_masuk ?: null,
                    'catatan' => $this->catatan ?: null,
                ]);

                if ($oldStatus !== $this->status) {
                    $this->handleStatusTransition($santri, $oldStatus, $this->status, $yearId);
                }
            });
        } else {
            DB::transaction(function () use ($yearId) {
                $santri = Santri::create([
                    'nama_lengkap' => $this->nama_lengkap,
                    'tempat_lahir' => $this->tempat_lahir ?: null,
                    'tanggal_lahir' => $this->tanggal_lahir ?: null,
                    'alamat' => $this->alamat ?: null,
                    'kamar_id' => $this->kamar_id,
                    'kelas_id' => $this->kelas_id,
                    'status' => $this->status,
                    'tanggal_masuk' => $this->tanggal_masuk ?: null,
                    'catatan' => $this->catatan ?: null,
                ]);

                // Inisiasi lembar tagihan jika statusnya aktif
                if ($this->status === 'aktif' && $yearId > 0) {
                    $this->generateBilling($santri, $yearId);
                }
            });
        }

        $this->closeModal();
        session()->flash('success', 'Data santri berhasil disimpan.');
    }

    private function handleStatusTransition(Santri $santri, string $old, string $new, int $yearId)
    {
        if ($yearId <= 0) {
            return;
        }

        $currentHijri = app(HijriCalendarService::class)->today();
        $currentMonth = (int) $currentHijri['month'];

        if ($new === 'nonaktif') {
            // Ubah tagihan bulan berjalan dan seterusnya menjadi status pulang (hanya Majeg Makan bulanan)
            Tagihan::where('santri_id', $santri->id)
                ->where('tahun_ajaran_id', $yearId)
                ->where('kategori', 'majeg_makan')
                ->where('bulan_hijri', '>=', $currentMonth)
                ->where('status', '!=', 'lunas')
                ->update([
                    'status' => 'pulang',
                    'catatan' => 'Dinonaktifkan oleh Admin',
                ]);
        } elseif ($new === 'aktif') {
            // Generate tagihan baru jika belum ada, atau kembalikan yang tadinya "pulang" ke "belum_bayar"
            $hasBills = Tagihan::where('santri_id', $santri->id)->where('tahun_ajaran_id', $yearId)->exists();
            if (! $hasBills) {
                $this->generateBilling($santri, $yearId);
            } else {
                Tagihan::where('santri_id', $santri->id)
                    ->where('tahun_ajaran_id', $yearId)
                    ->where('kategori', 'majeg_makan')
                    ->where('bulan_hijri', '>=', $currentMonth)
                    ->where('status', 'pulang')
                    ->update([
                        'status' => 'belum_bayar',
                        'catatan' => null,
                    ]);
            }
        } elseif ($new === 'lulus') {
            // Lulus diset read-only, semua sisa tagihan di-freeze (dibiarkan statusnya apa adanya)
            $santri->update([
                'tanggal_keluar' => now(),
            ]);
        }
    }

    private function generateBilling(Santri $santri, int $yearId)
    {
        $aktifTA = TahunAjaran::find($yearId);
        $config = $aktifTA ? $aktifTA->konfigurasiKeuangan : null;

        $duNominal = $config ? $config->nominal_daftar_ulang : 0;
        $s1Nominal = $config ? $config->nominal_syahriah_sem1 : 0;
        $s2Nominal = $config ? $config->nominal_syahriah_sem2 : 0;
        $mmNominal = $config ? $config->nominal_majeg_makan : 0;

        // 1. Daftar Ulang
        Tagihan::firstOrCreate([
            'santri_id' => $santri->id,
            'tahun_ajaran_id' => $yearId,
            'kategori' => 'daftar_ulang',
            'bulan_hijri' => null,
        ], [
            'tahun_hijri' => $aktifTA ? $aktifTA->tahun_hijri : 1447,
            'nominal' => $duNominal,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        // 2. Syahriah Sem 1
        Tagihan::firstOrCreate([
            'santri_id' => $santri->id,
            'tahun_ajaran_id' => $yearId,
            'kategori' => 'syahriah_sem1',
            'bulan_hijri' => null,
        ], [
            'tahun_hijri' => $aktifTA ? $aktifTA->tahun_hijri : 1447,
            'nominal' => $s1Nominal,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        // 3. Syahriah Sem 2
        Tagihan::firstOrCreate([
            'santri_id' => $santri->id,
            'tahun_ajaran_id' => $yearId,
            'kategori' => 'syahriah_sem2',
            'bulan_hijri' => null,
        ], [
            'tahun_hijri' => $aktifTA ? $aktifTA->tahun_hijri : 1447,
            'nominal' => $s2Nominal,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        // 4. Majeg Makan Bulanan (11 bulan)
        $activeMonths = [11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($activeMonths as $month) {
            Tagihan::firstOrCreate([
                'santri_id' => $santri->id,
                'tahun_ajaran_id' => $yearId,
                'kategori' => 'majeg_makan',
                'bulan_hijri' => $month,
            ], [
                'tahun_hijri' => $aktifTA ? $aktifTA->tahun_hijri : 1447,
                'nominal' => $mmNominal,
                'status' => 'belum_bayar',
                'nominal_terbayar' => 0,
            ]);
        }
    }

    public function delete(int $id)
    {
        $santri = Santri::findOrFail($id);

        // Blokir jika Lulus (Arsip Alumni bersifat Read-Only)
        if ($santri->status === 'lulus') {
            session()->flash('error', 'Gagal: Santri Lulus tidak dapat dihapus (Arsip Alumni).');

            return;
        }

        $santri->delete();
        session()->flash('success', 'Data santri berhasil dihapus.');
    }

    public function render()
    {
        $query = Santri::query();

        if (! empty($this->search)) {
            $query->where('nama_lengkap', 'like', '%'.$this->search.'%');
        }

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterKelasId > 0) {
            $query->where('kelas_id', $this->filterKelasId);
        }

        $santris = $query->with(['kelas', 'kamar'])->orderBy('nama_lengkap', 'asc')->paginate(10);

        return view('livewire.admin.santri-manager', [
            'santris' => $santris,
            'kelases' => Kelas::orderBy('urutan', 'asc')->get(),
            'kamars' => Kamar::all(),
        ]);
    }
}
