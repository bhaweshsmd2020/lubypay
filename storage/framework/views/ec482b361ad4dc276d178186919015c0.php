

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
                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <?php if(Common::has_permission(auth()->id(),'manage_transfer')): ?>
                                        <li><a href="<?php echo e(url('/moneytransfer')); ?>"><?php echo app('translator')->get('message.dashboard.send-request.menu.send'); ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if(Common::has_permission(auth()->id(),'manage_request_payment')): ?>
                                        <li class="active">
                                            <a href="<?php echo e(url('/request_payment/add')); ?>"><?php echo app('translator')->get('message.dashboard.send-request.menu.request'); ?></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <form method="POST" action="<?php echo e(url('request')); ?>" id="requestpayment_create_form" accept-charset='UTF-8'>
                            <input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">
                            <input type="hidden" name="requestMoneyProcessedBy" id="requestMoneyProcessedBy">

                            <div class="wap-wed mt20 mb20">
                                <h3 class="ash-font"><?php echo app('translator')->get('message.dashboard.send-request.request.title'); ?></h3>
                                <hr>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.send-request.common.recipient'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo e(isset($transInfo['email'])?$transInfo['email']:''); ?>" name="email" id="requestCreatorEmail" onkeyup="this.value = this.value.replace(/\s/g, '')">
                                    <span class="requestCreatorEmailOrPhoneError"></span>
                                    <small id="emailHelp" class="form-text text-muted"></small>

                                    <?php if($errors->has('email')): ?>
                                        <span class="error">
                                            <?php echo e($errors->first('email')); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1"><?php echo app('translator')->get('message.dashboard.send-request.common.amount'); ?></label>
                                            <input type="text" class="form-control" name="amount" placeholder="0.00" type="text" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                            value="<?php echo e(isset($transInfo['amount'])?$transInfo['amount']:''); ?>" oninput="restrictNumberToPrefdecimal(this)">
                                            <?php if($errors->has('amount')): ?>
                                                <span class="error">
                                                    <?php echo e($errors->first('amount')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                        <label for="exampleInputPassword1"><?php echo app('translator')->get('message.dashboard.send-request.common.currency'); ?></label>
                                            <select class="form-control" name="currency_id">
                                                <!--pm_v2.3-->
                                                <?php $__currentLoopData = $currencyList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($result['id']); ?>" <?php echo e($defaultWallet->currency_id == $result['id'] ? 'selected="selected"' : ''); ?>><?php echo e($result['code']); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('message.dashboard.send-request.common.note'); ?></label>
                                        <textarea class="form-control" rows="5" placeholder="<?php echo app('translator')->get('message.dashboard.send-request.common.enter-note'); ?>" name="note" id="note"><?php echo e(isset($transInfo['note'])?$transInfo['note']:''); ?></textarea>
                                    <?php if($errors->has('note')): ?>
                                        <span class="error">
                                            <?php echo e($errors->first('note')); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-cust col-12" id="rp_money">
                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="rp_text"><?php echo app('translator')->get('message.dashboard.button.send-request'); ?></span>
                                </button>
                            </div>
                        </form>
                    </div>
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

<script type="text/javascript">

    /**
     * [requestMoneyValidateEmail description]
     * @param  {null} email [regular expression for email pattern]
     * @return {null}
     */
    function requestMoneyValidateEmail(receiver) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(receiver);
    }

    function requestMoneyGetStringAfterPlusSymbol(str)
    {
        return str.split('+')[1];
    }

    function checkRequestMoneyProcessedBy()
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
                    $('#requestCreatorEmail').attr("placeholder", "<?php echo e(__("Please enter valid email (ex: user@gmail.com)")); ?>");
                    $('#emailHelp').text("<?php echo e(__("We will never share your email with anyone else.")); ?>");
                }
                else if (response.processedBy == "phone")
                {
                    $('#requestCreatorEmail').attr("placeholder", "<?php echo e(__("Please enter valid phone (ex: +12015550123)")); ?>");
                    $('#emailHelp').text("<?php echo e(__("We will never share your phone with anyone else.")); ?>");
                }
                else if (response.processedBy == "email_or_phone")
                {
                    $('#requestCreatorEmail').attr("placeholder", "<?php echo e(__("Please enter valid email (ex: user@gmail.com) or phone (ex: +12015550123)")); ?>");
                    $('#emailHelp').text("<?php echo e(__("We will never share your email or phone with anyone else.")); ?>");
                }
                $('#requestMoneyProcessedBy').val(response.processedBy);
            }
        })
        .fail(function(error)
        {
            console.log(error);
        });
    }

    function requestMoneyEmailPhoneValidationCheck(emailOrPhone, sendOrRequestSubmitButton)
    {
        var processedBy = $('#requestMoneyProcessedBy').val();
        if (emailOrPhone && emailOrPhone.length != 0)
        {
            let message = '';
            if (processedBy == "email")
            {
                // console.log('by email only');
                if (requestMoneyValidateEmail(emailOrPhone))
                {
                    $('.requestCreatorEmailOrPhoneError').html('');
                    sendOrRequestSubmitButton.attr("disabled", false);
                }
                else
                {
                    $('.requestCreatorEmailOrPhoneError').html("<?php echo e(__("Please enter a valid email address.")); ?>").css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                    sendOrRequestSubmitButton.attr("disabled", true);
                }
            }
            else if (processedBy == "phone")
            {
                // console.log('by phone only');
                if (emailOrPhone.charAt(0) != "+" || !$.isNumeric(requestMoneyGetStringAfterPlusSymbol(emailOrPhone)))
                {
                    $('.requestCreatorEmailOrPhoneError').html("<?php echo e(__("Please enter valid email (ex: user@gmail.com) or phone (ex: +12015550123)")); ?>").css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                    sendOrRequestSubmitButton.attr("disabled", true);
                }
                else
                {
                    $('.requestCreatorEmailOrPhoneError').html('');
                    sendOrRequestSubmitButton.attr("disabled", false);
                }
            }
            else if (processedBy == "email_or_phone")
            {
                if (emailOrPhone.charAt(0) != "+" || !$.isNumeric(requestMoneyGetStringAfterPlusSymbol(emailOrPhone)))
                {
                    // if (emailOrPhone.includes("@"))
                    if (requestMoneyValidateEmail(emailOrPhone))
                    {
                        $('.requestCreatorEmailOrPhoneError').html('');
                        sendOrRequestSubmitButton.attr("disabled", false);
                    }
                    else
                    {
                         $('.requestCreatorEmailOrPhoneError').html("<?php echo e(__("Please enter valid email (ex: user@gmail.com) or phone (ex: +12015550123)")); ?>")

                         .css({
                            'color': 'red',
                            'font-size': '14px',
                            'font-weight': '800',
                            'padding-top': '5px',
                        });
                        sendOrRequestSubmitButton.attr("disabled", true);
                    }
                }
                else
                {
                    $('.requestCreatorEmailOrPhoneError').html('');
                    sendOrRequestSubmitButton.attr("disabled", false);
                }
            }
        }
        else
        {
            $('.requestCreatorEmailOrPhoneError').html('');
            sendOrRequestSubmitButton.attr("disabled", false);
        }
    }

    function IsRequestMoneyEmailPhoneValid()
    {
        let emailOrPhone    = $('#requestCreatorEmail').val().trim();
        if (emailOrPhone != null) {
            requestMoneyEmailPhoneValidationCheck(emailOrPhone, $("#rp_money"));
        }
    }

    function checkRequestCreatorEmailorPhone(emailOrPhone)
    {
        if (emailOrPhone)
        {
            $.ajax({
                method: "POST",
                url: SITE_URL+"/request_payment/request-user-email-phone-receiver-status-validate",
                dataType: "json",
                data: {
                     '_token':$('#token').val(),
                    'requestCreatorEmailOrPhone': emailOrPhone,
                }
            })
            .done(function(response)
            {
                // console.log(response);
                if (response.status == true || response.status == 404)
                {
                    $('.requestCreatorEmailOrPhoneError').html(response.message).css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                    $('form').find("button[type='submit']").prop('disabled', true);
                }
                else
                {
                    $('.requestCreatorEmailOrPhoneError').html('');
                    $('form').find("button[type='submit']").prop('disabled', false);
                }
            });
        }
    }

    $(window).load(function(){
        checkRequestMoneyProcessedBy();
        IsRequestMoneyEmailPhoneValid();
    });

    //Code for Email validation
    $(document).on('input',"#requestCreatorEmail",function(e)
    {
        IsRequestMoneyEmailPhoneValid();
        let emailOrPhone    = $('#requestCreatorEmail').val().trim();
        checkRequestCreatorEmailorPhone(emailOrPhone);
    });

    jQuery.extend(jQuery.validator.messages, {
        required: "<?php echo e(__('This field is required.')); ?>",
        maxlength: $.validator.format( "<?php echo e(__("Please enter no more than")); ?>"+" {0} "+"<?php echo e(__("characters.")); ?>" ),
    })

    $('#requestpayment_create_form').validate({
        rules: {
            amount: {
                required: true,
            },
            email: {
                required: true,
            },
            note: {
                required: true,
                maxlength: 512,
            },
        },
        submitHandler: function(form)
        {
            var pretxt=$("#rp_text").text();
            setTimeout(function(){
                $("#rp_money").removeAttr("disabled");
                $(".spinner").hide();
                $("#rp_text").text(pretxt);
            },1000);

            $("#rp_money").attr("disabled", true);
            $(".spinner").show();
            $("#rp_text").text("<?php echo e(__('Sending Request...')); ?>");
            form.submit();
        }
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/requestPayment/add.blade.php ENDPATH**/ ?>