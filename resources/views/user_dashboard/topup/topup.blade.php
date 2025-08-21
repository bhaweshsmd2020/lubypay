@extends('user_dashboard.layouts.app')
@section('title','Create Topup')
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
/*custom font*/
@import url(https://fonts.googleapis.com/css?family=Montserrat);

/*basic reset*/
* {margin: 0; padding: 0;}

html {
	/*height: 100%;*/
	/*Image only BG fallback*/
	
	/*background = gradient + image pattern combo*/
	background: 
		linear-gradient(rgba(196, 102, 0, 0.6), rgba(155, 89, 182, 0.6));
}
body {
	font-family: montserrat, arial, verdana;
}
.overlay{
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(255,255,255,0.8) url("https://ewallet.xpay.mv/public/uploads/banner/Ajax_loader.gif") center no-repeat;
        border-radius: 15px;
    }
   
    /* Turn off scrollbar when body element has the loading class */
    body.loading{
        overflow: hidden;   
    }
    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay{
        display: block;
    }

/*form styles*/
#msform {
	width: auto;
	margin:  auto;
	text-align: center;
	position: relative;
}
#msform fieldset {
	background: white;
	border: 0 none;
	border-radius: 3px;
	box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
	padding: 20px 30px;
	box-sizing: border-box;
	width: 80%;
	margin: 0 10%;
	
	/*stacking fieldsets above each other*/
	position: relative;
}
/*Hide all except first fieldset*/
#msform fieldset:not(:first-of-type) {
	display: none;
}
/*inputs*/
#msform input, #msform textarea {

	border: 1px solid #ccc;
	border-radius: 3px;
	margin-bottom: 10px;
	width: 100%;
	box-sizing: border-box;
	font-family: montserrat;
	color: #2C3E50;

}
/*buttons*/
#msform .action-button {
	width: 100px;
	background: #27AE60;
	font-weight: bold;
	color: white;
	border: 0 none;
	border-radius: 1px;
	cursor: pointer;
	padding: 10px 5px;
	margin: 10px 5px;
}
#msform .action-button:hover, #msform .action-button:focus {
	box-shadow: 0 0 0 2px white, 0 0 0 3px #27AE60;
}
/*headings*/
.fs-title {
	font-size: 15px;
	text-transform: uppercase;
	color: #2C3E50;
	margin-bottom: 10px;
}
.fs-subtitle {
	font-weight: normal;
	font-size: 13px;
	color: #666;
	margin-bottom: 20px;
}
/*progressbar*/
#progressbar {
	margin-bottom: 30px;
	overflow: hidden;
	/*CSS counters to number the steps*/
	counter-reset: step;
}
#progressbar li {
	list-style-type: none;
	color: black;
	text-transform: uppercase;
	font-size: 9px;
	width: 33.33%;
	float: left;
	position: relative;
}
#progressbar li:before {
	content: counter(step);
	counter-increment: step;
	width: 20px;
	line-height: 20px;
	display: block;
	font-size: 10px;
	color: #333;
	background: white;
	border-radius: 3px;
	margin: 0 auto 5px auto;
}
/*progressbar connectors*/
#progressbar li:after {
	content: '';
	width: 100%;
	height: 2px;
	background: black;
	position: absolute;
	left: -50%;
	top: 9px;
	z-index: -1; /*put it behind the numbers*/
}
#progressbar li:first-child:after {
	/*connector not needed before the first step*/
	content: none; 
}
/*marking active/completed steps green*/
/*The number of the step and the connector before it = green*/
#progressbar li.active:before,  #progressbar li.active:after{
	background: #27AE60;
	color: white;
}

.operator{
        max-width: 75px;
    margin: 0 2px 15px;
}
.amtbtn{
        background: #1176db;
    padding: 4px;
    border-radius: 10px;
    color: #fff;
}

</style>
@section('content')
<!--<div class="row">-->
        <!--<div class="col-md-12">-->
        <!--    <div class="box box-info">-->
        <!--            <div class="box-header with-border text-center">-->
        <!--              <h3 class="box-title">Set phone number</h3>-->
        <!--            </div>-->
                    <!-- multistep form -->
