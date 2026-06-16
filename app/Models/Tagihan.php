<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['santri_id', 'tahun_ajaran_id', 'kategori', 'bulan_hijri', 'tahun_hijri', 'nominal', 'status', 'nominal_terbayar', 'catatan'])]
class Tagihan extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'tagihans';

    protected function casts(): array
    {
        return [
            'bulan_hijri' => 'integer',
            'tahun_hijri' => 'integer',
            'nominal' => 'decimal:0',
            'nominal_terbayar' => 'decimal:0',
            'sisa_tagihan' => 'decimal:0',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function cicilans(): HasMany
    {
        return $this->hasMany(Cicilan::class, 'tagihan_id');
    }
}
