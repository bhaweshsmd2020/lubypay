<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table    = 'cart';
    protected $fillable = ['product_id', 'user_id', 'quantity', 'packeging', 'store_id', 'amount', 'currency'];
    public $timestamps  = false;
}
