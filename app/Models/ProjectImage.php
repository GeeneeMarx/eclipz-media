<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    protected $fillable = ['project_id', 'path', 'alt_text', 'sort_order'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
