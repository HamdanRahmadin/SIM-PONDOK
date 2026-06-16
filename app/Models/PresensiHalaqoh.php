<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PresensiHalaqoh extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'santri_id',
        'kelas_id',
        'tanggal_masehi',
        'bulan_hijri',
        'tahun_hijri',
        'status',
        'catatan',
        'dicatat_oleh',
    ];

    protected $auditEvents = ['created', 'updated'];

    protected $auditInclude = ['status', 'catatan'];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
