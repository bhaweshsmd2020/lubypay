<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            Merchants
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
                       <td>Sr. No</td>
                        <td>ID</td>
                        <td>First Name</td>
                        <td>Last Name</td>
                        <td>Phone</td>
                        <td>Email</td>
                        <td>Country</td>
                        <td>Group</td>
                        <td>Last Login</td>
                        <td>Registration Date</td>
                        <td>Last Location</td>
                        <td>IP</td>
                        <td>Status</td>
                    </tr>

                    @foreach($merchants as $key=>$merchant)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $merchant->qp_id }}</td>
                        <td>{{ $merchant->first_name }}</td>
                        <td>{{ $merchant->last_name }}</td>
                        <td>{{ (isset($merchant->phone)) ? $merchant->phone : '-' }}</td>
                        <td>{{ (isset($merchant->email)) ? $merchant->email : '-' }}</td>
                        <td>{{ (isset($merchant->country)) ?  DB::table('countries')->where('short_name',$merchant->country)->first()->name??'': '-'}}</td>
                        <td>{{ ucfirst($merchant->type) }}</td>
                        <td>{{ $merchant->last_login }}</td>
                        <td>{{ dateFormat($merchant->created_at) }}</td>
                        <td>{{ $merchant->city }} | {{DB::table('countries')->where('short_name',$merchant->country)->first()->name??''}}</td>
                        <td>{{ isset($merchant->ip_address) ? $merchant->ip_address: "-" }}</td>

                        <td>{{ $merchant->status }}</td>
                    </tr>

                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
