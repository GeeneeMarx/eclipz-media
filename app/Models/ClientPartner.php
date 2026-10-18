<?php

namespace App\Models;

use App\Enums\ClientPartnerType;
use Illuminate\Database\Eloquent\Model;

class ClientPartner extends Model
{
    protected $table = 'clients_partners';

    protected $fillable = [
        'name',
        'type',
        'logo',
        'industry_id',
        'website',
        'display_scale',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ClientPartnerType::class,
            'is_visible' => 'boolean',
            'display_scale' => 'decimal:2',
        ];
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}