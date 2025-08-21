<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeIntent extends Model
{
    protected $table    = 'stripe_intents';
    protected $fillable = ['user_id', 'cus_id', 'intent_id', 'currency_id'];
    public $timestamps  = false;
}
