<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table    = 'services';
    protected $fillable = ['name', 'image','page','status','sorting','position'];
    public $timestamps  = false;

    // public function user_detail() 
    // {
    //     return $this->hasOne(UserDetail::class, 'country_id');
    // }

    // public function bank()
    // {
    //     return $this->hasOne(Bank::class, 'country_id');
    // }
}
