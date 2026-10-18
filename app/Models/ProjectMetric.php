<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMetric extends Model
{
    protected $fillable = ['project_id', 'label', 'value', 'sort_order'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
