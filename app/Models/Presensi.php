<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['santri_id', 'kelas_id', 'tanggal_masehi', 'bulan_hijri', 'tahun_hijri', 'sesi', 'status', 'catatan', 'dicatat_oleh'])]
class Presensi extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'presensis';

    protected function casts(): array
    {
        return [
            'tanggal_masehi' => 'date',
            'bulan_hijri' => 'integer',
            'tahun_hijri' => 'integer',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
