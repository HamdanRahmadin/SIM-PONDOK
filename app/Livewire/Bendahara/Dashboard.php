<?php

namespace App\Livewire\Bendahara;

use App\Models\Cicilan;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Dasbor Keuangan - RIBATHUL QUR'AN")]
class Dashboard extends Component
{
    public float $totalPemasukan = 0;

    public float $totalTunggakan = 0;

    public float $totalTarget = 0;

    public int $persenPemasukan = 0;

    public int $santriLunasCount = 0;

    public int $santriBelumLunasCount = 0;

    public int $persenSantriLunas = 0;

    public array $categoryMetrics = [
        'daftar_ulang' => ['terkumpul' => 0, 'tunggakan' => 0, 'target' => 0, 'persen' => 0, 'lunas' => 0, 'belum' => 0, 'pulang' => 0],
        'syahriah' => ['terkumpul' => 0, 'tunggakan' => 0, 'target' => 0, 'persen' => 0, 'lunas' => 0, 'belum' => 0, 'pulang' => 0],
        'majeg_makan' => ['terkumpul' => 0, 'tunggakan' => 0, 'target' => 0, 'persen' => 0, 'lunas' => 0, 'belum' => 0, 'pulang' => 0],
    ];

    public array $alerts = [];

    public function mount()
    {
        $this->loadMetrics();
        $this->loadAlerts();
    }

    public function loadMetrics()
    {
        $aktifTA = TahunAjaran::getAktif();
        if (! $aktifTA) {
            return;
        }

        // Ambil ID santri aktif
        $activeSantriIds = Santri::where('status', 'aktif')->pluck('id')->toArray();

        // 1. Pemasukan Terkumpul
        $this->totalPemasukan = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)
            ->whereIn('santri_id', $activeSantriIds)
            ->sum('nominal_terbayar');

