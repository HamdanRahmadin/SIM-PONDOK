<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['nama', 'tahun_hijri', 'is_aktif', 'koreksi_hilal'])]
class TahunAjaran extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'tahun_ajarans';

    protected function casts(): array
    {
        return [
            'tahun_hijri' => 'integer',
            'is_aktif' => 'boolean',
            'koreksi_hilal' => 'integer',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($tahunAjaran) {
            if ($tahunAjaran->is_aktif) {
                // Set all other years to is_aktif = false
                static::where('id', '!=', $tahunAjaran->id)->update(['is_aktif' => false]);
            }
        });
    }

    public static function getAktif(): ?self
    {
        return static::where('is_aktif', true)->first();
    }

    public function konfigurasiKeuangan(): HasOne
    {
        return $this->hasOne(KonfigurasiKeuangan::class, 'tahun_ajaran_id');
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'tahun_ajaran_id');
    }

    public function liburMassals(): HasMany
    {
        return $this->hasMany(LiburMassal::class, 'tahun_ajaran_id');
    }
}
