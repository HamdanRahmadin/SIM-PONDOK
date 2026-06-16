<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['nama_kamar', 'keterangan'])]
class Kamar extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'kamar';

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class, 'kamar_id');
    }
}
