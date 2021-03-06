<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'name', 'player_id', 'company_type', 'rank', 'hired_employees', 'max_employees'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
    public function type()
    {
        return $this->hasOne(CompanyTypes::class, 'id', 'company_type');
    }
}
