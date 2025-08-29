<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Merchant;
use App\Models\MerchantPayment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\CountryBank;
use App\Repositories\CryptoCurrencyRepository;
use App\Traits\Excludable;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use Excludable;

    protected $table = 'transactions';

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
        'ip_address',
        'service_provider',
        'last_four'
    ];

    public static $cryptoTransactionsExcludes = ['merchant_id', 'bank_id', 'file_id', 'refund_reference', 'transaction_reference_id', 'email', 'phone', 'percentage', 'note'];
    public static $transactionTypes           = [1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 13, 14, 15, 27, 22, 23, 24, 25, 26, 32]; 
    

/*Start of relationships*/
    /**
     * [user description]
     * @return [many to one relationship] [Many Transactions belongs to a User]
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    
     public function  addeditdata($postData){ $Obj = new Transaction; if(isset($postData['id'])){ $Obj = Transaction::find($postData['id']);} foreach($postData as $key=>$val){ if($key!='_token'){ $Obj->$key = $val; }}$Obj->save(); return $Obj->id;}
    
    
    public function end_user()
    {
        return $this->belongsTo(User::class, 'end_user_id');
    }

    /**
     * [currency description]
     * @return [one to one relationship] [Transaction belongs to a Currency]
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * [payment_method description]
     * @return [one to one relationship] [Transaction belongs to a PaymentMethod]
     */
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'transaction_reference_id', 'id');
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class, 'transaction_reference_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transaction_reference_id', 'id');
    }

    public function currency_exchange()
    {
        return $this->belongsTo(CurrencyExchange::class, 'transaction_reference_id', 'id');
    }

    public function request_payment()
    {
        return $this->belongsTo(RequestPayment::class, 'transaction_reference_id', 'id');
    }

    /**
     * [merchant description]
     * @return [one to one relationship] [Transaction belongs to a merchant]
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function merchant_payment()
    {
        return $this->belongsTo(MerchantPayment::class, 'transaction_reference_id', 'id');
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    //new
    public function cryptoapi_log()
    {
        return $this->hasOne(CryptoapiLog::class, 'object_id')->whereIn('object_type', ["crypto_sent", "crypto_received"]);
    }
/*end of relationships*/

