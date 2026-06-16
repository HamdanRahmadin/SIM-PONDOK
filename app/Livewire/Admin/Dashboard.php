<?php

namespace App\Livewire\Admin;

use App\Exports\PresensiExport;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\RiwayatKelas;
use App\Models\Santri;
use App\Models\TahunAjaran;
use App\Services\HijriCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Title("Dasbor Admin - RIBATHUL QUR'AN")]
class Dashboard extends Component
{
    // Hilal & Calendar properties
    public string $formattedHijriDate = '';

    public int $hilalOffset = 0;

    // Kenaikan Kelas Massal properties
    public int $kelasAsalId = 0;

    public int $kelasTujuanId = 0;

    public array $selectedSantriIds = [];

    public bool $isKenaikanModalOpen = false;

    public bool $isConfirmModalOpen = false;

    public string $confirmMessage = '';

    // Export Presensi properties
    public bool $isExportPresensiModalOpen = false;

    public int $exportKelasId = 0;

    public int $exportBulanHijri = 1;

    public int $exportTahunHijri = 1447;

    public function mount()
    {
        $this->loadCalendarInfo();
    }

    public function loadCalendarInfo()
    {
        $hijriService = app(HijriCalendarService::class);
        $this->formattedHijriDate = $hijriService->today()['formatted'];
        $this->hilalOffset = $hijriService->getHijriOffset();
    }

    /**
     * Koreksi Hilal Incremental (+1 / -1)
     */
    public function adjustHilal(int $diff)
    {
        $aktif = TahunAjaran::getAktif();
        if ($aktif) {
            $newOffset = max(-3, min(3, $aktif->koreksi_hilal + $diff));
            $aktif->update(['koreksi_hilal' => $newOffset]);
            $this->loadCalendarInfo();
            session()->flash('success', "Koreksi Hilal disesuaikan menjadi {$newOffset} hari.");
        }
    }

    /**
     * Hitung rekap 5 hari kehadiran untuk grafik batang
     */
    public function getAttendanceGraphData(): array
    {
        $data = [];
        Carbon::setLocale('id');

        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();

            $totalSantri = Santri::where('status', 'aktif')->count();

            $pagiHadir = Presensi::where('tanggal_masehi', $dateStr)->where('sesi', 'pagi')->where('status', 'hadir')->count();
            $malamHadir = Presensi::where('tanggal_masehi', $dateStr)->where('sesi', 'malam')->where('status', 'hadir')->count();

            $pagiPercent = $totalSantri > 0 ? ($pagiHadir / $totalSantri) * 100 : 0;
            $malamPercent = $totalSantri > 0 ? ($malamHadir / $totalSantri) * 100 : 0;

            $data[] = [
                'day_name' => $date->translatedFormat('l'),
                'pagi' => round($pagiPercent),
                'malam' => round($malamPercent),
            ];
        }

