<?php

namespace App\Livewire\Bendahara;

use App\Models\Keuangan;
use App\Models\Santri;
use App\Models\Setting;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Bendahara Dashboard')]
class Dashboard extends Component
{
    public array $metrics = [
        'daftar_ulang' => ['lunas' => 0, 'dicicil' => 0, 'belum_bayar' => 0],
        'majeg_makan' => ['lunas' => 0, 'dicicil' => 0, 'belum_bayar' => 0],
        'syahriah_sem_2' => ['lunas' => 0, 'dicicil' => 0, 'belum_bayar' => 0],
    ];

    public array $alerts = [];

    public function mount()
    {
        $this->loadMetrics();
        $this->loadAlerts();
    }

    public function loadMetrics()
    {
        $currentYear = Setting::getByKey('current_tahun_ajaran', 1447);

        // 1. Daftar Ulang Metrics
        $this->metrics['daftar_ulang'] = [
            'lunas' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'daftar_ulang')->where('status', 'lunas')->count(),
            'dicicil' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'daftar_ulang')->where('status', 'dicicil')->count(),
            'belum_bayar' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'daftar_ulang')->where('status', 'belum_bayar')->count(),
        ];

        // 2. Majeg Makan Metrics (aggregated over all majeg_makan_1 to _10 categories)
        $this->metrics['majeg_makan'] = [
            'lunas' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'like', 'majeg_makan_%')->where('status', 'lunas')->count(),
            'dicicil' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'like', 'majeg_makan_%')->where('status', 'dicicil')->count(),
            'belum_bayar' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'like', 'majeg_makan_%')->where('status', 'belum_bayar')->count(),
        ];

        // 3. Syahriah Sem 2 Metrics
        $this->metrics['syahriah_sem_2'] = [
            'lunas' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'syahriah_semester_2')->where('status', 'lunas')->count(),
            'dicicil' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'syahriah_semester_2')->where('status', 'dicicil')->count(),
            'belum_bayar' => Keuangan::where('tahun_ajaran', $currentYear)->where('kategori', 'syahriah_semester_2')->where('status', 'belum_bayar')->count(),
        ];
    }

    public function loadAlerts()
    {
        $this->alerts = [];
        $currentYear = Setting::getByKey('current_tahun_ajaran', 1447);

        // Alert A: Active students missing billing records
        $activeSantris = Santri::where('status', 'aktif')->get();
        $missingBillingCount = 0;
        foreach ($activeSantris as $s) {
            $hasBills = Keuangan::where('santri_id', $s->id)->where('tahun_ajaran', $currentYear)->exists();
            if (!$hasBills) {
                $missingBillingCount++;
            }
        }
        if ($missingBillingCount > 0) {
            $this->alerts[] = [
                'type' => 'info',
                'message' => "Terdapat {$missingBillingCount} santri baru di-input oleh Admin yang membutuhkan inisiasi lembar keuangan."
            ];
        }

        // Alert B: Nonactive students who still have outstanding installment ('dicicil') bills
        $nonactiveSantrisWithOutstanding = Santri::where('status', 'nonaktif')
            ->whereHas('keuangans', function ($query) use ($currentYear) {
                $query->where('tahun_ajaran', $currentYear)
                      ->where('status', 'dicicil');
            })
            ->with(['keuangans' => function ($query) use ($currentYear) {
                $query->where('tahun_ajaran', $currentYear)->where('status', 'dicicil');
            }])
            ->get();

        foreach ($nonactiveSantrisWithOutstanding as $s) {
            $categoriesList = $s->keuangans->map(fn($k) => str_replace('_', ' ', $k->kategori))->toArray();
            $this->alerts[] = [
                'type' => 'warning',
                'message' => "Santri \"{$s->nama_lengkap}\" berstatus Nonaktif tetapi masih memiliki sisa tagihan dicicil pada: " . implode(', ', $categoriesList) . "."
            ];
        }
    }

    public function confirmTransaction(int $id)
    {
        // Simple checklist confirmation (visual cue) to prevent human errors
        $keuangan = Keuangan::findOrFail($id);
        ActivityLog::log("Konfirmasi Transaksi", "Bendahara mengonfirmasi transaksi kategori {$keuangan->kategori} santri {$keuangan->santri->nama_lengkap}");
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Transaksi berhasil dikonfirmasi.']);
    }

    public function render()
    {
        $currentYear = Setting::getByKey('current_tahun_ajaran', 1447);
        $recentTransactions = Keuangan::where('tahun_ajaran', $currentYear)
            ->where('status', '!=', 'belum_bayar')
            ->with('santri')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.bendahara.dashboard', [
            'recentTransactions' => $recentTransactions
        ]);
    }
}
