<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Dispute;
use App\Models\EmailTemplate;
use App\Models\FeesLimit;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Repositories\CryptoCurrencyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TopupExport;
use App\Models\Country;
use Carbon\Carbon;

class DingConnectController extends Controller
{
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    /**
     * The CryptoCurrency repository instance.
     *
     * @var CryptoCurrencyRepository
     */
    protected $cryptoCurrency;
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    
    public function __construct()
    {
        $this->transaction    = new Transaction();
        $this->helper         = new Common();
        $this->email          = new EmailController();
        $this->currency       = new Currency();
        $this->user           = new User();
        $this->main_url       = "https://api.dingconnect.com"; // Main BASE URL
        $this->api_key        = "H5L1oTjrpTe6qXA98WwEoh";
    }
  
   
    /***********************RUN CURL Function START***************************/
    
    public function run_curl ($url, $fields, $method, $header = true, $auth = false) {
        $ch = curl_init();
        if($auth == false) {
        $url = $this->main_url . $url;
        }
         \Illuminate\Support\Facades\Log::channel('dingConnect')->info('API endpoints : run_curl '.$fields);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","api_key: ".$this->api_key)); // Live Token
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
        \Illuminate\Support\Facades\Log::channel('dingConnect')->info('curl response  : run_curl '.$result);
       return json_decode($result);
    }
    
