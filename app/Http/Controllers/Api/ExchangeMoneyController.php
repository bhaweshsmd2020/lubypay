<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\CurrencyExchange;
use App\Models\FeesLimit;
use App\Models\Wallet;
use Illuminate\Http\Request;
use DB;
use App\Models\Notification;
use App\Models\User;
use App\Models\PendingTransaction;
use App\Models\EmailTemplate;
use App\Http\Controllers\Users\EmailController;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class ExchangeMoneyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 401;
    protected $helper;
    protected $exchange;
    protected $email;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->exchange = new CurrencyExchange();
        $this->email  = new EmailController();
    }

    //Exchange Money Starts here
    public function getUserWalletsWithActiveAndHasTransactionCurrency()
    {
        // dd(request()->all());
        $feesLimitCurrency                               = FeesLimit::where(['transaction_type_id' => Exchange_From, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        $userCurrencyList                                = array_column(Wallet::where(['user_id' => request('user_id')])->get()->toArray(), 'currency_id');
        $userCurrencyList                                = Currency::whereIn('id', $userCurrencyList)->where(['status' => 'Active'])->get(['id', 'code', 'status']);
        $success['activeHasTransactionUserCurrencyList'] = $activeHasTransactionUserCurrencyList = $this->activeHasTransactionUserCurrencyList($userCurrencyList, $feesLimitCurrency);

        //Set default wallet as selected - starts
        $defaultWallet                      = Wallet::where(['user_id' => request('user_id'), 'is_default' => 'Yes'])->first(['currency_id']);
        $success['defaultWalletCurrencyId'] = $defaultWallet->currency_id;
        //Set default wallet as selected - ends

        $success['status'] = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    //Users Active, Has Transaction and Existing Currency Wallets/list
    public function activeHasTransactionUserCurrencyList($userCurrencyList, $feesLimitCurrency)
    {
        $selectedCurrency = [];
        foreach ($userCurrencyList as $aCurrency)
        {
            foreach ($feesLimitCurrency as $flCurrency)
            {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes')
                {
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                }
            }
        }
        return $selectedCurrency;
    }

    public function getWalletsExceptSelectedFromWallet()
    {
        // dd(request()->all());

        $feesLimitCurrency = FeesLimit::where(['transaction_type_id' => Exchange_From, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);

        $activeCurrency = Currency::where('id', '!=', request('currency_id'))->where(['status' => 'Active'])->orderBy('position','ASC')->get(['id', 'name','code', 'status', 'symbol', 'logo','position']);
        // dd($activeCurrency);

        $currencyList = $this->currencyList($activeCurrency, $feesLimitCurrency, request('user_id'));
         //dd($currencyList);

        if ($currencyList)
        {
            return response()->json([
                'currencies' => $currencyList,
                'status'     => true,
            ]);
        }
        else
        {
            return response()->json([
                'currencies' => null,
                'status'     => false,
            ]);
        }
    }

    public function currencyList($activeCurrency, $feesLimitCurrency, $user_id)
    {
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency)
        {
            foreach ($feesLimitCurrency as $flCurrency)
            {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes')
                {
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['name']   = $aCurrency->name;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                    $selectedCurrency[$aCurrency->id]['symbol'] = $aCurrency->symbol;
                    $selectedCurrency[$aCurrency->id]['logo'] = $aCurrency->logo;
                     $selectedCurrency[$aCurrency->id]['position'] = $aCurrency->position;

                    $wallet = Wallet::where(['currency_id' => $aCurrency->id, 'user_id' => $user_id])->first(['balance']);
                    // dd($wallet);
                    if (!empty($wallet))
                    {
                        $selectedCurrency[$aCurrency->id]['balance'] = isset($wallet->balance) ?number_format($wallet->balance,2) : 0.00;
                    }
                    
                    $selectedCurrency[$aCurrency->id]['base_url'] = env('CURRENCY_LOGO');
                }
            }
        }
        return $selectedCurrency;
    }

    public function getBalanceOfFromAndToWallet()
    {
        // dd(request()->all());

        $wallet = Wallet::where(['currency_id' => request('currency_id'), 'user_id' => request('user_id')])->first(['balance', 'currency_id']); //added by parvez - for wallet balance check
        if (!empty($wallet))
        {
            return response()->json([
                'status'       => true,
                'balance'      => number_format((float) $wallet->balance, 2, '.', ''),
                'currencyCode' => $wallet->currency->code,
            ]);
        }
        else
        {
            return response()->json([
                'status'       => false,
                'balance'      => null,
                'currencyCode' => null,
            ]);
        }
    }

    public function exchangeReview()
    {
        // dd(request()->all());
        $amount     = request('amount');
        $fromWallet = request('currency_id');
        $user_id    = request('user_id');

        $wallet      = Wallet::where(['currency_id' => $fromWallet, 'user_id' => $user_id])->first(['currency_id', 'balance']);
        $feesDetails = FeesLimit::where(['transaction_type_id' => Exchange_From, 'currency_id' => $fromWallet])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);

        //Wallet Balance Limit Check Starts here
        if (@$feesDetails)
        {
            if ($feesDetails->has_transaction == 'No')
            {
                $success['reason']       = 'noHasTransaction';
                $success['currencyCode'] = $feesDetails->currency->code;
                $success['message']      = 'The currency' . ' ' . $feesDetails->currency->code . ' ' . 'fees limit is inactive';
                $success['status']       = '401';
                return response()->json(['success' => $success], $this->successStatus);
            }
            $checkAmount = $amount;
        }

        if (@$wallet)
        {
            if ((@$checkAmount) > (@$wallet->balance) || (@$wallet->balance < 0))
            {
                $success['reason']  = 'insufficientBalance';
                $success['message'] = "Sorry, not enough funds to perform the operation!";
                $success['status']  = '401';
                return response()->json(['success' => $success], $this->successStatus);
            }
        }

        //Code for Amount Limit starts here
        if (@$feesDetails->max_limit == null)
        {
            if ((@$amount < @$feesDetails->min_limit))
            {
                $success['reason']          = 'minLimit';
                $success['minLimit']        = @$feesDetails->min_limit;
                $success['message']         = 'Minimum amount ' . formatNumber($feesDetails->min_limit);
                $success['wallet_currency'] = $wallet->currency->code;
                $success['status']          = '401';
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
                $success['reason']          = 'minMaxLimit';
                $success['minLimit']        = @$feesDetails->min_limit;
                $success['maxLimit']        = @$feesDetails->max_limit;
                $success['message']         = 'Minimum amount ' . formatNumber($feesDetails->min_limit) . ' and Maximum amount ' . formatNumber($feesDetails->max_limit);
                $success['wallet_currency'] = $wallet->currency->code;
                $success['status']          = '401';
            }
            else
            {
                $success['status'] = 200;
            }
        }

        return response()->json([
            'success' => $success,
        ]);
    }

    public function getCurrenciesExchangeRate(Request $request)
    {
        // dd(request()->all());
        $fromWallet = request('fromWallet');
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => request('toWallet')], ['exchange_from', 'code', 'rate', 'symbol']);
                $feesDetails = FeesLimit::where(['transaction_type_id' => Exchange_From, 'currency_id' => $fromWallet])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);

        // dd($toWalletCurrency); die;
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => request('fromWallet')], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => request('fromWallet')], ['rate', 'symbol']);
                $exchangevalue = getCurrencyRate(request('fromWalletCode'), $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat             = $toWalletRate * (request('amount') - ($feesDetails->charge_fixed + (request('amount')*$feesDetails->charge_percentage/100)));
            
            $formattedDestinationCurrencyRate = number_format($toWalletRate, 8, '.', '');
            
            $success['status']                = $this->successStatus;
            $success['toWalletRate']          = (float) $formattedDestinationCurrencyRate; // this was not necessary, but kept it as it creates confusion
            $success['toWalletRateHtml']      = (float) $formattedDestinationCurrencyRate; // this will not be shown as formatted as it creates confusion - when multiplying amount * currency rate
            $success['toWalletCode']          = $toWalletCurrency->code;
            $success['toWalletSymbol']        = $toWalletCurrency->symbol;
            $success['fromWalletSymbol']      = $fromWalletCurrency->symbol;
            $success['fee_percentage']        = $feesDetails->charge_percentage;
            $success['charge_fixed']        = $feesDetails->charge_fixed;
            $success['total_fee']        =number_format($feesDetails->charge_fixed + (request('amount')*$feesDetails->charge_percentage/100),2);
            $success['total_amount']        = (request('amount')+$feesDetails->charge_fixed + (request('amount')*$feesDetails->charge_percentage/100) - ($feesDetails->charge_fixed + (request('amount')*$feesDetails->charge_percentage/100)));
            $success['getAmountMoneyFormat']  = formatNumber($getAmountMoneyFormat); //just for show, not taken for further processing
             //dd($success);
            return response()->json(['success' => $success], $this->successStatus);
        }
        else
        {
            $success['status']         = $this->unauthorisedStatus;
            $success['toWalletRate']   = null;
            $success['toWalletCode']   = null;
            $success['toWalletSymbol'] = null;
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }

    public function reviewExchangeDetails()
    {
        // dd(request()->all());

        $user_id                     = request('user_id');
        $amount                      = request('amount');
        $fromWalletValue             = request('fromWalletValue');
        $toWalletRate                = request('toWalletRate');
        $feesDetails                 = $this->helper->getFeesLimitObject([], Exchange_From, $fromWalletValue, null, null, ['charge_percentage', 'charge_fixed']);
        // dd($feesDetails);
        $feesChargePercentage        = $amount * (@$feesDetails->charge_percentage / 100);
        $totalFess                   = $feesChargePercentage + (@$feesDetails->charge_fixed);
        $getAmountMoneyFormat        = $toWalletRate * request('amount');
        $success['convertedAmnt']    = $getAmountMoneyFormat;
        $success['totalAmount']      = $amount + $totalFess;
        $success['totalFees']        = $totalFess;
        $success['totalFeesHtml']    = formatNumber($totalFess);
        $success['toWalletRateHtml'] = $toWalletRate;
        $fromCurrency                = $this->helper->getCurrencyObject(['id' => $fromWalletValue], ['code', 'symbol']);
        $success['fCurrencySymbol']  = $fromCurrency->symbol;
        $success['fCurrencyCode']    = $fromCurrency->code;
        $success['status']           = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function exchangeMoneyComplete(Request $request)
    {
        $user_id              = request('user_id');
        $fromWalletValue      = request('fromWalletValue');
        $toWalletValue        = request('toWalletValue');
        $toWalletAmount       = request('toWalletAmount');
        $toWalletExchangeRate = request('toWalletExchangeRate');
        $fromWalletAmount     = request('fromWalletAmount');
        $totalFees            = request('totalFees');
        $uuid                 = unique_code();
        $fromWallet           = $this->helper->getUserWallet([], ['user_id' => $user_id, 'currency_id' => $fromWalletValue], ['id', 'currency_id', 'balance']);
        $toWallet             = $this->helper->getUserWallet([], ['user_id' => $user_id, 'currency_id' => $toWalletValue], ['id', 'balance']);
        $feesDetails          = $this->helper->getFeesLimitObject([], Exchange_From, $fromWalletValue, null, null, ['charge_percentage', 'charge_fixed']);

        $arr = [
            'unauthorisedStatus'        => $this->unauthorisedStatus,
            'user_id'                   => $user_id,
            'toWalletCurrencyId'        => $toWalletValue, //
            'fromWallet'                => $fromWallet,
            'toWallet'                  => $toWallet,
            'finalAmount'               => $toWalletAmount,
            'uuid'                      => $uuid,
            'destinationCurrencyExRate' => $toWalletExchangeRate,
            'amount'                    => $fromWalletAmount,
            'amount_new'                => $fromWalletAmount,
            'fee'                       => $totalFees,
            'charge_percentage'         => $feesDetails->charge_percentage,
            'charge_fixed'              => $feesDetails->charge_fixed,
            'formattedChargePercentage' => $fromWalletAmount * (@$feesDetails->charge_percentage / 100),
            'local_tran_time'    => $request->local_tran_time,
            'device_id' => $request->device_id, 
            'app_ver' => $request->app_ver, 
            'device_name' => $request->device_name, 
            'device_manufacture' => $request->device_manufacture, 
            'device_model' => $request->device_model, 
            'os_ver' => $request->os_ver, 
            'device_os' => $request->device_os, 
            'ip_address' => request()->ip(),
        ];
        
        $fromCurrency = Currency::where('id', $fromWalletValue)->first();
        $toCurrency = Currency::where('id', $toWalletValue)->first();
        
        // Check Fraud
        $pending_transaction                           = new PendingTransaction();
        $pending_transaction->user_id                  = $user_id;
        $pending_transaction->currency_id              = $fromWalletValue;
        $pending_transaction->payment_method_id        = null;
        $pending_transaction->transaction_reference_id = $arr['uuid'];
        $pending_transaction->transaction_type_id      = Exchange_From;
        $pending_transaction->uuid                     = $arr['uuid'];
        $pending_transaction->subtotal                 = $arr['amount'];
        $pending_transaction->percentage               = $feesDetails->charge_percentage;
        $pending_transaction->charge_percentage        = $feesDetails->charge_percentage;
        $pending_transaction->charge_fixed             = $feesDetails->charge_fixed;
        $pending_transaction->total                    = ($arr['amount'] + $arr['formattedChargePercentage'] + $arr['charge_fixed']);
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

        //Get response
        $response = $this->exchange->processExchangeMoneyConfirmation($arr, 'mobile');
         
        if ($response['status'] != 200)
        {
            if (empty($response['exchangeCurrencyId']))
            {
                return response()->json([
                    'status'                              => false,
                    'exchangeMoneyValidationErrorMessage' => $response['ex']['message'],
                ]);
            }
            return response()->json([
                'status' => true,
            ]);
        }
        
    	// For Sender
        $sender_device = DB::table('devices')->where('user_id', $user_id)->first();
        if(!empty($sender_device)){
            $device_lang = $sender_device->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $sender_template = NotificationTemplate::where('temp_id', '5')->where('language_id', $device_lang)->first();
        $sender_subject = $sender_template->title;
        $sender_subheader = $sender_template->subheader;
        $sender_message = $sender_template->content;
        
        $send_msg = str_replace('{from_currency}', $fromCurrency->code, $sender_message);
        $send_msg = str_replace('{from_amount}', number_format($fromWalletAmount, 2, '.', ','), $send_msg);
        $send_msg = str_replace('{to_currency}', $toCurrency->code, $send_msg);
        $send_msg = str_replace('{to_amount}', number_format($toWalletAmount, 2, '.', ','), $send_msg);
        
        $date      = date("Y-d-m h:i");
        $currency  = request('fromWalletValue');
        $type      = "exchangemoney";
        $this->helper->sendFirabasePush($sender_subject, $send_msg, $user_id, $currency, $type);
        
        Noticeboard::create([
            'tr_id' => $response['sender_transaction'],
            'title' => $sender_subject,
            'content' => $send_msg,
            'type' => 'push',
            'content_type' => 'exchangemoney',
            'user' => $user_id,
            'sub_header' => $sender_subheader,
            'push_date' => $request->local_tran_time,
            'template' => '5',
            'language' => $device_lang,
            'from_currency' => $fromCurrency->code,
            'from_amount' => number_format($fromWalletAmount, 2, '.', ','),
            'to_currency' => $toCurrency->code,
            'to_amount' => number_format($toWalletAmount, 2, '.', ',')
        ]);
        	
        $adminAllowed = Notification::has_permission([1]);
        foreach($adminAllowed as $admins){
            $name = User::where('id', $user_id)->first();
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => 1,
                'notification_type_id'  => 5,
                'notification_type'     => 'App',
                'description'           => "User ".$name->first_name." has exchanged ".$fromCurrency->code.' '.$fromWalletAmount.' to '.$toCurrency->code.' '.$toWalletAmount,
                'url_to_go'             => 'admin/exchange/edit/'.$response['exchangeCurrencyId'],
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
        
        $user = User::where('id', $user_id)->first();
        	
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 45,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{from_amount}', $fromWalletAmount, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{to_amount}', $toWalletAmount, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{created_at}', request('local_tran_time'), $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{uuid}', $pending_transaction->uuid, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{from_wallet}', $fromCurrency->code, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{to_wallet}', $toCurrency->code, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{fee}', $pending_transaction->charge_percentage + $pending_transaction->charge_fixed, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
       
        return response()->json([
            'status' => true,
        ]);
    }
    //Exchange Money Ends here
}
