<?php

namespace App\Models;

use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\WithdrawalDetail;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class SalesWithdrawal extends Model
{
    protected $table    = 'sales_withdrawal';
    public $timestamps  = true;
    protected $fillable = ['user_id', 'withdrawal_id', 'transaction_id', 'status', 'unique_order_id'];

    //
    protected $email;
    protected $helper;
    public function __construct()
    {
        $this->email  = new EmailController(); //needed to send email notification
        $this->helper = new Common();
    }
    //

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
        return $this->hasMany(Transaction::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function withdrawal_detail()
    {
        return $this->hasOne(WithdrawalDetail::class, 'withdrawal_id');
    }
}
