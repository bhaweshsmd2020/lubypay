
<?php $__env->startSection('title', 'Admin Profile'); ?>

<?php $__env->startSection('head_style'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="box">
       <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <h3><?php echo e(ucwords(Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name)); ?></h3>
                </div>
                <div class="col-md-4 text-right">
                    <?php if(Auth::guard('admin')->user()->status == 'Inactive'): ?>
                        <h3>Status: <span class="label label-danger">Inactive</span></h3>
                    <?php elseif(Auth::guard('admin')->user()->status == 'Suspended'): ?>
                        <h3>Status: <span class="label label-warning">Suspended</span></h3>
                    <?php elseif(Auth::guard('admin')->user()->status == 'Active'): ?>
                        <h3>Status: <span class="label label-success">Active</span></h3>
                    <?php endif; ?>
                </div>
            </div>
       </div>
    </div>
    
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                  <a href='<?php echo e(url('admin/profile')); ?>'>Profile</a>
                </li>

                <li>
                  <a href="<?php echo e(url("admin/change-password")); ?>">Security</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form action='<?php echo e(url("admin/update-admin/$admin_id")); ?>' method="POST" class="form-horizontal" enctype="multipart/form-data" id="profile_form">
                        <?php echo csrf_field(); ?>

                        
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">First Name</label>
                                        <div class="col-sm-8">
                                            <input type="text" value="<?php echo e(Auth::guard('admin')->user()->first_name); ?>" class="form-control" id="first_name" name="first_name">
                                            <span id="val_fname" style="color: red"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Last name</label>
                                        <div class="col-sm-8">
                                            <input type="text" value="<?php echo e(Auth::guard('admin')->user()->last_name); ?>" class="form-control"
                                                   id="last_name" name="last_name">
                                            <span id="val_lname" style="color: red"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail">Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" value="<?php echo e(Auth::guard('admin')->user()->email); ?>" class="form-control" id="em"
                                                   name="email" readonly>
                                            <span id="val_em" style="color: red"></span>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-4 control-label">Picture</label>
                                        <div class="col-sm-8">
                                            <input type="file" name="picture" class="form-control input-file-field" id="admin-picture">
                                            <input type="hidden" name="pic" value="<?php echo e(Auth::guard('admin')->user()->picture ? Auth::guard('admin')->user()->picture : ''); ?>">
                                            <div class="clearfix"></div>
                                            <small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(100,100)); ?></strong></small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4" for="inputEmail3">
                                        </label>
                                        <div class="col-sm-8">
                                            <button type="submit" class="btn btn-primary btn-flat">
                                                <span id="users_edit_text">Update</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-5">
                                    <center>
                                        <?php if(!empty(Auth::guard('admin')->user()->picture)): ?>
                                            <img src='<?php echo e(url("public/uploads/userPic/$admin_picture")); ?>' style="border-radius:100%; width:250px; height:250px; margin-bottom: 30px;">
                                        <?php else: ?>
                                            <img src='<?php echo e(url("public/admin_dashboard/img/avatar.jpg")); ?>' style="border-radius: 50%; width: auto; height: 300px; margin-bottom: 30px;">
                                        <?php endif; ?>
                                    </center>
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
<script src="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')); ?>" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="<?php echo e(asset('public/dist/js/isValidPhoneNumber.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">

    // flag for button disable/enable
    var hasPhoneError = false;
    var hasEmailError = false;

    $(function () {
        $(".select2").select2({
        });

        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ["us"],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            formatOnDisplay: false,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
        })
        .done(function()
        {
            let formattedPhone = '<?php echo e(!empty($users->formattedPhone) ? $users->formattedPhone : NULL); ?>';
            let carrierCode = '<?php echo e(!empty($users->carrierCode) ? $users->carrierCode : NULL); ?>';
            let defaultCountry = '<?php echo e(!empty($users->defaultCountry) ? $users->defaultCountry : NULL); ?>';
            if (formattedPhone !== null && carrierCode !== null && defaultCountry !== null) {
                $("#phone").intlTelInput("setNumber", formattedPhone);
                $('#user_defaultCountry').val(defaultCountry);
                $('#user_carrierCode').val(carrierCode);
            }
        });
    });

    /**
     * [check submit button should be disabled or not]
     * @return {void}
    */
    function enableDisableButton()
    {
        if (!hasPhoneError && !hasEmailError) {
            $('form').find("button[type='submit']").prop('disabled',false);
        } else {
            $('form').find("button[type='submit']").prop('disabled',true);
        }
    }

    function formattedPhone()
    {
        if ($('#phone').val != '')
        {
            let p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g, "");
            $("#formattedPhone").val(p);
        }
    }

