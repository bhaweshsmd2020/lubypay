<!DOCTYPE html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            Deposits
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
                            @if (!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                                <img src="{{ url('public/images/logos/' . settings('logo')) }}" width="288" height="90" alt="Logo"/>
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
                        <td>Title</td>
                        <td>User</td>
                        <td>End User</td>
                        <td>Transaction Id</td>
                        <td>Transaction Type</td>
                        <td>Amount</td>
                        <td>Time</td>
                    </tr>

                    @foreach($reports as $report)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>
                            <?php
                                if(!empty($report->per_hour_number)){
                                    $report_title = 'Transactions/hour';
                                }elseif(!empty($report->per_hour_amount)){
                                    $report_title = 'Transaction Amount/hour';
                                }elseif(!empty($report->per_email_number)){
                                    $report_title = 'Same Email/Phone';
                                }elseif(!empty($report->per_phone_number)){
                                    $report_title = 'Same Email/Phone';
                                }elseif(!empty($report->per_ip_number)){
                                    $report_title = 'Same IP Address';
                                }elseif(!empty($report->max_amount)){
                                    $report_title = 'Transaction Amount More';
                                }elseif(!empty($report->min_amount)){
                                    $report_title = 'Transaction Amount Less';
                                }elseif(!empty($report->user_created_at)){
                                    $report_title = 'New User';
                                }elseif(!empty($report->last_login_ip)){
                                    $report_title = 'Different Location';
                                }
                                
                                echo $report_title;
                            ?>
                        </td>

                        <td>
                            <?php
                                $user = DB::table('users')->where('id', $report->user_id)->first();
                                $username = $user->first_name.' '.$user->last_name;
                                
                                echo $username;
                            ?>
                        </td>

                        <td>
                            <?php
                                $user = DB::table('users')->where('id', $report->end_user_id)->first();
                                $end_user_name = $user->first_name.' '.$user->last_name;
                                
                                echo $end_user_name;
                            ?>
                        </td>

                        <td>{{ $report->trans_id }}</td>

                        <td>
                            <?php
                                $trans = DB::table('transaction_types')->where('id', $report->trans_type)->first();
                                $transname = $trans->name;
                                
                                echo $transname;
                            ?>
                        </td>

                        <td>{{ $report->amount }}</td>
                        <td>{{ dateFormat($report->created_at) }}</td>

                    </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
