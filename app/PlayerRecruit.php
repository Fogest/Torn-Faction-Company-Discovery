<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerRecruit extends Model
{
    protected $fillable = ['player_id', 'faction_id', 'player_name',
        'faction_name', 'is_required_stats', 'is_accepted',
        'recruited_by_id', 'recruited_by'];

    public function recruiter()
    {
        return $this->hasOne(Player::class, 'id', 'recruited_by_id');
    }
}
