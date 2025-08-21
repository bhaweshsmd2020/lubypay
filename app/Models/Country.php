<?php

namespace App\Models;

class Country extends Model
{
    protected $table    = 'countries';
    protected $fillable = ['short_name', 'name', 'iso3', 'number_code', 'phone_code', 'currency_code', 'flag', 'status', 'automatic_kyc', 'manual_kyc', 'sms_platform', 'region_name'];
    public $timestamps  = false;

    public function user_detail()
    {
        return $this->hasOne(UserDetail::class, 'country_id');
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'country_id');
    }

}
