<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    public $incrementing = false;
    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
