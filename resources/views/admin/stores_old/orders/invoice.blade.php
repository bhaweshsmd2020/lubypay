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
        font-size:13px;
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
                <p class="text-center">{{$store->name}}</p>
                <p class="text-center">{{$store->address}}, {{$store->city}}, {{$store->state}}, {{$country->name}} - {{$store->postalcode}}</p>
                <p class="text-center">Phone: {{$user->formattedPhone}}</p>
            </div>
            <div class="section">
                <p>Order ID: #{{$order->unique_id}}</p>
                <p>Date: {{ Carbon\Carbon::parse($transaction->local_tran_time)->format('d-M-Y h:i A') }}</p>
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
                        <tr class="tabletitle">
                            <td class="item"><p>Item</p></td>
                            <td class="qty" align="center"><p>Qty</p></td>
                            <td class="amt" align="right"><p>Amt</p></td>
                        </tr>
                        <tr>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                        
                        <?php 
                            $total_quantity = 0;
                            $all_products = json_decode($order->products);
                        ?>
                        @foreach($all_products as $k=>$product)
                            <?php
                                $product_detail = DB::table('product')->where('id', $product->product_id)->first();
                                
                                if($product_detail->discount_type == 'percent'){
                                    $discount = ($product_detail->discount/100)*$product_detail->price;
                                }else{
                                    $discount = $product_detail->discount;
                                }
                                
                                $total_quantity += $product->qty;
                            ?>
                                                
                            <tr class="product">
                                <td class="tableitem"><p>{{$product_detail->name}}</p></td>
                                <td class="tableitem" align="center"><p>{{$product->qty}}</p></td>
                                <td class="tableitem" align="right"><p>{{$currency->symbol}}{{ number_format(($product_detail->price - $discount) * $product->qty, 2, '.', ',') }}</p></td>
                            </tr>
                        @endforeach

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
                        <tr class="total-qty" >
                            <td class="tableitem"><p>Total Qty:</p></td>
                            <td class="tableitem" align="center"><p>{{$total_quantity}}</p></td>
                            <td class="tableitem" align="right"></td>
                        </tr>
                        <tr class="sub-total">
                            <td class="tableitem"><p>Sub Total:</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>{{$currency->symbol}}{{ number_format($order->subtotal, 2, '.', ',') }}</p></td>
                        </tr>
                        
                        <tr class="sub-total">
                            <td class="tableitem"><p>Discount:</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>{{$currency->symbol}}{{ number_format($order->discount, 2, '.', ',') }}</p></td>
                        </tr>
                        <tr class="tax">
                            <td class="tableitem"><p>Tax @ {{$store->tax}}%</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>{{$currency->symbol}}{{ number_format($order->tax, 2, '.', ',') }}</p></td>
                        </tr>
                        
                        @if(!empty($order->nfc_fee))
                            <tr class="vat">
                                <td class="tableitem"><p>Card processing Fee</p></td>
                                <td class="tableitem" align="center"></td>
                                <td class="tableitem" align="right"><p>{{ $paidcurrency->symbol }}{{ number_format($order->nfc_fee, 2, '.', ',') }}</p></td>
                            </tr>
                        @endif
                        
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
                        <tr>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                            <td style="padding:2px;"></td>
                        </tr>
                        <tr class="g-amt">
                            <td class="tableitem"><p>Total Amount:</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>{{$currency->symbol}}{{ number_format($order->total_amount, 2, '.', ',') }}</p></td>
                        </tr>
                        
                        <?php
                            if($order->payment_method_id == '1'){
                                $pay_met = 'QR Payment';
                            }else{
                                $pay_met = 'Card Payment';
                            }
                        ?>
                        
                        <tr class="round-off">
                            <td class="tableitem"><p>Payment method:</p></td>
                            <td class="tableitem" align="center"></td>
                            <td class="tableitem" align="right"><p>{{$pay_met}}</p></td>
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
                <p style="padding-bottom:8px;">Thank you, visit again!</p>
            </div>
    </main>
</body>
</html>