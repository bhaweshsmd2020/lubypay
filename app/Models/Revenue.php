<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $table    = 'revenues';
    protected $fillable = ['transaction_id', 'transactional','operational', 'operational_a', 'operational_b', 'currency_id'];
    public $timestamps  = false;
}
