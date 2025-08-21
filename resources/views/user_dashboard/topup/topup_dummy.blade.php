@extends('user_dashboard.layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
<style>
    .amount{
        cursor:pointer;
        border-radius:6px;
    }
    .content p{
        margin-bottom:8px;
    }
    .select-wallet{
        border:0;
        padding-left:10px;
    }
    .step1,.step2,.step3,.step4,.step5,.step6{
        display:none;
    }
    .show{
        display:flex;
        justify-content:center;
    }
    
</style>
@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
             Page title start 
            <div>
                <h3 class="page-title">{{ __('Topup') }}</h3>
            </div>
             Page title end
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="box box-info">
                            <div class="box-header with-border text-center">
                              <h3 class="box-title">Set phone number</h3>
                            </div>
                            
                            <form class="form-horizontal" id="searchoperator" method="POST">
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
                                         Status 
        
                                        <div class="box-footer">
                                            <button type="button" class="btn btn-primary pull-right btn-flat"  data-toggle="collapse" data-target="#demo" id="getoperator" ><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                        </div>
                                    </div>
                                </input>
                            </form>
                            
                            <!--<button data-toggle="collapse" data-target="#demo">Collapsible</button>-->
                            <div id="demo" class="collapse">
                                <div class="box-header with-border text-center">
                                  <h3 class="box-title">Select a Plan</h3>
                                </div>
                                <form action="{{ url('admin/users/store') }}" class="form-horizontal" id="user_form" method="POST">
                                <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="inputEmail3">
                                                Plans
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="hidden" name="operator_id_new" id="operator_id_new" class="form-control" value="0">
                                                <select class="form-control" class="operator" id="operator" name="operator" onChange="getoperatorid();">
                                                    <option>Select Plan</option>
                                                </select>
                                            </div>
                                        </div>
                                         Status 
        
                                        <div class="box-footer">
                                            <button type="button" class="btn btn-primary pull-right btn-flat" id="getvalue" data-toggle="collapse" data-target="#demo1"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                        </div>
                                    </div>
                                </input>
                            </form>
                            </div>
                            
                            
                            <div id="demo1" class="collapse">
                            <div class="box-header with-border text-center">
                              <h3 class="box-title">Select Wallet</h3>
                            </div>
                                <form action="{{ url('admin/users/store') }}" class="form-horizontal" id="amountform" method="POST">
                                <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                                <input type="hidden" name="defaultCountry1" id="defaultCountry1" class="form-control">
                                <input type="hidden" name="carrierCode1" id="carrierCode1" class="form-control">
                                <input type="hidden" name="formattedPhone1" id="formattedPhone1" class="form-control">
                                <input type="hidden" name="operator_id" id="operator_id" class="form-control">
                                <input type="hidden" name="mobile" id="mobile" class="form-control">
                                <input type="hidden" class="form-control" id="operator_amt" name="amount" >
                                    <div class="box-body">
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="inputEmail3">
                                                Select Wallet
                                            </label>
                                            <div class="col-sm-6">
                                                <select name="wallet" id="wallet" class="form-control">
                                                    @foreach($wallets as $wallet)
                                                        @foreach($currencies as $currency)
                                                            @if($currency->id == $wallet->currency_id)
                                                                <option value="{{$wallet->id}}">{{$currency->code}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
        
                                        <div class="box-footer">
                                            <button type="button" class="btn btn-primary pull-right btn-flat" id="topupsubmit" data-toggle="collapse" data-target="#demo2"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                        </div>
                                    </div>
                                </input>
                            </form>
                            </div>
        
                            
                            
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid py-5 step1 show" id="step1">
            <div class="col-md-6 px-0" >
                <div class="set-ph border p-3" style="border-radius:10px;">
                    <p class="mb-3">1. Set phone number <i class="fas fa-info-circle"></i></p>
                   <div class="box box-info">
                        <form class="form-horizontal" id="searchoperator" method="POST">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                            <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                            <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                            <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
                                <div class="box-body">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                            <span id="phone-error"></span>
                                            <span id="tel-error"></span>
                                        </div>
                                    </div>
                                        Status 
        
                                    <div class="box-footer">
                                        <button type="button" class="btn btn-primary pull-right btn-flat step1-next"  id="getoperator" ><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                    </div>
                                </div>
                            </input>
                        </form>
                    </div>
                </div>
                <div class="border p-3" style="border-top:0;border-radius:10px;">
                    <p>2. Select an operator</p>
                </div>
                <div class="border p-3" style="border-top:0;border-radius:10px;">
                    <p>3. Select recharge amount </p>
                </div
            </div>
        </div>
        </div>
        <div class="container-fluid py-5 step2 " id="step2">
        <div class="col-md-6 border px-0" style="border-radius:10px;">
            <div class="border-bottom">
                <div class="ph-no p-3 d-flex justify-content-between align-items-center">
                    <p>1. <i class="fas fa-phone-alt"></i> +9180814 80818</p>
                    <i class="fas fa-check text-success"></i>
                </div>
            </div>
            
            <div class="operator border-bottom">
                <div class="d-flex justify-content-between p-3">
                    <div class="d-flex align-items-center">
                        <p>2.</p>
                        <img src="https://www.91-cdn.com/hub/wp-content/uploads/2022/07/Airtel-feat-1.jpg?tr=q-100" class="ml-2" width="50px" height="50px">
                        <p class="ml-2">Airtel India</p>
                    </div>
                    <i class="fas fa-check text-success"></i>
                </div>
                <div class="d-flex p-3">
                    <a href="">not the correct operator?</a>
                    <a href="" class="ml-3">View bundles & data?</a>
                </div>
            </div>
            
            <div class="recharge p-3">
                <p>3. Select recharge amount</p>
                <div class="d-flex flex-wrap mt-3" style="row-gap:15px; column-gap:15px;">
                    <p class="amount p-2 bg-secondary ">$ 0.17</p>
                    <p class="amount p-2 bg-secondary ">$ 0.17</p>
                    <p class="amount p-2 bg-secondary ">$ 0.17</p>
                    <p class="amount p-2 bg-secondary ">$ 0.17</p>
                    <p class="amount p-2 bg-secondary ">$ 0.17</p>
                    <p class="amount p-2 bg-secondary ">$ 0.17</p>
                </div>
                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary step2-back" >Back</button>
                    <button type="button" class="btn btn-primary ml-3 step2-next">Next</button>
                </div>
            </div>
        </div>
    </div>
        <div class="container-fluid py-5 step3 " id="step3">
            <div class="col-md-6 border py-3" style="border-radius:10px;">
                <div class="text-center">
                    <h3 class="mb-4 font-weight-bold">Please confirm your request</h3>
                    <div class="content">
                        <p><i class="fas fa-phone-alt"></i> +9180814 80818</p>
                        <p>Operator: Airtel India</p>
                        <p>Topup Amount: $ 0.17</p>
                        <p>Fee: $0.01</p>
                        <p>Total Amount: $0.18</p>
                        <p>Delivery amount: INR 10.2</p>
                    </div>
                    <div class="d-flex mt-5 justify-content-center">
                        <button type="button" class="btn btn-primary step3-cancel">Cancel</button>
                        <button type="button" class="btn btn-primary ml-3  step3-confirm" >Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid py-5 step4 " id="step4">
            <div class="col-md-6 border px-0" style="border-radius:10px;">
                <div class="p-3 border-bottom">
                    <p>Recharge Amount : KYD 0.14</p>
                    <p>Fees : KYD 0.35</p>
                    <p>Total : KYD 0.49</p>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="px-3 py-2">
                        <p>Payable Amount</p>
                        <p class="mt-2">KYD <span class="text-success">0.49</span></p>
                    </div>
                    <select class="select-wallet">
                        <option>KYD</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary btn-block mt-2 step4-pay" >Pay KYD 0.49</button>
            </div>
        </div>
        <div class="container-fluid py-5 step5 " id="step5">
            <div class="col-md-6 border py-3" style="border-radius:10px;">
                <div class="transaction">
                    <h3 class="mb-3 font-weight-bold">Transaction details</h3>
                    <div class="content">
                        <p>Transaction: #11434145</p>
                        <p>Date and time: 2023-01-07 00:50:22</p>
                    </div>
                    <div id="accordion">
                        <div class="" id="headingOne">
                          <h5 class="mb-0">
                            <a class="btn btn-link" data-toggle="collapse" data-target="#details" aria-expanded="true" aria-controls="details">
                              <i class="fas fa-angle-down mr-2"></i>Show more details
                            </a>
                          </h5>
                        </div>
                        <div id="details" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                          <div class="card-body">
                            <p>Recharge Amount : KYD 0.14</p>
                            <p>Fees : KYD 0.35</p>
                            <p>Total : KYD 0.49</p>
                          </div>
                        </div>
                     </div>
                    <div class="d-flex mt-4">
                        <button type="button" class="btn btn-primary step5-new">New Topup</button>
                        <button type="button" class="btn btn-primary ml-3">Print receipt</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</section>



@endsection

@section('js')
<script>
    $(".step1-next").click(function(){
        $('#step1').removeClass('show');
        $('#step2').addClass('show');
        
    })
    $(".step2-next").click(function(){
        $('#step2').removeClass('show');
        $('#step3').addClass('show');
        
    })
    $(".step2-back").click(function(){
        $('#step2').removeClass('show');
        $('#step1').addClass('show');
        
    })
    $(".step3-confirm").click(function(){
        $('#step3').removeClass('show');
        $('#step4').addClass('show');
        
    })
    $(".step3-cancel").click(function(){
        $('#step3').removeClass('show');
        $('#step1').addClass('show');
        
    })
    $(".step4-pay").click(function(){
        $('#step4').removeClass('show');
        $('#step5').addClass('show');
        
    })
    $(".step5-new").click(function(){
        $('#step5').removeClass('show');
        $('#step1').addClass('show');
        
    })
    
</script>

<script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="{{ theme_asset('public/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$("#getoperator").click(function(){
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/getOperator")}}',
		type: "POST",
        data:$('#searchoperator').serialize(),
		success: function (result) {
		    console.log(result);
		    jQuery("#operator").html(result.value);
		    jQuery("#operator_id_new").val(result.operatorId);

		},
	});
});
function getoperatorid() {
   var operators = jQuery('#operator_id_new').val();
   var operator_amt = jQuery('#operator').val();
   var defaultCountry1 = jQuery('#defaultCountry').val();
   var carrierCode1 = jQuery('#carrierCode').val();
   var formattedPhone1  = jQuery('#formattedPhone').val();
   var mobile  = jQuery('#phone').val();
   $('#operator_id').val(operators);
   $('#defaultCountry1').val(defaultCountry1);
   $('#carrierCode1').val(carrierCode1);
   $('#mobile').val(mobile);
   $('#operator_amt').val(operator_amt);
}

$("#getvalue").click(function(){
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/getvalue")}}',
		type: "POST",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result);
		  //  jQuery("#operator").html(result);

		},
	});
})

$("#topupsubmit").click(function(){
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/makerecharge")}}',
		type: "POST",
		dataType:"JSON",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result);
		    if(result.status == "200") {
		        window.location.href = "/topup";
		    } else {
		        window.location.href = "/topup";
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
                    url: SITE_URL+"/duplicate-phone-number-check1",
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
                    url: SITE_URL+"/duplicate-phone-number-check1",
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
            url: SITE_URL+"/email_check",
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


@endsection