<form id="msform">
  <!-- progressbar -->
  <ul id="progressbar">
    <li class="active">Check Mobile Number</li>
    <li>Confirm Request</li>
    <li>Success/Failure</li>
  </ul>
  <!-- fieldsets -->
  <fieldset>
    <h2 class="fs-title">Create Your Topup Recharge</h2>
    <!--<h3 class="fs-subtitle">This is step 1</h3>-->
    <div class="row">
    <div class="col-md-6">
        <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
        <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
        <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
        <input type="hidden" name="senderCurrencySymbol" id="senderCurrencySymbol" class="form-control">
        <input type="tel" class="form-control" id="phone" name="phone" required>
        <span id="phone-error"></span>
        <span id="tel-error"></span>
    </div>
     <div class="col-md-6" id="operator">
       <div class="list-group">
          
          <a href="#" class="list-group-item active" id="opt-name"><strong> </strong></a> 
          <a href="#" class="list-group-item active" id="mobilenumber"><strong> </strong></a>
          <a href="#" class="list-group-item" id="logo"><img  class="operator"alt="operator" id="my_image"/></a>
         
          <span><strong>Select Amount :</strong> <select name="fxdamt" id="suggested_amount" class="form-control" >
													</select></span>
        </div>
    </div>
    </div>
    <input type="button" class="action-button first" value="Next" id="firstnext"/>
    <input type="button" name="next" class="next action-button fortopup" value="Next"/>
  </fieldset>
  <fieldset>
    <h2 class="fs-title">Please confirm your request</h2>
    <input type="hidden" id="operatorId"  />
    <input type="hidden" id="countryCode"  />
    <input type="hidden" id="number"  />
    <input type="hidden" id="amount"  />
    <div class="col-md-3">
        </div>
     <div class="col-md-6" id="operator" style="margin-left:237px;">

       <div class="list-group">
          
          <a  href="#" class="list-group-item active"  id="opt-name1"><strong>  </strong></a> 
          <a href="#" class="list-group-item active" id="mobilenumber1"><strong>  </strong></a>
          <a href="#" class="list-group-item" id="logo1"><img  class=" operator my_image1"alt="my_image1" /></a>
          <span><strong>Selected Amount :</strong><a  class="list-group-item" id="suggested_amount1"> </a></span>
        </div>
    </div>
    <div class="col-md-3">
        </div>
    <input type="button" name="previous" class="previous action-button" value="Previous" />
    <input type="button"  class="action-button afterconfirm" value="Confirm" />
    <input type="button" name="next" class="next action-button " value="Next" id="beforeconfirm"/>
  </fieldset>
  
</form>
<div class="overlay"></div>
                  
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
@endsection

@section('js')

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>

<!-- isValidPhoneNumber -->
<script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" type="text/javascript"></script>


<!-- read-file-on-change -->
@include('common.read-file-on-change')


<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
    //alert('Hello');
    $('#operator').hide();
    $('#beforeconfirm').hide();
    $('.fortopup').hide();
    
    $('#firstnext').on('click',function (){
         var phone = $('#phone').val();
         var formattedPhone = $('#carrierCode').val();
         var defaultCountry = $('#defaultCountry').val();
            if(phone == '')
            {
                 alert('Please Enter Mobile Number!');
            }else
            {
                var fullnumber = formattedPhone+phone;
                //alert(fullnumber);
                
                $.ajax({
                   type:"GET",
                   url:"{{url('get-operator-details')}}?fullnumber="+fullnumber+"&defaultCountry="+defaultCountry,
                   beforeSend: function() {
                        $("body").addClass("loading"); 
                    }, 
                   success:function(res){               
                    if(res){
                        $("body").removeClass("loading");
                        var obj = JSON.parse(res);
                        //alert(obj.operatorId);
                        var fixedAmounts = obj.fixedAmounts;
                        $.each(fixedAmounts, function(index, value){
                            //$("#suggested_amount").append("<b class='amtbtn'  id='attrId' data-id='"+value+"'>$ " + value + '</b>&nbsp;');
                            $("#suggested_amount").append('<option value="'+value+'"> '+obj.senderCurrencySymbol+value+'</option>');
                       });
                        $('#operator').show();
                        $("#mobilenumber").text("Mobile Number : +"+fullnumber);
                        $("#opt-name").text("Operator Name : "+obj.name);
                        $('#my_image').attr('src',obj.logoUrls[1]);
                        //For Confirm Input fields
                         $('#operatorId').val(obj.operatorId);
                         $('#countryCode').val(obj.country.isoName);
                         $('#number').val(phone);
                         $('#amount').val(fixedAmounts[0]);
                        //For Confirm Screen   
                         $("#mobilenumber1").text("Mobile Number : +"+fullnumber);
                         $("#senderCurrencySymbol").val(obj.senderCurrencySymbol);
                         $("#opt-name1").text("Operator Name : "+obj.name);
                         $('.my_image1').attr('src',obj.logoUrls[1]);
                         $("#suggested_amount1").text("Amount : "+obj.senderCurrencySymbol+fixedAmounts[0]);
                        $('.first').hide();
                        $('.fortopup').show();
                    }else{
                        alert('Not send!');
                    }
                   }
                });
    }
});

