<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Presensi;
use App\Models\Keuangan;
use App\Helpers\HijriHelper;
use Livewire\Component;
use Livewire\Attributes\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public int $selectedMonth;
    public int $selectedKelasId = 0;
    
    public function mount()
    {
        // Default to current Hijri month
        $hijriToday = HijriHelper::gregorianToHijri(date('Y-m-d'));
        $this->selectedMonth = $hijriToday['month'];
        
        $firstKelas = Kelas::first();
        if ($firstKelas) {
            $this->selectedKelasId = $firstKelas->id;
        }
    }

    public function getChartData()
    {
        // Aggregate presence statistics for Pagi and Malam for the selected month
        $stats = [
            'pagi' => ['hadir' => 0, 'alfa' => 0, 'izin_sakit' => 0],
            'malam' => ['hadir' => 0, 'alfa' => 0, 'izin_sakit' => 0]
        ];

        $presensi = Presensi::where('bulan_hijriah', $this->selectedMonth)->get();

        foreach ($presensi as $p) {
            $sesi = $p->sesi === 'malam' ? 'malam' : 'pagi';
            if ($p->status === 'hadir') {
                $stats[$sesi]['hadir']++;
            } elseif ($p->status === 'alfa') {
                $stats[$sesi]['alfa']++;
            } elseif ($p->status === 'izin' || $p->status === 'sakit') {
                $stats[$sesi]['izin_sakit']++;
            }
        }

        return $stats;
    }

    public function exportXlsx()
    {
        $kelas = Kelas::find($this->selectedKelasId);
        $kelasName = $kelas ? $kelas->nama_kelas : 'Semua';
        
        $monthNames = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awwal', 4 => 'Rabiul Akhir',
            5 => 'Jumadil Awwal', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => 'Sya\'ban',
            9 => 'Ramadhan', 10 => 'Syawal', 11 => 'Dzulqa\'dah', 12 => 'Dzulhijjah'
        ];
        $monthName = $monthNames[$this->selectedMonth] ?? 'Bulan';

        // 1. Fetch Students
        $query = Santri::query();
        if ($this->selectedKelasId > 0) {
            $query->where('kelas_id', $this->selectedKelasId);
        }
        $santris = $query->with('kelas')->get();

        // 2. Initialize Spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Akademik & Presensi
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Laporan Akademik');
        
        // Headers
        $sheet1->setCellValue('A1', 'LAPORAN AKADEMIK & PRESENSI SANTRI');
        $sheet1->setCellValue('A2', "Kelas: $kelasName | Bulan Hijriah: $monthName");
        $sheet1->mergeCells('A1:G1');
        $sheet1->mergeCells('A2:G2');
        
        $sheet1->setCellValue('A4', 'No');
        $sheet1->setCellValue('B4', 'Nama Lengkap');
        $sheet1->setCellValue('C4', 'Kelas');
        $sheet1->setCellValue('D4', 'Status Santri');
        $sheet1->setCellValue('E4', 'Sesi Pagi (Hadir / Alfa)');
        $sheet1->setCellValue('F4', 'Sesi Malam (Hadir / Alfa)');
        $sheet1->setCellValue('G4', 'Total Hadir');
        
        $row = 5;
        foreach ($santris as $index => $santri) {
            // Count Attendance
            $hadirPagi = Presensi::where('santri_id', $santri->id)->where('bulan_hijriah', $this->selectedMonth)->where('sesi', 'pagi')->where('status', 'hadir')->count();
            $alfaPagi = Presensi::where('santri_id', $santri->id)->where('bulan_hijriah', $this->selectedMonth)->where('sesi', 'pagi')->where('status', 'alfa')->count();
            
            $hadirMalam = Presensi::where('santri_id', $santri->id)->where('bulan_hijriah', $this->selectedMonth)->where('sesi', 'malam')->where('status', 'hadir')->count();
            $alfaMalam = Presensi::where('santri_id', $santri->id)->where('bulan_hijriah', $this->selectedMonth)->where('sesi', 'malam')->where('status', 'alfa')->count();
            
            $sheet1->setCellValue("A$row", $index + 1);
            $sheet1->setCellValue("B$row", $santri->nama_lengkap);
            $sheet1->setCellValue("C$row", $santri->kelas->nama_kelas ?? '-');
            $sheet1->setCellValue("D$row", ucfirst($santri->status));
            $sheet1->setCellValue("E$row", "$hadirPagi Hadir / $alfaPagi Alfa");
            $sheet1->setCellValue("F$row", "$hadirMalam Hadir / $alfaMalam Alfa");
            $sheet1->setCellValue("G$row", $hadirPagi + $hadirMalam);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Keuangan
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Laporan Keuangan');
        
        $sheet2->setCellValue('A1', 'LAPORAN STATUS KEUANGAN SANTRI');
        $sheet2->setCellValue('A2', "Kelas: $kelasName | Tahun Ajaran Hijriah: 1447");
        $sheet2->mergeCells('A1:G1');
        $sheet2->mergeCells('A2:G2');
        
        $sheet2->setCellValue('A4', 'No');
        $sheet2->setCellValue('B4', 'Nama Lengkap');
        $sheet2->setCellValue('C4', 'Daftar Ulang');
        $sheet2->setCellValue('D4', 'Syahriah Dzulqa\'dah');
        $sheet2->setCellValue('E4', 'Syahriah Sem 1');
        $sheet2->setCellValue('F4', 'Syahriah Sem 2');
        $sheet2->setCellValue('G4', 'Majeg Makan (Lunas / Total Bulan)');
        
        $row = 5;
        foreach ($santris as $index => $santri) {
            $daftarUlang = Keuangan::where('santri_id', $santri->id)->where('kategori', 'daftar_ulang')->first();
            $syahriahDzul = Keuangan::where('santri_id', $santri->id)->where('kategori', 'syahriah_dzulqadah')->first();
            $syahriahSem1 = Keuangan::where('santri_id', $santri->id)->where('kategori', 'syahriah_semester_1')->first();
            $syahriahSem2 = Keuangan::where('santri_id', $santri->id)->where('kategori', 'syahriah_semester_2')->first();
            
            // Count Majeg Makan
            $majegLunas = Keuangan::where('santri_id', $santri->id)->where('kategori', 'like', 'majeg_makan_%')->where('status', 'lunas')->count();
            
            $sheet2->setCellValue("A$row", $index + 1);
            $sheet2->setCellValue("B$row", $santri->nama_lengkap);
            $sheet2->setCellValue("C$row", $daftarUlang ? strtoupper(str_replace('_', ' ', $daftarUlang->status)) : '-');
            $sheet2->setCellValue("D$row", $syahriahDzul ? strtoupper(str_replace('_', ' ', $syahriahDzul->status)) : '-');
            $sheet2->setCellValue("E$row", $syahriahSem1 ? strtoupper(str_replace('_', ' ', $syahriahSem1->status)) : '-');
            $sheet2->setCellValue("F$row", $syahriahSem2 ? strtoupper(str_replace('_', ' ', $syahriahSem2->status)) : '-');
            $sheet2->setCellValue("G$row", "$majegLunas / 10 Lunas");
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "Laporan_SIM_Pondok_Kelas_{$kelasName}_Bulan_{$monthName}.xlsx";

        ActivityLog::log("Ekspor Laporan XLSX", "Mengekspor laporan kelas {$kelasName} bulan {$monthName}");

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function render()
    {
        $monthNames = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awwal', 4 => 'Rabiul Akhir',
            5 => 'Jumadil Awwal', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => 'Sya\'ban',
            9 => 'Ramadhan', 10 => 'Syawal', 11 => 'Dzulqa\'dah', 12 => 'Dzulhijjah'
        ];

        return view('livewire.admin.dashboard', [
            'chartData' => $this->getChartData(),
            'logs' => ActivityLog::orderBy('created_at', 'desc')->take(10)->get(),
            'kelases' => Kelas::all(),
            'monthNames' => $monthNames
        ]);
    }
}
