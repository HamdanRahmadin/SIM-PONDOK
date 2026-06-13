<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kelas'])]
class Kelas extends Model
{
    use HasFactory;

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class);
    }
}
