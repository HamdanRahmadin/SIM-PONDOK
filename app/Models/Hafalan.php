<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['santri_id', 'bulan_hijriah', 'tahun_hijriah', 'hafalan_text'])]
class Hafalan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'bulan_hijriah' => 'integer',
            'tahun_hijriah' => 'integer',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
