<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudyDetail extends Model
{
    protected $fillable = ['project_id', 'challenge', 'strategy', 'execution'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
