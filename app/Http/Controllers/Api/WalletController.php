<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\EmailTemplate;
use App\Models\ActivityLog;
use App\Models\Preference;
use App\Models\Setting;
use App\Models\User;
use App\Models\VerifyUser;
use App\Models\Wallet;
use App\Models\TempToken;
use App\Models\Currency;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\AppPage;
use App\Models\FeesLimit;
use App\Models\WalletPayment;
use App\Models\CardPayment;
use App\Models\CardToken;
use App\Models\Country;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use App\Models\Notification;
use App\Models\TransDeviceInfo;

class WalletController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unverifiedUser     = 201;
    public $inactiveUser       = 501;
    protected $helper;
    public $email;
    public $jwt;
    public $tokens;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
        $this->jwt    = new TokenRepository();
        
        $setting = Setting::first();
        $this->admin_email = $setting->notification_email;
    }

    public function store_temp_token(Request $request)
    {
        $check_token = TempToken::where('email', $request->email)->first();
        if(!empty($check_token)){
            TempToken::where('email', $request->email)->update(['status' => '2']);
        }
        
        $tk = TempToken::create([
            'email' => $request->email, 
            'token' => $request->token, 
            'expire_time' => $request->expire_time, 
            'status' => $request->status
        ]);
        
        $check_user = User::where('email', $request->email)->first();
        if(!empty($check_user)){
            $user_status = '1';
        }else{
            $user_status = '2';
        }
        
        return response()->json([
            'user_status' => $user_status,
            'status' => $this->successStatus,
            'message'=> 'Temp Token stored successfully.'
        ], $this->successStatus);
    }
   
    public function check_wallet(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        
        $check_token = TempToken::where('email', $email)->where('token', $token)->where('status', '1')->first();
        if(!empty($check_token)){
            $user = User::where('email', $email)->first();
            
            $wallets = Wallet::where('user_id', $user->id)->get();
            
            foreach($wallets as $wallet){
                $currency = Currency::where('id', $wallet->currency_id)->first();
                $wallet['symbol'] = $currency->symbol;
                $wallet['code'] = $currency->code;
            }
            
            return response()->json([
                'status' => $this->successStatus,
                'message'=> 'Wallet fetched successfully.',
                'data'   => $wallets
            ], $this->successStatus);
        }else{
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=> 'Invalid Token.'
            ], $this->unauthorisedStatus);
        }
    }
    
    public function currencies_exchange_rate(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $from_currency_code = $request->from_currency_code;
        $amount = $request->amount;
        
        $check_token = TempToken::where('email', $email)->where('token', $token)->where('status', '1')->first();
        if(!empty($check_token)){
            $check_currency = Currency::where('code', $from_currency_code)->first();
            $from_currency = $check_currency->id;
            $to_currency = '9';
            $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $to_currency], ['exchange_from', 'code', 'rate', 'symbol']);
            
            if (!empty($toWalletCurrency))
            {
                if ($toWalletCurrency->exchange_from == "local")
                {
                    $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['rate', 'symbol']);
                    $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                    $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
                }
                else
                {
                    $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['rate', 'symbol']);
                    $exchangevalue = getCurrencyRate($from_currency_code, $toWalletCurrency->code);
                    $toWalletRate = $exchangevalue;
                }
                
                $getAmountMoneyFormat = $toWalletRate * $amount;
                
                $formattedDestinationCurrencyRate = number_format($toWalletRate, 8, '.', '');
                
                $success['status']                = $this->successStatus;
                $success['toWalletRate']          = (float) $formattedDestinationCurrencyRate;
                $success['toWalletRateHtml']      = (float) $formattedDestinationCurrencyRate;
                $success['toWalletCode']          = $toWalletCurrency->code;
                $success['toWalletSymbol']        = $toWalletCurrency->symbol;
                $success['fromWalletSymbol']      = $fromWalletCurrency->symbol;
                $success['total_amount']          = $getAmountMoneyFormat;
                $success['getAmountMoneyFormat']  = number_format($getAmountMoneyFormat, 2, '.', '');;
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
        }else{
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=> 'Invalid Token.'
            ], $this->unauthorisedStatus);
        }
    }
    
    public function update_wallet(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $from_currency_code = $request->to_currency_code;
        $amount = $request->from_amount;
        
        $check_token = TempToken::where('email', $email)->where('token', $token)->where('status', '1')->first();
        if(!empty($check_token)){
            
            $check_currency = Currency::where('code', $from_currency_code)->first();
            $to_currency = $check_currency->id;
            $from_currency = '9';
            $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $to_currency], ['exchange_from', 'code', 'rate', 'symbol']);
            
            if (!empty($toWalletCurrency))
            {
                if ($toWalletCurrency->exchange_from == "local")
                {
                    $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['rate', 'symbol']);
                    $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                    $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
                }
                else
                {
                    $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['rate', 'symbol']);
                    $exchangevalue = getCurrencyRate($from_currency_code, $toWalletCurrency->code);
                    $toWalletRate = $exchangevalue;
                }
            }
                
            $getAmountMoneyFormat = $toWalletRate * $amount;
            
            $formattedDestinationCurrencyRate = number_format($toWalletRate, 8, '.', '');
            
            $success['status']                = $this->successStatus;
            $success['toWalletRate']          = (float) $formattedDestinationCurrencyRate;
            $success['toWalletRateHtml']      = (float) $formattedDestinationCurrencyRate;
            $success['toWalletCode']          = $toWalletCurrency->code;
            $success['toWalletSymbol']        = $toWalletCurrency->symbol;
            $success['fromWalletSymbol']      = $fromWalletCurrency->symbol;
            $success['total_amount']          = $getAmountMoneyFormat;
            $success['getAmountMoneyFormat']  = formatNumber($getAmountMoneyFormat);
            
            $user = User::where('email', $email)->first();
            $check_balance = Wallet::where('user_id', $user->id)->where('currency_id', $check_currency->id)->first();
            $wallets = Wallet::where('user_id', $user->id)->where('currency_id', $check_currency->id)->update(['balance' => $check_balance->balance - $success['getAmountMoneyFormat']]);
            
            //Save to Deposit
            $uuid                       = unique_code();
            $deposit                    = new WalletPayment();
            $deposit->user_id           = $user->id;
            $deposit->currency_id       = $check_currency->id;
            $deposit->payment_method_id = '1';
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = 0;
            $deposit->charge_fixed      = 0;
            $deposit->amount            = $success['getAmountMoneyFormat'];
            $deposit->status            = 'Success';
            $deposit->save();

            //Save to Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user->id;
            $transaction->currency_id              = $check_currency->id;
            $transaction->payment_method_id        = '1';
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = '37';
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = 0;
            $transaction->charge_percentage        = 0;
            $transaction->charge_fixed             = 0;
            $transaction->total                    = $deposit->amount;
            $transaction->status                   = 'Success';
            $transaction->save();
            
            return response()->json([
                'status' => $this->successStatus,
                'message'=> 'Wallet balance updated successfully.',
                'data'   => $success
            ], $this->successStatus);
        }else{
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=> 'Invalid Token.'
            ], $this->unauthorisedStatus);
        }
    }
    
    public function store_card_token(Request $request)
    {
        $rules = array(
            'email' => 'required',
            'token' => 'required',
            'expire_time' => 'required',
            'status' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
        $check_token = CardToken::where('email', $request->email)->first();
        if(!empty($check_token)){
            CardToken::where('email', $request->email)->update(['status' => '2']);
        }
        
        $tk = CardToken::create([
            'email' => $request->email, 
            'token' => $request->token, 
            'expire_time' => $request->expire_time, 
            'status' => $request->status
        ]);
        
        $check_user = User::where('email', $request->email)->first();
        if(!empty($check_user)){
            $user_status = '1';
        }else{
            $user_status = '2';
        }
        
        $data['user_status'] = $user_status;
        $data['token'] = $tk;
        
        return response()->json([
            'status' => $this->successStatus,
            'message'=> 'Temp Token stored successfully.',
            'data' => $data
        ], $this->successStatus);
    }
    
    public function check_card_wallet(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        
        $check_token = CardToken::where('email', $email)->where('token', $token)->where('status', '1')->first();
        if(!empty($check_token)){
            $user = User::where('email', $email)->first();
            
            $wallet = Wallet::where('user_id', $user->id)->where('currency_id', '9')->first();
            
            $currency = Currency::where('id', $wallet->currency_id)->first();
            $wallet['symbol'] = $currency->symbol;
            $wallet['code'] = $currency->code;

            
            return response()->json([
                'status' => $this->successStatus,
                'message'=> 'Wallet fetched successfully.',
                'data'   => $wallet
            ], $this->successStatus);
        }else{
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=> 'Invalid Token.'
            ], $this->unauthorisedStatus);
        }
    }
    
    public function update_card_wallet(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $wallet_id = $request->wallet_id;
        $amount = $request->amount;
        $decription = $request->trans_type;
        $last_four = $request->last_four;
        $local_time = $request->local_time;
        $cardamount = $request->cardamount;
        $cardfee = $request->cardfee;
        
        $check_token = CardToken::where('email', $email)->where('token', $token)->where('status', '1')->first();
        if(!empty($check_token)){
            
            $user = User::where('email', $email)->first();
            $check_wallet = Wallet::where('user_id', $user->id)->where('id', $wallet_id)->first();
            $check_currency = Currency::where('id', $check_wallet->currency_id)->first();
            
            $wallets = Wallet::where('user_id', $user->id)->where('currency_id', $check_wallet->currency_id)->update(['balance' => $check_wallet->balance - $amount]);
            
            if($decription == '1'){
                $trans_type = '38';
                $temp_id = '26';
                $email_temp_id = '54';
                $admin_email_temp_id = '57';
            }elseif($decription == '3'){
                $trans_type = '41';
                $temp_id = '31';
                $email_temp_id = '65';
                $admin_email_temp_id = '66';
            }else{
                $trans_type = '39';
                $temp_id = '27';
                $email_temp_id = '55';
                $admin_email_temp_id = '58';
            }
            
            //Save to Deposit
            $uuid                       = unique_code();
            $deposit                    = new WalletPayment();
            $deposit->user_id           = $user->id;
            $deposit->currency_id       = $check_currency->id;
            $deposit->payment_method_id = '1';
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = 0;
            $deposit->charge_fixed      = 0;
            $deposit->amount            = $amount;
            $deposit->status            = 'Success';
            $deposit->trans_type        = $trans_type;
            $deposit->local_tran_time   = $local_time;
            $deposit->last_four         = $last_four;
            $deposit->save();

            //Save to Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user->id;
            $transaction->currency_id              = $check_currency->id;
            $transaction->payment_method_id        = '1';
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = $trans_type;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = 0;
            $transaction->charge_percentage        = 0;
            $transaction->charge_fixed             = 0;
            $transaction->total                    = $deposit->amount;
            $transaction->status                   = 'Success';
            $transaction->last_four                = $last_four;
            $transaction->local_tran_time          = $local_time;
            $transaction->save();
            
            if($transaction->id){
                $rs = TransDeviceInfo::create([
                    'user_id' => $user->id, 
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
            
            $updated_wallet = Wallet::where('user_id', $user->id)->where('id', $wallet_id)->first();
            
            $adminAllowed = Notification::has_permission([1]);
                    
            foreach($adminAllowed as $admin){
                $name = User::where('id', $user->id)->first();
                
                if($temp_id == '26'){
                    $n_description = 'User '.$name->first_name.' has applied for a new Virtual Card.';
                }elseif($temp_id == '27'){
                    $n_description = 'User '.$name->first_name.' has reloaded '.$this->helper->getcurrencyCode($check_currency->id).' '.$deposit->amount.' in Card XX '.$last_four;
                }elseif($temp_id == '31'){
                    $n_description = 'User '.$name->first_name.' has renewed the card XX '.$last_four;
                }
                
                Notification::insert([
                    'user_id'               => $user->id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'App',
                    'description'           => $n_description,
                    'url_to_go'             => 'admin/transactions/edit/'.$transaction->id,
                    'local_tran_time'       => $request->local_tran_time
                ]);
            }
            
            $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $template = NotificationTemplate::where('temp_id', $temp_id)->where('language_id', $device_lang)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            if($temp_id == '26'){
                $msg = $message;
                
                $type = 'card apply';
                $n_currency = null;
                $n_amount = null;
                $n_last_four = null;
            }elseif($temp_id == '27'){
                $msg = str_replace('{currency}', $this->helper->getcurrencyCode($check_currency->id), $message);
                $msg = str_replace('{amount}', number_format($deposit->amount, 2, '.', ','), $msg);
                $msg = str_replace('{last_four}', $last_four, $msg);
                
                $type = 'card topup';
                $n_currency = $this->helper->getcurrencyCode($check_currency->id);
                $n_amount = number_format($deposit->amount, 2, '.', ',');
                $n_last_four = $last_four;
            }elseif($temp_id == '31'){
                $msg = str_replace('{last_four}', $last_four, $message);
                
                $type = 'card topup';
                $n_currency = null;
                $n_amount = null;
                $n_last_four = $last_four;
            }
            
            $this->helper->sendFirabasePush($subject, $msg, $user->id, $check_currency->id, $type);
            
            Noticeboard::create([
                'tr_id' => $transaction->id,
                'title' => $subject,
                'content' => $msg,
                'type' => 'push',
                'content_type' => $type,
                'user' => $user->id,
                'sub_header' => $subheader,
                'push_date' => $local_time,
                'template' => $temp_id,
                'language' => $device_lang,
                'currency' => $n_currency,
                'amount' => $n_amount,
                'last_four' => $n_last_four
            ]);
            
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => $email_temp_id,
                'language_id' => $device_lang,
                'type'        => 'email',
            ])->select('subject', 'body')->first();
            
            if($email_temp_id == '54'){
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            }elseif($email_temp_id == '55'){
                $amountadded = $cardamount - $cardfee;
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{card_number}', $last_four, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amount}', 'EUR '.$cardamount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{method}', 'Ewallet', $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{fee}', 'EUR '.$cardfee, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amount_added}', 'EUR '.$amountadded, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{wallet_amount}', $check_currency->code.' '.$amount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            }elseif($email_temp_id == '65'){
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{card_number}', $last_four, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amount}', 'USD '.number_format($cardamount, 2, '.', ','), $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{method}', 'Ewallet', $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{wallet_amount}', $check_currency->code.' '.$amount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            }
            
            $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
            
            $admin->email = $this->admin_email;
        	
        	if(!empty($admin->email)){
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => $admin_email_temp_id,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
                
                if($admin_email_temp_id == '57'){
                    $twoStepVerification_sub = $twoStepVerification->subject;
                    $twoStepVerification_msg = str_replace('{name}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                    $twoStepVerification_msg = str_replace('{email}', $user->email, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{phone}', $user->formattedPhone, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                }elseif($admin_email_temp_id == '58'){
                    $amountadded = $cardamount - $cardfee;
                    $twoStepVerification_sub = $twoStepVerification->subject;
                    $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                    $twoStepVerification_msg = str_replace('{card_number}', $last_four, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{amount}', 'EUR '.$cardamount, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{method}', 'Ewallet', $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{fee}', 'EUR '.$cardfee, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{amount_added}', 'EUR '.$amountadded, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                }elseif($admin_email_temp_id == '66'){
                    $twoStepVerification_sub = $twoStepVerification->subject;
                    $twoStepVerification_sub = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification_sub);
                    $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                    $twoStepVerification_msg = str_replace('{card_number}', $last_four, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{amount}', 'USD '.number_format($cardamount, 2, '.', ','), $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{method}', 'Ewallet', $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{wallet_amount}', $check_currency->code.' '.$amount, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                }
                
                $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
        	}
        
            return response()->json([
                'status' => $this->successStatus,
                'message'=> 'Wallet balance updated successfully.',
                'data'   => $updated_wallet
            ], $this->successStatus);
        }else{
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=> 'Invalid Token.'
            ], $this->unauthorisedStatus);
        }
    }
    
    public function get_wallet_details(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $wallet_id = $request->wallet_id;
        
        $check_token = CardToken::where('email', $email)->where('token', $token)->where('status', '1')->first();
        if(!empty($check_token)){
            $user = User::where('email', $email)->first();
            
            $wallet = Wallet::where('user_id', $user->id)->where('id', $wallet_id)->first();
            if(!empty($wallet)){
                $currency = Currency::where('id', $wallet->currency_id)->first();
                $country = Country::where('name', $user->defaultCountry)->first();
                
                $wallet['symbol'] = $currency->symbol;
                $wallet['code'] = $currency->code;
                $wallet['country_id'] = $country->id;
                $wallet['country'] = $user->defaultCountry;
                
                return response()->json([
                    'status' => $this->successStatus,
                    'message'=> 'Wallet fetched successfully.',
                    'data'   => $wallet
                ], $this->successStatus);
            }else{
                return response()->json([
                    'status' => $this->unauthorisedStatus,
                    'message'=> 'Invalid details.'
                ], $this->unauthorisedStatus);
            }
        }else{
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=> 'Invalid Token.'
            ], $this->unauthorisedStatus);
        }
    }
    
    public function card_to_card_notify(Request $request)
    {
        $user_id = $request->user_id;
        $amount = $request->amount;
        $from_card = $request->from_card;
        $to_card = $request->to_card;
        $destination_user = $request->destination_user;
        $destination_email = $request->destination_email;
        $fee = $request->fee;
        $local_time = $request->local_tran_time;
        $currency = '14';
       
        $uuid                       = unique_code();
        $deposit                    = new CardPayment();
        $deposit->user_id           = $user_id;
        $deposit->currency_id       = $currency;
        $deposit->payment_method_id = '1';
        $deposit->uuid              = $uuid;
        $deposit->charge_percentage = 0;
        $deposit->charge_fixed      = 0;
        $deposit->amount            = $amount;
        $deposit->status            = 'Success';
        $deposit->trans_type        = '40';
        $deposit->from_card         = $from_card;
        $deposit->to_card           = $to_card;
        $deposit->local_tran_time   = $local_time;
        $deposit->save();
        
        if($deposit->id){
            $rs = TransDeviceInfo::create([
                'user_id' => $user_id, 
                'trans_id' => $deposit->id, 
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
        
        $adminAllowed = Notification::has_permission([1]);
                    
        foreach($adminAllowed as $admin){
            $name = User::where('id', $user_id)->first();
            
            $n_description = 'User '.$name->first_name.' has transfered '.$this->helper->getcurrencyCode($currency).' '.$deposit->amount.' from Card XX '.$from_card.' to Card XX'.$to_card;
           
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'App',
                'description'           => $n_description,
                'url_to_go'             => 'admin/transactions/edit/'.$deposit->id,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
        
        // Sender Notification
        $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
        $template = NotificationTemplate::where('temp_id', '28')->where('language_id', $userdevice->language)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
            
        $msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency), $message);
        $msg = str_replace('{amount}', number_format($deposit->amount, 2, '.', ','), $msg);
        $msg = str_replace('{last_four}', $to_card, $msg);
        
        $type = 'card transfer';
            
        $this->helper->sendFirabasePush($subject, $msg, $user_id, $currency, $type);
            
        Noticeboard::create([
            'tr_id' => $deposit->id,
            'title' => $subject,
            'content' => $msg,
            'type' => 'push',
            'content_type' => $type,
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $local_time,
            'template' => '28',
            'language' => $userdevice->language,
            'currency' => $this->helper->getcurrencyCode($currency),
            'amount' => number_format($deposit->amount, 2, '.', ','),
            'last_four' => $to_card
        ]);
        
        // Receiver Notification
        $destination_users = User::where('email', $destination_email)->first();
        $destinationdevice = DB::table('devices')->where('user_id', $destination_users->id)->first();
        if(!empty($destinationdevice)){
            $destinationtemplate = NotificationTemplate::where('temp_id', '32')->where('language_id', $destinationdevice->language)->first();
            $destinationsubject = $destinationtemplate->title;
            $destinationsubheader = $destinationtemplate->subheader;
            $destinationmessage = $destinationtemplate->content;
            
            $destinationsub = str_replace('{last_four}', $to_card, $destinationsubject);
                
            $destinationmsg = str_replace('{currency}', $this->helper->getcurrencyCode($currency), $destinationmessage);
            $destinationmsg = str_replace('{amount}', number_format($deposit->amount, 2, '.', ','), $destinationmsg);
            $destinationmsg = str_replace('{last_four}', $to_card, $destinationmsg);
            $destinationmsg = str_replace('{sender}', $destination_user, $destinationmsg);
            
            $this->helper->sendFirabasePush($destinationsub, $destinationmsg, $destination_users->id, $currency, $type);
                
            Noticeboard::create([
                'tr_id' => $deposit->id,
                'title' => $destinationsub,
                'content' => $destinationmsg,
                'type' => 'push',
                'content_type' => $type,
                'user' => $destination_users->id,
                'sub_header' => $destinationsubheader,
                'push_date' => $local_time,
                'template' => '32',
                'language' => $destinationdevice->language,
                'currency' => $this->helper->getcurrencyCode($currency),
                'amount' => number_format($deposit->amount, 2, '.', ','),
                'last_four' => $to_card,
                'sender' => $destination_user
            ]);
        }
        
        $user = User::where('id', $user_id)->first();
        $amountadded = $amount - $fee;
        
        if(!empty($destinationdevice)){
            $dest_device_lang = $destinationdevice->language;
        }else{
            $dest_device_lang = getDefaultLanguage();
        }
        
        $twoStepVerification = EmailTemplate::where([
            'temp_id'     => '56',
            'language_id' => $dest_device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{from_card}', $from_card, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{to_card}', $to_card, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{amount}', 'EUR '.$amount, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{fee}', 'EUR '.$fee, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{amount_added}', 'EUR '.$amountadded, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        $twoStepVerification = EmailTemplate::where([
            'temp_id'     => '60',
            'language_id' => $dest_device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{sender}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{receiver}', $destination_user, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{from_card}', $from_card, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{to_card}', $to_card, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{amount}', 'EUR '.$amount, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{fee}', 'EUR '.$fee, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{amount_added}', 'EUR '.$amountadded, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($destination_email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        $admin->email = $this->admin_email;
        	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 59,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{sender}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{receiver}', $destination_user, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{from_card}', $from_card, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{to_card}', $to_card, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount}', 'EUR '.$amount, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{fee}', 'EUR '.$fee, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount_added}', 'EUR '.$amountadded, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status' => $this->successStatus,
            'message'=> 'Notification sent successfully.',
            'data'   => $deposit
        ], $this->successStatus);
    }
    
    public function exchange_amount(Request $request)
    {
        $from_currency = $request->from_currency;  // from which the amount is deducting
        $amount = $request->amount; // from the amount is deducting
        $wallet = $request->wallet; // to which the amount is adding
        $fee = $request->fee;
        
        $current_balance = Wallet::where('id', $wallet)->first();
        $currency_id = $current_balance->currency_id;
        $currency = Currency::where('id', $from_currency)->first();
        $new_curr = Currency::where('id', $currency_id)->first();
       
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['exchange_from', 'code', 'rate', 'symbol']);
        
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['rate', 'symbol']);
                $exchangevalue = getCurrencyRate($currency->code, $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat = $toWalletRate * $amount;
            $getFeeMoneyFormat = $toWalletRate * $fee;
            $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
            $new_fee = number_format((float)$getFeeMoneyFormat, 2, '.', '');
        }
        
        if(!empty($current_balance->balance) && $current_balance->balance >= $new_amount){
            $result['amount'] = $new_amount;
            $result['fee'] = $new_fee;
            $result['total'] = number_format((float)$new_amount, 2, '.', '');
            $result['total_fee'] = number_format((float)$new_fee, 2, '.', '');
            $result['currency'] = $new_curr->code;
            $result['currency_id'] = $currency_id;
            
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Wallet details fetched successfully.',
                'data'    => $result,
            ]);
        }else{
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Insufficient Fund!',
                'data'    => null
            ]);
        }
    }
    
    public function card_exchange_rate(Request $request)
    {
        $from_currency = $request->from_currency;  // from which the amount is deducting
        $amount = $request->amount; // from the amount is deducting
        $wallet = $request->wallet; // to which the amount is adding
        
        $current_balance = Wallet::where('id', $wallet)->first();
        $currency_id = $current_balance->currency_id;
        $currency = Currency::where('id', $from_currency)->first();
        $new_curr = Currency::where('id', $currency_id)->first();
       
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['card_exchange_from', 'code', 'card_rate', 'symbol']);
        
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->card_exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['card_rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['card_rate']);
                $toWalletRate       = ($defaultCurrency->card_rate / $fromWalletCurrency->card_rate) * $toWalletCurrency->card_rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['card_rate', 'symbol']);
                $exchangevalue = getCurrencyRate($currency->code, $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat = $toWalletRate * (request('amount'));
            $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
        }
        
        if(!empty($current_balance->balance) && $current_balance->balance >= $new_amount){
            $result['amount'] = $new_amount;
            $result['total'] = number_format((float)$new_amount, 2, '.', '');
            $result['currency'] = $new_curr->code;
            $result['currency_id'] = $currency_id;
            
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Wallet details fetched successfully.',
                'data'    => $result,
            ]);
        }else{
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Insufficient Fund!',
                'data'    => null
            ]);
        }
    }
}
