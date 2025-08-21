<?php

namespace App\Http\Controllers\Api;
use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Models\Preference;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\VirtualCardTransaction;
use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use App\Models\VirtualEnrollUser;
use Validator;
use App\Models\VirtualCard;
use App\Models\FundingAccount;
use Illuminate\Support\Facades\Mail;

class LithicController extends Controller
{
    //Sandbox Details
    // public $ApiKey = '78e4cfa4-c440-4d9e-9440-a3c91526b3eb';
    // public $ApiUrl = 'https://sandbox.privacy.com/v1/';

    //Live Deatil
    public $ApiKey = '30d64c52-e5cc-44c3-a81a-f58c6c44dcb5';
    public $ApiUrl = 'https://api.lithic.com/v1/';

    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    
    //Comman Function For Post Method
    public function PostFunction($url,$dataobj)
	{
	    $mainUrl = $this->ApiUrl.$url;
	    $data  = json_encode($dataobj,JSON_UNESCAPED_UNICODE); 
	    $headerArray =array("accept-language: en-US,en;q=0.8","content-type: application/json","Authorization: api-key $this->ApiKey");
	    $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $mainUrl); 
        curl_setopt($curl, CURLOPT_POST, 1);  
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"POST");  
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headerArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
	    curl_close($curl);
		return $output;
	}
	//Comman Function For Get Method
	public function GetFunction($url)
	{
	    $mainUrl = $this->ApiUrl.$url;
	   // dd($mainUrl);
	    $headerArray =array("accept-language: en-US,en;q=0.8","content-type: application/json","Authorization: api-key $this->ApiKey");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $mainUrl);
		curl_setopt($ch, CURLOPT_POST, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headerArray);
	    $output = curl_exec($ch);
		curl_close($ch);
		return $output;	
		
	}
	//Comman Function For Put Method
	public function PutFunction($url,$dataobj)
	{
	    $mainUrl = $this->ApiUrl.$url;
	    $data  = json_encode($dataobj,JSON_UNESCAPED_UNICODE); 
	    $headerArray =array("accept-language: en-US,en;q=0.8","content-type: application/json","Authorization: api-key $this->ApiKey");
	    $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $mainUrl); 
        curl_setopt($curl, CURLOPT_POST, 1);  
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"PUT");  
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headerArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
	    curl_close($curl);
		return $output;
	}
	
    //Add Funding Account (Postman)
    // public function AddFundingAccount(Request $request)
    // {
    //     $validator = \Validator::make($request->all(),
    //       [
    //             'account_name'   => 'required',
    //             'account_number' => 'required',
    //             'routing_number' => 'required'
    //       ]);
    //     if($validator->fails())
    //     {
    //         $response['response'] = $validator->messages();
    //         return $response;
    //     }
    //     $dataobj["account_name"]    = $request->account_name;
    // 	$dataobj["account_number"]  = $request->account_number;	
    // 	$dataobj["routing_number"]  = $request->routing_number;
    // 	$url                        = "fundingsource/bank"; 
	   // $output                     = $this->PostFunction($url,$dataobj);
	   // $outobj = json_decode($output,true);
	   // if (array_key_exists("message", $outobj))
    //     {
    //       print_r($outobj['message']);
    //     }
    //     else
    //     {
    //      $AccountCreate = new FundingAccount();
	   //  $AccountCreate->account_name = $outobj['data']['account_name'];
	   //  $AccountCreate->last_four    = $outobj['data']['last_four'];
	   //  $AccountCreate->nickname     = $outobj['data']['nickname'];
	   //  $AccountCreate->token        = $outobj['data']['token'];
	   //  $AccountCreate->account_state= $outobj['data']['state'];
	   //  $AccountCreate->type         = $outobj['data']['type'];
	   //  $AccountCreate->save();
	   //  print_r($outobj);
    //     }
	
    // }
    //For App
    public function AddFundingAccount($account_name,$account_number,$routing_number,$account_token,$user_id)
    {
        
        $dataobj["account_name"]    = $account_name;
    	$dataobj["account_number"]  = $account_number;	
    	$dataobj["routing_number"]  = $routing_number;
    	$dataobj["account_token"]   = $account_token;
    	$url                        = "fundingsource/bank"; 
	    $output                     = $this->PostFunction($url,$dataobj);
	    $outobj = json_decode($output,true);
	    
	    
         $AccountCreate = new FundingAccount();
	     $AccountCreate->account_name = $outobj['data']['account_name'];
	     $AccountCreate->last_four    = $outobj['data']['last_four'];
	     $AccountCreate->user_id      = $user_id;
	     $AccountCreate->nickname     = $outobj['data']['nickname'];
	     $AccountCreate->token        = $outobj['data']['token'];
	     $AccountCreate->account_state= $outobj['data']['state'];
	     $AccountCreate->type         = $outobj['data']['type'];
	     $AccountCreate->save();
	      return 1;
        
	
    }
    
    // public function ValidateFundingAccount($account_name,$account_number,$routing_number,$account_token,$user_id)
    // {
        
    //     $dataobj["token"]    = $token;
    // 	$dataobj["microdeposit"]  = $microdeposit;	
    // 	$dataobj["microdeposit"]  = $microdeposit;
    // 	$dataobj["account_token"]   = $account_token;
    // 	$url                        = "fundingsource/bank/validate"; 
	   // $output                     = $this->PostFunction($url,$dataobj);
	   // $outobj = json_decode($output,true);
	   // if (array_key_exists("message", $outobj))
    //     {
    //       return 0;
    //     }
    //     else
    //     {
    //      $AccountCreate = new FundingAccount();
	   //  $AccountCreate->account_name = $outobj['data']['account_name'];
	   //  $AccountCreate->last_four    = $outobj['data']['last_four'];
	   //  $AccountCreate->user_id      = $user_id;
	   //  $AccountCreate->nickname     = $outobj['data']['nickname'];
	   //  $AccountCreate->token        = $outobj['data']['token'];
	   //  $AccountCreate->account_state= $outobj['data']['state'];
	   //  $AccountCreate->type         = $outobj['data']['type'];
	   //  $AccountCreate->save();
	   //   return 1;
    //     }
	
    // }
    
    //Enroll/Add New Account
    public function EnrollAccount(Request $request)
    {
        $validator = \Validator::make($request->all(),
           [
                'first_name'   => 'required',
                'dob'          => 'required',
                'email'        => 'required',
                'street1'      => 'required',
                'zipcode'      => 'required',
                'last_name'    => 'required',
                'phone_number' => 'required',
                'ssn'          => 'required',
                'user_id'      => 'required|unique:virtual_enroll_user'
           ]);
          
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        $dataobj["first_name"]    = $request->first_name;
    	$dataobj["dob"]           = $request->dob;	
    	$dataobj["email"]         = $request->email;
    	$dataobj["street1"]       = $request->street1;
    	$dataobj["zipcode"]       = $request->zipcode;
    	$dataobj["last_name"]     = $request->last_name;
    	$dataobj["phone_number"]  = $request->phone_number;
    	$dataobj["ssn"]           = $request->ssn;
    	$dataobj['tos_timestamp'] = date('Y-m-d H:i:s');
    	$dataobj['kyc_passed_timestamp'] = date('Y-m-d H:i:s');
    	$dataobj['kyc_type'] = "BASIC";
    	//$dataobj["user_id"]       = $request->user_id;
    	$url                      = "enroll/consumer"; 
	    $output                   = $this->PostFunction($url,$dataobj);
	    $outobj = json_decode($output,true);
	    if (array_key_exists("message", $outobj))
        {
          $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
          return response()->json(['response'=>$response['response']]);
         }
        else
        {
         $AccountCreate = new VirtualEnrollUser();
	     $AccountCreate->enroll_email         = $request->email;
	     $AccountCreate->enroll_first_name    = $request->first_name;
	     $AccountCreate->enroll_street1       = $request->street1;
	     $AccountCreate->enroll_zipcode       = $request->zipcode;
	     $AccountCreate->enroll_last_name     = $request->last_name;
	     $AccountCreate->enroll_phone_number  = $request->phone_number;
	     $AccountCreate->enroll_ssn           = $request->ssn;
	     $AccountCreate->enroll_account_token = $outobj['data']['account_token']??'0';
	     $AccountCreate->enroll_kyc           = json_encode($outobj['data']['kyc']);
	     $AccountCreate->user_id              = $request->user_id;  
	     $AccountCreate->enroll_result        = $outobj['data']['result'];
	     $AccountCreate->save();
	   //  $account_name   = 'Roshan Bank';
	   //  $account_number = '13719713158835300';
	   //  $routing_number = '011103093';
	     
	     $account_name   = 'Greentech Fin Inc.';
	     $account_number = '9800371040';
	     $routing_number = '084106768';
	     $account_token  = $AccountCreate->enroll_account_token;
	     User::where('id',$request->user_id)->update(['vcard'=>1]);
	   //  $enroll = $this->AddFundingAccount($account_name,$account_number,$routing_number,$account_token,$request->user_id);
	   // // dd();
	   //  if($enroll == 0)
	   //  {
	   //      $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>'Funding Process cant Completed');
    //          return response()->json(['response'=>$response['response']]);
	   //  }else
	   //  {
	         $response['response'] = array('status'=>$this->successStatus,'message'=>'Enroll Process Successfully Completed');
             return response()->json(['response'=>$response['response']]);
	     
	    }
    }
    //Create Virtual Card
    public function CreateCard(Request $request)
    {
        
        $validator = \Validator::make($request->all(),
           [
                'name_on_card'  => 'required',
                'spend_limit'   => 'required',
                'user_id'       => 'required'
           ]);
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        // $card_fee = DB::table('settings')->where('id', '35')->first();
        // $vcard_fee = $card_fee->value;
        

        $vcard_fee = "1.5";
        //$dataobj["account_token"]          = VirtualEnrollUser::where('user_id',$request->user_id)->first()->enroll_account_token??'';
        // $dataobj["funding_token"]          = FundingAccount::where('user_id',$request->user_id)->token??'';
        $dataobj["funding_token"]          = '0550e634-7977-429d-9c07-5843f76a1062'; //Live
        // $dataobj["funding_token"]          = 'eb1b825a-488a-49e3-9adf-db98c57f08e3'; // Sandbox

        $dataobj["memo"]                   = $request->name_on_card;
    	$dataobj["spend_limit"]            = (int)$request->spend_limit*100;	
    	$dataobj["spend_limit_duration"]   = 'FOREVER';
    	$dataobj["state"]                  = 'OPEN';
    	$dataobj["type"]                   = 'UNLOCKED';
    	$url                               = "card"; 
    	$balance = Wallet::where(['user_id'=>$request->user_id,'currency_id'=>9])->first()->balance??'';
    	if((!empty($balance)) && (round($balance) >= $request->spend_limit))
    	{   
    	    Wallet::where(['user_id'=>$request->user_id,'currency_id'=>9])->update(['balance'=>(round($balance) - ($request->spend_limit + $vcard_fee))]);
    	    $output = $this->PostFunction($url,$dataobj);
    	}else
    	{
    	    $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>'You do not have enough balance in your wallet. Please add fund and try again','description'=>'less balance');
            return response()->json(['response'=>$response['response']]);
    	}
	    
	   //dd(User::find($request->user_id));
	    $outobj = json_decode($output,true);
	    if (array_key_exists("message", $outobj))
        {
          $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
          return response()->json(['response'=>$response['response']]);
        }
        else
        {
             // Store Card Infromation    
             $AccountCreate = new VirtualCard();
    	     $AccountCreate->user_id         = $request->user_id;
    	     $AccountCreate->host_name       = $outobj['hostname'];
    	     $AccountCreate->memo            = $outobj['memo'];
    	     $AccountCreate->last_four_digit = $outobj['last_four'];
    	   //  $AccountCreate->exp_month       = $outobj['exp_month'];
    	   //  $AccountCreate->exp_year        = $outobj['exp_year'];
    	   //  $AccountCreate->cvv             = $outobj['cvv'];
    	    // $AccountCreate->funding_account_id = $outobj['funding']['token']??'0';
    	   //  $AccountCreate->pan             = $outobj['pan'];
    	     $AccountCreate->spend_limit              = $outobj['spend_limit']/100;  
    	     $AccountCreate->spend_limit_duration     = $outobj['spend_limit_duration'];
    	     $AccountCreate->card_state      = $outobj['state'];
    	     $AccountCreate->token           = $outobj['token'];
    	     $AccountCreate->type            = $outobj['type'];
    	     $AccountCreate->save();
    	     //Store Transaction For wallet Deducation
    	     $transaction                           = new Transaction();
             $transaction->user_id                  = $request->user_id;
             $transaction->currency_id              = 9;
             $transaction->payment_method_id        = 1;
             $transaction->uuid                     = unique_code();;
             $transaction->transaction_reference_id = $AccountCreate->id;
             $transaction->transaction_type_id      = 2;
             $transaction->subtotal                 = $request->spend_limit;
             $transaction->is_card                  = 1;
             $transaction->percentage               = 0;
             $transaction->charge_percentage        = 0;
             $transaction->charge_fixed             = $vcard_fee;
             $transaction->total                    = ($request->spend_limit + $transaction->charge_fixed);
             $transaction->status                   = 'Success';
             $transaction->save();
             
             
             if($transaction){
                 
                $user_datail_card = User::where('id',$request->user_id)->update(['vcard'=>1]);
                
                $user_datail = User::where('id',$request->user_id)->first();
                $datamail = array();
                $datamail['first_name'] = $user_datail->first_name;
                $datamail['last_name'] = $user_datail->last_name;
                $datamail['email'] = $user_datail->email;
                $datamail['last_four_digit'] = $AccountCreate->last_four_digit;
                $datamail['name_on_card'] = $request->name_on_card;
                $datamail['spend_limit'] = $request->spend_limit;
                $datamail['spend_limit_duration'] = 'FOREVER';
                $datamail['type'] = 'UNLOCKED';
        			
        // 		Mail::send('emails.newcard', $datamail, function ($message) use ($datamail) {
        // 			$message->to($datamail['email']);
        // 			$message->from('noreply@caribpayintl.com', 'CaripPay');
        // 			$message->subject('New Virtual Card');
        // 		});
        		
        // 		Mail::send('emails.newcardadmin', $datamail, function ($message) use ($datamail) {
        // 			$message->to('smd.webtech@gmail.com');
        // 			$message->to('nirbhayrnc@gmail.com');
        // 			$message->from($datamail['email'], 'CaripPay');
        // 			$message->subject('New Virtual Card');
        // 		});
             }
                        
    	     $response['response'] = array('status'=>$this->successStatus,'message'=>'Card Create Successfully');
             return response()->json(['response'=>$response['response']]);
	    }
    }
    // Card Listing
    // public function GetCardList(Request $request)
    // {
    //     $validator = \Validator::make($request->all(),
    //      [
    //       'user_id' => 'required',
    //      ]);
    //     if($validator->fails())
    //     {
    //         $response['response'] = $validator->messages();
    //         return $response;
    //     }
    //     $user_id   = $request->user_id;
    //     $check = VirtualEnrollUser::where('user_id',$user_id)->first()->enroll_account_token??'';
    //     if(!empty($check))
    //     {
    //         $url             = "card?account_token=".$check;
    //         $output          = $this->GetFunction($url);
    // 	    $outobj = json_decode($output,true);
    // 	    if (array_key_exists("message", $outobj))
    //         {
    //           $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
    //           return response()->json(['response'=>$response['response']]);
    //          }
    //         else
    //         {
    //           $response['response'] = array('status'=>$this->successStatus,'message'=>'Get Card List Successfully','cards'=>$outobj);
    //           return response()->json(['response'=>$response['response']]);
    // 	    }
    //     }else
    //     {
    //         $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>'Sorry you are not enroll user','description'=>'cant enroll');
    //         return response()->json(['response'=>$response['response']]);
    //     }
    // }
    //Upadte Virtual Card
    public function UpdateCard(Request $request)
    {
        $validator = \Validator::make($request->all(),
         [
            'name_on_card'  => 'required',
            'spend_limit'   => 'required',
            'user_id'       => 'required',
            'last_four'     => 'required',
         ]);
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        $Card = VirtualCard::where(['last_four_digit'=>$request->last_four,'user_id'=>$request->user_id])->first();
        $dataobj["card_token"]             = $Card->token;
        //$dataobj["account_token"]          = VirtualEnrollUser::where('user_id',$request->user_id)->first()->enroll_account_token??'';
        $dataobj["memo"]                   = $request->name_on_card;
    	$dataobj["spend_limit"]            = (int)$request->spend_limit*100;	
        $dataobj["spend_limit_duration"]   = 'TRANSACTION';
    	$dataobj["state"]                  = 'OPEN';
        $url                               = "card"; 
    	$balance = Wallet::where(['user_id'=>$request->user_id,'currency_id'=>1])->first()->balance??'';
    	$deduct = (round($request->spend_limit) - round($Card->spend_limit));
    	if((!empty($balance)) && (round($balance) >= $request->spend_limit))
    	{ 
    	    if($request->spend_limit > $Card->spend_limit)
    	    {
        	    //dd((round($balance) - $deduct));
        	    Wallet::where(['user_id'=>$request->user_id,'currency_id'=>1])->update(['balance'=>(round($balance) - $deduct)]);
        	    $output = $this->PutFunction($url,$dataobj);
         	}else
         	{
         	     $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>'Spend Limit should be greather current limit','description'=>'less limit');
                 return response()->json(['response'=>$response['response']]);
         	}
    	}else
    	{
    	    $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>'You do not have enough balance in your wallet. Please add fund and try again','description'=>'less balance');
            return response()->json(['response'=>$response['response']]);
    	}
    	$outobj = json_decode($output,true);
    	if (array_key_exists("message", $outobj))
        {
          $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
          return response()->json(['response'=>$response['response']]);
        }
        else
        {
         $AccountCreate = VirtualCard::where('last_four_digit',$request->last_four)->update([
                	     'host_name'       => $outobj['hostname'],
                	     'memo'            => $outobj['memo'],
                	     'last_four_digit' => $outobj['last_four'],
                	     'exp_month'       => $outobj['exp_month'],
                	     'exp_year'        => $outobj['exp_year'],
                	     'cvv'             => $outobj['cvv'],
                	     'pan'             => $outobj['pan'],
                	     'spend_limit'              => $outobj['spend_limit']/100, 
                	     'spend_limit_duration'     => $outobj['spend_limit_duration'],
                	     'card_state'      => $outobj['state'],
                	     'token'           => $outobj['token']
	                   ]);
	        //Store Transaction For wallet Deducation
    	     $transaction                           = new Transaction();
             $transaction->user_id                  = $request->user_id;
             $transaction->currency_id              = 1;
             $transaction->payment_method_id        = 1;
             $transaction->uuid                     = unique_code();;
             $transaction->transaction_reference_id = $outobj['token'];
             $transaction->transaction_type_id      = 2;
             $transaction->subtotal                 = $deduct;
             $transaction->is_card                  = 2;
             $transaction->percentage               = 0;
             $transaction->charge_percentage        = 0;
             $transaction->charge_fixed             = 0;
             $transaction->total                    = ($deduct);
             $transaction->status                   = 'Success';
             $transaction->save();
	     $response['response'] = array('status'=>$this->successStatus,'message'=>'Card Update Successfully');
         return response()->json(['response'=>$response['response']]);
	    }
    }
    // Open Close Card
    public function OpenCloseCard(Request $request)
    {
        $validator = \Validator::make($request->all(),
         [
            'user_id'       => 'required',
            'last_four'     => 'required',
            'card_status'   => 'required'
         ]);
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        $carddetails = VirtualCard::where(['last_four_digit'=>$request->last_four,'user_id'=>$request->user_id])->first();
        $dataobj["card_token"]             = $carddetails->token;
        //$dataobj["account_token"]          = VirtualEnrollUser::where('user_id',$request->user_id)->first()->enroll_account_token??'';
        $dataobj["memo"]                   = $carddetails->memo;
    	$dataobj["spend_limit"]            = (int)$carddetails->spend_limit*100;	
        $dataobj["spend_limit_duration"]   = 'FOREVER';
    	$dataobj["state"]                  = $request->card_status;
        $url                               = "card"; 
        $output = $this->PutFunction($url,$dataobj);
        $outobj = json_decode($output,true);
        if (array_key_exists("message", $outobj))
        {
          $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
          return response()->json(['response'=>$response['response']]);
         }
        else
        {
         $AccountCreate = VirtualCard::where('last_four_digit',$request->last_four)->update([
                	    'card_state'      => $outobj['state']
                	   ]);
	     $response['response'] = array('status'=>$this->successStatus,'message'=>'Card Update Successfully');
         return response()->json(['response'=>$response['response']]);
	    }
    }
    //Get Transaction For Specific Card
    public function GetTransaction(Request $request)
    {
         $validator = \Validator::make($request->all(),
         [
           'user_id'   => 'required',
           'last_four' => 'required',
         ]);
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        $user_id   = $request->user_id;
        $check = VirtualCard::where(['user_id'=>$request->user_id,'last_four_digit'=>$request->last_four])->first()->token??'';
        //$account_token = VirtualEnrollUser::where('user_id',$request->user_id)->first()->enroll_account_token??'';
        //dd($check);
       if(!empty($check))
        {
            $url             = "transaction/all?card_token=".$check;
             //dd($url);
            $output          = $this->GetFunction($url);
    	    $outobj = json_decode($output,true);
    	    if (array_key_exists("message", $outobj))
            {
              $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
              return response()->json(['response'=>$response['response']]);
             }
            else
            {
              $response['response'] = array('status'=>$this->successStatus,'message'=>'Get Card Transaction Successfully','transaction'=>$outobj);
              return response()->json(['response'=>$response['response']]);
    	    }
        }else
        {
            $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>'Sorry your last four digit is invalid','description'=>'invalid card number!');
            return response()->json(['response'=>$response['response']]);
        }
    }
    //Transaction Webhook
    public function TransactionWebhook(Request $request)
    {
               
        $check = VirtualCardTransaction::where('trancation_token',$request->token)->first()->trancation_token??'';
        $last = count($request->events);
        $index = $last-1;
        if(!empty($check))
        {
            VirtualCardTransaction::where('trancation_token',$request->token)->update(['event'=>$request->events[$index]['type'],'amount' => $request->settled_amount]);
            
        }else
        {
            $transaction = new VirtualCardTransaction();
            $transaction->card = $request->card['last_four'];
            $transaction->card_token = $request->card['token'];
            $transaction->event = $request->events[$index]['type'];
            $transaction->amount = $request->amount/100;
            $transaction->funding = json_encode($request->funding);
            $transaction->merchant = $request->merchant['descriptor'];
            $transaction->result = $request->result;
            $transaction->created = $request->created;
            $transaction->status = $request->status;
            $transaction->trancation_token = $request->token;
            $transaction->save(); 
        }
     }
    //Test FUnction For Insert Transaction Stored in DB by WEbhook
    public function StoreTransaction()
    {
        $data = $insert = DB::table('virtual_card_transactions')->where('id',53)->first()->funding;  
        $de = json_decode($data,true);
        $transaction = new VirtualCardTransaction();
        $transaction->card = $de['card']['last_four'];
        $transaction->card_token = $de['card']['token'];
        $transaction->event = json_encode($de['events']);
        $transaction->amount = $de['amount'];
        $transaction->funding = json_encode($de['funding']);
        $transaction->merchant = json_encode($de['merchant']);
        $transaction->result = $de['result'];
        $transaction->created = $de['created'];
        $transaction->status = $de['status'];
        $transaction->trancation_token = $de['token'];
        $transaction->save();
    }
    //Get Current Card Limit
    public function GetCardLimit(Request $request)
    {
         $validator = \Validator::make($request->all(),
         [
           'card_token'   => 'required',
         ]);
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        $limit = VirtualCardTransaction::where('card_token',$request->card_token)->get();
        if(count($limit)>0)
        {
             $outobj['card_limit'] = VirtualCard::where('token',$request->card_token)->first()->spend_limit??'';
             $outobj['spending_amount']  = $limit->sum('amount');
             $outobj['available_amount']  = $outobj['card_limit'] - $outobj['spending_amount'];
             $response['response']  = array('status'=>$this->successStatus,'message'=>'Get Card Current Limit Successfully','limits'=>$outobj);
             return response()->json(['response'=>$response['response']]);
        }else
        {
             $outobj['card_limit'] = VirtualCard::where('token',$request->card_token)->first()->spend_limit??'';
             $outobj['spending_amount']  = 0;
             $outobj['available_amount']  = VirtualCard::where('token',$request->card_token)->first()->spend_limit??'';
             $response['response']  = array('status'=>$this->successStatus,'message'=>'Get Card Current Limit Successfully','limits'=>$outobj);
             return response()->json(['response'=>$response['response']]);
        }
    }
    //Set Account Limit
    public function SetAccountLimit(Request $request)
    {
         $validator = \Validator::make($request->all(),
         [
           'user_id'         => 'required',
           'daily_limit'     => 'required',
           'monthly_limit'   => 'required',
           'lifetime_limit'  => 'required'
         ]);
         if($validator->fails())
         {
            $response['response'] = $validator->messages();
            return $response;
         }
       
        //$dataobj["account_token"]         = VirtualEnrollUser::where('user_id',$request->user_id)->first()->enroll_account_token??'';
    	$dataobj["daily_spend_limit"]     = $request->daily_limit;	
    	$dataobj["lifetime_spend_limit"]  = $request->lifetime_limit;
    	$dataobj["monthly_spend_limit"]   = $request->monthly_limit;
        $url                              = "account/limit"; 
	    $output                           = $this->PostFunction($url,$dataobj);
	    $outobj = json_decode($output,true);
	    if (array_key_exists("message", $outobj))
        {
              $response['response'] = array('status'=>$this->unauthorisedStatus,'message'=>$outobj['message'],'description'=>$outobj['debugging_request_id']);
              return response()->json(['response'=>$response['response']]);
        }
        else
        { 
              $AccountCreate = VirtualEnrollUser::where('user_id',$request->user_id)->update([
               'daily_limit'    => $outobj['data'][0]['spend_limit']['daily'],
               'monthly_limit'  => $outobj['data'][0]['spend_limit']['monthly'],
               'lifetime_limit' => $outobj['data'][0]['spend_limit']['lifetime']
             ]);
             
             $response['response']  = array('status'=>$this->successStatus,'message'=>'Account Limit Set Successfully','limits'=>$outobj);
             return response()->json(['response'=>$response['response']]);
	     
        }
    }
    
    // Card Listing
    public function GetCardList(Request $request)
    {
        $validator = \Validator::make($request->all(),
         [
           'user_id' => 'required',
         ]);
        if($validator->fails())
        {
            $response['response'] = $validator->messages();
            return $response;
        }
        $user_id   = $request->user_id;
        $total = VirtualCard::where('user_id',$user_id)->count();
        $cards = VirtualCard::where('user_id',$user_id)->get();
        if($total > 0){
            foreach($cards as $card){
                $data[] = array(
                    'created' => $card['created_at'],
                    'cvv' => $card['cvv'],
                    'exp_month' => $card['exp_month'],
                    'exp_year' => $card['exp_year'],
                    'hostname' => $card['host_name'],
                    'last_four' => $card['last_four_digit'],
                    'memo' => $card['memo'],
                    'pan' => $card['pan'],
                    'spend_limit' => $card['spend_limit'],
                    'spend_limit_duration' => $card['spend_limit_duration'],
                    'state' => $card['card_state'],
                    'token' => $card['token'],
                    'type' => $card['type'],
                );
            }
            
            $response['response']  = array('status'=>$this->successStatus,'message'=>'Card List','data'=>$data,'total_entries'=>$total);
            return response()->json(['response'=>$response['response']]);
        }else{
            $response['response']  = array('status'=>$this->successStatus,'message'=>'No Card Available','total_entries'=>$total);
            return response()->json(['response'=>$response['response']]);
        }
        
        
    }
    
    public function getCardLimitAmt()
    {
// echo("hello");
        $data = DB::table('card_limit')->select('id','card_limit')->get();
        
        return response()->json($data);
        
    }
}







