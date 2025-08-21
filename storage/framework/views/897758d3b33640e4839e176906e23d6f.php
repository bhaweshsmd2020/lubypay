

<?php $__env->startSection('title', 'Add Banner'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>
<?php 
      $language=DB::table('languages')->where('status','Active')->get();
?>
<?php $__env->startSection('page_content'); ?>
<div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">
                    Add Banner
                  </div>
                </div>
                <div class="col-md-3 pull-right" style="margin-top:4px;">
                        <a href="<?php echo e(url('admin/banner')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-chevron-left"> &nbsp;</span>Back to banners list</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                    <div class="box-header  text-center">
                      <h3 class="box-title"></h3>
                    </div>
                    <form action="<?php echo e(url('admin/banner/store')); ?>" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">

                        <input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">
                        
                            <div class="box-body">
                                
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Banner
                                    </label>
                                    <div class="col-sm-6">
                                        <input type="file" name="banner_image" class="form-control input-file-field">
                                        <?php if($errors->has('banner_image')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('banner_image')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Banner Title
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Banner Title" name="banner_title" type="text" id="banner_title" value="">
                                        </input>

                                        <?php if($errors->has('banner_title')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('first_name')); ?>

                                            </span>
                                        <?php endif; ?>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                       Banner Text
                                    </label>
                                    <div class="col-sm-6">
                                        <textarea class="form-control" name="banner_text" id="banner_text"></textarea>
                                        <?php if($errors->has('banner_text')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('banner_text')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                  <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="app_redirect">Select Redirect Type</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="app_redirect" id="app_redirect">
                                            <option value='0'>None</option>
                                            <option value='1'>App Page</option>
                                            <option value='2'>Redirect URL</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        App Page
                                    </label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="app_page" id="app_page">
                                            <option value="">Select App Page</option>
                                            <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value='<?php echo e($page->id); ?>'><?php echo e($page->page_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Redirect URL
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" name="redirect_url" id="redirect_url"></input>
                                        <?php if($errors->has('banner_text')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('banner_text')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                

                                  <!-- Status -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Position</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="position" id="position">
                                            <option value='Top'>Top</option>
                                            <option value='Bottom'>Bottom</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Language -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Language</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="language" id="language" required>
                                            <?php $__currentLoopData = $language; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value->id); ?>"><?php echo e($value->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                

                                <!-- Status -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Platform</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="platform" id="platform">
                                            <option value='ewallet'>Ewallet</option>
                                            <option value='mpos'>Mpos</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Status</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="status" id="status">
                                            <option value='Active'>Active</option>
                                            <option value='Inactive'>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="box-footer" style="display:flex;justify-content:center;">
                                    <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/banner')); ?>" id="">Cancel</a>&nbsp;&nbsp;
                                    <button type="submit" class="btn btn-primary btn-flat" id=""><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Create</span></button>
                                </div>
                            </div>
                        </input>
                    </form>
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
        var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g,"");
        $("#formattedPhone").val(p);
    }
}

$(function () {
    $(".select2").select2({
    });
});

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
        $("#users_create").attr("disabled", true);
        $(".fa-spin").show();
        $("#users_create_text").text('Creating...');
        $('#users_cancel').attr("disabled",true);
        form.submit();
    }
});


/*
intlTelInput
 */

$(document).ready(function()
{
    $("#phone").intlTelInput({
        separateDialCode: true,
        nationalMode: true,
        preferredCountries: ["us"],
        autoPlaceholder: "polite",
        placeholderNumberType: "MOBILE",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
    });

    var countryData = $("#phone").intlTelInput("getSelectedCountryData");
    $('#defaultCountry').val(countryData.iso2);
    $('#carrierCode').val(countryData.dialCode);

    $("#phone").on("countrychange", function(e, countryData)
    {
        formattedPhone();
        // log(countryData);
        $('#defaultCountry').val(countryData.iso2);
        $('#carrierCode').val(countryData.dialCode);

        if ($.trim($(this).val()) !== '')
        {
            if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
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
                        'phone': $.trim($(this).val()),
                        'carrierCode': $.trim(countryData.dialCode),
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

//Invalid Number Validation - admin create
$(document).ready(function()
{
    $("input[name=phone]").on('blur', function(e)
    {
        formattedPhone();
        if ($.trim($(this).val()) !== '')
        {
            if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
            {
                // alert('invalid');
                $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                hasPhoneError = true;
                enableDisableButton();
                $('#phone-error').hide();
            }
            else
            {
                var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
                var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)

                // console.log(phone);

                var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/admin/duplicate-phone-number-check",
                    dataType: "json",
                    data: {
                        'phone': phone,
                        'carrierCode': pluginCarrierCode,
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

/*
intlTelInput
 */

// Validate Emal via Ajax
$(document).ready(function()
{
    $("#email").on('blur', function(e)
    {
        var email = $('#email').val();
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
            }
        })
        .done(function(response)
        {
            // console.log(response);
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

// $(document).ready(function()
// {
//     $("#email").on('keyup keypress', function(e)
//     {
//        if (e.type=="keyup" || e.type=="keypress")
//        {
//        }
//     });
// });

</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/banner/add.blade.php ENDPATH**/ ?>