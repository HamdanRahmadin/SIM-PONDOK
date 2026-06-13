<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['santri_id', 'tahun_ajaran', 'kategori', 'status', 'nominal_bayar', 'catatan'])]
class Keuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangans';

    protected function casts(): array
    {
        return [
            'tahun_ajaran' => 'integer',
            'nominal_bayar' => 'integer',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
