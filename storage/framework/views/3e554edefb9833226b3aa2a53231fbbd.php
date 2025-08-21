

<?php $__env->startSection('css'); ?>
    <style>
        @media only screen and (max-width: 206px) {
            .chart-list ul li.active a {
                padding-bottom: 0;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-xs-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <form method="POST" action="<?php echo e(url('transfer')); ?>" id="transfer_form" accept-charset='UTF-8'>

                        <input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">
                        <input type="hidden" name="percentage_fee" id="percentage_fee" value="">
                        <input type="hidden" name="fixed_fee" id="fixed_fee" value="">
                        <input type="hidden" name="fee" class="total_fees" value="0.00">
                        <input type="hidden" name="sendMoneyProcessedBy" id="sendMoneyProcessedBy">

                        <div class="card">
                            <div class="card-header">
                                <div class="chart-list float-left">
                                    <ul>
                                        <?php if(Common::has_permission(auth()->id(),'manage_transfer')): ?>
                                            <li class="active"><a href="<?php echo e(url('/moneytransfer')); ?>"><?php echo app('translator')->get('message.dashboard.send-request.menu.send'); ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if(Common::has_permission(auth()->id(),'manage_request_payment')): ?>
                                            <li>
                                                <a href="<?php echo e(url('/request_payment/add')); ?>"><?php echo app('translator')->get('message.dashboard.send-request.menu.request'); ?></a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>
                                </div>
                            </div>
                            <div class="wap-wed mt20 mb20">
                                <h3 class="ash-font"><?php echo app('translator')->get('message.dashboard.send-request.send.title'); ?></h3>
                                <hr>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.send-request.common.recipient'); ?></label>
                                    <input type="text" class="form-control receiver" value="<?php echo e(isset($transInfo['receiver'])?$transInfo['receiver']:''); ?>" name="receiver" id="receiver"
                                    onkeyup="this.value = this.value.replace(/\s/g, '')">
                                    <span class="receiverError"></span>
                                    <small id="emailHelp" class="form-text text-muted"></small>
                                </div>

                                <!-- removes whitespace -->
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1"><?php echo app('translator')->get('message.dashboard.send-request.common.amount'); ?></label>
                                            <input type="text" class="form-control amount" name="amount" placeholder="0.00" id="amount" onkeyup="this.value = this.value.replace(/^\.|[^\d\.]/g, '')"
                                            value="<?php echo e(isset($transInfo['amount'])?$transInfo['amount']:''); ?>" oninput="restrictNumberToPrefdecimal(this)">
                                            <span class="amountLimit" style="color: red;font-weight: bold"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1"><?php echo app('translator')->get('message.dashboard.send-request.common.currency'); ?></label>
                                            <select class="form-control wallet" name="wallet">
                                               <!--pm_v2.3-->
                                                <?php $__currentLoopData = $walletList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($result->id); ?>" <?php echo e($result->is_default == 'Yes' ? 'selected="selected"' : ''); ?>><?php echo e($result->active_currency->code); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <small id="walletlHelp" class="form-text text-muted">
                                                <?php echo app('translator')->get('message.dashboard.deposit.fee'); ?> (<span
                                                        class="pFees">0</span>%+<span class="fFees">0</span>)
                                                <?php echo app('translator')->get('message.dashboard.deposit.total-fee'); ?> <span class="total_fees">0.00</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.send-request.common.note'); ?></label>
                                    <textarea class="form-control" rows="5" placeholder="<?php echo app('translator')->get('message.dashboard.send-request.common.enter-note'); ?>" name="note" id="note"><?php echo e(isset($transInfo['note'])?$transInfo['note']:''); ?></textarea>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-cust col-12 transfer_form" id="send_money">
                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="send_text"><?php echo app('translator')->get('message.dashboard.button.send-money'); ?></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--/col-->
            </div>
            <!--/row-->
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<script src="<?php echo e(asset('public/user_dashboard/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/additional-methods.min.js')); ?>" type="text/javascript"></script>

<?php echo $__env->make('common.restrict_number_to_pref_decimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
    var recipientErrorFlag = false;
    var amountErrorFlag = false;

    /**
    * [check submit button should be disabled or not]
    * @return {void}
    */
    function enableDisableButton()
    {
        if (!recipientErrorFlag && !amountErrorFlag)
        {
            $("#send_money").attr("disabled", false);
        }
        else
        {
            $("#send_money").attr("disabled", true);
        }
    }

    /**
     * [validateEmail description]
     * @param  {null} email [regular expression for email pattern]
     * @return {null}
     */
    function validateEmail(receiver) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(receiver);
    }

    function getStringAfterPlusSymbol(str)
    {
        return str.split('+')[1];
    }

    function checkMoneyProcessedBy()
    {
        $.ajax(
        {
            url: SITE_URL + "/check-processed-by",
            type: 'GET',
            data: {},
            dataType: 'json',
        })
        .done(function(response)
        {
            // console.log(response.processedBy);
            if (response.status == true)
            {
                if (response.processedBy == "email")
                {
                    $('#receiver').attr("placeholder", "<?php echo e(__("Please enter valid email (ex: user@gmail.com)")); ?>");
                    $('#emailHelp').text("<?php echo e(__("We will never share your email with anyone else.")); ?>");
                }
                else if (response.processedBy == "phone")
                {
                    $('#receiver').attr("placeholder", "<?php echo e(__("Please enter valid phone (ex: +12015550123)")); ?>");
                    $('#emailHelp').text("<?php echo e(__("We will never share your phone with anyone else.")); ?>");
                }
                else if (response.processedBy == "email_or_phone")
                {
                    $('#receiver').attr("placeholder", "<?php echo e(__("Please enter valid email (ex: user@gmail.com) or phone (ex: +12015550123)")); ?>");
                    $('#emailHelp').text("<?php echo e(__("We will never share your email or phone with anyone else.")); ?>");
                }
                $('#receiver').attr("data-processedBy", response.processedBy);
                $('#sendMoneyProcessedBy').val(response.processedBy);
            }
        })
        .fail(function(error)
        {
            console.log(error);
        });
    }

    function emailPhoneValidationCheck(emailOrPhone, sendOrRequestSubmitButton)
    {
        let processedBy = $('#receiver').attr('data-processedBy');
        // console.log(processedBy);
        if (emailOrPhone && emailOrPhone.length != 0)
        {
            let message = '';
            if (processedBy == "email")
            {
                // console.log('by email only');
                if (validateEmail(emailOrPhone))
                {
                    $('.receiverError').html('');
                    recipientErrorFlag = false;
                    enableDisableButton();
                    // sendOrRequestSubmitButton.attr("disabled", false);
                }
                else
                {
                    $('.receiverError').html("<?php echo e(__("Please enter a valid email address.")); ?>").css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                    recipientErrorFlag = true;
                    enableDisableButton();
                    // sendOrRequestSubmitButton.attr("disabled", true);
                }
            }
            else if (processedBy == "phone")
            {
                // console.log('by phone only');
                if (emailOrPhone.charAt(0) != "+" || !$.isNumeric(getStringAfterPlusSymbol(emailOrPhone)))
                {
                    $('.receiverError').html("<?php echo e(__("Please enter valid phone (ex: +12015550123)")); ?>").css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                    recipientErrorFlag = true;
                    enableDisableButton();
                    // sendOrRequestSubmitButton.attr("disabled", true);
                }
                else
                {
                    $('.receiverError').html('');
                    recipientErrorFlag = false;
                    enableDisableButton();
                    // sendOrRequestSubmitButton.attr("disabled", false);
                }
            }
            else if (processedBy == "email_or_phone")
            {
                if (emailOrPhone.charAt(0) != "+" || !$.isNumeric(getStringAfterPlusSymbol(emailOrPhone)))
                {
                    // if (emailOrPhone.includes("@"))
                    if (validateEmail(emailOrPhone))
                    {
                        $('.receiverError').html('');
                        recipientErrorFlag = false;
                        enableDisableButton();
                        // sendOrRequestSubmitButton.attr("disabled", false);
                    }
                    else
                    {
                         $('.receiverError').html("<?php echo e(__("Please enter valid email (ex: user@gmail.com) or phone (ex: +12015550123)")); ?>")
                         .css({
                            'color': 'red',
                            'font-size': '14px',
                            'font-weight': '800',
                            'padding-top': '5px',
                        });
                        recipientErrorFlag = true;
                        enableDisableButton();
                        // sendOrRequestSubmitButton.attr("disabled", true);
                    }
                }
                else
                {
                    $('.receiverError').html('');
                    recipientErrorFlag = false;
                    enableDisableButton();
                    // sendOrRequestSubmitButton.attr("disabled", false);
                }
            }
        }
        else
        {
            $('.receiverError').html('');
            recipientErrorFlag = false;
            enableDisableButton();
            // sendOrRequestSubmitButton.attr("disabled", false);
        }
    }

    function checkReceiverEmailorPhone()
    {
        var token = $('#token').val();
        var receiver = $('#receiver').val().trim();
        if (receiver != '')
        {
            $.ajax({
                method: "POST",
                url: SITE_URL + "/transfer-user-email-phone-receiver-status-validate",
                dataType: "json",
                data: {
                    '_token': token,
                    'receiver': receiver
                }
            })
            .done(function (response)
            {
                if (response.status == true || response.status == 404)
                {
                    $('.receiverError').html(response.message).css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                    recipientErrorFlag = true;
                    enableDisableButton();
                }
                else
                {
                    $('.receiverError').html('');
                    recipientErrorFlag = false;
                    enableDisableButton();
                }
            });
        }
        else
        {
            $('.receiverError').html('');
        }
    }

    function checkAmountLimitAndFeesLimit()
    {
        var token = $("#token").val();
        var amount = $('#amount').val();
        var wallet_id = $('.wallet').val();

        if (amount.length === 0)
        {
            $('.amountLimit').hide();
        }
        else
        {
            $('.amountLimit').show();
            if (amount > 0 && wallet_id)
            {
                $.ajax({
                    method: "POST",
                    url: SITE_URL + "/amount-limit",
                    dataType: "json",
                    data: {
                        "_token": token,
                        'amount': amount,
                        'wallet_id': wallet_id,
                        'transaction_type_id':<?php echo e(Transferred); ?>

                    }
                })
                .done(function (response)
                {
                    checkReceiverEmailorPhone();

                    //console.log(response.success.status);
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
                        $('.amountLimit').text('');
                        amountErrorFlag = false;
                        enableDisableButton();

                        //Not have enough balance - starts
                        if(response.success.totalAmount > response.success.balance)
                        {
                            $('.amountLimit').text("<?php echo e(__("Not have enough balance !")); ?>");
                            amountErrorFlag = true;
                            enableDisableButton();
                        }
                        //Not have enough balance - ends
                    }
                    else
                    {
                        $('.amountLimit').text(response.success.message);
                        amountErrorFlag = true;
                        enableDisableButton();
                    }
                });
            }
        }
    }

    //Code for email and phone validation and Fees Limit  check
    $(window).on('load', function (e)
    {
        checkMoneyProcessedBy();
        let emailOrPhone    = $('#receiver').val().trim();
        if (emailOrPhone != null)
        {
            emailPhoneValidationCheck(emailOrPhone, $("#send_money"));
        }
        checkAmountLimitAndFeesLimit();
    });

    //Code for email and phone validation
    $(document).on('input', ".receiver", function (e)
    {
        let emailOrPhone    = $('#receiver').val().trim();
        if (emailOrPhone != null)
        {
            emailPhoneValidationCheck(emailOrPhone, $("#send_money"));
            checkReceiverEmailorPhone();
        }
    });

    // Code for Fees Limit  check
    $(document).on('input', '.amount', function (e) {
        checkAmountLimitAndFeesLimit();
    });

    // Code for Fees Limit  check
    $(document).on('change', '.wallet', function (e) {
        checkAmountLimitAndFeesLimit();
    });

    jQuery.extend(jQuery.validator.messages, {
        required: "<?php echo e(__('This field is required.')); ?>",
        email: "<?php echo e(__("Please enter a valid email address.")); ?>",
        maxlength: $.validator.format( "<?php echo e(__("Please enter no more than")); ?>"+" {0} "+"<?php echo e(__("characters.")); ?>" ),
    })

    $('#transfer_form').validate({
        rules: {
            amount: {
                required: true,
            },
            receiver: {
                required: true,
                // email: true,
            },
            note: {
                required: true,
                maxlength: 512,
            },
        },
        submitHandler: function (form)
        {
            var pretxt=$("#send_text").html();
            setTimeout(function()
            {
                $("#send_money").removeAttr("disabled");
                $(".spinner").hide();
                $("#send_text").html(pretxt);
            },1000);
            $("#send_money").attr("disabled", true);
            $(".spinner").show();
            $("#send_text").text("<?php echo e(__('Sending Money...')); ?>");
            form.submit();
        }
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/user_dashboard/moneytransfer/create.blade.php ENDPATH**/ ?>