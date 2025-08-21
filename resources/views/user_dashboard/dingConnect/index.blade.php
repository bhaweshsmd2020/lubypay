<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'></link>  
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
    
    .sizebutton {
      float: left;
      margin: 5px;
      width: auto;
      position: relative;
      margin-right: 10px;
      border: 1px solid #000;
      padding: 5px;
    }
    
    .sizebutton label,
    .sizebutton input {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }
    
    .sizebutton input[type="radio"] {
      opacity: 0.011;
      z-index: 100;
    }
    
    .sizebutton input[type="radio"]:checked + label {
      background: #20b8be33;
      height: 100%;
    }
    
    .sizebutton label {
      cursor: pointer;
      z-index: 90;
      line-height: 1.8em;
    }
</style>

@extends('user_dashboard.layouts.app')

@section('css')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{asset('public/user_dashboard/css/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('public/user_dashboard/css/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid py-5 step1 show" id="step1">
            <div class="col-md-6 px-0" >
                <div class="set-ph border p-3" style="border-radius:10px;">
                    <p class="mb-3">1. Enter phone number <i class="fas fa-info-circle"></i></p>
                   <div class="box box-info" style="height: auto !important;">
                        <form class="form-horizontal" id="searchoperator" method="POST">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                            <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                            <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                            <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
                                <div class="box-body">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <input type="tel" class="form-control" id="phone" name="phone" required>
                                            <span id="phone-error"></span>
                                            <span id="tel-error"></span>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="button" class="btn btn-primary pull-right btn-flat step1-next"  id="getoperator" ><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Next</span></button>
                                    </div>
                                </div>
                            </input>
                        </form>
                    </div>
                </div>
                <div class="border p-3" style="border-top:0;border-radius:10px;">
                    <p>2. Select Operator</p>
                </div>
                <div class="border p-3" style="border-top:0;border-radius:10px;">
                    <p>3. Select Recharge Plan </p>
                </div
            </div>
        </div>
        </div>
        <div class="container-fluid py-5 step2 " id="step2">
            <div class="col-md-6 border px-0" style="border-radius:10px;  background: #fff;">
                <div class="border-bottom">
                    <div class="ph-no p-3 justify-content-between align-items-center">
                        <p>1. Mobile Number </p>
                        <p id="mobile_number"></p>
                    </div>
                </div>
                
                <div class="operator border-bottom">
                    <div class="justify-content-between p-3">
                        <p>2.Select Operator</p>
                        <p class="ml-2">
                            <select class="form-control" class="operator" id="operator" onChange="getoperatorplan();" required>
                              <option>Select Operator</option>
                            </select>
                        </p>
                    </div>
                </div>
                
                <strong><p id="op_message" class="text-danger ml-2"></p></strong>
                
                <div class="operator border-bottom" id="rechargesection">
                    <div class="justify-content-between p-3">
                        <p>3.Select Recharge Plan</p>
                        <p class="ml-2">
                            <form class="form-horizontal" id="amountform" method="POST">
                                <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                                <input type="hidden" name="operator_name" id="operator_name" class="form-control">
                                <input type="hidden" name="mobilenumber" id="mobilenumber" class="form-control">
                                <input type="hidden" name="logo" id="logo" class="form-control">
                                <input type="hidden" name="sender_currency_code" id="sender_currency_code" class="form-control">
                                <input type="hidden" name="destination_currency_code" id="destination_currency_code" class="form-control">
                                <input type="hidden" name="operatorId" id="operatorId" class="form-control">
                                <input type="hidden" name="defaultcountry" id="defaultcountry" class="form-control">
                                <input type="hidden" name="carriercode" id="carriercode" class="form-control">
                                <input type="hidden" name="skucode" id="skucode" >
                                <input type="hidden" name="uatNum" id="uatNum" >
                                <input type="hidden" name="description" id="description" >
                                <input type="hidden" name="operatorName" id="operatorName" >
                                <input type="hidden" name="operatorLogo" id="operatorLogo" >
                                 <input type="hidden" name="recAmt" id="recAmt" >
                                 
                                 <input type="hidden" name="SendValue" id="SendValue" >
                                 <input type="hidden" name="SendCurrencyIso" id="SendCurrencyIso" >
                                
                                <select class="form-control" class="operator" id="operator_plan" name="recharge_amount" onChange="changeoperatorplan();" required>
                                    <option>Select Plan</option>
                                </select>
                            </form>
                        </p>
                    </div>
                    
                    <button type="button" class="btn btn-primary ml-3 step2-next mb-3" id="getvalue">Next</button>
                </div>
            </div>
        </div>
        <div class="container-fluid py-5 step3 " id="step3">
            <div class="col-md-6 border py-3" style="border-radius:10px; background: #fff;">
                <div class="text-center">
                    <h3 class="mb-4 font-weight-bold">Please confirm your request</h3>
                    <div class="content" style="min-height: auto;">
                        <img src="" id="logo_op" style="width: 50px; height: 50px; margin-bottom: 15px;">
                        <p><i class="fas fa-phone-alt"></i> <span id="phone_num"></span></p>
                        <p>Operator: <span id="operatorname"></span></p>
                        <p>Topup Amount: <span id="sendercurrencycode"></span> <span id="rec_amount"></span></p>
                        <p>Product Description: <span id="fx_rate"></span></p>
                    </div>
                    <div class="d-flex mt-5 justify-content-center">
                        <button type="button" class="btn btn-primary step3-cancel">Cancel</button>
                        <button type="button" class="btn btn-primary ml-3  step3-confirm" id="confirmvalue">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid py-5 step4 " id="step4">
            <div class="col-md-6 border p-3" style="border-radius:10px; background: #fff;">
                <h3 class="mb-4 font-weight-bold">Payment Details</h3>
                <div class="p-3 border-bottom">
                    <input type="hidden" value="0" id="rec_amount3">
                    <p>Recharge Amount : <span id="rec_curr2"></span> <span id="rec_amount2"></span></p>
                    <p>Fees : <span id="rec_curr3"></span> <span id="rec_fee2"></span></p>
                    <p>Total : <span id="rec_curr4"></span> <span id="rec_total2"></span></p>
                </div>
                <input type="hidden" name="fx_rate1" id="fx_rate1" class="form-control">
                <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                <input type="hidden" name="defaultcountry1" id="defaultcountry1" class="form-control">
                <input type="hidden" name="carriercode1" id="carriercode1" class="form-control">
                <div class="d-flex justify-content-between">
                    <div class="px-3 py-2">
                        <p>Payable Amount</p>
                        <p class="mt-2"><span id="rec_curr5"></span> <span class="text-success"><span id="rec_pay2"></span></p>
                    </div>
                    <select name="wallet" id="wallet" class="form-control" onChange="getwallet();" style="width: 200px;">
                        @foreach($wallets as $wallet)
                            @foreach($currencies as $currency)
                                @if($currency->id == $wallet->currency_id)
                                    <option value="{{$wallet->id}}" <?php if($currency->id == '7'){ echo 'selected'; }?>>{{$currency->code}}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <p class="text-danger px-3"><strong><span id="message"></span></strong></p>
                <span id="pay_button1">
                    <button type="button" class="btn btn-primary btn-block mt-2 step4-pay" id="topupsubmit">Pay <span id="rec_curr6"></span> <span id="rec_total3"></span></button>
                </span>
                <span id="pay_button2">
                    <button type="button" class="btn btn-primary btn-block mt-2" disabled>Pay <span id="rec_curr7"></span> <span id="rec_total4"></span></button>
                </span>
            </div>
        </div>
        <div class="container-fluid py-5 step5 " id="step5">
            <div class="col-md-6 border py-3" style="border-radius:10px; background: #fff;">
                <div class="transaction">
                    <h3 class="mb-3 font-weight-bold">Transaction details</h3>
                    <div class="content" style="min-height: auto;">
                        <p>Transaction: <span id="tr_id"></span></p>
                        <p>Date and time: <span id="tr_time"></span></p>
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
                            <p>Recharge Amount : <span id="tr_currency1"></span> <span id="tr_amount"></span></p>
                            <p>Fees : <span id="tr_currency2"></span> <span id="tr_fee"></span></p>
                            <p>Total : <span id="tr_currency3"></span> <span id="tr_total"></span></p>
                          </div>
                        </div>
                     </div>
                    <div class="d-flex mt-4">
                        <button type="button" class="btn btn-primary step5-new">New Topup</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</section>



@endsection

@section('js')
<script>
$('#operator_plan').change('on',function(){
    
    var skucode = $(this).find(':selected').attr('data-skucode');
    var description = $(this).find(':selected').attr('data-description');
    var SendCurrencyIso = $(this).find(':selected').attr('data-SendCurrencyIso');
    var SendValue = $(this).find(':selected').attr('data-SendValue');
    var uatNum = $(this).find(':selected').attr('data-uatNum');
    $('#uatNum').val(uatNum);
    $('#skucode').val(skucode);
    $('#description').val(description);
    $('#SendCurrencyIso').val(SendCurrencyIso);
    $('#SendValue').val(SendValue);
});

$('#operator').change('on',function(){
    
    var operatorName = $(this).find(':selected').attr('data-operator');
     var operatorLogo = $(this).find(':selected').attr('data-logo');
    $('#operatorName').val(operatorName);
    $('#operatorLogo').val(operatorLogo);
   
}) 


    $(document).ready(function(){
       
        $('.step1-next').attr('disabled',true);
        $('#phone').keyup(function(){
            if($(this).val().length !=0)
                $('.step1-next').attr('disabled', false);            
            else
                $('.step1-next').attr('disabled',true);
        })
    });
    
    $('#changeoperatorplan').on('change', function () {
       
        $('.step2-next').prop('disabled', !$(this).val());
    }).trigger('change');

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

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/user_dashboard/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
<!-- isValidPhoneNumber -->
<script src="{{ asset('public/user_dashboard/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$("#getoperator").click(function(){
    $('#rechargesection').hide();
    var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/ding-getOperator")}}',
		type: "POST",
        data:$('#searchoperator').serialize(),
		success: function (result) {
		    console.log(result);
		    if(result.code == 200){
		        jQuery("#operator").html(result.value);
    		    jQuery("#mobile_number").html(result.mobile_number);
    		    jQuery("#defaultCountry").val(result.defaultCountry);
    		    jQuery("#carrierCode").val(result.carrierCode);
    		     
		    }else{
		        swal("Oops!", result.msg, "error"); 
		        jQuery("#op_message").html(result.message);
		    }
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

function getoperatorplan() {
    var operator_id = jQuery('#operator').val();
    var mobile_num  = jQuery('#formattedPhone').val();
    var default_country  = jQuery('#defaultCountry').val();
    var carrier_code  = jQuery('#carrierCode').val();
  	jQuery.ajax({
		url: '{{ URL::to("/ding-getoperatorplan")}}',
		type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            "operator_id": operator_id,
            "mobile_num": mobile_num,
            "default_country": default_country,
            "carrier_code": carrier_code,
        },
		success: function (result) {
		    console.log(result);
		    if(result.code == 200){
		    jQuery("#operator_plan").html(result.plans);
		    jQuery("#uatNum").html(result.uatNum);
		    jQuery("#operator_name").val(result.name);
		    jQuery("#mobilenumber").val(result.mobile_number);
		    jQuery("#logo").val(result.logo);
		    jQuery("#sender_currency_code").val(result.sender_currency_code);
		    jQuery("#destination_currency_code").val(result.destination_currency_code);
		    jQuery("#operatorId").val(result.operatorId);
		    jQuery("#defaultcountry").val(result.default_country);
		    jQuery("#carriercode").val(result.carrier_code);
		    if(result.carrier_code != ''){
		        $('#rechargesection').show();
		    }else{
		        $('#rechargesection').hide();
		    }
		    }else{
		        swal("Oops!", result.msg, "error"); 
		        
		    }
		},
	});
}

$("#getvalue").click(function(){
  	jQuery.ajax({
		url: '{{ URL::to("/ding-getvalue")}}',
		type: "POST",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result.value.name);
		    jQuery("#phone_num").html(result.phone_num);
		    jQuery("#rec_amount").html(result.rec_amount);
		    jQuery("#recAmt").html(result.rec_amount);
		    jQuery("#logo_op").attr("src", result.logo);
            jQuery("#sendercurrencycode").html(result.sender_currency_code);
            jQuery("#destinationcurrencycode").html(result.destination_currency_code);
            jQuery("#operatorname").html(result.operator_name);
            jQuery("#fx_rate").html(result.fxRate);
            jQuery("#fx_rate1").val(result.fxRate);
            jQuery("#defaultcountry1").val(result.defaultcountry);
            jQuery("#carriercode1").val(result.carriercode);
		},
	});
})

