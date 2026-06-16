<?php

namespace App\Exports;

use App\Models\Santri;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KeuanganExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected int $yearId;

    protected int $kamarId;

    private int $rowNumber = 0;

    public function __construct(int $yearId, int $kamarId)
    {
        $this->yearId = $yearId;
        $this->kamarId = $kamarId;
    }

    public function collection()
    {
        $query = Santri::query()->where('status', 'aktif');

        if ($this->kamarId > 0) {
            $query->where('kamar_id', $this->kamarId);
        }

        return $query->with(['kamar', 'kelas', 'tagihans' => function ($q) {
            $q->where('tahun_ajaran_id', $this->yearId);
        }])->orderBy('nama_lengkap', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Santri',
            'Kamar',
            'Daftar Ulang (Terbayar)',
            'Daftar Ulang (Sisa)',
            'Syahriah Sem 1 (Terbayar)',
            'Syahriah Sem 1 (Sisa)',
            'Syahriah Sem 2 (Terbayar)',
            'Syahriah Sem 2 (Sisa)',
            'Majeg Makan (Terbayar)',
            'Majeg Makan (Sisa)',
            'Total Sisa Tagihan',
        ];
    }

    /**
     * @param  Santri  $row
     */
    public function map($row): array
    {
        $this->rowNumber++;

        $tags = $row->tagihans;

        $du = $tags->where('kategori', 'daftar_ulang')->first();
        $s1 = $tags->where('kategori', 'syahriah_sem1')->first();
        $s2 = $tags->where('kategori', 'syahriah_sem2')->first();
        $mm = $tags->where('kategori', 'majeg_makan');

        $duTerbayar = $du ? (int) $du->nominal_terbayar : 0;
        $duSisa = $du ? (int) $du->sisa_tagihan : 0;

        $s1Terbayar = $s1 ? (int) $s1->nominal_terbayar : 0;
        $s1Sisa = $s1 ? (int) $s1->sisa_tagihan : 0;

        $s2Terbayar = $s2 ? (int) $s2->nominal_terbayar : 0;
        $s2Sisa = $s2 ? (int) $s2->sisa_tagihan : 0;

        $mmTerbayar = $mm->sum('nominal_terbayar');
        $mmSisa = $mm->sum('sisa_tagihan');

        $totalSisa = $duSisa + $s1Sisa + $s2Sisa + $mmSisa;

        return [
            $this->rowNumber,
            $row->nama_lengkap,
            $row->kamar->nama_kamar ?? 'Tanpa Kamar',
            $duTerbayar,
            $duSisa,
            $s1Terbayar,
            $s1Sisa,
            $s2Terbayar,
            $s2Sisa,
            $mmTerbayar,
            $mmSisa,
            $totalSisa,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
