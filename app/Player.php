<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'faction_id', 'name'];

    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function isOwner()
    {
        return $this->company()->where('isOwner', true)->get('isOwner');
    }

    public function recruited()
    {
        return $this->hasMany(PlayerRecruit::class);
    }

    public function times()
    {
        return $this->hasMany(Time::class);
    }
}
