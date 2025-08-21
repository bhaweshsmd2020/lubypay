<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/frontend/css/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!--Start banner Section-->
<section class="inner-banner">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo app('translator')->get('message.registration.title'); ?></h1>
            </div>
        </div>
    </div>
</section>
<!--End banner Section-->

<!--Start Section-->
<section class="section-01 sign-up padding-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <!-- form card login -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="mb-0"><?php echo app('translator')->get('message.registration.form-title'); ?></h3>
                            </div>

                            <div class="card-body">
                                <?php echo $__env->make('frontend.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <br>

                                <form action="<?php echo e(url('register/store')); ?>" class="form-horizontal" id="register_form" method="POST">
                                    <?php echo e(csrf_field()); ?>


                                    <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                                    <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                                    <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
                                    
                                    <div class="form-group">
                                        <label for="inputAddress"><?php echo app('translator')->get('message.registration.type-title'); ?><span class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control" name="usertype" id="usertype">
                                            <option value='2'>Ewallet Customer</option>
                                            <option value='3'><?php echo app('translator')->get('message.registration.type-merchant'); ?></option>
                                        </select>
                                        <span id="user_error"></span>
                                        <span id="user_ok" class="text-success"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputAddress"><?php echo app('translator')->get('message.registration.first-name'); ?><span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo e(old('first_name')); ?>">
                                        <?php if($errors->has('first_name')): ?>
                                        <span class="error">
                                            <?php echo e($errors->first('first_name')); ?>

                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputAddress"><?php echo app('translator')->get('message.registration.last-name'); ?><span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo e(old('last_name')); ?>">

                                        <?php if($errors->has('last_name')): ?>
                                        <span class="error">
                                            <?php echo e($errors->first('last_name')); ?>

                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputAddress"><?php echo app('translator')->get('message.registration.email'); ?><span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo e(old('email')); ?>">
                                        <?php if($errors->has('email')): ?>
                                        <span class="error">
                                            <?php echo e($errors->first('email')); ?>

                                        </span>
                                        <?php endif; ?>
                                        <span id="email_error"></span>
                                        <span id="email_ok" class="text-success"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputAddress"><?php echo app('translator')->get('message.registration.phone'); ?></span></label>
                                        <br>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                        <?php if($errors->has('phone')): ?>
                                        <span class="error">
                                            <?php echo e($errors->first('phone')); ?>

                                        </span>
                                        <?php endif; ?>
                                        <span id="phone-error"></span>
                                        <span id="tel-error"></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="inputEmail4"><?php echo app('translator')->get('message.registration.password'); ?><span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" id="password">
                                            <?php if($errors->has('password')): ?>
                                                <span class="error">
                                                    <?php echo e($errors->first('password')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        </div>
                                        <div class=" col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="inputPassword4"><?php echo app('translator')->get('message.registration.confirm-password'); ?><span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="checkbox">
                                              <p><?php echo app('translator')->get('message.registration.terms'); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-sm-12 mt20">
                                            <button type="submit" class="btn btn-cust" id="users_create"><i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text"><?php echo app('translator')->get('message.form.button.sign-up'); ?></span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="signin">
                            <div class="message">
                                <span><?php echo app('translator')->get('message.registration.new-account-question'); ?> &nbsp; </span> <a href="<?php echo e(url('login')); ?>"><?php echo app('translator')->get('message.registration.sign-here'); ?></a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('public/frontend/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('public/frontend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('public/frontend/js/isValidPhoneNumber.js')); ?>" type="text/javascript"></script>
    
    <script>
        var hasPhoneError = false;
        var hasEmailError = false;
        var hasUserError = false;
    
        $.validator.setDefaults({
            highlight: function(element) {
                $(element).parent('div').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).parent('div').removeClass('has-error');
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });
    
        jQuery.extend(jQuery.validator.messages, {
            required: "<?php echo e(__('This field is required.')); ?>",
            email: "<?php echo e(__("Please enter a valid email address.")); ?>",
            equalTo: "<?php echo e(__("Please enter the same value again.")); ?>",
            minlength: $.validator.format( "<?php echo e(__("Please enter at least")); ?>"+" {0} "+"<?php echo e(__("characters.")); ?>" ),
            password_confirmation: {
                equalTo: "<?php echo e(__("Please enter same value as the password field!")); ?>",
            },
        })
    
        $('#register_form').validate({
            rules: {
                first_name: {
                    required: true,
                },
                last_name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                password_confirmation: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                },
                type: {
                    required: true,
                },
                phone: {
                    required: true,
                    phone: true,
                },
                usertype: {
                    required: true,
                    usertype: true,
                },
            },
            messages: {
                password_confirmation: {
                    equalTo: "<?php echo e(__("Please enter same value as the password field!")); ?>",
                },
            },
            submitHandler: function(form)
            {
                $("#users_create").attr("disabled", true).click(function (e)
                {
                    e.preventDefault();
                });
                $(".spinner").show();
                $("#users_create_text").text("<?php echo e(__('Signing Up...')); ?>");
                form.submit();
            }
        });
    
        $(document).ready(function()
        {
            var countryList = <?php echo json_encode($countries->pluck('short_name')->toArray(), 15, 512) ?>;  // Pluck country short names into an array
    
            $("#phone").intlTelInput({
                separateDialCode: true,
                nationalMode: true,
                preferredCountries: ["us"],
                onlyCountries: countryList,
                autoPlaceholder: "polite",
                placeholderNumberType: "MOBILE",
                utilsScript: "public/frontend/js/intl-tel-input-13.0.0/build/js/utils.js"
            });
    
            var countryData = $("#phone").intlTelInput("getSelectedCountryData");
            $('#defaultCountry').val(countryData.iso2);
            $('#carrierCode').val(countryData.dialCode);
            var usertype = $('#usertype').val();
    
            $("#phone").on("countrychange", function(e, countryData)
            {
                formattedPhone();
    
                $('#defaultCountry').val(countryData.iso2);
                $('#carrierCode').val(countryData.dialCode);
    
                if ($.trim($(this).val()) !== '')
                {
                    if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                    {
                        $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                        hasPhoneError = true;
                        enableDisableButton();
                        $('#phone-error').hide();
                    }
                    else
                    {
                        $('#tel-error').html('');
    
                        $.ajax({
                            headers:
                            {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: "POST",
                            url: SITE_URL+"/register/duplicate-phone-number-check",
                            dataType: "json",
                            cache: false,
                            data: {
                                'phone': $.trim($(this).val()),
                                'carrierCode': $.trim(countryData.dialCode),
                                'usertype': usertype,
                            }
                        })
                        .done(function(response)
                        {
                            if (response.status == true)
                            {
                                $('#tel-error').html('');
                                $('#phone-error').show();
    
                                $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                                hasPhoneError = true;
                                enableDisableButton();
                            }
                            else if (response.status == false)
                            {
                                $('#tel-error').show();
                                $('#phone-error').html('');
    
                                hasPhoneError = false;
                                enableDisableButton();
                            }
                        });
                    }
                }
                else
                {
                    $('#tel-error').html('');
                    $('#phone-error').html('');
                    hasPhoneError = false;
                    enableDisableButton();
                }
            });
        });
    
        $(document).ready(function()
        {
            $("input[name=phone]").on('blur', function(e)
            {
                formattedPhone();
    
                if ($.trim($(this).val()) !== '')
                {
                    if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                    {
                        $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                        hasPhoneError = true;
                        enableDisableButton();
                        $('#phone-error').hide();
                    }
                    else
                    {
                        var phone = $(this).val().replace(/-|\s/g,"");
                        var phone = $(this).val().replace(/^0+/,"");
                        var usertype = $('#usertype').val();
                        var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                        
                        $.ajax({
                            headers:
                            {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: "POST",
                            url: SITE_URL+"/register/duplicate-phone-number-check",
                            dataType: "json",
                            data: {
                                'phone': phone,
                                'carrierCode': pluginCarrierCode,
                                'usertype': usertype,
                            }
                        })
                        .done(function(response)
                        {
                            if (response.status == true)
                            {
                                if(phone.length == 0)
                                {
                                    $('#phone-error').html('');
                                }
                                else{
                                    $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                                    hasPhoneError = true;
                                    enableDisableButton();
                                }
                            }
                            else if (response.status == false)
                            {
                                $('#phone-error').html('');
                                hasPhoneError = false;
                                enableDisableButton();
                            }
                        });
                        $('#tel-error').html('');
                        $('#phone-error').show();
                        hasPhoneError = false;
                        enableDisableButton();
                    }
                }
                else
                {
                    $('#tel-error').html('');
                    $('#phone-error').html('');
                    hasPhoneError = false;
                    enableDisableButton();
                }
            });
        });
    
        function formattedPhone()
        {
            if ($('#phone').val != '')
            {
                var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g,"");
                $("#formattedPhone").val(p);
            }
        }
    
        $(document).ready(function()
        {
            $("#email").on('input', function(e)
            {
                var email = $('#email').val();
                var usertype = $('#usertype').val();
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/user-registration-check-email",
                    dataType: "json",
                    data: {
                        'email': email,
                        'usertype': usertype,
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        emptyEmail();
                        if (validateEmail(email))
                        {
                            $('#email_error').addClass('error').html(response.fail).css("font-weight", "bold");
                            $('#email_ok').html('');
                            hasEmailError = true;
                            enableDisableButton();
                        } else {
                            $('#email_error').html('');
                        }
                    }
                    else if (response.status == false)
                    {
                        emptyEmail();
                        if (validateEmail(email))
                        {
                            $('#email_error').html('');
                        } else {
                            $('#email_ok').html('');
                        }
                        hasEmailError = false;
                        enableDisableButton();
                    }
    
                    function validateEmail(email) {
                      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                      return re.test(email);
                    }
    
                    function emptyEmail() {
                        if( email.length === 0 )
                        {
                            $('#email_error').html('');
                            $('#email_ok').html('');
                        }
                    }
                });
            });
        });
        
        $(document).ready(function()
        {
            $("#usertype").on('change', function(e)
            {
                var email = $('#email').val();
                var phone = $('#phone').val();
                var usertype = $('#usertype').val();
                var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
            
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/register/duplicate-user-check",
                    dataType: "json",
                    data: {
                        'email': email,
                        'phone': phone,
                        'carrierCode': pluginCarrierCode,
                        'usertype': usertype,
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        $('#user_error').addClass('error').html(response.fail).css("font-weight", "bold");
                        $('#user_ok').html('');
                        hasUserError = true;
                        enableDisableButton();
                    }
                    else if (response.status == false)
                    {
                        $('#user_error').html('');
                        hasUserError = false;
                        enableDisableButton();
                    }
                });
            });
        });
        
        function enableDisableButton()
        {
            if (!hasPhoneError && !hasEmailError && !hasUserError) {
                $('form').find("button[type='submit']").prop('disabled',false);
            } else {
                $('form').find("button[type='submit']").prop('disabled',true);
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/frontend/auth/register.blade.php ENDPATH**/ ?>