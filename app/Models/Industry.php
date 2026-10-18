<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $fillable = ['name', 'slug'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function clientsPartners()
    {
        return $this->hasMany(ClientPartner::class);
    }
}
