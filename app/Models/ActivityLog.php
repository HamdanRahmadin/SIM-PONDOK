<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'nama_aktor', 'aksi', 'details'])]
class ActivityLog extends Model
{
    public $timestamps = false; // we only use created_at via db default

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $aksi, ?string $details = null): void
    {
        $user = auth()->user();
        
        self::create([
            'user_id' => $user ? $user->id : null,
            'nama_aktor' => $user ? $user->name : 'System',
            'aksi' => $aksi,
            'details' => $details,
        ]);
    }
}
