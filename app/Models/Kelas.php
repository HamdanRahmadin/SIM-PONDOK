<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['nama_kelas', 'ustaz_id', 'urutan'])]
class Kelas extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'kelas';

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function ustaz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustaz_id');
    }

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }
}
