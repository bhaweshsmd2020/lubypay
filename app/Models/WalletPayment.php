<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Transaction;
use DB;
use Illuminate\Database\Eloquent\Model;

class WalletPayment extends Model
{
    protected $table    = 'wallet_payments';
    protected $fillable = ['user_id', 'reference_number','currency_id', 'payment_method_id', 'bank_id', 'file_id', 'uuid', 'charge_percentage', 'charge_fixed', 'amount', 'status', 'trans_type', 'local_tran_time', 'last_four', 'ip_address'];
}
