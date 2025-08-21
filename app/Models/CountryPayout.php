<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryPayout extends Model
{
    protected $table    = 'countries_payout';
    protected $fillable = ['payout_method', 'country', 'sort_by', 'status', 'required'];
    public $timestamps  = false;
}