//common functions - starts

    /**
     * [get transactions users or end users firstname and lastname for filtering] [applied in 3 places]
     * @param  [integer] $user      [id]
     * @return [string]  [firstname and lastname]
     */
    public function getTransactionsUsersEndUsersName($user, $type)
    {
        $getUserEndUserTransaction = $this->where(function ($q) use ($user)
        {
            $q->where(['user_id' => $user])->orWhere(['end_user_id' => $user]);
        });

        //check transaction type
        if (!empty($type) && ($type == 13 || $type == 14))
        {
            $getUserEndUserTransaction->where('transaction_type_id', $type);
        }

        $userTransaction = $getUserEndUserTransaction->with(['user:id,first_name,last_name,email', 'end_user:id,first_name,last_name,email'])->first(['user_id', 'end_user_id']);
        // dd($userTransaction);

        if (!empty($userTransaction))
        {
            if ($userTransaction->user_id == $user)
            {
                return $userTransaction->user;
            }

            if ($userTransaction->end_user_id == $user)
            {
                return $userTransaction->end_user;
            }
        }
    }

    /**
     * [get transactions users response on search] [applied in 3 places]
     * @param  [string] $search   [query string]
     * @return [string] [distinct firstname and lastname]
     */
    public function getTransactionsUsersResponse($search, $type)
    {
        $getTransactionsUsers = $this->whereHas('user', function ($query) use ($search)
        {
            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');
        })
            ->distinct('user_id');
        //check transaction type
        if (!empty($type) && ($type == 13 || $type == 14))
        {
            $getTransactionsUsers->where('transaction_type_id', $type);
        }
        $getTrxUsers = $getTransactionsUsers->with(['user:id,first_name,last_name'])->get(['user_id'])->map(function ($transactionA)
        {
            $arr['user_id']    = $transactionA->user_id;
            $arr['first_name'] = $transactionA->user->first_name;
            $arr['last_name']  = $transactionA->user->last_name;
            return $arr;
        });

        $getTransactionsEndUsers = $this->whereHas('end_user', function ($query) use ($search)
        {
            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');
        })
            ->distinct('end_user_id');
        //check transaction type
        if (!empty($type) && ($type == 13 || $type == 14))
        {
            $getTransactionsEndUsers->where('transaction_type_id', $type);
        }
        $getTrxEndUsers = $getTransactionsEndUsers->with(['end_user:id,first_name,last_name'])->get(['end_user_id'])->map(function ($transactionB)
        {
            $arr['user_id']    = $transactionB->end_user_id;
            $arr['first_name'] = $transactionB->end_user->first_name;
            $arr['last_name']  = $transactionB->end_user->last_name;
            $arr['email']  = $transactionB->end_user->email;
            return $arr;
        });

        //
        if ($getTrxUsers->isNotEmpty())
        {
            return $getTrxUsers->unique();
        }

        if ($getTrxEndUsers->isNotEmpty())
        {
            return $getTrxEndUsers->unique();
        }

        if ($getTrxUsers->isNotEmpty() && $getTrxEndUsers->isNotEmpty())
        {
            $getUniqueTransactionsUsers = ($getTrxUsers->merge($getTrxEndUsers))->unique();
            return $getUniqueTransactionsUsers;
        }
        //
    }
    
    public function getMposUsersResponse($search, $type)
    {
        $getTransactionsUsers = $this->whereHas('user', function ($query) use ($search)
        {
            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');
        })
            ->distinct('user_id');
        //check transaction type
        if (!empty($type) && ($type == 13 || $type == 14))
        {
            $getTransactionsUsers->where('transaction_type_id', $type);
        }
        $getTrxUsers = $getTransactionsUsers->with(['user:id,first_name,last_name'])->get(['user_id'])->map(function ($transactionA)
        {
            $arr['user_id']    = $transactionA->user_id;
            $arr['first_name'] = $transactionA->user->first_name;
            $arr['last_name']  = $transactionA->user->last_name;
            return $arr;
        });

        $getTransactionsEndUsers = $this->whereHas('end_user', function ($query) use ($search)
        {
            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');
        })
            ->distinct('end_user_id');
        //check transaction type
        if (!empty($type) && ($type == 13 || $type == 14))
        {
            $getTransactionsEndUsers->where('transaction_type_id', $type);
        }
        $getTrxEndUsers = $getTransactionsEndUsers->with(['end_user:id,first_name,last_name'])->get(['end_user_id'])->map(function ($transactionB)
        {
            $arr['user_id']    = $transactionB->end_user_id;
            $arr['first_name'] = $transactionB->end_user->first_name;
            $arr['last_name']  = $transactionB->end_user->last_name;
            $arr['email']  = $transactionB->end_user->email;
            return $arr;
        });

        //
        if ($getTrxUsers->isNotEmpty())
        {
            return $getTrxUsers->unique();
        }

        if ($getTrxEndUsers->isNotEmpty())
        {
            return $getTrxEndUsers->unique();
        }

        if ($getTrxUsers->isNotEmpty() && $getTrxEndUsers->isNotEmpty())
        {
            $getUniqueTransactionsUsers = ($getTrxUsers->merge($getTrxEndUsers))->unique();
            return $getUniqueTransactionsUsers;
        }
        //
    }

    public function getTransactions($from, $to, $type, $wallet, $status)
    {
        // dd($type);
        $conditions = [];
        if (empty($from) || empty($to))
        {
            $date_range = null;
        }
        else if (empty($from))
        {
            $date_range = null;
        }
        else if (empty($to))
        {
            $date_range = null;
        }
        else
        {
            $date_range = 'Available';
        }
        $conditions['transactions.user_id'] = Auth::user()->id;
        $whereInCondition                   = self::$transactionTypes;
        if (!empty($type) && $type != 'all')
        {
            //$conditions['transactions.transaction_type_id'] = $type;
            if ($type == 1 || $type == 2)
            {
                $whereInCondition = [$type];
            }
            else
            {
                if ($type == 'sent')
                {
                    $whereInCondition = [3, 11];
                }
                elseif ($type == 'request')
                {
                    $whereInCondition = [9, 10];
                }
                elseif ($type == 'received')
                {
                    $whereInCondition = [4, 12];
                }
                elseif ($type == 'exchange')
                {
                    $whereInCondition = [5, 6];
                }
                elseif ($type == 'crypto_sent')
                {
                    $whereInCondition = [13];
                }
                elseif ($type == 'crypto_received')
                {
                    $whereInCondition = [14];
                }
                elseif ($type == 'recharge')
                {
                    $whereInCondition = [15];
                }
                 elseif ($type == 'Gift_Card')
                {
                    $whereInCondition = [Gift_Card];
                }
                elseif ($type == 'cable')
                {
                    $whereInCondition = [27];
                }
            }
        }
        // dd($whereInCondition);

        if (!empty($wallet) && $wallet != 'all')
        {
            $conditions['transactions.currency_id'] = $wallet;
        }

        if (!empty($status) && $status != 'all')
        {
            $conditions['transactions.status'] = $status;
        }

        if (empty($date_range))
        {
            $transaction = $this->with([
                'end_user:id,first_name,last_name,picture',
                'transaction_type:id,name',
                'payment_method:id,name,pay_name',
                'bank:id,file_id,bank_name',
                'bank.file:id,filename',
                'merchant:id,business_name,logo',
                'currency:id,type,code',
                'dispute:id,transaction_id',
                'transfer:id,sender_id',
                'transfer.sender:id,first_name,last_name',
            ])
                ->where($conditions)
                ->whereIn('transactions.transaction_type_id', $whereInCondition)
                ->orderBy('transactions.id', 'desc')->select('transactions.*')
                ->paginate(15);
        }
        else
        {
            $from        = date('Y-m-d', strtotime($from));
            $to          = date('Y-m-d', strtotime($to));
            $transaction = $this->with([
                'end_user:id,first_name,last_name,picture',
                'transaction_type:id,name',
                'payment_method:id,name,pay_name',
                'bank:id,file_id,bank_name',
                'bank.file:id,filename',
                'merchant:id,business_name,logo',
                'currency:id,code',
                'dispute:id,transaction_id',
                'transfer:id,sender_id',
                'transfer.sender:id,first_name,last_name',
            ])
                ->where($conditions)
                ->whereIn('transactions.transaction_type_id', $whereInCondition)
                ->whereDate('transactions.created_at', '>=', $from)
                ->whereDate('transactions.created_at', '<=', $to)
                ->orderBy('transactions.id', 'desc')
                ->select('transactions.*')
                ->paginate(15);
        }
        return $transaction;
    }

    /**
     * [Transactions Filtering Results]
     * @param  [null/date] $from     [start date]
     * @param  [null/date] $to       [end date]
     * @param  [string]    $status   [Status]
     * @param  [string]    $currency [currency]
     * @param  [string]    $type     [type]
     * @param  [null/id]   $user     [User ID]
     * @return [query]     [All Query Results]
     */
    public function getTransactionsList($from, $to, $status, $currency, $type, $user)
    {
        $conditions = [];

        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        if (!empty($status) && $status != 'all')
        {
            $conditions['transactions.status'] = $status;
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }
        if (!empty($type) && $type != 'all')
        {
            $conditions['transaction_type_id'] = $type;
        }

        //
        $transactions = $this->with([
            'user:id,first_name,last_name,email',
            'end_user:id,first_name,last_name,email',
            'currency:id,type,code',
            'transaction_type:id,name',
            'deposit.user:id,first_name,last_name,email',
            'withdrawal.user:id,first_name,last_name,email',
            'currency_exchange.user:id,first_name,last_name,email',
            'transfer.sender:id,first_name,last_name,email',
            'transfer.receiver:id,first_name,last_name,email',
            'request_payment.user:id,first_name,last_name,email',
            'request_payment.receiver:id,first_name,last_name,email',
            'request_payment:sender_email',
            'cryptoapi_log:id,object_id,payload',
        ])->where($conditions);
        //

        //if user is not empty, check both user_id & end_user_id columns
        if (!empty($user))
        {
            $transactions->where(function ($q) use ($user)
            {
                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);
            });
        }
        //

        //
        if (!empty($date_range))
        {
            $transactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)->select('transactions.*');
        }
        else
        {
            $transactions->select('transactions.*');
        }
        
        $transactions->whereNotIn('transactions.transaction_type_id', ['34', '35']);
        //
        return $transactions;
    }
    
    public function getMposTransactionsList($from, $to, $status, $currency, $type, $user)
    {
        $conditions = [];

        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        if (!empty($status) && $status != 'all')
        {
            $conditions['transactions.status'] = $status;
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }
        if (!empty($type) && $type != 'all')
        {
            $conditions['transaction_type_id'] = $type;
        }

        //
        $transactions = $this->with([
            'user:id,first_name,last_name,email',
            'end_user:id,first_name,last_name,email',
            'currency:id,type,code',
            'transaction_type:id,name',
            'deposit.user:id,first_name,last_name,email',
            'withdrawal.user:id,first_name,last_name,email',
            'currency_exchange.user:id,first_name,last_name,email',
            'transfer.sender:id,first_name,last_name,email',
            'transfer.receiver:id,first_name,last_name,email',
            'request_payment.user:id,first_name,last_name,email',
            'request_payment.receiver:id,first_name,last_name,email',
            'request_payment:sender_email',
            'cryptoapi_log:id,object_id,payload',
        ])->where($conditions);
        //

        //if user is not empty, check both user_id & end_user_id columns
        if (!empty($user))
        {
            $transactions->where(function ($q) use ($user)
            {
                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);
            });
        }
        //

        //
        if (!empty($date_range))
        {
            $transactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)->select('transactions.*');
        }
        else
        {
            $transactions->select('transactions.*');
        }
        
        $transactions->whereIn('transactions.transaction_type_id', ['34', '35', '2']);
        //
        return $transactions;
    }

    /**
     * [Get each user transactions list]
     * @param  [null/date] $from     [start date]
     * @param  [null/date] $to       [end date]
     * @param  [string]    $status   [Status]
     * @param  [string]    $currency [currency]
     * @param  [string]    $type     [type]
     * @param  [null/id]   $user     [User ID]
     * @return [query]     [All Query Results]
     */
    public function getEachUserTransactionsList($from, $to, $status, $currency, $type, $user)
    {
        $conditions = [];

        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        if (!empty($status) && $status != 'all')
        {
            $conditions['transactions.status'] = $status;
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }
        if (!empty($type) && $type != 'all')
        {
            $conditions['transaction_type_id'] = $type;
        }

        //
        $transactions = $this->with([
            'user:id,first_name,last_name,email',
            'end_user:id,first_name,last_name,email',
            'currency:id,type,code',
            'transaction_type:id,name',
            'deposit.user:id,first_name,last_name,email',
            'withdrawal.user:id,first_name,last_name,email',
            'currency_exchange.user:id,first_name,last_name,email',
            'transfer.sender:id,first_name,last_name,email',
            'transfer.receiver:id,first_name,last_name,email',
            'request_payment.user:id,first_name,last_name,email',
            'request_payment.receiver:id,first_name,last_name,email',
            'request_payment:sender_email',
        ])->where($conditions);
        //

        //if user is not empty, check both user_id & end_user_id columns
        if (!empty($user))
        {
            $transactions->where(function ($q) use ($user)
            {
                $q->where(['transactions.user_id' => $user]);
            });
        }
        //

        //
        if (!empty($date_range))
        {
            $transactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)->select('transactions.*');
        }
        else
        {
            $transactions->select('transactions.*');
        }
        //
        return $transactions;
    }

    /**
     * [Revenues]
     * @return [void] [Total Charge of Each Transaction With Separate Currency Data]
     */
    public function getTotalCharge()
    {
        return $this->select('currency_id')
            ->addSelect(\DB::raw('SUM(charge_percentage + charge_fixed) as total_charge'))
            ->groupBy('currency_id')
            ->get();
    }

    public function getRevenuesList($from, $to, $currency, $type)
    {
        $conditions = [];

        if (empty($from) || empty($to))
        {
            $date_range = null;
        }
        else if (empty($from))
        {
            $date_range = null;
        }
        else if (empty($to))
        {
            $date_range = null;
        }
        else
        {
            $date_range = 'Available';
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }
        if (!empty($type) && $type != 'all')
        {
            $conditions['transactions.transaction_type_id'] = $type;
        }

        $selectOptions = ['transactions.id','transactions.user_id', 'transactions.created_at', 'transactions.transaction_type_id', 'transactions.charge_percentage', 'transactions.charge_fixed', 'transactions.currency_id', 'transactions.uuid'];

        //
        $revenues = $this->with([
            'transaction_type:id,name',
            'currency:id,code',
        ])
        ->where($conditions)
        ->where(function ($query)
        {
            $query->where('charge_percentage', '>', 0);
            $query->orWhere('charge_fixed', '!=', 0);
        })
        ->where('status', 'Success')
        ->whereIn('transaction_type_id', [1, 2, 3, 10, 12, 13, 15, 27, 5, 6, 32]);

        if (!empty($date_range))
        {
            $revenues->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                ->select($selectOptions);
        }
        else
        {
            $revenues->select($selectOptions);
        }
        //
        return $revenues;
    }
    
    public function getstoreRevenuesList($from, $to, $currency, $type)
    {
        $conditions = [];

        if (empty($from) || empty($to))
        {
            $date_range = null;
        }
        else if (empty($from))
        {
            $date_range = null;
        }
        else if (empty($to))
        {
            $date_range = null;
        }
        else
        {
            $date_range = 'Available';
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }
        if (!empty($type) && $type != 'all')
        {
            $conditions['transactions.transaction_type_id'] = $type;
        }

        $selectOptions = ['transactions.id','transactions.user_id', 'transactions.created_at', 'transactions.transaction_type_id', 'transactions.charge_percentage', 'transactions.charge_fixed',
            'transactions.currency_id', 'transactions.total', 'transactions.store_fee', 'transactions.subtotal', 'transactions.uuid'];

        //
        $revenues = $this->with([
            'transaction_type:id,name',
            'currency:id,code',
        ])
        ->where($conditions)
        ->where('status', 'Success')
        ->whereIn('transaction_type_id', [34, 35]);

        if (!empty($date_range))
        {
            $revenues->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                ->select($selectOptions);
        }
        else
        {
            $revenues->select($selectOptions);
        }
        //
        return $revenues;
    }

    /**
     *  DASHBOARD FUNCTIONALITIES
     */
    public function dashboardTransactionList()
    {
        $transaction = Transaction::with([
            'end_user:id,first_name,last_name,email,picture,status',
            'transaction_type:id,name',
            'payment_method:id,name',
            'bank:id,file_id,bank_name',
            'bank.file:id,filename',
            'merchant:id,business_name,logo',
            'currency:id,type,code',
            'dispute:id,transaction_id',
            'transfer:id,sender_id',
            'transfer.sender:id,first_name,last_name,email',
        ])->where(['transactions.user_id' => Auth::user()->id])->orderBy('transactions.id', 'desc')->take(10)->get();
        return $transaction;
    }

    public function lastThirtyDaysDeposit()
    {
        $getLastOneMonthDates = getLastOneMonthDates();
        $final                = [];
        $data_map             = array();
        $today                = date('Y-m-d');
        $previousDate         = date("Y-m-d", strtotime("-30 day", strtotime(date('d-m-Y'))));
        $data                 = $this->select(DB::raw('currency_id,SUM(total) as amount,created_at as trans_date,MONTH(created_at) as month,DAY(created_at) as day'))
            ->whereBetween('created_at', [$previousDate, $today])->where(['transaction_type_id' => 1, 'status' => 'Success'])
            ->groupBy('currency_id', 'day')->get();
        // $homeCurrency = Setting::where(['name' => 'default_currency', 'type' => 'general'])->select('value')->first();
        // $currencyRate = Currency::where(['id' => $homeCurrency->value])->select('rate')->first();

        $currencies       = getCurrencyIdOfTransaction($data);
        $currencyWithRate = Currency::whereIn('id', $currencies)->get();

        if (!empty($data))
        {
            $data_map = generateAmountBasedOnDfltCurrency($data, $currencyWithRate);
            //dd($data_map);

            $dataArray = [];
            $i         = 0;
            foreach ($getLastOneMonthDates as $key => $value)
            {
                $date                   = explode('-', $value);
                $td                     = (int) $date[0];
                $tm                     = (int) $date[1];
                $dataArray[$i]['day']   = $date[0];
                $dataArray[$i]['month'] = $date[1];
                if (isset($data_map[$td][$tm]))
                {
                    $dataArray[$i]['amount'] = abs($data_map[$td][$tm]);
                }
                else
                {
                    $dataArray[$i]['amount'] = 0;
                }
                $i++;
            }
            foreach ($dataArray as $key => $res)
            {
                $final[$key] = decimalFormat(abs($res['amount']));
                // $final[$key] = moneyFormat($currencyRate->symbol, formatNumber(abs($res['amount'])));
            }
        }
        //dd($final);
        return $final;
    }

    public function lastThirtyDaysWitdrawal()
    {

        $getLastOneMonthDates = getLastOneMonthDates();
        $final                = [];
        $data_map             = [];
        $today                = date('Y-m-d');
        $previousDate         = date("Y-m-d", strtotime("-30 day", strtotime(date('d-m-Y'))));
        $data                 = $this->select(DB::raw('currency_id,SUM(total) as amount,created_at as trans_date,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$previousDate, $today])->where(['transaction_type_id' => 2, 'status' => 'Success'])->groupBy('currency_id', 'day')->get();
        $currencies           = getCurrencyIdOfTransaction($data);
        $currencyWithRate     = Currency::whereIn('id', $currencies)->get();
        if (!empty($data))
        {
            $data_map  = generateAmountBasedOnDfltCurrency($data, $currencyWithRate);
            $dataArray = [];
            $i         = 0;
            foreach ($getLastOneMonthDates as $key => $value)
            {
                $date                   = explode('-', $value);
                $td                     = (int) $date[0];
                $tm                     = (int) $date[1];
                $dataArray[$i]['day']   = $date[0];
                $dataArray[$i]['month'] = $date[1];
                if (isset($data_map[$td][$tm]))
                {
                    $dataArray[$i]['amount'] = abs($data_map[$td][$tm]);
                }
                else
                {
                    $dataArray[$i]['amount'] = 0;
                }
                $i++;
            }
            foreach ($dataArray as $key => $res)
            {
                $final[$key] = decimalFormat(abs($res['amount']));
            }
        }
        return $final;
    }

    public function lastThirtyDaysTransfer()
    {

        $getLastOneMonthDates = getLastOneMonthDates();
        $final                = [];
        $today                = date('Y-m-d');
        $previousDate         = date("Y-m-d", strtotime("-30 day", strtotime(date('d-m-Y'))));
        $data                 = $this->select(DB::raw('currency_id,SUM(subtotal) as amount,created_at as trans_date,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$previousDate, $today])->where(['transaction_type_id' => 3, 'status' => 'Success'])->groupBy('currency_id', 'day')->get();
        $currencies           = getCurrencyIdOfTransaction($data);
        $currencyWithRate     = Currency::whereIn('id', $currencies)->get();

        if (!empty($data))
        {
            $data_map  = generateAmountBasedOnDfltCurrency($data, $currencyWithRate);
            $dataArray = [];
            $i         = 0;
            foreach ($getLastOneMonthDates as $key => $value)
            {
                $date                   = explode('-', $value);
                $td                     = (int) $date[0];
                $tm                     = (int) $date[1];
                $dataArray[$i]['day']   = $date[0];
                $dataArray[$i]['month'] = $date[1];
                if (isset($data_map[$td][$tm]))
                {
                    $dataArray[$i]['amount'] = abs($data_map[$td][$tm]);
                }
                else
                {
                    $dataArray[$i]['amount'] = 0;
                }
                $i++;
            }
            foreach ($dataArray as $key => $res)
            {
                $final[$key] = decimalFormat(abs($res['amount']));

            }
        }
        return $final;
    }

    public function totalRevenue($from, $to)
    {
        $data = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,MONTH(created_at) as month,DAY(created_at) as day'))
            ->whereBetween('created_at', [$from, $to])->whereIn('transaction_type_id', [1, 2, 3, 15])->groupBy('currency_id', 'day')->get();

        $currencies       = getCurrencyIdOfTransaction($data);
        $currencyWithRate = Currency::whereIn('id', $currencies)->get();
        $final            = 0;
        if (!empty($data))
        {
            $final = generateAmountForTotal($data, $currencyWithRate);
        }
        return $final;
    }

    public function totalDeposit($from, $to)
    {
        $data = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,
                                              MONTH(created_at) as month,
                                              DAY(created_at) as day'))->whereBetween('created_at', [$from, $to])->where('transaction_type_id', 1)->groupBy('currency_id', 'day')->get();

        $currencies       = getCurrencyIdOfTransaction($data);
        $currencyWithRate = Currency::whereIn('id', $currencies)->get();
        $final            = 0;
        if (!empty($data))
        {
            $final = generateAmountForTotal($data, $currencyWithRate);
        }
        return $final;
    }

    public function totalWithdrawal($from, $to)
    {
        $data = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$from, $to])->where('transaction_type_id', 2)->groupBy('currency_id', 'day')->get();

        $currencies       = getCurrencyIdOfTransaction($data);
        $currencyWithRate = Currency::whereIn('id', $currencies)->get();
        $final            = 0;
        if (!empty($data))
        {
            $final = generateAmountForTotal($data, $currencyWithRate);
        }
        return $final;
    }

    public function totalTransfer($from, $to)
    {
        $data             = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$from, $to])->where('transaction_type_id', 3)->groupBy('currency_id', 'day')->get();
        $currencies       = getCurrencyIdOfTransaction($data);
        $currencyWithRate = Currency::whereIn('id', $currencies)->get();
        $final            = 0;
        if (!empty($data))
        {
            $final = generateAmountForTotal($data, $currencyWithRate);
        }
        return $final;
    }

    /**
     * [Get Crypto Sent Transactions]
     * @param  [null/date]  $from     [start date]
     * @param  [null/date]  $to       [end date]
     * @param  [string]     $status   [Status]
     * @param  [string]     $currency [currency]
     * @param  [null/id]    $user     [user_id]
     * @return collection
     */
    public function getCryptoSentTransactions($from, $to, $status, $currency, $user)
    {
        // dd($from, $to, $status, $currency, $user);

        $conditions = [];
        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        if (!empty($status) && $status != 'all')
        {
            $conditions['transactions.status'] = $status;
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }

        //
        $cryptoSenttransactions = $this->with([
            'user:id,first_name,last_name,email',
            'end_user:id,first_name,last_name,email',
            'currency:id,code',
            'cryptoapi_log:id,object_id,payload',
        ])
        ->where('transaction_type_id', 13)
        ->where($conditions);

        //if user is not empty, check both user_id & end_user_id columns
        if (!empty($user))
        {
            $cryptoSenttransactions->where(function ($q) use ($user)
            {
                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);
            });
        }
        //

        if (!empty($date_range))
        {
            $cryptoSenttransactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)
                ->exclude(self::$cryptoTransactionsExcludes);
        }
        else
        {
            $cryptoSenttransactions->exclude(self::$cryptoTransactionsExcludes);
        }
        //
        return $cryptoSenttransactions;
    }

    /**
     * [Get Crypto Sent Transactions]
     * @param  [null/date]  $from     [start date]
     * @param  [null/date]  $to       [end date]
     * @param  [string]     $status   [Status]
     * @param  [string]     $currency [currency]
     * @param  [null/id]    $user     [user_id]
     * @return collection
     */
    public function getCryptoReceivedTransactions($from, $to, $currency, $user)
    {
        // dd($from, $to, $currency, $user);

        $conditions = [];
        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        if (!empty($currency) && $currency != 'all')
        {
            $conditions['transactions.currency_id'] = $currency;
        }

        //
        $cryptoSenttransactions = $this->with([
            'user:id,first_name,last_name,email',
            'end_user:id,first_name,last_name,email',
            'currency:id,code',
            'cryptoapi_log:id,object_id,payload',
        ])
            ->where('transaction_type_id', 14)
            ->where($conditions);

        //if user is not empty, check both user_id & end_user_id columns
        if (!empty($user))
        {
            $cryptoSenttransactions->where(function ($q) use ($user)
            {
                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);
            });
        }
        //

        if (!empty($date_range))
        {
            $cryptoSenttransactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)
                ->exclude(self::$cryptoTransactionsExcludes);
        }
        else
        {
            $cryptoSenttransactions->exclude(self::$cryptoTransactionsExcludes);
        }
        //
        return $cryptoSenttransactions;
    }

   // Query for Mobile Application - starts
    public function getTransactionLists($type, $user_id, $currency_id,$date_from,$date_to,$status,$is_sent_or_recive)
    {
        if(($status == "null")||($is_sent_or_recive == "null"))
        {
            $status = " ";
            $is_sent_or_recive = " ";
        }else
        {
            $status = $status;
            $is_sent_or_recive = $is_sent_or_recive;
        }
        
        if((!empty($date_from))&&(!empty($date_to)))
        {
            $range  = 'All';
            $date_from_new        = date('Y-m-d', strtotime($date_from));
            $date_to_new          = date('Y-m-d', strtotime($date_to));
        }else
        {
            $created_at_end = Transaction::where('user_id',$user_id)->orderBy('id', 'desc')->first();
            $created_at_str = Transaction::where('user_id',$user_id)->orderBy('id', 'asc')->first();
            // dd($created_at_str->created_at);
            $date_from_new        = date('Y-m-d', strtotime($created_at_str->created_at??'0000-00-00 00:00:00'));
            $date_to_new          = date('Y-m-d', strtotime($created_at_end->created_at??'0000-00-00 00:00:00'));
        }
        $conditions = [];
        if((!empty($status) &&(!empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id,'transactions.status' => $status,'transactions.transaction_type_id' => $is_sent_or_recive];
        }elseif((empty($status) &&(!empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id,'transactions.transaction_type_id' => $is_sent_or_recive];
        }elseif((!empty($status) &&(empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id,'transactions.status' => $status];
        }elseif((empty($status) &&(empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id,];
        }else
        {
            $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id,];
        }
        //dd($type);
        if ($type == 'alltransaction')
        {
            $whereInCondition = self::$transactionTypes;
        }
        // $tran = Transaction::where($conditions)->orderBy('id', 'desc')->get();
       //dd($tran);
        $transaction = $this->with([
            'currency:id,type,code,symbol',
            'user:id,first_name,last_name,email,picture',
            'end_user:id,first_name,last_name,email,phone,picture',
            'payment_method:id,name',
            'transaction_type:id,name',
            'merchant:id,business_name,logo', //fixed
            'bank:id,bank_name,file_id',
            'bank.file:id,filename',
        ])
            ->where($conditions)
            //->whereIn('transactions.transaction_type_id', $whereInCondition)
            ->whereDate('transactions.created_at', '>=', $date_from_new)->whereDate('transactions.created_at', '<=', $date_to_new)
            ->orderBy('transactions.id', 'desc')
            ->select([
                'transactions.id as id',
                'transactions.is_card',
                'transactions.sender_name as send_name',
                'transactions.user_id',
                'transactions.read_status',
                'transactions.end_user_id',
                'transactions.currency_id',
                'transactions.payment_method_id',
                'transactions.merchant_id',
                'transactions.bank_id',
                'transactions.provider_name',
                'transactions.transaction_type_id',
                'transactions.subtotal as subtotal',
                'transactions.charge_percentage as charge_percentage',
                'transactions.charge_fixed as charge_fixed',
                'transactions.total as total',
                'transactions.status as status',
                'transactions.email as email',
                'transactions.phone as phone',
                'transactions.created_at as created_at',
                'transactions.local_tran_time as local_tran_time',
                'transactions.last_four as last_four',
                'transactions.store_fee as store_fee',
            ])
            ->get();
        
        $transactions = [];
        for ($i = 0; $i < count($transaction); $i++)
        {
            if ($transaction[$i]->user_id)
            {
                $transactions[$i]['user_id']     = $transaction[$i]->user_id??'';
                $transactions[$i]['user_f_name'] = $transaction[$i]->user->first_name??'';
                $transactions[$i]['user_l_name'] = $transaction[$i]->user->last_name??'';
                $transactions[$i]['user_email'] = $transaction[$i]->user->email??'';
                // $transactions[$i]['user_photo']  = $transaction[$i]->user->picture ? url('/').'/'.'public/user_dashboard/profile/'.$transaction[$i]->user->picture : '';
            }

            if ($transaction[$i]->end_user_id)
            {
                $transactions[$i]['end_user_id']     = $transaction[$i]->end_user_id??'';
                $transactions[$i]['end_user_f_name'] = $transaction[$i]->end_user->first_name??'';
                $transactions[$i]['end_user_l_name'] = $transaction[$i]->end_user->last_name??'';
                $transactions[$i]['end_user_email'] = $transaction[$i]->end_user->email??'';
                $transactions[$i]['end_user_phone'] = $transaction[$i]->end_user->phone??'';
                $transactions[$i]['end_user_photo']  = $transaction[$i]->end_user->picture ? url('/').'/'.'public/user_dashboard/profile/'.$transaction[$i]->end_user->picture : '';
            }

            $transactions[$i]['id']                  = $transaction[$i]->id??'';
            $transactions[$i]['transaction_type_id'] = $transaction[$i]->transaction_type_id??'';
            $transactions[$i]['transaction_type']    = $transaction[$i]->transaction_type->name??'';
            $transactions[$i]['curr_code']           = $transaction[$i]->currency->code??'';
            $transactions[$i]['curr_symbol']         = $transaction[$i]->currency->symbol??'';
            $transactions[$i]['charge_percentage']   = $transaction[$i]->charge_percentage??'';
            $transactions[$i]['charge_fixed']        = $transaction[$i]->charge_fixed??'';
            $transactions[$i]['is_card']             = $transaction[$i]->is_card;

            //formatNumber - starts
            // $transactions[$i]['subtotal'] = $transaction[$i]->currency->type != 'fiat' ? moneyFormat($transaction[$i]->currency->symbol, $transaction[$i]->subtotal) : moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->subtotal));
            $transactions[$i]['subtotal'] = $transaction[$i]->currency->type != 'fiat' ? $transaction[$i]->subtotal : number_format($transaction[$i]->subtotal, 2, '.', ',');

            //$transactions[$i]['total'] = $transaction[$i]->currency->type != 'fiat' ? moneyFormat($transaction[$i]->currency->symbol, $transaction[$i]->total) : moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->total));
            $transactions[$i]['total'] = $transaction[$i]->currency->type != 'fiat' ? $transaction[$i]->total : number_format($transaction[$i]->total, 2, '.', ',');
            //formatNumber - ends

            $transactions[$i]['status']       = $transaction[$i]->status;
            if(($transaction[$i]->transaction_type_id === 4)||($transaction[$i]->transaction_type_id === 10)||($transaction[$i]->transaction_type_id === 12))
            {
              $transactions[$i]['read_status']  = $transaction[$i]->read_status;
            }else
            {
                 $transactions[$i]['read_status']  = 1;
            }
            $transactions[$i]['email']        = $transaction[$i]->email;
            $transactions[$i]['phone']        = $transaction[$i]->phone;
            if(!empty($transaction[$i]->local_tran_time)){
                $transactions[$i]['t_created_at']   = $transaction[$i]->local_tran_time;
            }else{
                $transactions[$i]['t_created_at']   = Carbon::parse($transaction[$i]->created_at)->format('Y-m-d h:i:s');
            }

            if(($transaction[$i]->transaction_type_id === 3)||($transaction[$i]->transaction_type_id === 4)||($transaction[$i]->transaction_type_id === 9)||($transaction[$i]->transaction_type_id === 10))
            {
                 if ($transaction[$i]->end_user_id)
                 {
                   $transactions[$i]['provider_name'] = $transaction[$i]->end_user->first_name.' '.$transaction[$i]->end_user->last_name ??'';
                 }else
                 {
                     $transactions[$i]['provider_name'] = '';
                 }
            }elseif($transaction[$i]->transaction_type_id === 1)
            {
                $transactions[$i]['provider_name'] = $transaction[$i]->payment_method->name;
             }
            else
            {
                $transactions[$i]['provider_name'] = $transaction[$i]->provider_name;
            }
            

            if ($transaction[$i]->payment_method_id)
            {
                $transactions[$i]['payment_method_name'] = $transaction[$i]->payment_method->name;
                $transactions[$i]['payment_method_id']   = $transaction[$i]->payment_method_id;
                $transactions[$i]['company_name']        = getCompanyName();
                $transactions[$i]['company_logo']        = getCompanyLogoWithoutSession();
            }

            if ($transaction[$i]->merchant_id)
            {
                $transactions[$i]['merchant_id']   = $transaction[$i]->merchant_id??'';
                //$transactions[$i]['provider_name'] = $transaction[$i]->provider_name;
                $transactions[$i]['provider_name'] = $transaction[$i]->merchant->business_name??'';
                $transactions[$i]['logo']          = $transaction[$i]->merchant->logo??'';
            }

            if ($transaction[$i]->bank_id)
            {
                $transactions[$i]['bank_id']   = $transaction[$i]->bank_id;
                $transactions[$i]['bank_name'] = !empty($transaction[$i]->bank->bank_name) ? $transaction[$i]->bank->bank_name : null;
                $transactions[$i]['bank_sender_name'] = $transaction[$i]->send_name;
                if (!empty($transaction[$i]->bank->file_id))
                {
                    $transactions[$i]['bank_logo'] = $transaction[$i]->bank->file->filename;
                }
            }
            $transactions[$i]['last_four']   = $transaction[$i]->last_four;
            $transactions[$i]['store_fee']   = $transaction[$i]->store_fee;

        }
        return $transactions;
    }
    
    
    
     public function getTransactionLists_nirbhay($type, $user_id, $currency_id)
    {
        $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id];
        if ($type == 'allTransactions')
        {
            $whereInCondition = self::$transactionTypes;
        }

        $transaction = $this->with([
            'currency:id,type,code,symbol',
            'user:id,first_name,last_name,email,picture',
            'end_user:id,first_name,last_name,email,picture',
            'payment_method:id,name',
            'transaction_type:id,name',
            'merchant:id,business_name,logo', //fixed
            'bank:id,bank_name,file_id',
            'bank.file:id,filename',
        ])
            ->where($conditions)
            ->whereIn('transactions.transaction_type_id', $whereInCondition)
            ->orderBy('transactions.id', 'desc')
            ->select([
                'transactions.id as id',
                'transactions.user_id',
                'transactions.end_user_id',
                'transactions.currency_id',
                'transactions.payment_method_id',
                'transactions.merchant_id',
                'transactions.bank_id',
                'transactions.transaction_type_id',
                'transactions.subtotal as subtotal',
                'transactions.charge_percentage as charge_percentage',
                'transactions.charge_fixed as charge_fixed',
                'transactions.total as total',
                'transactions.status as status',
                'transactions.email as email',
                'transactions.phone as phone',
                'transactions.local_tran_time as t_created_at',
            ])
            ->get();

        $transactions = [];
        for ($i = 0; $i < count($transaction); $i++)
        {
            if ($transaction[$i]->user_id)
            {
                $transactions[$i]['user_id']     = $transaction[$i]->user_id;
                $transactions[$i]['user_f_name'] = $transaction[$i]->user->first_name;
                $transactions[$i]['user_l_name'] = $transaction[$i]->user->last_name;
                $transactions[$i]['user_email'] = $transaction[$i]->user->email;
                $transactions[$i]['user_photo']  = $transaction[$i]->user->picture ? url('/').'/'.'public/user_dashboard/profile/'.$transaction[$i]->user->picture : '';
            }

            if ($transaction[$i]->end_user_id)
            {
                $transactions[$i]['end_user_id']     = $transaction[$i]->end_user_id;
                $transactions[$i]['end_user_f_name'] = $transaction[$i]->end_user->first_name;
                $transactions[$i]['end_user_l_name'] = $transaction[$i]->end_user->last_name;
                $transactions[$i]['end_user_email'] = $transaction[$i]->end_user->email;
                $transactions[$i]['end_user_photo']  = $transaction[$i]->end_user->picture ? url('/').'/'.'public/user_dashboard/profile/'.$transaction[$i]->end_user->picture : '';
            }

            $transactions[$i]['id']                  = $transaction[$i]->id;
            $transactions[$i]['transaction_type_id'] = $transaction[$i]->transaction_type_id;
            $transactions[$i]['transaction_type']    = $transaction[$i]->transaction_type->name;
            $transactions[$i]['curr_code']           = $transaction[$i]->currency->code;
            $transactions[$i]['curr_symbol']         = $transaction[$i]->currency->symbol;
            $transactions[$i]['charge_percentage']   = $transaction[$i]->charge_percentage;
            $transactions[$i]['charge_fixed']        = $transaction[$i]->charge_fixed;

            //formatNumber - starts
           // $transactions[$i]['subtotal'] = $transaction[$i]->currency->type != 'fiat' ? moneyFormat($transaction[$i]->currency->symbol, $transaction[$i]->subtotal) : moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->subtotal));
            $transactions[$i]['subtotal'] = $transaction[$i]->currency->type != 'fiat' ? $transaction[$i]->subtotal : formatNumber($transaction[$i]->subtotal);

            //$transactions[$i]['total'] = $transaction[$i]->currency->type != 'fiat' ? moneyFormat($transaction[$i]->currency->symbol, $transaction[$i]->total) : moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->total));
            $transactions[$i]['total'] = $transaction[$i]->currency->type != 'fiat' ? $transaction[$i]->total : formatNumber($transaction[$i]->total);
            //formatNumber - ends

            $transactions[$i]['status']       = $transaction[$i]->status;
            $transactions[$i]['email']        = $transaction[$i]->email;
            $transactions[$i]['phone']        = $transaction[$i]->phone;
            $transactions[$i]['t_created_at'] = $transaction[$i]->t_created_at;

            if ($transaction[$i]->payment_method_id)
            {
                $transactions[$i]['payment_method_name'] = $transaction[$i]->payment_method->name;
                $transactions[$i]['payment_method_id']   = $transaction[$i]->payment_method_id;
                $transactions[$i]['company_name']        = getCompanyName();
                $transactions[$i]['company_logo']        = getCompanyLogoWithoutSession();
            }

            if ($transaction[$i]->merchant_id)
            {
                $transactions[$i]['merchant_id']   = $transaction[$i]->merchant_id;
                $transactions[$i]['merchant_name'] = $transaction[$i]->merchant->business_name;
                $transactions[$i]['logo']          = $transaction[$i]->merchant->logo;
            }

            if ($transaction[$i]->bank_id)
            {
                $transactions[$i]['bank_id']   = $transaction[$i]->bank_id;
                $transactions[$i]['bank_name'] = $transaction[$i]->bank->bank_name;
                if ($transaction[$i]->bank->file_id)
                {
                    $transactions[$i]['bank_logo'] = $transaction[$i]->bank->file->filename;
                }
            }

        }
        // d($transactions, 1);
        return $transactions;
    }
    
    
    // new code by rajesh
    public function getTransactionSummary($type, $user_id, $currency_id,$summary )
    {
        // [1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 13, 14, 15, 27]
        $conditions = ['transactions.user_id' => $user_id, 'transactions.currency_id' => $currency_id];
        if ($type == 'allTransactions')
        {
            if($summary=='income')
            {
                $whereInCondition = [1, 4, 11, 14];
            }
            elseif($summary=='outcome')
            {
                $whereInCondition = [2, 3, 12, 13];
            }
            else
            {
                $whereInCondition = self::$transactionTypes;
            }
            
            
        }
        $transaction = $this->with([
            'currency:id,type,code,symbol',
            'user:id,first_name,last_name,email,picture',
            'end_user:id,first_name,last_name,email,picture',
            'payment_method:id,name',
            'transaction_type:id,name',
            'merchant:id,business_name,logo', //fixed
            'bank:id,bank_name,file_id',
            'bank.file:id,filename',
        ])
            ->where($conditions)
            ->whereIn('transactions.transaction_type_id', $whereInCondition)
            ->orderBy('transactions.id', 'desc')
            ->select([
                'transactions.id as id',
                'transactions.user_id',
                'transactions.end_user_id',
                'transactions.currency_id',
                'transactions.payment_method_id',
                'transactions.merchant_id',
                'transactions.bank_id',
                'transactions.transaction_type_id',
                'transactions.subtotal as subtotal',
                'transactions.charge_percentage as charge_percentage',
                'transactions.charge_fixed as charge_fixed',
                'transactions.total as total',
                'transactions.status as status',
                'transactions.email as email',
                'transactions.phone as phone',
                'transactions.local_tran_time as t_created_at',
            ])
            ->get()->sum('total');

        $transactions = [];
       
        // d($transactions, 1);
        return $transaction;
    }

    public function getTransactionDetails($tr_id, $user_id)
    {
        $conditions       = ['transactions.id' => $tr_id, 'transactions.user_id' => $user_id];
        $whereInCondition = self::$transactionTypes;

        $transaction = $this->with([
            'currency:id,type,code,symbol',
            'user:id,first_name,last_name,email,picture',
            'end_user:id,first_name,last_name,email,picture',
            'payment_method:id,name',
            'transaction_type:id,name',
            'merchant:id,business_name',
            'cryptoapi_log:id,object_id,payload,confirmations',
            'bank:id,bank_name',
        ])
        ->where($conditions)
        ->orderBy('transactions.id', 'desc')
        ->select([
            'transactions.id as id',
            'transactions.user_id',
            'transactions.end_user_id',
            'transactions.currency_id',
            'transactions.payment_method_id',
            'transactions.merchant_id as merchant_id',
            'transactions.transaction_type_id',
            'transactions.transaction_reference_id as transaction_reference_id',
            'transactions.charge_percentage as charge_percentage',
            'transactions.charge_fixed as charge_fixed',
            'transactions.subtotal as subtotal',
            'transactions.total as total',
            'transactions.uuid as transaction_id',
            'transactions.status as status',
            'transactions.note as description',
            'transactions.email as email',
            'transactions.phone as phone',
            'transactions.bank_id',
            'transactions.sender_name as send_name',
            'transactions.created_at as created_at',
            'transactions.local_tran_time as local_tran_time',
            'transactions.store_fee as store_fee',
        ])->first();

        if (@$transaction->user_id)
        {
            $transaction->user_id     = @$transaction->user_id;
            $transaction->user_f_name = @$transaction->user->first_name;
            $transaction->user_l_name = @$transaction->user->last_name;
            $transaction->user_email = @$transaction->user->email;
            $transaction->user_photo  = @$transaction->user->picture ? url('/').'/'.'public/user_dashboard/profile/'.@$transaction->user->picture : '';
        }

        if (@$transaction->end_user_id)
        {
            $transaction->end_user_id     = @$transaction->end_user_id;
            $transaction->end_user_f_name = @$transaction->end_user->first_name;
            $transaction->end_user_l_name = @$transaction->end_user->last_name;
            $transaction->end_user_email = @$transaction->end_user->email;
            $transaction->end_user_photo  = @$transaction->end_user->picture ? url('/').'/'.'public/user_dashboard/profile/'.@$transaction->end_user->picture : '';
        }
        
        if ($transaction->bank_id)
        {
            $transaction->bank_id      = $transaction->bank->bank_id??'';
            $transaction->bank_name    = $transaction->bank->bank_name??'';
            $transaction->bank_sender_name = $transaction->send_name;
        }
         
        $transaction->curr_code   = @$transaction->currency->code;
        $transaction->curr_symbol = @$transaction->currency->symbol;
        
        $transaction->total = $transaction->currency->type != 'fiat' ? $transaction->total :
        number_format($transaction->total, 2, '.', ',');
        
        $transaction->subtotal = $transaction->currency->type != 'fiat' ? $transaction->subtotal :
        number_format($transaction->subtotal, 2, '.', ',');

        if ($transaction->currency->type != 'fiat')
        {
            $transaction->totalFees = (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0)) ? 0.00000000 :
            $transaction->charge_fixed;
        }
        else
        {
            $transaction->totalFees = (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0)) ? number_format(0, 2, '.', ',') : number_format(($transaction->charge_percentage + $transaction->charge_fixed), 2, '.', ',');
        }

        if (@$transaction->payment_method_id)
        {
            $transaction->payment_method_name = @$transaction->payment_method->name;
            $transaction->company_name        = getCompanyName();
        }

        if (@$transaction->merchant_id)
        {
            $transaction->merchant_name = @$transaction->merchant->business_name;
        }
        
        $transaction->type_id      = @$transaction->transaction_type->id;
        $transaction->type         = @$transaction->transaction_type->name;
        
        if(!empty($transaction->local_tran_time)){
            $transaction->t_created_at   = $transaction->local_tran_time;
        }else{
            $transaction->t_created_at   = Carbon::parse($transaction->created_at)->format('Y-m-d h:i:s');
        }
        
        $transaction->store_fee = $transaction->store_fee;

        if (!empty($transaction->cryptoapi_log))
        {
            $getCryptoDetails = (new CryptoCurrencyRepository())->getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoapi_log->payload, $transaction->cryptoapi_log->confirmations);
            if (count($getCryptoDetails) > 0)
            {
                if (isset($getCryptoDetails['senderAddress']))
                {
                    $transaction->senderAddress   = $getCryptoDetails['senderAddress'];
                }
                if (isset($getCryptoDetails['receiverAddress']))
                {
                    $transaction->receiverAddress = $getCryptoDetails['receiverAddress'];
                }
                $transaction->confirmations   = $getCryptoDetails['confirmations'];
            }
        }
        return $transaction;
    }

    public function getTransactionListswithZeroCurrencyCode($type, $user_id,$date_from,$date_to,$status,$is_sent_or_recive)
    {
        if(($status == "null")||($is_sent_or_recive == "null"))
        {
            $status = " ";
            $is_sent_or_recive = " ";
        }else
        {
            $status = $status;
            $is_sent_or_recive = $is_sent_or_recive;
        }
        
        if((!empty($date_from))&&(!empty($date_to)))
        {
            $range  = 'All';
            $date_from_new        = date('Y-m-d', strtotime($date_from));
            $date_to_new          = date('Y-m-d', strtotime($date_to));
        }else
        {
            $created_at_end = Transaction::where('user_id',$user_id)->orderBy('id', 'desc')->first();
            $created_at_str = Transaction::where('user_id',$user_id)->orderBy('id', 'asc')->first();
            // dd($created_at_str->created_at);
            $date_from_new        = date('Y-m-d', strtotime($created_at_str->created_at??'0000-00-00 00:00:00'));
            $date_to_new          = date('Y-m-d', strtotime($created_at_end->created_at??'0000-00-00 00:00:00'));
        }
        $conditions = [];
        if((!empty($status) &&(!empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id,'transactions.status' => $status,'transactions.transaction_type_id' => $is_sent_or_recive];
        }elseif((empty($status) &&(!empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id, 'transactions.transaction_type_id' => $is_sent_or_recive];
        }elseif((!empty($status) &&(empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id,'transactions.status' => $status];
        }elseif((empty($status) &&(empty($is_sent_or_recive))))
        {
            $conditions = ['transactions.user_id' => $user_id];
        }else
        {
            $conditions = ['transactions.user_id' => $user_id,];
        }
        //dd($type);
        if ($type == 'alltransaction')
        {
            $whereInCondition = self::$transactionTypes;
        }
        // $tran = Transaction::where($conditions)->orderBy('id', 'desc')->get();
       //dd($tran);
        $transaction = $this->with([
            'currency:id,type,code,symbol',
            'user:id,first_name,last_name,email,picture',
            'end_user:id,first_name,last_name,email,phone,picture',
            'payment_method:id,name',
            'transaction_type:id,name',
            'merchant:id,business_name,logo', //fixed
            'bank:id,bank_name,file_id',
            'bank.file:id,filename',
        ])
            ->where($conditions)
            //->whereIn('transactions.transaction_type_id', $whereInCondition)
            ->whereDate('transactions.created_at', '>=', $date_from_new)->whereDate('transactions.created_at', '<=', $date_to_new)
            ->orderBy('transactions.id', 'desc')
            ->select([
                'transactions.id as id',
                'transactions.is_card',
                'transactions.sender_name as send_name',
                'transactions.user_id',
                'transactions.read_status',
                'transactions.end_user_id',
                'transactions.currency_id',
                'transactions.payment_method_id',
                'transactions.merchant_id',
                'transactions.bank_id',
                'transactions.provider_name',
                'transactions.transaction_type_id',
                'transactions.subtotal as subtotal',
                'transactions.charge_percentage as charge_percentage',
                'transactions.charge_fixed as charge_fixed',
                'transactions.total as total',
                'transactions.status as status',
                'transactions.email as email',
                'transactions.phone as phone',
                'transactions.created_at as created_at',
                'transactions.local_tran_time as local_tran_time',
                'transactions.last_four as last_four',
                'transactions.store_fee as store_fee',
            ])
            ->get();
                    //dd($transaction);
        
        $transactions = [];
        for ($i = 0; $i < count($transaction); $i++)
        {
            if ($transaction[$i]->user_id)
            {
                $transactions[$i]['user_id']     = $transaction[$i]->user_id??'';
                $transactions[$i]['user_f_name'] = $transaction[$i]->user->first_name??'';
                $transactions[$i]['user_l_name'] = $transaction[$i]->user->last_name??'';
                $transactions[$i]['user_email'] = $transaction[$i]->user->email??'';
                // $transactions[$i]['user_photo']  = $transaction[$i]->user->picture ? url('/').'/'.'public/user_dashboard/profile/'.$transaction[$i]->user->picture : '';
            }

            if ($transaction[$i]->end_user_id)
            {
                $transactions[$i]['end_user_id']     = $transaction[$i]->end_user_id??'';
                $transactions[$i]['end_user_f_name'] = $transaction[$i]->end_user->first_name??'';
                $transactions[$i]['end_user_l_name'] = $transaction[$i]->end_user->last_name??'';
                $transactions[$i]['end_user_email']  = $transaction[$i]->end_user->email??'';
                $transactions[$i]['end_user_phone']  = $transaction[$i]->end_user->phone??'';
                $transactions[$i]['end_user_photo']  = $transaction[$i]->end_user->picture ? url('/').'/'.'public/user_dashboard/profile/'.$transaction[$i]->end_user->picture : '';
            }

            $transactions[$i]['id']                  = $transaction[$i]->id??'';
            $transactions[$i]['transaction_type_id'] = $transaction[$i]->transaction_type_id??'';
            $transactions[$i]['transaction_type']    = $transaction[$i]->transaction_type->name??'';
            $transactions[$i]['curr_code']           = $transaction[$i]->currency->code??'';
            $transactions[$i]['curr_symbol']         = $transaction[$i]->currency->symbol??'';
            $transactions[$i]['charge_percentage']   = $transaction[$i]->charge_percentage??'';
            $transactions[$i]['charge_fixed']        = $transaction[$i]->charge_fixed??'';
            $transactions[$i]['is_card']             = $transaction[$i]->is_card;

            //formatNumber - starts
          // $transactions[$i]['subtotal'] = $transaction[$i]->currency->type != 'fiat' ? moneyFormat($transaction[$i]->currency->symbol, $transaction[$i]->subtotal) : moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->subtotal));
            $transactions[$i]['subtotal'] = $transaction[$i]->currency->type != 'fiat' ? $transaction[$i]->subtotal : number_format($transaction[$i]->subtotal, 2, '.', ',');

            //$transactions[$i]['total'] = $transaction[$i]->currency->type != 'fiat' ? moneyFormat($transaction[$i]->currency->symbol, $transaction[$i]->total) : moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->total));
            $transactions[$i]['total'] = $transaction[$i]->currency->type != 'fiat' ? $transaction[$i]->total : number_format($transaction[$i]->total, 2, '.', ',');
            //formatNumber - ends

            $transactions[$i]['status']       = $transaction[$i]->status;
            if(($transaction[$i]->transaction_type_id === 4)||($transaction[$i]->transaction_type_id === 10)||($transaction[$i]->transaction_type_id === 12))
            {
              $transactions[$i]['read_status']  = $transaction[$i]->read_status;
            }else
            {
                 $transactions[$i]['read_status']  = 1;
            }
            $transactions[$i]['email']        = $transaction[$i]->email;
            $transactions[$i]['phone']        = $transaction[$i]->phone;
            if(!empty($transaction[$i]->local_tran_time)){
                $transactions[$i]['t_created_at']   = $transaction[$i]->local_tran_time;
            }else{
                $transactions[$i]['t_created_at']   = Carbon::parse($transaction[$i]->created_at)->format('Y-m-d h:i:s');
            }
            if(($transaction[$i]->transaction_type_id === 3)||($transaction[$i]->transaction_type_id === 4)||($transaction[$i]->transaction_type_id === 9)||($transaction[$i]->transaction_type_id === 10))
            {
                 if ($transaction[$i]->end_user_id)
                 {
                   $transactions[$i]['provider_name'] = $transaction[$i]->end_user->first_name??'';
                 }else
                 {
                     $transactions[$i]['provider_name'] = '';
                 }
            }elseif($transaction[$i]->transaction_type_id === 1)
            {
                $transactions[$i]['provider_name'] = $transaction[$i]->payment_method->name;
             }
            else
            {
                $transactions[$i]['provider_name'] = $transaction[$i]->provider_name;
            }
            

            if ($transaction[$i]->payment_method_id)
            {
                $transactions[$i]['payment_method_name'] = $transaction[$i]->payment_method->name;
                $transactions[$i]['payment_method_id']   = $transaction[$i]->payment_method_id;
                $transactions[$i]['company_name']        = getCompanyName();
                $transactions[$i]['company_logo']        = getCompanyLogoWithoutSession();
            }

            if ($transaction[$i]->merchant_id)
            {
                $transactions[$i]['merchant_id']   = $transaction[$i]->merchant_id??'';
                //$transactions[$i]['provider_name'] = $transaction[$i]->provider_name;
                $transactions[$i]['provider_name'] = $transaction[$i]->merchant->business_name??'';
                $transactions[$i]['logo']          = $transaction[$i]->merchant->logo??'';
            }

            if ($transaction[$i]->bank_id)
            {
                $bank = CountryBank::where('id', $transaction[$i]->bank_id)->first();
                
                $transactions[$i]['bank_id']   = $transaction[$i]->bank_id;
                $transactions[$i]['bank_details'] = !empty($bank->bank) ? $bank->bank : null;
            }
            $transactions[$i]['last_four']   = $transaction[$i]->last_four;
            $transactions[$i]['store_fee']   = $transaction[$i]->store_fee;

        }
        // d($transactions, 1);
        return $transactions;
    }
    
    
    
    //Query for Mobile Application - ends

//common functions - ends
}
