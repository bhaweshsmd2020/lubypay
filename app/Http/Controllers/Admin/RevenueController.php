<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\RevenuesDataTable;
use App\DataTables\Admin\RevenuesstoreDataTable;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;

class RevenueController extends Controller
{
    protected $revenue;
    public function __construct()
    {
        $this->revenue = new Transaction();
    }

    public function revenues_list()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'wallet_revenues';

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
                $data['revenues'] = $query = (new Transaction())->getRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from         = setDateForDb($_GET['from']);
                $data['to'] = $to           = setDateForDb($_GET['to']);
                $data['revenues'] = $query = (new Transaction())->getRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from     = null;
            $data['to'] = $to       = null;
            $data['currency'] = $currency = 'all';
            $data['type'] = $type     = 'all';
            $data['revenues'] = $query = (new Transaction())->getRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
        }
        $getRevenuesListForCurrencyIfo = $this->revenue->getRevenuesList($from, $to, $currency, $type)->orderBy('transactions.id', 'desc')->get();

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
        Transaction::where('read_revenue_status', '0')->whereIn('transaction_type_id', [Deposit, Withdrawal, Transferred, Request_To, Payment_Received, Crypto_Sent, Recharge, Cable, Exchange_From, Exchange_To, 32])->update(['read_revenue_status' => '1']);
        return view('admin.revenues.list', $data);
    }
    
    public function store_revenues_list()
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_revenues';

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
                $data['revenues'] = $query = (new Transaction())->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from         = setDateForDb($_GET['from']);
                $data['to'] = $to           = setDateForDb($_GET['to']);
                $data['revenues'] = $query = (new Transaction())->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from     = null;
            $data['to'] = $to       = null;
            $data['currency'] = $currency = 'all';
            $data['type'] = $type     = 'all';
            $data['revenues'] = $query = (new Transaction())->getstoreRevenuesList($from, $to, $currency, $type)->orderBy('id', 'desc')->get();
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
        Transaction::where('read_revenue_status', '0')->whereIn('transaction_type_id', ['34', '35'])->update(['read_revenue_status' => '1']);
        return view('admin.revenues.store_list', $data);
    }

    public function revenueCsv()
    {
        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;
        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $data['revenues'] = $revenues = $this->revenue->getRevenuesList($from, $to, $currency, $type)->orderBy('transactions.id', 'desc')->get();

        $datas = [];
        if (!empty($revenues))
        {
            foreach ($revenues as $key => $value)
            {
                $datas[$key]['Date']              = dateFormat($value->created_at);
                $datas[$key]['Transaction Type']  = ($value->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $value->transaction_type->name);
                $datas[$key]['Percentage Charge'] = ($value->charge_percentage == 0) ? '-' : formatNumber($value->charge_percentage);
                $datas[$key]['Fixed Charge']      = ($value->charge_fixed == 0) ? '-' : formatNumber($value->charge_fixed);
                $datas[$key]['Total']             = ($value->charge_percentage == 0) && ($value->charge_fixed == 0) ? '-' : '+'.formatNumber($value->charge_percentage + $value->charge_fixed);
                $datas[$key]['Currency']          = $value->currency->code;
            }
        }
        else
        {
            $datas[0]['Date']              = '';
            $datas[0]['Transaction Type']  = '';
            $datas[0]['Percentage Charge'] = '';
            $datas[0]['Fixed Charge']      = '';
            $datas[0]['Total']             = '';
            $datas[0]['Currency']          = '';
        }
        // dd($datas);

        return Excel::create('revenues_list_' . time() . '', function ($excel) use ($datas)
        {
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->sheet('mySheet', function ($sheet) use ($datas)
            {
                $sheet->cells('A1:F1', function ($cells)
                {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($datas);
            });
        })->download();
    }

    public function revenuePdf()
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
        $mpdf->WriteHTML(view('admin.revenues.revenues_report_pdf', $data));
        $mpdf->Output('revenues_report_' . time() . '.pdf', 'D');
    }

}
