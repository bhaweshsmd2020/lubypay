@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        @media only screen and (max-width: 259px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
        
        .ticket-btn {
    /* border: 2px solid #7d95b6; */
    border-radius: 2px;
    color: #ffffff!important;
    background-color: #f7ab33!important;
}
    </style>
@endsection

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    
                
                
                
                    @include('user_dashboard.layouts.common.alert')
                    
                    @include('user_dashboard.layouts.common.tab')

                    <div class="right mb10">
                        <a href="{{url('/merchant/shippingcostadd')}}" class="btn btn-cust ticket-btn" style="padding: 2px 10px;"><i class="fa fa-user"></i>&nbsp;
                           New Shipping Cost</a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            
                        </div>

                        <div class="table-responsive">
                            @if($list->count() > 0)
                                <table class="table recent_activity">
                                    <thead>
                                    <tr>
                                        <td><strong>@lang('message.dashboard.left-table.id')</strong></td>
                                        <td><strong>Name</strong></td>
                                        <td><strong>carriers</strong></td>
                                        <td><strong>Country</strong></td>
                                        <td><strong>State</strong></td>
                                        <td><strong>City</strong></td>
                                         <td><strong>Price</strong></td>
                                        <td><strong>@lang('message.dashboard.merchant.table.status')</strong></td>
                                        <td><strong>@lang('message.dashboard.merchant.table.action')</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @php $count = 1; @endphp
                                    @foreach($list as $result)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $result->name}} </td>
                                            <td>{{ ($result->carrier_id) ? DB::table('carriers')->where('id',$result->carrier_id)->first()->name :''}} </td>
                                            <td>{{ ($result->country_detail) ? $result->country_detail->name : ''}} </td>
                                            <td>{{ ($result->state_detail) ? $result->state_detail->name :''}} </td>
                                            <td>{{ ($result->city_detail) ? $result->city_detail->city_name :''}} </td>
                                            <td>{{ $result->price}} </td>
                                            @if ($result->status == '1')
                                                <td><span class="badge badge-warning">Active</span></td>
                                            @else
                                                <td><span class="badge badge-success">Inactive</span></td>
                                            @endif
                                            <td>
                                                <a href="{{url('merchant/shippingcostedit/'.$result->id)}}" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i></a>
                                                <a href="{{url('merchant/shippingcostedit/'.$result->id)}}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                        </div>
                        @else
                            <h5 style="padding:15px 10px;">@lang('message.dashboard.merchant.table.not-found')</h5>
                        @endif


                        <div class="card-footer">
                            {{ $list->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="merchantModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('message.dashboard.merchant.html-form-generator.title')</h4>
                        <button type="button" class="close" data-dismiss="modal" id="form-modal-cross">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.merchant-id')</label>
                                    <input readonly type="text" class="form-control" name="merchant_id" id="merchant_id">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="merchant_main_id" id="merchant_main_id">
                                    <input type="hidden" name="currency_id" id="currency_id"/>
                                </div>
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.item-name')</label>
                                    <input type="text" class="form-control" name="item_name" id="item_name">
                                </div>
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.order-number')</label>
                                    <input type="text" class="form-control" name="order" id="order">
                                </div>
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.price')<b><span id="merchantCurrencyCode"></span></b></label>
                                    <input type="text" class="form-control" name="amount" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00"
                                    oninput="restrictNumberToPrefdecimal(this)">
                                </div>
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.custom')</label>
                                    <input type="text" class="form-control" name="custom" id="custom">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.right-form-title')</label>

                                    <div class="pull-right">
                                        <span id="copiedMessage" style="display: none;margin-right: 10px">@lang('message.dashboard.merchant.html-form-generator.right-form-copied')</span>
                                        <span id="copyBtn"
                                              style="cursor: pointer;color: red; font-weight: 800">@lang('message.dashboard.merchant.html-form-generator.right-form-copy')</span>
                                    </div>

                                    <textarea class="form-control" name="html" id="result" rows="8" disabled>
                                        <form method="POST" action="{{url('/payment/form')}}">
                                            <input type="hidden" name="order" id="result_order" value="#"/>
                                            <input type="hidden" name="merchant" id="result_merchant" value="#"/>
                                            <input type="hidden" name="merchant_id" id="result_merchant_id" value="#"/>
                                            <input type="hidden" name="item_name" id="result_item_name" value="Testing payment"/>
                                            <input type="hidden" name="amount" id="result_amount" value="#"/>
                                            <input type="hidden" name="custom" id="result_custom" value="comment"/>
                                            <button type="submit">@lang('message.express-payment.test-payment-form')</button>
                                        </form>
                                    </textarea>
                                    {{-- <p class="help-block">@lang('message.dashboard.merchant.html-form-generator.right-form-footer')</p> --}}
                                    <br>
                                    <p class="help-block" style="text-align: center;font-weight: bold;">@lang('message.dashboard.merchant.html-form-generator.right-form-footer')</p>
                                    <div class="preloader" style="display: none;">
                                        <div class="preloader-img"></div>
                                    </div>

                                    <!-- payment-form-qr-code -->
                                    <div class="payment-form-qr-code" style="text-align: center;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary mr-auto standard-payment-form-close" data-dismiss="modal" id="form-modal-close">
                            @lang('message.dashboard.merchant.html-form-generator.close')
                        </button>
                        <button type="button" class="btn btn-secondary" id="generate-standard-payment-form">@lang('message.dashboard.merchant.html-form-generator.generate')</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- express merchant QrCode modal -->
        <div id="expressMerchantQrCodeModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Express Merchant QR Code</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="preloader" style="display: none;">
                                        <div class="preloader-img"></div>
                                    </div>
                                    <div class="express-merchant-qr-code" style="text-align: center;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">@lang('message.dashboard.merchant.html-form-generator.close')</button>
                        <a href="#" class="btn btn-secondary" id="qr-code-print-express">
                            <strong>Print</strong>
                        </a>
                        <button type="button" class="btn btn-secondary update-express-merchant-qr-code">@lang('message.dashboard.button.update')</button>
                    </div>
                </div>
            </div>
        </div>


        <div id="expressModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('message.dashboard.merchant.html-form-generator.app-info')</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.client-id')</label>
                                    <input type="text" class="form-control" id="client_id" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <label>@lang('message.dashboard.merchant.html-form-generator.client-secret')</label>
                                    <input type="text" class="form-control" id="client_secret" readonly="readonly">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-cust" data-dismiss="modal">@lang('message.dashboard.merchant.html-form-generator.close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')


    @include('common.restrict_number_to_pref_decimal')

    <script>

jQuery.fn.delay = function (time, func) {
            return this.each(function () {
                setTimeout(func, time);
            });
        };

        var result = document.getElementById('result'),
        f1 = document.getElementById('merchant_id'),
        f2 = document.getElementById('item_name'),
        f3 = document.getElementById('order'),
        f4 = document.getElementById('amount'),
        f5 = document.getElementById('custom'),
        f6 = document.getElementById('merchant_main_id'),
        f7 = document.getElementById('currency_id'),

        generateStandardPaymentFormBtn = document.getElementById('generate-standard-payment-form');
        BtnClose = document.getElementById('form-modal-close');
        BtnCross = document.getElementById('form-modal-cross');

        generateStandardPaymentFormBtn.onclick = function ()
        {
            var merchant_id = f1.value,
                item_name = f2.value,
                order = f3.value,
                paymentAmount = f4.value,
                custom = f5.value;
                merchant_main_id = f6.value;
                merchantDefaultCurrency = f7.value;

            result.value =
            '<form method="POST" action="' + SITE_URL + '/payment/form"><input type="hidden" name="merchant" value="'
            + merchant_id + '" /><input type="hidden" name="merchant_id" value="'
            + merchant_main_id + '" /><input type="hidden" name="item_name" value="'
            + item_name + '" /><input type="hidden" name="currency_id" value="'
            + merchantDefaultCurrency + '" /><input type="hidden" name="order" value="'
            + order + '" /><input type="hidden" name="amount" value="' + paymentAmount
            + '" /><input type="hidden" name="custom" value="' + custom + '" /><button type="submit">'+"{{ __('message.express-payment.pay-now') }}"+'</button></form>';

            if (item_name != '' && order != '' && paymentAmount != '' && custom != '' && merchant_main_id != '' && merchantDefaultCurrency != '')
            {
                //generate qr-code for above form
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL + "/merchant/generate-standard-merchant-payment-qrCode",
                    dataType: "json",
                    data: {
                        'merchantId': merchant_main_id,
                        'merchantDefaultCurrency': merchantDefaultCurrency,
                        'paymentAmount': paymentAmount,
                    },
                    beforeSend: function () {
                        $('.preloader').show();
                    },
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        setTimeout(function(){
                            $('.preloader').hide();
                        },2000);

                        $('.payment-form-qr-code').html(`<br>
                            <p class="help-block">-- OR --</p>
                            <br>
                            <p style="font-weight: bold;">Scan QrCode To Pay</p>
                            <br>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data=${response.secret}&amp;size=200x200"/>
                        `);

                        //Add qr-code-print-standard anchor tag on click generateStandardPaymentFormBtn
                        $('.standard-payment-form-close').after(`<a href="#" class="btn btn-secondary" id="qr-code-print-standard">
                            <strong>Print</strong>
                        </a>`);
                    }
                })
                .fail(function(error)
                {
                    console.log(error);
                });
            }
            else
            {
                $('.payment-form-qr-code').html('');
            }
        }

        BtnClose.onclick = function ()
        {
            var val1 = '',
                val2 = '',
                val3 = '',
                val4 = '',
                val5 = '';
                val6 = '';
                val7 = '';

            result.value = '<form method="POST" action="' + SITE_URL
            + '/payment/form"><input type="hidden" name="merchant" value="'
            + val1 + '" /><input type="hidden" name="merchant_id" value="'
            + val6 + '" /><input type="hidden" name="item_name" value="'
            + val2 + '" /><input type="hidden" name="currency_id" value="'
            + val7 + '" /><input type="hidden" name="order" value="' + val3
            + '" /><input type="hidden" name="amount" value="'
            + val4 + '" /><input type="hidden" name="custom" value="'
            + val5 + '" /><button type="submit">Pay now!</button></form>';

            document.getElementById("item_name").value = "";
            document.getElementById("order").value = "";
            document.getElementById("amount").value = "";
            document.getElementById("custom").value = "";
            document.getElementById("merchantCurrencyCode").innerHTML = "";
            $('.payment-form-qr-code').html('');

            $('#qr-code-print-standard').remove();
        }

        BtnCross.onclick = function ()
        {
            var val1 = '',
                val2 = '',
                val3 = '',
                val4 = '',
                val5 = '';
                val6 = '';
                val7 = '';
            result.value = '<form method="POST" action="' + SITE_URL + '/payment/form"><input type="hidden" name="merchant" value="' + val1
            + '" /><input type="hidden" name="merchant_id" value="'
            + val6 + '" /><input type="hidden" name="item_name" value="'
            + val2 + '" /><input type="hidden" name="currency_id" value="'
            + val7 + '" /><input type="hidden" name="order" value="'
            + val3 + '" /><input type="hidden" name="amount" value="'
            + val4 + '" /><input type="hidden" name="custom" value="'
            + val5 + '" /><button type="submit">Pay now!</button></form>';

            document.getElementById("item_name").value = "";
            document.getElementById("order").value = "";
            document.getElementById("amount").value = "";
            document.getElementById("custom").value = "";
            document.getElementById("merchantCurrencyCode").innerHTML = "";
            $('.payment-form-qr-code').html('');
            $('#qr-code-print-standard').remove();
        }

        $(document).on('click','.gearBtn',function(e)
        {
            e.preventDefault();

            if($(this).attr('data-type')=='standard')
            {
                var merchant = $(this).attr('data-marchant');
                $('#merchant_id').val(merchant);

                var merchant_main_id = $(this).attr('data-marchantID');
                $('#merchant_main_id').val(merchant_main_id);
                var merchantCurrencyCode = $(this).attr('data-merchantCurrencyCode');
                if (merchantCurrencyCode) {
                    $('#merchantCurrencyCode').html(', '+merchantCurrencyCode);
                }
                var merchantCurrencyId = $(this).attr('data-merchantCurrencyId');
                $('#currency_id').val(merchantCurrencyId);

                // $('#merchantModal').modal('show');
            }
            else
            {
                var clientId = $(this).attr('data-client-id');
                // console.log(clientId);
                var clientSecrect = $(this).attr('data-client-secret');
                // console.log(clientSecrect);

                $('#client_id').val(clientId);
                $('#client_secret').val(clientSecrect);

                var merchantCurrencyId = $(this).attr('data-merchantCurrencyId');
                $('#currency_id').val(merchantCurrencyId);
                // console.log(merchantCurrencyId);
            }
        });


        function executeExpressMerchantQrCode(endpoint, clientId, clientSecret, merchantId, merchantDefaultCurrencyId)
        {
            if (clientId != '' && clientSecret != '' && merchantId != '' && merchantDefaultCurrencyId != '')
            {
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL + endpoint,
                    dataType: "json",
                    data: {
                        'merchantId': merchantId,
                        'merchantDefaultCurrencyId': merchantDefaultCurrencyId,
                        'clientId': clientId,
                        'clientSecret': clientSecret,
                    },
                    beforeSend: function () {
                        $('.preloader').show();
                        // swal('Please Wait', 'Loading...', {
                        //     closeOnClickOutside: false,
                        //     closeOnEsc: false,
                        //     buttons: false,
                        //     timer: 2000,
                        // });
                    },
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        $('.express-merchant-qr-code').html(`<br>
                            <p style="font-weight: bold;">Scan QR Code</p>
                            <br>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data=${response.secret}&amp;size=200x200"/>
                        `);
                        setTimeout(function(){
                            $('.preloader').hide();
                        },2000);
                        // swal.close();
                    }
                })
                .fail(function(error)
                {
                    console.log(error);
                });
            }
            else
            {
                $('.express-merchant-qr-code').html('');
            }
        }

        //modal on show - generate express merchant qr code
        $('#expressMerchantQrCodeModal').on('show.bs.modal', function (e)
        {
            var endpoint = "/merchant/generate-express-merchant-qr-code";
            var clientId = $(e.relatedTarget).attr('data-clientId');
            var clientSecret = $(e.relatedTarget).attr('data-clientSecret');
            var merchantId = $(e.relatedTarget).attr('data-merchantId');
            var merchantDefaultCurrencyId = $(e.relatedTarget).attr('data-merchantDefaultCurrencyId');

            $('#client_id').val(clientId);
            $('#client_secret').val(clientSecret);
            $('#merchant_id').val(merchantId);
            $('#currency_id').val(merchantDefaultCurrencyId);

            executeExpressMerchantQrCode(endpoint, clientId, clientSecret, merchantId, merchantDefaultCurrencyId);
        });


        //on click - update express merchant qr code
        $(document).on('click','.update-express-merchant-qr-code',function(e)
        {
            e.preventDefault();

            let endpoint = "/merchant/update-express-merchant-qr-code";
            var clientId = $('#client_id').val();
            var clientSecret = $('#client_secret').val();;
            var merchantId = $('#merchant_id').val();
            var merchantDefaultCurrencyId = $('#currency_id').val();
            executeExpressMerchantQrCode(endpoint, clientId, clientSecret, merchantId, merchantDefaultCurrencyId);
        });


        //on click - print express merchant qr code
        $(document).on('click','#qr-code-print-express',function(e)
        {
            e.preventDefault();

            let expressMerchantId = $('#merchant_id').val();
            let printQrCodeUrl = SITE_URL+'/merchant/qr-code-print/'+expressMerchantId+'/express_merchant';
            $(this).attr('href', printQrCodeUrl);
            window.open($(this).attr('href'), '_blank');
        });


        //on click - print standard merchant qr code
        $(document).on('click','#qr-code-print-standard',function(e)
        {
            e.preventDefault();

            let standardMerchantId = $('#merchant_main_id').val();
            let printQrCodeUrl = SITE_URL+'/merchant/qr-code-print/'+standardMerchantId+'/standard_merchant';
            $(this).attr('href', printQrCodeUrl);
            window.open($(this).attr('href'), '_blank');
        });


        $(document).on('click','#copyBtn',function()
        {
            $(this).css('color', 'green');
            $('#result').removeAttr('disabled').select().attr('disabled', 'true');
            document.execCommand('copy');
            $('#copiedMessage').show().delay(5000, function () {
                $('#copiedMessage').fadeOut("slow")
            });
        });


        // $('#client_id,#client_secret').on('focus', function ()
        $(document).on('focus','#client_id,#client_secret',function()
        {
            $(this).select();
            document.execCommand('copy');
            $(this).before("<span style='color: green;font-weight: 700' class='pull-right copied'>Copied</span>").delay(2000, function () {
                $('.copied').remove()
            });
        });
    </script>
@endsection
