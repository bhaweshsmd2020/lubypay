<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleReport extends Model
{
    protected $table    = 'rule_reports';
    protected $fillable = ['user_id', 'end_user_id', 'trans_id', 'trans_type', 'amount', 'currency_id', 'transactions_hour', 'transactions_day', 'amount_hour', 'amount_day', 'amount_week', 'amount_month', 'same_amount', 'email_day', 'phone_day', 'ipadd_day', 'user_created_at'];
    public $timestamps  = false;
    
    public function getReportsList($from, $to, $type, $trans_type)
    {
        $conditions = [];

        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        
        if (!empty($trans_type) && $trans_type != 'all')
        {
            $conditions['trans_type'] = $trans_type;
        }

        $reports = $this->where($conditions)->orderBy('id', 'desc');
        
        if (!empty($type))
        {
            if($type == '1'){
                $reports->where('rule_reports.transactions_hour', '1')->select('rule_reports.*');
            }elseif($type == '2'){
                $reports->where('rule_reports.transactions_day', '1')->select('rule_reports.*');
            }elseif($type == '3'){
                $reports->where('rule_reports.amount_hour', '1')->select('rule_reports.*');
            }elseif($type == '4'){
                $reports->where('rule_reports.amount_day', '1')->select('rule_reports.*');
            }elseif($type == '5'){
                $reports->where('rule_reports.amount_week', '1')->select('rule_reports.*');
            }elseif($type == '6'){
                $reports->where('rule_reports.amount_month', '1')->select('rule_reports.*');
            }elseif($type == '7'){
                $reports->where('rule_reports.same_amount', '1')->select('rule_reports.*');
            }elseif($type == '8'){
                $reports->where('rule_reports.email_day', '1')->select('rule_reports.*');
            }elseif($type == '9'){
                $reports->where('rule_reports.ipadd_day', '1')->select('rule_reports.*');
            }elseif($type == '10'){
                $reports->where('rule_reports.user_created_at', '1')->select('rule_reports.*');
            }elseif($type == 'all'){
                $reports->select('rule_reports.*');
            }else{
                $reports->select('rule_reports.*');
            }
        }

        if (!empty($date_range))
        {
            $reports->whereDate('rule_reports.created_at', '>=', $from)->whereDate('rule_reports.created_at', '<=', $to)->select('rule_reports.*');
        }
        else
        {
            $reports->select('rule_reports.*');
        }
        return $reports;
    }
}
