<?php

namespace App\Http\Controllers\Api;

use App\DataTables\Admin\InsuranceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Insurance;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\FeesLimit;
use App\Models\PayoutSetting;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalDetail;
use DB;
use App\Models\Notification;
use App\Models\User;
use App\Models\PendingTransaction;
use App\Models\GiftCard;
use App\Models\Setting;
use App\Models\Currency;
use App\Http\Controllers\Users\EmailController;
use App\Models\TransDeviceInfo;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class GiftCardController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    protected $helper;
    protected $settings;
    public $email;

    public function __construct()
    {
        $this->settings =  Setting::where('type', 'giftcard')->pluck('value', 'name')->toArray();
        $this->main_url = $this->settings['main_url']; // Main BASE URL
        $this->helper   = new Common();
        $this->email    = new EmailController();
    }
    
    public function generateToken() {
        $url = "https://auth.reloadly.com/oauth/token";

        $fields =json_encode(
                 array( 
                	"client_id"=>$this->settings['client_id']??'',
                	"client_secret"=>$this->settings['client_secret']??'',
                	"grant_type"=>"client_credentials",
                	"audience"=>$this->settings['main_url']??''
                 )
             );

        $method = "POST";
        $data = $this->run_curl($url, $fields, $method, false, true);
        $this->my_token = $data->access_token;
    }
    
    /*********************Generate Token Function END***************************/
    /***********************RUN CURL Function START***************************/
    public function run_curl ($url, $fields, $method, $header = false, $auth = false) {
        $ch = curl_init();
        if($auth == false) {
        $url = $this->main_url . $url;
        }
        if($header == true) {
            
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Accept: application/com.giftcards.topups-v1+json","Authorization: Bearer ".$this->my_token)); // Live Token
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
    public function getCountries(Request $request)
    {
        $this->generateToken();
        $url = "/countries";
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->main_url.'/countries',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->my_token,
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        echo $response;
    }


     public function UserGiftList(Request $request)
    {     
        $user_id= $request->user_id; 
        if( $user_id) { 
        $product= GiftCard::where('user_id',$user_id)->latest()->get(); 
       
        $data = $this->paginate($product);        
            return response()->json([
            'message'=>'use Gift Card lists',
            'product'=>$data,
            'status'=>200
        ]);
        }else{
        return response()->json([
            'message'=>'error',
            'status'=>400
        ]);
      }
    }   
    
    public function allgiftcard(Request $request)
    {   
        
        $this->generateToken();
        $iso = $request->iso;
        $products = $request->product;
        $page = $request->page;
        $curl = curl_init();
       
        curl_setopt_array($curl, [
        	CURLOPT_URL => $this->main_url."/products?size=100&productName=$products&countryCode=$iso&includeRange=true&includeFixed=true",
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => "",
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 30,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => "GET",
        	CURLOPT_HTTPHEADER => [
        		"Accept: application/com.reloadly.giftcards-v1+json",
        		"Authorization: Bearer ".$this->my_token ],
        ]);
        
        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($result);
        $contents = $response->content;
        // dd($response);
        $i = 0;
        $product=array();
        foreach ($contents as $content){
            $product[$i]['productId']                           = $content->productId;
            $product[$i]['productName']                         = $content->productName;
            // $product[$i]['global']                              = $content->global;
            // $product[$i]['senderFee']                           = $content->senderFee;
            // $product[$i]['senderFeePercentage']                 = $content->senderFeePercentage;
            // $product[$i]['discountPercentage']                  = $content->discountPercentage;
            // $product[$i]['denominationType']                    = $content->denominationType;
            // $product[$i]['recipientCurrencyCode']               = $content->recipientCurrencyCode;
            // $product[$i]['minRecipientDenomination']            = $content->minRecipientDenomination;
            // $product[$i]['senderCurrencyCode']                  = $content->senderCurrencyCode;
            // $product[$i]['minSenderDenomination']               = $content->minSenderDenomination;
            // $product[$i]['maxSenderDenomination']               = $content->maxSenderDenomination;
            // $product[$i]['fixedRecipientDenominations']         = $content->fixedRecipientDenominations;
            // $product[$i]['fixedSenderDenominations']            = $content->fixedSenderDenominations;
            $product[$i]['logoUrls']                            = $content->logoUrls;
            // $product[$i]['brandName']                           = $content->brandName;
            // $product[$i]['redeemInstruction']                   = $content->redeemInstruction;
            // $product[$i]['maxSenderDenomination']               = $content->maxSenderDenomination;
             $i++;
        }
        $data = $this->paginate($product);        
            return response()->json([
            'message'=>'Gift Cards',
            'code'=>200,
            'product'=>$data,
            'status'=>'success'
        ]);
        
    }
    
    public function giftcarddetails(Request $request)
    {  
        $this->generateToken();
        $id = $request->id;
        $curl = curl_init();
        
        curl_setopt_array($curl, [
        	CURLOPT_URL => $this->main_url."/products/$id",
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => "",
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 30,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => "GET",
        	CURLOPT_HTTPHEADER => [
        		"Accept: application/com.reloadly.giftcards-v1+json",
        		"Authorization: Bearer ".$this->my_token ,
        	],
        ]);
        
        $response = curl_exec($curl);
        
        $product_details = json_decode($response);
        // dd($product_details);
        $fixedRecipientDenominations=$product_details->fixedRecipientDenominations;
        $denominationType=$product_details->denominationType;
        $err = curl_error($curl);
        
        curl_close($curl);
        return response()->json([
            'message'=>'Gift Cards coupon',
            'code'=>200,
            'product_details'=>$product_details,
            'status'=>'success'
        ]);

    }
    
    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function order_gift_card(Request $request)
    {
        try{
            $product_id = $request->productId;
            $countryCode = $request->countryCode;
            $quantity = $request->quantity;
            $unitPrice = $request->unitPrice;
            $identifier = $request->customIdentifier;
            $senderName = $request->senderName;
            $recipientEmail = $request->recipientEmail;
            $fixedSenderDenominations = $request->fixedSenderDenominations;
            $senderFee = $request->senderFee;
            $amount=($fixedSenderDenominations+$senderFee)*$quantity;
            $subtotal=$fixedSenderDenominations*$quantity;
            $charges=$senderFee*$quantity;
            $fromWallet=9;
            $toWallet=$request->currency_id;
            $total_amount = $this->getCurrenciesExchangeRate($fromWallet, $toWallet, $amount);
            
            $feesDetails = FeesLimit::where(['currency_id' => $toWallet, 'transaction_type_id' => 32])->first(['transaction_type_id','charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
            $percentage = $feesDetails->charge_percentage;
            $charge_percentage  = ($total_amount / 100)*$percentage;
            $tot_fees = ($total_amount / 100) * $percentage+ $feesDetails->charge_fixed;
            $totalWithFee = $total_amount + $tot_fees;
    
            $wallet = Wallet::where(['currency_id' => $request->currency_id, 'user_id' => $request->user_id])->first();
            if(empty($wallet))
            {
                return response()->json([
                    'message' => 'Wallet not found!',
                    'status' => $this->unauthorisedStatus
                ]);
            }
            
            if ($totalWithFee > $wallet->balance)
            {
                return response()->json([
                    'message' => 'Insufficient fund in Wallet!',
                    'status' => $this->unauthorisedStatus
                ]);
            }
            
            $availableBalancereloadly = $this->getBalance();
            
            if($amount > $availableBalancereloadly->balance){
                return response()->json([
                    'message' => 'Insufficient fund in Reloadly!',
                    'status' => $this->unauthorisedStatus
                ]);
            }
            
            $current_balance = Wallet::where('user_id',$request->user_id)->where('currency_id',$request->currency_id)->first()??'';
            $currency_details=Currency::where('id',$request->currency_id)->first();
            $currency_code=$currency_details->code??'';
            $name = User::where('id', $request->user_id)->first();
            $postfield = [
                'productId'=> $product_id,
                'countryCode'=> $countryCode,
                'quantity'=> $quantity,
                'unitPrice'=> $unitPrice,
                'customIdentifier'=> $identifier,
                'senderName' => $name->first_name.' '.$name->last_name,
                'recipientEmail'=>$recipientEmail,
            ];
         
            $this->generateToken();
            $curl = curl_init();
            curl_setopt_array($curl, [
            	CURLOPT_URL => $this->main_url."/orders",
            	CURLOPT_RETURNTRANSFER => true,
            	CURLOPT_ENCODING => "",
            	CURLOPT_MAXREDIRS => 10,
            	CURLOPT_TIMEOUT => 30,
            	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            	CURLOPT_CUSTOMREQUEST => "POST",
            	CURLOPT_POSTFIELDS => json_encode($postfield),
            	CURLOPT_HTTPHEADER => [
            		"Accept: application/com.reloadly.giftcards-v1+json",
            		"Authorization: Bearer ".$this->my_token ,
            		"Content-Type: application/json",
                    "cache-control: no-cache"
            	],
            ]);        
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);  
        
            if ($err) {
                return response()->json([
                    'message'=>'error',
                    'orderdetails'=>json_decode($err),
                    'status'=>400
                ]);
            } else {
                $order_details = json_decode($response);
    
                if(!empty($order_details->status) && $order_details->status == "SUCCESSFUL")
                {             
                    $transactionId=  $order_details->transactionId;
                    $subtotal=$this->getCurrenciesExchangeRate($fromWallet,$toWallet,$subtotal);
        
                    $giftcode=$this->getGiftCode($transactionId);
        
                    $giftcard  = new GiftCard ; 
                    $giftcard->transaction_id=$order_details->transactionId;
                    $giftcard->recipient_email=$order_details->recipientEmail;
                    $giftcard->product_id=$order_details->product->productId;
                    $giftcard->quantity=$order_details->product->quantity;
                    $giftcard->gift_card_number=$giftcode[0]->cardNumber??'';          
                    $giftcard->gift_pin_code=$giftcode[0]->pinCode??'';
                    $giftcard->product_unit_price=$order_details->product->unitPrice;
                    $giftcard->product_total_price=$order_details->product->totalPrice;
                    $giftcard->brand_id=$order_details->product->brand->brandId;
                    $giftcard->brand_name=$order_details->product->brand->brandName;
                    $giftcard->amount = $totalWithFee;
                    $giftcard->per_unit_fee=$senderFee;
                    $giftcard->product_logo=$request->logoUrls;
                    $giftcard->currency_code=$order_details->product->currencyCode;
                    $giftcard->product_name=$order_details->product->productName;
                    $giftcard->country_id='';
                    $giftcard->user_id=$request->user_id;
                    $giftcard->card_number='';
                    $giftcard->card_expiry=''; 
                    $giftcard->card_cvv=''; 
                    $giftcard->currency_id=$request->currency_id;
                    $giftcard->issue_date='';
                    $giftcard->validity='';
                    $giftcard->status=$order_details->status;
                    $giftcard->local_tran_time          = $request->local_tran_time;
                    $giftcard->ip_address               = request()->ip();
                    $giftcard->save();
        
                    Wallet::where('user_id',$request->user_id)->where('currency_id',$request->currency_id)->update([
                        'balance' => $current_balance->balance - $totalWithFee,
                    ]);
        
                    $uuid                                  = unique_code();
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $request->user_id;
                    $transaction->currency_id              = $request->currency_id;
                    $transaction->uuid                     = $uuid ;
                    $transaction->transaction_reference_id = $giftcard->id;
                    $transaction->transaction_type_id      = 32;
                    $transaction->subtotal                 = $total_amount;
                    $transaction->percentage               = $feesDetails->charge_percentage;
                    $transaction->charge_percentage        = $charge_percentage;
                    $transaction->charge_fixed             = $feesDetails->charge_fixed;
                    $transaction->total                    = '-' . $totalWithFee;
                    $transaction->status                   = 'Success';
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
                    
                    $adminAllowed = Notification::has_permission([1]);
                                
                    foreach($adminAllowed as $admin){
                        Notification::insert([
                            'user_id'               => $request->user_id,
                            'notification_to'       => $admin->agent_id,
                            'notification_type_id'  => 13,
                            'notification_type'     => 'App',
                            'description'           => 'User '.$name->first_name.' '.$name->last_name.' has purchased '.$order_details->product->productName.' gift card of '.$this->helper->getcurrencyCode($request->currency_id).' '.$totalWithFee,
                            'url_to_go'             => 'admin/transactions/edit/'.$transaction->id,
                            'local_tran_time'       => $request->local_tran_time
                        ]);
                    }
                                
                    $date    = date("m-d-Y h:i");
                    $type = 'gift_card';
                	
                	$userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $device_lang = $userdevice->language;
                    }else{
                        $device_lang = getDefaultLanguage();
                    }

                    $template = NotificationTemplate::where('temp_id', '6')->where('language_id', $device_lang)->first();
                    $subject = $template->title;
                    $subheader = $template->subheader;
                    $message = $template->content;
                    
                    $msg = str_replace('{currency}', $currency_details->code, $message);
                    $msg = str_replace('{amount}', number_format($totalWithFee, 2, '.', ','), $msg);
                    $msg = str_replace('{product}', $order_details->product->productName, $msg);
                    
                    $this->helper->sendFirabasePush($subject, $msg, $request->user_id, $request->currency_id, $type);
                    
                    Noticeboard::create([
                        'tr_id' => $transaction->id,
                        'title' => $subject,
                        'content' => $msg,
                        'type' => 'push',
                        'content_type' => 'gift_card',
                        'user' => $request->user_id,
                        'sub_header' => $subheader,
                        'push_date' => $request->local_tran_time,
                        'template' => '6',
                        'language' => $device_lang,
                        'currency' => $currency_details->code,
                        'amount' => number_format($totalWithFee, 2, '.', ','),
                        'product' => $order_details->product->productName
                    ]);
                            
                    $emaildetails['user']=$name->first_name.' '.$name->last_name;
                    $emaildetails['created_at']=$transaction->local_tran_time;
                    $emaildetails['uuid']=$transaction->uuid??'';
                    $emaildetails['product']=$order_details->product->productName??'';
                    $emaildetails['unit_price']=$order_details->product->unitPrice??'';
                    $emaildetails['quantity']=$order_details->product->quantity??'';
                    $emaildetails['code']=$currency_code;
                    $emaildetails['amount']=$total_amount;
                    $emaildetails['fee']=$tot_fees;
                    $emaildetails['total']=$totalWithFee;
                    $emaildetails['user_id']=$request->user_id;
                        
                    $mailresponse = $this->helper->sendTransactionNotificationToAdmin('gift_card', ['data' => $emaildetails]);
    
                    $data=json_decode($response);
                    $data->transactionCreatedTime=$request->local_tran_time;
                    
                    $user = User::where('id', $request->user_id)->first();
            	
                	$twoStepVerification = EmailTemplate::where([
                        'temp_id'     => 47,
                        'language_id' => $device_lang,
                        'type'        => 'email',
                    ])->select('subject', 'body')->first();
                   
                    $twoStepVerification_sub = $twoStepVerification->subject;
                    $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                    $twoStepVerification_msg = str_replace('{amount}', $totalWithFee, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{created_at}', request('local_tran_time'), $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{uuid}', $transaction->uuid, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{code}', $currency_details->code, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{fee}', $transaction->charge_percentage + $transaction->charge_fixed, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{giftcardbrandname}', $order_details->product->brand->brandName, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{giftcardproduct}', $order_details->product->productName, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{receiver_id}', $order_details->recipientEmail, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                    $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
                    
                    return response()->json([
                        'message' => 'success',
                        'status' => $this->successStatus,
                        'orderdetails' => $data,
                    ]);
                }else{
                    return response()->json([
                        'message' => $order_details->message,
                        'status' => $this->unauthorisedStatus,
                    ]);
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'status' => $this->unauthorisedStatus
            ]);
        }
    }
    
    public function getBalance()
    {
        $this->generateToken();
        // $url = "/https://giftcards.reloadly.com/accounts/balance";
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->main_url.'/accounts/balance',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->my_token,
          ),
        ));        
        $response = curl_exec($curl);        
        curl_close($curl);
        return json_decode($response);
    }

    public function getGiftCode($trasactionid)
    {
        $this->generateToken();
        
        // $trasactionid= $request->trasactionid;
        // $url = "/https://giftcards.reloadly.com/accounts/balance";
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->main_url."/orders/transactions/$trasactionid/cards",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->my_token,
          ),
        ));        
        $response = curl_exec($curl);        
        curl_close($curl);
        return json_decode($response);
    }


    public function getGiftCodeApi(Request $request,$trasactionid=null)
    {
        $this->generateToken();
        $trasactionid= $request->trasactionid??$trasactionid;
       
        // $trasactionid= $request->trasactionid;
        // $url = "/https://giftcards.reloadly.com/accounts/balance";
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->main_url."/orders/transactions/$trasactionid/cards",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->my_token,
          ),
        ));        
        $response = curl_exec($curl);        
        curl_close($curl);
        return json_decode($response);
    }

    // public function giftcarddetailsbyid($unitPrice=null,$id=null)
    // {  
    //     $this->generateToken();
    //     $id = $id;
    //     $curl = curl_init();        
    //     curl_setopt_array($curl, [
    //     	CURLOPT_URL => $this->main_url."/products/$id",
    //     	CURLOPT_RETURNTRANSFER => true,
    //     	CURLOPT_ENCODING => "",
    //     	CURLOPT_MAXREDIRS => 10,
    //     	CURLOPT_TIMEOUT => 30,
    //     	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     	CURLOPT_CUSTOMREQUEST => "GET",
    //     	CURLOPT_HTTPHEADER => [
    //     		"Accept: application/com.reloadly.giftcards-v1+json",
    //     		"Authorization: Bearer ".$this->my_token ,
    //     	],
    //     ]);        
    //     $response = curl_exec($curl);
        
    //     $product_details = json_decode($response);
    //     // dd($product_details);
    //     $fixedRecipientDenominations=$product_details->fixedRecipientToSenderDenominationsMap;
    //     $denominationType=$product_details->denominationType;
    //     $err = curl_error($curl);
    //     curl_close($curl);
    //     return   $fixedRecipientDenominations;

    //     // return response()->json([
    //     //     'message'=>'Gift Cards coupon',
    //     //     'code'=>200,
    //     //     'product_details'=>$product_details,
    //     //     'status'=>'success'
    //     // ]);

    // }


    public function getCurrenciesExchangeRate($fromWallet,$toWallet,$amount)
    {
        $fromWallet = $fromWallet;
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $toWallet], ['exchange_from', 'code', 'rate', 'symbol']);
        $feesDetails = FeesLimit::where(['transaction_type_id' => '32', 'currency_id' => $fromWallet])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);

        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $fromWallet], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $fromWallet], ['rate', 'symbol']);
                $exchangevalue = getCurrencyRate('usd', $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            
            $getAmountMoneyFormat             = $toWalletRate * $amount ;          
            $formattedDestinationCurrencyRate = number_format($toWalletRate,2);            
            $success['getAmountMoneyFormat']  = formatNumber($getAmountMoneyFormat);
            return number_format($getAmountMoneyFormat, 2, '.', '');
        }
    }
    
    public function getCurrenciesExchangeRateforGift(Request $request)
    {
        // dd(request()->all());
        $fromWallet =  request('fromWallet');
        $toWallet =request('toWallet');
        $amount  =request('amount');
        
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $toWallet], ['exchange_from', 'code', 'rate', 'symbol']);
        $feesDetails = FeesLimit::where(['transaction_type_id' => 32, 'currency_id' => $toWallet])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);

        // dd($toWalletCurrency); die;
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $fromWallet], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
              $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $fromWallet], ['rate', 'symbol']);
              $exchangevalue = getCurrencyRate('usd', $toWalletCurrency->code);
              $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat             = $toWalletRate * $amount ;          
            $formattedDestinationCurrencyRate = number_format($toWalletRate,2);            
            $success['status']                = 200;
            $success['toWalletRate']          = (float) $formattedDestinationCurrencyRate; // this was not necessary, but kept it as it creates confusion
            $success['toWalletRateHtml']      = (float) $formattedDestinationCurrencyRate; // this will not be shown as formatted as it creates confusion - when multiplying amount * currency rate
            $success['toWalletCode']          = $toWalletCurrency->code;
            $success['toWalletSymbol']        = $toWalletCurrency->symbol;
            $success['fromWalletSymbol']      = $fromWalletCurrency->symbol;
            $success['fee_percentage']        = $feesDetails->charge_percentage;
            $success['charge_fixed']          = $feesDetails->charge_fixed;
            $success['total_fee']             =number_format($feesDetails->charge_fixed + ($getAmountMoneyFormat*$feesDetails->charge_percentage/100),2);
            $success['total_amount']          = (request('amount')+$feesDetails->charge_fixed + (request('amount')*$feesDetails->charge_percentage/100) - ($feesDetails->charge_fixed + (request('amount')*$feesDetails->charge_percentage/100)));
            $success['getAmountMoneyFormat']  = formatNumber($getAmountMoneyFormat); //just for show, not taken for further processing
            //  dd($success);
            // return  formatNumber($getAmountMoneyFormat);
            return response()->json(['success' => $success], 200);
        }
        else
        {
            $success['status']         = 400;
            $success['toWalletRate']   = null;
            $success['toWalletCode']   = null;
            $success['toWalletSymbol'] = null;
            return response()->json(['success' => $success], 400);
        }
    }
    
    
   public function SubmitGiftCard(Request $request){

           
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
               'ip_address'          => request()->ip(),
           ];
           dd($feeInfo);

           // Check Fraud
           $pending_transaction                           = new PendingTransaction();
           $pending_transaction->user_id                  = $user_id;
           $pending_transaction->currency_id              = $arr['currency_id'];
           $pending_transaction->payment_method_id        = $arr['payment_method_id'];
           $pending_transaction->transaction_reference_id = $arr['uuid'];
           $pending_transaction->transaction_type_id      = Withdrawal;
           $pending_transaction->uuid                     = $arr['uuid'];
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
                                       'description'           => "User ".$name->first_name." has order gift card ".$this->helper->getcurrencyCode($currency_id). " ".$amount,
                                       'url_to_go'             => '/admin/withdrawals/edit/'.$response['withdrawal_id'],
                                       'local_tran_time'       => $request->local_tran_time
                                   ]);
                               }       
           
           $userdevices = DB::table('devices')->where(['user_id' => $user_id])->first();
        //    if(isset($userdevices) && $userdevices->fcm_token)
        //    {
        //        $msg= 'Your request for money withdraw of '.$amount. ' is successfull.';
        //        $notifyData   = array (
        //            'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
        //            'title'         => 'New message from TickTap Pay',
        //            'content'       => $msg,
        //            'type'          => 'Message',
        //            'payload'       => array (
        //            )
        //        );
        //        $datanotice= array('title'=>'New message from TickTap Pay','content'=>$msg,'type'=>'push','content_type'=>'withdrawmoney','user'=>$user_id);
        //        DB::table('noticeboard')->insert($datanotice);
        //        $this->helper->sendFCMPush($notifyData);
        //    }
           
           return response()->json([
               'status' => true,
           ]);
        }  
}