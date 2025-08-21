<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $table    = 'rules';
    protected $fillable = ['transaction_type', 'currency_type', 'transactions_hour', 'transactions_day', 'amount_hour', 'amount_day', 'amount_week', 'amount_month', 'same_amount', 'email_day', 'phone_day', 'ipadd_day', 'user_created_at'];
    public $timestamps  = false;
}
