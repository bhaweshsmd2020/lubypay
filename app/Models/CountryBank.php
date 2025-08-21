<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryBank extends Model
{
    protected $table    = 'countries_bank';
    protected $fillable = ['user_id', 'country_id', 'bank'];
    public $timestamps  = false;
}
