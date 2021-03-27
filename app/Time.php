<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $fillable = ['player_id', 'event_id', 'event_name',
        'recurring', 'multiple_per_day', 'day_of_week', 'event_date_time'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
