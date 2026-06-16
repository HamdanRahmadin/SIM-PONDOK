<?php

namespace App\Livewire\Ustaz;

use App\Exports\LaporanHalaqohExport;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\PresensiHalaqoh;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Title("Riwayat Presensi - Ribathul Qur'an")]
class RiwayatPresensi extends Component
{
    public string $activeTab = 'setoran';

    public string $tanggal = '';

    public int $kelas_id = 0;

    public string $sesi = 'semua';

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->kelas_id = session('active_kelas_id', Kelas::orderBy('urutan')->value('id') ?? 0);
    }

    public function updatedKelasId($value)
    {
        session(['active_kelas_id' => $value]);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function exportHalaqoh()
    {
        if ($this->kelas_id == 0) {
            return;
        }

        $bulan = date('m', strtotime($this->tanggal));
        $tahun = date('Y', strtotime($this->tanggal));

        return Excel::download(
            new LaporanHalaqohExport($this->kelas_id, $bulan, $tahun),
            "Laporan_Halaqoh_Kelas_{$this->kelas_id}_{$bulan}_{$tahun}.xlsx"
        );
    }

    public function render()
    {
        $kelasList = Kelas::orderBy('urutan')->get();

        $riwayatData = collect();

        if ($this->kelas_id > 0) {
            if ($this->activeTab === 'setoran') {
                $query = Presensi::with('santri')
                    ->where('kelas_id', $this->kelas_id)
                    ->where('tanggal_masehi', $this->tanggal);

                if ($this->sesi !== 'semua') {
                    $query->where('sesi', $this->sesi);
                }

                $riwayatData = $query->orderBy('sesi')->get();
            } else {
                $riwayatData = PresensiHalaqoh::with('santri')
                    ->where('kelas_id', $this->kelas_id)
                    ->where('tanggal_masehi', $this->tanggal)
                    ->get();
            }
        }

        return view('livewire.ustaz.riwayat-presensi', compact('kelasList', 'riwayatData'))
            ->layout('components.layouts.app');
    }
}
