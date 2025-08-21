

<?php $__env->startSection('title', 'Payout'); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                  <a href='<?php echo e(url("admin/users/edit/$users->id")); ?>'>Profile</a>
                </li>

                <li>
                  <a href="<?php echo e(url("admin/users/transactions/$users->id")); ?>">Transactions</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/wallets/$users->id")); ?>">Wallets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/tickets/$users->id")); ?>">Tickets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/disputes/$users->id")); ?>">Disputes</a>
                </li>
                
                <li>
                  <a href="<?php echo e(url("admin/users/photoproof/$users->id")); ?>">Photo Proof</a>
                </li>
                
                <li >
                  <a href="<?php echo e(url("admin/users/addressproof/$users->id")); ?>">Address Proof</a>
                </li>
                
                <li >
                  <a href="<?php echo e(url("admin/users/idproof/$users->id")); ?>">Identity Proof</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/bankdetails/$users->id")); ?>">Bank Details</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button style="margin-top: 15px;"  type="button" class="btn button-secondary btn-flat active">Payout</button>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-4">
            <div class="pull-right">
                <h3><?php echo e($users->first_name.' '.$users->last_name); ?></h3>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form action="<?php echo e(url("admin/users/withdraw/create/$users->id")); ?>" method="post" accept-charset='UTF-8' id="admin-user-withdraw-create">
                        <input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">

                        <input type="hidden" name="user_id" id="user_id" value="<?php echo e($users->id); ?>">

                        <input type="hidden" name="fullname" id="fullname" value="<?php echo e($users->first_name.' '.$users->last_name); ?>">

                        <input type="hidden" name="payment_method" id="payment_method" value="<?php echo e($payment_met->id); ?>">

                        <input type="hidden" name="percentage_fee" id="percentage_fee" value="">
                        <input type="hidden" name="fixed_fee" id="fixed_fee" value="">
                        <input type="hidden" name="fee" class="total_fees" value="0.00">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                            <label><?php echo app('translator')->get('message.dashboard.payout.new-payout.payment-method'); ?></label>
                                            
                                            
                                            <select class="form-control" name="payment_method" id="method" required>
                                                <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($method->type =='3'): ?>
                                                        <option data-obj="<?php echo e(json_encode($method->getAttributes())); ?>" value="<?php echo e($method->type); ?>" data-type="<?php echo e($method->type); ?>">
                                                            <?php echo e($method->paymentMethod->name); ?> (<?php echo e($method->email); ?>)
                                                        </option>
                                                    <?php elseif($method->type == '6'): ?>
                                                        <option data-obj="<?php echo e(json_encode($method->getAttributes())); ?>" value="<?php echo e($method->type); ?>" data-type="<?php echo e($method->type); ?>">
                                                            <?php echo e($method->paymentMethod->name); ?> (<?php echo e($method->account_name); ?>)
                                                        </option>
                                                    <?php else: ?>
                                                        <option data-obj="<?php echo e(json_encode($method->getAttributes())); ?>" value="<?php echo e($method->type); ?>" data-type="<?php echo e($method->type); ?>">
                                                            <?php echo e($method->paymentMethod->name); ?> (<?php echo e($method->account_number); ?>)
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                </div>
                                
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Amount</label>
                                        <input type="text" class="form-control amount" name="amount" placeholder="0.00" type="text" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                        value="" oninput="restrictNumberToPrefdecimal(this)">
                                        <span class="amountLimit" style="color: red;font-weight: bold"></span>
                                        <div class="clearfix"></div>
                                        <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Currency</label>
                                        <!--<select class="select2 wallet" name="currency_id" id="currency_id">-->
                                        <!--    <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>-->
                                        <!--    <option data-wallet="<?php echo e($row->id); ?>" value="8">MVR</option>-->
                                                <!--<option data-wallet="<?php echo e($row->id); ?>" value="<?php echo e($row->active_currency->id); ?>"><?php echo e($row->active_currency->code); ?></option>-->
                                        <!--    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->
                                        <!--</select>-->
                                         <select class="select2 wallet" name="currency_id" id="currency_id">
                                          
                                            <option  value="8">MVR</option>
                                               
                                        </select>
                                    </div>
                                    <small id="walletlHelp" class="form-text text-muted">
                                        Fee(<span class="pFees">0</span>%+<span class="fFees">0</span>),
                                        Total:  <span class="total_fees">0.00</span>
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-5">
                                    <a href="<?php echo e(url('admin/users/edit/'. $users->id)); ?>" class="btn button-secondary"><span><i class="fa fa-angle-left"></i>&nbsp;Back</span></a>
                                    <button type="submit" class="btn button-secondary" id="withdrawal-create">
                                        <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                                        <span id="withdrawal-create-text">Next&nbsp;<i class="fa fa-angle-right"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<?php echo $__env->make('common.restrict_number_to_pref_decimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

    $(".select2").select2({});

    $('#admin-user-withdraw-create').validate({
        rules: {
            amount: {
                required: true,
            },
        },
        submitHandler: function (form)
        {
            $("#withdrawal-create").attr("disabled", true);
            $(".fa-spin").show();
            var pretext=$("#withdrawal-create-text").text();
            $("#withdrawal-create-text").text('Payout...');
            form.submit();
            setTimeout(function(){
                $("#withdrawal-create-text").html(pretext + '<i class="fa fa-angle-right"></i>');
                $("#withdrawal-create").removeAttr("disabled");
                $(".fa-spin").hide();
            },1000);

        }
    });

    $(window).on('load', function (e) {
        checkAmountLimitAndFeesLimit();
    });

    $(document).on('input', '.amount', function (e) {
        checkAmountLimitAndFeesLimit();
    });
    $(document).on('change', '.wallet', function (e) {
        checkAmountLimitAndFeesLimit();
    });

    function checkAmountLimitAndFeesLimit()
    {
        var token = $("#token").val();
        var amount = $('#amount').val();
        log(amount);
        var currency_id = $('#currency_id').val();
        var payment_method_id = $('#payment_method').val();

        $.ajax({
            method: "POST",
            url: SITE_URL + "/admin/users/withdraw/amount-fees-limit-check",
            dataType: "json",
            data: {
                "_token": token,
                'amount': amount,
                'currency_id': currency_id,
                'payment_method_id': payment_method_id,
                'user_id': '<?php echo e($users->id); ?>',
                'transaction_type_id': '<?php echo e(Withdrawal); ?>'
            }
        })
        .done(function (response)
        {
            // console.log(response);

            if (response.success.status == 200)
            {
                $("#percentage_fee").val(response.success.feesPercentage);
                $("#fixed_fee").val(response.success.feesFixed);
                $(".percentage_fees").html(response.success.feesPercentage);
                $(".fixed_fees").html(response.success.feesFixed);
                $(".total_fees").val(response.success.totalFees);
                $('.total_fees').html(response.success.totalFeesHtml);
                $('.pFees').html(response.success.pFeesHtml);
                $('.fFees').html(response.success.fFeesHtml);

                //Balance Checking
                if(response.success.totalAmount > response.success.balance)
                {
                    $('.amountLimit').text("Insufficient Balance");
                    $("#withdrawal-create").attr("disabled", true);
                }
                else
                {
                    $('.amountLimit').text('');
                    $("#withdrawal-create").attr("disabled", false);
                }
                return true;
            }
            else
            {
                if (amount == '')
                {
                    $('.amountLimit').text('');
                }
                else
                {
                    $('.amountLimit').text(response.success.message);
                    $("#withdrawal-create").attr("disabled", true);
                    return false;
                }
            }
        });
    }

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/users/withdraw/create.blade.php ENDPATH**/ ?>