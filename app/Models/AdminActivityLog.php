<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Audit trail — intentionally has no update/delete usage anywhere in the
 * application. Records are only ever inserted, via App\Services\ActivityLogger.
 */
class AdminActivityLog extends Model
{
    public $timestamps = false; // only created_at, set via DB default

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
