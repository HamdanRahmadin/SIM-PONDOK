<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\PresensiHalaqoh;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanHalaqohExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    protected $kelas_id;

    protected $bulan;

    protected $tahun;

    public function __construct($kelas_id, $bulan, $tahun)
    {
        $this->kelas_id = $kelas_id;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return PresensiHalaqoh::with(['santri', 'kelas'])
            ->where('kelas_id', $this->kelas_id)
            ->whereMonth('tanggal_masehi', $this->bulan)
            ->whereYear('tanggal_masehi', $this->tahun)
            ->orderBy('tanggal_masehi')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Masehi',
            'Bulan Hijriah',
            'Tahun Hijriah',
            'Nama Santri',
            'Kelas',
            'Status',
            'Catatan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal_masehi,
            $row->bulan_hijri,
            $row->tahun_hijri,
            $row->santri->nama_lengkap ?? '-',
            $row->kelas->nama_kelas ?? '-',
            strtoupper($row->status),
            $row->catatan,
        ];
    }

    public function title(): string
    {
        $kelas = Kelas::find($this->kelas_id);

        return 'Rekap Halaqoh '.($kelas ? $kelas->nama_kelas : '');
    }
}
