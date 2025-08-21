<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Models\Preference;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use Validator;
use App\Models\AddFund;

class VirtualcardController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $email;

    public function __construct()
    {
        $this->email = new EmailController();
    }

    public function virtualcard(Request $request)
    {
        $data['virtualCardsList'] = DB::table('virtual_cards')
                                    ->select('virtual_cards.*','virtual_cards_funding_accounts.account_name as FundingAccount','virtual_cards_funding_accounts.last_four as FundingLastFour')
                                    ->join('virtual_cards_funding_accounts','virtual_cards_funding_accounts.id','=','virtual_cards.funding_account_id')
                                    ->where('user_id',$request->user_id)
                                    ->get();
        $OrdataCheck = array('OPEN','PAUSED','CLOSED');  
        //dd($OrdataCheck);
        $data['create_limit_checked'] = DB::table('virtual_cards')
        ->where('user_id',$request->user_id)
        ->WhereIn('card_state',$OrdataCheck)
        ->get(); 
        $arr = [];
        //dd($data);
        
        return response()->json([
            'message'=>'Virtual card Details.',
            'status'=>'success',
            'card'=>$data,
            'code'=>200
        ]);
    }
    
    public function createCard(Request $request)
    {  
        $validator=Validator::make($request->all(), [
            'name_on_card' => ['required', 'max:255', 'string'],
            'card_limit' => ['required'],
           
            'spend_limit_duration'=> ['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Something went wrong with request.Please try again later',
                'validations'=>$validator->errors(),
                'status'=>'error'
            ]);
        }
         
        $memo = 'Jhon Deo';
        $type = 'UNLOCKED';
        $funding_token = '46e9a102-1d8e-489c-a19e-b314ccc219ac';
        $pin = base64_decode(1234);
        $spend_limit = 100;
        $spend_limit_duration = 'FOREVER';
        $state = 'OPEN';
        $shipping_address = NULL;
        $product_id = NULL;
        if(empty($request->card_type))
        {
            $card_type = 'MERCHANT_LOCKED';
        } else {
            $card_type = $request->card_type;
        }
        $postData = array(
            "type"=>$card_type,
            "memo" =>$request->name_on_card,
            "spend_limit"=>(int)$request->card_limit*100,
            "spend_limit_duration" =>$request->spend_limit_duration,
            );
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
        $CreatedResponse = json_decode(curl_exec($curl));
        if(!empty($CreatedResponse->debugging_request_id))
        {
            return back()->with('alert',$CreatedResponse->message);
        } else {
            //dd($CreatedResponse);
            
            $card_exists = DB::table('virtual_cards')->where('user_id', $request->user_id)->first();
            if($card_exists){
                return response()->json([
                    'message'=>'Virtual card already exists.',
                    'status'=>'success',
                    'card'=>$card_exists,
                    'code'=>200
                ]);
            }else{
                $insertCard = array(
                    'user_id'=>$request->user_id,
                    'host_name'=>$CreatedResponse->memo,
                    'memo'=>$CreatedResponse->memo,
                    'last_four_digit'=>$CreatedResponse->last_four,
                    //'exp_month'=>$CreatedResponse->exp_month,
                    //'exp_year'=>$CreatedResponse->exp_year,
                    //'cvv'=>$CreatedResponse->cvv,
                    //'pan'=>$CreatedResponse->pan,
                    'spend_limit'=>$CreatedResponse->spend_limit/100,
                    'spend_limit_duration'=>$CreatedResponse->spend_limit_duration,
                    'card_state'=>$CreatedResponse->state,
                    'token'=>$CreatedResponse->token,
                    'type'=>$CreatedResponse->type,
                   
                );
                
               $cardInserted = DB::table('virtual_cards')->insertGetId($insertCard);
               $card_details = DB::table('virtual_cards')->where('id', $cardInserted)->first();
               //dd($cardInserted);
               if($cardInserted)
               {
                   $insertFunding = array(
                    'virtual_cards_id'=>$cardInserted,
                    'account_name'=>$CreatedResponse->funding->account_name,
                    'last_four'=>$CreatedResponse->funding->last_four,
                    'nickname'=>$CreatedResponse->funding->nickname,
                    'account_state'=>$CreatedResponse->funding->state,
                    'token'=>$CreatedResponse->funding->token,
                    'type'=>$CreatedResponse->funding->type,
                    );
                    $fundingInserted = DB::table('virtual_cards_funding_accounts')->insertGetId($insertFunding);    
                    DB::table('virtual_cards')->where('id',$cardInserted)->update(['funding_account_id'=>$fundingInserted]);
                   
                     return response()->json([
                        'message'=>'Virtual card created succesfully , it will activated within 4-5 hours, you will be notify through email once your card has been activated.',
                        'status'=>'success',
                        'card'=>$card_details,
                        'code'=>200
                    ]);
               } else {
                    return response()->json([
                        'message'=>'Virtual Card created successfully but value is not inserted in DB!',
                        'status'=>'error'
                    ]);
               }
            }
        }
        
        //curl_close($curl);
   }
   
    public function updateVirtualCard(Request $request)
    {
        
        $validator=Validator::make($request->all(), [
            'name_on_card' => ['required', 'max:255', 'string'],
            'card_limit' => ['required'],
            'card_token'=>['required']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Something went wrong with request.Please try again later',
                'validations'=>$validator->errors(),
                'status'=>'error'
            ]);
        }
     
        $postData = array(
            //"state"=>'OPEN',
            "card_token"=>$request->card_token,
            "memo" =>$request->name_on_card,
            "spend_limit"=>(int)$request->card_limit,
        );
        
        
        
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $CreatedResponse = json_decode(curl_exec($curl));
        if(!empty($CreatedResponse->debugging_request_id))
        {
            return back()->with('alert',$CreatedResponse->message);
        } else {
            //dd($CreatedResponse);
            $updateCard = array(
                //'user_id'=>Auth::id(),
                'host_name'=>$CreatedResponse->memo,
                'memo'=>$CreatedResponse->memo,
                //'last_four_digit'=>$CreatedResponse->last_four,
                //'exp_month'=>$CreatedResponse->exp_month,
                //'exp_year'=>$CreatedResponse->exp_year,
                //'cvv'=>$CreatedResponse->cvv,
                //'pan'=>$CreatedResponse->pan,
                'spend_limit'=>$CreatedResponse->spend_limit,
                'spend_limit_duration'=>$CreatedResponse->spend_limit_duration,
                'card_state'=>$CreatedResponse->state,
                //'token'=>$CreatedResponse->token,
                'type'=>$CreatedResponse->type,
               
                );
                
           $cardUpdated = DB::table('virtual_cards')->where(['user_id'=>$request->user_id,'token'=>$request->card_token])->update($updateCard);
           //dd($cardUpdated);
           if($cardUpdated)
           {
                return response()->json([
                    'message'=>'Virtual Card updated successfully.',
                    'status'=>'success',
                    'code'=>200
                ]);
           } else {
                return response()->json([
                    'message'=>'Something went wrong with request.Please try again later',
                    'status'=>'error'
                ]);
           }
        }
        
        //curl_close($curl);
   }
   
   public function pausedVirtualCard(Request $request)
   {
           $validator=Validator::make($request->all(), [
                
                'card_token'=>['required']
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message'=>'Something went wrong with request.Please try again later',
                    'validations'=>$validator->errors(),
                    'status'=>'error'
                ]);
            }
         //dd($request->card_token);
        //$memo = 'Jhon Deo';
        //$type = 'UNLOCKED';
        //$funding_token = '46e9a102-1d8e-489c-a19e-b314ccc219ac';
        //$pin = base64_decode(1234);
        //$spend_limit = 100;
        //$spend_limit_duration = 'FOREVER';
        //$state = 'OPEN';
        //$shipping_address = NULL;
        //$product_id = NULL;
        $postData = array(
            //"state"=>'OPEN',
            "card_token"=>$request->card_token,
            "state"=>'PAUSED',
            //"memo" =>$request->name_on_card,
            //"spend_limit"=>(int)$request->card_limit,
            //"spend_limit_duration" =>$request->spend_limit_duration,
            );
        //if(!empty($request->name_on_card))    
       // dd($postData);    
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $CreatedResponse = json_decode(curl_exec($curl));
        if(!empty($CreatedResponse->debugging_request_id))
        {
            return back()->with('alert',$CreatedResponse->message);
        } else {
            //dd($CreatedResponse);
            $updateCard = array(
                //'user_id'=>Auth::id(),
                'host_name'=>$CreatedResponse->memo,
                'memo'=>$CreatedResponse->memo,
                //'last_four_digit'=>$CreatedResponse->last_four,
                //'exp_month'=>$CreatedResponse->exp_month,
                //'exp_year'=>$CreatedResponse->exp_year,
                //'cvv'=>$CreatedResponse->cvv,
                //'pan'=>$CreatedResponse->pan,
                'spend_limit'=>$CreatedResponse->spend_limit,
                'spend_limit_duration'=>$CreatedResponse->spend_limit_duration,
                'card_state'=>$CreatedResponse->state,
                //'token'=>$CreatedResponse->token,
                'type'=>$CreatedResponse->type,
               
                );
                
           $cardUpdated = DB::table('virtual_cards')->where(['user_id'=>$request->user_id,'token'=>$request->card_token])->update($updateCard);
           //dd($cardUpdated);
           if($cardUpdated)
           {
                return response()->json([
                    'message'=>'Virtual Card paused successfully.',
                    'status'=>'success',
                    'code'=>200
                ]);
           } else {
                return response()->json([
                    'message'=>'Something went wrong with request.Please try again later',
                    'status'=>'error'
                ]);
           }
        }
        
        //curl_close($curl);
   }
   
    public function openVirtualCard(Request $request)
    {
        $validator=Validator::make($request->all(), [
            
            'card_token'=>['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Something went wrong with request.Please try again later',
                'validations'=>$validator->errors(),
                'status'=>'error'
            ]);
        }
         //dd($request->card_token);
        //$memo = 'Jhon Deo';
        //$type = 'UNLOCKED';
        //$funding_token = '46e9a102-1d8e-489c-a19e-b314ccc219ac';
        //$pin = base64_decode(1234);
        //$spend_limit = 100;
        //$spend_limit_duration = 'FOREVER';
        //$state = 'OPEN';
        //$shipping_address = NULL;
        //$product_id = NULL;
        $postData = array(
            //"state"=>'OPEN',
            "card_token"=>$request->card_token,
            "state"=>'OPEN',
            //"memo" =>$request->name_on_card,
            //"spend_limit"=>(int)$request->card_limit,
            //"spend_limit_duration" =>$request->spend_limit_duration,
            );
        //if(!empty($request->name_on_card))    
       // dd($postData);    
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $CreatedResponse = json_decode(curl_exec($curl));
        if(!empty($CreatedResponse->debugging_request_id))
        {
            return back()->with('alert',$CreatedResponse->message);
        } else {
            //dd($CreatedResponse);
            $updateCard = array(
                //'user_id'=>Auth::id(),
                'host_name'=>$CreatedResponse->memo,
                'memo'=>$CreatedResponse->memo,
                //'last_four_digit'=>$CreatedResponse->last_four,
                //'exp_month'=>$CreatedResponse->exp_month,
                //'exp_year'=>$CreatedResponse->exp_year,
                //'cvv'=>$CreatedResponse->cvv,
                //'pan'=>$CreatedResponse->pan,
                'spend_limit'=>$CreatedResponse->spend_limit,
                'spend_limit_duration'=>$CreatedResponse->spend_limit_duration,
                'card_state'=>$CreatedResponse->state,
                //'token'=>$CreatedResponse->token,
                'type'=>$CreatedResponse->type,
               
                );
                
           $cardUpdated = DB::table('virtual_cards')->where(['user_id'=>$request->user_id,'token'=>$request->card_token])->update($updateCard);
           //dd($cardUpdated);
           if($cardUpdated)
           {
                return response()->json([
                    'message'=>'Virtual Card unpaused successfully.',
                    'status'=>'success',
                    'code'=>200
                ]);
           } else {
                return response()->json([
                    'message'=>'Something went wrong with request.Please try again later',
                    'status'=>'error'
                ]);
           }
        }
        
        //curl_close($curl);
   }
   
    public function closeVirtualCard(Request $request)
    {
        $validator=Validator::make($request->all(), [
            
            'card_token'=>['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Something went wrong with request.Please try again later',
                'validations'=>$validator->errors(),
                'status'=>'error'
            ]);
        }
         //dd($request->card_token);
        //$memo = 'Jhon Deo';
        //$type = 'UNLOCKED';
        //$funding_token = '46e9a102-1d8e-489c-a19e-b314ccc219ac';
        //$pin = base64_decode(1234);
        //$spend_limit = 100;
        //$spend_limit_duration = 'FOREVER';
        //$state = 'OPEN';
        //$shipping_address = NULL;
        //$product_id = NULL;
        $postData = array(
            //"state"=>'OPEN',
            "card_token"=>$request->card_token,
            "state"=>'CLOSED',
            //"memo" =>$request->name_on_card,
            //"spend_limit"=>(int)$request->card_limit,
            //"spend_limit_duration" =>$request->spend_limit_duration,
            );
        //if(!empty($request->name_on_card))    
       // dd($postData);    
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $CreatedResponse = json_decode(curl_exec($curl));
        if(!empty($CreatedResponse->debugging_request_id))
        {
            return back()->with('alert',$CreatedResponse->message);
        } else {
            //dd($CreatedResponse);
            $updateCard = array(
                //'user_id'=>Auth::id(),
                'host_name'=>$CreatedResponse->memo,
                'memo'=>$CreatedResponse->memo,
                //'last_four_digit'=>$CreatedResponse->last_four,
                //'exp_month'=>$CreatedResponse->exp_month,
                //'exp_year'=>$CreatedResponse->exp_year,
                //'cvv'=>$CreatedResponse->cvv,
                //'pan'=>$CreatedResponse->pan,
                'spend_limit'=>$CreatedResponse->spend_limit,
                'spend_limit_duration'=>$CreatedResponse->spend_limit_duration,
                'card_state'=>$CreatedResponse->state,
                //'token'=>$CreatedResponse->token,
                'type'=>$CreatedResponse->type,
               
                );
                
           $cardUpdated = DB::table('virtual_cards')->where(['user_id'=>$request->user_id,'token'=>$request->card_token])->update($updateCard);
           //dd($cardUpdated);
           if($cardUpdated)
           {
                return response()->json([
                    'message'=>'Virtual Card closed successfully.',
                    'status'=>'success',
                    'code'=>200
                ]);
           } else {
                return response()->json([
                    'message'=>'Something went wrong with request.Please try again later',
                    'status'=>'error'
                ]);
           }
        }
        
        //curl_close($curl);
   }
   
    public function virtualtransactions(Request $request, $card_token='')
    {
       $card_token = $request->card_token;
       $data['card_last_four_by_url'] = $card_token;
       $data['AllTransactionsList'] = $this->getTransactionsList($card_token);
       
        return response()->json([
            'message'=>'Transaction List.',
            'status'=>'success',
            'transactions'=>$data,
            'code'=>200
        ]);
         
    }
   
   public function getTransactionsList($card_token ='')
    {
        //dd($card_token);
      /*   $postData = array(
        "card_token"=>$request->card_token,
        "memo" =>$request->name_on_card,
        "spend_limit"=>(int)$request->card_limit,
        "spend_limit_duration" =>$request->spend_limit_duration,
        ); */
        //if(!empty($request->name_on_card))    
       // dd($postData);    
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/transaction?card_token='.$card_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        //CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $TransactionsResponse = json_decode(curl_exec($curl));
        //dd($TransactionsResponse);
        if(!empty($TransactionsResponse->debugging_request_id))
        {
            return back()->with('alert',$TransactionsResponse->message);
        } else {
            return $TransactionsResponse->data;
            //return back()->with('success','Transactions get successfully.');
           
        } 
    }

    public function getCardsList($card_token ='')
    {
        //dd($card_token);
      /*   $postData = array(
        "card_token"=>$request->card_token,
        "memo" =>$request->name_on_card,
        "spend_limit"=>(int)$request->card_limit,
        "spend_limit_duration" =>$request->spend_limit_duration,
        ); */
        //if(!empty($request->name_on_card))    
       // dd($postData);    
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card?card_token='.$card_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        //CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $TransactionsResponse = json_decode(curl_exec($curl));
        //dd($TransactionsResponse);
        if(!empty($TransactionsResponse->debugging_request_id))
        {
            return back()->with('alert',$TransactionsResponse->message);
        } else {
            return $TransactionsResponse->data;
            //return back()->with('success','Transactions get successfully.');
           
        } 
    }


    public function getVirtualCardsList()
    {
      /*   $postData = array(
        "card_token"=>$request->card_token,
        "memo" =>$request->name_on_card,
        "spend_limit"=>(int)$request->card_limit,
        "spend_limit_duration" =>$request->spend_limit_duration,
        ); */
        //if(!empty($request->name_on_card))    
       // dd($postData);    
        $api_key = config('app.PRIVACY_API_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('app.PRIVACY_API_URL').'/card?card_token=7913',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        //CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: api-key $api_key",
            ),
        ));
       
        $TransactionsResponse = json_decode(curl_exec($curl));
        dd($TransactionsResponse->data);
        if(!empty($TransactionsResponse->debugging_request_id))
        {
            return back()->with('alert',$TransactionsResponse->message);
        } else {
            return $TransactionsResponse->data;
            //return back()->with('success','Transactions get successfully.');
           
        } 
    }
    
    public function instantIssue()
    {
        $data['title'] = "Instant Issue Card";
        return view('user.instantissue.index',$data);
         
    }
    
    public function addnewfund(Request $request)
    {
        $fund = new AddFund();
        $fund->user_id = $request->user_id;
        $fund->card_number = $request->card_number;
        $fund->exp_month = $request->exp_month;
        $fund->exp_year = $request->exp_year;
        $fund->card_name = $request->card_name;
        $fund->email = $request->email;
        $fund->country = $request->country;
        $fund->zip = $request->zip;
        $fund->save();
        if($fund){
            $response['response']  = array('status'=>$this->successStatus,'message'=>'Fund added Successfully.');
            return response()->json(['response'=>$response['response']]);
        }else{
            $response['response']  = array('status'=>$this->successStatus,'message'=>'Something went wrong');
            return response()->json(['response'=>$response['response']]);
        }
    }
    
    public function getsavedcard(Request $request)
    {
        $user_id = $request->user_id;
        $data = AddFund::where('user_id', $user_id)->get();
        
        if($data){
            return response()->json([
                'message'=>'Saved Card List.',
                'status'=>'success',
                'cards'=>$data,
                'code'=>200
            ]);
        }else{
            return response()->json([
                'message'=>'No Card List Found.',
                'status'=>'error',
                'code'=>200
            ]);
        }
    }
    
    public function removesavedcard(Request $request)
    {
        $user_id = $request->user_id;
        $card_number = $request->card_number;
        $data = AddFund::where('user_id', $user_id)->where('card_number', $card_number)->delete();
        
        if($data){
            return response()->json([
                'message'=>'Card Removed Successfully.',
                'status'=>'success',
                'code'=>200
            ]);
        }else{
            return response()->json([
                'message'=>'No Card Found.',
                'status'=>'error',
                'code'=>200
            ]);
        }
    }
}
