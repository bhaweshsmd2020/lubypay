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
use App\Models\Setting;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class DingController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unverifiedUser     = 201;
    public $inactiveUser       = 501;
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    protected $settings;
    
    public function __construct()
    {
        $this->helper         = new Common();
        $this->user           = new User();
        $this->settings       =  Setting::where('type', 'ding')->pluck('value', 'name')->toArray();
        $this->main_url       = $this->settings['ding_main_url']; // Main BASE URL
        $this->api_key        = $this->settings['ding_api_key'];
    }
    
    public function getFunction($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->main_url.$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
           'api_key: '.$this->api_key,
        ]);
        
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        return $response;
    }
    
    public function getOperator(Request $request)
    {
        $phone    = $request->phone;
        $prefix   = $request->carrierCode;
        $country  = $request->defaultCountry;
        $completephone = $prefix.$phone;
        
        $url = "/api/V1/GetProviders?countryIsos=$country";
        $data = $this->getFunction($url);
        
        if(empty($data['Items'])){
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => $data['ErrorCodes'][0]['Code']??'Something Went Wrong!',
                'data'    => null
            ]);
        }else{
            foreach($data['Items'] as $key=>$res)
            {
                if(isset($res['LogoUrl']) && $res['LogoUrl'] !='')
                {
                    $logo = $res['LogoUrl'];
                }else
                {
                    $logo = '';
                }
                $providerPlans = $this->getPlansByProviderCode($res['ProviderCode'],$country);
                $decodePlans   = json_decode(json_encode($providerPlans),true);
                $value[] = array(
                    'name' => $res['Name'],
                    'operator_id' => $res['ProviderCode'],
                    'logo' => $logo,
                    'plans' => $decodePlans['original']
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
    
    public function getPlansByProviderCode($code,$countryIso)
    {
        if ($code=='')
        {
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Provider Code can not be empty!',
                'data'    => null,
           ]);
        }else
        {
            $url = '/api/V1/GetProducts?countryIsos='.$countryIso.'&providerCodes='.$code;
            $data = $this->getFunction($url);
            if(empty($data['Items'])){
                return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => $data['ErrorCodes'][0]['Code']??'Something Went Wrong!',
                    'data'    => null,
                ]);
            }else{
                $ProviderCode = [];
                if(count($data['Items']) < 2)
                {
                    $response['type'] = 'free_range';
                    $response['description'] = 'you can enter the amount from  '.number_format($data['Items'][0]['Minimum']['SendValue'], 2).'USD to '.number_format($data['Items'][0]['Maximum']['SendValue'], 2).' USD';
                    foreach($data['Items'] as $key => $plan)
                    {
                       
                        $value[] = array(
                        'fixed_amount' => number_format($plan['Maximum']['SendValue'], 2),
                        'local_amount' => $plan['Maximum']['ReceiveValue'],
                        'UatNumber'    => $plan['UatNumber'],
                        'SkuCode'      => $plan['SkuCode'],
                        'destination_currency_code'  => $plan['Maximum']['ReceiveCurrencyIso'],
                        'sender_currency_code'       => $plan['Maximum']['SendCurrencyIso'],
                        'providerCode'               => $plan['ProviderCode']??'',
                        'maxiMum'               => number_format($plan['Maximum']['SendValue'], 2)??'',
                        'miniMum'               => number_format($plan['Minimum']['SendValue'], 2)??''
                        );
                    }
                }else{
                    foreach($data['Items'] as $key => $plan)
                    {  
                        $value[] = array(
                            'fixed_amount' => number_format($plan['Maximum']['SendValue'], 2),
                            'local_amount' => $plan['Maximum']['ReceiveValue'],
                            'UatNumber'    => $plan['UatNumber'],
                            'SkuCode'      => $plan['SkuCode'],
                            'destination_currency_code'  => $plan['Maximum']['ReceiveCurrencyIso'],
                            'sender_currency_code'       => $plan['Maximum']['SendCurrencyIso'],
                            'providerCode'               => $plan['ProviderCode']??'',
                            'maxiMum'               => number_format($plan['Maximum']['SendValue'], 2)??'',
                            'miniMum'               => number_format($plan['Minimum']['SendValue'], 2)??''
                        );
                    }
                    $response['type'] = 'denomination';
                    $response['description'] = 'you can select any amount from these plans';
                }
            }
            
            $response['items'] = $value;
            
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Operator Plans fetched successfully.',
                'data'    => $response,
            ]);
        }
    }
    
    public function getoperatorplan(Request $request)
    {
        $rules = array(
            'operator_id' => 'required',
            'default_country'  => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => $errors,
                'data'    => null,
            ]);
        }else{
            $operator_id = $request->operator_id;
            $default_country = $request->default_country;
            
            $url = '/api/V1/GetProducts?countryIsos='.$default_country.'&providerCodes='.$operator_id;
            
            $data = $this->getFunction($url);
            if(empty($data['Items'])){
                return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => $data['ErrorCodes'][0]['Code']??'Something Went Wrong!',
                    'data'    => null,
                ]);
            }else{
                foreach($data['Items'] as $key => $plan)
                {   
                    $value[] = array(
                        'fixed_amount' => number_format($plan['Maximum']['SendValue'], 2),
                        'local_amount' => $plan['Maximum']['ReceiveValue'],
                        'UatNumber'    => $plan['UatNumber'],
                        'SkuCode'      => $plan['SkuCode'],
                        'destination_currency_code'  => $plan['Maximum']['ReceiveCurrencyIso'],
                        'sender_currency_code'       => $plan['Maximum']['SendCurrencyIso'],
                    );
                }
            }
            $response['operator_id'] = $request->operator_id;
            $response['plans'] = $value;
            
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Operator Plans fetched successfully.',
                'data'    => $response,
            ]);
        }
    }
    
    public function makerecharge(Request $request)
    {
       $rules = array(
            'operator_id'           => 'required',
            'mobile'                => 'required',
            'amount'                => 'required',
            'defaultcountry'        => 'required',
            'SkuCode'               => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => $errors,
                'data'    => null,
            ]);
        }else{
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
                try{
                    
                    $url = "/api/V1/SendTransfer";
                    
                    $params = [
                        "SkuCode"        => $request->SkuCode,
                        "SendValue"      => $request->amount,
                        "AccountNumber"  => $request->mobile,
                        "DistributorRef" => "LP-".rand(00000,99999),
                        "ValidateOnly"   => false,
                        "SendCurrencyIso"=> 'USD'
                    ];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->main_url.$url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                       "Content-Type: application/json",
                       'api_key: '.$this->api_key,
                    ]);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 80);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $result = curl_exec($ch);
                    $data = json_decode($result, true);
                    if(empty($data['ErrorCodes'])){
                        date_default_timezone_set("Asia/Calcutta");
                        $tr_time = Carbon::now()->format('d M Y h:i A');
                        
                        $transaction                           = new Transaction();
                        $transaction->user_id                  = $request->user_id;
                        $transaction->phone                    = $phone;
                        $transaction->end_user_id              = null;
                        $transaction->currency_id              = $currency_id;
                        $transaction->uuid                     = strtoupper(uniqid());
                        $transaction->transaction_reference_id = $data['TransferRecord']['TransferId']['TransferRef'];
                        $transaction->transaction_type_id      = 15;
                        $transaction->user_type                = 'registered';
                        $transaction->subtotal                 = $new_amount;
                        $transaction->percentage               = $feesDetailsforTop->charge_percentage;
                        $transaction->charge_percentage        = $new_amount*$feesDetailsforTop->charge_percentage/100;
                        $transaction->charge_fixed             = $feesDetailsforTop->charge_fixed;
                        $transaction->total                    = '-'.$new_total;
                        $transaction->note                     = null;
                        $transaction->status                   = "Success";
                        $transaction->service_provider_name    = "DingConnect";
                        $transaction->local_tran_time          = $request->local_tran_time;
                        $transaction->save();
                        
                        $value['tr_amount'] = $new_amount;
                        $value['tr_fee'] = $new_fee;
                        $value['tr_total'] = number_format($new_total,2);
                        $value['tr_currency'] = $currency_new->code;
                        $value['tr_id'] = $data['TransferRecord']['TransferId']['TransferRef'];
                        $value['tr_time'] = $tr_time;
                       
                        Wallet::where('id', $wallet)->update([
                            'balance' => $current_balance->balance - $new_total,
                        ]);
                           
                        $date = date("m-d-Y h:i");
                        $type = "mobile_reload";
                        $currency = "9";
                        
                        $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                        $template = NotificationTemplate::where('temp_id', '7')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $msg = str_replace('{amount}', number_format($request->amount, 2, '.', ','), $message);
                        
                        $this->helper->sendFirabasePush($subject, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => $transaction->id,
                            'title' => $subject,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'mobile_reload',
                            'user' => $request->user_id,
                            'sub_header' => $subheader,
                            'push_date' => $request->local_tran_time,
                            'template' => '7',
                            'language' => $userdevice->language,
                            'currency' => 'USD',
                            'amount' => number_format($request->amount, 2, '.', ',')
                        ]);
                        
                        $adminAllowed = Notification::has_permission([1]);
                        $name = User::where('id',$request->user_id)->first();
        
                        foreach($adminAllowed as $admin){
                            Notification::insert([
                                'user_id'               => $request->user_id,
                                'notification_to'       => $admin->agent_id,
                                'notification_type_id'  => 14,
                                'notification_type'     => 'App',
                                'description'           => 'User '.$name->first_name.' '.$name->last_name.' has done mobile reload of '.$this->helper->getcurrencyCode($currency_id).' '.$request->amount,
                                'url_to_go'             => 'admin/transactions/edit/'.$transaction->id
                            ]);
                        }
                        
                        $mailresponse = $this->helper->sendTransactionNotificationToAdmin('mobile_reload', ['data' => $transaction]);
                       
                        return response()->json([
                            'status'  => $this->successStatus,
                            'message' => 'Topup successfully done.',
                            'data'    => $value,
                        ]);
                    } else {
                        return response()->json([
                            'status'  => $this->unauthorisedStatus,
                            'message' => 'Recharge was not successfull.',
                            'data'    => null
                        ]);
                    }
                }catch (\Exception $e) {
                    return response()->json([
                        'status'  => $this->unauthorisedStatus,
                        'message' => $e->getMessage(),
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
    }
   
    /***********************RUN CURL Function START***************************/
    public function run_curl ($url, $fields=null, $method, $header = false, $auth = false) {
       // dd($this->api_key);
        $ch = curl_init();
        if($auth == false) {
        $url = $this->main_url . $url;
        }
        //echo $url; die;
        if($header == true) {
            
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","api_key: ".$this->api_key)); // Live Token
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
        // print_r($result);
        
        //close connection
        curl_close($ch);
         \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('response  method :'.$method.' run CURL '.$result);
       return json_decode($result);
    }
    
    public function estimatePrice ($url, $fields=null, $method, $header = false, $auth = false) {
       
        $ch = curl_init();
        if($auth == false) {
        $url = $this->main_url . $url;
        }
        //echo $url; die;
        if($header == true) {
            
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","api_key: ".$this->api_key)); // Live Token
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
        // print_r($result);
        
        //close connection
        curl_close($ch);
         \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('response  method :'.$method.' run CURL '.$result);
       return json_decode($result);
    }
    /*********************Start new flow~DingConnect***********************/
    public function getProductByNumber(Request $request)
    {
        $rules = array(
                'accountNumber' => 'required',
            );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
             $errors = $validator->errors();
             return response()->json([
            'status'  => $this->unauthorisedStatus,
            'message' => $errors,
            'data'    => null,
           ]);
        }else
        {
        \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('request for the getting plans : getoperatorplan '.json_encode($request->all()));
        $operator_id = $request->operator_id;
        $fields =json_encode(
                array(
                )
            );
        $url = '/api/V1/GetProducts?accountNumber='.$request->accountNumber;
         \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('request URL : getoperatorplan '.$url);
        $method ='GET';
        $result = $this->run_curl($url, $fields, $method, true, false);
        \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('response from the CURl to function : getoperatorplan '.json_encode($result));
        $data = json_decode(json_encode($result),true);
        //dd($data);
        $value=[];
        if(empty($data['Items'])){
          
             return response()->json([
            'status'  => $this->unauthorisedStatus,
            'message' => $data['ErrorCodes'][0]['Code']??'Something Went Wrong!',
            'data'    => null,
           ]);
                
        }else{
            $ProviderCode = [];
             if(count($data['Items']) < 2)
             {
                
                 $response['type'] = 'free_range';
                 $response['description'] = 'you can enter the amount in this range '.number_format($data['Items'][0]['Minimum']['SendValue'], 2).' and '.number_format($data['Items'][0]['Maximum']['SendValue'], 2);
                 foreach($data['Items'] as $key => $plan)
                    {
                       
                        $value[] = array(
                        'fixed_amount' => number_format($plan['Maximum']['SendValue'], 2),
                        'local_amount' => $plan['Maximum']['ReceiveValue'],
                        'UatNumber'    => $plan['UatNumber'],
                        'SkuCode'      => $plan['SkuCode'],
                        'destination_currency_code'  => $plan['Maximum']['ReceiveCurrencyIso'],
                        'sender_currency_code'       => $plan['Maximum']['SendCurrencyIso'],
                        'providerCode'               => $plan['ProviderCode']??''
                        );
                     
                    }
             }else
             {
                foreach($data['Items'] as $key => $plan)
                {   if(!in_array($plan['ProviderCode'],$ProviderCode))
                    {
                        $operatorDetails = $this->getProviderByProviderCode($plan['ProviderCode']);
                        array_push($ProviderCode,$plan['ProviderCode']);
                    }
                     $value[] = array(
                    'fixed_amount' => number_format($plan['Maximum']['SendValue'], 2),
                    'local_amount' => $plan['Maximum']['ReceiveValue'],
                    'UatNumber'    => $plan['UatNumber'],
                    'SkuCode'      => $plan['SkuCode'],
                    'destination_currency_code'  => $plan['Maximum']['ReceiveCurrencyIso'],
                    'sender_currency_code'       => $plan['Maximum']['SendCurrencyIso'],
                    'providerCode'               => $operatorDetails??''
                    );
                 
                }
                $response['type'] = 'denomination';
                $response['description'] = 'you can select any amount from these plans';
            }
            
        }
        // $providerCode = array_column($value , 'providerCode');
        // $uniqueCode   = array_unique($providerCode, SORT_REGULAR);
        // $operatorDetails = $this->getProviderByProviderCode($uniqueCode);
        // $response['operatorDetails'] = $operatorDetails;
        $response['plans'] = $value;
        
        return response()->json([
            'status'  => $this->successStatus,
            'message' => 'Operator Plans fetched successfully.',
            'data'    => $response,
        ]);
        
        }
        
    }
    
    public function getProviderByProviderCode($providerCode)
    {
        if(empty($providerCode)){
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Please send provider code!',
                'data'    => null
            ]);
            
        }else{
            $fields = [];
            $url = "/api/V1/GetProviders?providerCodes=$providerCode";
            $method = "GET";
            $curlResponse = $this->run_curl($url, $fields, $method, true, false);
            // foreach($providerCode as $code)
            // {
            //     $url = "/api/V1/GetProviders?providerCodes=$code";
            //     $method = "GET";
            //     $curlResponse[] = $this->run_curl($url, $fields, $method, true, false);
            // }
            return $curlResponse;   
        }
      
    }
    
    public function getEstimatedPrice(Request $request)
    {
        $rules = array(
                'SkuCode'       => 'required',
                'SendValue'     => 'required',
                'user_id'       => 'required'
            );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->error('request for the makerecharge : makerecharge '.json_encode($request->all()));
             $errors = $validator->errors();
             return response()->json([
            'status'  => $this->unauthorisedStatus,
            'message' => $errors,
            'data'    => null,
           ]);
        }else
        {
       
            try{
            
            $jsonResult[] = [
                'SkuCode' => $request->SkuCode,
                'SendValue' => $request->SendValue,
                'BatchItemRef' => $request->SkuCode
            ];
            $fields = json_encode($jsonResult);
         
            \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('request for the : estimatedPrice :customerId'.$request->user_id.'fields : '.$fields);
            $url = "/api/V1/EstimatePrices";
            $method = "POST";
            $data1 = $this->estimatePrice($url, $fields, $method, true);
            $data = json_decode(json_encode($data1),true);
            // print_r($data);die;
             \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->info('response for the curl : estimatedPrice :customerId'.$request->user_id.'fields : '.json_encode($data));
            if(empty($data['Items'][0]['ErrorCodes'])){
                 return response()->json([
                    'status'  => $this->successStatus,
                    'message' => 'Here is the estimated price of this plan.',
                    'data'    => $data,
                ]);
            } else {
                
                 return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => $data['Items'][0]['ErrorCodes'][0]['Code']??'Something Went Wrong!',
                    'data'    => null
                ]);
            }
            }catch(\Exception $e)
            {
                  \Illuminate\Support\Facades\Log::channel('dingConnectAPI')->error('Something happened with customer check ' .$request->user_id.'error : '. $e->getMessage() . ', ' . $e->getFile() . ', ' . $e->getLine());
                return response()->json([
                    'status'  => $this->unauthorisedStatus,
                    'message' => $e->getMessage(),
                    'data'    => null
                ]);
            }
        }
      
    }
      /*********************End new flow***********************/
      
    
      
    
    
    
    
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
    //      DB::table('noticeboard')->insert($datanotice1);
            
    //         // $userdevices               = DB::table('devices')->where(['user_id' => $request->user_id])->first();
    //         //  if(isset($userdevices) && $userdevices->fcm_token)
    //         // {
    //         //     $msg= 'Your topup of amount '.$data->requestedAmount.' successfully done.';
                
    //         //   //echo "<pre>"; print_r($userdevices); die;
    //         //   $notifyData   = array (
    //         //   'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
    //         //   'title'         => 'Topup done Successfully!',
    //         //   'content'       => $msg,
    //         //   'type'          => 'Message',
    //         //   // Require for auto fetch incoming request push.
    //         //   'payload'       => array (//'post' => $data->created_at
    //         //       )
    //         //   );
    //         //   $datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
    //         //   DB::table('noticeboard')->insert($datanotice);
    //         //   $this->helper->sendFCMPush($notifyData);
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
    //              'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
    //              'title'         => 'Topup done Successfully!',
    //              'content'       => $msg,
    //              'type'          => 'Message',
    //                 'payload'       => array (//'post' => $data->created_at
    //                  )
    //              );
    //              $datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
    //              DB::table('noticeboard')->insert($datanotice);
    //             //   $this->helper->sendFCMPush($notifyData);
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
                    $adminAllowed = Notification::has_permission([1]);
                                    $name = User::where('id',$user_id)->first();

                            foreach($adminAllowed as $admin){
                                Notification::insert([
                                    'user_id'               => $user_id,
                                    'notification_to'       => $admin->agent_id,
                                    'notification_type_id'  => 14,
                                    'notification_type'     => 'App',
                                    'description'           => 'User '.$name->first_name.' '.$name->last_name.' has Top of '.$this->helper->getcurrencyCode( $request->currency_id).' '.$data->requestedAmount,
                                    'url_to_go'             => 'admin/transactions/edit/'.$transaction->id
                                ]);
                            }
        
                    $subject   = "Mobile reload done successfully!";
                    $subheader = "Congratulations! Your mobile reload has done successfully";
                    $type="mobile_reload";
                    $mailresponse = $this->helper->sendTransactionNotificationToAdmin('mobile_reload', ['data' => $transaction]);
                    $this->helper->sendFirabasePush($subject,$subheader,$request->user_id, $request->currency_id, $type);
                    
                    
                    
                    // $userdevices      = DB::table('devices')->where(['user_id' => $request->user_id])->first();
                    // if(isset($userdevices) && $userdevices->fcm_token)
                    // {
                    //     $msg= 'Your topup of amount '.$data->requestedAmount.' successfully done.';
                    //     $notifyData   = array (
                    //  'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
                    //  'title'         => 'Topup done Successfully!',
                    //  'content'       => $msg,
                    //  'type'          => 'Message',
                    //     'payload'       => array (//'post' => $data->created_at
                    //      )
                    //  );
                    //  $datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
                    //  DB::table('noticeboard')->insert($datanotice);
                    // //   $this->helper->sendFCMPush($notifyData);
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
