<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tagihan_id', 'nominal', 'catatan', 'dicatat_oleh'])]
class Cicilan extends Model
{
    protected $table = 'cicilans';

    public $timestamps = false;

    protected static function booted()
    {
        static::creating(function ($cicilan) {
            $cicilan->created_at = now();
        });
    }

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:0',
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