        // 2. Piutang / Sisa Tunggakan (Kecuali status 'pulang' atau 'lunas')
        $this->totalTunggakan = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)
            ->whereIn('santri_id', $activeSantriIds)
            ->whereNotIn('status', ['lunas', 'pulang'])
            ->sum('sisa_tagihan');

        // 3. Target
        $this->totalTarget = $this->totalPemasukan + $this->totalTunggakan;
        $this->persenPemasukan = $this->totalTarget > 0 ? (int) round(($this->totalPemasukan / $this->totalTarget) * 100) : 0;

        // 4. Kategori: Daftar Ulang
        $duTerkumpul = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'daftar_ulang')->sum('nominal_terbayar');
        $duTunggakan = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'daftar_ulang')->whereNotIn('status', ['lunas', 'pulang'])->sum('sisa_tagihan');
        $duTarget = $duTerkumpul + $duTunggakan;
        $this->categoryMetrics['daftar_ulang'] = [
            'terkumpul' => $duTerkumpul,
            'tunggakan' => $duTunggakan,
            'target' => $duTarget,
            'persen' => $duTarget > 0 ? (int) round(($duTerkumpul / $duTarget) * 100) : 0,
            'lunas' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'daftar_ulang')->where('status', 'lunas')->count(),
            'belum' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'daftar_ulang')->whereIn('status', ['belum_bayar', 'dicicil'])->count(),
            'pulang' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'daftar_ulang')->where('status', 'pulang')->count(),
        ];

        // 5. Kategori: Syahriah (Sem 1 & Sem 2 combined)
        $syTerkumpul = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->whereIn('kategori', ['syahriah_sem1', 'syahriah_sem2'])->sum('nominal_terbayar');
        $syTunggakan = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->whereIn('kategori', ['syahriah_sem1', 'syahriah_sem2'])->whereNotIn('status', ['lunas', 'pulang'])->sum('sisa_tagihan');
        $syTarget = $syTerkumpul + $syTunggakan;
        $this->categoryMetrics['syahriah'] = [
            'terkumpul' => $syTerkumpul,
            'tunggakan' => $syTunggakan,
            'target' => $syTarget,
            'persen' => $syTarget > 0 ? (int) round(($syTerkumpul / $syTarget) * 100) : 0,
            'lunas' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->whereIn('kategori', ['syahriah_sem1', 'syahriah_sem2'])->where('status', 'lunas')->count(),
            'belum' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->whereIn('kategori', ['syahriah_sem1', 'syahriah_sem2'])->whereIn('status', ['belum_bayar', 'dicicil'])->count(),
            'pulang' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->whereIn('kategori', ['syahriah_sem1', 'syahriah_sem2'])->where('status', 'pulang')->count(),
        ];

        // 6. Kategori: Majeg Makan
        $mmTerkumpul = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'majeg_makan')->sum('nominal_terbayar');
        $mmTunggakan = (float) Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'majeg_makan')->whereNotIn('status', ['lunas', 'pulang'])->sum('sisa_tagihan');
        $mmTarget = $mmTerkumpul + $mmTunggakan;
        $this->categoryMetrics['majeg_makan'] = [
            'terkumpul' => $mmTerkumpul,
            'tunggakan' => $mmTunggakan,
            'target' => $mmTarget,
            'persen' => $mmTarget > 0 ? (int) round(($mmTerkumpul / $mmTarget) * 100) : 0,
            'lunas' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'majeg_makan')->where('status', 'lunas')->count(),
            'belum' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'majeg_makan')->whereIn('status', ['belum_bayar', 'dicicil'])->count(),
            'pulang' => Tagihan::where('tahun_ajaran_id', $aktifTA->id)->whereIn('santri_id', $activeSantriIds)->where('kategori', 'majeg_makan')->where('status', 'pulang')->count(),
        ];

        // 7. Status Pembayaran Santri
        $this->santriLunasCount = Santri::where('status', 'aktif')
            ->whereHas('tagihans', function ($query) use ($aktifTA) {
                $query->where('tahun_ajaran_id', $aktifTA->id);
            })
            ->whereDoesntHave('tagihans', function ($query) use ($aktifTA) {
                $query->where('tahun_ajaran_id', $aktifTA->id)
                    ->whereIn('status', ['belum_bayar', 'dicicil']);
            })->count();

        $this->santriBelumLunasCount = Santri::where('status', 'aktif')
            ->whereHas('tagihans', function ($query) use ($aktifTA) {
                $query->where('tahun_ajaran_id', $aktifTA->id)
                    ->whereIn('status', ['belum_bayar', 'dicicil']);
            })->count();

        $totalSantriTagihan = $this->santriLunasCount + $this->santriBelumLunasCount;
        $this->persenSantriLunas = $totalSantriTagihan > 0
            ? (int) round(($this->santriLunasCount / $totalSantriTagihan) * 100)
            : 0;
    }

    public function loadAlerts()
    {
        $this->alerts = [];
        $aktifTA = TahunAjaran::getAktif();
        if (! $aktifTA) {
            return;
        }

        // Alert: Santri nonaktif yang masih memiliki tunggakan dicicil yang belum selesai
        $nonaktifSantrisWithDebt = Santri::where('status', 'nonaktif')
            ->whereHas('tagihans', function ($query) use ($aktifTA) {
                $query->where('tahun_ajaran_id', $aktifTA->id)
                    ->where('status', 'dicicil');
            })
            ->with(['tagihans' => function ($query) use ($aktifTA) {
                $query->where('tahun_ajaran_id', $aktifTA->id)->where('status', 'dicicil');
            }])
            ->get();

        foreach ($nonaktifSantrisWithDebt as $s) {
            $categoriesList = $s->tagihans->map(fn ($t) => str_replace('_', ' ', $t->kategori))->toArray();
            $this->alerts[] = [
                'type' => 'warning',
                'message' => "Santri nonaktif \"{$s->nama_lengkap}\" masih memiliki tunggakan cicilan berjalan pada tagihan: ".implode(', ', $categoriesList).'.',
            ];
        }
    }

    public function render()
    {
        $aktifTA = TahunAjaran::getAktif();
        $recentTransactions = [];
        if ($aktifTA) {
            $recentTransactions = Cicilan::whereHas('tagihan', function ($q) use ($aktifTA) {
                $q->where('tahun_ajaran_id', $aktifTA->id);
            })
                ->with(['tagihan.santri', 'dicatatOleh'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('livewire.bendahara.dashboard', [
            'recentTransactions' => $recentTransactions,
            'tahunAjaran' => $aktifTA,
        ]);
    }
}
