<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\FeesLimit;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\DocumentVerification;
use Illuminate\Http\Request;
use App\Models\Transaction;
use DB;
use Carbon\Carbon;
use App\Models\File;
use App\Models\Notification;
use App\Models\PendingTransaction;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class SendMoneyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $notFound           = 404;
    protected $helper;
    protected $transfer;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->transfer = new Transfer();
    }

    //Send Money Starts here
    public function postSendMoneyEmailCheckApi()
    {
        if (request('user_id'))
        {
            $user_id       = request('user_id');
            $receiverEmail = request('receiverEmail');
            $user_type     = request('user_type');
            $user          = User::where('id', '=', $user_id)->first(['email']);
            $receiver      = User::where('email', $receiverEmail)->where('role_id', $user_type)->first(['id','email','status','first_name','last_name','formattedPhone']);

            if (@$user->email == @$receiver->email)
            {
                $success['status']  = $this->unauthorisedStatus;
                $success['reason']  = 'own-email';
                $success['tran_limit_message'] = 'You cannot send money to yourself!';
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
                    
                    $userData = DocumentVerification::where('user_id',$receiver->id)->groupBy('verification_type')->orderBy('id','DESC')->get();
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
                      
                    $transaction = Transaction::where('user_id', $receiver->id)->where(function ($query) { $query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('subtotal');
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',1)->get();
                    if($transaction >= $limit[1]->daily_limit)
                    {
                        $success['tran_limit_message'] = $receiver->first_name." exceed own monthly limit!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['reason']  = 'monthly';
                        
                        $success['limit_status']    = 1;
                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                    }else
                    {
                        $success['tran_limit_message'] = "You are eligble for add fund!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['addable_amount'] = $limit[1]->daily_limit-$transaction;
                        $success['limit_status']    = 0;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                    }
                  
                   
                  }else
                  {
                      
                   $daily_amount = Transaction::where('user_id', $receiver->id)->where(function ($query) {$query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->where('created_at', '>=', date('Y-m-d 00:00:00'))->sum('subtotal');
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
                         $success['tran_limit_message'] = $receiver->first_name." exceed own daily limit!";
                         $success['limit_status']    = 1;
                         $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                         $success['reciver_email'] = $receiver->email;
                         $success['formatted_phone'] = $receiver->formattedPhone;
                         $success['reason']  = 'daily';
                         return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }
                      
                   }
                   
                }
                else
                {
                    $success['status'] = $this->unauthorisedStatus;
                    $success['reason']  = 'Not-Exist';
                    $success['tran_limit_message'] = 'The receiver email does not  exist!';
                    return response()->json(['success' => $success], $this->successStatus);
                }
            }
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }

    public function postSendMoneyPhoneCheckApi()
    {
        $user_type     = request('user_type');
        if (request('user_id'))
        {
            $user     = User::where('id', '=', request('user_id'))->first(['formattedPhone']);
            $receiver = User::where('phone', request('receiverPhone'))->where('role_id', '2')->first(['id','formattedPhone','status','first_name','last_name','email']);
            if (!empty($user->formattedPhone))
            {
                if (@$user->formattedPhone == @$receiver->formattedPhone)
                {
                    $success['status']  = $this->unauthorisedStatus;
                    $success['reason']  = 'own-phone';
                    $success['tran_limit_message'] = 'You cannot send money to yourself!';
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
                    
                    $userData = DocumentVerification::where('user_id',$receiver->id)->groupBy('verification_type')->orderBy('id','DESC')->get();
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
                      
                    $transaction = Transaction::where('user_id', $receiver->id)->where(function ($query) { $query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('subtotal');
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',1)->get();
                    if($transaction >= $limit[1]->daily_limit)
                    {
                        $success['tran_limit_message'] = $receiver->first_name." exceed own monthly limit!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;

                        
                        $success['reason']  = 'monthly';
                        $success['limit_status']    = 1;
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
                      
                   $daily_amount = Transaction::where('user_id', $receiver->id)->where(function ($query) {$query->where('transaction_type_id', '=', 1)->orWhere('transaction_type_id', '=', 4);})->where('created_at', '>=', date('Y-m-d 00:00:00'))->sum('subtotal');
                    $limit       = DB::table('set_kyc_limit')->where('is_kyc',0)->get();
                    //dd($limit);
                    if($daily_amount <= $limit[0]->daily_limit)
                     {
                        $success['tran_limit_message'] = "You are eligble for add fund!";
                        $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                        $success['reciver_email'] = $receiver->email;
                        $success['formatted_phone'] = $receiver->formattedPhone;
                        $success['addable_amount'] = $limit[0]->daily_limit-$daily_amount;
                        $success['limit_status']    = 0;
                        $success['receiver_id']    =$receiver->id;

                        return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }else
                     {
                         $success['tran_limit_message'] = $receiver->first_name." exceed own daily limit!";
                         $success['limit_status']    = 1;
                         $success['reciver_name']  = $receiver->first_name.' '.$receiver->last_name;
                         $success['reciver_email'] = $receiver->email;
                         $success['formatted_phone'] = $receiver->formattedPhone;
                         $success['reason']  = 'daily';
                         return response()->json(['status'=>$this->successStatus,'success' => $success]);
                     }
                      
                   }
                   
                }
                    else
                    {
                        $success['status'] = $this->unauthorisedStatus;
                        $success['reason']  = 'Not-Exist';
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

    public function getSendMoneyCurrenciesApi()
    {
        // dd(request()->all());
        $user_id = request('user_id');

        /*Check Whether Currency is Activated in feesLimit*/
        $walletList                      = Wallet::with('currency:id,code,symbol')->where(['user_id' => $user_id])->whereHas('active_currency')->get(['currency_id', 'is_default','balance']);
        $checkWhetherCurrencyIsActivated = FeesLimit::where(['transaction_type_id' => Transferred, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        // $success['currencies']           = $this->walletList($walletList, $checkWhetherCurrencyIsActivated);
        // $success['status']               = $this->successStatus;
        return response()->json(['status'=>$this->successStatus,'currencies'=>$this->walletList($walletList, $checkWhetherCurrencyIsActivated)], $this->successStatus);
    }

    //Helper Functions Starts here
    public function walletList($activeWallet, $feesLimitWallet)
    {
        $selectedWallet = [];
        foreach ($activeWallet as $aWallet)
        {
            foreach ($feesLimitWallet as $flWallet)
            {
                if ($aWallet->currency_id == $flWallet->currency_id && $flWallet->has_transaction == 'Yes')
                {
                    $activeCurrency                     = Currency::where(['status' => 'Active'])->where('id',$aWallet->currency_id)->orderBy('position','ASC')->get(['logo','name','position','symbol']);
                    $curr = json_decode($activeCurrency, true);
                    $logo = $curr['0']['logo'];
                    $name = $curr['0']['name'];
                    $symbol = $curr['0']['symbol'];
                    $position = $curr['0']['position'];
                    $selectedWallet[$aWallet->currency_id]['id']         = $aWallet->currency_id;
                    $selectedWallet[$aWallet->currency_id]['code']       = $aWallet->currency->code;
                    $selectedWallet[$aWallet->currency_id]['is_default'] = $aWallet->is_default;
                    $selectedWallet[$aWallet->currency_id]['logo'] = $logo;
                    $selectedWallet[$aWallet->currency_id]['symbol'] = $symbol;
                    $selectedWallet[$aWallet->currency_id]['name'] = $name;
                    $selectedWallet[$aWallet->currency_id]['position'] = $position;
                     $selectedWallet[$aWallet->currency_id]['min_limit'] = $flWallet->min_limit;
                      $selectedWallet[$aWallet->currency_id]['max_limit'] = $flWallet->max_limit;
                      $selectedWallet[$aWallet->currency_id]['charge_fixed'] = $flWallet->charge_fixed;
                      $selectedWallet[$aWallet->currency_id]['charge_percentage'] = $flWallet->charge_percentage;
                      $selectedWallet[$aWallet->currency_id]['base_url'] = env('CURRENCY_LOGO');
                      $selectedWallet[$aWallet->currency_id]['balance'] = number_format($aWallet->balance,2);

                }
            }
        }
        return array_values($selectedWallet);
    }
    //Helper Functions Ends here

    public function postSendMoneyFeesAmountLimitCheckApi()
    {
        // dd(request()->all());
        $currency_id = request('sendCurrency');
        $user_id     = request('user_id');
        $amount      = request('sendAmount');
        $feesDetails = $this->helper->getFeesLimitObject(['currency:id,code,symbol'],Transferred, $currency_id, null, null, ['charge_percentage', 'charge_fixed', 'currency_id', 'min_limit', 'max_limit']);;
        //dd($feesDetails);
        //Wallet Balance Limit Check Starts here
        $feesPercentage      = $amount * ($feesDetails->charge_percentage / 100);
        $checkAmountWithFees = $amount + $feesDetails->charge_fixed + $feesPercentage;
        $wallet              = $this->helper->getUserWallet([],['user_id' => $user_id, 'currency_id' => $currency_id], ['balance']);
        if (@$wallet)
        {
            if ((@$checkAmountWithFees) > (@$wallet->balance) || (@$wallet->balance < 0))
            {
                $success['reason']  = 'insufficientBalance';
                $success['message'] = "Sorry, not enough funds to perform the operation!";
                $success['status']  = '400';
                return response()->json(['success' => $success], $this->successStatus);
            }
        }
        //Wallet Balance Limit Check Ends here

        //Amount Limit Check Starts here
        if (@$feesDetails)
        {
            if (@$feesDetails->max_limit == null)
            {
                if ((@$amount < @$feesDetails->min_limit))
                {
                    $success['reason']   = 'minLimit';
                    $success['minLimit'] = @$feesDetails->min_limit;
                    $success['message']  = 'Minimum amount ' . number_format($feesDetails->min_limit, 2, '.', '');
                    $success['status']   = '401';
                }
                else
                {
                    $feesPercentage                = $amount * ($feesDetails->charge_percentage / 100);
                    $feesFixed                     = $feesDetails->charge_fixed;
                    $totalFess                     = $feesPercentage + $feesFixed;
                    $totalAmount                   = $amount + $totalFess;
                    $success['sendAmount']         = $amount;
                    $success['sendCurrency']       = $currency_id;
                    $success['totalFees']          = $totalFess;
                    $success['sendAmountDisplay']  = number_format($amount, 2, '.', '');
                    $success['totalFeesDisplay']   = number_format($totalFess, 2, '.', '');
                    $success['totalAmountDisplay'] = number_format($totalAmount, 2, '.', '');
                    $success['currCode']           = $feesDetails->currency->code;
                    $success['currSymbol']         = $feesDetails->currency->symbol;
                    $success['status']             = $this->successStatus;
                }
            }
            else
            {
                if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit))
                {
                    $success['reason']   = 'minMaxLimit';
                    $success['minLimit'] = @$feesDetails->min_limit;
                    $success['maxLimit'] = @$feesDetails->max_limit;
                    $success['message']  = 'Minimum amount ' . number_format($feesDetails->min_limit, 2, '.', '') . ' and Maximum amount ' . number_format($feesDetails->max_limit, 2, '.', '');
                    $success['status']   = '401';
                }
                else
                {
                    $feesPercentage                = $amount * ($feesDetails->charge_percentage / 100);
                    $feesFixed                     = $feesDetails->charge_fixed;
                    $totalFess                     = $feesPercentage + $feesFixed;
                    $totalAmount                   = $amount + $totalFess;
                    $success['sendAmount']         = $amount;
                    $success['sendCurrency']       = $currency_id;
                    $success['totalFees']          = $totalFess;
                    $success['sendAmountDisplay']  = number_format($amount, 2, '.', '');
                    $success['totalFeesDisplay']   = number_format($totalFess, 2, '.', '');
                    $success['totalAmountDisplay'] = number_format($totalAmount, 2, '.', '');
                    $success['currCode']           = $feesDetails->currency->code;
                    $success['currSymbol']         = $feesDetails->currency->symbol;
                    $success['status']             = $this->successStatus;
                }
            }
            return response()->json(['success' => $success], $this->successStatus);
        }
        else
        {
            $feesPercentage                = 0;
            $feesFixed                     = 0;
            $totalFess                     = $feesPercentage + $feesFixed;
            $totalAmount                   = $amount + $totalFess;
            $success['sendAmount']         = $amount;
            $success['sendCurrency']       = $currency_id;
            $success['totalFees']          = $totalFess;
            $success['sendAmountDisplay']  = number_format($amount, 2, '.', '');
            $success['totalFeesDisplay']   = number_format($totalFess, 2, '.', '');
            $success['totalAmountDisplay'] = number_format($totalAmount, 2, '.', '');
            $success['currCode']           = $feesDetails->currency->code;
            $success['currSymbol']         = $feesDetails->currency->symbol;
            $success['status']             = $this->successStatus;
            return response()->json(['success' => $success], $this->successStatus);
        }
        //Amount Limit Check Ends here
    }

    public function postSendMoneyPayApi(Request $request)
    {
        $user_id             = request('user_id');
        $emailOrPhone        = request('emailOrPhone');
        $currency_id         = request('currency_id');
        $amount              = request('amount');
        $totalFees           = request('totalFees');
        $total_with_fee      = $amount + $totalFees;
        $note                = request('note');
        $user_type           = request('user_type');
        $unique_code         = unique_code();
        $emailFilterValidate = $this->helper->validateEmailInput($emailOrPhone);
        $phoneRegex          = $this->helper->validatePhoneInput($emailOrPhone);
        $processedBy         = $this->helper->getPrefProcessedBy();
        $feesDetails         = $this->helper->getFeesLimitObject([], Transferred, $currency_id, null, null, ['charge_percentage', 'charge_fixed']);
        $user                = User::where(['id' => $user_id])->first(['first_name','email']);
        $senderWallet        = $this->helper->getUserWallet([], ['user_id' => $user_id, 'currency_id' => $currency_id], ['id', 'balance']);
        $userInfo            = $this->helper->getEmailPhoneValidatedUserInfoNew($emailFilterValidate, $phoneRegex, trim($emailOrPhone), $user_type);

        $arr = [
            'emailFilterValidate' => $emailFilterValidate,
            'phoneRegex'          => $phoneRegex,
            'processedBy'         => $processedBy,
            'user_id'             => $user_id,
            'userInfo'            => $userInfo,
            'currency_id'         => $currency_id,
            'uuid'                => $unique_code,
            'fee'                 => $totalFees,
            'amount'              => $amount,
            'note'                => trim($note),
            'receiver'            => trim($emailOrPhone),
            'charge_percentage'   => $feesDetails->charge_percentage,
            'charge_fixed'        => $feesDetails->charge_fixed,
            'p_calc'              => $amount * ($feesDetails->charge_percentage / 100),
            'total'               => $total_with_fee,
            'senderWallet'        => $senderWallet,
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
        $pending_transaction->user_id                  = $user_id;
        $pending_transaction->end_user_id              = $arr['receiver'];
        $pending_transaction->currency_id              = $arr['currency_id'];
        $pending_transaction->payment_method_id        = null;
        $pending_transaction->transaction_reference_id = $arr['uuid'];
        $pending_transaction->transaction_type_id      = Transferred;
        $pending_transaction->uuid                     = $arr['uuid'];
        $pending_transaction->subtotal                 = $arr['amount'];
        $pending_transaction->percentage               = $feesDetails->charge_percentage;
        $pending_transaction->charge_percentage        = $feesDetails->charge_percentage;
        $pending_transaction->charge_fixed             = $feesDetails->charge_fixed;
        $pending_transaction->total                    = $arr['total'];
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
        $response = $this->transfer->processSendMoneyConfirmation($arr, 'mobile');
      
        if (!empty($response['ex']['message']) || $response['status'] != 200)
        {
            if (empty($response['transactionOrTransferId']))
            {
                // dd($response['transactionOrTransferId']);
                return response()->json([
                    'status'                          => false,
                    'sendMoneyValidationErrorMessage' => $response['ex']['message'],
                ]);
            }
            return response()->json([
                'status'                    => true,
                'sendMoneyMailErrorMessage' => $response['ex']['message'],
                'tr_ref_id'                 => $unique_code,
            ]);
        }
        
        $adminAllowed = Notification::has_permission([1]);
        foreach($adminAllowed as $admins){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => 1,
                'notification_type_id'  => 3,
                'notification_type'     => 'App',
                'description'           => "User ".$user->first_name." has sent ".$this->helper->getcurrencyCode($currency_id). " ".$amount." "."to ".$userInfo->first_name??'',
                'url_to_go'             => 'admin/transfers/edit/'.$response['transactionOrTransferId'],
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
        
        // For Sender
        $sender_device = DB::table('devices')->where('user_id', $user_id)->first();
        $sender_template = NotificationTemplate::where('temp_id', '3')->where('language_id', $sender_device->language)->first();
        $sender_subject = $sender_template->title;
        $sender_subheader = $sender_template->subheader;
        $sender_message = $sender_template->content;
        
        $send_msg = str_replace('{receiver}', $userInfo->first_name, $sender_message);
        $send_msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $send_msg);
        $send_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $send_msg);
        
        $date      = date("Y-d-m h:i");
        $currency  = request('currency_id');
        $type      = "sendmoney";
        $this->helper->sendFirabasePush($sender_subject, $send_msg, $user_id, $currency, $type);
        
        Noticeboard::create([
            'tr_id' => $response['sender_transaction'],
            'title' => $sender_subject,
            'content' => $send_msg,
            'type' => 'push',
            'content_type' => 'sendmoney',
            'user' => $user_id,
            'sub_header' => $sender_subheader,
            'push_date' => $request->local_tran_time,
            'template' => '3',
            'language' => $sender_device->language,
            'currency' => $this->helper->getcurrencyCode($currency_id),
            'amount' => number_format($amount, 2, '.', ','),
            'receiver' => $userInfo->first_name,
        ]);
    	
    	// For Reciver
    	$receiver_device = DB::table('devices')->where('user_id', $userInfo->id)->first();
    	if(!empty($receiver_device)){
        	$receiver_templates = NotificationTemplate::where('temp_id', '10')->where('language_id', $receiver_device->language)->first();
            $receiver_subject = $receiver_templates->title;
            $receiver_subheader = $receiver_templates->subheader;
            $receiver_message = $receiver_templates->content;
            
            $receiver_subhead = str_replace('{sender}', $user->first_name, $receiver_subheader);
            $receiver_subhead = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $receiver_subhead);
            $receiver_subhead = str_replace('{amount}', number_format($amount, 2, '.', ','), $receiver_subhead);
            
            $receive_msg = str_replace('{sender}', $user->first_name, $receiver_message);
            $receive_msg = str_replace('{currency}', $this->helper->getcurrencyCode($currency_id), $receive_msg);
            $receive_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $receive_msg);
            
            $date      = date("Y-d-m h:i"); 
            $currency  = request('currency_id');
            $type      = "sendmoney";
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
                'template' => '10',
                'language' => $receiver_device->language,
                'currency' => $this->helper->getcurrencyCode($currency_id),
                'amount' => number_format($amount, 2, '.', ','),
                'sender' => $user->first_name,
            ]);
    	}
        
        $wallet            = new Wallet();
        $wallets           = $wallet->getAvailableBalance($user_id);
            
        return response()->json([
            'status'    => true,
            'balance'   => $wallets,
            'tr_ref_id' => $unique_code,
        ]);
    }
    //Send Money Ends here
    
    public function getRecommendedamt()
    {
        if((request('currency_id') == '')||(request('transaction_type_id') == ''))
        {
            $success['reason']  = 'Required Field';
            $success['message'] = "All fileds are required!";
            $success['status']  = '400';
            return response()->json(['success' => $success], $this->successStatus);
        }else
        {
            $payment_method = request('payment_method');
            if($payment_method == '')
            {
                $currency_id = request('currency_id');
                $type_id     = request('transaction_type_id');
                $feesDetails = FeesLimit::where(['transaction_type_id' => $type_id, 'currency_id' => $currency_id])->get(['transaction_type_id','charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id','recom_amt']);
                foreach($feesDetails as $value)
                {
                    $value->recommended_amt = explode(",",$value->recom_amt);
                }
            }else
            {
                $currency_id = request('currency_id');
                $type_id     = request('transaction_type_id');
                $feesDetails = FeesLimit::where(['transaction_type_id' => $type_id, 'currency_id' => $currency_id,'payment_method_id'=> $payment_method])->get(['transaction_type_id','charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id','recom_amt']);
                foreach($feesDetails as $value)
                {
                    $value->recommended_amt = explode(",",$value->recom_amt);
                }
            }
        
        $success['recommended_amt'] = $feesDetails;
        return response()->json(['status'=>$this->successStatus,'success' => $success]);
        }
    }
    
    public function GetSendTransaction(Request $request)
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
            $tran = Transfer::where(['sender_id'=>$user_id,'status'=>'Success'])->select('receiver_id','currency_id','amount','note')->orderBy('id','desc')->paginate(5);
            
            foreach($tran as $value)
            {
                $data = User::where('id',$value->receiver_id)->first();
                //dd($data);
                if((!empty($data))||($data != null ))
                {
                    $value->image = $data->picture??'';
                    $value->email = $data->email??'';
                    $value->formatted_phone = $data->formattedPhone??'';
                    $value->receiver_name = $data->first_name.' '.$data->last_name??'';
                    $value->currency_code = $this->helper->getcurrencyCode($value->currency_id);
                }else
                {
                    $value->image = '';
                    $value->email = '';
                    $value->formatted_phone = '';
                    $value->receiver_name = '';
                    $value->currency_code = $this->helper->getcurrencyCode($value->currency_id);
                }
               
            }
           return response()->json([
            'status'    => true,
            'message'   => 'Get last five transaction',
            'transaction' => $tran,
        ]);
        }
    }
}
