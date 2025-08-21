<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $table    = 'gift_cards';
    protected $fillable = ['country_id', 'user_id', 'card_number', 'card_expiry', 'card_cvv', 'currency_id', 'amount', 'issue_date', 'validity', 'status', 'local_tran_time', 'ip_address'];
    public $timestamps  = false;

    public function user_detail() 
    {
        return $this->hasOne(UserDetail::class, 'giftcard_id');
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'giftcard_id');
    }


    
}
