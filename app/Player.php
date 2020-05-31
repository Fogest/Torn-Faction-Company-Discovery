<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    public $incrementing = false;
    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
