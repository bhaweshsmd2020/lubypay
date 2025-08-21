<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Http\Helpers\Common;
use App\Http\Controllers\Users\EmailController;
use App\Models\User;
use App\Models\CurrencyPaymentMethod;
use App\Models\PendingTransaction;
use App\Models\FeesLimit;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\TransDeviceInfo;
use App\Models\EmailTemplate;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use App\Models\EmailConfig;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Wallet;
use Stripe\Webhook;

class PlaidController extends Controller
{
    public function __construct()
    {
        $emaiConfig = EmailConfig::first();
        $this->helper = new Common();
        $this->email  = new EmailController();
        $this->admin_email = $emaiConfig->notification_email;
    }
    
    public function postFunction($url, $headers, $payloads)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        
        $result = curl_exec($ch);
        if ($result === false) {
            dd([
                'error' => curl_error($ch),
                'info' => curl_getinfo($ch)
            ]);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }
    
    public function PaymentCredentials($currency_id, $method_id)
    {
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currency_id, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData = json_decode($currencyPaymentMethod->method_data);
        
        $data['secretKey'] = $methodData->plaid_stripe_secret_key;
        $data['webhookSecret'] = $methodData->plaid_stripe_webhook_secret;
        $data['baseUrl'] = $methodData->plaid_base_url;
        $data['clientId'] = $methodData->plaid_client_id;
        $data['clientSecret'] = $methodData->plaid_client_secret;
        
        return $data;
    }
	
	public function createLinkToken(Request $request)
    {
        $user_id = $request->user_id;
        $currency_id = $request->currency_id;
        $payment_method_id = $request->payment_method_id;
        
        $currencypaymentmethod = $this->PaymentCredentials($currency_id, $payment_method_id);
        if(empty($currencypaymentmethod)){
            return response()->json([
                'status' => 'error',
                'message' => 'Payment details not exists',
                'data' => null,
            ]);
        }
        
        $user_detail = User::find($user_id);
        if(empty($user_detail)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $url = $currencypaymentmethod['baseUrl'].'link/token/create';
        
        $headers = [
            'Content-Type: application/json',
        ];
        
        $payloads = [
            "client_id" => $currencypaymentmethod['clientId'],
            "secret" => $currencypaymentmethod['clientSecret'],
            "user" => [
                "client_user_id" => $user_detail->carib_id
            ],
            "client_name" => env('APP_NAME'),
            "products" => ["auth"],
            "country_codes" => ["US"],
            "language" => "en"
        ];
        
        $response = $this->postFunction($url, $headers, $payloads);

        return response()->json([
            'status'  => 'success',
            'message' => 'Link token created successfully.',
            'data'    => $response
        ]);
    }
    
    public function plaidDepositStore(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'amount' => 'required',
            'account_id' => 'required',
            'public_token' => 'required',
        );

        $fieldNames = array(
            'user_id' => 'User Id',
            'amount' => 'Amount',
            'account_id' => 'Account Id',
            'public_token' => 'Public Token',
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        
        if ($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'data' => null
            ]);
        }
        
        $user_id = $request->user_id;
        $amount = $request->amount;
        $totalAmount = $request->totalAmount;
        $account_id = $request->account_id;
        $public_token = $request->public_token;
        $device_id = $request->device_id;
        $app_ver = $request->app_ver; 
        $device_name = $request->device_name;
        $device_manufacture = $request->device_manufacture; 
        $device_model = $request->device_model; 
        $os_ver = $request->os_ver;
        $device_os = $request->device_os;
        $ip_address = request()->ip();
        $currency_id = $request->currency_id;
        $payment_method_id = $request->payment_method_id;
        
        $currencypaymentmethod = $this->PaymentCredentials($currency_id, $payment_method_id);
        if(empty($currencypaymentmethod)){
            return response()->json([
                'status' => 'error',
                'message' => 'Payment details not exists',
                'data' => null,
            ]);
        }
        
        $user_detail = User::find($user_id);
        if(empty($user_detail)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $currency = Currency::where('id', $currency_id)->first();
        
        $wallet  = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['id', 'currency_id']);
        if (empty($wallet))
        {
            $walletInstance              = new Wallet();
            $walletInstance->user_id     = $user_id;
            $walletInstance->currency_id = $currency_id;
            $walletInstance->balance     = 0;
            $walletInstance->is_default  = 'No';
            $walletInstance->save();
        }
        
        // Attach the Plaid-linked bank account to stripe
        $stripe = new \Stripe\StripeClient($currencypaymentmethod['secretKey']);
        
        $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currency_id, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
        $feePercentage = $amount * ($feeInfo->charge_percentage / 100);
        $uuid = unique_code();

