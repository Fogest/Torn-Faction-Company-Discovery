<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'name', 'current_players', 'max_players'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
