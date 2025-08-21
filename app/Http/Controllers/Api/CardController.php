<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use App\Models\ApplyCard;
use App\Models\Country;
use App\Models\Card;
use App\Models\TransactionCharge;
use App\Models\CardTopup;
use App\Models\CardTransaction;
use App\Models\CardFee;
use App\Models\UserDetail;
use Carbon\Carbon;
use DB;
use App\Http\Helpers\Common;
use App\Http\Controllers\Users\EmailController;
use App\Models\EmailTemplate;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use App\Models\EmailConfig;
use App\Models\Device;
use App\Models\Notification;
use App\Models\CardSubscription;

class CardController extends Controller
{
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
        
        $setting = EmailConfig::first();
        $this->admin_email = $setting->notification_email;
    }
    
    public function encryptData($encrypted_data)
    {
        $file = env('ENC_PATH');
        $jsonContents = file_get_contents($file);
        $data = json_decode($jsonContents, true);
        
        $method = $data['method'];
        $key = $data['secret_key'];
        $hash = substr(hash('sha256', $key, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        
        $rand = mt_rand(100000,999999);
        $string = bin2hex(openssl_random_pseudo_bytes(10));
        $plaintext = $rand.'-'.$encrypted_data.'-'.$string;
        $encrypt_data = base64_encode(openssl_encrypt($plaintext, $method, $hash, OPENSSL_RAW_DATA, $iv));
        
        return $encrypt_data;
    }
    
    public function decryptData($encrypted_data)
    {
        $file = env('ENC_PATH');
        $jsonContents = file_get_contents($file);
        $data = json_decode($jsonContents, true);
        
        $method = $data['method'];
        $key = $data['secret_key'];
        $hash = substr(hash('sha256', $key, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $decrypt_data = openssl_decrypt(base64_decode($encrypted_data), $method, $hash, OPENSSL_RAW_DATA, $iv);
        $explode_data = explode("-", $decrypt_data);
        foreach($explode_data as $key => $e){
            if($key > 0){ 
                $new_data[] = $e;
            }
        }        
        foreach (array_slice($new_data, 0, count($new_data) - 1) as $key => $val) {
            $dec_data['val'.$key] = $val;
        }
        
        return $dec_data['val0'];
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

    public function userDetails(Request $request)
    {
        $decryptData = $this->decryptData($request->data);
        $user = User::where('email', $decryptData)->first();
        return response()->json([
            'status'  => 'success',
            'message' => 'User deatils fetched successfully.',
            'data'    => $user
        ]);
    }
    
    public function checkUser(Request $request)
    {
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('user_id', $user_id)->first();
        if(!empty($card)){
            return response()->json([
                'status'  => 'success',
                'message' => 'User has applied for card',
                'data'    => '1'
            ]);
        }
        
        if(!empty($user->plan_id)){
            return response()->json([
                'status'  => 'success',
                'message' => 'User has taken subscription plan',
                'data'    => '2'
            ]);
        }
        
        $apply_card = ApplyCard::where('user_id', $user_id)->first();
        
        if((empty($user->card_user_id) || empty($user->card_user_status)) && (!empty($apply_card))){
            return response()->json([
                'status'  => 'success',
                'message' => 'Old User has done card registration',
                'data'    => '6'
            ]);
        }
        
        if(!empty($apply_card)){
            return response()->json([
                'status'  => 'success',
                'message' => 'User has done card registration',
                'data'    => '3'
            ]);
        }
        
        if(empty($user->card_user_id) || empty($user->card_user_status)){
            return response()->json([
                'status'  => 'success',
                'message' => 'Old User',
                'data'    => '5'
            ]);
        }
        
        if(empty($apply_card)){
            return response()->json([
                'status'  => 'success',
                'message' => 'User has not done card registration',
                'data'    => '4'
            ]);
        }
    }
    
    public function cardRegistration(Request $request)
    {
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        if(empty($user->card_user_id) || empty($user->card_user_status)){
            $cardUser = User::find($user_id);
            $cardUserDetail = UserDetail::where('user_id', $user_id)->first();
            $cardUserCountry = Country::where('id', $cardUserDetail->country)->first();
    
            $url = env('BASE_URL').'create-user';
            
            $headers = [
                'Host: cards.lubypay.com',
                'Content-Type: application/json',
            ];
            
            $payloads = [
                'firstname' => $cardUser->first_name,
                'lastname' => $cardUser->last_name,
                'dial_code' => str_replace('+', '', $cardUser->carrierCode),
                'mobile' => $cardUser->phone,
                'email' => $cardUser->email,
                'platform' => 'Sandbox Ewallet',
                'country_name' => $cardUserCountry->name,
                'state' => $cardUserDetail->state,
                'city' => $cardUserDetail->city,
                'zip' => $cardUserDetail->zip_code,
                'address' => $cardUserDetail->address_1,
            ];
            
            $userDetails = $this->postFunction($url, $headers, $payloads);
        }
        
        $apply_card = ApplyCard::where('user_id', $user_id)->first();
        if(empty($apply_card)){
            $card = ApplyCard::create([
                'user_id' => $user_id,
                'dob' => $request->dob,
                'address_line' => $request->address_line,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'employment_type' => $request->employment_type,
                'annual_income' => $request->annual_income,
            ]);
        }else{
            $card = $apply_card;
        }
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card registration successfully done',
            'data'    => $card
        ]);
    }
    
    public function cardCountries(Request $request)
    {
        $countries = Country::where('status', '1')->get();
        return response()->json([
            'status'  => 'success',
            'message' => 'Countries deatils fetched successfully.',
            'data'    => $countries
        ]);
    }
    
    public function cardStates(Request $request)
    {
        $states = State::where('status', 'active')->where('country_id', $request->country_id)->get();
        return response()->json([
            'status'  => 'success',
            'message' => 'States deatils fetched successfully.',
            'data'    => $states
        ]);
    }
    
    public function cardCities(Request $request)
    {
        $cities = City::where('status', 'active')->where('state_id', $request->state_id)->get();
        return response()->json([
            'status'  => 'success',
            'message' => 'Cities deatils fetched successfully.',
            'data'    => $cities
        ]);
    }
    
    public function subscriptionPlans(Request $request)
    {
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->first();
    
        if (empty($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
    
        $plan_id = $user->plan_id;
    
        $url = env('BASE_URL') . 'subscription-plans';
    
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
    
        $payloads = [
            'platform' => "Sandbox Ewallet"
        ];
    
        $plans = $this->postFunction($url, $headers, $payloads);
    
        if (!empty($plans['data'])) {
            foreach ($plans['data'] as &$plan) {
                $plan['is_current_plan'] = ($plan_id !== null && $plan['id'] == $plan_id);
            }
        }
    
        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription plans fetched successfully.',
            'data'    => $plans
        ]);
    }

    public function subscribe(Request $request)
    {
        $user_id = $request->user_id;
        $plan_id = $request->plan_id;
        $amount = $request->amount;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        if($plan_id != '10'){
            $wallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
            if(!empty($wallet) && $wallet->balance <= $amount){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient Balance',
                    'data' => null,
                ]);
            }
        }
        
        $url = env('BASE_URL').'subscribe';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'plan_id' => $plan_id,
            'user_email' => $user->email,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $plans = $this->postFunction($url, $headers, $payloads);
        
        User::where('id', $user_id)->update([
            'plan_data' => json_encode($plans['data']['user']['plan_data']),
            'plan_id' => $plans['data']['user']['plan_id'],
            'plan_name' => $plans['data']['plan']['title'],
            'plan_amount' => $plans['data']['plan']['price'],
            'will_expire' => $plans['data']['user']['will_expire'],
        ]);
        
        $card_subs                  = new CardSubscription();
        $card_subs->plan_id         = $plans['data']['user']['plan_id'];
        $card_subs->plan_data       = json_encode($plans['data']['user']['plan_data']);
        $card_subs->user_id         = $user_id;
        $card_subs->wallet_id       = $wallet->id ?? '';
        $card_subs->currency_id     = '9';
        $card_subs->transaction_id  = null;
        $card_subs->sub_total       = $amount;
        $card_subs->fees            = 0;
        $card_subs->total           = $amount;
        $card_subs->status          = 'Success';
        $card_subs->trx              = unique_code();
        $card_subs->remarks         = 'Subscribe';
        $card_subs->save();
        
        if($plan_id != '10'){
            Wallet::where('user_id', $user_id)->where('currency_id', '9')->update([
                'balance' => $wallet->balance - $amount
            ]);
            
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = '9';
            $transaction->payment_method_id        = '1';
            $transaction->uuid                     = unique_code();
            $transaction->transaction_reference_id = $card_subs->id;
            $transaction->transaction_type_id      = '41';
            $transaction->subtotal                 = $amount;
            $transaction->percentage               = 0;
            $transaction->charge_percentage        = 0;
            $transaction->charge_fixed             = 0;
            $transaction->total                    = $amount;
            $transaction->status                   = 'Success';
            $transaction->save();
            
            CardSubscription::where('id', $card_subs->id)->update(['transaction_id' => $transaction->id]);
        }
        
        // Notification to User
        $userdevice = Device::where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '36')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '36',
            'language' => $device_lang
        ]);
        
        $userPlan = User::where('id', $user_id)->first();
        $plan = json_decode($userPlan['plan_data'], true);
        $plan_card_limit = $plan['virtual_card_limit']['value'];
        $plan_deposit_fee = $plan['card_reload_fee']['value'];
        $plan_transaction_fee = $plan['transaction_fee']['value'];
        $plan_online_purchase = $plan['online_purchase']['value'];
        $plan_service_fee = $plan['virtual_card_service_fee']['value'];
        $plan_dialy_limit = $plan['daily_virtual_card_spending_limit']['value'];
        $plan_monthly_limit = $plan['monthly_virtual_card_spending_limit']['value'];
        
    	// Email to User
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 65,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A new user '.$user->first_name . ' ' . $user->last_name.' has subscribed successfully.',
                'url_to_go'             => null,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 69,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription purchased successfully.',
            'data'    => $plans
        ]);
    }
    
    public function cardTypes(Request $request)
    {
        $url = env('BASE_URL').'card-types';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [];
        
        $cards = $this->postFunction($url, $headers, $payloads);
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card types fetched successfully.',
            'data'    => $cards
        ]);
    }
    
    public function cardCreate(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        $amount = $request->amount;
        $fee = $request->fee;
        $name_on_card = $request->name_on_card;
        $total = $amount + $fee;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        // $wallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
        // if(!empty($wallet) && $wallet->balance <= $total){
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Insufficient Balance',
        //         'data' => null,
        //     ]);
        // }
        
        $url = env('BASE_URL').'create-card';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'user_email' => $user->email,
            'card_id' => $card_id,
            'subtotal' => $amount,
            'name_on_card' => $name_on_card,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $cards = $this->postFunction($url, $headers, $payloads);
        
        // Wallet::where('user_id', $user_id)->where('currency_id', '9')->update([
        //     'balance' => $wallet->balance - $total
        // ]);
        
        $details = Card::create([
            'user_id' => $user_id,
            'invite_id' => $cards['data']['invite_id'],
            'card_user_id' => $cards['data']['user_id'],
            'card_id' => $card_id,
            'status' => $cards['data']['status'],
            'type' => $cards['data']['type'],
            'card_holder' => $name_on_card
        ]);
        
        // Notification to User
        $userdevice = Device::where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '26')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '26',
            'language' => $device_lang
        ]);
        
        // Email to User
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 54,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A user '.$user->first_name . ' ' . $user->last_name.' has applied for the card.',
                'url_to_go'             => null,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 57,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{name}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{email}', $user->email, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{phone}', $user->formattedPhone, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card applied successfully.',
            'data'    => $details
        ]);
    }
    
    public function allCards(Request $request)
    {
        $user_id = $request->user_id;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $plan = json_decode($user['plan_data'], true);
        $plan_card_limit = $plan['virtual_card_limit']['value'];
        
        $cards = Card::where('user_id', $user_id)->get();
        $charge = CardFee::where('id', '1')->first();

        $card_data_array = [];
        foreach ($cards as $card) {
        
            $transactions = CardTopup::where('card_id', $card->id)->orderBy('id', 'desc')->limit(5)->get();
            
            $card_data_array[] = [
                'id' => $card->id,
                'user_id' => $card->user_id,
                'card_id' => $card->card_id,
                'last_four' => $card->last_four,
                'expiry_month' => $card->expiry_month,
                'expiry_year' => $card->expiry_year,
                'currency' => $card->currency,
                'type' => $card->type,
                'amount' => $card->amount,
                'status' => $card->status,
                'card_status' => $card->card_status,
                'card_holder' => $card->card_holder,
                'card_number' => $card->card_number,
                'card_cvv' => $card->card_cvv,
                'transactions' => $transactions,
                'billing_info' => $charge->billing_info
            ];
        }
        
        $card_data = [
            'cards' => $card_data_array,
            'limit' => $plan_card_limit,
            'plan_data' => $user->plan_data,
            'plan_expire' => $user->will_expire,
        ];
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Cards fetched successfully',
            'data'    => $card_data
        ]);
    }
    
    public function cardLimit(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        $limit = $request->limit;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $cards = Card::where('user_id', $user_id)->where('card_id', $card_id)->count();
        if($cards >= $limit){
            return response()->json([
                'status'  => 'error',
                'message' => 'Cards limit exceeded.',
                'data'    => $cards,
            ]);
        }
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Apply new card.',
            'data'    => null
        ]);
    }
    
    public function cardUpdate(Request $request)
    {
        $invite_id = $request->invite_id;
        $card_number = $request->card_number;
        $card_cvv = $request->card_cvv;
        $last_four = $request->last_four;
        $expiry_month = $request->expiry_month;
        $expiry_year = $request->expiry_year;
        $currency = $request->currency;
        $card_id = $request->card;
        
        $card = Card::where('invite_id', $invite_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        Card::where('invite_id', $invite_id)->update([
            'card' => $card_id,
            'last_four' => $last_four,
            'status' => 'success',
            'card_status' => 'active',
            'card_number' => $card_number,
            'card_cvv' => $card_cvv,
            'expiry_month' => $expiry_month,
            'expiry_year' => $expiry_year,
            'currency' => $currency,
        ]);
        
        // Notification to User
        $userdevice = Device::where('user_id', $card->user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '41')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $card->user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $card->user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '41',
            'language' => $device_lang
        ]);
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card details updated successfully.',
            'data'    => null
        ]);
    }
    
    public function cardDetails(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        $charge = CardFee::where('id', '1')->first();
        
        $transactions = CardTopup::where('card_id', $card->id)->orderBy('id', 'desc')->limit(5)->get();
        
        $cardDetails['id'] = $card->id;
        $cardDetails['user_id'] = $card->user_id;
        $cardDetails['amount'] = $card->amount;
        $cardDetails['last_four'] = $card->last_four;
        $cardDetails['type'] = $card->type;
        $cardDetails['expiry_month'] = $card->expiry_month;
        $cardDetails['expiry_year'] = $card->expiry_year;
        $cardDetails['status'] = $card->status;
        $cardDetails['card_status'] = $card->card_status;
        $cardDetails['currency'] = $card->currency;
        $cardDetails['card_holder'] = $card->card_holder;
        $cardDetails['created_at'] = $card->created_at;
        $cardDetails['card_number'] = $card->card_number;
        $cardDetails['card_cvv'] = $card->card_cvv;
        $cardDetails['billing_info'] = $charge->billing_info;
        $cardDetails['transactions'] = $transactions;
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card details fetched successfully.',
            'data'    => $cardDetails
        ]);
    }
    
    public function additionalDetails(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        $cardDetails['card_number'] = $card['card_number'];
        $cardDetails['card_cvv'] = $card['card_cvv'];
        $cardDetails['enc_path'] = env('ENC_PATH');
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card additional details fetched successfully.',
            'data'    => $cardDetails
        ]);
    }
    
    public function cardStatus(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        $card_status = $request->card_status;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        $url = env('BASE_URL').'card-status';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'card' => $card->card,
            'user_email' => $user->email,
            'status' => $card_status,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $cardStatus = $this->postFunction($url, $headers, $payloads);
        
        Card::where('id', $card_id)->where('user_id', $user_id)->update([
            'card_status' => $card_status
        ]);
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        $charge = CardFee::where('id', '1')->first();
        
        $cardDetails['id'] = $card->id;
        $cardDetails['user_id'] = $card->user_id;
        $cardDetails['amount'] = $card->amount;
        $cardDetails['last_four'] = $card->last_four;
        $cardDetails['type'] = $card->type;
        $cardDetails['expiry_month'] = $card->expiry_month;
        $cardDetails['expiry_year'] = $card->expiry_year;
        $cardDetails['status'] = $card->status;
        $cardDetails['card_status'] = $card->card_status;
        $cardDetails['currency'] = $card->currency;
        $cardDetails['card_holder'] = $card->card_holder;
        $cardDetails['card_number'] = $card->card_number;
        $cardDetails['card_cvv'] = $card->card_cvv;
        $cardDetails['created_at'] = $card->created_at;
        $cardDetails['billing_info'] = $charge->billing_info;
        
        // Notification to User
        $userdevice = Device::where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '38')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = str_replace('{last_four}', $card->last_four, $template->subheader);
        $subheader = str_replace('{status}', $card_status, $subheader);
        $message = str_replace('{last_four}', $card->last_four, $template->content);
        $message = str_replace('{status}', $card_status, $message);
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '38',
            'language' => $device_lang
        ]);
        
        // Email to User
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 70,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{last_four}', $card->last_four, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{status}', $card_status, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A user, '.$user->first_name . ' ' . $user->last_name.' has '.$card_status.' his card ending with '.$card->last_four,
                'url_to_go'             => null,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 71,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{last_four}', $card->last_four, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{status}', $card_status, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card status updated successfully.',
            'data'    => $cardDetails
        ]);
    }
    
    public function cardTopup(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        $fee = $request->fee;
        $subtotal = $request->subtotal;
        $total = $fee + $subtotal;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        $wallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
        if(!empty($wallet) && $wallet->balance < $total){
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient Balance',
                'data' => null,
            ]);
        }
        
        $url = env('BASE_URL').'card-topup';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'card' => $card->card,
            'user_email' => $user->email,
            'amount' => $subtotal,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $cardTopup = $this->postFunction($url, $headers, $payloads);
        
        Wallet::where('user_id', $user_id)->where('currency_id', '9')->update([
            'balance' => $wallet->balance - $total
        ]);
        
        Card::where('id', $card_id)->where('user_id', $user_id)->update([
            'amount' => $card->amount + $subtotal
        ]);
        
        $updatedWallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
        
        $charge = CardFee::where('id', '1')->first();
        $chargePercentage = $subtotal * ($charge->percent_charge / 100);
        
        $card_topup                 = new CardTopup();
        $card_topup->card_id        = $card_id;
        $card_topup->user_id        = $user_id;
        $card_topup->wallet_id      = $wallet->id;
        $card_topup->currency_id    = $wallet->currency_id;
        $card_topup->transaction_id = null;
        $card_topup->sub_total      = $subtotal;
        $card_topup->fees           = $fee;
        $card_topup->total          = $total;
        $card_topup->status         = 'paid';
        $card_topup->trx            = unique_code();
        $card_topup->type           = 'income';
        $card_topup->remark         = $request->remark;
        $card_topup->save();
        
        $transaction                           = new Transaction();
        $transaction->user_id                  = $user_id;
        $transaction->currency_id              = '9';
        $transaction->payment_method_id        = '1';
        $transaction->uuid                     = unique_code();
        $transaction->transaction_reference_id = $card_topup->id;
        $transaction->transaction_type_id      = '39';
        $transaction->subtotal                 = $subtotal;
        $transaction->percentage               = $charge->percent_charge;
        $transaction->charge_percentage        = $chargePercentage;
        $transaction->charge_fixed             = $charge->fixed_charge;
        $transaction->total                    = $total;
        $transaction->status                   = 'Success';
        $transaction->save();
        
        CardTopup::where('id', $card_topup->id)->update(['transaction_id' => $transaction->id]);
        
        $card_transaction                 = new CardTransaction();
        $card_transaction->card_id        = $card_id;
        $card_transaction->user_id        = $user_id;
        $card_transaction->wallet_id      = $wallet->id;
        $card_transaction->currency_id    = $wallet->currency_id;
        $card_transaction->transaction_id = $transaction->id;
        $card_transaction->sub_total      = $subtotal;
        $card_transaction->fees           = $fee;
        $card_transaction->total          = $total;
        $card_transaction->status         = 'paid';
        $card_transaction->trx            = unique_code();
        $card_transaction->type           = 'income';
        $card_transaction->remark         = $request->remark;
        $card_transaction->save();
        
        // Notification to User
        $userdevice = Device::where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '27')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '27',
            'language' => $device_lang
        ]);
        
        // Email to User
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 55,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{card_number}', $card->last_four, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{amount}', 'USD '.$total, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{fee}', 'USD '.$fee, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{amount_added}', 'USD '.$subtotal, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A user, '.$user->first_name . ' ' . $user->last_name.' has successfully reloaded an amount of USD '.$subtotal.' to the card ending with '.$card->last_four,
                'url_to_go'             => null,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 58,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{card_number}', $card->last_four, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount}', 'USD '.$total, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{fee}', 'USD '.$fee, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount_added}', 'USD '.$subtotal, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card topup done successfully.',
            'data'    => $card_topup
        ]);
    }
    
    public function previewTopup(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        $todayTopup = CardTopup::where('card_id', $card_id)->whereDate('created_at', Carbon::today())->sum('sub_total');
        $monthTopup = CardTopup::where('card_id', $card_id)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('sub_total');
        
        $charge = CardFee::where('id', '1')->first();
        $plan = json_decode($user['plan_data'], true);
        $preview['fixed_charge'] = $plan['card_reload_fee']['value'];
        $preview['min_limit'] = $charge->min_limit;
        $preview['max_limit'] = $charge->max_limit;
        $preview['daily_limit'] = $plan['daily_virtual_card_spending_limit']['value'];
        $preview['monthly_limit'] = $plan['monthly_virtual_card_spending_limit']['value'];
        $preview['recommended_amount'] = explode(",",$charge->recommended_amount);
        $preview['billing_info'] = $charge->billing_info;
        $preview['plan_expire'] = $charge->plan_expire;
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card charges fetched successfully.',
            'data'    => $preview
        ]);
    }
    
    public function cardTransactions(Request $request)
    {
        $user_id = $request->user_id;
        $card_id = $request->card_id;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if(empty($card)){
            return response()->json([
                'status' => 'error',
                'message' => 'Card not exists',
                'data' => null,
            ]);
        }
        
        $url = env('BASE_URL').'platform-card-transactions';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'card' => $card->card,
            'user_email' => $user->email,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $transactions = $this->postFunction($url, $headers, $payloads);
        
        foreach($transactions['data'] as $transaction){
            $exists = CardTransaction::where('card_transaction_id', $transaction['transaction_id'])->exists();
        
            if (!$exists) {
                $card_transaction                          = new CardTransaction();
                $card_transaction->card_id                 = $card_id;
                $card_transaction->user_id                 = $user_id;
                $card_transaction->currency_id             = '1';
                $card_transaction->card_transaction_id     = $transaction['transaction_id'];
                $card_transaction->card_transaction_time   = $transaction['user_transaction_time'];
                $card_transaction->sub_total               = $transaction['amount'];
                $card_transaction->total                   = $transaction['amount'];
                $card_transaction->status                  = 'paid';
                $card_transaction->trx                     = unique_code();
                $card_transaction->type                    = 'outcome';
                $card_transaction->remark                  = $transaction['merchant_name'];
                $card_transaction->save();
            }
        }
        
        $allTransactions = CardTransaction::where('card_id', $card_id)->orderBy('id', 'desc')->get();
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card transactions fetched successfully.',
            'data'    => $allTransactions
        ]);
    }
    
    public function cardAnalytics(Request $request)
    {
        $user_id = $request->user_id;
        $months = $request->months;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        
        $transactions = CardTopup::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), 'type', DB::raw('SUM(total) as total'))
        ->where('user_id', $user_id)
        ->where('created_at', '>=', $startDate)
        ->groupBy('month', 'type')
        ->orderBy('month')
        ->get();
        
        $monthMap = [];
        for($i = 0; $i < $months; $i++){
            $monthKey = $startDate->copy()->addMonths($i)->format('Y-m');
            $monthMap[$monthKey] = [
                'month' => $monthKey,
                'income' => 0,
                'outcome' => 0,
            ];
        }
        
        foreach($transactions as $raw){
            if(isset($monthMap[$raw->month])){
                $monthMap[$raw->month][$raw->type] = (float) $raw->total;
            }
        }
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Card analytics fetched successfully.',
            'data'    => array_values($monthMap),
        ]);
    }
    
    public function filterTransactions(Request $request)
    {
        $user_id   = $request->user_id;
        $card_id   = $request->card_id;
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $type      = $request->type;
    
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not exist',
                'data' => null,
            ]);
        }
    
        $card = Card::where('id', $card_id)->where('user_id', $user_id)->first();
        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Card does not exist',
                'data' => null,
            ]);
        }
    
        $query = CardTransaction::where('card_id', $card_id);
    
        if (!empty($type)) {
            $query->where('type', $type);
        }
    
        if (!empty($from_date)) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if (!empty($to_date)) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        $allTransactions = $query->orderBy('id', 'desc')->get();
    
        return response()->json([
            'status'  => 'success',
            'message' => 'Filtered card transactions fetched successfully.',
            'data'    => $allTransactions
        ]);
    }
    
    public function subscriptionDetails(Request $request)
    {
        $user_id   = $request->user_id;
    
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not exist',
                'data' => null,
            ]);
        }
        
        $url = env('BASE_URL') . 'subscription-plans';
    
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
    
        $payloads = [
            'platform' => "Sandbox Ewallet"
        ];
    
        $plans = $this->postFunction($url, $headers, $payloads);
        $lastPlan = end($plans['data']);
        
        if($lastPlan['id'] == $user->plan_id){
            $upgrade_plan = false;
        }else{
            $upgrade_plan = true;
        }
        
        $plan = json_decode($user->plan_data, true);
        
        $expireDate = Carbon::parse($user->will_expire);
        $today = Carbon::today();
        if ($expireDate->lt($today)) {
            $status = "Expired";
        } else {
            $status = "Active";
        }
        
        $subscription['plan_id'] = $user->plan_id;
        $subscription['plan_name'] = $user->plan_name;
        $subscription['plan_amount'] = 'USD '.number_format($user->plan_amount, 2, '.', ',');
        $subscription['will_expire'] = $user->will_expire;
        $subscription['card_limit'] = $plan['virtual_card_limit']['value'];
        $subscription['deposit_fee'] = $plan['card_reload_fee']['value'];
        $subscription['transaction_fee'] = $plan['transaction_fee']['value'];
        $subscription['online_purchase'] = $plan['online_purchase']['value'];
        $subscription['service_fee'] = $plan['virtual_card_service_fee']['value'];
        $subscription['dialy_limit'] = $plan['daily_virtual_card_spending_limit']['value'];
        $subscription['monthly_limit'] = $plan['monthly_virtual_card_spending_limit']['value'];
        $subscription['status'] = $status;
        $subscription['upgrade_plan'] = $upgrade_plan;
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription details fetched successfully.',
            'data'    => $subscription
        ]);
    }
    
    public function upgradeSubscription(Request $request)
    { 
        $user_id = $request->user_id;
        $plan_id = $request->plan_id;
        $amount = $request->amount;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        if($plan_id != '10'){
            $wallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
            if(!empty($wallet) && $wallet->balance <= $amount){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient Balance',
                    'data' => null,
                ]);
            }
        }
        
        $url = env('BASE_URL').'upgrade-subscription';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'plan_id' => $plan_id,
            'user_email' => $user->email,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $plans = $this->postFunction($url, $headers, $payloads);
        
        User::where('id', $user_id)->update([
            'plan_data' => json_encode($plans['data']['user']['plan_data']),
            'plan_id' => $plans['data']['user']['plan_id'],
            'plan_name' => $plans['data']['plan']['title'],
            'plan_amount' => $plans['data']['plan']['price'],
            'will_expire' => $plans['data']['user']['will_expire'],
        ]);
        
        $card_subs                  = new CardSubscription();
        $card_subs->plan_id         = $plans['data']['user']['plan_id'];
        $card_subs->plan_data       = json_encode($plans['data']['user']['plan_data']);
        $card_subs->user_id         = $user_id;
        $card_subs->wallet_id       = $wallet->id ?? '';
        $card_subs->currency_id     = '9';
        $card_subs->transaction_id  = null;
        $card_subs->sub_total       = $amount;
        $card_subs->fees            = 0;
        $card_subs->total           = $amount;
        $card_subs->status          = 'Success';
        $card_subs->trx              = unique_code();
        $card_subs->remarks         = 'Subscription Upgrade';
        $card_subs->save();
        
        if($plan_id != '10'){
            Wallet::where('user_id', $user_id)->where('currency_id', '9')->update([
                'balance' => $wallet->balance - $amount
            ]);
            
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = '9';
            $transaction->payment_method_id        = '1';
            $transaction->uuid                     = unique_code();
            $transaction->transaction_reference_id = $card_subs->id;
            $transaction->transaction_type_id      = '41';
            $transaction->subtotal                 = $amount;
            $transaction->percentage               = 0;
            $transaction->charge_percentage        = 0;
            $transaction->charge_fixed             = 0;
            $transaction->total                    = $amount;
            $transaction->status                   = 'Success';
            $transaction->save();
            
            CardSubscription::where('id', $card_subs->id)->update(['transaction_id' => $transaction->id]);
        }
        
        // Notification to User
        $userdevice = Device::where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '39')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '39',
            'language' => $device_lang
        ]);
        
        $userPlan = User::where('id', $user_id)->first();
        $plan = json_decode($userPlan['plan_data'], true);
        $plan_card_limit = $plan['virtual_card_limit']['value'];
        $plan_deposit_fee = $plan['card_reload_fee']['value'];
        $plan_transaction_fee = $plan['transaction_fee']['value'];
        $plan_online_purchase = $plan['online_purchase']['value'];
        $plan_service_fee = $plan['virtual_card_service_fee']['value'];
        $plan_dialy_limit = $plan['daily_virtual_card_spending_limit']['value'];
        $plan_monthly_limit = $plan['monthly_virtual_card_spending_limit']['value'];
        
    	// Email to User
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 73,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A user '.$user->first_name . ' ' . $user->last_name.' has upgraded subscription successfully.',
                'url_to_go'             => null,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 74,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription upgraded successfully.',
            'data'    => $plans
        ]);
    }
    
    public function renewSubscription(Request $request)
    { 
        $user_id = $request->user_id;
        $plan_id = $request->plan_id;
        $amount = $request->amount;
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)){
            return response()->json([
                'status' => 'error',
                'message' => 'User not exists',
                'data' => null,
            ]);
        }
        
        if($plan_id != '10'){
            $wallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
            if(!empty($wallet) && $wallet->balance <= $amount){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient Balance',
                    'data' => null,
                ]);
            }
        }
        
        $url = env('BASE_URL').'renew-subscription';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'plan_id' => $plan_id,
            'user_email' => $user->email,
            'platform' => 'Sandbox Ewallet',
        ];
        
        $plans = $this->postFunction($url, $headers, $payloads);
        
        User::where('id', $user_id)->update([
            'plan_data' => json_encode($plans['data']['user']['plan_data']),
            'plan_id' => $plans['data']['user']['plan_id'],
            'plan_name' => $plans['data']['plan']['title'],
            'plan_amount' => $plans['data']['plan']['price'],
            'will_expire' => $plans['data']['user']['will_expire'],
        ]);
        
        $card_subs                  = new CardSubscription();
        $card_subs->plan_id         = $plans['data']['user']['plan_id'];
        $card_subs->plan_data       = json_encode($plans['data']['user']['plan_data']);
        $card_subs->user_id         = $user_id;
        $card_subs->wallet_id       = $wallet->id ?? '';
        $card_subs->currency_id     = '9';
        $card_subs->transaction_id  = null;
        $card_subs->sub_total       = $amount;
        $card_subs->fees            = 0;
        $card_subs->total           = $amount;
        $card_subs->status          = 'Success';
        $card_subs->trx             = unique_code();
        $card_subs->remarks         = 'Subscription Renew';
        $card_subs->save();
        
        if($plan_id != '10'){
            Wallet::where('user_id', $user_id)->where('currency_id', '9')->update([
                'balance' => $wallet->balance - $amount
            ]);
            
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = '9';
            $transaction->payment_method_id        = '1';
            $transaction->uuid                     = unique_code();
            $transaction->transaction_reference_id = $card_subs->id;
            $transaction->transaction_type_id      = '41';
            $transaction->subtotal                 = $amount;
            $transaction->percentage               = 0;
            $transaction->charge_percentage        = 0;
            $transaction->charge_fixed             = 0;
            $transaction->total                    = $amount;
            $transaction->status                   = 'Success';
            $transaction->save();
            
            CardSubscription::where('id', $card_subs->id)->update(['transaction_id' => $transaction->id]);
        }
        
        // Notification to User
        $userdevice = Device::where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $template = NotificationTemplate::where('temp_id', '31')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'cards',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '31',
            'language' => $device_lang
        ]);
        
        $userPlan = User::where('id', $user_id)->first();
        $plan = json_decode($userPlan['plan_data'], true);
        $plan_card_limit = $plan['virtual_card_limit']['value'];
        $plan_deposit_fee = $plan['card_reload_fee']['value'];
        $plan_transaction_fee = $plan['transaction_fee']['value'];
        $plan_online_purchase = $plan['online_purchase']['value'];
        $plan_service_fee = $plan['virtual_card_service_fee']['value'];
        $plan_dialy_limit = $plan['daily_virtual_card_spending_limit']['value'];
        $plan_monthly_limit = $plan['monthly_virtual_card_spending_limit']['value'];
        
    	// Email to User
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 66,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A user '.$user->first_name . ' ' . $user->last_name.' has renewed subscription successfully.',
                'url_to_go'             => null,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 72,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Subscription renewed successfully.',
            'data'    => $plans
        ]);
    }
}