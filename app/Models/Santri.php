<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['nis', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'kamar_id', 'kelas_id', 'status', 'tanggal_masuk', 'tanggal_keluar', 'catatan'])]
class Santri extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'santris';

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'santri_id');
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'santri_id');
    }

    public function riwayatKelas(): HasMany
    {
        return $this->hasMany(RiwayatKelas::class, 'santri_id');
    }

    public function isReadOnly(): bool
    {
        return $this->status === 'lulus';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
