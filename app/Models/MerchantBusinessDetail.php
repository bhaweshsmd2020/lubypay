<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantBusinessDetail extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'business_name', 'business_type', 'business_no', 'website_url', 'business_nature','sell','description','customer_type','average_transaction','annual_turnover','employees','foreign_currency_payment','official_phone', 'customer_statement_phone', 'street','city','region', 'country', 'postcode', 'operate_from','establish_date','trading_name','use_caribPay', 'days_deliver', 'when_charged', 'based', 'target_country'];
}
