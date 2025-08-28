<?php
namespace App\Http\Helpers;

use App\Http\Controllers\Users\EmailController;
use App\Models\NotificationSetting;
use Session, Config, Exception;
use App\Exceptions\Api\V2\{
    AmountLimitException,
    WalletException,
    FeesException
};
use App\Models\{PermissionRole,
    PayoutSetting,
    EmailTemplate,
    Permission,
    FeesLimit,
    Currency,
    RoleUser,
    QrCode,
    Wallet,
    User,
    Device
};
use App\Models\PendingTransaction;
use App\Models\Rule;
use App\Models\RuleReport;
use App\Models\Setting;
use App\Models\Preference;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Http;

class Common
{
    public static $templateIds = ['deposit' => 23, 'payout' => 24, 'exchange' => 25, 'send' => 26, 'request' => 27, 'payment' => 28,'gift_card'=>30,'mobile_reload'=>31,'user_verification'=>32];
    public static $languages   = [1 => 'en', 2 => 'ar', 3 => 'fr', 4 => 'pt', 5 => 'ru', 6 => 'es', 7 => 'tr', 8 => 'ch'];
    protected $email;
    
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $this->email = new EmailController();
    }

    public static function one_time_message($class, $message)
    {
        if ($class == 'error')
        {
            $class = 'danger';
        }
        Session::flash('alert-class', 'alert-' . $class);
        Session::flash('message', $message);
    }

    // key_value [key,value, collection]
    public static function key_value($key, $value, $collection)
    {
        $return_value = [];
        foreach ($collection as $row)
        {
            $return_value[$row[$key]] = $row[$value];
        }
        return $return_value;
    }

    /*
     * @param $user_id
     * @param $permissions
     * @static has_permission
     */
    public static function has_permission($user_id, $permissions = '')
    {
        $permissions = explode('|', $permissions);

        $permission_id = [];
        $i             = 0;

        $prefix = str_replace('/', '', request()->route()->getPrefix());
        if ($prefix == config('adminPrefix'))
        {
            $user_type = 'Admin';
        }
        else
        {
            $user_type = 'User';
        }

        $userPermissions = Permission::whereIn('name', $permissions)->get(['id']);
        foreach ($userPermissions as $value)
        {
            $permission_id[$i++] = $value->id;
        }
        $role = RoleUser::where(['user_id' => $user_id, 'user_type' => $user_type])->first(['role_id']);
        if (count($permission_id) && isset($role->role_id))
        {
            $has_permit = PermissionRole::where('role_id', $role->role_id)->whereIn('permission_id', $permission_id);
            return $has_permit->count();
        }
        else
        {
            return 0;
        }
    }

    /**
     * Undocumented function
     *
     * @param  [type] $host
     * @param  [type] $user
     * @param  [type] $pass
     * @param  [type] $name
     * @param  string $tables
     * @return void
     */
    public function backup_tables($host, $user, $pass, $name, $tables = '*')
    {
        try {
            $con = mysqli_connect($host, $user, $pass, $name);
        }
        catch (Exception $e)
        {
            print_r($e->getMessage());
        }

        if (mysqli_connect_errno())
        {
            $this->one_time_message('danger', "Failed to connect to MySQL: " . mysqli_connect_error());
            return 0;
        }

        $con->set_charset("utf8mb4");

        if ($tables == '*')
        {
            $tables = array();
            $result = mysqli_query($con, 'SHOW TABLES');
            while ($row = mysqli_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        $return = '';
        foreach ($tables as $table)
        {
            $result     = mysqli_query($con, 'SELECT * FROM ' . $table);
            $num_fields = mysqli_num_fields($result);

            $row2 = mysqli_fetch_row(mysqli_query($con, 'SHOW CREATE TABLE ' . $table));
            $return .= "\n\n" . str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row2[1]) . ";\n\n";

            for ($i = 0; $i < $num_fields; $i++)
            {
                while ($row = mysqli_fetch_row($result))
                {
                    $return .= 'INSERT INTO ' . $table . ' VALUES(';
                    for ($j = 0; $j < $num_fields; $j++)
                    {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                        if (isset($row[$j]))
                        {
                            $return .= '"' . $row[$j] . '"';
                        }
                        else
                        {
                            $return .= '""';
                        }
                        if ($j < ($num_fields - 1))
                        {
                            $return .= ',';
                        }
                    }
                    $return .= ");\n";
                }
            }

            $return .= "\n\n\n";
        }

        $backup_name = date('Y-m-d-His') . '.sql';

        $directoryPath = public_path("uploads/db-backups");

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, config('paymoney.file_permission'), true);
        }

        $handle = fopen($directoryPath . '/' . $backup_name, 'w+');
        fwrite($handle, $return);
        fclose($handle);

        return $backup_name;
    }

    //  Check user status
    public function getUserStatus($userStatus)
    {
        if ($userStatus == 'Suspended')
        {
            return 'Suspended';
        }
        elseif ($userStatus == 'Inactive')
        {
            return 'Inactive';
        }
    }

    public function checkWalletBalanceAgainstAmount($totalWithFee, $currency_id, $user_id)
    {
        //Backend Validation - Wallet Balance Again Amount Check - Starts here
        $wallet = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['id', 'balance']);
        if (!empty($wallet))
        {
            if (($totalWithFee > $wallet->balance) || ($wallet->balance < 0))
            {
                return true;
            }
        }
        //Backend Validation - Wallet Balance Again Amount Check - Ends here
    }

    /**
     * [Get Current Date & Time - Carbon]
     * return [string] [Cardbon Date Time]
     */
    public function getCurrentDateTime()
    {
        return dateFormat(now());
    }

    public function clearSessionWithRedirect($sessionArr, $exception, $path)
    {
        Session::forget($sessionArr);
        clearActionSession();
        $this->one_time_message('error', $exception->getMessage());
        return redirect($path);
    }

    public function returnUnauthorizedResponse($unauthorisedStatus, $exception)
    {
        $success            = [];
        $success['status']  = $unauthorisedStatus;
        $success['message'] = $exception->getMessage();
        return response()->json(['success' => $success], $unauthorisedStatus);
    }

    public function validateEmailInput($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function validatePhoneInput($value)
    {
        return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i',
            $value);
    }

    public function getEmailPhoneValidatedUserInfo($emailFilterValidate, $phoneRegex, $emailOrPhone)
    {
        $selectOptions = ['id', 'first_name', 'last_name', 'email', 'carrierCode', 'phone'];
        if ($emailFilterValidate)
        {
            $userInfo = User::where(['email' => $emailOrPhone, 'role_id' => '2'])->first($selectOptions);
        }
        elseif ($phoneRegex)
        {
            $userInfo = User::where(['phone' => $emailOrPhone, 'role_id' => '2'])->first($selectOptions);
        }
        return $userInfo;
    }
    
    public function getEmailPhoneValidatedUserInfoNew($emailFilterValidate, $phoneRegex, $emailOrPhone, $user_type)
    {
        $selectOptions = ['id', 'first_name', 'last_name', 'email', 'carrierCode', 'phone'];
        if ($emailFilterValidate)
        {
            $userInfo = User::where(['email' => $emailOrPhone, 'role_id' => $user_type])->first($selectOptions);
        }
        elseif ($phoneRegex)
        {
            $userInfo = User::where(['formattedPhone' => $emailOrPhone])->orWhere('phone',$emailOrPhone)->where('role_id', $user_type)->first($selectOptions);
        }
        return $userInfo;
    }

    /**
     * fetch Deposit Active Fees Limit
     * @param array $withOptions Data needs to be fetched with lazy loading
     * @param  int $transactionType Transaction type id
     * @param  int $currencyId Currency Id
     * @param  int $paymentMethodId Payment method id
     * @param  array $options
     * @return object|null
     */
    public function getFeesLimitObject($withOptions = [], $transactionType, $currencyId, $paymentMethodId, $hasTransaction, $options)
    {
        return FeesLimit::with($withOptions)
            ->where('transaction_type_id', $transactionType)
            ->where('currency_id', $currencyId)
            ->when(!is_null($hasTransaction), function ($query) use ($hasTransaction) {
                return $query->where('has_transaction', $hasTransaction);
            })
            ->when(!is_null($paymentMethodId), function ($query) use ($paymentMethodId) {
                return $query->where('payment_method_id', $paymentMethodId);
            })
            ->first($options);
    }

    /**
     * Get Wallet Object
     * param  array  $withOptions   [eagar loaded relations]
     * param  array $constraints   [where or other conditions]
     * param  array $selectOptions [specific fields]
     * return object
     */
    public function getUserWallet($withOptions = [], $constraints, $selectOptions)
    {
        return Wallet::with($withOptions)->where($constraints)->first($selectOptions);
    }

    /**
     * Get All Wallets
     * param  array  $withOptions   [eagar loaded relations]
     * param  array $constraints   [where or other conditions]
     * param  array $selectOptions [specific fields]
     * return collection
     */
    public function getUserWallets($withOptions = [], $constraints, $selectOptions)
    {
        return Wallet::with($withOptions)->where($constraints)->get($selectOptions);
    }

    /**
     * Get Currency
     * @param  array    $constraints
     * @param  array    $selectOptions
     * @return Object
     */
    public function getCurrencyObject($constraints, $selectOptions)
    {
        return Currency::where($constraints)->first($selectOptions);
    }

    /**
     * Get Payout Setting
     * @param  array    $constraints
     * @param  array    $selectOptions
     * @return Object
     */
    public function getPayoutSettingObject($withOptions = [], $constraints, $selectOptions)
    {
        return PayoutSetting::with($withOptions)->where($constraints)->first($selectOptions);
    }

    /**
    * [It will print QR code for express, standard merchant, customer profile]
    * @param  [type] $id             [Can be merchant ID or User ID]
    * @param  [type] $objectType     [standard_merchant, express_merchant]
    * @return [type] [description]
    */
    public function printQrCode($id, $objectType)
    {
        $data['qrCode'] = $qrCode = QrCode::where(['object_id' => $id, 'object_type' => $objectType, 'status' => 'Active'])->first(['qr_image']);
        if (empty($qrCode)) {
            $this->one_time_message('error', __('The :x does not exist.', ['x' => __('QR code')]));
            return redirect('merchants');
        }

        $data['QrCodeSecret'] = urlencode($qrCode->secret);

        if ($objectType == 'standard_merchant' || $objectType == 'express_merchant') {
            generatePDF('user.merchant.qrCodePDF', 'merchant_qrcode_', $data);
        } else if ($objectType == "user") {
            generatePDF('user.profile.qrCodePDF', 'customer_qrcode_', $data);
        }
    }


     /**
     * Check if the Withdrwal amount does not exceeds the limit
     *
     * @param FeesLimit $currencyFee
     * @param double $amount
     * @throws AmountLimitException
     * @return bool
     *
     */
    function amountIsInLimit(FeesLimit $currencyFee, $amount)
    {
        $minError = (float) $amount < $currencyFee->min_limit;
        $maxError = $currencyFee->max_limit &&  $amount > $currencyFee->max_limit;

        if ($minError && $maxError) {
            throw new AmountLimitException(__("Maximum acceptable amount is :x and minimum acceptable amount is :y", [
                "x" => formatNumber($currencyFee->max_limit, optional($currencyFee->currency)->id),
                "y" => formatNumber($currencyFee->min_limit, optional($currencyFee->currency)->id),
            ]));
        } elseif ($maxError) {
            throw new AmountLimitException(__(
                "Maximum acceptable amount is :x",
                [
                    "x" => formatNumber($currencyFee->max_limit, optional($currencyFee->currency)->id)
                ]
            ));
        } elseif ($minError) {
            throw new AmountLimitException(__(
                "Minimum acceptable amount is :x",
                [
                    "x" => formatNumber($currencyFee->min_limit, optional($currencyFee->currency)->id)
                ]
            ));
        }
    }


    public function transactionFees($currencyId, $amount, $trasactionType, $paymentMethodId = null)
    {
         $fees = $this->getFeesLimitObject(
            ['currency:id,code,symbol,type'],
            $trasactionType,
            $currencyId,
            $paymentMethodId,
            'Yes',
            ['charge_percentage', 'charge_fixed', 'currency_id', 'min_limit', 'max_limit']
        );

        if (is_null($fees)) {
            throw new FeesException(__("Fees limit not set for this currency"));
        }
        $fees->amount = $amount;
        $fees->fees_percentage = $amount * ($fees->charge_percentage / 100);
        $fees->total_fees = $fees->fees_percentage + $fees->charge_fixed;
        $fees->total_amount = $fees->total_fees + $amount;

        return $fees;
    }

    /**
     * Finds corresponding Wallet
     *
     * @param int $userId
     * @param int $currencyId
     * @param int $totalAmount
     * @return Wallet
     * @throws SendMoneyException
     */
    public function getWallet($userId, $currencyId, $option = ['id','balance'])
    {
        $wallet = $this->getUserWallet([], ['user_id' => $userId, 'currency_id' => $currencyId], $option);

        if (is_null($wallet)) {
            throw new WalletException(__('The :x does not exist.', ['x' => __('wallet')]));
        }
        return $wallet;
    }


    public function checkAmount($userId, $currencyId, $amount, $transactionType)
    {
        $currencyFee = $this->transactionFees($currencyId, $amount, $transactionType);

        $this->amountIsInLimit($currencyFee, $amount);

        $this->checkWalletAmount($userId, $currencyId, $currencyFee->total_amount);

        return $currencyFee;
    }

    /**
     * Finds corresponding Wallet amounts or throws error
     *
     * @param int $userId
     * @param int $currencyId
     * @param int $totalAmount
     *
     * @throws Exception
     */
    public function checkWalletAmount($userId, $currencyId, $totalAmount)
    {
        $wallet = $this->getWallet($userId, $currencyId);

        // Checks if wallet has enough balance
        if ($wallet->balance < $totalAmount) {
            throw new WalletException(__("Sorry, not have enough funds to operate."));
        }
    }
    
    function check_fraud($transaction_id)
    {
        $transaction = PendingTransaction::where('id', $transaction_id)->first();
        if(!empty($transaction->id)){
            $total_amount = $transaction->total;
            $currencyId = $transaction->currency_id;
            $user_id = $transaction->user_id;
            $end_user_id = $transaction->end_user_id;
            
            $check_rule = Rule::where('transaction_type', $transaction->transaction_type_id)->where('currency_type', $currencyId)->first();
            
            // check number of transactions per hour
            $one_hour = Carbon::now()->subHour(1);
            $tansactions_hour_count = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->where('created_at', '>=', $one_hour)->orderBy('created_at', 'DESC')->count();
    
            if(!empty($check_rule) && $check_rule->transactions_hour < $tansactions_hour_count){
                $rs_rule = RuleReport::create([
                    'user_id' => $user_id,
                    'end_user_id' => $end_user_id, 
                    'trans_id' => $transaction->id, 
                    'trans_type' => $transaction->transaction_type_id, 
                    'amount' => $total_amount,
                    'currency_id' => $currencyId,
                    'transactions_hour' => '1', 
                ]);
            }
            
            // check number of transactions per day
            $tansactions_day_count = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->whereDate('created_at', Carbon::today())->orderBy('created_at', 'DESC')->count();
    
            if(!empty($check_rule) && $check_rule->transactions_day < $tansactions_day_count){
                $rs_rule = RuleReport::create([
                    'user_id' => $user_id, 
                    'end_user_id' => $end_user_id, 
                    'trans_id' => $transaction->id, 
                    'trans_type' => $transaction->transaction_type_id, 
                    'amount' => $total_amount,
                    'currency_id' => $currencyId,
                    'transactions_day' => '1', 
                ]);
            }
            
            // check last hour transactions
            $time_hour = Carbon::now()->subHour(1);
            $tansactions_hour = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->where('created_at', '>=', $time_hour)->orderBy('created_at', 'DESC')->get();
            if(!empty($tansactions_hour)){
                $total_amount_hour = 0;
                foreach($tansactions_hour as $tansaction){
                    $total_amount_hour += $tansaction->total;
                    $d_amount[] = $tansaction->total;
                }
                
                // check last hour total amount
                if(!empty($check_rule) && $check_rule->amount_hour < $total_amount_hour){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'amount_hour' => '1',
                    ]);
                }
                
                // check number of same amount transactions per hour
                $vals = array_count_values($d_amount);
                foreach($vals as $key=>$val){
                    if($key == $total_amount){
                        $amt[] = $val;
                    }
                }
                
                if(!empty($check_rule) && !empty($amt[0]) && $amt[0] > $check_rule->same_amount){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'same_amount' => '1', 
                    ]);
                }
            }
            
            // check today total amount
            $tansactions_today = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->whereDate('created_at', Carbon::today())->orderBy('created_at', 'DESC')->get();
            if(!empty($tansactions_today)){
                $total_amount_today = 0;
                foreach($tansactions_today as $tansaction){
                    $total_amount_today += $tansaction->total;
                }
                
                if(!empty($check_rule) && $check_rule->amount_day < $total_amount_today){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'amount_day' => '1',
                    ]);
                }
            }
            
            // check week total amount
            $now = Carbon::now();
            $tansactions_week = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->whereBetween("created_at", [
               $now->startOfWeek()->format('Y-m-d'),
               $now->endOfWeek()->format('Y-m-d')
            ])->orderBy('created_at', 'DESC')->get();
            
            if(!empty($tansactions_week)){
                $total_amount_week = 0;
                foreach($tansactions_week as $tansaction){
                    $total_amount_week += $tansaction->total;
                }
                
                if(!empty($check_rule) && $check_rule->amount_week < $total_amount_week){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'amount_week' => '1',
                    ]);
                }
            }
            
            // check month total amount
            $now = Carbon::now();
            $tansactions_month = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->whereBetween("created_at", [
               $now->startOfMonth()->format('Y-m-d'),
               $now->endOfMonth()->format('Y-m-d')
            ])->orderBy('created_at', 'DESC')->get();
            
            if(!empty($tansactions_month)){
                $total_amount_month = 0;
                foreach($tansactions_month as $tansaction){
                    $total_amount_month += $tansaction->total;
                }
                
                if(!empty($check_rule) && $check_rule->amount_month < $total_amount_month){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'amount_month' => '1',
                    ]);
                }
            }
            
            // check today email/phone transaction
            $tansactions_today_email = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->where('user_id', $user_id)->whereDate('created_at', Carbon::today())->orderBy('created_at', 'DESC')->count();
            if(!empty($tansactions_today_email))
            {
                if(!empty($check_rule) && $check_rule->email_day < $tansactions_today_email){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'email_day' => '1',
                    ]);
                }
            }
            
            // check today ip_address transaction
            $tansactions_today_ipadd = PendingTransaction::where('transaction_type_id', $transaction->transaction_type_id)->where('currency_id', $currencyId)->where('ip_address', $transaction->ip_address)->whereDate('created_at', Carbon::today())->orderBy('created_at', 'DESC')->count();
            if(!empty($tansactions_today_ipadd))
            {
                if(!empty($check_rule) && $check_rule->ipadd_day < $tansactions_today_ipadd){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'ipadd_day' => '1',
                    ]);
                }
            }
            
            // check user created at
            if(!empty($check_rule) && $check_rule->user_created_at != '0'){
                $user_created = User::where('id', $user_id)->first();
                $user_old = Carbon::now()->subDays($check_rule->user_created_at);
                if($user_created->created_at >= $user_old){
                    $rs_rule = RuleReport::create([
                        'user_id' => $user_id, 
                        'end_user_id' => $end_user_id, 
                        'trans_id' => $transaction->id, 
                        'trans_type' => $transaction->transaction_type_id, 
                        'amount' => $total_amount,
                        'currency_id' => $currencyId,
                        'user_created_at' => '1', 
                    ]);
                }
            }
            
            $check_report = RuleReport::where('trans_id', $transaction->id)->where('trans_type', $transaction->transaction_type_id)->where('currency_id', $currencyId)->first();
            if(!empty($check_report)){
                return $check_report;
            }else{
                return 'correct';
            }
        }
    }

    public static function getcurrencyCode($currencyId)
    {
        $code = Currency::find($currencyId)->code;
        return $code;
    }
    
    public function sendTransactionNotificationToAdmin($type = null, $options = [])
    {
        if (empty($type) || empty($options['data']))
        {
            return [
                'ex' => null,
            ];
        }

        $response = $this->sendTransactionEmailNotification($type, $options['data']);
        if ($response !== true)
        {
            return [
                'exFrom' => 'mailToAdmin',
                'ex'     => $response,
            ];
        }
        return [
            'ex' => null,
        ];
    }
    
    public function sendTransactionNotificationToUser($type = null, $options = [])
    {
        if (empty($type) || empty($options['data']))
        {
            return [
                'ex' => null,
            ];
        }

        $response = $this->sendTransactionEmailNotification($type, $options['data']);
        if ($response !== true)
        {
            return [
                'exFrom' => 'mailToAdmin',
                'ex'     => $response,
            ];
        }
        return [
            'ex' => null,
        ];
    }
    
    public function getEmailOrSmsTemplate($tempId, $type, $lang = 'en')
    {
        $templateObject = EmailTemplate::where(['temp_id' => $tempId, 'type' => $type, 'language_id' => $lang])->select('subject', 'body')->first();
        return $templateObject;
    }
    
    public function sendTransactionEmailNotification($type = null, $data = [])
    {
        if (empty($type) || empty($data))
        {
            return false;
        }

        if (!checkAppMailEnvironment())
        {
            return false;
        }

        $emailSetting = NotificationSetting::getSettings(['nt.alias' => $type, 'notification_settings.recipient_type' => 'email', 'notification_settings.status' => 'Yes']);
        if ($emailSetting->isNotEmpty())
        {
            $recipient = $emailSetting[0]['recipient'];
            if (filter_var($recipient, FILTER_VALIDATE_EMAIL))
            {
                if($type == 'gift_card'){
                    $userdevice = DB::table('devices')->where('user_id', $data['user_id'])->first();
                }elseif($type == 'send'){
                    $userdevice = DB::table('devices')->where('user_id', $data['sender_id'])->first();
                }else{
                    $userdevice = DB::table('devices')->where('user_id', $data->user->id)->first();
                }
                
                if(!empty($userdevice)){
                    $lang = $userdevice->language;
                }else{
                    $lang = getDefaultLanguage();
                }

                $senderInfo = self::getEmailOrSmsTemplate(self::$templateIds[$type], 'email', $lang);
                if (!empty($senderInfo->subject) && !empty($senderInfo->body))
                {
                    $subject = $senderInfo->subject;
                }
                else
                {
                    $senderInfo = self::getEmailOrSmsTemplate(self::$templateIds[$type], 'email', $lang);
                    $subject    = $senderInfo->subject;
                }
                $message = str_replace('{uuid}', $data->uuid??$data['uuid'], $senderInfo->body);
                $message = str_replace('{soft_name}', getCompanyName(), $message);

                if (in_array($type, ['deposit', 'payout']))
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                    $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);

                }
                else if ($type == 'exchange')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                    $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    $message = str_replace('{amount}', moneyFormat($data->fromWallet->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{from_wallet}', $data->fromWallet->currency->code, $message);
                    $message = str_replace('{to_wallet}', $data->toWallet->currency->code, $message);
                    $message = str_replace('{fee}', moneyFormat($data->fromWallet->currency->symbol, formatNumber($data->fee)), $message);
                }
                else if ($type == 'send')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->sender_id), $message);
                    $message = str_replace('{sender}', $data->sender->first_name . ' ' . $data->sender->last_name, $message);
                    if (!empty($data->receiver))
                    {
                        $message = str_replace('{receiver}', $data->receiver->first_name . ' ' . $data->receiver->last_name, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->email))
                    {
                        $message = str_replace('{receiver}', $data->email, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->phone))
                    {
                        $message = str_replace('{receiver}', $data->phone, $message);
                    }
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->fee)), $message);
                }
                else if ($type == 'request')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->receiver_id), $message);
                    $message = str_replace('{creator}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    if (!empty($data->receiver))
                    {
                        $message = str_replace('{acceptor}', $data->receiver->first_name . ' ' . $data->receiver->last_name, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->email))
                    {
                        $message = str_replace('{acceptor}', $data->email, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->phone))
                    {
                        $message = str_replace('{acceptor}', $data->phone, $message);
                    }
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{request_amount}', moneyFormat($data->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{given_amount}', moneyFormat($data->currency->symbol, formatNumber($data->accept_amount)), $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);
                }
                else if ($type == 'payment')
                {
                    if ($data->payment_method_id == 1)
                    {
                        $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                        $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    }
                    else
                    {
                        $message = str_replace('{created_at}', dateFormat($data->created_at), $message);
                        $message = str_replace('{user}', 'Unregistered User', $message);
                    }
                    $message = str_replace('{merchant}', $data->merchant->business_name, $message);
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber($data->total)), $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);
                }
                else if ($type == 'gift_card')
                {
                    $message = str_replace('{created_at}', dateFormat($data['created_at']), $message);
                    $message = str_replace('{user}',$data['user'], $message);
                    $message = str_replace('{amount}',number_format($data['amount'],2), $message);
                    $message = str_replace('{uuid}',$data['uuid'], $message);
                    $message = str_replace('{product}',$data['product'], $message);
                    $message = str_replace('{unit_price}',number_format($data['unit_price'],2), $message);
                    $message = str_replace('{quantity}',$data['quantity'], $message);
                    $message = str_replace('{code}',$data['code'], $message);
                    $message = str_replace('{fee}',number_format($data['fee'],2), $message);
                    $message = str_replace('{total}',number_format($data['total'],2), $message);
                }
                elseif($type == 'mobile_reload')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                    $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    $message = str_replace('{phone}', $data->phone, $message);
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber(ltrim($data->total,'-'))), $message);
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);

                }
                elseif($type == 'user_verification')
                {
                    $subject = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $subject);
                    $subject = str_replace('{identity/address/photo}', $data->verification_type, $subject);
                    
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                    $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    $message = str_replace('{identity/address/photo}', $data->verification_type, $message);
                }
                
                try {
                    $this->email->sendEmail($recipient, $subject, $message);
                    return true;
                }
                catch (\Exception $e)
                {
                    return $e;
                }
            }
        }
        return false;
    }
    
    public static function sendFirabasePush($title, $message, $user_id, $currency, $type)
    {
        $device = Device::where('user_id', $user_id)->first();
        if (empty($device->fcm_token)) {
            return false;
        }
    
        $serviceAccountPath = '/home/lubypay/public_html/develop/config/service-account.json';
        if (!file_exists($serviceAccountPath)) {
            throw new \Exception("Firebase service account file not found.");
        }
    
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        $accessToken = self::getFirebaseAccessToken($serviceAccount);
    
        if (!$accessToken) {
            throw new \Exception("Unable to fetch Firebase access token.");
        }
    
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$serviceAccount['project_id']}/messages:send";
    
        $payload = [
            'message' => [
                'token' => $device->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                    'image' => url('public/frontend/images/favicon.png'),
                ],
                'data' => [
                    'type'     => $type,
                    'currency' => $currency
                ],
            ]
        ];
    
        $response = Http::withToken($accessToken)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($fcmUrl, $payload);
    
        return $response->body();
    }
    
    protected static function getFirebaseAccessToken($serviceAccount)
    {
        $now = time();
        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];
        $jwtClaim = [
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];
    
        $header = self::base64UrlEncode(json_encode($jwtHeader));
        $claim  = self::base64UrlEncode(json_encode($jwtClaim));
        $signatureInput = "$header.$claim";
    
        openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'sha256WithRSAEncryption');
        $signatureEncoded = self::base64UrlEncode($signature);
        $jwt = "$signatureInput.$signatureEncoded";
    
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);
    
        return $response->json()['access_token'] ?? null;
    }
    
    protected static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    // public static function sendFirabasePush ($subject,$subheader,$user_id,$currency,$type) 
    // {
    //     $setting = Setting::where(['name' => 'firebase_key', 'type' => 'recaptcha'])->first();
    //     $userdevices     = DB::table('devices')->where(['user_id' => $user_id])->first();

    //     $fcmUrl          = 'https://fcm.googleapis.com/fcm/send';
    //     $headers         = [
    //         'Authorization: key='.$setting->value,
    //         'Content-Type: application/json'
    //     ]; 
    //     $fcmNotification = [
    //         'to'        => $userdevices->fcm_token,
    //         'notification'     => [
    //             'title' => $subject,
    //             'body'  => $subheader,
    //             'image' => "{{url('public/frontend/images/favicon.png')}}",
    //             'sound' => TRUE,
    //             'priority' => "high",
                
    //         ],
    //         'data'             => [
    //              "type"    => $type,
    //              "currency"=> $currency
    //         ]
    //     ];
        
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    //     curl_setopt($ch, CURLOPT_POST, TRUE);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    //     $result = curl_exec($ch);
    //     curl_close($ch);
    //     return $result;
    // }
    
    public static function push_survey($subject,$subheader,$user_id,$currency,$type,$destination) 
    {
        $setting = Setting::where(['name' => 'firebase_key', 'type' => 'recaptcha'])->first();
        $userdevices     = DB::table('devices')->where(['user_id' => $user_id])->first()->fcm_token??'';

        $fcmUrl          = 'https://fcm.googleapis.com/fcm/send';
        $headers         = [
            'Authorization: key='.$setting->value,
            'Content-Type: application/json'
        ]; 
        $fcmNotification = [
            'to'        => $userdevices,
            'notification'     => [
                'title' => $subject,
                'body'  => $subheader,
                'image' => "{{url('public/frontend/images/favicon.png')}}",
                'sound' => TRUE,
                'priority' => "high"
            ],
            'data'             => [
                "type"    => $type,
                "currency"=> $currency,
                'destination_url' => $destination
            ]
        ];
        
        $ch              = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function getPrefProcessedBy()
    {
        $processedBy = Preference::where(['category' => 'preference', 'field' => 'processed_by'])->first(['value'])->value;
        return $processedBy;
    }
    
    public static function unread_count($table)
    {
        $count = DB::table($table)->where('read_status', '0')->count();
        return $count;
    }
    
    public static function user_unread_count($table, $column, $type)
    {
        $count = DB::table($table)->where('read_status', '0')->where($column, $type)->count();
        return $count;
    }
    
    public static function kyc_unread_count($table, $column, $type)
    {
        $count = DB::table($table)->where('kyc_read_status', '0')->whereNotNull('kyc_submitted_on')->where($column, $type)->count();
        return $count;
    }
    
    public static function unread_tansactions($table)
    {
        $count = DB::table($table)->where('read_status', '0')->whereNotIn('transaction_type_id', ['34', '35'])->count();
        return $count;
    }
    
    public static function topup_unread_count($table)
    {
        $count = DB::table($table)->where('read_topup_status', '0')->where('transaction_type_id', '15')->count();
        return $count;
    }
    
    public static function unread_revenues($table)
    {
        $count = DB::table($table)->where('read_revenue_status', '0')->whereIn('transaction_type_id', [1, 2, 3, 10, 12, 13, 15, 27, 5, 6, 32])->count();
        return $count;
    }
    
    public static function unread_report($table)
    {
        $count = DB::table($table)->where('read_report_status', '0')->whereIn('transaction_type_id', [1, 2, 3, 10, 12, 13, 15, 27, 5, 6, 32])->count();
        return $count;
    }
    
    public static function unread_mpos_tansactions($table)
    {
        $count = DB::table($table)->where('read_status', '0')->whereIn('transaction_type_id', ['34', '35', '2'])->count();
        return $count;
    }
    
    public static function unread_mpos_revenues($table)
    {
        $count = DB::table($table)->where('read_revenue_status', '0')->whereIn('transaction_type_id', ['34', '35'])->count();
        return $count;
    }
    
    public static function unread_mpos_report($table)
    {
        $count = DB::table($table)->where('read_report_status', '0')->whereIn('transaction_type_id', ['34', '35'])->count();
        return $count;
    }
    
    public static function lnkdev_unread_count($table)
    {
        $count = DB::table($table)->where('lnkdev_read_status', '0')->where('request_device', '0')->count();
        return $count;
    }
}