/*
intlTelInput
 */

    function checkInvalidAndDuplicatePhoneNumberForUserProfile(phoneVal, phoneData, userId)
    {
        var that = $("input[name=phone]");
        if ($.trim(that.val()) !== '')
        {
            if (!that.intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim(that.val())))
            {
                // alert('invalid');
                $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                hasPhoneError = true;
                enableDisableButton();
                $('#phone-error').hide();
            }
            else
            {
                $('#tel-error').html('');

                var id = $('#id').val();
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/admin/duplicate-phone-number-check",
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': phoneVal,
                        'carrierCode': phoneData,
                        'id': userId,
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
    }

    var countryData = $("#phone").intlTelInput("getSelectedCountryData");
    $('#user_defaultCountry').val(countryData.iso2);
    $('#user_carrierCode').val(countryData.dialCode);

    $("#phone").on("countrychange", function(e, countryData)
    {
        $('#user_defaultCountry').val(countryData.iso2);
        $('#user_carrierCode').val(countryData.dialCode);
        formattedPhone();
        var id = $('#id').val();
        //Invalid Phone Number Validation
        checkInvalidAndDuplicatePhoneNumberForUserProfile($.trim($(this).val()), $.trim(countryData.dialCode), id);
    });

    //Duplicated Phone Number Validation
    $("#phone").on('blur', function(e)
    {
        formattedPhone();
        var id = $('#id').val();
        var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
        var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)
        var pluginCarrierCode = $(this).intlTelInput('getSelectedCountryData').dialCode;
        checkInvalidAndDuplicatePhoneNumberForUserProfile(phone, pluginCarrierCode, id);
    });
/*
intlTelInput
 */

    // Validate email via Ajax
    $(document).ready(function()
    {
        $("#email").on('input', function(e)
        {
            var email = $(this).val();
            var id = $('#id').val();
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/admin/email_check",
                dataType: "json",
                data: {
                    'email': email,
                    'user_id': id,
                }
            })
            .done(function(response)
            {
                emptyEmail(email);
                // console.log(response);
                if (response.status == true)
                {

                    if (validateEmail(email))
                    {
                        $('#emailError').addClass('error').html(response.fail).css("font-weight", "bold");
                        $('#email-ok').html('');
                        hasEmailError = true;
                        enableDisableButton();
                    } else {
                        $('#emailError').html('');
                    }
                }
                else if (response.status == false)
                {
                    hasEmailError = false;
                    enableDisableButton();
                    if (validateEmail(email))
                    {
                        $('#emailError').html('');
                    } else {
                        $('#email-ok').html('');
                    }
                }

                /**
                 * [validateEmail description]
                 * @param  {null} email [regular expression for email pattern]
                 * @return {null}
                 */
                function validateEmail(email) {
                  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                  return re.test(email);
                }

                /**
                 * [checks whether email value is empty or not]
                 * @return {void}
                 */
                function emptyEmail(email) {
                    if( email.length === 0 )
                    {
                        $('#emailError').html('');
                        $('#email-ok').html('');
                    }
                }
            });
        });
    });

    // show warnings on user status change
    $(document).on('change', '#status', function() {
        $status = $('#status').val();
        if ($status == 'Inactive') {
            $('#user-status').text('Warning! User won\'t be able to login.');
        } else if ($status == 'Suspended') {
            $('#user-status').text('Warning! User won\'t be able to do any transaction.');
        } else {
            $('#user-status').text('');
        }
    });

    $('#user_form').validate({
        rules: {
            first_name: {
                required: true,
                // letters_with_spaces_and_dot: true,
            },
            last_name: {
                required: true,
                // letters_with_spaces: true,
            },
             phone: {
                required: true,
                // letters_with_spaces: true,
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                minlength: 6,
            },
            password_confirmation: {
                minlength: 6,
                equalTo: "#password",
            },
        },
        messages: {
            password_confirmation: {
              equalTo: "Please enter same value as the password field!",
            },
        },
        submitHandler: function(form)
        {
            $("#users_edit").attr("disabled", true);
            $(".fa-spin").show();
            $("#users_edit_text").text('Updating...');
            $('#users_cancel').attr("disabled","disabled");
            form.submit();
        }
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/profile/editProfile.blade.php ENDPATH**/ ?>