<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\WithdrawalsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\User;
use App\Models\Currency;
use App\Models\EmailTemplate;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\SalesWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class WithdrawalController extends Controller
{
    protected $helper;
    protected $withdrawal;
    protected $email;

    public function __construct()
    {
        $this->helper     = new Common();
        $this->withdrawal = new Withdrawal();
        $this->email      = new EmailController();
    }

    public function index()
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'withdrawals';

        $data['w_status']     = $w_status     = $this->withdrawal->select('status')->groupBy('status')->get();
        $data['w_currencies'] = $w_currencies = $this->withdrawal->select('currency_id')->groupBy('currency_id')->get();
        $data['w_pm']         = $w_pm         = $this->withdrawal->select('payment_method_id')->whereNotNull('payment_method_id')->groupBy('payment_method_id')->get();
        $data['widhral_list']   = $withdrawal   = Withdrawal::orderBy('id', 'desc')->get();
        
        if (isset($_GET['btn']))
        {
            $data['status'] = $status   = $_GET['status'];
            $data['currency'] = $currency = $_GET['currency'];
            $data['pm'] = $pm       = $_GET['payment_methods'];
            $data['user'] = $user     = $_GET['user_id'];

            if (empty($_GET['from']))
            {
                $data['from'] = $from  = null;
                $data['to'] = $to    = null;
                $data['withdrawals'] = $query = (new Withdrawal())->getWithdrawalsList($from, $to, $status, $currency, $pm, $user)->whereNull('pay_type')->orWhere('pay_type', '1')->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from  = setDateForDb($_GET['from']);
                $data['to'] = $to    = setDateForDb($_GET['to']);
                $data['withdrawals'] = $query = (new Withdrawal())->getWithdrawalsList($from, $to, $status, $currency, $pm, $user)->whereNull('pay_type')->orWhere('pay_type', '1')->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from = null;
            $data['to'] = $to   = null;
            $data['status'] = $status   = 'all';
            $data['currency'] = $currency = 'all';
            $data['pm'] = $pm       = 'all';
            $data['user'] = $user     = null;
            $data['withdrawals'] = $query    = (new Withdrawal())->getWithdrawalsList($from, $to, $status, $currency, $pm, $user)->whereNull('pay_type')->orWhere('pay_type', '1')->orderBy('id', 'desc')->get();
        }
        Withdrawal::where('read_status', '0')->where('pay_type', '1')->update(['read_status' => '1']);
        return view('admin.withdrawals.list', $data);
    }

    public function withdrawalCsv()
    {
        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;

        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $pm = isset($_GET['payment_methods']) ? $_GET['payment_methods'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['withdrawals'] = $withdrawals = $this->withdrawal->getWithdrawalsList($from, $to, $status, $currency, $pm, $user)->orderBy('withdrawals.id', 'desc')->get();
        // dd($withdrawals);

        $datas = [];
        if (!empty($withdrawals))
        {
            foreach ($withdrawals as $key => $value)
            {
                $datas[$key]['Date'] = dateFormat($value->created_at);

                $datas[$key]['User'] = isset($value->user) ? $value->user->first_name . ' ' . $value->user->last_name : "-";

                $datas[$key]['Amount'] = formatNumber($value->amount);

                $datas[$key]['Fees'] = ($value->charge_percentage == 0) && ($value->charge_fixed == 0) ? '-' : formatNumber($value->charge_percentage + $value->charge_fixed);

                $datas[$key]['Total'] = '-'.formatNumber($value->amount + ($value->charge_percentage + $value->charge_fixed));

                $datas[$key]['Currency'] = $value->currency->code;

                $datas[$key]['Payment Method'] = ($value->payment_method->name == "Mts") ? getCompanyName() : $value->payment_method->name;

                if ($value->payment_method->name != "Bank")
                {
                    $payment_method_info_withdrawal =  !empty($value->payment_method_info) ? $value->payment_method_info : '-';
                }
                else
                {
                    $payment_method_info_withdrawal = !empty($value->withdrawal_detail) ?
                    $value->withdrawal_detail->account_name.' '.'('.('*****'.substr($value->withdrawal_detail->account_number,-4)).')'.' '.$value->withdrawal_detail->bank_name : '-';
                }
                $datas[$key]['Method Info'] = $payment_method_info_withdrawal;

                $datas[$key]['Status'] = ($value->status == 'Blocked') ? 'Cancelled' : $value->status;
            }
        }
        else
        {
            $datas[0]['Date']           = '';
            $datas[0]['User']           = '';
            $datas[0]['Amount']         = '';
            $datas[0]['Fees']           = '';
            $datas[0]['Total']          = '';
            $datas[0]['Currency']       = '';
            $datas[0]['Payment Method'] = '';
            $datas[0]['Method Info']    = '';
            $datas[0]['Status']         = '';
        }
        // dd($datas);

        return Excel::create('payout_list_' . time() . '', function ($excel) use ($datas)
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

    public function withdrawalPdf()
    {
        // $data['company_logo'] = \Session::get('company_logo');
        $data['company_logo'] = getCompanyLogoWithoutSession();

        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;

        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $pm = isset($_GET['payment_methods']) ? $_GET['payment_methods'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['withdrawals'] = $withdrawals = $this->withdrawal->getWithdrawalsList($from, $to, $status, $currency, $pm, $user)->orderBy('withdrawals.id', 'desc')->get();
        // dd($withdrawals);

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

        $mpdf->WriteHTML(view('admin.withdrawals.withdrawals_report_pdf', $data));

        $mpdf->Output('payouts_report_' . time() . '.pdf', 'D');
    }

    public function withdrawalsUserSearch(Request $request)
    {
        $search = $request->search;
        $user   = $this->withdrawal->getWithdrawalsUsersResponse($search);

        $res = [
            'status' => 'fail',
        ];
        if (count($user) > 0)
        {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }

    public function edit($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'withdrawals';
        
        $data['withdrawal'] = $withdrawal = Withdrawal::find($id);
        // dd($withdrawal);
        
        $data['transaction'] = $transaction = Transaction::select('transaction_type_id', 'status', 'percentage', 'transaction_reference_id')
            ->where(['transaction_reference_id' => $withdrawal->id, 'status' => $withdrawal->status, 'transaction_type_id' => Withdrawal])
            ->first();
            
        $sales_withdrawns = SalesWithdrawal::where(['withdrawal_id' => $withdrawal->id])->get();
        $sales_withdrawn = json_decode($sales_withdrawns, true);
        
        $order_ids = [];
        foreach($sales_withdrawn as $sales){
            array_push($order_ids, $sales['unique_order_id']);
        }
        $sales_transaction = json_decode(Transaction::where(['transaction_type_id' => '12'])->whereIn('uuid', $order_ids)->get(), true);
        
        $buyer_ids = [];
        foreach($sales_transaction as $sales){
            array_push($buyer_ids, $sales['end_user_id']);
        }
        
        $data['buyer_details'] = $buyer = User::whereIn('id', $buyer_ids)->get();
        // $data['transaction_details'] = $sales_withdrawns->count() > 0 ? $sales_withdrawn : null;
        $data['sales_transaction'] = $sales_withdrawns->count() > 0 ? $sales_transaction : null;
        // dd($transaction);

        return view('admin.withdrawals.edit', $data);
    }

    public function update(Request $request)
    {
        // dd($request->all());

        //if other language's subject and body not set, get en sub and body for mail
        $english_withdrawal_email_temp = EmailTemplate::where(['temp_id' => 10, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();

        /**
         * Email Template
         */
        $withdrawal_email_temp = EmailTemplate::where([
            'temp_id'     => 10,
            'language_id' => Session::get('default_language'),
            'type' => 'email',
        ])->select('subject', 'body')->first();

        /**
         * SMS Template
         */
        $withdrawal_status_en_sms_info = EmailTemplate::where(['temp_id' => 10, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
        $withdrawal_status_sms_info    = EmailTemplate::where(['temp_id' => 10, 'language_id' => Session::get('default_language'), 'type' => 'sms'])->select('subject', 'body')->first();
        
        $type = "payout";
        $date = date("Y-m-d h:i:s");
        
        if($request->status == 'Success'){
            $req_status = 'Accepted';
        }elseif($request->status == 'Pending'){
            $req_status = 'Pending';
        }elseif($request->status == 'Blocked'){
            $req_status = 'Rejected';
        }
                    
        //Withdrawal
        if ($request->transaction_type == 'Withdrawal')
        {
            if ($request->status == 'Pending') //requested status
            {
                if ($request->transaction_status == 'Pending')
                {
                    $this->helper->one_time_message('success', 'Payout is already Pending!');
                }
                elseif ($request->transaction_status == 'Success')
                {
                    // dd('current status: Success, doing Pending');
                    $withdrawal         = Withdrawal::find($request->id);
                    $withdrawal->status = $request->status;
                    $withdrawal->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    //Mail when withdrawal is 'Success'
                    if (!empty($withdrawal_email_temp->subject) && !empty($withdrawal_email_temp->body))
                    {
                        // subject
                        $withdrawal_pending_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                        // body
                        $withdrawal_pending_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                    }
                    else
                    {
                        // subject
                        $withdrawal_pending_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                        // body
                        $withdrawal_pending_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                    }
                    //1
                    $withdrawal_pending_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{status}', $withdrawal->status, $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{amount}', 'No Amount', $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{added/subtracted}', 'added/subtracted', $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{from/to}', 'from', $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{soft_name}', Session::get('name'), $withdrawal_pending_msg);

                    if (checkAppMailEnvironment())
                    {
                        // dd('here');
                        $this->email->sendEmail($withdrawal->user->email, $withdrawal_pending_sub, $withdrawal_pending_msg);
                    }
                    
                    //notification
            	    $currency = $withdrawal->currency_id;
            	    $currency_sym = Currency::where('id', $withdrawal->currency_id)->first();
                    
                	$userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $template = NotificationTemplate::where('temp_id', '21')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $sub = str_replace('{status}', $req_status, $subject);
                        $subhead = str_replace('{status}', $req_status, $subheader);
                        $msg = str_replace('{currency}', $currency_sym->code, $message);
                        $msg = str_replace('{amount}', number_format($withdrawal->amount, 2, '.', ','), $msg);
                        $msg = str_replace('{status}', $req_status, $msg);
                        
                        $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $sub,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'photo',
                            'user' => $request->user_id,
                            'sub_header' => $subhead,
                            'push_date' => $date,
                            'template' => '21',
                            'language' => $userdevice->language,
                            'currency' => $currency_sym->code,
                            'amount' => number_format($withdrawal->amount, 2, '.', ','),
                            'status' => $req_status
                        ]);
                	}

                    //sms
                    if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone))
                    {
                        if (!empty($withdrawal_status_sms_info->subject) && !empty($withdrawal_status_sms_info->body))
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_sms_info->body);
                        }
                        else
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_en_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_en_sms_info->body);
                        }
                        //2 - fixed
                        $withdrawal_status_sms_info_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info_msg);
                        //
                        $withdrawal_status_sms_info_msg = str_replace('{status}', $withdrawal->status, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{amount}', 'No Amount', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{added/subtracted}', 'added/subtracted', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{from/to}', 'from', $withdrawal_status_sms_info_msg);

                        if (checkAppSmsEnvironment())
                        {
                            sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $withdrawal_status_sms_info_msg);
                        }
                    }
                    $this->helper->one_time_message('success', 'Payout Updated Successfully!');
                }
                elseif ($request->transaction_status == 'Blocked')
                {
                    // dd('current status: blocked, doing pending');
                    $withdrawal         = Withdrawal::find($request->id);
                    $withdrawal->status = $request->status;
                    $withdrawal->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    $current_balance = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();

                    $update_wallet_for_deposit = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->update([
                        'balance' => $current_balance->balance - $request->amount,
                    ]);

                    if (!empty($withdrawal_email_temp->subject) && !empty($withdrawal_email_temp->body))
                    {
                        $withdrawal_pending_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                        $withdrawal_pending_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                    }
                    else
                    {
                        $withdrawal_pending_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                        $withdrawal_pending_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                    }
                    //3
                    $withdrawal_pending_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{status}', $withdrawal->status, $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{added/subtracted}', 'subtracted', $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{from/to}', 'from', $withdrawal_pending_msg);
                    $withdrawal_pending_msg = str_replace('{soft_name}', Session::get('name'), $withdrawal_pending_msg);

                    if (checkAppMailEnvironment())
                    {
                        $this->email->sendEmail($withdrawal->user->email, $withdrawal_pending_sub, $withdrawal_pending_msg);
                    }
                    
                    //notification
            	    $currency = $withdrawal->currency_id;
            	    $currency_sym = Currency::where('id', $withdrawal->currency_id)->first();
                	
                	$userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $template = NotificationTemplate::where('temp_id', '21')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $sub = str_replace('{status}', $req_status, $subject);
                        $subhead = str_replace('{status}', $req_status, $subheader);
                        $msg = str_replace('{currency}', $currency_sym->code, $message);
                        $msg = str_replace('{amount}', number_format($withdrawal->amount, 2, '.', ','), $msg);
                        $msg = str_replace('{status}', $req_status, $msg);
                        
                        $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $sub,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'photo',
                            'user' => $request->user_id,
                            'sub_header' => $subhead,
                            'push_date' => $date,
                            'template' => '21',
                            'language' => $userdevice->language,
                            'currency' => $currency_sym->code,
                            'amount' => number_format($withdrawal->amount, 2, '.', ','),
                            'status' => $req_status
                        ]);
                	}

                    //sms
                    if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone))
                    {
                        if (!empty($withdrawal_status_sms_info->subject) && !empty($withdrawal_status_sms_info->body))
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_sms_info->body);
                        }
                        else
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_en_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_en_sms_info->body);
                        }
                        //4
                        $withdrawal_status_sms_info_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{status}', $withdrawal->status, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{added/subtracted}', 'subtracted', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{from/to}', 'from', $withdrawal_status_sms_info_msg);

                        if (checkAppSmsEnvironment())
                        {
                            sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $withdrawal_status_sms_info_msg);
                        }
                    }
                    $this->helper->one_time_message('success', 'Payout Updated Successfully!');
                }
            }
            elseif ($request->status == 'Success')
            {
                if ($request->transaction_status == 'Success') //current status
                {
                    $this->helper->one_time_message('success', 'Payout is already Successfull!');
                }
                elseif ($request->transaction_status == 'Pending')
                {
                    // dd('current status: Pending, doing Success');
                    $withdrawal         = Withdrawal::find($request->id);
                    $withdrawal->status = $request->status;
                    $withdrawal->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    //Mail when withdrawal is 'Success'
                    if (!empty($withdrawal_email_temp->subject) && !empty($withdrawal_email_temp->body))
                    {
                        // subject
                        $withdrawal_success_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                        // body
                        $withdrawal_success_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                    }
                    else
                    {
                        // subject
                        $withdrawal_success_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                        // body
                        $withdrawal_success_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                    }
                    //5
                    $withdrawal_success_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{status}', $withdrawal->status, $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{amount}', 'No Amount', $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{added/subtracted}', 'added/subtracted', $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{from/to}', 'from', $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{soft_name}', Session::get('name'), $withdrawal_success_msg);

                    if (checkAppMailEnvironment())
                    {
                        $this->email->sendEmail($withdrawal->user->email, $withdrawal_success_sub, $withdrawal_success_msg);
                    }
                    
                    //notification
            	    $currency = $withdrawal->currency_id;
            	    $currency_sym = Currency::where('id', $withdrawal->currency_id)->first();
                    
                    $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $template = NotificationTemplate::where('temp_id', '21')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $sub = str_replace('{status}', $req_status, $subject);
                        $subhead = str_replace('{status}', $req_status, $subheader);
                        $msg = str_replace('{currency}', $currency_sym->code, $message);
                        $msg = str_replace('{amount}', number_format($withdrawal->amount, 2, '.', ','), $msg);
                        $msg = str_replace('{status}', $req_status, $msg);
                        
                        $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $sub,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'photo',
                            'user' => $request->user_id,
                            'sub_header' => $subhead,
                            'push_date' => $date,
                            'template' => '21',
                            'language' => $userdevice->language,
                            'currency' => $currency_sym->code,
                            'amount' => number_format($withdrawal->amount, 2, '.', ','),
                            'status' => $req_status
                        ]);
                	}

                    //sms
                    if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone))
                    {
                        if (!empty($withdrawal_status_sms_info->subject) && !empty($withdrawal_status_sms_info->body))
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_sms_info->body);
                        }
                        else
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_en_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_en_sms_info->body);
                        }
                        //6
                        $withdrawal_status_sms_info_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{status}', $withdrawal->status, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{amount}', 'No Amount', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{added/subtracted}', 'added/subtracted', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{from/to}', 'from', $withdrawal_status_sms_info_msg);

                        if (checkAppSmsEnvironment())
                        {
                            sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $withdrawal_status_sms_info_msg);
                        }
                    }
                    $this->helper->one_time_message('success', 'Payout Updated Successfully!');
                }
                elseif ($request->transaction_status == 'Blocked') //current status
                {
                    // dd('current status: Success, doing Blocked');
                    $withdrawal         = Withdrawal::find($request->id);
                    $withdrawal->status = $request->status;
                    $withdrawal->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    $current_balance = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();

                    Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->update([
                        'balance' => $current_balance->balance - $request->amount,
                    ]);

                    if (!empty($withdrawal_email_temp->subject) && !empty($withdrawal_email_temp->body))
                    {
                        $withdrawal_success_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                        $withdrawal_success_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                    }
                    else
                    {
                        $withdrawal_success_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                        $withdrawal_success_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                    }
                    //7
                    $withdrawal_success_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{status}', $withdrawal->status, $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{added/subtracted}', 'subtracted', $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{from/to}', 'from', $withdrawal_success_msg);
                    $withdrawal_success_msg = str_replace('{soft_name}', Session::get('name'), $withdrawal_success_msg);

                    if (checkAppMailEnvironment())
                    {
                        $this->email->sendEmail($withdrawal->user->email, $withdrawal_success_sub, $withdrawal_success_msg);
                    }
                    
                    //notification
            	    $currency = $withdrawal->currency_id;
            	    $currency_sym = Currency::where('id', $withdrawal->currency_id)->first();
                    
                    $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $template = NotificationTemplate::where('temp_id', '21')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $sub = str_replace('{status}', $req_status, $subject);
                        $subhead = str_replace('{status}', $req_status, $subheader);
                        $msg = str_replace('{currency}', $currency_sym->code, $message);
                        $msg = str_replace('{amount}', number_format($withdrawal->amount, 2, '.', ','), $msg);
                        $msg = str_replace('{status}', $req_status, $msg);
                        
                        $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $sub,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'photo',
                            'user' => $request->user_id,
                            'sub_header' => $subhead,
                            'push_date' => $date,
                            'template' => '21',
                            'language' => $userdevice->language,
                            'currency' => $currency_sym->code,
                            'amount' => number_format($withdrawal->amount, 2, '.', ','),
                            'status' => $req_status
                        ]);
                	}

                    //sms
                    if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone))
                    {
                        if (!empty($withdrawal_status_sms_info->subject) && !empty($withdrawal_status_sms_info->body))
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_sms_info->body);
                        }
                        else
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_en_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_en_sms_info->body);
                        }
                        //8
                        $withdrawal_status_sms_info_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{status}', $withdrawal->status, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{added/subtracted}', 'subtracted', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{from/to}', 'from', $withdrawal_status_sms_info_msg);

                        if (checkAppSmsEnvironment())
                        {
                            sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $withdrawal_status_sms_info_msg);
                        }
                    }
                    $this->helper->one_time_message('success', 'Payout Updated Successfully!');
                }
            }
            elseif ($request->status == 'Blocked')
            {
                if ($request->transaction_status == 'Blocked') //current status
                {
                    $this->helper->one_time_message('success', 'Payout is already Blocked!');
                }
                elseif ($request->transaction_status == 'Pending') //current status
                {
                    // dd($request->amount);

                    // dd('current status: Pending, doing Blocked');
                    $withdrawal         = Withdrawal::find($request->id);
                    $withdrawal->status = $request->status;
                    $withdrawal->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    $current_balance = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();

                    $update_wallet_for_deposit = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->update([
                        'balance' => $current_balance->balance + $request->amount,
                    ]);

                    //Mail when withdrawal is 'Success'
                    if (!empty($withdrawal_email_temp->subject) && !empty($withdrawal_email_temp->body))
                    {
                        // subject
                        $withdrawal_cancel_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                        // body
                        $withdrawal_cancel_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                    }
                    else
                    {
                        // subject
                        $withdrawal_cancel_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                        // body
                        $withdrawal_cancel_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                    }
                    //9
                    $withdrawal_cancel_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{status}', ($withdrawal->status == 'Blocked') ? 'Cancelled' : $withdrawal->status, $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{added/subtracted}', 'added', $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{from/to}', 'to', $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{soft_name}', Session::get('name'), $withdrawal_cancel_msg);

                    if (checkAppMailEnvironment())
                    {
                        // dd('here');
                        $this->email->sendEmail($withdrawal->user->email, $withdrawal_cancel_sub, $withdrawal_cancel_msg);
                    }
                    
                    //notification
            	    $currency = $withdrawal->currency_id;
            	    $currency_sym = Currency::where('id', $withdrawal->currency_id)->first();
                    
                    $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $template = NotificationTemplate::where('temp_id', '21')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $sub = str_replace('{status}', $req_status, $subject);
                        $subhead = str_replace('{status}', $req_status, $subheader);
                        $msg = str_replace('{currency}', $currency_sym->code, $message);
                        $msg = str_replace('{amount}', number_format($withdrawal->amount, 2, '.', ','), $msg);
                        $msg = str_replace('{status}', $req_status, $msg);
                        
                        $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $sub,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'photo',
                            'user' => $request->user_id,
                            'sub_header' => $subhead,
                            'push_date' => $date,
                            'template' => '21',
                            'language' => $userdevice->language,
                            'currency' => $currency_sym->code,
                            'amount' => number_format($withdrawal->amount, 2, '.', ','),
                            'status' => $req_status
                        ]);
                	}

                    //sms
                    if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone))
                    {
                        if (!empty($withdrawal_status_sms_info->subject) && !empty($withdrawal_status_sms_info->body))
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_sms_info->body);
                        }
                        else
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_en_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_en_sms_info->body);
                        }
                        //10
                        $withdrawal_status_sms_info_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{status}', ($withdrawal->status == 'Blocked') ? 'Cancelled' : $withdrawal->status, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{added/subtracted}', 'added', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{from/to}', 'to', $withdrawal_status_sms_info_msg);

                        if (checkAppSmsEnvironment())
                        {
                            sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $withdrawal_status_sms_info_msg);
                        }
                    }
                    $this->helper->one_time_message('success', 'Payout Updated Successfully!');
                }
                elseif ($request->transaction_status == 'Success') //current status
                {
                    // dd('current status: Success, doing Blocked');
                    $withdrawal         = Withdrawal::find($request->id);
                    $withdrawal->status = $request->status;
                    $withdrawal->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    $current_balance = Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();

                    Wallet::where([
                        'user_id'     => $request->user_id,
                        'currency_id' => $request->currency_id,
                    ])->update([
                        'balance' => $current_balance->balance + $request->amount,
                    ]);

                    if (!empty($withdrawal_email_temp->subject) && !empty($withdrawal_email_temp->body))
                    {
                        $withdrawal_cancel_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                        $withdrawal_cancel_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                    }
                    else
                    {
                        $withdrawal_cancel_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                        $withdrawal_cancel_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                    }
                    //11
                    $withdrawal_cancel_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{status}', ($withdrawal->status == 'Blocked') ? 'Cancelled' : $withdrawal->status, $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{added/subtracted}', 'added', $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{from/to}', 'to', $withdrawal_cancel_msg);
                    $withdrawal_cancel_msg = str_replace('{soft_name}', Session::get('name'), $withdrawal_cancel_msg);

                    if (checkAppMailEnvironment())
                    {
                        $this->email->sendEmail($withdrawal->user->email, $withdrawal_cancel_sub, $withdrawal_cancel_msg);
                    }
                    
                    //notification
            	    $currency = $withdrawal->currency_id;
            	    $currency_sym = Currency::where('id', $withdrawal->currency_id)->first();
                    
                    $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
                	if(!empty($userdevice)){
                        $template = NotificationTemplate::where('temp_id', '21')->where('language_id', $userdevice->language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $sub = str_replace('{status}', $req_status, $subject);
                        $subhead = str_replace('{status}', $req_status, $subheader);
                        $msg = str_replace('{currency}', $currency_sym->code, $message);
                        $msg = str_replace('{amount}', number_format($withdrawal->amount, 2, '.', ','), $msg);
                        $msg = str_replace('{status}', $req_status, $msg);
                        
                        $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $sub,
                            'content' => $msg,
                            'type' => 'push',
                            'content_type' => 'photo',
                            'user' => $request->user_id,
                            'sub_header' => $subhead,
                            'push_date' => $date,
                            'template' => '21',
                            'language' => $userdevice->language,
                            'currency' => $currency_sym->code,
                            'amount' => number_format($withdrawal->amount, 2, '.', ','),
                            'status' => $req_status
                        ]);
                	}

                    //sms
                    if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone))
                    {
                        if (!empty($withdrawal_status_sms_info->subject) && !empty($withdrawal_status_sms_info->body))
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_sms_info->body);
                        }
                        else
                        {
                            $withdrawal_status_sms_info_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_en_sms_info->subject);
                            $withdrawal_status_sms_info_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_status_en_sms_info->body);
                        }
                        //12
                        $withdrawal_status_sms_info_msg = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{status}', ($withdrawal->status == 'Blocked') ? 'Cancelled' : $withdrawal->status, $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($request->amount)), $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{added/subtracted}', 'added', $withdrawal_status_sms_info_msg);
                        $withdrawal_status_sms_info_msg = str_replace('{from/to}', 'to', $withdrawal_status_sms_info_msg);

                        if (checkAppSmsEnvironment())
                        {
                            sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $withdrawal_status_sms_info_msg);
                        }
                    }
                    $this->helper->one_time_message('success', 'Payout Updated Successfully!');
                }
            }
        }
        return redirect('admin/withdrawals');
    }
}
