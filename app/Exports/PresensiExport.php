<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Santri;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected int $kelasId;

    protected int $bulanHijri;

    protected int $tahunHijri;

    private int $rowNumber = 0;

    public function __construct(int $kelasId, int $bulanHijri, int $tahunHijri)
    {
        $this->kelasId = $kelasId;
        $this->bulanHijri = $bulanHijri;
        $this->tahunHijri = $tahunHijri;
    }

    public function collection()
    {
        return Santri::where('kelas_id', $this->kelasId)
            ->where('status', 'aktif')
            ->with(['kelas', 'kamar'])
            ->orderBy('nama_lengkap', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Santri',
            'Kelas',
            'Kamar',
            'Total Hadir',
            'Total Izin/Sakit',
            'Total Alfa',
            'Total Sesi Aktif',
            '% Kehadiran',
            'Catatan Khusus',
        ];
    }

    /**
     * @param  Santri  $row
     */
    public function map($row): array
    {
        $this->rowNumber++;

        // Fetch presensi counts for this santri in this class, month, and year
        $presensis = Presensi::where('santri_id', $row->id)
            ->where('bulan_hijri', $this->bulanHijri)
            ->where('tahun_hijri', $this->tahunHijri)
            ->get();

        $totalHadir = $presensis->where('status', 'hadir')->count();
        $totalIzinSakit = $presensis->where('status', 'izin_sakit')->count();
        $totalAlfa = $presensis->where('status', 'alfa')->count();
        $totalSesi = $presensis->count();

        $persentase = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100, 1).'%' : '0%';

        // Gather any notes
        $catatanList = $presensis->whereNotNull('catatan')->filter(fn ($c) => trim($c) !== '')->pluck('catatan')->unique()->implode(', ');

        return [
            $this->rowNumber,
            $row->nama_lengkap,
            $row->kelas->nama_kelas ?? 'Tanpa Kelas',
            $row->kamar->nama_kamar ?? 'Tanpa Kamar',
            $totalHadir,
            $totalIzinSakit,
            $totalAlfa,
            $totalSesi,
            $persentase,
            $catatanList ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
