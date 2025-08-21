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
                      <h3 class="box-title">Set phone number</h3>
                    </div>
                    
                    <form action="{{ url('admin/users/store') }}" class="form-horizontal" id="searchoperator" method="POST">
                        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                         <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                        <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                        <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Phone
                                    </label>
                                    <div class="col-sm-6">
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                        <span id="phone-error"></span>
                                        <span id="tel-error"></span>
                                    </div>
                                </div>
                                <!-- Status -->

                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary pull-right btn-flat"  data-toggle="collapse" data-target="#demo" id="getoperator" ><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                </div>
                            </div>
                        </input>
                    </form>
                    
                    <!--<button data-toggle="collapse" data-target="#demo">Collapsible</button>-->
                    <div id="demo" class="collapse">
                        <div class="box-header with-border text-center">
                          <h3 class="box-title">Select an operator</h3>
                        </div>
                        <form action="{{ url('admin/users/store') }}" class="form-horizontal" id="user_form" method="POST">
                        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Operator
                                    </label>
                                    <div class="col-sm-6">
                                       <select class="form-control" class="operator" id="operator" onChange="getoperatorid();">
                                          <option>Select Operator</option>
                                       </select>
                                    </div>
                                </div>
                                <!-- Status -->

                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary pull-right btn-flat" id="users_create" data-toggle="collapse" data-target="#demo1"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                </div>
                            </div>
                        </input>
                    </form>
                    </div>
                    
                    
                    <div id="demo1" class="collapse">
                    <div class="box-header with-border text-center">
                      <h3 class="box-title">Enter recharge amount</h3>
                    </div>
                        <form action="{{ url('admin/users/store') }}" class="form-horizontal" id="amountform" method="POST">
                        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                        <input type="hidden" name="defaultCountry1" id="defaultCountry1" class="form-control">
                        <input type="hidden" name="carrierCode1" id="carrierCode1" class="form-control">
                        <input type="hidden" name="formattedPhone1" id="formattedPhone1" class="form-control">
                        <input type="hidden" name="operator_id" id="operator_id" class="form-control">
                        <input type="hidden" name="mobile" id="mobile" class="form-control">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Amount
                                    </label>
                                    <div class="col-sm-6">
                                        <input type="number" min="0" step="any" class="form-control" id="amount" name="amount" onkeyup="getvalue()">
                                        <span id="phone-error"></span>
                                        <span id="tel-error"></span>
                                    </div>
                                </div>
                                <!-- Status -->

                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary pull-right btn-flat" id="topupsubmit"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                
                                </div>
                            </div>
                        </input>
                    </form>
                    </div>

                    
                    
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>

<!-- isValidPhoneNumber -->
<script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$("#getoperator").click(function(){
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/admin/getOperator")}}',
		type: "POST",
        data:$('#searchoperator').serialize(),
		success: function (result) {
		    console.log(result);
		    jQuery("#operator").html(result);

		},
	});
});
function getoperatorid() {
   var operators = jQuery('#operator').val();
   var defaultCountry1 = jQuery('#defaultCountry').val();
   var carrierCode1 = jQuery('#carrierCode').val();
   var formattedPhone1  = jQuery('#formattedPhone').val();
   var mobile  = jQuery('#phone').val();
   $('#operator_id').val(operators);
   $('#defaultCountry1').val(defaultCountry1);
   $('#carrierCode1').val(carrierCode1);
   $('#mobile').val(mobile);
}


function getvalue() {
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/admin/getvalue")}}',
		type: "POST",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result);
		  //  jQuery("#operator").html(result);

		},
	});
}

$("#topupsubmit").click(function(){
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/admin/makerecharge")}}',
		type: "POST",
		dataType:"JSON",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result);
		    if(result.status == "200") {
		        window.location.href = "/admin/topup";
		    } else {
		        window.location.href = "/admin/topup";
		    }
		    //jQuery("#operator").html(result);

		},
	});
});

// ==============




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


$('#searchoperator').validate({
    rules: {
        phone: {
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
                    url: SITE_URL+"/admin/duplicate-phone-number-check1",
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
                    url: SITE_URL+"/admin/duplicate-phone-number-check1",
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
@endpush


