<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['santri_id', 'tanggal_gregorian', 'tanggal_hijriah', 'bulan_hijriah', 'tahun_hijriah', 'sesi', 'status', 'catatan_setoran'])]
class Presensi extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal_gregorian' => 'date',
            'bulan_hijriah' => 'integer',
            'tahun_hijriah' => 'integer',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
