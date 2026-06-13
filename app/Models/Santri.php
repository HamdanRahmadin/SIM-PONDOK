<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'status', 'kelas_id'])]
class Santri extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function hafalans(): HasMany
    {
        return $this->hasMany(Hafalan::class);
    }

    public function keuangans(): HasMany
    {
        return $this->hasMany(Keuangan::class);
    }

    /**
     * Check if the student is read-only (Alumni / Lulus)
     */
    public function isReadOnly(): bool
    {
        return $this->status === 'lulus';
    }
}
