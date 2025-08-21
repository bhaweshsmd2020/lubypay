<?php

namespace App\Http\Controllers\Api;
require_once(base_path('/vendor/stripe_new/stripe-php/init.php'));
error_reporting(0);
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\CurrencyPaymentMethod;
use App\Models\Deposit;
use App\Models\FeesLimit;
use App\Models\File;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use PayPal\Api\Amount;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Validator;
use Carbon\Carbon;
use DB;
use App\Models\DocumentVerification;
use Braintree;
use App\Models\User;
use App\Models\StripeIntent;
use App\Models\Notification;
use App\Models\PendingTransaction;
use App\Models\EmailTemplate;
use App\Http\Controllers\Users\EmailController;
use App\Models\TransDeviceInfo;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class DepositMoneyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper  = new Common();
        $this->email  = new EmailController();
    }

    //Deposit Money Starts here
    public function getDepositCurrencyList()
    {    
        $user_id=request('user_id');
        $activeCurrency                     = Currency::where(['status' => 'Active'])->orderBy('position','ASC')->get(['id', 'code', 'status', 'logo', 'name','symbol']);
        $feesLimitCurrency                  = FeesLimit::where(['transaction_type_id' => Deposit, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);

        //Set default wallet as selected - starts
        $defaultWallet                      = Wallet::where(['user_id' => request('user_id'), 'is_default' => 'Yes'])->first(['currency_id','balance']);
        $success['defaultWalletCurrencyId'] = $defaultWallet->currency_id;
        // $success['balance'] = $defaultWallet->balance;

        //Set default wallet as selected - ends

          $success['currencies']              = $this->currencyList($activeCurrency, $feesLimitCurrency,$user_id);
        //$success['status']                  = $this->successStatus;
        return response()->json(['status'=>$this->successStatus,'success' => $success], $this->successStatus);
        return response()->json(['success' => $success], $this->successStatus);
    }

        //Extended function - 1
    public function currencyList($activeCurrency, $feesLimitCurrency,$user_id)
    {
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency)
        {
            foreach ($feesLimitCurrency as $flCurrency)
            {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes')
                {  
                    $balance=Wallet::where(['user_id' =>$user_id, 'currency_id' => $aCurrency->id])->first()->balance??'00';
                    
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['balance']   = number_format($balance,2);
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                    $selectedCurrency[$aCurrency->id]['logo'] = $aCurrency->logo;
                    $selectedCurrency[$aCurrency->id]['symbol'] = $aCurrency->symbol;
                    $selectedCurrency[$aCurrency->id]['name'] = $aCurrency->name;
                    $selectedCurrency[$aCurrency->id]['min_limit'] = $flCurrency->min_limit;
                    $selectedCurrency[$aCurrency->id]['max_limit'] = $flCurrency->max_limit;
                    $selectedCurrency[$aCurrency->id]['charge_fixed'] = $flCurrency->charge_fixed;
                    $selectedCurrency[$aCurrency->id]['charge_percentage'] = $flCurrency->charge_percentage;
                    $selectedCurrency[$aCurrency->id]['base_url'] = env('CURRENCY_LOGO');
                }
            }
        }
        return array_values($selectedCurrency);
    }

    //getMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods
    public function getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods(Request $request)
    {
        $feesLimits = FeesLimit::whereHas('currency', function($q)
        {
            $q->where('status','=','Active');
        })
        ->whereHas('payment_method', function($q)
        {
            $q->whereIn('name', ['Stripe', 'Paypal', 'Bank'])->where('status','=','Active');
        })
        ->where(['transaction_type_id' => $request->transaction_type_id, 'has_transaction' => 'Yes', 'currency_id' => $request->currency_id])
        ->get(['payment_method_id']);
        // dd($feesLimits);

        $currencyPaymentMethods                       = CurrencyPaymentMethod::where('currency_id', $request->currency_id)->where('activated_for', 'like', "%deposit%")->get(['method_id']);
        $currencyPaymentMethodFeesLimitCurrenciesList = $this->currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods);
        $success['paymentMethods']                    = $currencyPaymentMethodFeesLimitCurrenciesList;
        $success['status']                            = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    //Extended function - 2
    public function currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods)
    {
        $selectedCurrencies = [];
        foreach ($feesLimits as $feesLimit)
        {
            foreach ($currencyPaymentMethods as $currencyPaymentMethod)
            {
                if ($feesLimit->payment_method_id == $currencyPaymentMethod->method_id)
                {
                    $selectedCurrencies[$feesLimit->payment_method_id]['id']   = $feesLimit->payment_method_id;
                    $selectedCurrencies[$feesLimit->payment_method_id]['name'] = $feesLimit->payment_method->name;
                }
            }
        }
        return $selectedCurrencies;
    }
    
    public function getFeesListByPaymentMethod(Request $request) 
    {
        $user_id         = request('user_id'); 
        $paymentMethodId = request('paymentMethodId');
        $currencyId      = request('currencyId');
        try
        {
            \DB::beginTransaction();
            
            $success['tran_limit_message'] = "You are eligble for add fund!";
            $success['limit_status'] = "0";
            $feesDetails = FeesLimit::where(['currency_id' => $currencyId, 'payment_method_id' => $paymentMethodId])->get(['transaction_type_id','charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
            $success['feesdetails'] = $feesDetails;
            return response()->json(['status'=>$this->successStatus,'success' => $success]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    
    public function getFeesListByForTopup() 
    {
        $validation = Validator::make(request()->all(), [
            'transactionTpyeId' => 'required',
            'currencyId' => 'required',
            'amount'     => 'required'
        ]);
        if ($validation->fails())
        {
            $data['status']  = 401;
            $data['message'] = $validation->errors();
            return response()->json(['success' => $data]);
        }
        $transactionTpyeId = request('transactionTpyeId');
        $currencyId        = request('currencyId');
        $feesDetails       = FeesLimit::where(['currency_id' => $currencyId, 'transaction_type_id' => $transactionTpyeId])->first(['transaction_type_id','charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
            
        $percentage = $feesDetails->charge_percentage;
        $totalWidth = request('amount');  
        $tot_fees   = ($totalWidth / 100) * $percentage+ $feesDetails->charge_fixed;
        $success['total_fees'] = $tot_fees;
        $success['total_amt']  = $totalWidth+$tot_fees;
        return response()->json(['status'=>$this->successStatus,'success' => $success]);
    }

    public function getDepositDetailsWithAmountLimitCheck()
    {
        $user_id         = request('user_id');
        $amount          = request('amount');
        $currency_id     = request('currency_id');
        $paymentMethodId = request('paymentMethodId');

        $success['paymentMethodName'] = PaymentMethod::where('id', $paymentMethodId)->first(['name'])->name;
        $wallets                      = Wallet::where(['currency_id' => request('currency_id'), 'user_id' => $user_id])->first(['balance']);

        $feesDetails = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => request('currency_id'), 'payment_method_id' => $paymentMethodId])
            ->first(['charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
        //  dd($feesDetails);

        if (@$feesDetails->max_limit == null)
        {
            if ((@$amount < @$feesDetails->min_limit))
            {
                $success['reason']   = 'minLimit';
                $success['minLimit'] = @$feesDetails->min_limit;
                $success['message']  = 'Minimum amount ' . formatNumber(@$feesDetails->min_limit);
                $success['status']   = '401';
                return response()->json(['success' => $success]);
            }
            else
            {
                $success['status'] = 200;
            }
        }
        else
        {
            if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit))
            {
                $success['reason']   = 'minMaxLimit';
                $success['minLimit'] = @$feesDetails->min_limit;
                $success['maxLimit'] = @$feesDetails->max_limit;
                $success['message']  = 'Minimum amount ' . formatNumber(@$feesDetails->min_limit) . ' and Maximum amount ' . formatNumber(@$feesDetails->max_limit);
                //$success['status']   = '401';
                return response()->json(['status'=>401,'success' => $success]);
            }
            else
            {
                $success['status'] = 200;
            }
        }
        //Code for Amount Limit ends here

        //Code for Fees Limit Starts here
        if (empty($feesDetails))
        {
            $success['message'] = "ERROR";
            $success['status']  = 401;
        }
        else
        {
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['amount']         = $amount;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess);
            $success['currency_id']    = $feesDetails->currency_id;
            $success['currSymbol']     = $feesDetails->currency->symbol;
            $success['currCode']       = $feesDetails->currency->code;
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesDetails->charge_percentage;
            $success['fFees']          = $feesDetails->charge_fixed;
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $success['balance']        = @$wallets->balance ? @$wallets->balance : 0;
            $success['status']         = 200;
        }
        $status =  $success['status'];
        return response()->json(['status'=>$status,'success' => $success]);
    }
    
    /**
     * Payment Methods Starts
     * @return [type] [description]
     */
    //Get Payment Methods
    public function getPaymentMethodList()
    {
        $PaymentMethod = PaymentMethod::where('status','Active')->get();
        if (empty($PaymentMethod))
        {
            $success['message'] = __('Payment gateways not found!');
            $success['status']  = 401;
        }
        else
        {
            $success['data'] = $PaymentMethod;
            //$success['status']      = 200;
            return response()->json(['status'=>200,'success' => $success]);
        }
    }

    /**
     * Stripe Starts
     * @return [type] [description]
     */
    //Get Stripe Info
    public function getStripeInfo()
    {
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => request('currency_id'), 'method_id' => request('method_id')])
            ->where('activated_for', 'like', "%deposit%")
            ->first(['method_data']);
        if (empty($currencyPaymentMethod))
        {
            $success['message'] = __('Payment gateway credentials not found!');
            $success['status']  = 401;
        }
        else
        {
            $success['stripe_keys'] = json_decode($currencyPaymentMethod->method_data);
            $success['status']      = 200;
            return response()->json(['success' => $success]);
        }
    }

    //Deposit Confirm Post via Stripe
   public function stripePaymentStore()
    {
        // dd(request()->all()); 
        $validation = Validator::make(request()->all(), [
            'stripeToken' => 'required',
        ]);
        if ($validation->fails())
        {
            $data['status']  = 401;
            $data['message'] = $validation->errors();
            return response()->json(['success' => $data]);
        }
        $payment_method_id = request('deposit_payment_id');

        $user_id = request('user_id');
        $wallet  = Wallet::where(['currency_id' => request('currency_id'), 'user_id' => $user_id])->first(['id', 'currency_id']);
        try {
            \DB::beginTransaction();

            if (empty($wallet))
            {
                $walletInstance              = new Wallet();
                $walletInstance->user_id     = $user_id;
                $walletInstance->currency_id = request('currency_id');
                $walletInstance->balance     = 0;
                $walletInstance->is_default  = 'No';
                $walletInstance->save();
            }
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);
            //return $_POST; die;
            if (request()->all())
            {
                if (request('stripeToken') != null)
                {
                    $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $payment_method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
                    $methodData            = json_decode($currencyPaymentMethod->method_data);
                    $totalAmount           = (float) request('totalAmount');
                    $amount                = (float) request('amount');
                    $stripe                = Setting::where(['type' => 'Stripe', 'name' => 'secret'])->first();
                    $gateway               = Omnipay::create('Stripe');
                    $gateway->setApiKey($methodData->secret_key);
                    $response = $gateway->purchase([
                        //Stripe accepts 2 decimal places only(only for server) - if not rounded to 2 decimal places, it will throw error - Amount precision is too high for currency.
                        'amount'   => number_format($totalAmount, 2, '.', ''),
                        'currency' => $currency->code,
                        'token'    => request('stripeToken'),
                    ])->send();

                    if ($response->isSuccessful())
                    {
                        $token         = $response->getTransactionReference();
                        $feeInfo       = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currencyId, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
                        $feePercentage = $amount * ($feeInfo->charge_percentage / 100);

                        //Save to Deposit
                        $uuid                       = unique_code();
                        $deposit                    = new Deposit();
                        $deposit->user_id           = $user_id;
                        $deposit->currency_id       = $currencyId;
                        $deposit->payment_method_id = $payment_method_id;
                        $deposit->uuid              = $uuid;
                        $deposit->charge_percentage = $feePercentage;
                        $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                        $deposit->amount            = $amount;
                        $deposit->status            = 'Success'; //in Stripe deposit, status will be success
                        $deposit->save();

                        //Save to Transaction
                        $transaction                           = new Transaction();
                        $transaction->user_id                  = $user_id;
                        $transaction->currency_id              = $currencyId;
                        $transaction->payment_method_id        = $payment_method_id;
                        $transaction->uuid                     = $uuid;
                        $transaction->transaction_reference_id = $deposit->id;
                        $transaction->transaction_type_id      = Deposit;
                        $transaction->subtotal                 = $deposit->amount;
                        $transaction->percentage               = $feeInfo->charge_percentage;
                        $transaction->charge_percentage        = $feePercentage;
                        $transaction->charge_fixed             = $feeInfo->charge_fixed;
                        $transaction->total                    = ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
                        $transaction->status                   = 'Success';
                        $transaction->save();

                        //Update to Wallet
                        $wallet          = Wallet::where(['user_id' => $user_id, 'currency_id' => $currencyId])->first(['id', 'balance']);
                        $wallet->balance = ($wallet->balance + $transaction->subtotal);
                        $wallet->save();

                        \DB::commit();

                        // Send notification to admin
                        $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

                        $data['transaction'] = $transaction;
                        $data['status']      = 200;
                        return response()->json(['status'=>200,'success' => $data]);
                    }
                    else
                    {
                        $data['status']  = 401;
                        $data['message'] = $validation->errors();
                        return response()->json(['status'=>401,'success' => $data]);
                    }
                }
                else
                {
                    $data['status']  = 401;
                    $data['message'] = $validation->errors();
                    return response()->json(['status'=>401,'success' => $data]);
                }
            }
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['status'=>$this->unauthorisedStatus,'success' => $success], $this->unauthorisedStatus);
        }
    }
    /**
     * Stripe Ends
     * @return [type] [description]
     */

    /**
     * Paypal Starts
     * @return [type] [description]
     */
    //Get Paypal Info
    public function getPeypalInfo()
    {
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => request('currency_id'), 'method_id' => request('method_id')])
            ->where('activated_for', 'like', "%deposit%")
            ->first(['method_data']);

        if (empty($currencyPaymentMethod))
        {
            $success['message'] = __('Payment gateway credentials not found!');
            $success['status']  = 401;
        }
        else
        {
            $success['method_info'] = json_decode($currencyPaymentMethod->method_data);
            $success['status']      = 200;
            return response()->json(['success' => $success]);
        }
    }

    public function paypalSetup()
    {
        $numarr = func_num_args();
        if ($numarr > 0)
        {
            $clientID   = func_get_arg(0);
            $secret     = func_get_arg(1);
            $mode       = func_get_arg(2);
            $apicontext = new ApiContext(new OAuthTokenCredential($clientID, $secret));
            $apicontext->setConfig([
                'mode' => $mode,
            ]);
        }
        else
        {
            $credentials = Setting::where(['type' => 'PayPal'])->get();
            $clientID    = $credentials[0]->value;
            $secret      = $credentials[1]->value;
            $apicontext  = new ApiContext(new OAuthTokenCredential($clientID, $secret));
            $apicontext->setConfig([
                'mode' => $credentials[3]->value,
            ]);
        }

        return $apicontext;
    }

    //Deposit Confirm Post via Paypal
    public function peypalPaymentStore()
    {
        //print_r(request()->all()); die;
        if (request('details')['status'] != 'COMPLETED')
        {
            $success['status']  = 401;
            $success['message'] = __('Unsuccessful Transaction');
            return response()->json(['success' => $success]);
        }

        $amount            = request('amount');
        $currency_id       = request('currencyID');
        $payment_method_id = request('methodID');
        $user_id           = request('userId');
        $uuid              = unique_code();
        $feeInfo           = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currency_id, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
        $wallet            = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['id', 'balance']);

        try {
            \DB::beginTransaction();

            if (empty($wallet))
            {
                $walletInstance              = new Wallet();
                $walletInstance->user_id     = $user_id;
                $walletInstance->currency_id = $currency_id;
                $walletInstance->balance     = 0;
                $walletInstance->is_default  = 'No';
                $walletInstance->save();
            }
            $feePercentage = $amount * ($feeInfo->charge_percentage / 100);

            //Save to Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = $user_id;
            $deposit->currency_id       = $currency_id;
            $deposit->payment_method_id = $payment_method_id;
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = $feePercentage;
            $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $deposit->amount            = $amount;
            $deposit->status            = 'Success'; //in paypal deposit, status will be success
            $deposit->save();

            //Save to Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $currency_id;
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = $feeInfo->charge_percentage;
            $transaction->charge_percentage        = $feePercentage;
            $transaction->charge_fixed             = $feeInfo->charge_fixed;
            $transaction->total                    = ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
            $transaction->status                   = 'Success';
            $transaction->payment_method_id        = $payment_method_id;
            $transaction->save();

            //Update to Wallet
            $wallet->balance = ($wallet->balance + $transaction->subtotal);
            $wallet->save();

            \DB::commit();

            // Send notification to admin
            $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

            $success['transaction'] = $transaction;
            $success['status']      = 200;
            return response()->json(['success' => $success]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }

    /**
     * Paypal Ends
     * @return [type] [description]
     */

    /**
     * Bank Starts
     * @return [type] [description]
     */
    public function getDepositBankList(Request $request)
    {
        $bank = Bank::with('file:id,filename')->where(['currency_id' => $request->currency_id])->get(['id', 'bank_name', 'is_default', 'account_name', 'account_number', 'file_id']);
        if ($bank)
        {
            $success['status'] = 200;
            $success['base_url'] = asset('public/uploads/files/bank_logos/');
            $success['banks']   = $bank;
            if (!empty($bank->file_id))
            {
                $success['bank_logo'] = $bank->file->filename;
            }
        }
        else
        {
            $success['status'] = 401;
            $success['banks']   = "Bank Not Found!";
        }
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function bankList($banks, $currencyPaymentMethods)
    {
        // print_r($currencyPaymentMethods);
        // die;
        $selectedBanks = [];
        $i             = 0;
        foreach ($banks as $bank)
        {
            foreach ($currencyPaymentMethods as $cpm)
            {
                if ($bank->id == json_decode($cpm->method_data)->bank_id)
                {
                    $selectedBanks[$i]['id']             = $bank->id;
                    $selectedBanks[$i]['bank_name']      = $bank->bank_name;
                    $selectedBanks[$i]['is_default']     = $bank->is_default;
                    $selectedBanks[$i]['account_name']   = $bank->account_name;
                    $selectedBanks[$i]['account_number'] = $bank->account_number;
                    $i++;
                }
            }
        }
        return $selectedBanks;
    }

    public function getBankDetails()
    {
        $bank = Bank::with('file:id,filename')->where(['id' => request('bank')])->first(['account_name', 'account_number', 'bank_name', 'file_id']);
        if ($bank)
        {
            $success['status'] = 200;
            $success['bank']   = $bank;
            if (!empty($bank->file_id))
            {
                $success['bank_logo'] = $bank->file->filename;
            }
        }
        else
        {
            $success['status'] = 401;
            $success['bank']   = "Bank Not Found!";
        }
        return response()->json(['success' => $success], $this->successStatus);
    }
      public function createImageFromBase64($image,$param,$imagedir) {
        if(isset($image) && $image && isset($imagedir) && $imagedir) {
            $upload_dir = $imagedir;
            $img =$image;
          
            $type= ".jpg";
            //$img = str_replace(' ', '+', $img);
            $datas = base64_decode($img);
            $fileName = strtolower(time() . $type);
            $file = $upload_dir .'/'. $fileName;
            $success = file_put_contents($file, $datas);
             
            $file               = new File();
            $file->user_id      = request('user_id');
            $file->filename     = $fileName;
            $file->originalname = $fileName;
            $file->type         = $type;
            $file->save();
            //dd($file->id);
            return $file->id;
        } else {
            return "";
        }
    }
    //Deposit Confirm Post via Bank
    public function bankPaymentStore()
    {
        // dd(request()->all());
            
        $uid                  = request('user_id');
        $uuid                 = unique_code();
        $deposit_payment_id   = request('deposit_payment_id');
        $deposit_payment_name = request('deposit_payment_name');
        $currency_id          = request('currency_id');
        $amount               = request('amount');
        $bank_id              = request('bank_id');
        $reference_number     = request('reference_number');
        $totalAmount          = request('amount') + request('totalFees');
        $feeInfo              = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currency_id, 'payment_method_id' => $deposit_payment_id])->first(['charge_percentage', 'charge_fixed']);
        $feePercentage        = $amount * ($feeInfo->charge_percentage / 100);

        try {
            \DB::beginTransaction();

            if ($deposit_payment_name == 'Bank')
            {
                // File Entries
                if (request('photo'))
                {
                    $param = "photo";
                    $dirt       = 'uploads/files/bank_attached_files';
                    $path       = public_path($dirt);
                    $fileId = $this->createImageFromBase64(request('photo'),$param, $path);
                    // $fileName     = request()->file('file');
                    // $originalName = $fileName->getClientOriginalName();
                    // $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                    // $file_extn    = strtolower($fileName->getClientOriginalExtension());
                    // $path         = 'uploads/files/bank_attached_files';
                    // $uploadPath   = public_path($path);
                    // $fileName->move($uploadPath, $uniqueName);

                    // //File
                    // $file               = new File();
                    // $file->user_id      = $uid;
                    // $file->filename     = $uniqueName;
                    // $file->originalname = $originalName;
                    // $file->type         = $file_extn;
                    // $file->save();
                }
            }
            //Save to Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = $uid;
            $deposit->currency_id       = $currency_id;
            $deposit->payment_method_id = $deposit_payment_id;
            $deposit->uuid              = $uuid;
            $deposit->reference_number  = $reference_number;
            $deposit->charge_percentage = $feePercentage;
            $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $deposit->amount            = $amount;
            $deposit->status            = 'Pending'; //in bank deposit, status will be pending
            if ($deposit_payment_name == 'Bank')
            {
                $deposit->bank_id = $bank_id;
                $deposit->file_id = $fileId;
            }
            $deposit->save();

            //Save to Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $uid;
            $transaction->currency_id              = $currency_id;
            $transaction->sender_name              = request('sendername');
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = $feeInfo->charge_percentage;
            $transaction->charge_percentage        = $feePercentage;
            $transaction->charge_fixed             = $feeInfo->charge_fixed;
            $transaction->total                    = ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
            $transaction->status                   = 'Success';
            $transaction->payment_method_id        = $deposit_payment_id;
            if ($deposit_payment_name == 'Bank')
            {
                $transaction->bank_id = $bank_id;
                $transaction->file_id = $fileId;
            }
            $transaction->save();

            $wallet = Wallet::where(['user_id' => $uid, 'currency_id' => $currency_id])->first(['id','balance']);
            if (empty($wallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $uid;
                $wallet->currency_id = $currency_id;
                $wallet->balance     = 0.00; // as initially, transaction status will be pending
                $wallet->is_default  = 'No';
                $wallet->save();
             }
            else
            {
                 //Update to Wallet
                     $wallet_balance = $wallet->balance + $transaction->subtotal;
                    // dd($wallet_balance);
                     $wallet        = Wallet::where(['user_id' => $uid, 'currency_id' => $currency_id])->update(['balance'=> $wallet_balance]);
                    
            }
            \DB::commit();

            // Send notification to admin
            
            $subject   = "Fund Added Successfully!";
            $currency = request('currency_id');
            $type = "sendmoney";
            $subheader = "Congratulations! Your fund added successfully";
            $date    = date("m-d-Y h:i");
            $message = "You’ve successfully added ".$this->helper->getcurrencyCode($currencyId)." ".$amount."  to own wallet";
            $this->helper->sendFirabasePush($subject,$subheader,$user_id, $currency, $type);
            $success['status'] = $this->successStatus;
            $success['transaction_id'] = $transaction->uuid;
            $success['added_amount']   = request('amount');
            return response()->json(['success' => $success], $this->successStatus);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
     }
    
    /**
     * Bank Ends
     * @return [type] [description]
     */

    //Deposit Money Ends here
    
    public function braintree(Request $request)
    {
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => request('currency_id'), 'method_id' => request('method_id')])
            ->where('activated_for', 'like', "%deposit%")
            ->first(['method_data']);
            
        $cedentials = json_decode($currencyPaymentMethod->method_data);
        
        //dd($cedentials);
        
        $urlPrefix = '/braintree/';
        
        // Include the Braintree.php library
        define('THIS_PATH_FILE', realpath(dirname(__FILE__)));
        define('PATH_TO_BRAINTREE', '/home/yangonpos/public_html/pagospay/vendor/braintree_braintree');
        define('PATH_TO_BRAINTREE_LIBRARY', PATH_TO_BRAINTREE . '/lib/Braintree.php');
        include(PATH_TO_BRAINTREE_LIBRARY);

        if (empty($currencyPaymentMethod))
        {
            $success['message'] = __('Payment gateway credentials not found!');
            $success['status']  = 401;
        }
        else
        {
            $gateway = new Braintree\Gateway([
                'environment' => 'sandbox',
                'merchantId' => $cedentials->merchant_id,
                'publicKey' => $cedentials->public_key,
                'privateKey' => $cedentials->private_key
                // 'merchantId' => 'cbwsnkcbwwcp9wjk',
                // 'publicKey' => 'jwdpdxfwq7j7ybdk',
                // 'privateKey' => '7bcbf02e64555ac061108c8fc86f4867'
            ]);
            
            $clientToken = $gateway->clientToken()->generate();
            
            $success['status']      = 200;
            $success['client_id'] = $clientToken;
            return response()->json(['success' => $success]);
            
        }
    }
    
    public function braintreePaymentStore()
    {
        if (request('details')['status'] != 'COMPLETED')
        {
            $success['status']  = 401;
            $success['message'] = __('Unsuccessful Transaction');
            return response()->json(['success' => $success]);
        }

        $amount            = request('amount');
        $currency_id       = request('currencyID');
        $payment_method_id = request('methodID');
        $user_id           = request('userId');
        $uuid              = unique_code();
        $feeInfo           = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currency_id, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
        $wallet            = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['id', 'balance']);

        try {
            \DB::beginTransaction();

            if (empty($wallet))
            {
                $walletInstance              = new Wallet();
                $walletInstance->user_id     = $user_id;
                $walletInstance->currency_id = $currency_id;
                $walletInstance->balance     = 0;
                $walletInstance->is_default  = 'No';
                $walletInstance->save();
            }
            $feePercentage = $amount * ($feeInfo->charge_percentage / 100);

            //Save to Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = $user_id;
            $deposit->currency_id       = $currency_id;
            $deposit->payment_method_id = $payment_method_id;
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = $feePercentage;
            $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $deposit->amount            = $amount;
            $deposit->status            = 'Success'; //in paypal deposit, status will be success
            $deposit->save();

            //Save to Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $currency_id;
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = $feeInfo->charge_percentage;
            $transaction->charge_percentage        = $feePercentage;
            $transaction->charge_fixed             = $feeInfo->charge_fixed;
            $transaction->total                    = ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
            $transaction->status                   = 'Success';
            $transaction->payment_method_id        = $payment_method_id;
            $transaction->save();

            //Update to Wallet
            $wallet->balance = ($wallet->balance + $transaction->subtotal);
            $wallet->save();
            
            $transaction->updated_balance        = $wallet->balance;

            \DB::commit();

            // Send notification to admin
           // $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);
            $subject   = "Fund Added Successfully!";
            $subheader = "Congratulations! Your fund Added successfully";
            $date    = date("m-d-Y h:i");
            $message = "You’ve successfully Added ".$this->helper->getcurrencyCode($currencyId)." ".$amount."  to own wallet";
            
            $this->helper->sendFirabasePush($subject,$subheader,$user_id, $currency, $type);
            $datanotice1= array('title'=>$subject,'content'=>$message,'type'=>'push','content_type'=>'addmoney','user'=>$user_id,'sub_header'=>$subheader,'push_date'=>request('local_tran_time'));
        	DB::table('noticeboard')->insert($datanotice1);
            $success['transaction'] = $transaction;
            $success['status']      = 200;
            return response()->json(['success' => $success]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    
    // This is working on APP
    public function stripeDepositStore(Request $request)
    {
        $validation = Validator::make(request()->all(), [
            'stripeToken' => '',
        ]);
        
        if ($validation->fails())
        {
            $data['status']  = 401;
            $data['message'] = $validation->errors();
            return response()->json(['success' => $data]);
        }
        
        $payment_method_id = request('deposit_payment_id');

        $user_id = request('user_id');
        $wallet  = Wallet::where(['currency_id' => request('currency_id'), 'user_id' => $user_id])->first(['id', 'currency_id']);
        try{
            \DB::beginTransaction();

            if (empty($wallet))
            {
                $walletInstance              = new Wallet();
                $walletInstance->user_id     = $user_id;
                $walletInstance->currency_id = request('currency_id');
                $walletInstance->balance     = 0;
                $walletInstance->is_default  = 'No';
                $walletInstance->save();
            }
            
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);
            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $payment_method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData            = json_decode($currencyPaymentMethod->method_data);

            $totalAmount           = (float) request('totalAmount');
            $amount                = (float) request('amount');
            if (request()->all())
            {
                $token         = "ABC";
                $feeInfo       = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currencyId, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
                $feePercentage = $amount * ($feeInfo->charge_percentage / 100);
                
                $uuid                       = unique_code();

                //Pending Transaction
                $pending_transaction                           = new PendingTransaction();
                $pending_transaction->user_id                  = $user_id;
                $pending_transaction->currency_id              = $currencyId;
                $pending_transaction->payment_method_id        = $payment_method_id;
                $pending_transaction->uuid                     = $uuid;
                $pending_transaction->transaction_reference_id = $uuid;
                $pending_transaction->transaction_type_id      = Deposit;
                $pending_transaction->subtotal                 = $amount;
                $pending_transaction->percentage               = $feeInfo->charge_percentage;
                $pending_transaction->charge_percentage        = $feePercentage;
                $pending_transaction->charge_fixed             = $feeInfo->charge_fixed;
                $pending_transaction->total                    = ($pending_transaction->subtotal + $pending_transaction->charge_percentage + $pending_transaction->charge_fixed);
                $pending_transaction->ip_address               = request()->ip();
                $pending_transaction->status                   = 'Pending';
                $pending_transaction->save();
                
                $response_fraud = $this->helper->check_fraud($pending_transaction->id);
                if(!empty($response_fraud->id)){
                    
                    if(!empty($response_fraud->transactions_hour)){
                        $message = 'You have exceed allowed number of transactions per hour.';
                        $fraud_type = 'transactions_hour';
                    }elseif(!empty($response_fraud->transactions_day)){
                        $message = 'You have exceed allowed number of transactions per day.';
                        $fraud_type = 'transactions_day';
                    }elseif(!empty($response_fraud->amount_hour)){
                        $message = 'You have exceed allowed amount limit per Hour.';
                        $fraud_type = 'amount_hour';
                    }elseif(!empty($response_fraud->amount_day)){
                        $message = 'You have exceed allowed amount limit per Day.';
                        $fraud_type = 'amount_day';
                    }elseif(!empty($response_fraud->amount_week)){
                        $message = 'You have exceed allowed amount limit per Week.';
                        $fraud_type = 'amount_week';
                    }elseif(!empty($response_fraud->amount_month)){
                        $message = 'You have exceed allowed amount limit per Month.';
                        $fraud_type = 'amount_month';
                    }elseif(!empty($response_fraud->same_amount)){
                        $message = 'You transaction is rejected due to repeating same amount multiple times.';
                        $fraud_type = 'same_amount';
                    }elseif(!empty($response_fraud->email_day)){
                        $message = 'You transaction is rejected due to repeat transactions on same account.';
                        $fraud_type = 'email_day';
                    }elseif(!empty($response_fraud->ipadd_day)){
                        $message = 'You transaction is rejected due to repeat transactions on same IP.';
                        $fraud_type = 'ipadd_day';
                    }elseif(!empty($response_fraud->user_created_at)){
                        $message = 'You transaction is rejected as per new account limitations. Please try after some days.';
                        $fraud_type = 'user_created_at';
                    }
                    
                    $delete_trans = PendingTransaction::where('id', $pending_transaction->id)->delete();
                    
                    return response()->json(['status'=>'420','message' => $message,'fraud_type' => $fraud_type], $this->unauthorisedStatus);
                }

                //Save to Deposit
                $deposit                    = new Deposit();
                $deposit->user_id           = $user_id;
                $deposit->currency_id       = $currencyId;
                $deposit->payment_method_id = $payment_method_id;
                $deposit->uuid              = $uuid;
                $deposit->charge_percentage = $feePercentage;
                $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                $deposit->amount            = $amount;
                $deposit->status            = 'Success';
                $deposit->local_tran_time   = $request->local_tran_time;
                $deposit->ip_address        = request()->ip();
                $deposit->save();

                //Save to Transaction
                $transaction                           = new Transaction();
                $transaction->user_id                  = $user_id;
                $transaction->currency_id              = $currencyId;
                $transaction->payment_method_id        = $payment_method_id;
                $transaction->uuid                     = $uuid;
                $transaction->transaction_reference_id = $deposit->id;
                $transaction->transaction_type_id      = Deposit;
                $transaction->subtotal                 = $deposit->amount;
                $transaction->percentage               = $feeInfo->charge_percentage;
                $transaction->charge_percentage        = $feePercentage;
                $transaction->charge_fixed             = $feeInfo->charge_fixed;
                $transaction->total                    = ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
                $transaction->status                   = 'Success';
                $transaction->local_tran_time          = $request->local_tran_time;
                $transaction->ip_address               = request()->ip();
                $transaction->save();
                
                if($transaction->id){
                    $rs = TransDeviceInfo::create([
                        'user_id' => $user_id, 
                        'trans_id' => $transaction->id, 
                        'device_id' => $request->device_id, 
                        'app_ver' => $request->app_ver, 
                        'device_name' => $request->device_name, 
                        'device_manufacture' => $request->device_manufacture, 
                        'device_model' => $request->device_model, 
                        'os_ver' => $request->os_ver, 
                        'device_os' => $request->device_os, 
                        'ip_address' => request()->ip(),
                    ]);
                }

                //Update to Wallet
                $wallet          = Wallet::where(['user_id' => $user_id, 'currency_id' => $currencyId])->first(['id', 'balance']);
                $wallet->balance = ($wallet->balance + $transaction->subtotal);
                $wallet->save();

                \DB::commit();

                $adminAllowed = Notification::has_permission([1]);
                    
                foreach($adminAllowed as $admin){
                    $name = User::where('id', $user_id)->first();
                    Notification::insert([
                        'user_id'               => $user_id,
                        'notification_to'       => $admin->agent_id,
                        'notification_type_id'  => 1,
                        'notification_type'     => 'App',
                        'description'           => 'User '.$name->first_name.' has deposited '.$this->helper->getcurrencyCode(request('currency_id')).' '.$amount.' via Debit / Credit Card',
                        'url_to_go'             => 'admin/transactions/edit/'.$transaction->id,
                        'local_tran_time'       => $request->local_tran_time
                    ]);
                }

                // Send notification to admin
                $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);
                
                $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
                if(!empty($userdevice)){
                    $device_lang = $userdevice->language;
                }else{
                    $device_lang = getDefaultLanguage();
                }

                $template = NotificationTemplate::where('temp_id', '1')->where('language_id', $device_lang)->first();
                $subject = $template->title;
                $subheader = $template->subheader;
                $message = $template->content;
                
                $msg = str_replace('{currency}', $this->helper->getcurrencyCode($currencyId), $message);
                $msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $msg);
                
                $date    = date("m-d-Y h:i");
                $this->helper->sendFirabasePush($subject,$msg,$user_id, $currency, $type);
                
                Noticeboard::create([
                    'tr_id' => $transaction->id,
                    'title' => $subject,
                    'content' => $msg,
                    'type' => 'push',
                    'content_type' => 'addmoney',
                    'user' => $user_id,
                    'sub_header' => $subheader,
                    'push_date' => $request->local_tran_time,
                    'template' => '1',
                    'language' => $device_lang,
                    'currency' => $this->helper->getcurrencyCode($currencyId),
                    'amount' => number_format($amount, 2, '.', ',')
                ]);
            	
            	$user = User::where('id', $user_id)->first();
            	$currency_sym = Currency::where('id', $currencyId)->first();
            	
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 33,
                    'language_id' => $device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{amount}', $deposit->amount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{created_at}', $transaction->local_tran_time, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{uuid}', $deposit->uuid, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{code}', $currency_sym->code, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{fee}', $transaction->charge_percentage + $transaction->charge_fixed, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
            	
                $data['transaction'] = $transaction;
                $data['status']      = 200;
                return response()->json(['status'=>200,'success' => $data]);
            }
            else
            {
                $data['status']  = 401;
                $data['message'] = $validation->errors();
                return response()->json(['status'=>401,'success' => $data]);
            }
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['status'=>$this->unauthorisedStatus,'success' => $success], $this->unauthorisedStatus);
        }
    }
    
    //Get Stripe Info
    public function getIntentInfo(Request $request)
    {
        $user_id = $request->user_id;
        $amount = $request->amount;
        $currency_id = $request->currency_id;
        
        $payment_method_id = 2;
        $currency   = Currency::where('id', $currency_id)->first();
        
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currency->id, 'method_id' => $payment_method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData            = json_decode($currencyPaymentMethod->method_data);
        
        $user_detail = User::where('id', $user_id)->first();
        
        \Stripe\Stripe::setApiKey($methodData->secret_key);
        $stripe = new \Stripe\StripeClient($methodData->secret_key);
        
        $intents = StripeIntent::where('cus_id', $user_detail->stripe_cus_id)->orderBy('id', 'desc')->limit('3')->get();
        if(count($intents) > 0){
            foreach($intents as $intent){
                $retrieve = $stripe->paymentIntents->retrieve(
                    $intent->intent_id
                );
                
                $pay_status[] = $retrieve->status;
            }
            
            $design_id = 'succeeded';
            $list_desings_ids = $pay_status;
            
            if(!in_array($design_id, $list_desings_ids))
            {
                User::where('id', $user_id)->update(['status' => 'Inactive']);
            }
        }
        
        try {
        
            if(!empty($user_detail->stripe_cus_id)){
                $customer_str = $user_detail->stripe_cus_id;
            }else{
                $customer = $stripe->customers->create([
                    'email' => $user_detail->email,
                    'name' => $user_detail->first_name.' '.$user_detail->last_name,
                    'phone' => $user_detail->phone,
                ]);
                
                $rs = User::where('id', $user_id)->update(['stripe_cus_id'=>$customer->id]);
                
                $customer_str = $customer->id;
            }
            
            $ephemeralKey = \Stripe\EphemeralKey::create(
              ["customer" => $customer_str],
              ["stripe_version" => '2020-08-27']
            );
            
            //dd($ephemeralKey->secret);
            
            $intent = $stripe->paymentIntents->create([
                'amount' => $amount*100,
                'currency' => $currency->code,
                'customer' => $customer_str,
                'description' => 'Deposited - LP',
                'payment_method_types'=>["card"]
            ]);
            
            if (empty($intent->client_secret))
            {
                $success['message'] = __('Payment gateway credentials not found!');
                $success['status']  = 401;
            }
            else
            {
                $stripe             = new StripeIntent();
                $stripe->user_id    = $request->user_id;
                $stripe->cus_id     = $customer_str;
                $stripe->intent_id  = $intent->id;
                $stripe->currency_id  = $request->currency_id;
                $stripe->save();
                
                $success['paymentIntent'] = $intent->client_secret;
                $success['customer'] = $customer_str;
                $success['publishableKey'] = $methodData->publishable_key;
                $success['ephemeralKey'] = $ephemeralKey->secret;
                $success['status']      = 200;
                return response()->json(['success' => $success]);
            }
        } catch(\Stripe\Exception\CardException $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        } catch (\Stripe\Exception\RateLimitException $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        } catch (Exception $e) {
            $error['message'] = $e->getError();
            $error['status']  = $this->unauthorisedStatus;
            return response()->json(['error' => $error]);
        }
    }
    
    public function getRetrieveInfo(Request $request)
    {
        $intent = $request->intent_id;
        
        $check_intent = StripeIntent::where('intent_id', $intent)->first();
        
        if(!empty($check_intent)){
            $currency_id = $check_intent->currency_id;
            
            $payment_method_id = 2;
            $currency   = Currency::where('id', $currency_id)->first();
            
            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currency->id, 'method_id' => $payment_method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData            = json_decode($currencyPaymentMethod->method_data);
            
            $stripe = new \Stripe\StripeClient($methodData->secret_key);
        
            $retrieve = $stripe->paymentIntents->retrieve(
              $check_intent->intent_id
            );
            
            //dd($retrieve);
            
            if (empty($retrieve->id))
            {
                $success['message'] = __('Payment gateway credentials not found!');
                $success['status']  = 401;
                return response()->json(['success' => $success]);
            }
            else
            {   
                
                //Customere address update
                $address=$retrieve->charges->data[0]->billing_details->address??'';
                $stripe->customers->update(
                   $check_intent->cus_id,
                  ['address'=>[
                         'line1'=> $address->line1??'',
                          'line2'=>$address->line2??'',
                          'city'=> $address->city??'',
                          'state'=> $address->state??'',
                          'postal_code'=>$address->postal_code??'',
                          'country'=>$address->country??'',
                      ]
                      ]
                );
                
                $success['data'] = $retrieve;
                $success['status']      = 200;
                return response()->json(['success' => $success]);
            }
        }else{
            $success['message'] = __('Intent ID not found!');
            $success['status']  = 401;
            return response()->json(['success' => $success]);
        }
    }
}
