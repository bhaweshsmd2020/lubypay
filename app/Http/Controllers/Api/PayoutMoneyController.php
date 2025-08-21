<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\FeesLimit;
use App\Models\PayoutSetting;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalDetail;
use Illuminate\Http\Request;
use DB;
use App\Models\Notification;
use App\Models\User;
use App\Models\PendingTransaction;
use App\Models\EmailTemplate;
use App\Http\Controllers\Users\EmailController;

class PayoutMoneyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    protected $helper;
    protected $withdrawal;
    protected $email;

    public function __construct()
    {
        $this->helper     = new Common();
        $this->withdrawal = new Withdrawal();
        $this->email  = new EmailController();
    }

    //Check User Payout Settings
    public function checkPayoutSettingsApi()
    {
        $payoutSettings = PayoutSetting::where(['user_id' => request('user_id')])->get(['id', 'account_name', 'account_number', 'email']);
        // $hello = json_decode($payoutSettings,true);
        // dd($hello);
        return response()->json([
            'status'         => $this->successStatus,
            'payoutSettings' => $payoutSettings,
        ]);
    }

    //Withdrawal Money Starts here
    public function getWithdrawalPaymentMethod()
    {
        // dd(request()->all());
        $paymentMethod = PayoutSetting::where(['user_id' => request('user_id')])->get(['id', 'user_id', 'type', 'email', 'account_name']);
        $pm            = [];
        for ($i = 0; $i < count($paymentMethod); $i++)
        {
            $pm[$i]['id']                      = $paymentMethod[$i]->id;
            $pm[$i]['user_id']                 = $paymentMethod[$i]->user_id;
            $pm[$i]['paymentMethod']           = $paymentMethod[$i]->paymentMethod->name;
            $pm[$i]['paymentMethodId']         = $paymentMethod[$i]->type;
            $pm[$i]['paymentMethodCredential'] = $paymentMethod[$i]->email ? $paymentMethod[$i]->email : $paymentMethod[$i]->account_name;
        }
        $success['status']        = $this->successStatus;
        $success['paymentmethod'] = $pm;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function getWithdrawalCurrencyBasedOnPaymentMethod()
    {
        // dd(request()->all());
        $payment_met_id = request('paymentMethodId');
        $wallets        = Wallet::where(['user_id' => request('user_id')])->whereHas('active_currency', function ($q) use ($payment_met_id)
        {
            $q->whereHas('fees_limit', function ($query) use ($payment_met_id)
            {
                $query->where('has_transaction', 'Yes')->where('transaction_type_id', Withdrawal)->where('payment_method_id', $payment_met_id);
            });
        })
            ->with(['active_currency:id,code,name,logo,symbol', 'active_currency.fees_limit:id,currency_id']) //Optimized
            ->get(['currency_id', 'is_default','balance']);
// dd($wallets);
        //map wallets
        $arr        = [];
        $currencies = $wallets->map(function ($wallet)
        {
           $arr['id']             = $wallet->active_currency->id;
            $arr['code']           = $wallet->active_currency->code;
            $arr['name']           = $wallet->active_currency->name;
            $arr['symbol']         = $wallet->active_currency->symbol;
            $arr['logo']           = $wallet->active_currency->logo;
            $arr['base_url']       = ENV('CURRENCY_LOGO');
            $arr['default_wallet'] = $wallet->is_default;
            $arr['balance']        = number_format($wallet->balance,2);
            return $arr;
        });
        //
        $success['currencies'] = $currencies;
        $success['status']     = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function getWithdrawDetailsWithAmountLimitCheck()
    {
        // dd(request()->all());
        $user_id         = request('user_id');
        $amount          = request('amount');
        $currency_id     = request('currency_id');
        $payoutSettingId = request('payoutSetId');
        $paymentMethodId = request('paymentMethodId');

        $payoutSetting             = PayoutSetting::with(['paymentMethod:id,name'])->where(['id' => $payoutSettingId])->first(['account_name', 'account_number', 'type', 'swift_code', 'bank_name', 'routing', 'transit_number', 'account_type','short_code' ]);
        $success['account_name']   = $payoutSetting->account_name;
        $success['account_number'] = $payoutSetting->account_number;
        $success['type']           = $payoutSetting->paymentMethod->name;
        $success['swift_code']     = $payoutSetting->swift_code;
        $success['bank_name']      = $payoutSetting->bank_name;
        $success['routing']        = $payoutSetting->routing;
        $success['transit_number'] = $payoutSetting->transit_number;
        $success['account_type']   = $payoutSetting->account_type;
        $success['short_code']     = $payoutSetting->short_code;

        $wallets     = Wallet::where(['user_id' => $user_id, 'currency_id' => $currency_id])->first(['balance']);
        $feesDetails = FeesLimit::with('currency:id,symbol,code')->where(['transaction_type_id' => Withdrawal, 'currency_id' => $currency_id, 'payment_method_id' => $paymentMethodId])
            ->first(['charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
        // dd($feesDetails);

        //Wallet Balance Limit Check Starts here
        // $checkAmount = $amount + $feesDetails->charge_fixed + $feesDetails->charge_percentage;
        $checkAmount = $amount;
        if (@$wallets)
        {
            //if((@$wallets->balance) < (@$amount)){
            if ((@$checkAmount) > (@$wallets->balance) || (@$wallets->balance < 0))
            {
                $success['reason']  = 'insufficientBalance';
                $success['message'] = "Sorry, not enough funds to perform the operation!";
                $success['status']  = '401';
                return response()->json(['success' => $success], $this->successStatus);
            }
        }
        //Wallet Balance Limit Check Ends here

        //Amount Limit Check Starts here
        if (@$feesDetails)
        {
            $totalFess                    = (@$feesDetails->charge_percentage * $amount / 100) + (@$feesDetails->charge_fixed);
            $success['amount']            = $amount;
            $success['totalFees']         = $totalFess;
            $success['totalHtml']         = formatNumber($totalFess);
            $success['currency_id']       = $feesDetails->currency_id;
            $success['payout_setting_id'] = $payoutSettingId;
            $success['currSymbol']        = $feesDetails->currency->symbol;
            $success['currCode']          = $feesDetails->currency->code;
            // $success['totalAmount']       = $amount + $totalFess;
            $success['totalAmount']       = $amount;
            $success['status'] = $this->successStatus;

            if (@$feesDetails->max_limit == null)
            {
                if ((@$amount < @$feesDetails->min_limit))
                {
                    $success['reason']   = 'minLimit';
                    $success['minLimit'] = @$feesDetails->min_limit;
                    $success['message']  = 'Minimum amount ' . formatNumber(@$feesDetails->min_limit);
                    $success['status']   = '401';
                }
                else
                {
                    $success['status'] = $this->successStatus;
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
                    $success['status']   = '401';
                }
                else
                {
                    $success['status'] = $this->successStatus;
                }
            }
            return response()->json(['success' => $success], $this->successStatus);
        }
        else
        {
            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success], $this->successStatus);
        }

        //Code for Fees Limit Starts here
        if (empty($feesDetails))
        {
            $feesPercentage               = 0;
            $feesFixed                    = 0;
            $totalFess                    = ($feesPercentage * $amount / 100) + ($feesFixed);
            $success['amount']            = $amount;
            $success['totalFees']         = $totalFess;
            $success['totalHtml']         = formatNumber($totalFess);
            $success['currency_id']       = $feesDetails->currency_id;
            $success['payout_setting_id'] = $payoutSettingId;
            $success['currSymbol']        = $feesDetails->currency->symbol;
            $success['currCode']          = $feesDetails->currency->code;
            $success['totalAmount']       = $amount + $totalFess;
            $success['status']            = $this->successStatus;
            return response()->json(['success' => $success], $this->successStatus);
        }
        //Amount Limit Check Ends here
    }

    public function withdrawMoneyConfirm(Request $request)
    {
        // dd(request()->all());

        $user_id             = request('user_id');
        $uuid                = unique_code();
        $currency_id         = request('currency_id');
        $amount              = request('amount');
        $totalAmount         = request('amount');
        $payout_setting_id   = request('payout_setting_id');
        $payoutSetting       = $this->helper->getPayoutSettingObject(['paymentMethod:id,name'], ['id' => $payout_setting_id], ['*']);
        $payment_method_info = $payoutSetting->email ? $payoutSetting->email : $payoutSetting->paymentMethod->name;
        $wallet              = $this->helper->getUserWallet(['currency:id,symbol'], ['user_id' => $user_id, 'currency_id' => $currency_id], ['id', 'currency_id', 'balance']);
        $feeInfo             = $this->helper->getFeesLimitObject([], Withdrawal, $currency_id, $payoutSetting->type, null, ['charge_percentage', 'charge_fixed']);
        $feePercentage       = $amount * ($feeInfo->charge_percentage / 100);
        $arr                 = [
            'user_id'             => $user_id,
            'wallet'              => $wallet,
            'currency_id'         => $wallet->currency_id,
            'payment_method_id'   => $payoutSetting->paymentMethod->id,
            'payoutSetting'       => $payoutSetting,
            'uuid'                => $uuid,
            'percentage'          => $feeInfo->charge_percentage,
            'charge_percentage'   => $feePercentage,
            'charge_fixed'        => $feeInfo->charge_fixed,
            'amount'              => $amount,
            'totalAmount'         => $totalAmount,
            'subtotal'            => $amount - ($feePercentage + $feeInfo->charge_fixed),
            'payment_method_info' => $payment_method_info,
            'local_tran_time'     => $request->local_tran_time,
            'device_id' => $request->device_id, 
            'app_ver' => $request->app_ver, 
            'device_name' => $request->device_name, 
            'device_manufacture' => $request->device_manufacture, 
            'device_model' => $request->device_model, 
            'os_ver' => $request->os_ver, 
            'device_os' => $request->device_os, 
            'ip_address' => request()->ip(),
            'pay_type' => $request->pay_type,
        ];
        
        // Check Fraud
        $pending_transaction                           = new PendingTransaction();
        $pending_transaction->user_id                  = $user_id;
        $pending_transaction->currency_id              = $arr['currency_id'];
        $pending_transaction->payment_method_id        = $arr['payment_method_id'];
        $pending_transaction->transaction_reference_id = $arr['uuid'];
        $pending_transaction->transaction_type_id      = Withdrawal;
        $pending_transaction->uuid                     = $arr['uuid'];;
        $pending_transaction->subtotal                 = $arr['subtotal'];
        $pending_transaction->percentage               = $arr['percentage'];
        $pending_transaction->charge_percentage        = $arr['charge_percentage'];
        $pending_transaction->charge_fixed             = $arr['charge_fixed'];
        $pending_transaction->total                    = $arr['totalAmount'];
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
        $response = $this->withdrawal->processPayoutMoneyConfirmation($arr, 'mobile');

        if ($response['status'] != 200)
        {
            if (empty($response['withdrawalTransactionId']))
            {
                return response()->json([
                    'status'                           => false,
                    'withdrawalValidationErrorMessage' => $response['ex']['message'],
                ]);
            }
            return response()->json([
                'status' => true,
            ]);
        }
        
        $adminAllowed = Notification::has_permission([1]);
        foreach($adminAllowed as $admins){
            $name = User::where('id', $user_id)->first();
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => 1,
                'notification_type_id'  => 2,
                'notification_type'     => 'App',
                'description'           => "User ".$name->first_name." has requested to withdraw ".$this->helper->getcurrencyCode($currency_id). " ".$amount,
                'url_to_go'             => '/admin/withdrawals/edit/'.$response['withdrawal_id'],
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
        
        $userdevices = DB::table('devices')->where(['user_id' => $user_id])->first();
        if(!empty($userdevices)){
            $device_lang = $userdevices->language;
        }else{
            $device_lang = getDefaultLanguage();
        }
        if(isset($userdevices) && $userdevices->fcm_token)
        {
            $msg= 'Your request for money withdraw of '.$amount. ' is successfull.';
        	$notifyData   = array (
            	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
            	'title'         => 'Payout request placed successfully',
            	'content'       => $msg,
            	'type'          => 'Message',
            	'payload'       => array (
            	)
        	);
        	$datanotice= array('title'=>'Payout request placed successfully','content'=>$msg,'type'=>'push','content_type'=>'withdrawmoney','user'=>$user_id);
        	DB::table('noticeboard')->insert($datanotice);
        	$this->helper->sendFCMPush($notifyData);
        }
        
        $user = User::where('id', $user_id)->first();
    	$currency_sym = Currency::where('id', $currencyId)->first();
    	
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 44,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user_id}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{amount}', $pending_transaction->amount, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{created_at}', request('local_tran_time'), $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{uuid}', $pending_transaction->uuid, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{code}', $currency_sym->code, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{fee}', $pending_transaction->charge_percentage + $pending_transaction->charge_fixed, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        return response()->json([
            'status' => true,
        ]);
    }
    //Withdrawal Money Ends here
}
