<?php

namespace App\Livewire\Bendahara;

use App\Exports\KeuanganExport;
use App\Models\Cicilan;
use App\Models\Kamar;
use App\Models\KonfigurasiKeuangan;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title("Kelola Keuangan - RIBATHUL QUR'AN")]
class KeuanganManager extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';

    public int $filterKamarId = 0;

    public int $selectedYearId = 0;

    // Modal & Selection properties
    public bool $isModalOpen = false;

    public ?int $selectedSantriId = null;

    // Payment form properties
    public ?int $selectedTagihanId = null;

    public string $paymentNote = '';

    public int $paymentAmount = 0;

    public int $billNominal = 0;

    // Global Config properties
    public bool $isConfigModalOpen = false;

    public int $configDaftarUlang = 0;

    public int $configSyahriahSem1 = 0;

    public int $configSyahriahSem2 = 0;

    public int $configMajegMakan = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKamarId' => ['except' => 0],
        'selectedYearId' => ['except' => 0],
    ];

    public function mount()
    {
        $aktifTA = TahunAjaran::getAktif();
        if ($aktifTA) {
            $this->selectedYearId = $aktifTA->id;
        } else {
            $firstTA = TahunAjaran::first();
            $this->selectedYearId = $firstTA ? $firstTA->id : 0;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKamarId()
    {
        $this->resetPage();
    }

    public function updatingSelectedYearId()
    {
        $this->resetPage();
    }

    public function selectSantri(int $id)
    {
        $this->selectedSantriId = $id;
        $this->isModalOpen = true;
        $this->closePaymentForm();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedSantriId = null;
        $this->closePaymentForm();
    }

    public function openPaymentForm(int $tagihanId)
    {
        $tagihan = Tagihan::findOrFail($tagihanId);
        $this->selectedTagihanId = $tagihan->id;
        $this->billNominal = (int) $tagihan->nominal;
        $this->paymentAmount = (int) $tagihan->sisa_tagihan;
        $this->paymentNote = '';
    }

    public function closePaymentForm()
    {
        $this->selectedTagihanId = null;
        $this->paymentAmount = 0;
        $this->billNominal = 0;
        $this->paymentNote = '';
    }

    public function savePayment()
    {
        if (! $this->selectedTagihanId) {
            return;
        }

        $tagihan = Tagihan::findOrFail($this->selectedTagihanId);
        $santri = $tagihan->santri;

        // Cegah transaksi untuk santri yang lulus
        if ($santri->status === 'lulus') {
            session()->flash('error', 'Gagal: Santri Lulus bersifat Read-Only.');

            return;
        }

        // Validasi input nominal tagihan
        if ($this->billNominal < 0) {
            session()->flash('error', 'Total nominal tagihan tidak boleh kurang dari Rp 0.');

            return;
        }

        if ($this->billNominal < $tagihan->nominal_terbayar) {
            session()->flash('error', 'Total nominal tagihan tidak boleh lebih kecil dari jumlah yang sudah terbayar (Rp '.number_format($tagihan->nominal_terbayar, 0, ',', '.').').');

            return;
        }

        // Hitung sisa tagihan baru setelah total nominal disesuaikan
        $newSisa = $this->billNominal - $tagihan->nominal_terbayar;

        // Validasi cicilan baru
        if ($this->paymentAmount < 0 || $this->paymentAmount > $newSisa) {
            session()->flash('error', 'Jumlah pembayaran tidak valid (harus di antara Rp 0 s.d Sisa Tagihan Baru).');

            return;
        }

        DB::transaction(function () use ($tagihan, $santri) {
            // 1. Simpan data cicilan jika nominal bayar > 0
            if ($this->paymentAmount > 0) {
                Cicilan::create([
                    'tagihan_id' => $tagihan->id,
                    'nominal' => $this->paymentAmount,
                    'catatan' => $this->paymentNote,
                    'dicatat_oleh' => Auth::id(),
                ]);
            }

            // 2. Hitung nominal terbayar baru & status baru
            $newTerbayar = $tagihan->nominal_terbayar + $this->paymentAmount;

            if ($newTerbayar >= $this->billNominal) {
                $newStatus = 'lunas';
            } elseif ($newTerbayar > 0) {
                $newStatus = 'dicicil';
            } else {
                $newStatus = ($tagihan->status === 'pulang' && $santri->status === 'nonaktif') ? 'pulang' : 'belum_bayar';
            }

            $tagihan->update([
                'nominal' => $this->billNominal,
                'nominal_terbayar' => $newTerbayar,
                'status' => $newStatus,
                'catatan' => $this->paymentNote ?: null,
            ]);

            // 3. Logika Cascade Daftar Ulang
            if ($tagihan->kategori === 'daftar_ulang' && $newStatus === 'lunas') {
                // A. Lunas Syahriah Semester 1
                $syahriahSem1 = Tagihan::where('santri_id', $tagihan->santri_id)
                    ->where('tahun_ajaran_id', $this->selectedYearId)
                    ->where('kategori', 'syahriah_sem1')
                    ->first();

                if ($syahriahSem1 && $syahriahSem1->status !== 'lunas') {
                    $sisaS1 = $syahriahSem1->sisa_tagihan;
                    if ($sisaS1 > 0) {
                        Cicilan::create([
                            'tagihan_id' => $syahriahSem1->id,
                            'nominal' => $sisaS1,
                            'catatan' => 'Pelunasan otomatis via Daftar Ulang',
                            'dicatat_oleh' => Auth::id(),
                        ]);
                    }
                    $syahriahSem1->update([
                        'nominal_terbayar' => $syahriahSem1->nominal,
                        'status' => 'lunas',
                        'catatan' => 'Pelunasan otomatis via Daftar Ulang',
                    ]);
                }

                // B. Lunas Majeg Makan Bulan 11 (Dzulqa'dah)
                $majegMakanDzulqadah = Tagihan::where('santri_id', $tagihan->santri_id)
                    ->where('tahun_ajaran_id', $this->selectedYearId)
                    ->where('kategori', 'majeg_makan')
                    ->where('bulan_hijri', 11)
                    ->first();

                if ($majegMakanDzulqadah && $majegMakanDzulqadah->status !== 'lunas') {
                    $sisaMM = $majegMakanDzulqadah->sisa_tagihan;
                    if ($sisaMM > 0) {
                        Cicilan::create([
                            'tagihan_id' => $majegMakanDzulqadah->id,
                            'nominal' => $sisaMM,
                            'catatan' => 'Pelunasan otomatis via Daftar Ulang',
                            'dicatat_oleh' => Auth::id(),
                        ]);
                    }
                    $majegMakanDzulqadah->update([
                        'nominal_terbayar' => $majegMakanDzulqadah->nominal,
                        'status' => 'lunas',
                        'catatan' => 'Pelunasan otomatis via Daftar Ulang',
                    ]);
                }
            }
        });

        $this->closePaymentForm();
        session()->flash('success', 'Tagihan dan pembayaran berhasil diperbarui.');
    }

    public function markAsPulang()
    {
        if (! $this->selectedTagihanId) {
            return;
        }

        $tagihan = Tagihan::findOrFail($this->selectedTagihanId);

        if ($tagihan->santri->status === 'lulus') {
            session()->flash('error', 'Gagal: Santri Lulus bersifat Read-Only.');

            return;
        }

        $tagihan->update([
            'status' => 'pulang',
            'catatan' => 'Ditandai pulang oleh Bendahara',
        ]);

        $this->closePaymentForm();
        session()->flash('success', 'Tagihan berhasil ditandai Pulang (Santri Nonaktif).');
    }

    public function resetTagihan()
    {
        if (! $this->selectedTagihanId) {
            return;
        }

        $tagihan = Tagihan::findOrFail($this->selectedTagihanId);

        if ($tagihan->santri->status === 'lulus') {
            session()->flash('error', 'Gagal: Santri Lulus bersifat Read-Only.');

            return;
        }

        // Hapus log cicilan terkait
        Cicilan::where('tagihan_id', $tagihan->id)->delete();

        $tagihan->update([
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
            'catatan' => null,
        ]);

        $this->closePaymentForm();
        session()->flash('success', 'Status tagihan berhasil di-reset.');
    }

    public function openConfigModal()
    {
        $aktifTA = TahunAjaran::find($this->selectedYearId);
        if (! $aktifTA) {
            session()->flash('error', 'Tahun ajaran tidak ditemukan.');

            return;
        }

        $config = $aktifTA->konfigurasiKeuangan;
        if (! $config) {
            $config = KonfigurasiKeuangan::create([
                'tahun_ajaran_id' => $aktifTA->id,
                'nominal_daftar_ulang' => 0,
                'nominal_syahriah_sem1' => 0,
                'nominal_syahriah_sem2' => 0,
                'nominal_majeg_makan' => 0,
            ]);
        }

        $this->configDaftarUlang = (int) $config->nominal_daftar_ulang;
        $this->configSyahriahSem1 = (int) $config->nominal_syahriah_sem1;
        $this->configSyahriahSem2 = (int) $config->nominal_syahriah_sem2;
        $this->configMajegMakan = (int) $config->nominal_majeg_makan;
        $this->isConfigModalOpen = true;
    }

    public function closeConfigModal()
    {
        $this->isConfigModalOpen = false;
    }

    public function saveGlobalConfig()
    {
        $aktifTA = TahunAjaran::find($this->selectedYearId);
        if (! $aktifTA) {
            session()->flash('error', 'Tahun ajaran tidak ditemukan.');

            return;
        }

        $this->validate([
            'configDaftarUlang' => 'required|integer|min:0',
            'configSyahriahSem1' => 'required|integer|min:0',
            'configSyahriahSem2' => 'required|integer|min:0',
            'configMajegMakan' => 'required|integer|min:0',
        ], [
            'configDaftarUlang.min' => 'Nominal Daftar Ulang tidak boleh negatif.',
            'configSyahriahSem1.min' => 'Nominal Syahriah Sem 1 tidak boleh negatif.',
            'configSyahriahSem2.min' => 'Nominal Syahriah Sem 2 tidak boleh negatif.',
            'configMajegMakan.min' => 'Nominal Majeg Makan tidak boleh negatif.',
        ]);

        $config = $aktifTA->konfigurasiKeuangan;
        if ($config) {
            $config->update([
                'nominal_daftar_ulang' => $this->configDaftarUlang,
                'nominal_syahriah_sem1' => $this->configSyahriahSem1,
                'nominal_syahriah_sem2' => $this->configSyahriahSem2,
                'nominal_majeg_makan' => $this->configMajegMakan,
            ]);

            // Sinkronkan tagihan yang sudah ada untuk tahun ajaran terpilih
            // 1. Daftar Ulang
            Tagihan::where('tahun_ajaran_id', $aktifTA->id)
                ->where('kategori', 'daftar_ulang')
                ->where('status', '!=', 'pulang')
                ->get()
                ->each(function ($tagihan) {
                    $newTerbayar = $tagihan->nominal_terbayar;
                    if ($newTerbayar >= $this->configDaftarUlang) {
                        $newStatus = 'lunas';
                    } elseif ($newTerbayar > 0) {
                        $newStatus = 'dicicil';
                    } else {
                        $newStatus = 'belum_bayar';
                    }
                    $tagihan->update([
                        'nominal' => $this->configDaftarUlang,
                        'status' => $newStatus,
                    ]);
                });

            // 2. Syahriah Sem 1
            Tagihan::where('tahun_ajaran_id', $aktifTA->id)
                ->where('kategori', 'syahriah_sem1')
                ->where('status', '!=', 'pulang')
                ->get()
                ->each(function ($tagihan) {
                    $newTerbayar = $tagihan->nominal_terbayar;
                    if ($newTerbayar >= $this->configSyahriahSem1) {
                        $newStatus = 'lunas';
                    } elseif ($newTerbayar > 0) {
                        $newStatus = 'dicicil';
                    } else {
                        $newStatus = 'belum_bayar';
                    }
                    $tagihan->update([
                        'nominal' => $this->configSyahriahSem1,
                        'status' => $newStatus,
                    ]);
                });

            // 3. Syahriah Sem 2
            Tagihan::where('tahun_ajaran_id', $aktifTA->id)
                ->where('kategori', 'syahriah_sem2')
                ->where('status', '!=', 'pulang')
                ->get()
                ->each(function ($tagihan) {
                    $newTerbayar = $tagihan->nominal_terbayar;
                    if ($newTerbayar >= $this->configSyahriahSem2) {
                        $newStatus = 'lunas';
                    } elseif ($newTerbayar > 0) {
                        $newStatus = 'dicicil';
                    } else {
                        $newStatus = 'belum_bayar';
                    }
                    $tagihan->update([
                        'nominal' => $this->configSyahriahSem2,
                        'status' => $newStatus,
                    ]);
                });

            // 4. Majeg Makan
            Tagihan::where('tahun_ajaran_id', $aktifTA->id)
                ->where('kategori', 'majeg_makan')
                ->where('status', '!=', 'pulang')
                ->get()
                ->each(function ($tagihan) {
                    $newTerbayar = $tagihan->nominal_terbayar;
                    if ($newTerbayar >= $this->configMajegMakan) {
                        $newStatus = 'lunas';
                    } elseif ($newTerbayar > 0) {
                        $newStatus = 'dicicil';
                    } else {
                        $newStatus = 'belum_bayar';
                    }
                    $tagihan->update([
                        'nominal' => $this->configMajegMakan,
                        'status' => $newStatus,
                    ]);
                });
        }

        $this->isConfigModalOpen = false;
        session()->flash('success', 'Konfigurasi tarif default tahun ajaran ini berhasil diperbarui.');
    }

    public function export()
    {
        return Excel::download(
            new KeuanganExport($this->selectedYearId, $this->filterKamarId),
            'rekap_keuangan_tahun_'.$this->selectedYearId.'.xlsx'
        );
    }

    public function render()
    {
        $query = Santri::query()->where('status', 'aktif');

        if (! empty($this->search)) {
            $query->where('nama_lengkap', 'like', '%'.$this->search.'%');
        }

        if ($this->filterKamarId > 0) {
            $query->where('kamar_id', $this->filterKamarId);
        }

        $santris = $query->with(['kamar', 'kelas'])
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(10);

        // Ambil info detail untuk santri terpilih di modal
        $selectedSantri = null;
        $billings = [];
        if ($this->isModalOpen && $this->selectedSantriId) {
            $selectedSantri = Santri::with(['kamar', 'kelas'])->findOrFail($this->selectedSantriId);
            $billings = Tagihan::where('santri_id', $this->selectedSantriId)
                ->where('tahun_ajaran_id', $this->selectedYearId)
                ->get()
                ->sortBy(function ($bill) {
                    $categoryOrder = array_search($bill->kategori, ['daftar_ulang', 'syahriah_sem1', 'syahriah_sem2', 'majeg_makan']);
                    $monthOrder = $bill->bulan_hijri ? array_search((int) $bill->bulan_hijri, [11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9]) : -1;

                    return [$categoryOrder, $monthOrder];
                });
        }

        return view('livewire.bendahara.keuangan-manager', [
            'santris' => $santris,
            'kamars' => Kamar::all(),
            'tahunAjarans' => TahunAjaran::all(),
            'selectedSantri' => $selectedSantri,
            'billings' => $billings,
        ]);
    }
}
