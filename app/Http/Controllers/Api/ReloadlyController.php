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
use App\Models\Country;
use App\Models\UserDetail;

class ReloadlyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unverifiedUser     = 201;
    public $inactiveUser       = 501;
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->main_url       = "https://topups.reloadly.com";
        $this->helper         = new Common();
        $this->user           = new User();
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
    
    public function getCountries()
    { 
        $data = DB::table('countries')->where('status',1)->select('short_name AS isoName','name AS name','currency_code AS currencyCode','flag AS flag', 'id', 'automatic_kyc', 'manual_kyc')->orderBy('name', 'ASC')->get();
        foreach ($data as $value) {
          $value->callingCodes[] = '+'.DB::table('countries')->where('short_name',$value->isoName)->first()->phone_code;
        }
        foreach ($data as $value1) {
           $value2 = DB::table('countries')->where('short_name','=',$value1->isoName)->first()->short_name;
           $value1->path=url('public/img/flags/');
           $value3 = 'https://s3.amazonaws.com/rld-flags/'.strtolower($value2).'.svg';
           $value1->flag_new= $value3;
        }
        return response()->json($data);
    }
    
    public function kycCountries(Request $request)
    { 
        $user_id = $request->user_id;
        $user = UserDetail::where('user_id', $user_id)->first();
        $data = Country::where('id', $user->country)->first();
        return response()->json($data);
    }
    
    public function getallCountries()
    {
        $data = DB::table('countries')->select('short_name AS isoName','name AS name','currency_code AS currencyCode','flag AS flag', 'id')->orderBy('name', 'ASC')->get();
        foreach ($data as $value) {
          $value->callingCodes[] = '+'.DB::table('countries')->where('short_name',$value->isoName)->first()->phone_code;
        }
        foreach ($data as $value1) {
           $value2 = DB::table('countries')->where('short_name','=',$value1->isoName)->first()->short_name;
           $value1->path=url('public/img/flags/');
           $value3 = 'http://api.hostip.info/images/flags/'.strtolower($value2).'.gif';
           $value1->flag_new= $value3;
        }
        return response()->json($data);
    }
    /***********************GET Countries Function END***************************/
    /*********************GET Operators By ISO Function Start***********************/
    public function getOperatorsByIso(Request $request)
    {
        //dd($request->all());
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
        //dd($request->all());
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
    public function topUp(Request $request)
    {
        //dd($request->all());
        $phone = $request->phone;
        $country = $request->country;
        $amount = $request->amount;
        $operator_id = $request->operator_id;
        $this->generateToken();
        
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
        //echo $url; die;
        $method = "POST";
        $data = $this->run_curl($url, $fields, $method, true);
        //dd($data);
        if($data && @$data->errorCode) {
         return response()->json([
                    'status'                => $this->unauthorisedStatus,
                    'message'               => $data->message
                ]);
        }
        if($data && $data->transactionId) {
            
            // USER WALLET
            $current_balance = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();
            // dd($current_balance);

            Wallet::where([
                'user_id'     => $request->user_id,
                'currency_id' => $request->currency_id,
            ])->update([
                'balance' => $current_balance->balance - $request->amount,
            ]);
            
          // USER WALLET
           
          //Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $request->user_id;
            $transaction->end_user_id              = null;
            $transaction->currency_id              = $request->currency_id;
            $transaction->uuid                     = strtoupper(uniqid());
            $transaction->transaction_reference_id = $data->transactionId;
            $transaction->transaction_type_id      = 15;
            $transaction->user_type                = 'registered';
            $transaction->subtotal          = $data->requestedAmount - $data->discount;
            $transaction->percentage        = 0;
            $transaction->charge_percentage = 0;
            $transaction->charge_fixed      = $data->discount;
            $transaction->total             = '-'.$data->requestedAmount;
            $transaction->note              = null;
            $transaction->status            = "Success";
            $transaction->save();
            
            $userdevices      = DB::table('devices')->where(['user_id' => $request->user_id])->first();
             if(isset($userdevices) && $userdevices->fcm_token)
            {
                $msg= 'Your topup of amount '.$data->requestedAmount.' successfully done.';
                $notifyData   = array (
            	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
            	'title'         => 'Topup done Successfully!',
            	'content'       => $msg,
            	'type'          => 'Message',
                'payload'       => array (//'post' => $data->created_at
            		)
            	);
            	$datanotice= array('title'=>'Topup done Successfully','content'=>$msg,'type'=>'push','content_type'=>'topup','user'=>$request->user_id);
            	DB::table('noticeboard')->insert($datanotice);
            //	$this->helper->sendFCMPush($notifyData);
            }
            
            
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
