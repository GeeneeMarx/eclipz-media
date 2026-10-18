<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'pillar', 'service_category_id', 'sort_order', 'is_visible'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}