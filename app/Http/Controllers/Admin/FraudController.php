<?php

namespace App\Http\Controllers\Admin;

use Config, Artisan, Session, Hash, Auth, DB, Exception;
use App\Http\Controllers\Users\EmailController;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{
    ActivityLog, 
    Admin, 
    EmailTemplate, 
    Preference,
    Rule,
    RuleReport,
    TransactionType,
    Currency
};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FraudExport;
use App\DataTables\Admin\FraudreportDataTable;

class FraudController extends Controller
{
    protected $helper, $emailController;

    public function __construct()
    {
        $this->helper          = new Common();
        $this->emailController = new EmailController();
    }

    public function index($tab, $id)
    {
        $data['menu'] = 'fraud_detection';
        $data['sub_menu'] = 'settings';
        
        $data['fraud'] = Rule::where('transaction_type', $tab)->where('currency_type', $id)->first();
        $data['transactions'] = TransactionType::where('status', '1')->get();
        $data['transact'] = TransactionType::where('id', $tab)->first();
        $data['currencyList'] = Currency::where('status', 'Active')->get();
        $data['currency'] = Currency::where('id', $id)->first();
        return view('admin.fraud.index', $data);
    }
    
    public function update(Request $request)
    {
        $data = array(
            'transaction_type'=>$request->transaction_type,
            'currency_type'=>$request->currency_type,
            'transactions_hour'=>$request->transactions_hour,
            'transactions_day'=>$request->transactions_day,
            'amount_hour'=>$request->amount_hour,
            'amount_day'=>$request->amount_day,
            'amount_week'=>$request->amount_week,
            'amount_month'=>$request->amount_month,
            'same_amount'=>$request->same_amount,
            'email_day'=>$request->email_day,
            'phone_day'=>$request->phone_day,
            'ipadd_day'=>$request->ipadd_day,
            'user_created_at'=>$request->user_created_at,
        );
        
        $rs = Rule::where('transaction_type', $request->transaction_type)->where('currency_type', $request->currency_type)->first();
        if(!empty($rs))
        {
            Rule::where('transaction_type', $request->transaction_type)->where('currency_type', $request->currency_type)->update($data);
            $this->helper->one_time_message('success', 'Settings Updated Successfully!');
            return redirect()->back();
        }
        else
        {
            Rule::create($data);
            $this->helper->one_time_message('success', 'Settings Added Successfully!');
            return redirect()->back();
        } 
    }
    
    public function fraud_report()
    {
        $data['menu'] = 'fraud_detection';
        $data['sub_menu'] = 'reports';
        
        $data['transactionTypes'] = [];

        $results = RuleReport::distinct()->get(['trans_type']);
        if (! $results->isEmpty()) {
            foreach ($results as $value) {
                $data['trans_type'][$value->trans_type] = $value->trans_type;
            }
            $data['transactionTypes'] = TransactionType::select(['id', 'name'])->whereIn('id', $data['trans_type'])->get();
        }
        
        $data['fraud'] = Rule::where('id', '1')->first();
        $data['reports'] = RuleReport::get();
        
        if (isset($_GET['btn']))
        {
            $data['type'] = $type = $_GET['type'];
            $data['trans_type'] = $trans_type   = $_GET['trans_type'];

            if (empty($_GET['from']))
            {
                $data['from'] = $from  = null;
                $data['to'] = $to    = null;
                $data['frauds'] = (new RuleReport())->getReportsList($from, $to, $type, $trans_type)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from  = setDateForDb($_GET['from']);
                $data['to'] = $to    = setDateForDb($_GET['to']);
                $data['frauds'] = (new RuleReport())->getReportsList($from, $to, $type, $trans_type)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from = null;
            $data['to'] = $to   = null;
            $data['type'] = $type   = 'all';
            $data['trans_type'] = $trans_type = 'all';
            $data['frauds'] = (new RuleReport())->getReportsList($from, $to, $type, $trans_type)->orderBy('id', 'desc')->get();
        }
        
        RuleReport::where('read_status', '0')->update(['read_status' => 1]);
        
        return view('admin.fraud.reports', $data);
    }
    
    public function fraudCsv()
    {
        return Excel::download(new FraudExport(), 'report_list_'. time() .'.xls');
    }

    public function fraudPdf()
    {
        $data['reports'] = RuleReport::orderBy('id', 'desc')->get();
        
        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else {
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

        $mpdf->WriteHTML(view('admin.fraud.fraud_report_pdf', $data));

        $mpdf->Output('fraud_report_' . time() . '.pdf', 'D');
    }
}