<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            Revenues
        </title>
    </head>
    <style>
        body {
        font-family: "DeJaVu Sans", Helvetica, sans-serif;
        color: #121212;
        line-height: 15px;
    }

    table, tr, td {
        padding: 6px 6px;
        border: 1px solid black;
    }

    tr {
        height: 40px;
    }

    </style>

    <body>
        <div style="width:100%; margin:0px auto;">
            <div style="height:80px">
                <div style="width:80%; float:left; font-size:13px; color:#383838; font-weight:400;">
                    <div>
                        <strong>
                            {{ ucwords(Session::get('name')) }}
                        </strong>
                    </div>
                    <br>
                    <div>
                        Period : {{ $date_range }}
                    </div>
                    <br>
                    <div>
                        Print Date : {{ dateFormat(now())}}
                    </div>
                </div>
                <div style="width:20%; float:left;font-size:15px; color:#383838; font-weight:400;">
                    <div>
                        <div>
                            @if (!empty($company_logo))
                                <img src="{{ url('public/images/logos/'.$company_logo) }}" width="288" height="90" alt="Logo"/>
                            @else
                                <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" width="288" height="90">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both">
            </div>
            <div style="margin-top:30px;">
                <table style="width:100%; border-radius:1px;  border-collapse: collapse;">
                    <tr style="background-color:#f0f0f0;text-align:center; font-size:12px; font-weight:bold;">

                        <td>Date</td>
                        <td>Transaction Type</td>
                        <td>Percentage Charge</td>
                        <td>Fixed Charge</td>
                        <td>Total</td>
                        <td>Transactional</td>   
                        <td>Operational</td>     
                        <td>Operational A</td>   
                        <td>Operational B</td>  
                        <td>Currency</td>
                    </tr>

                    @foreach($revenues as $revenue)
                    
                    <?php
                        $new_transaction = DB::table('revenues')->where('transaction_id', $revenue->id)->first();
                        if($new_transaction){
                            $transactional_revenue = $new_transaction->transactional;
                            $operational_revenue = $new_transaction->operational;
                            $operationala_revenue = $new_transaction->operational_a;
                            $operationalb_revenue = $new_transaction->operational_b;
                        }else{
                            $transactional = DB::table('settings')->where('id', '37')->first();
                            $operational   = DB::table('settings')->where('id', '38')->first();
                            $operational_a = DB::table('settings')->where('id', '39')->first();
                            $operational_b = DB::table('settings')->where('id', '40')->first();
                    
                            $revenue_new = ($revenue->charge_percentage + $revenue->charge_fixed); 
                            $transactional_revenue = ($transactional->value*$revenue_new)/100;
                            $operational_revenue = ($operational->value*$revenue_new)/100;
                            $operationala_revenue = ($operational_a->value*$operational_revenue)/100;
                            $operationalb_revenue = ($operational_b->value*$operational_revenue)/100;
                        }
                    ?>

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>{{ dateFormat($revenue->created_at) }}</td>

                        <td>{{ ($revenue->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $revenue->transaction_type->name) }}</td>

                        <td>{{ ($revenue->charge_percentage == 0) ?  '-' : formatNumber($revenue->charge_percentage) }}</td>

                        <td>{{ ($revenue->charge_fixed == 0) ?  '-' : formatNumber($revenue->charge_fixed) }}</td>

                        @php
                            $total = ($revenue->charge_percentage == 0) && ($revenue->charge_fixed == 0) ? '-' : formatNumber($revenue->charge_percentage + $revenue->charge_fixed);
                        @endphp

                        <td>{{ '+'.$total }}</td>
                        
                        <td>{{ formatNumber($transactional_revenue) }} </td>
                        <td>{{ formatNumber($operational_revenue) }}</td>
                        <td>{{ formatNumber($operationala_revenue) }}</td>
                        <td>{{ formatNumber($operationalb_revenue) }}</td>

                        <td>{{ $revenue->currency->code }}</td>

                    </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