        return $data;
    }

    // MODAL KENAIKAN KELAS MASSAL ACTIONS
    public function openKenaikanModal()
    {
        $this->kelasAsalId = 0;
        $this->kelasTujuanId = 0;
        $this->selectedSantriIds = [];
        $this->isKenaikanModalOpen = true;
    }

    public function closeKenaikanModal()
    {
        $this->isKenaikanModalOpen = false;
        $this->isConfirmModalOpen = false;
    }

    public function getKenaikanStudents()
    {
        if ($this->kelasAsalId <= 0) {
            return [];
        }

        return Santri::where('kelas_id', $this->kelasAsalId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
    }

    public function triggerKenaikanProcess()
    {
        $count = count($this->selectedSantriIds);
        if ($count === 0) {
            session()->flash('error', 'Silakan pilih minimal 1 santri untuk dipindahkan.');

            return;
        }
        if ($this->kelasTujuanId === 0) {
            session()->flash('error', 'Silakan pilih kelas tujuan kenaikan.');

            return;
        }
        if ($this->kelasAsalId === $this->kelasTujuanId) {
            session()->flash('error', 'Kelas asal dan kelas tujuan tidak boleh sama.');

            return;
        }

        if ($this->kelasTujuanId == -1) {
            $targetKelasName = 'Lulus (Alumni)';
        } else {
            $kelasTujuan = Kelas::find($this->kelasTujuanId);
            $targetKelasName = $kelasTujuan ? $kelasTujuan->nama_kelas : 'Kelas tidak ditemukan';
        }
        $this->confirmMessage = "{$count} santri akan dipindahkan ke {$targetKelasName}. Lanjutkan?";
        $this->isConfirmModalOpen = true;
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }

    public function confirmKenaikanMassal()
    {
        $aktifTA = TahunAjaran::getAktif();
        $taId = $aktifTA ? $aktifTA->id : 1;

        DB::transaction(function () use ($taId) {
            $students = Santri::whereIn('id', $this->selectedSantriIds)->lockForUpdate()->get();

            foreach ($students as $student) {
                $oldKelasId = $student->kelas_id;

                if ($this->kelasTujuanId == -1) {
                    $student->update([
                        'status' => 'lulus',
                        'kelas_id' => null,
                        'tanggal_keluar' => now(),
                    ]);

                    RiwayatKelas::create([
                        'santri_id' => $student->id,
                        'kelas_lama_id' => $oldKelasId,
                        'kelas_baru_id' => null,
                        'tahun_ajaran_id' => $taId,
                        'dipindah_oleh' => Auth::id(),
                    ]);
                } else {
                    $student->update([
                        'kelas_id' => $this->kelasTujuanId,
                    ]);

                    RiwayatKelas::create([
                        'santri_id' => $student->id,
                        'kelas_lama_id' => $oldKelasId,
                        'kelas_baru_id' => $this->kelasTujuanId,
                        'tahun_ajaran_id' => $taId,
                        'dipindah_oleh' => Auth::id(),
                    ]);
                }
            }
        });

        session()->flash('success', count($this->selectedSantriIds).' santri berhasil dipindahkan.');
        $this->closeKenaikanModal();
    }

    public function openExportPresensiModal()
    {
        $this->exportKelasId = Kelas::first()?->id ?? 0;
        $aktif = TahunAjaran::getAktif();
        $this->exportTahunHijri = $aktif ? $aktif->tahun_hijri : 1447;

        $currentHijri = app(HijriCalendarService::class)->today();
        $this->exportBulanHijri = (int) $currentHijri['month'];

        $this->isExportPresensiModalOpen = true;
    }

    public function closeExportPresensiModal()
    {
        $this->isExportPresensiModalOpen = false;
    }

    public function exportPresensi()
    {
        $this->validate([
            'exportKelasId' => 'required|exists:kelas,id',
            'exportBulanHijri' => 'required|integer|between:1,12',
            'exportTahunHijri' => 'required|integer|min:1400',
        ]);

        $kelas = Kelas::find($this->exportKelasId);
        $filename = 'laporan_presensi_kelas_'.str_replace(' ', '_', $kelas->nama_kelas).'_bulan_'.$this->exportBulanHijri.'_tahun_'.$this->exportTahunHijri.'.xlsx';

        $this->isExportPresensiModalOpen = false;

        return Excel::download(
            new PresensiExport($this->exportKelasId, $this->exportBulanHijri, $this->exportTahunHijri),
            $filename
        );
    }

    public function render()
    {
        $aktifTA = TahunAjaran::getAktif();

        return view('livewire.admin.dashboard', [
            'santriAktifCount' => Santri::where('status', 'aktif')->count(),
            'santriNonaktifCount' => Santri::where('status', 'nonaktif')->count(),
            'kelasCount' => Kelas::count(),
            'kamarCount' => Kamar::count(),
            'graphData' => $this->getAttendanceGraphData(),
            'kelases' => Kelas::orderBy('urutan', 'asc')->get(),
        ]);
    }
}
