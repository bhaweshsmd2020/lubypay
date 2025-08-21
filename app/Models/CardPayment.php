<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Transaction;
use DB;
use Illuminate\Database\Eloquent\Model;

class CardPayment extends Model
{
    protected $table    = 'card_payments';
    protected $fillable = ['user_id', 'reference_number','currency_id', 'payment_method_id', 'uuid', 'charge_percentage', 'charge_fixed', 'amount', 'status', 'trans_type', 'from_card', 'to_card', 'local_tran_time', 'ip_address'];
}