        $pending_transaction                           = new PendingTransaction();
        $pending_transaction->user_id                  = $user_id;
        $pending_transaction->currency_id              = $currency_id;
        $pending_transaction->payment_method_id        = $payment_method_id;
        $pending_transaction->uuid                     = $uuid;
        $pending_transaction->transaction_reference_id = $uuid;
        $pending_transaction->transaction_type_id      = Deposit;
        $pending_transaction->subtotal                 = $amount;
        $pending_transaction->percentage               = $feeInfo->charge_percentage;
        $pending_transaction->charge_percentage        = $feePercentage;
        $pending_transaction->charge_fixed             = $feeInfo->charge_fixed;
        $pending_transaction->total                    = ($pending_transaction->subtotal + $pending_transaction->charge_percentage + $pending_transaction->charge_fixed);
        $pending_transaction->ip_address               = $ip_address;
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
            
            return response()->json([
                'status'  => 'error',
                'message' => $message,
                'data'    => $fraud_type
            ]);
        }
        
        try {
            // Check customer
            if(!empty($user_detail->stripe_cus_id)){
                $customer_str = $user_detail->stripe_cus_id;
            }else{
                $customer = $stripe->customers->create([
                    'email' => $user_detail->email,
                    'name' => $user_detail->first_name.' '.$user_detail->last_name,
                    'phone' => $user_detail->phone,
                ]);
                
                User::where('id', $user_id)->update(['stripe_cus_id'=>$customer->id]);
                
                $customer_str = $customer->id;
            }
            
            // Check bank
            if(!empty($user_detail->stripe_bank_id)){
                $bank_str = $user_detail->stripe_bank_id;
            }else{
                
                $headers = [
                    'Content-Type: application/json',
                ];
                
                // create access token
                $access_token_url = $currencypaymentmethod['baseUrl'].'item/public_token/exchange';
                
                $access_token_payloads = [
                    "client_id" => $currencypaymentmethod['clientId'],
                    "secret" => $currencypaymentmethod['clientSecret'],
                	"public_token" => $public_token,
                ];
                
                $access_token_response = $this->postFunction($access_token_url, $headers, $access_token_payloads);
                if(!empty($access_token_response['error_code'])){
                    return response()->json([
                        'status'  => 'error',
                        'message' => $access_token_response['error_message'],
                        'data'    => null
                    ]);
                }
                
                // create bank token
                $bank_token_url = $currencypaymentmethod['baseUrl'].'processor/stripe/bank_account_token/create';
                
                $bank_token_payloads = [
                    "client_id" => $currencypaymentmethod['clientId'],
                    "secret" => $currencypaymentmethod['clientSecret'],
                	"access_token" => $access_token_response['access_token'],
                    "account_id" => $account_id
                ];
                
                $bank_token_response = $this->postFunction($bank_token_url, $headers, $bank_token_payloads);
                if(!empty($bank_token_response['error_code'])){
                    return response()->json([
                        'status'  => 'error',
                        'message' => $bank_token_response['error_message'],
                        'data'    => null
                    ]);
                }
                
                $bankAccount = $stripe->customers->createSource(
                    $customer_str,
                    ['source' => $bank_token_response['stripe_bank_account_token']]
                );
                
                User::where('id', $user_id)->update(['stripe_bank_id'=>$bankAccount->id]);
                
                $bank_str = $bankAccount->id;
            }
            
            if($currency_id == '9'){
                $currecyCode = $currency->code;
                $finalAmount = $totalAmount * 100;
            }else{
                $exchange = $this->getCurrenciesExchangeRate($currency_id, '9', $totalAmount);
                $currecyCode = 'USD';
                $finalAmount = number_format($exchange['getAmountMoneyFormat'], 2, '.', ',') * 100;
            }
            
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $finalAmount,
                'currency' => $currecyCode,
                'customer' => $customer_str,
                'payment_method' => $bank_str,
                'payment_method_types' => ['us_bank_account'],
                'confirm' => true,
                'off_session' => true,
                'description' => 'Deposited - ACH - LP',
                'mandate_data' => [
                    'customer_acceptance' => [
                        'type' => 'online',
                        'online' => [
                            'ip_address' => $ip_address,
                            'user_agent' => $request->userAgent(),
                        ],
                    ],
                ],
            ]);
            
            //Save to Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = $user_id;
            $deposit->currency_id       = $currency_id;
            $deposit->payment_method_id = $payment_method_id;
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = $feePercentage;
            $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $deposit->amount            = $amount;
            $deposit->status            = $paymentIntent->status;
            $deposit->local_tran_time   = $request->local_tran_time;
            $deposit->ip_address        = $ip_address;
            $deposit->save();

            //Save to Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $currency_id;
            $transaction->payment_method_id        = $payment_method_id;
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = $feeInfo->charge_percentage;
            $transaction->charge_percentage        = $feePercentage;
            $transaction->charge_fixed             = $feeInfo->charge_fixed;
            $transaction->total                    = ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
            $transaction->status                   = $paymentIntent->status;
            $transaction->local_tran_time          = $request->local_tran_time;
            $transaction->ip_address               = $ip_address;
            $transaction->stripe_charge_id         = $paymentIntent->id;
            $transaction->service_provider_name    = 'Deposited - ACH - LP';
            $transaction->save();
            
            if($transaction->id){
                $rs = TransDeviceInfo::create([
                    'user_id' => $user_id, 
                    'trans_id' => $transaction->id, 
                    'device_id' => $device_id, 
                    'app_ver' => $app_ver, 
                    'device_name' => $device_name, 
                    'device_manufacture' => $device_manufacture, 
                    'device_model' => $device_model, 
                    'os_ver' => $os_ver, 
                    'device_os' => $device_os, 
                    'ip_address' => $ip_address,
                ]);
            }
            
            // Send email/notification to user
            $userdevice = Device::where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $template = NotificationTemplate::where('temp_id', '42')->where('language_id', $device_lang)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $message);
            $msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $msg);
            
            $this->helper->sendFirabasePush($subject, $msg, $user_id, $currency_id, 'push');
            
            Noticeboard::create([
                'tr_id' => $transaction->id,
                'title' => $subject,
                'content' => $msg,
                'type' => 'push',
                'content_type' => 'addmoney',
                'user' => $user_id,
                'sub_header' => $subheader,
                'push_date' => $request->local_tran_time,
                'template' => '42',
                'language' => $device_lang,
                'currency' => $this->helper->getcurrencyCode($currency_id),
                'amount' => number_format($amount, 2, '.', ',')
            ]);
        	
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 75,
                'language_id' => $device_lang,
                'type'        => 'email',
            ])->select('subject', 'body')->first();
            
            $twoStepVerification_sub = str_replace('{currency}', $currency->code, $twoStepVerification->subject);
            $twoStepVerification_sub = str_replace('{amount}', $deposit->amount, $twoStepVerification_sub);
            
            $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{currency}', $currency->code, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount}', $deposit->amount, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{created_at}', $transaction->local_tran_time, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{uuid}', $deposit->uuid, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{fee}', $transaction->charge_percentage + $transaction->charge_fixed, $twoStepVerification_msg);
            $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
            
            // Send email/notification to admin
            $adminAllowed = Notification::has_permission([1]);
                                
            foreach($adminAllowed as $admin){
                Notification::insert([
                    'user_id'               => $user_id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'Web',
                    'description'           => 'A user '.$user_detail->first_name . ' ' . $user_detail->last_name.' has initiated a payment of '.$currency->code .' '.number_format($amount, 2, '.', ',').'.',
                    'url_to_go'             => null,
                    'local_tran_time'       => $request->local_tran_time
                ]);
            }
        	
        	$admin->email = $this->admin_email;
        	if(!empty($admin->email)){
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 76,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{currency}', $currency->code, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amount}', $deposit->amount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{created_at}', $transaction->local_tran_time, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{uuid}', $deposit->uuid, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{fee}', $transaction->charge_percentage + $transaction->charge_fixed, $twoStepVerification_msg);
                $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
        	}
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Payment done successfully.',
                'data'    => $transaction,
            ]);
        } catch(\Stripe\Exception\CardException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        } catch (\Stripe\Exception\RateLimitException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null
            ]);
        }
    }
    
    public function stripeWebhook(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $currency = Currency::where('code', $data['data']['object']['currency'])->first();
        $currencypaymentmethod = $this->PaymentCredentials($currency->id, '14');
        
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = $currencypaymentmethod['webhookSecret'];

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid JSON payload',
                'data' => null,
            ], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature',
                'data' => null,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $this->updateTransactionStatus($paymentIntent->id, 'success');
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object;
            $this->updateTransactionStatus($paymentIntent->id, 'failed');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook processed successfully',
            'data' => null,
        ], 200);
    }

    private function updateTransactionStatus($stripeChargeId, $status)
    {
        $transaction = Transaction::where('stripe_charge_id', $stripeChargeId)->first();

        if ($transaction && $transaction->status !== $status) {
            $transaction->status = $status;
            $transaction->save();
            
            Deposit::where('uuid', $transaction->uuid)->update(['status' => $status]);
            
            $user_id = $transaction->user_id;
            $currency_id = $transaction->currency_id;
            $amount = $transaction->subtotal;
            
            $wallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $currency_id])->first(['id', 'balance']);
            $wallet->balance = ($wallet->balance + $transaction->subtotal);
            $wallet->save();
            
            $user_detail = User::find($user_id);
            
            // Send email/notification to user
            $userdevice = Device::where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $template = NotificationTemplate::where('temp_id', '43')->where('language_id', $device_lang)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $sub = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $subject);
            $sub = str_replace('{amount}', number_format($amount, 2, '.', ','), $sub);
            $sub = str_replace('{status}', $status, $sub);
            
            $subhead = str_replace('{status}', $status, $subheader);
            
            $msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $message);
            $msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $msg);
            $msg = str_replace('{status}', $status, $msg);
            
            $this->helper->sendFirabasePush($sub, $msg, $user_id, $currency_id, 'push');
            
            Noticeboard::create([
                'tr_id' => $transaction->id,
                'title' => $sub,
                'content' => $msg,
                'type' => 'push',
                'content_type' => 'addmoney',
                'user' => $user_id,
                'sub_header' => $subhead,
                'push_date' => $transaction->local_tran_time,
                'template' => '43',
                'language' => $device_lang,
                'currency' => $this->helper->getcurrencyCode($currency_id),
                'amount' => number_format($amount, 2, '.', ','),
                'status' => $status
            ]);
        	
        	$currency = Currency::where('id', $currency_id)->first();
        	
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 77,
                'language_id' => $device_lang,
                'type'        => 'email',
            ])->select('subject', 'body')->first();
            
            $twoStepVerification_sub = str_replace('{status}', $status, $twoStepVerification->subject);
            
            $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{currency}', $currency->code, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount}', $amount, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{created_at}', $transaction->local_tran_time, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{uuid}', $transaction->uuid, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{status}', $status, $twoStepVerification_msg);
            $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
            
            // Send email/notification to admin
            $adminAllowed = Notification::has_permission([1]);
                                
            foreach($adminAllowed as $admin){
                Notification::insert([
                    'user_id'               => $user_id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'Web',
                    'description'           => 'A payment transaction initiated by '.$user_detail->first_name . ' ' . $user_detail->last_name.' has been '.$status,
                    'url_to_go'             => null,
                    'local_tran_time'       => $transaction->local_tran_time
                ]);
            }
        	
        	$admin->email = $this->admin_email;
        	if(!empty($admin->email)){
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 78,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{currency}', $currency->code, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amount}', $amount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{created_at}', $transaction->local_tran_time, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{uuid}', $transaction->uuid, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{status}', $status, $twoStepVerification_msg);
                $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
        	}
        }
    }
    
    public function getCurrenciesExchangeRate($fromCurreny, $toCurreny, $amount)
    {
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $toCurreny], ['exchange_from', 'code', 'rate', 'symbol']);
        $feesDetails = FeesLimit::where(['transaction_type_id' => Exchange_From, 'currency_id' => $fromCurreny])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);
        $checkFromCurrency = Currency::where('id', $fromCurreny)->first();

        if ($toWalletCurrency->exchange_from == "local")
        {
            $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $fromCurreny], ['rate', 'symbol']);
            $defaultCurrency = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
            $toWalletRate = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
        }
        else
        {
            $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $fromCurreny], ['rate', 'symbol']);
            $exchangevalue = getCurrencyRate($checkFromCurrency->code, $toWalletCurrency->code);
            $toWalletRate = $exchangevalue;
        }
        
        $getAmountMoneyFormat = $toWalletRate * ($amount - ($feesDetails->charge_fixed + ($amount*$feesDetails->charge_percentage/100)));
        $formattedDestinationCurrencyRate = number_format($toWalletRate, 8, '.', '');
        
        $data['toWalletRate']          = (float) $formattedDestinationCurrencyRate;
        $data['toWalletRateHtml']      = (float) $formattedDestinationCurrencyRate;
        $data['toWalletCode']          = $toWalletCurrency->code;
        $data['toWalletSymbol']        = $toWalletCurrency->symbol;
        $data['fromWalletSymbol']      = $fromWalletCurrency->symbol;
        $data['fee_percentage']        = $feesDetails->charge_percentage;
        $data['charge_fixed']          = $feesDetails->charge_fixed;
        $data['total_fee']             = number_format($feesDetails->charge_fixed + ($amount*$feesDetails->charge_percentage/100),2);
        $data['total_amount']          = ($amount+$feesDetails->charge_fixed + ($amount*$feesDetails->charge_percentage/100) - ($feesDetails->charge_fixed + ($amount*$feesDetails->charge_percentage/100)));
        $data['getAmountMoneyFormat']  = $getAmountMoneyFormat;
        return $data;
    }
}