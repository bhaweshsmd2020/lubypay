<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Merchant;
use App\Models\MerchantPayment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Repositories\CryptoCurrencyRepository;
use App\Traits\Excludable;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;

class PendingTransaction extends Model
{
    use Excludable;

    protected $table = 'pending_transactions';

    protected $fillable = [
        'user_id',
        'sender_name',
        'is_card',
        'read_status',
        'provider_name',
        'end_user_id',
        'currency_id',
        'payment_method_id',
        'merchant_id',
        'bank_id',
        'file_id',
        'uuid',
        'refund_reference',
        'transaction_reference_id',
        'transaction_type_id',
        'user_type',
        'email',
        'phone',
        'subtotal',
        'percentage',
        'charge_percentage',
        'charge_fixed',
        'total',
        'note',
        'status',
    ];
}
