<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['santri_id', 'kelas_lama_id', 'kelas_baru_id', 'tahun_ajaran_id', 'dipindah_oleh'])]
class RiwayatKelas extends Model
{
    protected $table = 'riwayat_kelas';

    public $timestamps = false;

    protected static function booted()
    {
        static::creating(function ($riwayat) {
            $riwayat->created_at = now();
        });
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function kelasLama(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_lama_id');
    }

    public function kelasBaru(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_baru_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function dipindahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dipindah_oleh');
    }
}
