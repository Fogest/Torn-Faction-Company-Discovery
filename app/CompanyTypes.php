<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyTypes extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    public function company()
    {
        return $this->hasMany(Company::class);
    }
}
