<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\UtilityDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Utility;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Currency;
use Auth;
use Illuminate\Support\Facades\Validator;
class DhiraaguController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    } 
    public function internet_bill()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'int_bill';
        $data['content_title'] = 'int_bill';
        $data['dhiraagu'] = DB::table('dhiraagu_reload')->where(['operator_id'=>'0','paid_type'=>'6'])->get();
        $data['details'] = DB::table('electricity_bill')->where(['type_status'=>'1','operator_id'=>'0'])->get();
        $data['ooredoo'] = DB::table('electricity_bill')->where(['type_status'=>'1','operator_id'=>'1'])->get();
       // dd($data['details']);
        return view('admin.dhiraagufunction.internet_bill', $data);
    }
    
    public function ooredoo_bill_form()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'int_bill';
        $data['users'] = User::all();
        $data['content_title'] = 'add_internet_pay';
        return view('admin.dhiraagufunction.oreedo_internet_bill', $data);
    }
    public function ooredoo_bill_load(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance/ooredoo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "account"    => $request->account_number,
          "bill_type"  => "SUPERNET"
        ]);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        
        $response = curl_exec($ch);
        //dd($response);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        if (array_key_exists("error",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            $data['response'] = array();
            echo json_encode($data);
          }else
          {
            //   print_r(array_keys($arr));
            //   die;
            $data['status']   = 'true';
            $data['message']  = 'Successfully get Pending Bill list!';
            $data['response'] = $arr;
            echo json_encode($data);
           
          }
    }
    
    public function ooredoo_bill_pay(Request $request)
    {
        $check_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
        if($check_balance->balance >= $request->amount)
        {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/pay/ooredoo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "amount"    => $request->amount,
          "account"   => $request->account_id,
          "bill_type" => "SUPERNET"
        ]);
        //dd($requestBody);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
       // echo $response;
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        // print_r($arr);
        // die;
        if(array_key_exists("error",$arr))
        {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            echo json_encode($data);
        }else
        {
           $check = DB::table('fees_limits')->where('transaction_type_id',25)->first()->has_transaction??'0';
           if($check == 'Yes')
           {
               $percentage = $check->charge_percentage;
               $totalAmt = $request->amount;
               $charge_percentage  = ($totalAmt / 100)*$percentage;
              // dd($charge_percentage);
               $charge_fixed       = $check->charge_fixed;
               $subtotal           = $request->amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }else
           {
               $charge_percentage  = 0;
               $percentage         = 0;
               $charge_fixed       = 0;
               $subtotal           = $request->amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }
        //   print_r($check->has_transaction);
        //   die;
           $insert = DB::table('electricity_bill')->insert([
                    'transaction_id'     => $arr['transaction_id'],
                    'amount'             => $arr['amount'],
                    'user_id'            => $request->user_id,
                    'account_number'     => $arr['account_number'],
                    'contact_number'     => $request->number,
                    'status'             => $arr['status'],
                    'product_id'         => $request->product_id,
                    'type_status'        => 1,
                    'operator_id'        => 1
                    ]);
            if($insert)
            {
                
                //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $request->user_id;
                    $transaction->end_user_id              = null;
                    $transaction->currency_id              = 8;
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $arr['transaction_id'];
                    $transaction->transaction_type_id      = 25;
                    $transaction->user_type                = 'registered';
                    $transaction->subtotal          = $arr['amount'];
                    $transaction->percentage        = $percentage;
                    $transaction->charge_percentage = $charge_percentage;
                    $transaction->charge_fixed      = $charge_fixed;
                    $transaction->total             = $total;
                    $transaction->phone             = $request->number??'';
                    $transaction->note              = null;
                    $transaction->status            = "Success";
                    $transaction->save();
                 $current_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
                 Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
                
                $data['status'] = 'true';
                $data['title']  = 'Congratulation!';
                $data['message'] = 'Your Internet Bill Pay Successfully';
                echo json_encode($data);
                
            }else
            {
                $data['msg'] = 'true';
                $data['tran'] = '123';
                echo json_encode($data);
            }
         }
        }else
        {
            $data['status'] = 'false';
            $data['title']  = 'Dont have Balance!';
            $data['message'] = 'Sorry, this user dont have sufficient balance!';
            echo json_encode($data);
        }
    } 
    public function ooredoo_postpaid_form()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'bill_pay';
        $data['users'] = User::all();
        $data['content_title'] = 'add_internet_pay';
        return view('admin.dhiraagufunction.add_ooredoo_form', $data);
    }
    public function find_ooredoo_postpaid_customer(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance/ooredoo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "account"    => $request->account_number,
          "bill_type"  => "POSTPAID"
        ]);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        
        $response = curl_exec($ch);
       // dd($response);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        if (array_key_exists("error",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            $data['response'] = array();
            echo json_encode($data);
          }else
          {
            //   print_r(array_keys($arr));
            //   die;
            $data['status']   = 'true';
            $data['message']  = 'Successfully get Pending Bill list!';
            $data['response'] = $arr;
            echo json_encode($data);
           
          }
    }
    public function pay_ooredoo_postpaid(Request $request)
    {   //dd($request->all());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/pay/ooredoo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "amount"     => $request->amount,
          "account"    => $request->account_id,
          "bill_type"  => "POSTPAID"
        ]);
       
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        //dd($arr); 
        if (array_key_exists("error",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            $data['response'] = array();
            echo json_encode($data);
          }else
          {
             $check_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
            //dd($check_balance);
              if($check_balance->balance >= $request->amount)
                {
               $check = DB::table('fees_limits')->where('transaction_type_id',15)->first();
               if($check->has_transaction == 'Yes')
               {
                   $percentage = $check->charge_percentage;
                   $totalAmt = $request->amount;
                   $charge_percentage  = ($totalAmt / 100)*$percentage;
                  // dd($charge_percentage);
                   $charge_fixed       = $check->charge_fixed;
                   $subtotal           = $request->amount;
                   $total              = $subtotal+$charge_fixed+$charge_percentage;
               }else
               {
                   $charge_percentage  = 0;
                   $percentage         = 0;
                   $charge_fixed       = 0;
                   $subtotal           = $request->amount;
                   $total              = $subtotal+$charge_fixed+$charge_percentage;
               }
            //   print_r($check->has_transaction);
            //   die;
                $insert = DB::table('dhiraagu_reload')->insert([
                    'reload_transaction_id'     => $arr['transaction_id'],
                    'reload_amount'             => $request->amount,
                    'customer_id'               => $request->user_id,
                    'reload_destinationNumber'  => $request->account_id,
                    'reload_InvoiceNumber'      => uniqid(),
                    'reload_transactionDescription'      => $arr['message'],
                    'reload_transactionStatus'           => 1,
                    'operator_id'               => 1,
                    'paid_type'                 => 1
                    ]);
                if($insert)
                {
                    
                    //Transaction
                        $transaction                           = new Transaction();
                        $transaction->user_id                  = $request->user_id;
                        $transaction->end_user_id              = null;
                        $transaction->currency_id              = 8;
                        $transaction->uuid                     = strtoupper(uniqid());
                        $transaction->transaction_reference_id = $arr['transaction_id'];
                        $transaction->transaction_type_id      = 15;
                        $transaction->user_type                = 'registered';
                        $transaction->subtotal          = $subtotal;
                        $transaction->percentage        = 0;
                        $transaction->charge_percentage = $charge_percentage;
                        $transaction->charge_fixed      = $charge_fixed;
                        $transaction->total             = $total;
                        $transaction->phone             = $request->account_id;
                        $transaction->note              = null;
                        $transaction->status            = "Success";
                        $transaction->save();
                     $current_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
                     Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
              
                        $data['status'] = 'true';
                        $data['title']  = 'Congratulation!';
                        $data['message'] = 'Your Ooredoo Postpaid Bill Pay';
                        echo json_encode($data);
                }else
                {
                    $data['msg'] = 'true';
                    $data['tran'] = '123';
                    echo json_encode($data);
                }
             }else
             {
                $data['status'] = 'false';
                $data['title']  = 'Dont have balance!';
                $data['message'] = 'Sorry you dont have sufficient balance!';
                echo json_encode($data);
             }
           
          }
    }
    public function internet_bill_form()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'int_bill';
        $data['users'] = User::all();
        $data['content_title'] = 'add_internet_pay';
        return view('admin.dhiraagufunction.add_internet_bill', $data);
    }
    public function dhiraagu_bill_form()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'int_bill';
        $data['users'] = User::all();
        $data['content_title'] = 'add_internet_pay';
        return view('admin.dhiraagufunction.dhiraagu_bill_form', $data);
    }
   public function index()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'bill_pay';
        $data['content_title'] = 'bill_pay';
        $data['details'] = DB::table('dhiraagu_reload')->where('paid_type',1)->where('operator_id',0)->get();
        $data['ooredoo'] = DB::table('dhiraagu_reload')->where('paid_type',1)->where('operator_id',1)->get();
        return view('admin.dhiraagufunction.allbill', $data);
    }
     public function topup()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'bill_pay';
        $data['content_title'] = 'bill_pay';
        return view('admin.dhiraagufunction.alltopup', $data);
    }
    
    public function topup_list()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'prepaid';
        $data['content_title'] = 'reload';
        $data['details'] = Transaction::where(['transaction_type_id' => 15])->orderBy('id', 'desc')->get();
        Transaction::where('read_topup_status', '0')->where('transaction_type_id', '15')->update(['read_topup_status' => '1']);
        return view('admin.dhiraagufunction.topuplist', $data);
    }
    
    public function topup_list_edit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'prepaid';
        $data['content_title'] = 'reload';
        $data['value'] = $value = Transaction::where('id', $id)->where(['transaction_type_id' => 15])->orderBy('id', 'desc')->first();
        $data['user'] = User::where('id', $value->user_id)->first();
        $data['currency'] = Currency::where('id', $value->currency_id)->first();
        return view('admin.dhiraagufunction.edittopuplist', $data);
    }
    
    public function add_prepaid()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'bill_pay';
        $data['content_title'] = 'bill_pay';
        return view('admin.dhiraagufunction.add_topup', $data);
    }
    
    
    public function add_dhiragu_bill()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'bill_pay';
        $data['content_title'] = 'add_bill_pay';
        return view('admin.dhiraagufunction.add_bill', $data);
    }
    
    
    public function dhiragu_all_reload()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'prepaid';
        $data['content_title'] = 'reload';
        $data['details'] = DB::table('dhiraagu_reload')->where(['paid_type'=>0,'operator_id'=>0])->get();
        $data['ooredoo'] = DB::table('dhiraagu_reload')->where(['paid_type'=>0,'operator_id'=>1])->get();
        return view('admin.dhiraagufunction.all_reload', $data);
    }
    public function ooredoo_reload()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'prepaid';
        $data['content_title'] = 'prepaid';
        $data['customer']  = User::where('status','Active')->get();
        return view('admin.dhiraagufunction.ooredoo_reload', $data);
    }
    
    public function new_reload()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'prepaid';
        $data['content_title'] = 'prepaid';
         $data['customer']  = User::where('status','Active')->get();
        return view('admin.dhiraagufunction.new_reload', $data);
    }
    
    public function allcashin()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'allcashin';
        $data['content_title'] = 'allcashin';
        $data['allcashin'] = DB::table('dhiraagu_cashin')->get();
        return view('admin.dhiraagufunction.allcashin', $data);
    }
    
    public function newcashin()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'newcashin';
        $data['content_title'] = 'newcashin';
        $data['customer']  = User::where('status','Active')->get();
        return view('admin.dhiraagufunction.add_cashin', $data);
    }
    
    public function getcustomerid(Request $request)
    {
        $subcat = DB::table("users")
                    ->where("id",$request->customerID)
                    ->pluck("phone");
        return response()->json($subcat);
    }
    
    public function all_payments()
    {
        $data['menu']     = 'reload';
        $data['sub_menu'] = 'all_payments';
        $data['content_title'] = 'all_payments';
        $data['details'] = array();
        return view('admin.dhiraagufunction.allpayments', $data);
    }
    
    public function new_payments()
    {
         $data['menu']     = 'reload';
        $data['sub_menu'] = 'all_payments';
        $data['content_title'] = 'new_payments';
        return view('admin.dhiraagufunction.add_payments', $data);
    }
    
    
    public function getdhiraagutoken()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/apitoken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
      
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            "grant_type" => "password",
            "username"   => "conus",
            "password"   => "C0nu5Inv3st&DPay_DHI*21!"

               )));
       // curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        return $arr['access_token'];
    }
    public function dhiraagu_number_verify($number)
    {
        if ($number == '')
        {
            $data['status'] = 'false';
            $data['message'] = 'number are required!';
            echo json_encode($data);
        }else
        {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/".$number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           "Content-Type: application/x-www-form-urlencoded",
          "Authorization: Bearer ".$this->getdhiraagutoken() ));
        
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
       
        if (array_key_exists("message",$arr))
          {
            $data['status'] = 'false';
            $data['message'] = $arr['message'];
            $data['response'] = array();
            return json_encode($data);
          }else
          {
            $data['status'] = 'true';
            $data['message'] = 'Successfully get Pending Bill list!';
            $data['response'] = $arr;
            return json_encode($data);
           
          }
        }
    }
    public function ooredoo_customer(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance/ooredoo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "account"    => $request->account_number,
          "bill_type"  => "PREPAID"
        ]);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        
        $response = curl_exec($ch);
        //dd($response);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        if (array_key_exists("error",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            $data['response'] = array();
            echo json_encode($data);
          }else
          {
            $data['status']   = 'true';
            $data['message']  = 'Successfully get Pending Bill list!';
            $data['response'] = $arr;
            echo json_encode($data);
           
          }
    }
    public function pay_ooredoo_topup(Request $request)
    {     //dd($request->all());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/pay/ooredoo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "amount"     => $request->amount,
          "account"    => $request->account_id,
          "bill_type"  => "PREPAID"
        ]);
       
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        //dd($arr); 
        if (array_key_exists("error",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            $data['response'] = array();
            echo json_encode($data);
          }else
          {
             $check_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
            //dd($check_balance);
              if($check_balance->balance >= $request->amount)
                {
               $check = DB::table('fees_limits')->where('transaction_type_id',15)->first();
               if($check->has_transaction == 'Yes')
               {
                   $percentage = $check->charge_percentage;
                   $totalAmt = $request->amount;
                   $charge_percentage  = ($totalAmt / 100)*$percentage;
                  // dd($charge_percentage);
                   $charge_fixed       = $check->charge_fixed;
                   $subtotal           = $request->amount;
                   $total              = $subtotal+$charge_fixed+$charge_percentage;
               }else
               {
                   $charge_percentage  = 0;
                   $charge_fixed       = 0;
                   $subtotal           = $request->amount;
                   $total              = $subtotal+$charge_fixed+$charge_percentage;
               }
            //   print_r($check->has_transaction);
            //   die;
                $insert = DB::table('dhiraagu_reload')->insert([
                    'reload_transaction_id'     => $arr['transaction_id'],
                    'reload_amount'             => $request->amount,
                    'customer_id'               => $request->user_id,
                    'reload_destinationNumber'  => $request->account_id,
                    'reload_InvoiceNumber'      => uniqid(),
                    'reload_transactionDescription'      => $arr['message'],
                    'reload_transactionStatus'           => 1,
                    'operator_id'               => 1
                    ]);
                if($insert)
                {
                    
                    //Transaction
                        $transaction                           = new Transaction();
                        $transaction->user_id                  = $request->user_id;
                        $transaction->end_user_id              = null;
                        $transaction->currency_id              = 8;
                        $transaction->uuid                     = strtoupper(uniqid());
                        $transaction->transaction_reference_id = $arr['transaction_id'];
                        $transaction->transaction_type_id      = 15;
                        $transaction->user_type                = 'registered';
                        $transaction->subtotal          = $subtotal;
                        $transaction->percentage        = 0;
                        $transaction->charge_percentage = $charge_percentage;
                        $transaction->charge_fixed      = $charge_fixed;
                        $transaction->total             = $total;
                        $transaction->phone             = $request->account_id;
                        $transaction->note              = null;
                        $transaction->status            = "Success";
                        $transaction->save();
                     $current_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
                     Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
              
                        $data['status'] = 'true';
                        $data['title']  = 'Congratulation!';
                        $data['message'] = 'Your Ooredoo Recharge Successfully';
                        echo json_encode($data);
                }else
                {
                    $data['msg'] = 'true';
                    $data['tran'] = '123';
                    echo json_encode($data);
                }
             }else
             {
                $data['status'] = 'false';
                $data['title']  = 'Dont have balance!';
                $data['message'] = 'Sorry you dont have sufficient balance!';
                echo json_encode($data);
             }
           
          }
      }
    public function find_intenet_bill(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "account"    => $request->account_number,
          "bill_type"  => "FASEYHA"
        ]);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        
        $response = curl_exec($ch);
        //dd($response);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        if (array_key_exists("error",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            $data['response'] = array();
            echo json_encode($data);
          }else
          {
            $data['status']   = 'true';
            $data['message']  = 'Successfully get Pending Bill list!';
            $data['response'] = $arr;
            echo json_encode($data);
           
          }
    }
    public function pay_internet_bill(Request $request)
    {   //dd($request->all());
        $check_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
        if($check_balance->balance >= $request->amount)
        {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/pay/faseyha");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $requestBody = json_encode([
          "amount"  => $request->amount,
          "account" => $request->account_id,
          "product_id"  => $request->product_id
        ]);
        //dd($requestBody);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json",
           "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
       // echo $response;
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        // print_r($arr);
        // die;
        if(array_key_exists("error",$arr))
        {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['error'];
            echo json_encode($data);
        }else
        {
           $check = DB::table('fees_limits')->where('transaction_type_id',25)->first()->has_transaction??'0';
           if($check == 'Yes')
           {
               $percentage = $check->charge_percentage;
               $totalAmt = $request->amount;
               $charge_percentage  = ($totalAmt / 100)*$percentage;
              // dd($charge_percentage);
               $charge_fixed       = $check->charge_fixed;
               $subtotal           = $request->amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }else
           {
               $charge_percentage  = 0;
               $percentage         = 0;
               $charge_fixed       = 0;
               $subtotal           = $request->amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }
        //   print_r($check->has_transaction);
        //   die;
           $insert = DB::table('electricity_bill')->insert([
                    'transaction_id'     => $arr['transaction_id'],
                    'amount'             => $arr['amount'],
                    'user_id'            => $request->user_id,
                    'account_number'     => $arr['account_number'],
                    'contact_number'     => $request->number,
                    'status'             => $arr['status'],
                    'product_id'         => $request->product_id,
                    'type_status'        => 1
                    ]);
            if($insert)
            {
                
                //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $request->user_id;
                    $transaction->end_user_id              = null;
                    $transaction->currency_id              = 8;
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $arr['transaction_id'];
                    $transaction->transaction_type_id      = 25;
                    $transaction->user_type                = 'registered';
                    $transaction->subtotal          =  $arr['amount'];
                    $transaction->percentage        = $percentage;
                    $transaction->charge_percentage = $charge_percentage;
                    $transaction->charge_fixed      = $charge_fixed;
                    $transaction->total             = $total;
                    $transaction->phone             = $request->number??'';
                    $transaction->note              = null;
                    $transaction->status            = "Success";
                    $transaction->save();
                 $current_balance = Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->select('balance')->first();
                 Wallet::where(['user_id'=> $request->user_id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
                
                $data['status'] = 'true';
                $data['title']  = 'Congratulation!';
                $data['message'] = 'Your Internet Bill Pay Successfully';
                echo json_encode($data);
                
            }else
            {
                $data['msg'] = 'true';
                $data['tran'] = '123';
                echo json_encode($data);
            }
         }
        }else
        {
            $data['status'] = 'false';
            $data['title']  = 'Dont have Balance!';
            $data['message'] = 'Sorry, this user dont have sufficient balance!';
            echo json_encode($data);
        }
    }
    public function pending_bill(Request $request)
    {
        //dd($this->getdhiraagutoken());
        $number_status =  $this->dhiraagu_number_verify($request->number);
        $array = json_decode(json_encode(json_decode($number_status)), true);
        if(($array['response']['serviceType'] == 'PO')||($array['response']['serviceType'] == 'HB')&& ($array['response']['status'] == 'Active'))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/bills/pending/".$request->number);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
               "Content-Type: application/x-www-form-urlencoded",
              "Authorization: Bearer ".$this->getdhiraagutoken() ));
            
            $response = curl_exec($ch);
            curl_close($ch);
           // echo $response;
            $d = json_decode($response);
            $arr = json_decode(json_encode($d), true);
            if (array_key_exists("message",$arr))
              {
                $data['status'] = 'false';
                $data['message'] = $arr['message'];
                $data['response'] = array();
                echo json_encode($data);
              }else
              {
                $data['status'] = 'true';
                $data['message'] = 'Successfully get Pending Bill list!';
                $data['response'] = $arr;
                echo json_encode($data);
               
              }
        }elseif($array['response']['serviceType'] == 'PR')
        {
            $data['status'] = 'false';
            $data['message'] = 'Sorry, your number is Prepaid number!';
            echo json_encode($data);
           
        }else
        {
            $data['status'] = 'false';
            $data['message'] = 'Please enter valid number!';
            echo json_encode($data);
        }
        
        
    }
   
    public function dhiraagu_pay_cable_tv(Request $request)
    {
       $check_user = User::where('phone',$request->number)->first();
      //dd($check->id);
       if(empty($check_user))
       {
            $data['status'] = 'false';
            $data['title']  = 'User not found!';
            $data['message'] = 'Cant find any user for this number!';
            echo json_encode($data);
       }else
       {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/billpay");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
             "username"             => "VFNUTUZTQUdFTlQ=",
             "merchantKey"          => "bnYxNzJqZA==",
             "originationNumber"    => "7400038",
             "amount"               => $request->pay_amount,
             "BillNumber"           => $request->bill_number,
             "PaymentIdentifier"    => $request->PaymentIdentifier,
             "remarks"              => $request->add_remark,
             "transactionDescription" => $request->description
         )));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          "Authorization: Bearer ".$this->getdhiraagutoken()
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        // print_r($arr);
        // die;
        if (array_key_exists("message",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['message'];
            echo json_encode($data);
          }else
          {
            
            $check_balance = Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->select('balance')->first();
            //dd($check_balance);
          if($check_balance->balance >= $request->pay_amount)
            {
           $check = DB::table('fees_limits')->where('transaction_type_id',29)->first();
           if(!empty($check))
           {
               $percentage = $check->charge_percentage;
               $totalAmt = $request->pay_amount;
               $charge_percentage  = ($totalAmt / 100)*$percentage;
              // dd($charge_percentage);
               $charge_fixed       = $check->charge_fixed;
               $subtotal           = $request->pay_amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }else
           {
               $charge_percentage  = 0;
               $percentage         = 0;
               $charge_fixed       = 0;
               $subtotal           = $request->pay_amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }
        //   print_r($check->has_transaction);
        //   die;  paid_type
        $insert = DB::table('dhiraagu_reload')->insert([
                'reload_transaction_id'     => $arr['transactionId'],
                'reload_amount'             => $request->pay_amount,
                'customer_id'               => $check_user->id,
                'reload_destinationNumber'  => $request->number??'',
                'reload_InvoiceNumber'      => $request->PaymentIdentifier,
                'reload_transactionDescription'      => $request->description,
                'reload_transactionStatus'           => $arr['transactionStatus'],
                'reload_resultData'                  => json_encode($arr['resultData']),
                'paid_type'                          => "5",
                ]);
            if($insert)
            {
                
                //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $check_user->id;
                    $transaction->end_user_id              = null;
                    $transaction->currency_id              = 8;
                    $transaction->provider_name            = "Dhiraagu TV";
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $arr['transactionId'];
                    $transaction->transaction_type_id      = 29;
                    $transaction->user_type                = 'registered';
                    $transaction->subtotal          = $subtotal;
                    $transaction->percentage        = 0;
                    $transaction->charge_percentage = $charge_percentage;
                    $transaction->charge_fixed      = $charge_fixed;
                    $transaction->total             = $total;
                    $transaction->phone             = $request->number??'';
                    $transaction->note              = null;
                    $transaction->status            = "Success";
                    $transaction->save();
                 $current_balance = Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->select('balance')->first();
                 Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
                    $data['status'] = 'true';
                    $data['title']  = 'Congrulations';
                    $data['message'] = 'Your Dhiraagu Cable TV Bill Pay Successfully';
                    echo json_encode($data);
                }else
                {
                    $data['status'] = 'false';
                    $data['title']  = 'Not update in Transaction!';
                    $data['message'] = 'Cant update in Transaction table!';
                    echo json_encode($data);
                }
             }else
             {
                $data['status'] = 'false';
                $data['title']  = 'Less Balance!';
                $data['message'] = 'Sorry you dont have sufficient balance!';
                echo json_encode($data);
             }
        }
       }
    }
     public function dhiraagu_internet_pay(Request $request)
    {
      
       $check_user = User::where('phone',$request->number)->first();
      //dd($check->id);
       if(empty($check_user))
       {
            $data['status'] = 'false';
            $data['title']  = 'User not found!';
            $data['message'] = 'Cant find any user for this number!';
            echo json_encode($data);
       }else
       {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/billpay");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
             "username"             => "VFNUTUZTQUdFTlQ=",
             "merchantKey"          => "bnYxNzJqZA==",
             "originationNumber"    => "7400038",
             "amount"               => $request->pay_amount,
             "BillNumber"           => $request->bill_number,
             "PaymentIdentifier"    => $request->PaymentIdentifier,
             "remarks"              => $request->add_remark,
             "transactionDescription" => $request->description
         )));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          "Authorization: Bearer ".$this->getdhiraagutoken()
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        // print_r($arr);
        // die;
        if (array_key_exists("message",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['message'];
            echo json_encode($data);
          }else
          {
            
            $check_balance = Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->select('balance')->first();
            //dd($check_balance);
          if($check_balance->balance >= $request->pay_amount)
            {
           $check = DB::table('fees_limits')->where('transaction_type_id',25)->first();
           if(!empty($check))
           {
               $percentage = $check->charge_percentage;
               $totalAmt = $request->pay_amount;
               $charge_percentage  = ($totalAmt / 100)*$percentage;
              // dd($charge_percentage);
               $charge_fixed       = $check->charge_fixed;
               $subtotal           = $request->pay_amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }else
           {
               $charge_percentage  = 0;
               $charge_fixed       = 0;
               $subtotal           = $request->pay_amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }
        //   print_r($check->has_transaction);
        //   die;  paid_type
            $insert = DB::table('dhiraagu_reload')->insert([
                'reload_transaction_id'     => $arr['transactionId'],
                'reload_amount'             => $request->pay_amount,
                'customer_id'               => $check_user->id,
                'reload_destinationNumber'  => $request->number??'',
                'reload_InvoiceNumber'      => $request->PaymentIdentifier,
                'reload_transactionDescription'      => $request->description,
                'reload_transactionStatus'           => $arr['transactionStatus'],
                'reload_resultData'                  => json_encode($arr['resultData']),
                'paid_type'                          => "6",
                'operator_id'                        => "0",
                ]);
            if($insert)
            {
                
                //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $check_user->id;
                    $transaction->end_user_id              = null;
                    $transaction->currency_id              = 8;
                    $transaction->provider_name            = "Dhiraagu Broadband";
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $arr['transactionId'];
                    $transaction->transaction_type_id      = 25;
                    $transaction->user_type                = 'registered';
                    $transaction->subtotal          = $subtotal;
                    $transaction->percentage        = 0;
                    $transaction->charge_percentage = $charge_percentage;
                    $transaction->charge_fixed      = $charge_fixed;
                    $transaction->total             = $total;
                    $transaction->phone             = $request->number??'';
                    $transaction->note              = null;
                    $transaction->status            = "Success";
                    $transaction->save();
                 $current_balance = Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->select('balance')->first();
                 Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
                    $data['status'] = 'true';
                    $data['title']  = 'Congrulations';
                    $data['message'] = 'Your Internet Bill Pay Successfully';
                    echo json_encode($data);
                }else
                {
                    $data['status'] = 'false';
                    $data['title']  = 'Not update in Transaction!';
                    $data['message'] = 'Cant update in Transaction table!';
                    echo json_encode($data);
                }
             }else
             {
                $data['status'] = 'false';
                $data['title']  = 'Less Balance!';
                $data['message'] = 'Sorry you dont have sufficient balance!';
                echo json_encode($data);
             }
        }
       }
    }
    public function dhiraagu_postpaid(Request $request)
    {
      
       $check_user = User::where('phone',$request->number)->first();
      //dd($check->id);
       if(empty($check_user))
       {
            $data['status'] = 'false';
            $data['title']  = 'User not found!';
            $data['message'] = 'Cant find any user for this number!';
            echo json_encode($data);
       }else
       {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/billpay");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
             "username"             => "VFNUTUZTQUdFTlQ=",
             "merchantKey"          => "bnYxNzJqZA==",
             "originationNumber"    => "7400038",
             "amount"               => $request->pay_amount,
             "BillNumber"           => $request->bill_number,
             "PaymentIdentifier"    => $request->PaymentIdentifier,
             "remarks"              => $request->add_remark,
             "transactionDescription" => $request->description
         )));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          "Authorization: Bearer ".$this->getdhiraagutoken()
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        // print_r($arr);
        // die;
        if (array_key_exists("message",$arr))
          {
            $data['status'] = 'false';
            $data['title']  = 'Something Wrong!';
            $data['message'] = $arr['message'];
            echo json_encode($data);
          }else
          {
            
            $check_balance = Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->select('balance')->first();
            //dd($check_balance);
          if($check_balance->balance >= $request->pay_amount)
            {
           $check = DB::table('fees_limits')->where('transaction_type_id',15)->first();
           if($check->has_transaction == 'Yes')
           {
               $percentage = $check->charge_percentage;
               $totalAmt = $request->pay_amount;
               $charge_percentage  = ($totalAmt / 100)*$percentage;
              // dd($charge_percentage);
               $charge_fixed       = $check->charge_fixed;
               $subtotal           = $request->pay_amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }else
           {
               $charge_percentage  = 0;
               $charge_fixed       = 0;
               $subtotal           = $request->pay_amount;
               $total              = $subtotal+$charge_fixed+$charge_percentage;
           }
        //   print_r($check->has_transaction);
        //   die;  paid_type
            $insert = DB::table('dhiraagu_reload')->insert([
                'reload_transaction_id'     => $arr['transactionId'],
                'reload_amount'             => $request->pay_amount,
                'customer_id'               => $check_user->id,
                'reload_destinationNumber'  => $request->number??'',
                'reload_InvoiceNumber'      => $request->PaymentIdentifier,
                'reload_transactionDescription'      => $request->description,
                'reload_transactionStatus'           => $arr['transactionStatus'],
                'reload_resultData'                  => json_encode($arr['resultData']),
                'paid_type'                          => "1",
                ]);
            if($insert)
            {
                
                //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $check_user->id;
                    $transaction->end_user_id              = null;
                    $transaction->currency_id              = 8;
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $arr['transactionId'];
                    $transaction->transaction_type_id      = 15;
                    $transaction->user_type                = 'registered';
                    $transaction->subtotal          = $subtotal;
                    $transaction->percentage        = 0;
                    $transaction->charge_percentage = $charge_percentage;
                    $transaction->charge_fixed      = $charge_fixed;
                    $transaction->total             = $total;
                    $transaction->phone             = $request->number??'';
                    $transaction->note              = null;
                    $transaction->status            = "Success";
                    $transaction->save();
                 $current_balance = Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->select('balance')->first();
                 Wallet::where(['user_id'=> $check_user->id,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
                    $data['status'] = 'true';
                    $data['title']  = 'Congrulations';
                    $data['message'] = 'Your Postpaid Bill Pay Successfully';
                    echo json_encode($data);
                }else
                {
                    $data['status'] = 'false';
                    $data['title']  = 'Not update in Transaction!';
                    $data['message'] = 'Cant update in Transaction table!';
                    echo json_encode($data);
                }
             }else
             {
                $data['status'] = 'false';
                $data['title']  = 'Less Balance!';
                $data['message'] = 'Sorry you dont have sufficient balance!';
                echo json_encode($data);
             }
        }
       }
    }
    public function pay_prepaid(Request $request)
    {
       
        $number_status =  $this->dhiraagu_number_verify($request->destinationnumber);
        $array = json_decode(json_encode(json_decode($number_status)), true);
        if(($array['response']['serviceType'] == 'PR')||($array['response']['serviceType'] == 'HB')&& ($array['response']['status'] == 'Active'))
        {
            $inv = rand(000000,999999);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/reload");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                
                 "username"             => "VFNUTUZTQUdFTlQ=",
                 "merchantKey"          => "bnYxNzJqZA==",
                 "originationNumber"    => "7400038",
                 "destinationNumber"    => $request->destinationnumber,
                 "amount"               => $request->amount,
                 "reloadInvoiceNumber"  => $inv,
                 "transactionDescription" => $request->tran_description
             )));
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
              "Content-Type: application/x-www-form-urlencoded",
              "Authorization: Bearer ".$this->getdhiraagutoken()
            ]);
    
            $response = curl_exec($ch);
            curl_close($ch);
            $d = json_decode($response);
            $arr = json_decode(json_encode($d), true);
            //print_r($response); 
            if($arr['transactionStatus'] == 'true')
            {
                $check_balance = Wallet::where(['user_id'=> $request->cid,'currency_id' => 8,])->select('balance')->first();
                //dd($check_balance);
              if($check_balance->balance >= $request->amount)
                {
               $check = DB::table('fees_limits')->where('transaction_type_id',15)->first();
               if($check->has_transaction == 'Yes')
               {
                   $percentage = $check->charge_percentage;
                   $totalAmt = $request->amount;
                   $charge_percentage  = ($totalAmt / 100)*$percentage;
                  // dd($charge_percentage);
                   $charge_fixed       = $check->charge_fixed;
                   $subtotal           = $request->amount;
                   $total              = $subtotal+$charge_fixed+$charge_percentage;
               }else
               {
                   $charge_percentage  = 0;
                   $charge_fixed       = 0;
                   $subtotal           = $request->amount;
                   $total              = $subtotal+$charge_fixed+$charge_percentage;
               }
            //   print_r($check->has_transaction);
            //   die;
                $insert = DB::table('dhiraagu_reload')->insert([
                    'reload_transaction_id'     => $arr['transactionId'],
                    'reload_amount'             => $request->amount,
                    'customer_id'               => $request->cid,
                    'reload_destinationNumber'  => $request->destinationnumber,
                    'reload_InvoiceNumber'      => $inv,
                    'reload_transactionDescription'      => $request->tran_description,
                    'reload_transactionStatus'           => $arr['transactionStatus'],
                    'reload_resultData'                  => json_encode($arr['resultData']),
                    ]);
                if($insert)
                {
                    
                    //Transaction
                        $transaction                           = new Transaction();
                        $transaction->user_id                  = $request->cid;
                        $transaction->end_user_id              = null;
                        $transaction->currency_id              = 8;
                        $transaction->uuid                     = strtoupper(uniqid());
                        $transaction->transaction_reference_id = $arr['transactionId'];
                        $transaction->transaction_type_id      = 15;
                        $transaction->user_type                = 'registered';
                        $transaction->subtotal          = $subtotal;
                        $transaction->percentage        = 0;
                        $transaction->charge_percentage = $charge_percentage;
                        $transaction->charge_fixed      = $charge_fixed;
                        $transaction->total             = $total;
                        $transaction->phone             = $request->destinationnumber;
                        $transaction->note              = null;
                        $transaction->status            = "Success";
                        $transaction->save();
                     $current_balance = Wallet::where(['user_id'=> $request->cid,'currency_id' => 8,])->select('balance')->first();
                     Wallet::where(['user_id'=> $request->cid,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $total,]);
              
                     $data['msg'] = 'true';
                     $data['tran'] = $arr['transactionId'];
                     echo json_encode($data);
                }else
                {
                    $data['msg'] = 'true';
                    $data['tran'] = '123';
                    echo json_encode($data);
                }
             }else
             {
                $data['msg'] = 'false';
                $data['tran'] = 'Sorry you dont have sufficient balance!';
                echo json_encode($data);
             }
            }else
            {
                $insert = DB::table('dhiraagu_reload')->insert([
                    'reload_transaction_id'     => $arr['transactionId'],
                    'reload_amount'             => $request->amount,
                    'customer_id'               => $request->cid,
                    'reload_destinationNumber'  => $request->destinationnumber,
                    'reload_InvoiceNumber'      => $inv,
                    'reload_transactionDescription'      => $request->tran_description,
                    'reload_transactionStatus'           => $arr['transactionStatus'],
                    'reload_resultData'                  => json_encode($arr['resultData']),
                    ]);
                $data['msg'] = 'false';
                $data['tran'] = $arr['transactionId'];
                echo json_encode($data);
            }
        }elseif($array['response']['serviceType'] == 'PO')
        {
            $data['msg'] = 'false';
            $data['tran'] = 'Sorry, your number is Postpaid number!';
            echo json_encode($data);
        }else
        {
            $data['msg'] = 'false';
            $data['tran'] = 'Please enter valid number!';
            echo json_encode($data);
        }
       
    }
    
    public function dhiraagu_cashin(Request $request)
    {
       //dd($request->all());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/cashin");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            
             "username"             => "VFNUTUZTQUdFTlQ=",
             "merchantKey"          => "bnYxNzJqZA==",
             "originationNumber"    => "7400038",
             "destinationNumber"    => $request->destinationnumber,
             "amount"               => $request->amount,
             "transactionDescription" => $request->tran_description
         )));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          "Authorization: Bearer ".$this->getdhiraagutoken()
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        //print_r($response); 
        if($arr['transactionStatus'] == 'true')
        {
           
            $insert = DB::table('dhiraagu_cashin')->insert([
                'cashin_transaction'        => $arr['transactionId'],
                'customer_id'               => $request->cid,
                'cashin_amount'             => $request->amount,
                'cashin_destinationNumber'  => $request->destinationnumber,
                'cashin_transactionDescription'      => $request->tran_description,
                'cashin_transactionStatus'           => $arr['transactionStatus'],
                'cashin_resultData'                  => json_encode($arr['resultData']),
                ]);
            if($insert)
            {
                $current_balance = Wallet::where(['user_id'=> $request->cid,'currency_id' => 8,])->select('balance')->first();
                 Wallet::where(['user_id'=> $request->cid,'currency_id' => 8,])->update(['balance' => $current_balance->balance - $request->amount]);
                  //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = $request->cid;
                    $transaction->end_user_id              = null;
                    $transaction->currency_id              = 8;
                    $transaction->uuid                     = strtoupper(uniqid());
                    $transaction->transaction_reference_id = $arr['transactionId'];
                    $transaction->transaction_type_id      = 15;
                    $transaction->user_type                = 'registered';
                    $transaction->subtotal          = 0;
                    $transaction->percentage        = 0;
                    $transaction->charge_percentage = 0;
                    $transaction->charge_fixed      = 0;
                    $transaction->total             = $request->amount;
                    $transaction->note              = null;
                    $transaction->status            = "Success";
                    $transaction->save();
               
                
                $data['msg'] = 'true';
                $data['tran'] = $arr['transactionId'];
                echo json_encode($data);
            }else
            {
                $data['msg'] = 'true';
                $data['tran'] = '123';
                echo json_encode($data);
            }
        }else
        {
             $insert = DB::table('dhiraagu_cashin')->insert([
                'cashin_transaction'        => $arr['transactionId'],
                'cashin_amount'             => $request->amount,
                'customer_id'               => $request->cid,
                'cashin_destinationNumber'  => $request->destinationnumber,
                'cashin_transactionDescription'      => $request->tran_description,
                'cashin_transactionStatus'           => $arr['transactionStatus'],
                'cashin_resultData'                  => json_encode($arr['resultData']),
                ]);
            $data['msg'] = 'false';
            $data['tran'] = $arr['transactionId'];
            echo json_encode($data);
        }
    }
    
    public function dhiraagu_payment(Request $request)
    {
        //dd($request->all());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/payment");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            
             "username"             => "VFNUTUZTQUdFTlQ=",
             "merchantKey"          => "bnYxNzJqZA==",
             "originationNumber"    => "7400038",
             "destinationNumber"    => $request->destinationnumber,
             "amount"               => $request->amount,
             "paymentInvoiceNumber" => $request->paymentInvoiceNumber,
             "transactionDescription" => $request->tran_description
         )));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          "Authorization: Bearer ".$this->getdhiraagutoken()
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
       print_r($response);
    }
    
    public function mwsclist()
    {
        $data['menu']     = 'bill_pay';
        $data['sub_menu'] = 'bill_pay';
        $data['content_title'] = 'bill_pay';
        $data['details']    = array();
        return view('admin.dhiraagu.mwsclist', $data);
    }
}
