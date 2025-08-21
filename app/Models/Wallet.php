<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Transaction;
use DB;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table    = 'wallets';
    protected $fillable = ['user_id', 'currency_id', 'balance', 'is_default','is_collect_payment'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function active_currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->where('status', 'Active');
    }

    public function currency_exchanges()
    {
        return $this->hasMany(CurrencyExchange::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function walletBalance()
    {
        $data = $this->leftJoin('currencies', 'currencies.id', '=', 'wallets.currency_id')
            ->select(DB::raw('SUM(wallets.balance) as amount,wallets.currency_id,currencies.type, currencies.code, currencies.symbol'))
            ->groupBy('wallets.currency_id')
            ->get();
        // dd($data);

        $array_data = [];
        foreach ($data as $row)
        {
            $array_data[$row->code] = $row->type != 'fiat' ? $row->amount : formatNumber($row->amount);
        }
        return $array_data;
    }

    //new
    public function cryptoapi_log()
    {
        return $this->hasOne(CryptoapiLog::class, 'object_id')->whereIn('object_type', ["wallet_address"]);
    }

    //Query for Mobile Application - starts
    public function getAvailableBalance($user_id)
    {
        
        $wallets = $this->with(['currency:id,type,code,logo,symbol,name,position'])->where(['user_id' => $user_id])
            ->orderBy('balance', 'ASC')
            ->get(['currency_id','user_id', 'is_default', 'balance','is_collect_payment'])
            ->map(function ($wallet)
        {   
            
                $currency_code =  $wallet->currency->code;
                $currency_id= DB::table('currencies')->where('code',$currency_code)->first()->id;
                $transaction  = new Transaction();
                $arr['income'] = formatNumber(abs($transaction->getTransactionSummary('allTransactions',$wallet->user_id,$currency_id,'income')));
                $arr['outcome'] =formatNumber(abs($transaction->getTransactionSummary('allTransactions',$wallet->user_id,$currency_id, 'outcome')));
                
                $arr['balance']    = $wallet->currency->type != 'fiat' ? $wallet->balance : number_format($wallet->balance, 2);
                $arr['is_default'] = $wallet->is_default;
                $arr['is_collect_payment'] = $wallet->is_collect_payment;
                $arr['curr_code']  = $wallet->currency->code;
                $arr['name']       = $wallet->currency->name;
                $arr['curr_id']    = $wallet->currency->id;
                $arr['position']    = $wallet->currency->position;
                $arr['symbol']       = $wallet->currency->symbol;
                $arr['logo']       = $wallet->currency->logo;
                $arr['base_url']   = env('CURRENCY_LOGO');
               
              
                return $arr;
                
            });
             //dd($wallets);
        return $wallets;
    }
    //Query for Mobile Application - ends
    
    public function getAvailableBalanceNew($user_id)
    {
        $wallets = $this->with(['currency:id,type,code,logo,symbol,name,position'])->where(['user_id' => $user_id])
            ->orderBy('balance', 'ASC')
            ->get(['currency_id','user_id', 'is_default', 'balance', 'id','is_collect_payment'])
            ->map(function ($wallet)
        {   
            
                $currency_code =  $wallet->currency->code;
                $currency_id= DB::table('currencies')->where('code',$currency_code)->first()->id;
                $transaction  = new Transaction();
                $arr['income'] = formatNumber(abs($transaction->getTransactionSummary('allTransactions',$wallet->user_id,$currency_id,'income')));
                $arr['outcome'] =formatNumber(abs($transaction->getTransactionSummary('allTransactions',$wallet->user_id,$currency_id, 'outcome')));
                
                $arr['balance']    = $wallet->currency->type != 'fiat' ? $wallet->balance : number_format($wallet->balance, 2);
                $arr['is_default'] = $wallet->is_default;
                $arr['is_collect_payment'] = $wallet->is_collect_payment;
                $arr['curr_code']  = $wallet->currency->code;
                $arr['name']       = $wallet->currency->name;
                $arr['curr_id']    = $wallet->currency->id;
                $arr['position']    = $wallet->currency->position;
                $arr['symbol']       = $wallet->currency->symbol;
                $arr['logo']       = $wallet->currency->logo;
                $arr['base_url']   = env('CURRENCY_LOGO');
                $arr['wallet_id']  = $wallet->id;
               
              
                return $arr;
                
            });
             //dd($wallets);
        return $wallets;
    }
}
