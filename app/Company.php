<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $fillable = ['id', 'name', 'player_id', 'company_type', 'rank', 'hired_employees', 'max_employees'];
    protected $dates = ['deleted_at'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
    public function type()
    {
        return $this->hasOne(CompanyTypes::class, 'id', 'company_type');
    }
}
