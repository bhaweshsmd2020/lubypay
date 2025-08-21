

<?php $__env->startSection('css'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="right mb10">
                        <a href="<?php echo e(url('/merchant/add')); ?>" class="btn btn-cust ticket-btn" style="padding: 2px 10px;"><i class="fa fa-user"></i>&nbsp;
                            <?php echo app('translator')->get('message.dashboard.button.new-merchant'); ?></a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li class="active"><a href="<?php echo e(url('/merchants')); ?>"><?php echo app('translator')->get('message.dashboard.merchant.menu.merchant'); ?></a></li>
                                    <li><a href="<?php echo e(url('/merchant/payments')); ?>"><?php echo app('translator')->get('message.dashboard.merchant.menu.payment'); ?></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <?php if($list->count() > 0): ?>
                                <table class="table recent_activity">
                                    <thead>
                                    <tr>
                                        <td><strong><?php echo app('translator')->get('message.dashboard.merchant.table.id'); ?></strong></td>
                                        <td><strong><?php echo app('translator')->get('message.dashboard.merchant.table.business-name'); ?></strong></td>
                                        <td><strong><?php echo app('translator')->get('message.dashboard.merchant.table.site-url'); ?></strong></td>
                                        <td><strong><?php echo app('translator')->get('message.dashboard.merchant.table.type'); ?></strong></td>
                                        <td><strong><?php echo app('translator')->get('message.dashboard.merchant.table.status'); ?></strong></td>
                                        <td><strong><?php echo app('translator')->get('message.dashboard.merchant.table.action'); ?></strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($result->merchant_uuid); ?></td>
                                            <td><?php echo e($result->business_name); ?> </td>
                                            <td><?php echo e($result->site_url); ?> </td>
                                            <td><?php echo e(ucfirst($result->type)); ?> </td>
                                            <?php if($result->status == 'Moderation'): ?>
                                                <td><span class="badge badge-warning"><?php echo app('translator')->get('message.dashboard.merchant.table.moderation'); ?></span></td>
                                            <?php elseif($result->status == 'Disapproved'): ?>
                                                <td><span class="badge badge-danger"><?php echo app('translator')->get('message.dashboard.merchant.table.disapproved'); ?></span></td>
                                            <?php elseif($result->status == 'Approved'): ?>
                                                <td><span class="badge badge-success"><?php echo app('translator')->get('message.dashboard.merchant.table.approved'); ?></span></td>
                                            <?php endif; ?>
                                            <td>
                                                <?php if($result->status == 'Approved'): ?>
                                                    <?php if($result->type=='standard'): ?>
                                                        <button data-type="<?php echo e($result->type); ?>" data-merchantCurrencyCode="<?php echo e(!empty($result->currency) ? $result->currency->code : $defaultWallet->currency->code); ?>"
                                                                data-merchantCurrencyId="<?php echo e(!empty($result->currency) ? $result->currency->id : $defaultWallet->currency_id); ?>"
                                                                data-marchantID="<?php echo e($result->id); ?>" type="button"
                                                                data-marchant="<?php echo e($result->merchant_uuid); ?>" type="button"
                                                                class="btn btn-success btn-sm gearBtn" data-toggle="modal"
                                                                data-target="#merchantModal"
                                                                >
                                                                <i class="fa fa-cog"></i>
                                                        </button>

                                                    <?php else: ?>
                                                    <?php if(!empty($result->appInfo->client_id) && !empty($result->appInfo->client_secret)): ?>
                                                            <!-- expressMerchantQrCodeModal -->
                                                            <button
                                                                    data-clientId="<?php echo e(!empty($result->appInfo->client_id) ? $result->appInfo->client_id : ''); ?>"
                                                                    data-clientSecret="<?php echo e(!empty($result->appInfo->client_secret) ? $result->appInfo->client_secret : ''); ?>"
                                                                    data-merchantId="<?php echo e($result->id); ?>"
                                                                    data-merchantDefaultCurrencyId="<?php echo e(!empty($result->currency) ? $result->currency->id : ''); ?>"
                                                                    type="button" class="btn btn-info btn-sm generateExpressMerchantQrCode" data-toggle="modal"
                                                                    data-target="#expressMerchantQrCodeModal"><i class="fa fa-qrcode"></i>
                                                            </button>

                                                            <button
                                                                    data-client-id="<?php echo e(isset($result->appInfo->client_id) ? $result->appInfo->client_id : ''); ?>"
                                                                    data-client-secret="<?php echo e(isset($result->appInfo->client_secret) ? $result->appInfo->client_secret : ''); ?>"
                                                                    data-merchantCurrencyId="<?php echo e(!empty($result->currency) ? $result->currency->id : $defaultWallet->currency_id); ?>"
                                                                    data-marchantID="<?php echo e($result->id); ?>" type="button"
                                                                    data-marchant="<?php echo e($result->merchant_uuid); ?>" type="button"
                                                                    class="btn btn-success btn-sm gearBtn" data-toggle="modal"
                                                                    data-target="#expressModal"><i class="fa fa-cog"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <a href="<?php echo e(url('merchant/detail/'.$result->id)); ?>" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i></a>
                                                <a href="<?php echo e(url('merchant/edit/'.$result->id)); ?>" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                        </div>
                        <?php else: ?>
                            <!--<h5 style="padding:15px 10px;"><?php echo app('translator')->get('message.dashboard.merchant.table.not-found'); ?></h5>-->
                            <h5 style="padding: 15px 10px; text-align: center;"> <img src="<?php echo e(url('public/images/nodata.png')); ?>" style="    margin: 35px 0px;"></h5>
                        <?php endif; ?>


                        <div class="card-footer">
                            <?php echo e($list->links('vendor.pagination.bootstrap-5')); ?>

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
                        <h4 class="modal-title"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.title'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal" id="form-modal-cross">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.merchant-id'); ?></label>
                                    <input readonly type="text" class="form-control" name="merchant_id" id="merchant_id">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="merchant_main_id" id="merchant_main_id">
                                    <input type="hidden" name="currency_id" id="currency_id"/>
                                </div>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.item-name'); ?></label>
                                    <input type="text" class="form-control" name="item_name" id="item_name">
                                </div>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.order-number'); ?></label>
                                    <input type="text" class="form-control" name="order" id="order">
                                </div>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.price'); ?><b><span id="merchantCurrencyCode"></span></b></label>
                                    <input type="text" class="form-control" name="amount" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00"
                                    oninput="restrictNumberToPrefdecimal(this)">
                                </div>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.custom'); ?></label>
                                    <input type="text" class="form-control" name="custom" id="custom">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.right-form-title'); ?></label>

                                    <div class="pull-right">
                                        <span id="copiedMessage" style="display: none;margin-right: 10px"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.right-form-copied'); ?></span>
                                        <span id="copyBtn"
                                              style="cursor: pointer;color: red; font-weight: 800"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.right-form-copy'); ?></span>
                                    </div>

                                    <textarea class="form-control" name="html" id="result" rows="8" disabled>
                                        <form method="POST" action="<?php echo e(url('/payment/form')); ?>">
                                            <input type="hidden" name="order" id="result_order" value="#"/>
                                            <input type="hidden" name="merchant" id="result_merchant" value="#"/>
                                            <input type="hidden" name="merchant_id" id="result_merchant_id" value="#"/>
                                            <input type="hidden" name="item_name" id="result_item_name" value="Testing payment"/>
                                            <input type="hidden" name="amount" id="result_amount" value="#"/>
                                            <input type="hidden" name="custom" id="result_custom" value="comment"/>
                                            <button type="submit"><?php echo app('translator')->get('message.express-payment.test-payment-form'); ?></button>
                                        </form>
                                    </textarea>
                                    
                                    <br>
                                    <p class="help-block" style="text-align: center;font-weight: bold;"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.right-form-footer'); ?></p>
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
                            <?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.close'); ?>
                        </button>
                        <button type="button" class="btn btn-secondary" id="generate-standard-payment-form"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.generate'); ?></button>
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
                        <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.close'); ?></button>
                        <a href="#" class="btn btn-secondary" id="qr-code-print-express">
                            <strong>Print</strong>
                        </a>
                        <button type="button" class="btn btn-secondary update-express-merchant-qr-code"><?php echo app('translator')->get('message.dashboard.button.update'); ?></button>
                    </div>
                </div>
            </div>
        </div>


        <div id="expressModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.app-info'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.client-id'); ?></label>
                                    <input type="text" class="form-control" id="client_id" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.client-secret'); ?></label>
                                    <input type="text" class="form-control" id="client_secret" readonly="readonly">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-cust" data-dismiss="modal"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.close'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>


    <?php echo $__env->make('common.restrict_number_to_pref_decimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
            + '" /><input type="hidden" name="custom" value="' + custom + '" /><button type="submit">'+"<?php echo e(__('message.express-payment.pay-now')); ?>"+'</button></form>';

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/Merchant/list.blade.php ENDPATH**/ ?>