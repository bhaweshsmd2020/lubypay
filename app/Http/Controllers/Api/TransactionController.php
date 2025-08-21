<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Models\Preference;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class TransactionController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $email;

    public function __construct()
    {
        $this->email = new EmailController();
    }

    public function getTransactionApi()
    {
         //dd(request()->all());
           $type    = request('type');
           $user_id = request('user_id');
           $currency_id= request('currency_id');
           $date_from = request('date_from');
           $date_to   = request('date_to');
           $status    = request('status');
           $is_sent_or_recive    = request('is_sent_or_recive');
       
       if(request('type') && request('user_id') && request('currency_id')==0){
           
            $transaction  = new Transaction();
            $transactions = $transaction->getTransactionListswithZeroCurrencyCode($type, $user_id, $date_from,$date_to,$status,$is_sent_or_recive);

            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success, 'transactions' => $transactions], $this->successStatus);
       }
        if (request('type') && request('user_id') && request('currency_id'))
        {
            
            $transaction  = new Transaction();
            $transactions = $transaction->getTransactionLists($type, $user_id, $currency_id,$date_from,$date_to,$status,$is_sent_or_recive);

            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success, 'transactions' => $transactions], $this->successStatus);
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }
    
    public function transactionUnreadCount(Request $request)
    {
        
        $data = DB::table('transactions')->where('currency_id', $request->currency_id)->where('user_id',$request->user_id)->where('read_status', '0')->get();
        $notice_board_count = DB::table('noticeboard')->where('user',$request->user_id)->where('read_status',0)->get()->count();
        $count =count($data);
        
        return response()->json(['count' => $count,'notice_board_count'=>$notice_board_count, 'message' => 'Transaction Unread Count', 'status' =>$this->successStatus], $this->successStatus);
        
    }
    
    public function readTransaction(Request $request)
    {
        $update = Transaction::where(['user_id'=>$request->user_id,'id'=>$request->tran_id])->update(['read_status'=>1]);
        $success['status'] = $this->successStatus;
        return response()->json(['success' => $success, 'message' => 'Transaction Read Successfully'], $this->successStatus);
    }
    public function getTransactionApi_nirbhay()
    {
       //  dd(request()->all());

        if (request('type') && request('user_id') && request('currency_id'))
        {
            $type    = request('type');
            $user_id = request('user_id');
            $currency_id= request('currency_id');

            $transaction  = new Transaction();
            $transactions = $transaction->getTransactionLists($type, $user_id, $currency_id);

            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success, 'transactions' => $transactions], $this->successStatus);
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }
    
    
    // new code by rajesh
    public function getTransactionsummary()
    {
        // dd(request()->all());

        if (request('type') && request('user_id') && request('currency_id'))
        {
            $type    = request('type');
            $user_id = request('user_id');
            $currency_id= request('currency_id');
            $summary_type= request('summary_type');

            $transaction  = new Transaction();
            $transactions = $transaction->getTransactionSummary($type, $user_id,$currency_id, $summary_type);

            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success, 'transactions' => $transactions], $this->successStatus);
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }
    // new code by rajesh

    public function getTransactionDetailsApi()
    {
        // dd(request()->all());
        if (request('user_id'))
        {
            $user_id           = request('user_id');
            $tr_id             = request('tr_id');
            $transaction       = new Transaction();
            $transaction       = $transaction->getTransactionDetails($tr_id, $user_id);
            // $balance           = $balance->wallet;
            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success, 'transaction' => $transaction], $this->successStatus);
        }
        else
        {
            echo "In else block";exit();return false;
        }
    }
}
