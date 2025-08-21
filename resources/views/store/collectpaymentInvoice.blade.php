<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
</head>
<style>
    *{
        padding:0;
        margin:0;
        box-sizing: border-box;
    }
    p{
        font-size:12px;
    }
    main{
        display:flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    main .receipt{
        max-width: 300px;
        border:1px solid #eee;
        border-radius: 4px;
        padding:0 8px;
        margin:15px;
    }
    .section{
        border-bottom:1px dotted #333;
        padding:6px 0;
    }
    .text-center{
        text-align: center;
    }
    .ml-3{
        margin-left:15px;
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }

    .tabletitle{
    border-bottom: 1px dotted #333;
    }
    .tabletitle td{
        padding:4px 0;
    }

    

</style>
<body style="margin:0;padding:0;" onload="window.print();">
    <main>
        <div class="receipt">
            <div class="section">
                <p class="text-center"><strong>Customer Copy</strong></p><br>
                @if(!empty($store))
                <p class="text-center">{{$store->name??''}}</p>
                <p class="text-center">{{$store->address}}, {{$store->city}}, {{$store->state}}, {{$country->name}} - {{$store->postalcode}}</p>
                @endif
                <p class="text-center">Phone: {{$user->formattedPhone}}</p>
            </div>
            <div class="section">
                <p>Date: {{ Carbon\Carbon::parse($transaction->local_tran_time)->format('d-M-Y h:i A') }}</p>
                <p>Transaction ID: #{{$order->uuid}}</p>
                @if(!empty($order->customer_name))
                    <p>Name: {{$order->customer_name}}</p>
                @endif
                @if(!empty($order->customer_email))
                    <p>Email: {{$order->customer_email}}</p>
                @endif
                @if(!empty($order->customer_phone))
                    <p>Mobile number: {{$order->customer_phone_prefix}} {{$order->customer_phone}}</p>
                @endif
            </div>
                <div id="table">
                    <table>
                        <tr>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                        <tr>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                        <tr>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                        <tr class="g-amt">
                            <td class="tableitem"><p>Total Amount:</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>{{$currency->symbol}}{{ number_format($order->amount, 2, '.', ',') }}</p></td>
                        </tr>
                       
                        <tr class="round-off">
                            <td class="tableitem"><p>Payment method:</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>Card Payment</p></td>
                        </tr>
                        <tr>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                        <tr style="border-top: 1px dotted #333;">
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                    </table>
                </div>
                <p style="padding-bottom:8px;text-align:center;">Thank you, visit again!</p>
            </div>
    </main>
</body>
</html>