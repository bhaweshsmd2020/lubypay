<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ReportDataTable;
use App\DataTables\Admin\ReportstoreDataTable;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Revenue;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    protected $revenue;
    public function __construct()
    {
        $this->revenue = new Transaction();
    }
    
    public function index()
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'wallet_reports';

        $revenueDatas = $this->revenue
        ->where(function($query)
        {
            $query->where('charge_percentage', '>', 0);
            $query->orWhere('charge_fixed', '!=', 0);
        })
        ->where('status', 'Success')
        ->whereIn('transaction_type_id', [Deposit, Withdrawal, Transferred, Request_To, Payment_Received, Crypto_Sent, Recharge, Exchange_From, Exchange_To]);

        $data['revenues_currency'] = $revenueDatas->groupBy('currency_id')->select('currency_id')->get();
        $data['revenues_type'] = $this->revenue->select('transaction_type_id')->whereNotIn('transaction_type_id', [34,35])->groupBy('transaction_type_id')->get();

        if (isset($_GET['btn']))
        {
            $data['currency'] = $currency = $_GET['currency'];
            $data['type'] = $type = $_GET['type'];

            if (empty($_GET['from']))
            {
                $data['from'] = $from  = null;
                $data['to'] = $to    = null;
                $data['reports'] = (new Transaction())->getRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from         = setDateForDb($_GET['from']);
                $data['to'] = $to           = setDateForDb($_GET['to']);
                $data['reports'] = (new Transaction())->getRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from     = null;
            $data['to'] = $to       = null;
            $data['currency'] = $currency = 'all';
            $data['type'] = $type     = 'all';
            $data['reports'] = (new Transaction())->getRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
        }
        $getRevenuesListForCurrencyIfo = $this->revenue->getRevenuesList($from, $to, $currency, $type)->orderBy('transactions.id', 'desc')->get();
        // dd($getRevenuesListForCurrencyIfo);

        $toal_revenue = 0;
        $array_map    = [];
        $array_final  = [];
        $counter      = 0;

        if ($getRevenuesListForCurrencyIfo->count() > 0)
        {
            foreach ($getRevenuesListForCurrencyIfo as $value)
            {
                $toal_revenue                                = ($value->charge_percentage + $value->charge_fixed);
                $array_map[$value->currency->code][$counter] = $toal_revenue;
                $counter++;
            }

            if ($array_map)
            {
                foreach ($array_map as $key => $res)
                {
                    $array_final[$key] = array_sum($res);
                }
            }
            $data['currency_info'] = $array_final;
            // dd($array_final);
        }
        else
        {
            $data['currency_info'] = [];
        }
        
        $data['total_revenue'] = Transaction::where('percentage', '0.00')->sum('subtotal');
        Transaction::where('read_report_status', '0')->whereIn('transaction_type_id', [Deposit, Withdrawal, Transferred, Request_To, Payment_Received, Crypto_Sent, Recharge, Cable, Exchange_From, Exchange_To, 32])->update(['read_report_status' => '1']);
        return view('admin.reports.index', $data);
    }
    
    public function store_report()
    {
        $data['menu'] = 'mpos';
        $data['sub_menu'] = 'store_reports';

        $revenueDatas = $this->revenue
        ->where(function($query)
        {
            $query->where('store_fee', '>', 0);
        })
        ->where('status', 'Success')
        ->whereIn('transaction_type_id', [Deposit, Withdrawal, Transferred, Request_To, Payment_Received, Crypto_Sent, Recharge, Exchange_From, Exchange_To, 34, 35]);

        $data['revenues_currency'] = $revenueDatas->groupBy('currency_id')->select('currency_id')->get();
        $data['revenues_type'] = $this->revenue->select('transaction_type_id')->groupBy('transaction_type_id')->whereIn('transaction_type_id', [34,35])->get();

        if (isset($_GET['btn']))
        {
            $data['currency'] = $currency = $_GET['currency'];
            $data['type'] = $type = $_GET['type'];

            if (empty($_GET['from']))
            {
                $data['from'] = $from  = null;
                $data['to'] = $to    = null;
                $data['reports'] = (new Transaction())->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from         = setDateForDb($_GET['from']);
                $data['to'] = $to           = setDateForDb($_GET['to']);
                $data['reports'] = (new Transaction())->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from     = null;
            $data['to'] = $to       = null;
            $data['currency'] = $currency = 'all';
            $data['type'] = $type     = 'all';
            $data['reports'] = (new Transaction())->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
        }
        $getRevenuesListForCurrencyIfo = $this->revenue->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('transactions.id', 'desc')->get();
        // dd($getRevenuesListForCurrencyIfo);

        $toal_revenue = 0;
        $array_map    = [];
        $array_final  = [];
        $counter      = 0;

        if ($getRevenuesListForCurrencyIfo->count() > 0)
        {
            foreach ($getRevenuesListForCurrencyIfo as $value)
            {
                $toal_revenue                                = ($value->store_fee);
                $array_map[$value->currency->code][$counter] = $toal_revenue;
                $counter++;
            }

            if ($array_map)
            {
                foreach ($array_map as $key => $res)
                {
                    $array_final[$key] = array_sum($res);
                }
            }
            $data['currency_info'] = $array_final;
            // dd($array_final);
        }
        else
        {
            $data['currency_info'] = [];
        }
        
        $data['total_revenue'] = Transaction::where('percentage', '0.00')->sum('subtotal');
        Transaction::where('read_report_status', '0')->whereIn('transaction_type_id', ['34', '35'])->update(['read_report_status' => '1']);
        return view('admin.reports.index', $data);
    }
    
    public function edit($id)
    {
        $data['menu'] = 'reports';

        $data['revenues'] = Revenue::where('transaction_id', $id)->first();
        
        $data['tansactions'] = Transaction::where('id', $id)->first();
        
        $data['currencies'] = Currency::get();
        
        return view('admin.reports._edit', $data);
    }
    
    
    public function reportCsv()
    {
        return Excel::download(new ReportExport(), 'report_list_' . time() . '.xlsx');
    }

    public function reportPdf()
    {
        $data['company_logo'] = getCompanyLogoWithoutSession();
        $from                 = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to                   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;
        $currency             = isset($_GET['currency']) ? $_GET['currency'] : null;
        $type                 = isset($_GET['type']) ? $_GET['type'] : null;

        $data['revenues'] = $revenues = $this->revenue->getRevenuesList($from, $to, $currency, $type)->orderBy('transactions.id', 'desc')->get();
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
        $mpdf->WriteHTML(view('admin.reports.report_pdf', $data));
        $mpdf->Output('report_list_' . time() . '.pdf', 'D');
    }
 
}