$("#confirmvalue").click(function(){
  	jQuery.ajax({
		url: '{{ URL::to("/ding-confirmvalue")}}',
		type: "POST",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result);
		    jQuery("#rec_amount2").html(result.amount);
		    jQuery("#rec_amount3").val(result.amount);
		    jQuery("#rec_fee2").html(result.fee);
		    jQuery("#rec_total2").html(result.total);
		    jQuery("#rec_pay2").html(result.total);
		    jQuery("#rec_curr2").html(result.currency);
		    jQuery("#rec_curr3").html(result.currency);
		    jQuery("#rec_curr4").html(result.currency);
		    jQuery("#rec_curr5").html(result.currency);
		    jQuery("#rec_curr6").html(result.currency);
		    jQuery("#rec_curr7").html(result.currency);
		    jQuery("#rec_total3").html(result.total);
		    jQuery("#rec_total4").html(result.total);
		    jQuery("#message").html(result.message);
		    if(result.message == ''){
		        $('#pay_button1').show();
		        $('#pay_button2').hide();
		    }else{
		        $('#pay_button1').hide();
		        $('#pay_button2').show();
		    }
		},
	});
})

function getwallet() {
    var wallet = jQuery('#wallet').val();
    var amount = jQuery('#rec_amount3').val();
  	jQuery.ajax({
		url: '{{ URL::to("/getwallet")}}',
		type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            "wallet": wallet,
            "amount": amount,
        },
		success: function (result) {
		    console.log(result);
            jQuery("#rec_amount2").html(result.amount);
		    jQuery("#rec_fee2").html(result.fee);
		    jQuery("#rec_total2").html(result.total);
		    jQuery("#rec_pay2").html(result.total);
		    jQuery("#rec_curr2").html(result.currency);
		    jQuery("#rec_curr3").html(result.currency);
		    jQuery("#rec_curr4").html(result.currency);
		    jQuery("#rec_curr5").html(result.currency);
		    jQuery("#rec_curr6").html(result.currency);
		    jQuery("#rec_curr7").html(result.currency);
		    jQuery("#rec_total3").html(result.total);
		    jQuery("#rec_total4").html(result.total);
		    jQuery("#message").html(result.message);
		    if(result.message == ''){
		        $('#pay_button1').show();
		        $('#pay_button2').hide();
		    }else{
		        $('#pay_button1').hide();
		        $('#pay_button2').show();
		    }
		},
	});
}

