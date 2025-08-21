<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\UserDetail;
use App\Models\Country;
use App\Models\Currency;
use App\Models\FeesLimit;
use Carbon\Carbon;
use App\Models\EmailTemplate;
use App\Models\TransDeviceInfo;

class TopupController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unverifiedUser     = 201;
    public $inactiveUser       = 501;
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    
    public function __construct()
    {
        $this->main_url       = "https://topups.reloadly.com"; // Main BASE URL
        $this->helper         = new Common();
        $this->user           = new User();
        $this->email          = new EmailController();
    }
    
    /***********************Generate Token Function START***************************/
    
    public function generateToken() {
        $url = "https://auth.reloadly.com/oauth/token";
        /***********************Sandbox Token*********************************/
        // $fields =json_encode(
        //          array(
        //         	"client_id"=>"EqOrfznTj2ekkDSD9ZUPnYsgRyu6oRsf",
        //         	"client_secret"=>"p4YD6eUMRlU64j80AX2jY0w5n4rpodJZiD_YlqoWkb1q91NRu0B1dZSUUTyrLq5Z",
        //         	"grant_type"=>"client_credentials",
        //         	"audience"=>$this->main_url
        //          )
        //      );
        /***********************Sandbox Token*********************************/
        /*************************Live Token*********************************/
        //echo $this->main_url; die;
        $fields =json_encode(
                 array(
                	"client_id"=>"Vj8AtyPjXtEcMd0iC1WItYJmmQ9aZGvw",
                	"client_secret"=>"-prpPdWmYcaMhd8KZOOyU0WctjqyR5POJlKrVMAimTpJwBdEWOa_stmXaBrm-j7y",
                	"grant_type"=>"client_credentials",
                	"audience"=>$this->main_url
                 )
             );
        /***********************Live Token*********************************/
             //print_r($fields); die;
        $method = "POST";
        $data = $this->run_curl($url, $fields, $method, false, true);
        //print_r($data); die;
        $this->my_token = $data->access_token;
    }
    
    /*********************Generate Token Function END***************************/
    /***********************RUN CURL Function START***************************/
    public function run_curl ($url, $fields, $method, $header = false, $auth = false) {
        $ch = curl_init();
        if($auth == false) {
        $url = $this->main_url . $url;
        }
        //echo $url; die;
        if($header == true) {
            
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Accept: application/com.reloadly.topups-v1+json","Authorization: Bearer ".$this->my_token)); // Live Token
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // Live
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($method=='POST')
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        
        //close connection
        curl_close($ch);
        
       return json_decode($result);
    }
    
     /***********************RUN CURL Function END***************************/
    /***********************GET Countries Function START***************************/
    // public function getCountries(Request $request)
    // {
    //     $this->generateToken();
    //     $url = "/countries";
    //     $fields =json_encode(
    //             array(
    //             )
    //         );
    //     $method = "GET";
    //     $data = $this->run_curl($url, $fields, $method, true);
    //     return response()->json([
    //         'status'                => $this->successStatus,
    //         'data'                  => $data
    //     ]);
    // }
    
    //Get Countries New Function (Shubham Kumar Date : 12/03/2021)
    public function getCountries()
    {
        //$data = DB::table('countries')->select('short_name AS isoName','name AS name','currency_code AS currencyCode','flag AS flag','phone_code AS callingCodes')->get();
       
        $data = DB::table('countries')->where('status',1)->select('id','short_name AS isoName','name AS name','phone_code AS currencyCode','flag AS flag', 'iso3', 'defualt')->orderBy('name', 'asc')->get();
        foreach ($data as $value) {
          $value->callingCodes[] = '+'.DB::table('countries')->where('short_name',$value->isoName)->first()->phone_code;
        }
        foreach ($data as $value1) {
           $value2 = DB::table('countries')->where('short_name','=',$value1->isoName)->first()->short_name;
           //dd($value1);
           $value3 = ENV('COUNTRY_FLAG').strtolower($value2).'.png';
           $value1->flag_new= $value3;
           //dd($value1);
        }
        // print json_encode($data);
        // die;
        
        return response()->json($data);
            //'status'                => $this->successStatus,
            //'data'                  => 
        
       // echo "test";
    }
    /***********************GET Countries Function END***************************/
    /*********************GET Operators By ISO Function Start***********************/
    public function getOperator(Request $request)
    {
        $this->generateToken();
        $phone = $request->phone;
        $prefix = $request->carrierCode;
        $country = $request->defaultCountry;
        $completephone = $prefix.$phone;
        $fields =json_encode(
                array(
                )
            );
        $url = "/operators/countries/$country?includeBundles=true&includeData=true&includePin=true&suggestedAmounts=true&suggestedAmountsMap=true";
        $method = "GET";
        $data = $this->run_curl($url, $fields, $method, true, false);
       
        if(!empty($data->errorCode)){
            if($data->errorCode == 'INVALID_COUNTRY_ISO_CODE'){
                return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => 'Country not supported',
                    'data'    => null
                ]);
            }else{
                return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => $data->errorCode,
                    'data'    => null
                ]);
            }
        }else{
            foreach($data as $res)
            {
                $value[] = array(
                    'name' => $res->name,
                    'operator_id' => $res->operatorId,
                    'logo' => $res->logoUrls[0],
                );
            }
            
            $response['mobile_number'] = $completephone;
            $response['defaultCountry'] = $request->defaultCountry;
            $response['carrierCode'] = $request->carrierCode;
            $response['operators'] = $value;
            
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Operators fetched successfully.',
                'data'    => $response,
            ]);
        }
    }
    
    public function getoperatorplan(Request $request)
    {
        $this->generateToken();
        $operator_id = $request->operator_id;
        $fields =json_encode(
                array(
                )
            );
        $url = '/operators/'.$operator_id;
        $method ='GET';
        $result = $this->run_curl($url, $fields, $method, true, false);

        if(!empty($result->geographicalRechargePlans[0]->fixedAmounts)){
            foreach($result->geographicalRechargePlans[0]->fixedAmounts as $key => $plan)
            {
                $value[] = array(
                    'fixed_amount' => $plan,
                    'local_amount' => $result->geographicalRechargePlans[0]->localAmounts[$key],
                );
            }
        }else{
            foreach($result->fixedAmounts as $key => $plan)
            {
                $value[] = array(
                    'fixed_amount' => $plan,
                    'local_amount' => $result->localFixedAmounts[$key],
                );
            }
        }
        
        $response['operator_id'] = $result->operatorId;
        $response['name'] = $result->name;
        $response['sender_currency_code'] = $result->senderCurrencyCode;
        $response['destination_currency_code'] = $result->destinationCurrencyCode;
        $response['logo'] = $result->logoUrls[0];
        $response['plans'] = $value;
        
        return response()->json([
            'status'  => $this->successStatus,
            'message' => 'Operator Plans fetched successfully.',
            'data'    => $response,
        ]);
    }
    
    public function getvalue(Request $request)
    {
        $this->generateToken();
        $mobilenumber = $request->mobilenumber;
        $operator_name = $request->operator_name;
        $logo = $request->logo;
        $sender_currency_code = $request->sender_currency_code;
        $destination_currency_code = $request->destination_currency_code;
        $operatorId = $request->operator_id;
        $amount = $request->recharge_amount;
        $url = '/operators/fx-rate';
        $fields = json_encode(array('operatorId'=>$operatorId,'amount'=>$amount));
        $method ='POST';
        $value = $this->run_curl($url, $fields, $method, true, false);
        
        $result['value'] = $value;
        $result['phone_num'] = $mobilenumber;
        $result['rec_amount'] = $amount;
        $result['logo'] = $logo;
        $result['sender_currency_code'] = $sender_currency_code;
        $result['destination_currency_code'] = $destination_currency_code;
        $result['operatorId'] = $operatorId;
        $result['operator_name'] = $operator_name;
        $result['defaultcountry'] = $request->defaultcountry;
        $result['carriercode'] = $request->carriercode;
       
        return response()->json([
            'status'  => $this->successStatus,
            'message' => 'Recharge details fetched successfully.',
            'data'    => $result,
        ]);
    }
    
    public function mywallets(Request $request)
    {
        $user_id = $request->user_id;
        
        $wallet            = new Wallet();
        $wallets = $wallet->getAvailableBalanceNew($user_id);
        
        if(!empty($wallets)){
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Wallet details fetched successfully.',
                'data'    => $wallets,
            ]);
        }else{
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Wallets not found!',
                'data'    => null
            ]);
        }
    }
    
    public function getwallet(Request $request)
    {
        $amount = $request->amount;
        $wallet = $request->wallet;
        
        $current_balance = Wallet::where('id', $wallet)->first();
        $currency_id = $current_balance->currency_id;
        $currency = Currency::where('id', '9')->first();
        $new_curr = Currency::where('id', $currency_id)->first();
       
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['exchange_from', 'code', 'rate', 'symbol']);
        
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => '9'], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['rate', 'symbol']);
                $exchangevalue = getCurrencyRate($currency->code, $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat = $toWalletRate * (request('amount'));
            $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
        }
        
        $feesDetailsforTop = FeesLimit::where(['transaction_type_id' => Recharge, 'currency_id' => $currency_id])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);
        $recharge_fee_total = $feesDetailsforTop->charge_fixed + ($new_amount*$feesDetailsforTop->charge_percentage/100);
        $new_fee = number_format((float)$recharge_fee_total, 2, '.', '');
        $new_total = $new_amount + $new_fee;
        
        if(!empty($current_balance->balance) && $current_balance->balance >= $new_total){
            $result['amount'] = $new_amount;
            $result['fee'] = $new_fee;
            $result['total'] = number_format((float)$new_total, 2, '.', '');;
            $result['currency'] = $new_curr->code;
            
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
    
    public function makerecharge(Request $request)
    {
        $this->generateToken();
        $phone = $request->mobile;
        $amount = $request->amount;
        $operator_id = $request->operator_id;
        $wallet = $request->wallet;
        $country = $request->defaultcountry;
        
        $current_balance = Wallet::where('id', $wallet)->first();
        $currency_id = $current_balance->currency_id;
        $currency = Currency::where('id', '9')->first();
        $currency_new = Currency::where('id', $currency_id)->first();
       
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['exchange_from', 'code', 'rate', 'symbol']);
        
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => '9'], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['rate', 'symbol']);
                $exchangevalue = getCurrencyRate($currency->code, $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat = $toWalletRate * (request('amount'));
            $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
        }
        
        $feesDetailsforTop = FeesLimit::where(['transaction_type_id' => Recharge, 'currency_id' => $currency_id])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);
        $recharge_fee_total = $feesDetailsforTop->charge_fixed + ($new_amount*$feesDetailsforTop->charge_percentage/100);
        $new_fee = number_format((float)$recharge_fee_total, 2, '.', '');
        
        $new_total = $new_amount + $new_fee;
        
        if(!empty($current_balance->balance) && $current_balance->balance >= $new_total){
            $recipientPhone = array("countryCode"=>$country,"number"=>$phone);
            
            $fields =json_encode(array(
                'operatorId'=>$operator_id,
                'amount'=>$amount,
                'useLocalAmount'=>false,
                'customIdentifier'=>uniqid(),
                'recipientPhone'=>$recipientPhone,
                'senderPhone'=>$recipientPhone,
            ));
            $url = "/topups";
            $method = "POST";
            //dd($fields);
            $data = $this->run_curl($url, $fields, $method, true);
            
            if(!empty($data->transactionId)) {
                Wallet::where('id', $wallet)->update([
                    'balance' => $current_balance->balance - $new_total,
                ]);
                
                date_default_timezone_set("Asia/Calcutta");
                $tr_time = Carbon::now()->format('d M Y h:i A');
                
                //Transaction
                $transaction                           = new Transaction();
                $transaction->user_id                  = $request->user_id;
                $transaction->phone                    = $recipientPhone['number'];
                $transaction->end_user_id              = null;
                $transaction->currency_id              = $currency_id;
                $transaction->uuid                     = strtoupper(uniqid());
                $transaction->transaction_reference_id = $data->transactionId;
                $transaction->transaction_type_id      = 15;
                $transaction->user_type                = 'registered';
                $transaction->subtotal                 = $new_amount;
                $transaction->percentage               = $feesDetailsforTop->charge_percentage;
                $transaction->charge_percentage        = $new_amount*$feesDetailsforTop->charge_percentage/100;
                $transaction->charge_fixed             = $feesDetailsforTop->charge_fixed;
                $transaction->total                    = '-'.$new_total;
                $transaction->note                     = null;
                $transaction->status                   = "Success";
                $transaction->local_tran_time          = $request->local_tran_time;
                $transaction->ip_address               = request()->ip();
                $transaction->save();
                
                $rs = TransDeviceInfo::create([
                    'user_id' => $request->user_id, 
                    'trans_id' => $transaction->id, 
                    'device_id' => $request->device_id, 
                    'app_ver' => $request->app_ver, 
                    'device_name' => $request->device_name, 
                    'device_manufacture' => $request->device_manufacture, 
                    'device_model' =>$request->device_model, 
                    'os_ver' => $request->os_ver, 
                    'device_os' => $request->device_os, 
                    'ip_address' => request()->ip(),
                ]);
                
                $value['tr_amount'] = $new_amount;
                $value['tr_fee'] = $new_fee;
                $value['tr_total'] = $new_total;
                $value['tr_currency'] = $currency_new->code;
                $value['tr_id'] = $data->transactionId;
                $value['tr_time'] = $tr_time;
                
                $user = User::where('id', $request->user_id)->first();
                
                $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                if(!empty($userdevice)){
                    $device_lang = $userdevice->language;
                }else{
                    $device_lang = getDefaultLanguage();
                }
        	
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 46,
                    'language_id' => $device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{amount}', $new_total, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{created_at}', request('local_tran_time'), $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{uuid}', $transaction->uuid, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{currencycode}', $currency_new->code, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{fee}', $pending_transaction->charge_percentage + $pending_transaction->charge_fixed, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
                
                return response()->json([
                    'status'  => $this->successStatus,
                    'message' => 'Topup done successfully.',
                    'data'    => $value,
                ]);
            } else {
                return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => 'Recharge was not successfull.',
                    'data'    => null
                ]);
            }
        }else{
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Insufficient Fund!',
                'data'    => null
            ]);
        }
    }
    
    
    
    public function getOperatorsByIso(Request $request)
    {
        $this->generateToken();
        $iso = $request->iso;
        $fields =json_encode(
                array(
                )
            );
        $url = "/operators/countries/$iso?includeBundles=true&includeData=true&includePin=true&suggestedAmounts=true&suggestedAmountsMap=true";
        $method = "GET";
        $data = $this->run_curl($url, $fields, $method, true);
        return response()->json([
            'status'                => $this->successStatus,
            'data'                  => $data
        ]);
    }
    
    /*********************GET Operators By ISO Function END***********************/
    /*********************GET Operators FOR PHONE Function Start***********************/
    public function getOperatorsForPhone(Request $request)
    {
        $this->generateToken();
        $iso = $request->iso;
        $phone = $request->phone;
        $fields =json_encode(
                array(
                )
            );
        $url = "/operators/auto-detect/phone/$phone/countries/$iso?suggestedAmountsMap=true";
        //echo $url; die;
        $method = "GET";
        $data = $this->run_curl($url, $fields, $method, true);
        return response()->json([
            'status'                => $this->successStatus,
            'data'                  => $data
        ]);
    }
    
    
    public function getOperatorById(Request $request)
    {
        // echo("hello"); die;
        $this->generateToken();
        $operatorid = $request->operatorid;
        $fields =json_encode(
                array(
                )
            );
        $url = "/operators/$operatorid?suggestedAmountsMap=true";
        //echo $url; die;
        $method = "GET";
        $data = $this->run_curl($url, $fields, $method, true);
        return response()->json([
            'status'                => $this->successStatus,
            'data'                  => $data
        ]);
    }
    
    
    /*******************GET Operators FOR PHONE Function END***********************/
    /*********************GET Balance Function Start***********************/
    
    public function getBalance(Request $request)
    {
        $this->generateToken();
        $fields =json_encode(
                array(
                )
            );
        $url = "/accounts/balance";
        //echo $url; die;
        $method = "GET";
        $data = $this->run_curl($url, $fields, $method, true);
        return response()->json([
            'status'                => $this->successStatus,
            'data'                  => $data
        ]);
    }
    
    /*********************GET Balance Function END***********************/
    /*********************TOP UP FUNCTION START***********************/
    // public function topUp(Request $request)
    // {
    //     $phone = $request->phone;
    //     $country = $request->country;
    //     $amount = $request->amount;
    //     $operator_id = $request->operator_id;
    //     $this->generateToken();
        
    //     $recipientPhone = array("countryCode"=>$country,"number"=>$phone);
        
    //     $fields =json_encode(array(
    //         'operatorId'=>$operator_id,
    //         'amount'=>$amount,
    //         'useLocalAmount'=>false,
    //         'customIdentifier'=>uniqid(),
    //         'recipientPhone'=>$recipientPhone,
    //         'senderPhone'=>$recipientPhone,
    //         ));
    //     $url = "/topups";
    //     //echo $url; die;
    //     $method = "POST";
    //     $data = $this->run_curl($url, $fields, $method, true);
    //     if($data && @$data->errorCode) {
    //      return response()->json([
    //                 'status'                => $this->unauthorisedStatus,
    //                 'message'               => $data->message
    //             ]);
    //     }
    //     if($data && $data->transactionId) {
            
    //         // USER WALLET
    //         $current_balance = Wallet::where([
    //                     'user_id'     => $request->user_id,
    //                     'currency_id' => $request->currency_id,
    //                 ])->select('balance')->first();
    //         // dd($current_balance);

    //         Wallet::where([
    //             'user_id'     => $request->user_id,
    //             'currency_id' => $request->currency_id,
    //         ])->update([
    //             'balance' => $current_balance->balance - $request->amount,
    //         ]);
            
    //       // USER WALLET
           
    //       //Transaction
    //         $transaction                           = new Transaction();
    //         $transaction->user_id                  = $request->user_id;
    //         $transaction->end_user_id              = null;
    //         $transaction->currency_id              = $request->currency_id;
    //         $transaction->uuid                     = strtoupper(uniqid());
    //         $transaction->transaction_reference_id = $data->transactionId;
    //         $transaction->transaction_type_id      = 15;
    //         $transaction->user_type                = 'registered';
    //         $transaction->subtotal          = $data->requestedAmount - $data->discount;
    //         $transaction->percentage        = 0;
    //         $transaction->charge_percentage = 0;
    //         $transaction->charge_fixed      = $data->discount;
    //         $transaction->total             = '-'.$data->requestedAmount;
    //         $transaction->note              = null;
    //         $transaction->status            = "Success";
    //         $transaction->ip_address        = $request->getClientIp();
    //         $transaction->save();
            
    //         $subject   = "Recharge Successfull!";
    //         $subheader = "Your prepaid mobile number is successfully recharged";
    //         $date    = date("m-d-Y h:i");
    //         $message = "Dear Customer, your mobile number ".$request->phone." has been successfully recharged with amount ".$this->helper->getcurrencyCode($request->currency_id)." ".$request->amount."";
    //         $this->helper->sendFirabasePush($subject,$subheader,$request->user_id);
    //         $datanotice1= array('title'=>$subject,'content'=>$message,'type'=>'push','content_type'=>'topup','user'=>$request->user_id,'sub_header'=>$subheader,'push_date'=>request('local_tran_time'));
    //     	DB::table('noticeboard')->insert($datanotice1);
            
    //         // $userdevices               = DB::table('devices')->where(['user_id' => $request->user_id])->first();
    //         //  if(isset($userdevices) && $userdevices->fcm_token)
    //         // {
    //         //     $msg= 'Your topup of amount '.$data->requestedAmount.' successfully done.';
                
    //         // 	//echo "<pre>"; print_r($userdevices); die;
    //         // 	$notifyData   = array (
    //         // 	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
    //         // 	'title'         => 'Topup done Successfully!',
    //         // 	'content'       => $msg,
    //         // 	'type'          => 'Message',
    //         // 	// Require for auto fetch incoming request push.
    //         // 	'payload'       => array (//'post' => $data->created_at
    //         // 		)
    //         // 	);
    //         // 	$datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
    //         // 	DB::table('noticeboard')->insert($datanotice);
    //         // 	$this->helper->sendFCMPush($notifyData);
    //         // }
            
            
    //         $wallet            = new Wallet();
    //         $wallets           = $wallet->getAvailableBalance($request->user_id);
            
    //             return response()->json([
    //             'status'                => $this->successStatus,
    //             'data'                  => $data, 
    //             'balance'               => $wallets
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status'                => $this->unauthorisedStatus,
    //                 'message'               => "Recharge was not successfull. Please try again."
    //             ]);
    //         }

    // }
    
    // public function topUp(Request $request)
    // {
    //     //dd($request->all());
    //     $phone = $request->phone;
    //     $country = $request->country;
    //     $amount = $request->amount;
    //     $ramount = $request->ramount;
    //     $operator_id = $request->operator_id;
    //     $this->generateToken();
        
    //     // USER WALLET
    //         $current_balance = Wallet::where([
    //                     'user_id'     => $request->user_id,
    //                     'currency_id' => $request->currency_id,
    //                 ])->select('balance')->first();
    //          //dd($current_balance->balance);
            
    //     if($current_balance->balance >= $amount){
        
    //         $recipientPhone = array("countryCode"=>$country,"number"=>$phone);
            
    //         $fields =json_encode(array(
    //             'operatorId'=>$operator_id,
    //             'amount'=>$amount,
    //             'useLocalAmount'=>false,
    //             'customIdentifier'=>uniqid(),
    //             'recipientPhone'=>$recipientPhone,
    //             'senderPhone'=>$recipientPhone,
    //             ));
    //         $url = "/topups";
    //         //echo $url; die;
    //         $method = "POST";
    //         $data = $this->run_curl($url, $fields, $method, true);
    //         //dd($data);
            
    //         if($data && @$data->errorCode) {
    //          return response()->json([
    //                     'status'                => $this->unauthorisedStatus,
    //                     'message'               => $data->message
    //                 ]);
    //         }
    //         if($data && $data->transactionId) {
    
    //             Wallet::where([
    //                 'user_id'     => $request->user_id,
    //                 'currency_id' => $request->currency_id,
    //             ])->update([
    //                 'balance' => $current_balance->balance - $request->amount,
    //             ]);
                
    //             // USER WALLET
                
    //             $user_country = UserDetail::where('user_id', $request->user_id)->first();
                        
    //             if(!empty($user_country)){
    //                 $country = Country::where('id', $user_country->country_id)->first();
    //                 $country_name = $country->name;
    //             }else{
    //                 $country_name = '-';
    //             }
               
    //             //Transaction
    //             $transaction                           = new Transaction();
    //             $transaction->user_id                  = $request->user_id;
    //             $transaction->end_user_id              = null;
    //             $transaction->currency_id              = $request->currency_id;
    //             $transaction->uuid                     = strtoupper(uniqid());
    //             $transaction->transaction_reference_id = $data->transactionId;
    //             $transaction->transaction_type_id      = 15;
    //             $transaction->user_type                = 'registered';
    //             // $transaction->subtotal              = $data->requestedAmount - $data->discount;
    //             $transaction->subtotal                 = $request->amount;
    //             $transaction->percentage               = 0;
    //             $transaction->charge_percentage        = 0;
    //             $transaction->phone                    = $request->phone;
    //             $transaction->charge_fixed             = $data->discount;
    //             $transaction->total                    = '-'.$request->amount;
    //             $transaction->note                     = null;
    //             $transaction->status                   = "Success";
    //             $transaction->ip_address               = $request->getClientIp();
    //             $transaction->country                  = $country_name;
    //             $transaction->save();
                
    //             $userdevices      = DB::table('devices')->where(['user_id' => $request->user_id])->first();
    //             if(isset($userdevices) && $userdevices->fcm_token)
    //             {
    //                 $msg= 'Your topup of amount '.$data->requestedAmount.' successfully done.';
    //                 $notifyData   = array (
    //             	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
    //             	'title'         => 'Topup done Successfully!',
    //             	'content'       => $msg,
    //             	'type'          => 'Message',
    //                 'payload'       => array (//'post' => $data->created_at
    //             		)
    //             	);
    //             	$datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
    //             	DB::table('noticeboard')->insert($datanotice);
    //             //	$this->helper->sendFCMPush($notifyData);
    //             }
                
                
    //             $wallet            = new Wallet();
    //             $wallets           = $wallet->getAvailableBalance($request->user_id);
                
    //             return response()->json([
    //             'status'                => $this->successStatus,
    //             'data'                  => $data, 
    //             'balance'               => $wallets
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status'                => $this->unauthorisedStatus,
    //                 'message'               => "Recharge was not successfull. Please try again."
    //             ]);
    //         }
    //     }else{
    //         return response()->json([
    //             'status'                => $this->unauthorisedStatus,
    //             'message'               => "Insufficient Fund."
    //         ]);
    //     }

    // }
    
    public function topUp(Request $request)
    {
        $phone = $request->phone;
        $country = $request->country;
        $amount = $request->amount;
        $ramount = $request->ramount; // in USD
        // 17/11/22
        $total_amount = $request->total_amount;
        $charge_percentage=$request->recharge_percetage;
        $charge_fixed=$request->recharge_fixed;
        $operator_id = $request->operator_id;
        $this->generateToken();
        $user_id=$request->user_id;
        
        // USER WALLET
            $current_balance = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();
             //dd($current_balance->balance);
        
        if(!empty($current_balance)){
            if($current_balance->balance >= $amount){
            
                $recipientPhone = array("countryCode"=>$country,"number"=>$phone);
                
                $fields =json_encode(array(
                    'operatorId'=>$operator_id,
                    'amount'=>$ramount,
                    'useLocalAmount'=>false,
                    'customIdentifier'=>uniqid(),
                    'recipientPhone'=>$recipientPhone,
                    'senderPhone'=>$recipientPhone,
                    ));
                $url = "/topups";
                //echo $url; die;
                $method = "POST";
                $data = $this->run_curl($url, $fields, $method, true);
                
                if($data && @$data->errorCode) {
                 return response()->json([
                            'status'                => $this->unauthorisedStatus,
                            'message'               => $data->message
                        ]);
                }
                if($data && $data->transactionId) {
                    Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->update([
                        // 'balance' => $current_balance->balance - ($request->ramount + $total_fee),
                        'balance' => $current_balance->balance - $request->total_amount,

                    ]);
                    
                    // USER WALLET
                    
                    $user_country = UserDetail::where('user_id', $request->user_id)->first();
                            
                    if(!empty($user_country)){
                        $country = Country::where('id', $user_country->country_id)->first();
                        $country_name = $country->name;
                    }else{
                        $country_name = '-';
                    }
                    
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $request->user_id;
                    $transaction->currency_id              = $request->currency_id;
                    // $transaction->payment_method_id        = '';
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $data->transactionId;
                    $transaction->transaction_type_id      = 11;
                    $transaction->subtotal                 = $request->amount;
                    $transaction->percentage               = 0;
                    $transaction->charge_percentage        = $charge_percentage;
                    $transaction->charge_fixed             = $charge_fixed;
                    // $transaction->charge_fixed             = $data->discount;

                    $transaction->total                    = '-'.$request->total_amount;
                    $transaction->status                   = 'Success';
                    $transaction->tr_time                  = $request->tr_time;
                    $transaction->save();
                    
                    
                    $subject   = "Topup done Successfully";
                    $subheader = "Congratulations! Topup done successfully";
                    $date    = date("m-d-Y h:i");
                    $message = 'Your topup of amount '.$data->requestedAmount.' successfully done.';
                    $this->helper->sendTransNotification($subject,$message,$user_id);
                    $datanotice1= array('title'=>$subject,'content'=>$message,'type'=>'push','content_type'=>'exchange','user'=>$user_id,'sub_header'=>$subheader,'push_date'=>request('local_tran_time'));
                	DB::table('noticeboard')->insert($datanotice1);
    	
    	
                    
                    // $userdevices      = DB::table('devices')->where(['user_id' => $request->user_id])->first();
                    // if(isset($userdevices) && $userdevices->fcm_token)
                    // {
                    //     $msg= 'Your topup of amount '.$data->requestedAmount.' successfully done.';
                    //     $notifyData   = array (
                    // 	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
                    // 	'title'         => 'Topup done Successfully!',
                    // 	'content'       => $msg,
                    // 	'type'          => 'Message',
                    //     'payload'       => array (//'post' => $data->created_at
                    // 		)
                    // 	);
                    // 	$datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
                    // 	DB::table('noticeboard')->insert($datanotice);
                    // //	$this->helper->sendFCMPush($notifyData);
                    // }
                    
                    
                    $wallet            = new Wallet();
                    $wallets           = $wallet->getAvailableBalance($request->user_id);
                    
                    return response()->json([
                    'status'                => $this->successStatus,
                    'data'                  => $data, 
                    'balance'               => $wallets
                    ]);
                } else {
                    return response()->json([
                        'status'                => $this->unauthorisedStatus,
                        'message'               => "Recharge was not successfull. Please try again."
                    ]);
                }
            }else{
                return response()->json([
                    'status'                => $this->unauthorisedStatus,
                    'message'               => "Insufficient Fund."
                ]);
            }
        }else{
            return response()->json([
                'status'                => $this->unauthorisedStatus,
                'message'               => "Wallet not exist."
            ]);
        }

    }
    
    public function kycCountries()
    {
        $data = DB::table('countries')->whereIn('id', [533,312,92,28,850,474,896,895])->get();
        return response()->json([
            'status'                => $this->successStatus,
            'countries'             => $data
        ]);
    }
    
     /*********************TOP UP FUNCTION END***********************/


// $url = "/topups";
//         //echo $url; die;
//         $method = "POST";
//         $data = $this->run_curl($url, $fields, $method, true);
//         return response()->json([
//             'status'                => $this->successStatus,
//             'data'                  => $data
//         ]);
 //   }

    
     /*********************TOP UP FUNCTION END***********************/
}
