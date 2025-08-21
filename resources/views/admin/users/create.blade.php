@extends('admin.layouts.master')

@section('title', 'Add User')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                    <div class="box-header with-border text-center">
                      <h3 class="box-title">Add User</h3>
                    </div>
                    <form action="{{ url('admin/users/store') }}" class="form-horizontal" id="user_form" method="POST">

                        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

                        <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                        <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                        <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">

                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="inputEmail3">User Type</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="usertype" id="usertype">
                                            <option value='2'>Ewallet Customer</option>
                                            <option value='3'>@lang('message.registration.type-merchant')</option>
                                        </select>
                                        <span id="user_error"></span>
                                        <span id="user_ok" class="text-success"></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        First Name
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Enter First Name" name="first_name" type="text" id="first_name" value="">
                                        </input>

                                        @if($errors->has('first_name'))
                                            <span class="error">
                                                {{ $errors->first('first_name') }}
                                            </span>
                                        @endif

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Last Name
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Enter Last Name" name="last_name" type="text" id="last_name" value="">
                                        </input>
                                        @if($errors->has('last_name'))
                                            <span class="error">
                                                {{ $errors->first('last_name') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Phone
                                    </label>
                                    <div class="col-sm-6">
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <span id="phone-error"></span>
                                        <span id="tel-error"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="inputEmail3">
                                        Email
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Enter a valid email" name="email" type="email" id="email">
                                        </input>
                                        @if($errors->has('email'))
                                            <span class="error">
                                                {{ $errors->first('email') }}
                                            </span>
                                        @endif
                                        <span id="email_error"></span>
                                        <span id="email_ok" class="text-success"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="inputEmail3">
                                        Password
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Enter new Password (min 6 characters)" name="password" type="password" id="password">
                                        </input>
                                        @if($errors->has('password'))
                                            <span class="error">
                                                {{ $errors->first('password') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="inputEmail3">
                                        Confirm Password
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Confirm password (min 6 characters)" name="password_confirmation" type="password" id="password_confirmation">
                                        </input>
                                        @if($errors->has('password_confirmation'))
                                            <span class="error">
                                                {{ $errors->first('password_confirmation') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Status</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="status" id="status">
                                            <option value='Active'>Active</option>
                                            <option value='Inactive'>Inactive</option>
                                            <option value='Suspended'>Suspended</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <a class="btn btn-danger btn-flat pull-left" href="{{ url('admin/users') }}" id="users_cancel">Cancel</a>
                                    <button type="submit" class="btn btn-primary pull-right btn-flat" id="users_create"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Create</span></button>
                                </div>
                            </div>
                        </input>
                    </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
    
        var hasPhoneError = false;
        var hasEmailError = false;
        
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
                },
                last_name: {
                    required: true,
                },
                phone:{
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
        
        $(document).ready(function()
        {
            var countryList = @json($countries->pluck('short_name')->toArray());  // Pluck country short names into an array

            $("#phone").intlTelInput({
                separateDialCode: true,
                nationalMode: true,
                preferredCountries: ["us"],
                onlyCountries: countryList,
                autoPlaceholder: "polite",
                placeholderNumberType: "MOBILE",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
            });
        
            var countryData = $("#phone").intlTelInput("getSelectedCountryData");
            $('#defaultCountry').val(countryData.iso2);
            $('#carrierCode').val(countryData.dialCode);
            var usertype = $('#usertype').val();
        
            $("#phone").on("countrychange", function(e, countryData) {
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
                            url: SITE_URL+"/admin/duplicate-phone-number-check",
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
                            url: SITE_URL+"/admin/duplicate-phone-number-check",
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
        
        $(document).ready(function()
        {
            $("#email").on('blur', function(e)
            {
                var email = $('#email').val();
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
                    url: SITE_URL+"/admin/duplicate-user-check",
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
    </script>
@endpush