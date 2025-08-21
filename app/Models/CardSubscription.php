<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Transaction;
use DB;
use Illuminate\Database\Eloquent\Model;

class CardSubscription extends Model
{
    protected $table    = 'card_subscriptions';
    protected $fillable = ['plan_id', 'plan_data', 'user_id', 'wallet_id', 'currency_id', 'transaction_id', 'sub_total', 'fees', 'total', 'status', 'trx', 'remarks'];
}