    public function run_curl_post ($url, $fields, $method, $header = true) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.dingconnect.com/api/V1/SendTransfer',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
                "SkuCode":"IN_1A_IN_TopUp",
                "SendValue":2.00,
                "AccountNumber":3000000000,
                "DistributorRef":"NZ-1033",
                "ValidateOnly":false,
                "SendCurrencyIso":"USD"
         }',
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'api_key: H5L1oTjrpTe6qXA98WwEoh'
          ),
        ));
        
        $response = curl_exec($curl);
        //dd($response);
        curl_close($curl);
         return json_decode($response);

    }
    
     /***********************RUN CURL Function END***************************/

    public function index()
    {
        $data['menu']     = 'Mobile';
        $data['sub_menu'] = 'Top-up';
        $data['wallets']  = Wallet::where('user_id', auth()->user()->id)->get();
        $data['currencies']  = Currency::get();
        return view('user_dashboard.dingConnect.index', $data);
    }
    
    public function topup_dummy()
    {
        $data['menu']     = 'Mobile';
        $data['sub_menu'] = 'Top-up';
        $data['wallets']  = Wallet::where('user_id', auth()->user()->id)->get();
        $data['currencies']  = Currency::get();
        return view('user_dashboard.topup.topup_dummy', $data);
    }
    
    public function getOperator(Request $request)
    {
        \Illuminate\Support\Facades\Log::channel('dingConnect')->info('request sent to controller : getOperator '.json_encode($request->all()));
        $phone = $request->phone;
        $countryIsos = $request->carrierCode;
        $country = $request->defaultCountry;
        $completephone = $request->formattedPhone;
        $fields =json_encode(
                array(
                )
            );
        $url = "/api/V1/GetProviders?countryIsos=$country";
        $method = "GET";
        $curlResponse = $this->run_curl($url, $fields, $method, true, false);
        $data = json_decode(json_encode($curlResponse),true);
       \Illuminate\Support\Facades\Log::channel('dingConnect')->info('response from the CURl to function : getOperator '.json_encode($data));
        if(empty($data['Items'])){
         
          
            $val['code'] = 400;
            $val['msg'] = $data['ErrorCodes'][0]['Code']??'Something Went Wrong!';
        }else{
            $value="<option value=''>Select Operator</option>";
            foreach($data['Items'] as $key=>$res)
            {
                if(isset($res['LogoUrl']) && $res['LogoUrl'] !='')
                {
                    $logo = $res['LogoUrl'];
                }else
                {
                     $logo = '';
                }
                $value .="<option data-operator='".$res['Name']."' data-logo='".$logo."' value='".$res['ProviderCode']."'>".$res['Name']."</option>";
            }
             $val['code'] = 200;
            $val['value'] = $value;
            $val['mobile_number'] = $request->formattedPhone;
            $val['defaultCountry'] = $request->defaultCountry;
            $val['carrierCode'] = $request->carrierCode;
            $val['message'] = '';
        }
        return $val;
    }
    
    public function getoperatorplan(Request $request)
    {
        \Illuminate\Support\Facades\Log::channel('dingConnect')->info('request for the getting plans : getoperatorplan '.json_encode($request->all()));
        $operator_id = $request->operator_id;
        $fields =json_encode(
                array(
                )
            );
        $url = '/api/V1/GetProducts?countryIsos='.$request->default_country.'&providerCodes='.$operator_id;
         \Illuminate\Support\Facades\Log::channel('dingConnect')->info('request URL : getoperatorplan '.$url);
        $method ='GET';
        $result = $this->run_curl($url, $fields, $method, true, false);
        \Illuminate\Support\Facades\Log::channel('dingConnect')->info('response from the CURl to function : getoperatorplan '.json_encode($result));
        $data = json_decode(json_encode($result),true);
        $value="";
        $value .="<option value=''>Select plan</option>";
        if(empty($data['Items'])){
           
                $value .="<option value=''>No Data Found!</option>";
            $val['plans'] = $value;
            $val['code'] = 400;
            $val['msg'] = $data['ErrorCodes'][0]['Code']??'Something Went Wrong!';
        }else{
         
            foreach($data['Items'] as $key => $plan)
            {   
                $value .="<option data-skuCode='".$plan['SkuCode']."' data-uatNum='".$plan['UatNumber']."'  data-SendCurrencyIso='".$plan['Maximum']['SendCurrencyIso']."' data-sendValue='".$plan['Maximum']['SendValue']."' data-description='".$plan['DefaultDisplayText']."'  value='".$plan['Maximum']['ReceiveValue']."'>".$plan['DefaultDisplayText']."</option>";
            }
            $val['code'] = 200;
            $val['name'] = $result->name??'';
            $val['sender_currency_code'] = $plan['Maximum']['ReceiveCurrencyIso'];
            $val['destination_currency_code'] = $plan['Maximum']['ReceiveCurrencyIso'];
            $val['logo'] = $result->logoUrls[0]??'';
            $val['plans'] = $value;
          
            $val['mobile_number'] = $request->mobile_num;
            $val['operatorId'] = $request->operatorId;
            $val['default_country'] = $request->default_country;
            $val['carrier_code'] = $request->carrier_code;
        }
        
       
        return $val;
    }
    
    
    public function getvalue(Request $request)
    {
      
      
        $mobilenumber = $request->mobilenumber;
        $operator_name = $request->operatorName??'';
        $logo = $request->logo??'';
        $sender_currency_code = $request->sender_currency_code;
        $destination_currency_code = $request->destination_currency_code;
        $operatorId = $request->skucode;
        $amount = $request->recharge_amount;
       
        $result['value'] = $value??'';
        $result['phone_num'] = $mobilenumber;
        $result['rec_amount'] = $amount;
        $result['logo'] = $request->operatorLogo;
        $result['sender_currency_code'] = $sender_currency_code;
        $result['destination_currency_code'] = $destination_currency_code;
        $result['operatorId'] = $operatorId;
        $result['operator_name'] = $operator_name;
        $result['fxRate'] = $request->description;
        $result['defaultcountry'] = $request->defaultcountry;
        $result['carriercode'] = $request->carriercode;
       
        return $result;
    }
    
    public function confirmvalue(Request $request)
    {
       
        $operator_name = $request->operator_name;
        $mobilenumber = $request->mobilenumber;
        $logo = $request->logo;
        $sender_currency_code = $request->sender_currency_code;
        $destination_currency_code = $request->destination_currency_code;
        $operatorId = $request->operatorId;
        $amount = $request->recharge_amount;
        
        $currency_id = '9';
        
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
                $exchangevalue = getCurrencyRate($sender_currency_code, $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat = $toWalletRate * $amount;
            $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
        }
        
        $feesDetailsforTop = FeesLimit::where(['transaction_type_id' => Recharge, 'currency_id' => $currency_id])->first(['max_limit', 'min_limit', 'has_transaction', 'currency_id', 'charge_percentage', 'charge_fixed']);
        $recharge_fee_total = $feesDetailsforTop->charge_fixed + ($new_amount*$feesDetailsforTop->charge_percentage/100);
        $new_fee = number_format((float)$recharge_fee_total, 2, '.', '');
        $new_total = $new_amount + $new_fee;
        
        $current_balance = Wallet::where('user_id', auth()->user()->id)->where('currency_id', '9')->first();
        
        if(!empty($current_balance->balance) && $current_balance->balance >= $new_total){
            $result['amount'] = $new_amount;
            $result['fee'] = $new_fee;
            $result['total'] = $new_total;
            $result['currency'] = 'USD';
            $result['message'] = '';
        }else{
            $result['amount'] = $new_amount;
            $result['fee'] = $new_fee;
            $result['total'] = $new_total;
            $result['currency'] = 'USD';
            $result['message'] = 'Insufficient Fund!';
        }
       
        return $result;
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
            $value['amount'] = $new_amount;
            $value['fee'] = $new_fee;
            $value['total'] = number_format((float)$new_total, 2, '.', '');;
            $value['currency'] = $new_curr->code;
            $value['message'] = '';
        }else{
            $value['amount'] = $new_amount;
            $value['fee'] = $new_fee;
            $value['total'] = number_format((float)$new_total, 2, '.', '');;
            $value['currency'] = $new_curr->code;
            $value['message'] = 'Insufficient Fund!';
        }
        
        return $value;
    }
    
    
    public function makerecharge(Request $request)
    {  // dd($request->all());
       
        $phone = $request->mobile;
        $amount = $request->amount;
        $operator_id = $request->operator_id;
        $wallet = $request->wallet;
        $operator_amt = $request->operator_amt;
        $country = $request->defaultcountry;
        $carriercode = $request->carriercode;
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

            $fields =json_encode(array(
                "SkuCode"        => $request->skucode,
                "SendValue"      => $request->SendValue,
                "AccountNumber"  => $request->uatNum,
                "DistributorRef" => "NZ-1033",
                "ValidateOnly"   => false,
                "SendCurrencyIso"=> $request->SendCurrencyIso
            ));
            $url = "/api/V1/SendTransfer";
            $method = "POST";
           
            $data = $this->run_curl($url, $fields, $method, true);
            $data = json_decode(json_encode($data),true);
           
            if(empty($data['ErrorCodes'])){
                date_default_timezone_set("Asia/Calcutta");
                $tr_time = Carbon::now()->format('d M Y h:i A');
                
                //Transaction
                $transaction                           = new Transaction();
                $transaction->user_id                  = auth()->user()->id;
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
                $transaction->service_provider_name         = "DingConnect";
                $transaction->save();
                
                $value['tr_amount'] = $new_amount;
                $value['tr_fee'] = $new_fee;
                $value['tr_total'] = $new_total;
                $value['tr_currency'] = $currency_new->code;
                $value['tr_id'] = $data['TransferRecord']['TransferId']['TransferRef'];
                $value['tr_time'] = $tr_time;
                $value['code'] = '200';
                   Wallet::where('id', $wallet)->update([
                    'balance' => $current_balance->balance - $new_total,
                ]);
                return $value;
            } else {
                $value['code'] = '400';
                $value['msg'] = $data['ErrorCodes'][0]['Code']??'Recharge was not successfull. Please try again.';
                 return $value;
            }
        }else{
             $value['code'] = '400';
             $value['msg'] = 'Insufficient Fund!';
              return $value;
          
        }
    }
    
    public function create()
    {
        // dd(session()->all());

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['roles'] = $roles = Role::select('id', 'display_name')->where('user_type', "User")->get();
        // dd($roles);

        return view('admin.users.create', $data);
    }

    public function adminEdit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'admin_users_list';

        $data['admin'] = $users = Admin::find($id);
        $data['roles'] = $roles = Role::select('id', 'display_name')->where('user_type', "Admin")->get();
        return view('admin.users.adminEdit', $data);
    }
    
    public function topup(TopupsDataTable $dataTable)
    {
        $data['menu'] = 'transaction';
        $data['sub_menu'] = 'topup';

        $data['t_status']   = $t_status   = Transaction::select('status')->groupBy('status')->get();
        $data['t_currency'] = $t_currency = Transaction::with('currency:id,code')->select('currency_id')->groupBy('currency_id')->get();

        $data['t_type'] = $t_type = Transaction::with('transaction_type:id,name')->select('transaction_type_id')->where('transaction_type_id', '11')->groupBy('transaction_type_id')->get();
        
        // $data['c_type'] = $c_type = Transaction::select('country')->groupBy('country')->get();

        if (isset($_GET['btn']))
        {
         $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status']   = isset(request()->status) ? request()->status : 'all';
        $data['currency'] = isset(request()->currency) ? request()->currency : 'all';
        $data['type']     = isset(request()->type) ? request()->type : 'all';
        $data['user']     = $user = isset(request()->user_id) ? request()->user_id : null;
        $data['getName']  = $this->transaction->getTransactionsUsersEndUsersName($user, null);

            if (empty($_GET['from']))
            {
                $data['from'] = null;
                $data['to']   = null;
            }
            else
            {
                $data['from'] = $_GET['from'];
                $data['to']   = $_GET['to'];
            }
        }
        else
        {
            // dd('init');
            $data['from']     = null;
            $data['to']       = null;
            $data['status']   = 'all';
            $data['currency'] = 'all';
            $data['type']     = 'all';
            $data['user']     = null;
            // $data['country'] = 'all';
        }
        return $dataTable->render('admin.topup.topup', $data);
    }
    
    public function topupCsv()
    {
        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;

        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        
        $country = isset($_GET['country']) ? $_GET['country'] : null;

        $data['transaction'] = $transaction = $this->transaction->getTopupsList($from, $to, $status, $currency, $type, $user, $country)->orderBy('transactions.id', 'desc')->take(1100)->get();
        //mdf problem, so, i have set take(1100)

        // dd($transaction);

        $datas = [];
        if (!empty($transaction))
        {
            foreach ($transaction as $key => $value)
            {
                $datas[$key]['Date'] = dateFormat($value->created_at);

                // User
                // if (in_array($value->transaction_type_id, [Deposit, Transferred, Exchange_From, Exchange_To, Request_From, Withdrawal, Payment_Sent, Crypto_Sent, Crypto_Received]))
                if (in_array($value->transaction_type_id, [Deposit, Transferred, Exchange_From, Exchange_To,Topup, Request_From, Withdrawal,Payment_Sent]))
                {
                    $datas[$key]['User'] = !empty($value->user) ? $value->user->first_name . ' ' . $value->user->last_name : "-";
                }
                elseif (in_array($value->transaction_type_id, [Received, Request_To, Payment_Received, Crypto_Sent, Crypto_Received]))
                {
                    $datas[$key]['User'] = !empty($value->end_user) ? $value->end_user->first_name . ' ' . $value->end_user->last_name : "-";
                }

                $datas[$key]['Type'] = ($value->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $value->transaction_type->name);

                $datas[$key]['Amount'] = $value->currency->type != 'fiat' ? $value->subtotal : formatNumber($value->subtotal);

                $datas[$key]['Fees'] = (($value->charge_percentage == 0) && ($value->charge_fixed == 0) ? '-' : ($value->currency->type != 'fiat' ? $value->charge_fixed : formatNumber($value->charge_percentage + $value->charge_fixed)));

                if ($value->total > 0)
                {
                    $total = '+' . ($value->currency->type != 'fiat' ? $value->total : formatNumber($value->total));
                }
                else
                {
                    $total = $value->currency->type != 'fiat' ? $value->total : formatNumber($value->total);
                }
                $datas[$key]['Total'] = $total;

                $datas[$key]['Currency'] = $value->currency->code;

                //Receiver
                switch ($value->transaction_type_id)
                {
                    case Deposit:
                    case Exchange_From:
                    case Exchange_To:
                    case Topup:    
                    case Withdrawal:
                    case Crypto_Sent:
                        $datas[$key]['Receiver'] = isset($value->end_user) ? $value->end_user->first_name . ' ' . $value->end_user->last_name : "-";
                        break;
                    case Transferred:
                    case Received:
                        if ($value->transfer->receiver)
                        {
                            $datas[$key]['Receiver'] = $value->transfer->receiver->first_name . ' ' . $value->transfer->receiver->last_name;
                        }
                        elseif ($value->transfer->email)
                        {
                            $datas[$key]['Receiver'] = $value->transfer->email;
                        }
                        elseif ($value->transfer->phone)
                        {
                            $datas[$key]['Receiver'] = $value->transfer->phone;
                        }
                        else
                        {
                            $datas[$key]['Receiver'] = '-';
                        }
                        break;
                    case Request_From:
                    case Request_To:
                        $datas[$key]['Receiver'] = isset($value->request_payment->receiver) ? $value->request_payment->receiver->first_name . ' ' . $value->request_payment->receiver->last_name : $value->request_payment->email;
                        break;
                    case Payment_Sent:
                        $datas[$key]['Receiver'] = isset($value->end_user) ? $value->end_user->first_name . ' ' . $value->end_user->last_name : "-";
                        break;
                    case Payment_Received:
                    case Crypto_Received:
                        $datas[$key]['Receiver'] = isset($value->user) ? $value->user->first_name . ' ' . $value->user->last_name : "-";
                        break;
                }
                $datas[$key]['Status'] = (($value->status == 'Blocked') ? "Cancelled" : (($value->status == 'Refund') ? "Refunded" : $value->status));
            }
        }
        else
        {
            $datas[0]['Date']     = '';
            $datas[0]['User']     = '';
            $datas[0]['Type']     = '';
            $datas[0]['Amount']   = '';
            $datas[0]['Fees']     = '';
            $datas[0]['Total']    = '';
            $datas[0]['Currency'] = '';
            $datas[0]['Receiver'] = '';
            $datas[0]['Status']   = '';
        }
        // dd($datas);
                 return Excel::download(new TopupExport(), 'topup_list_' . time() . '.xlsx');

        // TopuoExport

        // return Excel::create('topup_list_' . time() . '', function ($excel) use ($datas)
        // {
        //     $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //     $excel->sheet('mySheet', function ($sheet) use ($datas)
        //     {
        //         $sheet->cells('A1:I1', function ($cells)
        //         {
        //             $cells->setFontWeight('bold');
        //         });
        //         $sheet->fromArray($datas);
        //     });
        // })->download();
    }

    public function topupPdf()
    {
        // $data['company_logo'] = getCompanyLogoWithoutSession();

        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;

        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        
        $country = isset($_GET['country']) ? $_GET['country'] : null;

        $data['transactions'] = $transactions = $this->transaction->getTopupsList($from, $to, $status, $currency, $type, $user, $country)->orderBy('transactions.id', 'desc')->take(1100)->get(); //mdf problem, so, i have set take(1100)

        // dd($transactions);

        if (isset($from) && isset($to))
        {
            $data['date_range'] = $_GET['startfrom'] . ' To ' . $_GET['endto'];
        }
        else
        {
            $data['date_range'] = 'N/A';
        }

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);

        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);

        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;

        $mpdf->WriteHTML(view('admin.topup.topup_report_pdf', $data));

        $mpdf->Output('transactions_report_' . time() . '.pdf', 'D');
    }
}
