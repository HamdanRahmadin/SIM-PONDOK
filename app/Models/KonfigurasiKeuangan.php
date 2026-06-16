<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['tahun_ajaran_id', 'nominal_daftar_ulang', 'nominal_syahriah_sem1', 'nominal_syahriah_sem2', 'nominal_majeg_makan', 'catatan'])]
class KonfigurasiKeuangan extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

    protected $table = 'konfigurasi_keuangans';

    protected function casts(): array
    {
        return [
            'nominal_daftar_ulang' => 'decimal:0',
            'nominal_syahriah_sem1' => 'decimal:0',
            'nominal_syahriah_sem2' => 'decimal:0',
            'nominal_majeg_makan' => 'decimal:0',
        ];
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
