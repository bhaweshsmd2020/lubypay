<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\Common;

class CollectPayment extends Model
{
    protected $table = 'collect_payments';
    protected static $helper;

    protected $fillable = ['user_id','store_id', 'currency_id','store_user_id', 'payment_method_id', 'bank_id', 'file_id', 'uuid', 'charge_percentage', 'charge_fixed', 'amount', 'status'];

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'transaction_reference_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

 
}
