<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\FeesLimit;
use App\Models\RequestPayment;
use App\Models\DocumentVerification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\File;
use App\Models\Transfer;
use App\Models\Notification;
use App\Models\PendingTransaction;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class RequestMoneyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 401;
    public $notFound           = 404;
    public $email;
    protected $helper;
    protected $requestPayment;
    protected $transfer;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->email          = new EmailController();
        $this->requestPayment = new RequestPayment();
        $this->transfer = new Transfer();
    }

    //Request Money starts here
    public function postRequestMoneyEmailCheckApi()
    {
        // dd(request()->all());
       
       if (request('user_id'))
        {
            $user_id       = request('user_id');
            $receiverEmail = request('receiverEmail');
            $user          = User::where('id', '=', $user_id)->first(['first_name','email']);
            $receiver      = User::where('email', $receiverEmail)->where('role_id', '2')->first(['id','email','status','first_name','last_name','formattedPhone']);

            if (@$user->email == @$receiver->email)
            {
                $success['status']  = $this->unauthorisedStatus;
                $success['reason']  = 'own-email';
                $success['tran_limit_message'] = 'You cannot request money to yourself!';
                return response()->json(['success' => $success], $this->successStatus);
            }
            else
            {
                if ($receiver)
                {
                    if ($receiver->status == 'Suspended')
                    {
                        $success['status']  = $this->unauthorisedStatus;
                        $success['reason']  = 'suspended';
                        $success['tran_limit_message'] = 'The recipient is suspended!';
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                    elseif ($receiver->status == 'Inactive')
                    {
                        $success['status']  = $this->unauthorisedStatus;
                        $success['reason']  = 'inactive';
                        $success['tran_limit_message'] = 'The recipient is inactive!';
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                    
                    $userData = DocumentVerification::where('user_id',$user_id)->groupBy('verification_type')->orderBy('id','DESC')->get();
                      if(count($userData) < 3) {
                         $success['Kyc_status'] = 'Not Uploaded';
                      } else {
                      if($userData->isEmpty()) {
                         $success['Kyc_status'] = 'Not Uploaded';
                      } else {
                          $i = 0; foreach($userData as $userData1) {
                              
                              $file = File::where('id',$userData1->file_id)->first();
                              if($file) {
                             
                              $userData[$i]->file_name = $file->originalname;
                              }
                              $userDataStatus[] = $userData1->status;
                          $i++; }
                          
                      if(in_array('pending',$userDataStatus)) {
                          $success['Kyc_status'] = 'pending';
                      } else if(in_array('rejected',$userDataStatus)) {
                          $success['Kyc_status'] = 'rejected';
                      } else if(in_array('approved',$userDataStatus)) {
                          $success['Kyc_status'] = 'approved';
                      }
                      }
                      
                      }
                     
                      
                if($success['Kyc_status'] == 'approved')
                  {
                      
                    $transaction = Transaction::where('user_id', $user_id)->where(function ($query) { $query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('subtotal');
                    //dd($transaction);
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',1)->get();
                    if($transaction >= $limit[1]->daily_limit)
                    {
                        $success['tran_limit_message'] = $user->first_name." exceed own monthly limit!";
                       $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['limit_status']    = 1;
                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                    }else
                    {
                        $success['tran_limit_message'] = "You are eligble for add fund!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['addable_amount'] = $limit[1]->daily_limit-$transaction;
                        $success['limit_status']    = 0;
                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                    }
                  
                   
                  }else
                  {
                      
                   $daily_amount = Transaction::where('user_id', $user_id)->where(function ($query) {$query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->where('created_at', '>=', date('Y-m-d 00:00:00'))->sum('subtotal');
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',0)->get();
                    //dd($limit);
                    if($daily_amount <= $limit[0]->daily_limit)
                     {
                        $success['tran_limit_message'] = "You are eligble for add fund!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['addable_amount'] = $limit[0]->daily_limit-$daily_amount;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['limit_status']    = 0;
                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }else
                     {
                         $success['tran_limit_message'] = $user->first_name." exceed own daily limit!";
                         $success['limit_status']    = 1;
                         $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                         $success['reciver_email'] = $receiver->email;
                         $success['formatted_phone'] = $receiver->formattedPhone;
                         return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }
                      
                   }
                   
                }
                else
                {
                    $success['status'] = $this->unauthorisedStatus;
                    $success['reason']  = 'Not Exist';
                    $success['tran_limit_message'] = 'The receiver email does not exist!';
                    return response()->json(['success' => $success], $this->successStatus);
                }
            }
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }

    public function postRequestMoneyPhoneCheckApi()
    {
        if (request('user_id'))
        {
            $user     = User::where('id', '=', request('user_id'))->first(['id','first_name','formattedPhone']);
            $receiver = User::where('phone', request('receiverPhone'))->where('role_id', '2')->first(['id','formattedPhone','status','first_name','last_name','email']);
            if (!empty($user->formattedPhone))
            {
                if (@$user->formattedPhone == @$receiver->formattedPhone)
                {
                    $success['status']  = $this->unauthorisedStatus;
                    $success['reason']  = 'own-phone';
                    $success['tran_limit_message'] = 'You can not request money to yourself!';
                    return response()->json(['success' => $success], $this->successStatus);
                }
                else
                {
                    if ($receiver)
                    {
                    if ($receiver->status == 'Suspended')
                    {
                        $success['status']  = $this->unauthorisedStatus;
                        $success['reason']  = 'suspended';
                        $success['tran_limit_message'] = 'The recipient is suspended!';
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                    elseif ($receiver->status == 'Inactive')
                    {
                        $success['status']  = $this->unauthorisedStatus;
                        $success['reason']  = 'inactive';
                        $success['tran_limit_message'] = 'The recipient is inactive!';
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                    
                    $userData = DocumentVerification::where('user_id',$user->id)->groupBy('verification_type')->orderBy('id','DESC')->get();
                      if(count($userData) < 3) {
                         $success['Kyc_status'] = 'Not Uploaded';
                      } else {
                      if($userData->isEmpty()) {
                         $success['Kyc_status'] = 'Not Uploaded';
                      } else {
                          $i = 0; foreach($userData as $userData1) {
                              
                              $file = File::where('id',$userData1->file_id)->first();
                              if($file) {
                             
                              $userData[$i]->file_name = $file->originalname;
                              }
                              $userDataStatus[] = $userData1->status;
                          $i++; }
                          
                      if(in_array('pending',$userDataStatus)) {
                          $success['Kyc_status'] = 'pending';
                      } else if(in_array('rejected',$userDataStatus)) {
                          $success['Kyc_status'] = 'rejected';
                      } else if(in_array('approved',$userDataStatus)) {
                          $success['Kyc_status'] = 'approved';
                      }
                      }
                      
                      }
                     
                      
                if($success['Kyc_status'] == 'approved')
                  {
                      
                    $transaction = Transaction::where('user_id', $user->id)->where(function ($query) { $query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('subtotal');
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',1)->get();
                    if($transaction >= $limit[1]->daily_limit)
                    {
                        $success['tran_limit_message'] = $user->first_name." exceed own monthly limit!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['limit_status']    = 1;
                        $success['receiver_id']    =$receiver->id;

                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                    }else
                    {
                        $success['tran_limit_message'] = "You are eligble for add fund!";
                         $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['addable_amount'] = $limit[0]->daily_limit-$transaction;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['limit_status']    = 0;
                        $success['receiver_id']    =$receiver->id;
                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                    }
                  
                   
                  }else
                  {
                      
                   $daily_amount = Transaction::where('user_id', $user->id)->where(function ($query) {$query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->where('created_at', '>=', date('Y-m-d 00:00:00'))->sum('subtotal');
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',0)->get();
                    //dd($limit);
                    if($daily_amount <= $limit[0]->daily_limit)
                     {
                        $success['tran_limit_message'] = "You are eligble for add fund!";
                         $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['addable_amount'] = $limit[0]->daily_limit-$daily_amount;
                        $success['limit_status']    = 0;
                        $success['receiver_id']    =$receiver->id;
                        $success['formatted_phone'] = $receiver->formattedPhone;

                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }else
                     {
                         $success['tran_limit_message'] = $user->first_name." exceed own daily limit!";
                         $success['limit_status']    = 1;
                         $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                         $success['reciver_email'] = $receiver->email;
                         $success['formatted_phone'] = $receiver->formattedPhone;
                         $success['receiver_id']    =$receiver->id;

                         return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }
                      
                   }
                   
                }
                    else
                    {
                        $success['status'] = $this->unauthorisedStatus;
                        $success['reason']  = 'Not Exist';
                        $success['tran_limit_message'] = 'The receiver phone does not  exist!';
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                }
            }
            else
            {
                $success['status']  = $this->notFound;
                $success['tran_limit_message'] = 'Please set your phone number first!';
                return response()->json(['success' => $success], $this->successStatus);
            }
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }

    //Request Payment Currency List
    public function getRequestMoneyCurrenciesApi()
    {
        $user_id                            = request('user_id');
        $currenciesList        = Currency::where(['status' => 'Active'])->orderBy('position','ASC')->get(['id', 'code', 'symbol', 'logo', 'name']);
       // $feesLimitWallet       = FeesLimit::where(['transaction_type_id' => Request_To, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction','min_limit','max_limit','charge_fixed']);
        $feesLimitWallet       = FeesLimit::where(['transaction_type_id' => request('transaction_type_id'), 'has_transaction' => 'Yes'])->get(['currency_id','charge_percentage', 'has_transaction','min_limit','max_limit','charge_fixed']);
        $success['currencies'] = $this->requestWalletList($currenciesList, $feesLimitWallet,$user_id);

        //Set default wallet as selected - starts
        // $user_id                            = request('user_id');
        $defaultWallet                      = Wallet::where(['user_id' => $user_id, 'is_default' => 'Yes'])->first(['currency_id']);
        $success['defaultWalletCurrencyId'] = $defaultWallet->currency_id;
        //Set default wallet as selected - ends

        $success['status'] = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    //Helper Functions Starts here
    public function requestWalletList($currenciesList, $feesLimitWallet,$user_id)
    {
        $selectedWallet = [];
        foreach ($currenciesList as $currency)
        {
            foreach ($feesLimitWallet as $flWallet)
            {
                if ($currency->id == $flWallet->currency_id && $flWallet->has_transaction == 'Yes')
                {
                    
                    $balance=Wallet::where(['user_id' =>$user_id, 'currency_id' => $currency->id])->first()->balance??'00';
                    
                    $selectedWallet[$currency->id]['balance']   = number_format($balance,2);

                    $selectedWallet[$currency->id]['id']     = $currency->id;
                    $selectedWallet[$currency->id]['code']   = $currency->code;
                    $selectedWallet[$currency->id]['symbol'] = $currency->symbol;
                    
                    $selectedWallet[$currency->id]['logo'] = $currency->logo;
                    $selectedWallet[$currency->id]['name'] = $currency->name;
                    // echo("hello1"); die;
                     $selectedWallet[$currency->id]['min_limit'] = $flWallet->min_limit;
                      $selectedWallet[$currency->id]['max_limit'] = $flWallet->max_limit;
                      $selectedWallet[$currency->id]['charge_fixed'] = $flWallet->charge_fixed;
                      $selectedWallet[$currency->id]['charge_percentage'] = $flWallet->charge_percentage;
                      $selectedWallet[$currency->id]['base_url'] = env('CURRENCY_LOGO');
                      
                }
            }
        }
        return array_values($selectedWallet);
    }
    //Helper Functions Ends here

    
    
    
    public function postRequestMoneyPayApi(Request $request)
    {
        $uid                 = request('user_id');
        $emailOrPhone        = request('emailOrPhone');
        $amount              = request('amount');
        $currency_id         = request('currency_id');
        $note                = request('note');
        $uuid                = unique_code();
        $processedBy         = $this->helper->getPrefProcessedBy();
        $emailFilterValidate = $this->helper->validateEmailInput(trim($emailOrPhone));
        $phoneRegex          = $this->helper->validatePhoneInput(trim($emailOrPhone));
        $senderInfo          = User::where(['id' => $uid])->first(['first_name','email']);
        $userInfo            = $this->helper->getEmailPhoneValidatedUserInfo($emailFilterValidate, $phoneRegex, trim($emailOrPhone));
       
        if(($userInfo !=null)||($userInfo !=''))
        {
            $check = Wallet::where(['currency_id'=>$currency_id,'user_id'=>$userInfo->id])->first();
            if(!empty($check))
            {
                $receiverName        = isset($userInfo) ? $userInfo->first_name . ' ' . $userInfo->last_name : '';
                $senderName          = isset($senderInfo) ? $senderInfo->first_name . ' ' . $senderInfo->last_name : '';
                $arr                 = [
                    'unauthorisedStatus'  => $this->unauthorisedStatus,
                    'emailFilterValidate' => $emailFilterValidate,
                    'phoneRegex'          => $phoneRegex,
                    'processedBy'         => $processedBy,
                    'user_id'             => $uid,
                    'userInfo'            => $userInfo,
                    'currency_id'         => $currency_id,
                    'uuid'                => $uuid,
                    'amount'              => $amount,
                    'receiver'            => $emailOrPhone,
                    'note'                => $note,
                    'receiverName'        => $receiverName,
                    'receierEmail'        => $userInfo->email,
                    'senderEmail'         => $senderInfo->email,
                    'senderInfo'          => $senderInfo,
                    'senderName'          => $senderName,
                    'local_tran_time'    => $request->local_tran_time,
                    'device_id' => $request->device_id, 
                    'app_ver' => $request->app_ver, 
                    'device_name' => $request->device_name, 
                    'device_manufacture' => $request->device_manufacture, 
                    'device_model' => $request->device_model, 
                    'os_ver' => $request->os_ver, 
                    'device_os' => $request->device_os, 
                    'ip_address' => request()->ip(),
                ];
                
                //Pending Transaction
                $pending_transaction                           = new PendingTransaction();
                $pending_transaction->user_id                  = $uid;
                $pending_transaction->end_user_id              = $arr['receiver'];
                $pending_transaction->currency_id              = $arr['currency_id'];
                $pending_transaction->payment_method_id        = null;
                $pending_transaction->transaction_reference_id = $arr['uuid'];
                $pending_transaction->transaction_type_id      = Request_From;
                $pending_transaction->uuid                     = $arr['uuid'];
                $pending_transaction->subtotal                 = $arr['amount'];
                $pending_transaction->percentage               = null;
                $pending_transaction->charge_percentage        = null;
                $pending_transaction->charge_fixed             = null;
                $pending_transaction->total                    = $arr['amount'];
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
               
                $response = $this->requestPayment->processRequestCreateConfirmation($arr, 'mobile');
                $ticket_code = $response['transactionOrReqPaymentId'];
                $request_status = DB::table('transactions')->where(['transaction_reference_id' => $response['transactionOrReqPaymentId']])->first();
                if ($response['status'] != 200)
                {
                    if (empty($response['transactionOrReqPaymentId']))
                    {
                        return response()->json([
                            'status' => false,
                        ]);
                    }
                    return response()->json([
                        'status'                       => true,
                        'requestMoneyMailErrorMessage' => $response['ex']['message'],
                        'tr_ref_id'                    => $response['transactionOrReqPaymentId'],
                        
                    ]);
                }
                
        	    $adminAllowed = Notification::has_permission([1]);
                foreach($adminAllowed as $admins){
                    Notification::insert([
                        'user_id'               => $uid,
                        'notification_to'       => 1,
                        'notification_type_id'  => 4,
                        'notification_type'     => 'App',
                        'description'           => "User ".$senderInfo->first_name." has requested ".$this->helper->getcurrencyCode($currency_id). " ".$amount." "."from ".$userInfo->first_name??'',
                        'url_to_go'             => 'admin/request_payments/edit/'.$response['transactionOrReqPaymentId'],
                        'local_tran_time'       => $request->local_tran_time
                    ]);
                }
                
                // For Sender
                $sender_device = DB::table('devices')->where('user_id', $uid)->first();
                if(!empty($sender_device)){
                    $sender_template = NotificationTemplate::where('temp_id', '11')->where('language_id', $sender_device->language)->first();
                    $sender_subject = $sender_template->title;
                    $sender_subheader = $sender_template->subheader;
                    $sender_message = $sender_template->content;
                    
                    $send_msg = str_replace('{receiver}', $userInfo->first_name, $sender_message);
                    $send_msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $send_msg);
                    $send_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $send_msg);
                    
                    $currency = request('currency_id');
                    $type = "requestmoney";
                    $date      = date("Y-d-m h:i");
                    
                    $this->helper->sendFirabasePush($sender_subject, $send_msg, $uid, $currency, $type);
                    
                    Noticeboard::create([
                        'tr_id' => $response['sender_transaction'],
                        'title' => $sender_subject,
                        'content' => $send_msg,
                        'type' => 'push',
                        'content_type' => 'requestmoney',
                        'user' => $uid,
                        'sub_header' => $sender_subheader,
                        'push_date' => $request->local_tran_time,
                        'template' => '11',
                        'language' => $sender_device->language,
                        'currency' => $this->helper->getcurrencyCode($currency_id),
                        'amount' => number_format($amount, 2, '.', ','),
                        'receiver' => $userInfo->first_name,
                    ]);
                }
            	
            	// For Reciver
            	$receiver_device = DB::table('devices')->where('user_id', $userInfo->id)->first();
            	if(!empty($receiver_device)){
                	$receiver_templates = NotificationTemplate::where('temp_id', '4')->where('language_id', $receiver_device->language)->first();
                    $receiver_subject = $receiver_templates->title;
                    $receiver_subheader = $receiver_templates->subheader;
                    $receiver_message = $receiver_templates->content;
                    
                    $receiver_subhead = str_replace('{sender}', $senderInfo->first_name, $receiver_subheader);
                    $receiver_subhead = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $receiver_subhead);
                    $receiver_subhead = str_replace('{amount}', number_format($amount, 2, '.', ','), $receiver_subhead);
                    
                    $receive_msg = str_replace('{sender}', $senderInfo->first_name, $receiver_message);
                    $receive_msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $receive_msg);
                    $receive_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $receive_msg);
                    
                    $currency = request('currency_id');
                    $type = "requestmoney";
                    $email = trim($senderInfo->email);
                    $this->helper->sendFirabasePush($receiver_subject, $receive_msg, $userInfo->id, $currency, $type);
                    
                    Noticeboard::create([
                        'tr_id' => $response['receiver_transaction'],
                        'title' => $receiver_subject,
                        'content' => $receive_msg,
                        'type' => 'push',
                        'content_type' => 'sendmoney',
                        'user' => $userInfo->id,
                        'sub_header' => $receiver_subhead,
                        'push_date' => $request->local_tran_time,
                        'template' => '4',
                        'language' => $receiver_device->language,
                        'currency' => $this->helper->getcurrencyCode($currency_id),
                        'amount' => number_format($amount, 2, '.', ','),
                        'sender' => $senderInfo->first_name,
                    ]);
            	}
                
                return response()->json([
                    'status'    => true,
                    'tr_ref_id' => $response['transactionOrReqPaymentId'],
                    'senderEmail'         => $senderInfo->email,
                ]);
            }else
            {
                return response()->json([
                    'status'    => '402',
                    'tr_ref_id' => '',
                    'message'   => 'Invalid Currency',
                ]);
            }
        }else
        {
            return response()->json([
                'status'    => false,
                'tr_ref_id' => '',
                'message'   => 'Sorry this customer not found!',
            ]);
        }
    }


    //Check Request Creator Status (for dashboard and transactions list - user panel)
    public function checkReqCreatorStatusApi(Request $request)
    
    {
        // dd(request()->all());
        try
        {
            $transaction                        = Transaction::with(['end_user:id,status'])->find($request->trans_id, ['id', 'end_user_id']);
            $success['status']                  = $this->successStatus;
            $success['transaction-user-status'] = $transaction->end_user->status;
            return response()->json(['success' => $success], $this->successStatus);
        }
        catch (\Exception $e)
        {
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage();
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    //Request Money Ends here
    
    //Accept Requested Money
    public function acceptmoney(Request $request)
    {
        // dd(request()->all());
         
        $user_id             = request('user_id');
        $emailOrPhone        = request('emailOrPhone');
        $currency_id         = request('currency_id');
        $amount              = request('amount');
        $totalFees           = request('totalFees');
        $total_with_fee      = $amount + $totalFees;
        $note                = request('note');
        $requestPaymentId    = request('requestPaymentId');
        $unique_code         = unique_code();
        $emailFilterValidate = $this->helper->validateEmailInput($emailOrPhone);
        $phoneRegex          = $this->helper->validatePhoneInput($emailOrPhone);
        $processedBy         = $this->helper->getPrefProcessedBy();
        $feesDetails         = $this->helper->getFeesLimitObject([], Transferred, $currency_id, null, null, ['charge_percentage', 'charge_fixed']);
        $user                = User::where(['id' => $user_id])->first(['email']);
        $senderWallet        = $this->helper->getUserWallet([], ['user_id' => $user_id, 'currency_id' => $currency_id], ['id', 'balance']);
        $userInfo            = $this->helper->getEmailPhoneValidatedUserInfo($emailFilterValidate, $phoneRegex, trim($emailOrPhone));

        $arr = [
            'unauthorisedStatus'  => null,
            'emailFilterValidate' => $emailFilterValidate,
            'phoneRegex'          => $phoneRegex,
            'processedBy'         => $processedBy,
            'requestPaymentId'    => $requestPaymentId,
            'currency_id'         => $currency_id,
            'user_id'             => $user_id,
            'accept_amount'       => $amount,
            'charge_percentage'   => $feesDetails->charge_percentage,
            'percentage_fee'      => $amount * ($feesDetails->charge_percentage / 100),
            'fixed_fee'           => $feesDetails->charge_fixed,
            'fee'                 => $totalFees,
            'total'               => $total_with_fee,
            'senderWallet'        => $senderWallet,
        ];
        //dd($arr);
        //Get response
        $response = $this->requestPayment->processRequestAcceptConfirmation($arr, 'mobile');
         //dd($response);
        if ($response['status'] != 200)
        {
            if (empty($response['transactionOrReqPaymentId']))
            {
                // dd($response['transactionOrReqPaymentId']);
                return response()->json([
                    'status'                          => false,
                    'requestMoneyValidationErrorMessage' => $response['ex']['message'],
                ]);
            }
            return response()->json([
                'status'                    => true,
                'sendMoneyMailErrorMessage' => $response['ex']['message'],
                'tr_ref_id'                 => $response['transactionOrReqPaymentId'],
            ]);
        }
        
         $userdevices               = DB::table('devices')->where(['user_id' => $user_id])->first();
         if(isset($userdevices) && $userdevices->fcm_token)
            {
                $msg= 'Your transaction of amount '.$amount. ' to '.trim($emailOrPhone) .' successfully done.';
            	//echo "<pre>"; print_r($userdevices); die;
            	$notifyData   = array (
            	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
            	'title'         => 'Payment done Successfully!',
            	'content'       => $msg,
            	'type'          => 'Message',
            	// Require for auto fetch incoming request push.
            	'payload'       => array (//'post' => $data->created_at
            		)
            	);
            	$datanotice= array('title'=>'Payment done Successfully','content'=>$msg,'type'=>'push','content_type'=>'sendmoney','user'=>$user_id);
            	DB::table('noticeboard')->insert($datanotice);
            	$this->helper->sendFCMPush($notifyData);
            }
            
            $wallet            = new Wallet();
            $wallets           = $wallet->getAvailableBalance($user_id);
            
        return response()->json([
            'status'    => true,
            'balance'               => $wallets,
        ]);
    }
    //Accept Requested Money Ends here
    
    //Cancel from request acceptor
    public function cancelmoney(Request $request)
    {
        // dd(request()->all());
        $requestPaymentId = $request->requestPaymentId;
        $user_id          = request('user_id');
        try
        {
            \DB::beginTransaction();
            $TransactionA         = Transaction::where('transaction_reference_id', $requestPaymentId)->where('user_id', $user_id)->first(); //TODO: query optimization
            $TransactionA->status = "Blocked";
            $TransactionA->save();

            $transaction_type_id = $TransactionA->transaction_type_id == Request_To ? Request_From : Request_To;
            $TransactionB        = Transaction::where([
                'transaction_reference_id' => $TransactionA->transaction_reference_id,
                'transaction_type_id'      => $transaction_type_id])->first(); //TODO: query optimization
            $TransactionB->status = "Blocked";
            $TransactionB->save();

            $RequestPayment         = RequestPayment::find($TransactionA->transaction_reference_id); //TODO: query optimization
            $RequestPayment->status = "Blocked";
            $RequestPayment->save();
            
            $this->sendRequestCancelNotificationToAcceptorOrCreator($RequestPayment, $request->notificationType); //TODO: query optimization
            \DB::commit();
            return response()->json([
                'status' => $this->successStatus,
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage();
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }

    //Cancel from request creator
    
    //Start search from Phonebook
    public function phonebook(Request $request)
    { 
        
        //dd(request()->all());
        $phone         = request('phone');
        try
        {
            $data         = User::where('phone', $phone)->orWhere('formattedPhone',$phone)->first();
            if($data){
                return response()->json([
                    'status' => 'true',
                ]);
            }else{
                return response()->json([
                    'status' => 'false',
                ]);
                
            }
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage();
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }

    //End search from Phonebook
    
     public function sendRequestCancelNotificationToAcceptorOrCreator($RequestPayment, $notificationType)
    {
        $processedBy         = $this->helper->getPrefProcessedBy();
        $emailFilterValidate = $this->helper->validateEmailInput($notificationType);
        $phoneRegex          = $this->helper->validatePhoneInput($notificationType);

        $soft_name = session('name');

        $messageFromCreatorToAcceptor = 'Your request payment #' . $RequestPayment->uuid . ' of ' . moneyFormat($RequestPayment->currency->symbol, formatNumber($RequestPayment->amount)) . ' has been cancelled by ' .
        $RequestPayment->user->first_name . ' ' . $RequestPayment->user->last_name . '.';

        //////////////////////////////////////////////////////////////////////////
        if ($emailFilterValidate && $processedBy == "email")
        {
            if (auth()->user()->id == $RequestPayment->user_id)
            {
                if (!empty($RequestPayment->receiver_id))
                {
                    //ok
                    $data = $this->onlyEmailToRegisteredRequestReceiver($messageFromCreatorToAcceptor,
                        $RequestPayment->receiver->first_name, $RequestPayment->receiver->last_name, $soft_name, $RequestPayment->receiver->email);
                    return $data;
                }
                else
                {
                    //ok
                    $data = $this->onlyEmailToUnregisteredRequestReceiver($messageFromCreatorToAcceptor, $soft_name, $RequestPayment->email);
                    return $data;
                }
            }
            elseif (!empty($RequestPayment->receiver_id) && auth()->user()->id == $RequestPayment->receiver_id)
            {
                //ok
                $messageFromAcceptorToCreator = 'Your request payment #' . $RequestPayment->uuid . ' of ' . moneyFormat($RequestPayment->currency->symbol, formatNumber($RequestPayment->amount)) .
                ' has been cancelled by ' . $RequestPayment->receiver->first_name . ' ' . $RequestPayment->receiver->last_name . '.';
                $data = $this->onlyEmailToRequestCreator($messageFromAcceptorToCreator, $RequestPayment->user->first_name, $RequestPayment->user->last_name, $soft_name, $RequestPayment->user->email);
                return $data;
            }
        }
        elseif ($phoneRegex && $processedBy == "phone")
        {
            if (auth()->user()->id == $RequestPayment->user_id)
            {
                if (!empty($RequestPayment->receiver_id))
                {
                    $data = $this->onlySmsToRegisteredRequestReceiver($messageFromCreatorToAcceptor,
                        $RequestPayment->receiver->first_name, $RequestPayment->receiver->last_name, $soft_name, $RequestPayment->receiver->carrierCode, $RequestPayment->receiver->phone);
                    return $data;
                }
                else
                {
                    $data = $this->onlySmsToUnregisteredRequestReceiver($messageFromCreatorToAcceptor, $soft_name, $RequestPayment->phone);
                    return $data;
                }
            }
            elseif (!empty($RequestPayment->receiver_id) && auth()->user()->id == $RequestPayment->receiver_id)
            {
                $messageFromAcceptorToCreator = 'Your request payment #' . $RequestPayment->uuid . ' of ' . moneyFormat($RequestPayment->currency->symbol, formatNumber($RequestPayment->amount)) .
                ' has been cancelled by ' . $RequestPayment->receiver->first_name . ' ' . $RequestPayment->receiver->last_name . '.';
                $data = $this->onlySmsToRequestCreator($messageFromAcceptorToCreator, $RequestPayment->user->first_name, $RequestPayment->user->last_name, $soft_name,
                    $RequestPayment->user->carrierCode, $RequestPayment->user->phone);
                return $data;
            }
        }
        elseif ($processedBy == "email_or_phone")
        {
            if ($emailFilterValidate)
            {
                if (auth()->user()->id == $RequestPayment->user_id)
                {
                    if (!empty($RequestPayment->receiver_id))
                    {
                        $data = $this->onlyEmailToRegisteredRequestReceiver($messageFromCreatorToAcceptor,
                            $RequestPayment->receiver->first_name, $RequestPayment->receiver->last_name, $soft_name, $RequestPayment->receiver->email);
                        return $data;
                    }
                    else
                    {
                        $data = $this->onlyEmailToUnregisteredRequestReceiver($messageFromCreatorToAcceptor, $soft_name, $RequestPayment->email);
                        return $data;
                    }
                }
                elseif (!empty($RequestPayment->receiver_id) && auth()->user()->id == $RequestPayment->receiver_id)
                {
                    $messageFromAcceptorToCreator = 'Your request payment #' . $RequestPayment->uuid . ' of ' . moneyFormat($RequestPayment->currency->symbol, formatNumber($RequestPayment->amount)) .
                    ' has been cancelled by ' . $RequestPayment->receiver->first_name . ' ' . $RequestPayment->receiver->last_name . '.';
                    $data = $this->onlyEmailToRequestCreator($messageFromAcceptorToCreator, $RequestPayment->user->first_name, $RequestPayment->user->last_name, $soft_name, $RequestPayment->user->email);
                    return $data;
                }
            }
            elseif ($phoneRegex)
            {
                if (auth()->user()->id == $RequestPayment->user_id)
                {
                    if (!empty($RequestPayment->receiver_id))
                    {
                        $data = $this->onlySmsToRegisteredRequestReceiver($messageFromCreatorToAcceptor,
                            $RequestPayment->receiver->first_name, $RequestPayment->receiver->last_name, $soft_name, $RequestPayment->receiver->carrierCode, $RequestPayment->receiver->phone);
                        return $data;
                    }
                    else
                    {
                        $data = $this->onlySmsToUnregisteredRequestReceiver($messageFromCreatorToAcceptor, $soft_name, $RequestPayment->phone);
                        return $data;
                    }
                }
                elseif (!empty($RequestPayment->receiver_id) && auth()->user()->id == $RequestPayment->receiver_id)
                {
                    $messageFromAcceptorToCreator = 'Your request payment #' . $RequestPayment->uuid . ' of ' . moneyFormat($RequestPayment->currency->symbol, formatNumber($RequestPayment->amount)) .
                    ' has been cancelled by ' . $RequestPayment->receiver->first_name . ' ' . $RequestPayment->receiver->last_name . '.';
                    $data = $this->onlySmsToRequestCreator($messageFromAcceptorToCreator, $RequestPayment->user->first_name, $RequestPayment->user->last_name, $soft_name,
                        $RequestPayment->user->carrierCode, $RequestPayment->user->phone);
                    return $data;
                }
            }
        }
        //////////////////////////////////////////////////////////////////////////
    }
    
    public function GetFiveRequest(Request $request)
    {
        $user_id = request('user_id');
        if($user_id == '')
        {
             return response()->json([
            'status'    => false,
            'message'   => "User ID required!"
        ]);
        }else
        {
            $tran = RequestPayment::where(['user_id'=>$user_id])->select('user_id','receiver_id','currency_id','amount','note')->orderBy('id','desc')->paginate(5);
          
            foreach($tran as $value)
            {
                $data = User::where('id',$value->receiver_id)->first();
                //dd($data);
                if((!empty($data))||($data != null ))
                {
                    $value->image = $data->picture??'';
                    $value->email = $data->email??'';
                    $value->receiver_name = $data->first_name.' '. $data->last_name??'';
                    $value->currency_code = $this->helper->getcurrencyCode($value->currency_id);
                    $value->formatted_phone = $data->formattedPhone??'';
                }else
                {
                    $value->image = '';
                    $value->email = '';
                    $value->receiver_name = '';
                    $value->formatted_phone='';
                    $value->currency_code = $this->helper->getcurrencyCode($value->currency_id);
                }
               
            }
              
           return response()->json([
            'status'    => true,
            'message'   => 'Get last five request transaction',
            'transaction' => $tran,
        ]);
        }
    }
    
   
    
}