$("#topupsubmit").click(function(){
    var wallet = jQuery('#wallet').val();
    var amount = jQuery('#rec_amount3').val();
    var operator_id = jQuery('#operatorId').val();
    var mobile = jQuery('#mobilenumber').val();
    var operator_amt = jQuery('#fx_rate1').val();
    var defaultcountry = jQuery('#defaultcountry1').val();
    var carriercode = jQuery('#carriercode1').val();
    var operatorId = jQuery('#operatorId').val();
    var skucode = jQuery('#skucode').val();
    var recAmt = jQuery('#recAmt').val();
    var SendValue = jQuery('#SendValue').val();
    var uatNum = jQuery('#uatNum').val();
    var SendCurrencyIso = jQuery('#SendCurrencyIso').val();
                       
  	jQuery.ajax({
		url: '{{ URL::to("/ding-makerecharge")}}',
		type: "POST",
		dataType:"JSON",
        data: {
            "_token": "{{ csrf_token() }}",
            "wallet": wallet,
            "amount": amount,
            "operator_id": operator_id,
            "mobile": mobile,
            "operator_amt": operator_amt,
            "defaultcountry": defaultcountry,
            "carriercode": carriercode,
             "operatorId": operatorId,
            "skucode": skucode,
            "recAmt":recAmt,
             "SendCurrencyIso": SendCurrencyIso,
            "SendValue":SendValue,
            "uatNum":uatNum
        },
		success: function (result) {
		    console.log(result);
		    if(result.code == "200") {
    		    jQuery("#tr_id").html(result.tr_id);
    		    jQuery("#tr_time").html(result.tr_time);
    		    jQuery("#tr_fee").html(result.tr_fee);
    		    jQuery("#tr_amount").html(result.tr_amount);
    		    jQuery("#tr_total").html(result.tr_total);
    		    jQuery("#tr_currency1").html(result.tr_currency);
    		    jQuery("#tr_currency2").html(result.tr_currency);
    		    jQuery("#tr_currency3").html(result.tr_currency);
    		      swal("Oops!", 'Your Recharge has been successfully...', "success");
		    } else {
		         swal("Oops!", result.msg, "error");
		          setTimeout(function() {
                     window.location.reload();
                  }, 2000);
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
