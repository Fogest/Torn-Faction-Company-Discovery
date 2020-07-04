<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $fillable = ['id', 'faction_id', 'name'];
    protected $dates = ['deleted_at'];

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
}
