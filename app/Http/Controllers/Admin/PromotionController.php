<?php

namespace App\Http\Controllers\Admin;

use App;
use App\DataTables\Admin\PromotionsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\CryptoapiLog;
use App\Models\Currency;
use App\Models\CurrencyExchange;
use App\Models\Deposit;
use App\Models\Dispute;
use App\Models\EmailTemplate;
use App\Models\MerchantPayment;
use App\Models\PaymentMethod;
use App\Models\RequestPayment;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\AppPage;
use App\Models\Language;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use DB;

class PromotionController extends Controller
{
    protected $helper;
    protected $email;
    protected $transaction;
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->helper      = new Common();
        $this->email       = new EmailController();
        $this->transaction = new Transaction();
    }

    public function index()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'promotions';
        $data['promotions'] = Promotion::orderBy('id', 'desc')->get();
        return view('admin.promotions.index', $data);
    }
    
    public function create()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'promotions';
        $data['pages'] = AppPage::where('status', 'Active')->get();
        $data['language'] = Language::where('status', 'Active')->get();
        $data['merchants'] = User::where('type', 'merchant')->get();
        $data['users'] = User::where('type', 'user')->get();
        return view('admin.promotions.create', $data);
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'user_type' => 'required',
            'title' => 'required',
            'subject' => 'required',
            'type' => 'required',
            'app_redirect' => 'required',
            'description' => 'required',
        );

        $fieldNames = array(
            'user_type' => 'User Type',
            'title' => 'Title',
            'subject' => 'Subject',
            'type' => 'Promotion Type',
            'app_redirect' => 'App Redirect',
            'description' => 'Description',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            if ($request->hasFile('image'))
            {
                $fileName     = $request->file('image');
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());
                $path       = 'uploads/promotions';
                $uploadPath = public_path($path); //problem
                $fileName->move($uploadPath, $uniqueName);
                
                $image = url('public').'/'.$path.'/'.$uniqueName;
            }else{
                $image = null;
            }
            
            if($request->user_type == 'merchant'){
                $user_type = $request->merchant;
            }else{
                $user_type = $request->user;
            }
            
            $rs = Promotion::create([
                'user_type'     => $user_type,
                'title'         => $request->title,
                'subject'       => $request->subject,
                'type'          => $request->type,
                'app_redirect'  => $request->app_redirect,
                'app_page'      => $request->app_page,
                'redirect_url'  => $request->redirect_url,
                'description'   => $request->description,
                'language'      => $request->language,
                'image'         => $image
            ]);
            
            if($request->type == 'Email'){
                
                if($request->user_type == 'merchant'){
                    if($request->merchant == 'All'){
                        $merchants = User::where('type', 'merchant')->get();
                        
                        foreach($merchants as $merchant){
                            $user_detail = User::where('id', $merchant->id)->first();
                            
                            $userdevice = DB::table('devices')->where('user_id', $merchant->id)->first();
                            if(!empty($userdevice)){
                                $device_lang = $userdevice->language;
                            }else{
                                $device_lang = getDefaultLanguage();
                            }
                            
                            $twoStepVerification = EmailTemplate::where([
                                'temp_id'     => 37,
                                'language_id' => $device_lang,
                                'type'        => 'email',
                            ])->select('subject', 'body')->first();
                           
                            $twoStepVerification_sub = $request->subject;
                            $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                            $twoStepVerification_msg = str_replace('{description}', $request->description, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                            $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
                        }
                    }else{
                        $user_detail = User::where('id', $request->merchant)->first();
                        
                        $userdevice = DB::table('devices')->where('user_id', $request->merchant)->first();
                        if(!empty($userdevice)){
                            $device_lang = $userdevice->language;
                        }else{
                            $device_lang = getDefaultLanguage();
                        }
                        
                        $twoStepVerification = EmailTemplate::where([
                            'temp_id'     => 37,
                            'language_id' => $device_lang,
                            'type'        => 'email',
                        ])->select('subject', 'body')->first();
                       
                        $twoStepVerification_sub = $request->subject;
                        $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                        $twoStepVerification_msg = str_replace('{description}', $request->description, $twoStepVerification_msg);
                        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                        $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
                    }
                }else{
                    if($request->user == 'All'){
                        $merchants = User::where('type', 'user')->get();
                        
                        foreach($merchants as $merchant){
                            $user_detail = User::where('id', $merchant->id)->first();
                            
                            $userdevice = DB::table('devices')->where('user_id', $merchant->id)->first();
                            if(!empty($userdevice)){
                                $device_lang = $userdevice->language;
                            }else{
                                $device_lang = getDefaultLanguage();
                            }
                            
                            $twoStepVerification = EmailTemplate::where([ 
                                'temp_id'     => 37,
                                'language_id' => $device_lang,
                                'type'        => 'email',
                            ])->select('subject', 'body')->first();
                           
                            $twoStepVerification_sub = $request->subject;
                            $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                            $twoStepVerification_msg = str_replace('{description}', $request->description, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                            $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
                        }
                    }else{
                        $user_detail = User::where('id', $request->user)->first();
                        
                        $userdevice = DB::table('devices')->where('user_id', $request->user)->first();
                        if(!empty($userdevice)){
                            $device_lang = $userdevice->language;
                        }else{
                            $device_lang = getDefaultLanguage();
                        }
                        
                        $twoStepVerification = EmailTemplate::where([
                            'temp_id'     => 37,
                            'language_id' => $device_lang,
                            'type'        => 'email',
                        ])->select('subject', 'body')->first();
                       
                        $twoStepVerification_sub = $request->subject;
                        $twoStepVerification_msg = str_replace('{user}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                        $twoStepVerification_msg = str_replace('{description}', $request->description, $twoStepVerification_msg);
                        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                        $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
                    }
                }
            }elseif($request->type == 'Notification'){
                if($request->user_type == 'merchant'){
                    if($request->merchant == 'All'){
                        $merchants = User::where('type', 'merchant')->get();
                        
                        foreach($merchants as $merchant){
                            $user_detail = User::where('id', $merchant->id)->first();
                            $this->helper->sendFirabasePush($request->subject,$request->description,$user_detail->id, "9", "mpos");
                            $datanotice1= array('title'=>$request->subject,'content'=>$request->description,'type'=>'push','content_type'=>'mpos','user'=>$user_detail->id,'sub_header'=>$request->title,'push_date'=>date('Y-m-d h:i:s a'));
            	            DB::table('noticeboard')->insert($datanotice1);
                        }
                    }else{
                        $user_detail = User::where('id', $request->merchant)->first();
                        $this->helper->sendFirabasePush($request->subject,$request->description,$user_detail->id, "9", "mpos");
                        $datanotice1= array('title'=>$request->subject,'content'=>$request->description,'type'=>'push','content_type'=>'mpos','user'=>$user_detail->id,'sub_header'=>$request->title,'push_date'=>date('Y-m-d h:i:s a'));
        	            DB::table('noticeboard')->insert($datanotice1);
                    }
                }else{
                    if($request->user == 'All'){
                        $merchants = User::where('type', 'user')->get();
                        
                        foreach($merchants as $merchant){
                            $user_detail = User::where('id', $merchant->id)->first();
                            $this->helper->sendFirabasePush($request->subject,$request->description,$user_detail->id, "9", "ewallet");
                            $datanotice1= array('title'=>$request->subject,'content'=>$request->description,'type'=>'push','content_type'=>'ewallet','user'=>$user_detail->id,'sub_header'=>$request->title,'push_date'=>date('Y-m-d h:i:s a'));
            	            DB::table('noticeboard')->insert($datanotice1);
                        }
                    }else{
                        $user_detail = User::where('id', $request->user)->first();
                        $this->helper->sendFirabasePush($request->subject,$request->description,$user_detail->id, "9", "ewallet");
                        $datanotice1= array('title'=>$request->subject,'content'=>$request->description,'type'=>'push','content_type'=>'ewallet','user'=>$user_detail->id,'sub_header'=>$request->title,'push_date'=>date('Y-m-d h:i:s a'));
            	        DB::table('noticeboard')->insert($datanotice1);
                    }
                }
            }
            
            $this->helper->one_time_message('success', 'Promotion Saved Successfully!');
            return redirect('admin/promotions');
        }
    }
    
    public function edit(Request $request, $id)
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'promotions';
        $data['promotion'] = Promotion::where('id', $id)->first();
        $data['pages'] = AppPage::where('status', 'Active')->get();
        $data['language'] = Language::where('status', 'Active')->get();
        $data['merchants'] = User::where('type', 'merchant')->get();
        return view('admin.promotions.edit', $data);
    }

    public function transactionCsv()
    {
        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;

        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['transaction'] = $transaction = $this->transaction->getTransactionsList($from, $to, $status, $currency, $type, $user)->orderBy('transactions.id', 'desc')->take(1100)->get();
        //mdf problem, so, i have set take(1100)

        // dd($transaction);

        $datas = [];
        if (!empty($transaction))
        {
            foreach ($transaction as $key => $value)
            {
                $datas[$key]['Date'] = dateFormat($value->created_at);

                // User
                if (in_array($value->transaction_type_id, [Deposit, Transferred, Exchange_From, Exchange_To, Request_From, Withdrawal, Payment_Sent, Crypto_Sent, Crypto_Received]))
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
                        elseif ($value->transfer->email??'')
                        {
                            $datas[$key]['Receiver'] = $value->transfer->email??'';
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
                        $datas[$key]['Receiver'] = isset($value->request_payment->receiver) ? $value->request_payment->receiver->first_name . ' ' . $value->request_payment->receiver->last_name : $value->request_payment->email??'';
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

        return Excel::create('transaction_list_' . time() . '', function ($excel) use ($datas)
        {
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->sheet('mySheet', function ($sheet) use ($datas)
            {
                $sheet->cells('A1:I1', function ($cells)
                {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($datas);
            });
        })->download();
    }

    public function transactionPdf()
    {
        $data['company_logo'] = getCompanyLogoWithoutSession();

        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;

        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['transactions'] = $transactions = $this->transaction->getTransactionsList($from, $to, $status, $currency, $type, $user)->orderBy('transactions.id', 'desc')->take(1100)->get(); //mdf problem, so, i have set take(1100)

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

        $mpdf->WriteHTML(view('admin.transactions.transactions_report_pdf', $data));

        $mpdf->Output('transactions_report_' . time() . '.pdf', 'D');
    }
}
