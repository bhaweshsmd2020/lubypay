<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesLimit extends Model
{
    protected $table    = 'fees_limits';
    protected $fillable = [
        'currency_id',
        'transaction_type_id',
        'transaction_type',
        'payment_method_id',
        'subscription_id',
        'min_balance',
        'has_transaction',
        'recom_amt',
        'description',      
        'min_limit',
        'max_limit',
        'charge_percentage',
        'charge_fixed',
        'second_min_limit',
        'second_max_limit',
        'second_charge_percentage',
        'second_charge_fixed',
        'card_limit',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
