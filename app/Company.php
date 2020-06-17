<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $incrementing = false;

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
    public function type()
    {
        return $this->hasOne(CompanyTypes::class);
    }
}