$('#suggested_amount').change(function(){
       var suggested_amount = $(this).val();  
       var senderCurrencySymbolnew = $('#senderCurrencySymbol').val();
        $("#suggested_amount1").text("Amount : "+senderCurrencySymbolnew+suggested_amount);
        $('#amount').val(suggested_amount);
      // alert(suggested_amount);	
    });

 $('.afterconfirm').on('click',function (){
     //alert('cadfas');
      var operatorId  = $('#operatorId').val();
      var countryCode = $('#countryCode').val();
      var number      = $('#number').val();
      var amount      = $('#amount').val();
      $.ajax({
             url: "{{url('post-topup-data')}}",
             data: {'operatorId': operatorId, 'countryCode':countryCode,'number':number,'amount':amount,"_token":$('meta[name=csrf-token]').attr('content')},
             type: 'POST',
             beforeSend: function() {
                        $("body").addClass("loading"); 
                    }, 
             success: function (response) {
                 $("body").removeClass("loading");
                 var topup_res = JSON.parse(response);
                 if(topup_res.errorCode !='' )
                 {
                    swal({
                      title: topup_res.errorCode,
                      text: topup_res.message,
                      icon: "error",
                      button: "Error!",
                    });
                 }if(topup_res.transactionId !='' )
                 {
                     swal({
                      title: 'Topup done Successfully!',
                      text:  'Your topup of Amount :'+topup_res.requestedAmount +'and transaction ID : '+topup_res.transactionId+' successfully done',
                      icon:  "success",
                      button: "Ok!",
                    });
                    setTimeout(function(){
                       window.location.reload(1);
                    }, 5000);
                 }
             }
             
         });
      //alert(amount);
 });

});
</script>
<!-- isValidPhoneNumber -->
<script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$("#getoperator").click(function(){
  var phone = jQuery('#phone').val();
  	jQuery.ajax({
		url: '{{ URL::to("/getOperatornew")}}',
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
		url: '{{ URL::to("/getvaluenew")}}',
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
		url: '{{ URL::to("/makerechargenew")}}',
		type: "POST",
        data:$('#amountform').serialize(),
		success: function (result) {
		    console.log(result);
		    jQuery("#operator").html(result);

		},
	});
});

</script>
<script>
// flag for button disable/enable
var hasPhoneError = false;
var hasEmailError = false;

/**
* [check submit button should be disabled or not]
* @return {void}
*/
// function enableDisableButton()
// {
//     if (!hasPhoneError && !hasEmailError) {
//         $('#firstnext').prop('disabled',false);
//     } else {
//       $('#firstnext').prop('disabled',true);
//     }
// }

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
</script>
<script>
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

    
</script>
<script>

//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

$(".next").click(function(){
    
    
           	if(animating) return false;
        	animating = true;
        	
        	current_fs = $(this).parent();
        	next_fs = $(this).parent().next();
        	
        	//activate next step on progressbar using the index of next_fs
        	$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
        	
        	//show the next fieldset
        	next_fs.show(); 
        	//hide the current fieldset with style
        	current_fs.animate({opacity: 0}, {
        		step: function(now, mx) {
        			//as the opacity of current_fs reduces to 0 - stored in "now"
        			//1. scale current_fs down to 80%
        			scale = 1 - (1 - now) * 0.2;
        			//2. bring next_fs from the right(50%)
        			left = (now * 50)+"%";
        			//3. increase opacity of next_fs to 1 as it moves in
        			opacity = 1 - now;
        			current_fs.css({
                'transform': 'scale('+scale+')',
                'position': 'absolute'
              });
        			next_fs.css({'left': left, 'opacity': opacity});
        		}, 
        		duration: 800, 
        		complete: function(){
        			current_fs.hide();
        			animating = false;
        		}, 
        		//this comes from the custom easing plugin
        		easing: 'easeInOutBack'
        	});
        

});

$(".previous").click(function(){
	if(animating) return false;
	animating = true;
	
	current_fs = $(this).parent();
	previous_fs = $(this).parent().prev();
	
	//de-activate current step on progressbar
	$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
	
	//show the previous fieldset
	previous_fs.show(); 
	//hide the current fieldset with style
	current_fs.animate({opacity: 0}, {
		step: function(now, mx) {
			//as the opacity of current_fs reduces to 0 - stored in "now"
			//1. scale previous_fs from 80% to 100%
			scale = 0.8 + (1 - now) * 0.2;
			//2. take current_fs to the right(50%) - from 0%
			left = ((1-now) * 50)+"%";
			//3. increase opacity of previous_fs to 1 as it moves in
			opacity = 1 - now;
			current_fs.css({'left': left});
			previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
		}, 
		duration: 800, 
		complete: function(){
			current_fs.hide();
			animating = false;
		}, 
		//this comes from the custom easing plugin
		easing: 'easeInOutBack'
	});
});

$(".submit").click(function(){
	return false;
})

</script>
@endsection