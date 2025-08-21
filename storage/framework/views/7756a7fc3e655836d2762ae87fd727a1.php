

<?php $__env->startSection('title', 'Edit Profile'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

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
                  <a href="<?php echo e(url("admin/users/kyc-verications/$users->id")); ?>">KYC Verifications</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/bankdetails/$users->id")); ?>">Bank Details</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/address_edit/$users->id")); ?>">Address</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/activity-logs/$users->id")); ?>">Activity Logs</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <?php if($users->status == 'Inactive'): ?>
                <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-danger">Inactive</span></h3>
            <?php elseif($users->status == 'Suspended'): ?>
                <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-warning">Suspended</span></h3>
            <?php elseif($users->status == 'Active'): ?>
                <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-success">Active</span></h3>
            <?php endif; ?>
        </div>
        <div class="col-md-2"></div>

        <div class="col-md-5">
            <div class="pull-right">
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_deposit')): ?>
                    <a style="margin-top: 15px;border-radius: 15px;" href="<?php echo e(url('admin/users/deposit/create/'.$users->id)); ?>" class="btn btn-success btn-flat">
                        <i class="glyphicon glyphicon-download" ></i>Deposit
                    </a>&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
                    
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_withdrawal')): ?>
                    <a style="margin-top: 15px;border-radius: 15px;" href="<?php echo e(url('admin/users/withdraw/create/'.$users->id)); ?>" class="btn  btn-info btn-flat">
                        <i class="glyphicon glyphicon-upload"></i>Payout
                    </a>&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_transfer')): ?>
                    <a style="margin-top: 15px;border-radius: 15px;" href="<?php echo e(url('admin/users/moneytransfer/'.$users->id)); ?>" class="btn btn-primary btn-flat">
                        <i class="glyphicon glyphicon-transfer"></i>Transfer
                    </a>&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_ticket')): ?>
                    <a style="margin-top: 15px;border-radius: 15px;" href="<?php echo e(url('admin/tickets/add')); ?>" class="btn btn-info btn-flat">
                        <i class="fa fa-ticket"></i>Add Ticket
                    </a>&nbsp;&nbsp;&nbsp;
                <?php endif; ?> 

            </div>
        </div>
    </div>

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!-- form start -->
                <form action="<?php echo e(url('admin/users/update')); ?>" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                    <?php echo e(csrf_field()); ?>


                    <input type="hidden" value="<?php echo e($users->id); ?>" name="id" id="id" />
                    <input type="hidden" value="<?php echo e($users->defaultCountry); ?>" name="user_defaultCountry" id="user_defaultCountry" />
                    <input type="hidden" value="<?php echo e($users->carrierCode); ?>" name="user_carrierCode" id="user_carrierCode" />
                    <input type="hidden" name="formattedPhone" id="formattedPhone">

                    <div class="box-body">
                        <?php if(count($errors) > 0): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="inputEmail3">User Type</label>
                                        <div class="col-sm-8">
                                            <select class="select2" name="usertype" id="usertype">
                                                <option value='2' <?php if($users->role_id == '2'): ?> selected <?php endif; ?>>Ewallet Customer</option>
                                                <option value='3' <?php if($users->role_id == '3'): ?> selected <?php endif; ?>><?php echo app('translator')->get('message.registration.type-merchant'); ?></option>
                                            </select>
                                            <span id="user_error"></span>
                                            <span id="user_ok" class="text-success"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            First Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update First Name" name="first_name" type="text" id="first_name" value="<?php echo e($users->first_name); ?>">
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Middle Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update Middle Name" name="middle_name" type="text" id="middle_name" value="<?php echo e($users->middle_name); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Second  Last Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update Second Last Name" name="second_last_name" type="text" id="second_last_name" value="<?php echo e($users->second_last_name); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Last Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update Last Name" name="last_name" type="text" id="last_name" value="<?php echo e($users->last_name); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Phone
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                            <span id="phone-error"></span>
                                            <span id="tel-error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="inputEmail3">
                                            Email
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update Email" name="email" type="email" id="email" value="<?php echo e($users->email); ?>">
                                            <span id="emailError"></span>
                                            <span id="email-ok" class="text-success"></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Password
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update Password (min 6 characters)" name="password" type="password" id="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Confirm Password
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Confirm password (min 6 characters)" name="password_confirmation" type="password" id="password_confirmation">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="status">Account Status</label>
                                        <div class="col-sm-8">
                                            <select class="select2" name="status" id="status">
                                                <option value='Active' <?php echo e($users->status == 'Active' ? 'selected' : ''); ?>>Active</option>
                                                <option value='Inactive' <?php echo e($users->status == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                                                <option value='Suspended' <?php echo e($users->status == 'Suspended' ? 'selected' : ''); ?>>Suspended</option>
                                            </select>
                                            <label id="user-status" class="error" for="status"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="status-reason">Reason</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="status-reason" name="status_reason" placeholder="Please provide a reason..."><?php echo e($users->status_reason); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="email_status">Email Status</label>
                                        <div class="col-sm-8">
                                            <select class="select2" name="email_status" id="email_status">
                                                <option value='0' <?php echo e($users->email_status == '0' ? 'selected' : ''); ?>>Not Verified</option>
                                                <option value='1' <?php echo e($users->email_status == '1' ? 'selected' : ''); ?>>Verified</option>
                                            </select>
                                            <label id="email_status" class="error" for="email_status"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="phone_status">Phone Status</label>
                                        <div class="col-sm-8">
                                            <select class="select2" name="phone_status" id="phone_status">
                                                <option value='0' <?php echo e($users->phone_status == '0' ? 'selected' : ''); ?>>Not Verified</option>
                                                <option value='1' <?php echo e($users->phone_status == '1' ? 'selected' : ''); ?>>Verified</option>
                                            </select>
                                            <label id="phone_status" class="error" for="phone_status"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="kyc_status">KYC Status</label>
                                        <div class="col-sm-8">
                                            <select class="select2" name="kyc_status" id="kyc_status">
                                                <option value='0' <?php echo e($users->kyc_status == '0' ? 'selected' : ''); ?>>Processing</option>
                                                <option value='1' <?php echo e($users->kyc_status == '1' ? 'selected' : ''); ?>>Verified</option>
                                                <option value='2' <?php echo e($users->kyc_status == '2' ? 'selected' : ''); ?>>Unverified</option>
                                                <option value='3' <?php echo e($users->kyc_status == '3' ? 'selected' : ''); ?>>Rejected</option> 
                                            </select>
                                            <label id="kyc_status" class="error" for="kyc_status"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label require" for="inputEmail3">
                                            Profile Image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update Profile Image" name="picture" type="file" id="picture">
                                        </div>
                                    </div>

                                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')): ?>
                                        <div class="form-group">
                                            <label class="col-sm-4" for="inputEmail3">
                                            </label>
                                            <div class="col-sm-8" style="display: flex;">
                                                <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/users')); ?>" id="users_cancel" style="margin-right: 20px;">
                                                    Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary pull-right btn-flat" id="users_edit">
                                                    <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_edit_text">Update</span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-5">
                                    <center>
                                        <?php if($users->picture): ?>
                                            <img src="<?php echo e(url('').'/public/user_dashboard/profile/'.$users->picture); ?>" style="border-radius:100%; width:250px; height:250px; margin-bottom: 30px;">
                                        <?php else: ?>
                                            <img src="<?php echo e(url('').'/public/user_dashboard/profile/user.png'); ?>" style="border-radius: 50%; width: auto; height: 300px; margin-bottom: 30px;">
                                        <?php endif; ?>
                                    </center>
                                    <?php  
                                        $last_location=DB::table('users_login_location')->where('user_id',$users->id)->first();
                                        $country=DB::table('countries')->where('short_name',$last_location->country??'')->first()->name??'';
                                        $divices=DB::table('devices')->where('user_id',$users->id??'')->first()??'';
                                    ?>
                                    <p>Account Status : <?php echo e($users->status); ?></p>
                                    <p>KYC Status : 
                                        <?php if($users->kyc_status == '1'): ?>
                                            Completed
                                        <?php else: ?>
                                            Not Completed
                                        <?php endif; ?>
                                    </p>
                                    <?php
                                        if(!empty($users->local_tran_time)){
                                            $local_time = $users->local_tran_time;
                                        }else{
                                            $local_time = $users->created_at;
                                        }
                                        
                                        $store = DB::table('stores')->where('user_id', $users->id)->first();
                                    ?>

                                    <p>Last Login : <?php if($users->user_detail->last_login_at): ?> <?php echo e(Carbon\Carbon::parse($users->user_detail->last_login_at)->format('d-M-Y h:i A')); ?> <?php endif; ?></p>
                                    <p>Registration Date : <?php echo e(Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A')); ?></p>
                                    <p>Last Location : <?php if($last_location): ?>  <?php echo e($last_location->city); ?> | <?php echo e($country??''); ?> <?php else: ?> - <?php endif; ?></p>
                                    <p>Last IP : <?php echo e($users->user_detail->last_login_ip); ?></p>
                                    <p>App Version : <?php echo e($divices->app_ver??''); ?></p>
                                    <p>Device Name : <?php echo e(ucfirst($divices->device_manufacture??'')??''); ?> | <?php echo e($divices->device_name??''); ?> | <?php echo e($divices->device_model??''); ?></p>
                                    <p>OS : <?php echo e(ucfirst($divices->device_os??'')??''); ?> <?php echo e($divices->os_ver??''); ?></p>
                                    <?php if(!empty($store)): ?>
                                        <p>Store Name : <?php echo e($store->name); ?></p>
                                    <?php endif; ?>
                                </div>

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
        
        var countryList = <?php echo json_encode($countries->pluck('short_name')->toArray(), 15, 512) ?>;
        
        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ["us"],
            onlyCountries: countryList,
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
                var usertype = $('#usertype').val();
                
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
            var usertype = $('#usertype').val();
            
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/admin/duplicate-email-check",
                dataType: "json",
                data: {
                    'email': email,
                    'user_id': id,
                    'usertype': usertype,
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
    
    $(document).ready(function()
    {
        $("#usertype").on('change', function(e)
        {
            var email = $('#email').val();
            var phone = $('#phone').val();
            var usertype = $('#usertype').val();
            var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
            var id = $('#id').val();
        
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/admin/duplicate-user-check",
                dataType: "json",
                data: {
                    'email': email,
                    'phone': phone,
                    'carrierCode': pluginCarrierCode,
                    'usertype': usertype,
                    'user_id': id,
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
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>