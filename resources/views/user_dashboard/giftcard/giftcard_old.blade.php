@extends('user_dashboard.layouts.app')
@section('content')
<style>
  .add-money-card-new{
    height: 50%;
    overflow-y: scroll;
  }
  #overlay {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  z-index: 2;
  cursor: pointer;
}
#text{
  position: absolute;
  top: 50%;
  left: 50%;
  font-size: 30px;
  color: white;
  transform: translate(-50%,-50%);
  -ms-transform: translate(-50%,-50%);
}
.modal-footer{
	padding: 0px;
	justify-content: center;
}
</style>
<div id="overlay">
  <div id="text">Please wait! <p>We are fetching information from server...</p></div>
</div>
<input type="hidden" class="previous_operator">
<form action="" method="POST" id="form">
  @csrf
  <div class="row justify-content-center gy-4 firstDiv">
    <div class="col-lg-6 ">
      <div class="add-money-card">
        <h4 class="title"><i class="las la-mobile"></i> @lang('Gift Card')</h4>
          
          <div class="form-group">
            <label>@lang('Select Your Country')</label>
            <input type="hidden" name="country_code" >
            <input type="hidden" name="currency_id" >
            <input type="hidden" name="topup_number" >
            <input type="hidden" name="topup_amount" >
            <input type="hidden" name="amountWithFees" >
            <input type="hidden" name="operator_id" >
            <input type="hidden" name="operator_name" >
            <input type="hidden" name="wallet_id" >
             <select name="country" id="country" class="form--control country">
                  @foreach($countries as $key => $country)
                      <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                  @endforeach
              </select>
          </div>

         
         
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="add-money-card style--two" >
        <h4 class="title"><i class="lar la-file-alt"></i> @lang('Summery')</h4>
       
        <div class="add-moeny-card-middle">

          <ul class="add-money-details-list">
            <li>
              <span class="caption">@lang('Country Code')</span>
              <div class="value"><span class="selectCountry">NA</span></div>
            </li>

          </ul>

        </div>
         <button type="button" class="btn btn-md btn--base w-100 mt-3 firstStep">@lang('Next')</button>
      </div>
    </div>
  </div>
  <div class="row justify-content-center gy-4 secondDiv">

  <div class="col-lg-6 ">
      <div class="add-money-card" >
        <h4 class="title"><i class="las la-mobile"></i> @lang('Your Service Provider Info')</h4>
          <div class="form-group">
            <label>@lang('Provider Name')</label>
            <input type="hidden" name="country_code" >
            <input type="hidden" name="currency_id" >
            <div class="row" id="providerLogo"></div>
            </div>

         
      </div>
    </div>
<div class="col-lg-4">
      <div class="add-money-card style--two" style="display: block;">
        <h4 class="title"><i class="lar la-file-alt"></i> @lang('Summary')</h4>
        <div class="row showAmountRow">
          <label>@lang('Suggested Amount')</label>
        </div>
        <br>
        <div class="add-moeny-card-middle">
          <ul class="add-money-details-list">
          
            <li>
              <span class="caption">@lang('Gift Card')</span>
              <div class="value"><span class="providerCurrency">NA</span> </div>
            </li>
            <li>
              <span class="caption">@lang('Amount')</span>
               <div class="value"><span class="providerSymbol"></span>USD<span class="providerpayable">0.00</span> </div>
            </li>
            <li>
              <span class="caption">@lang('Unit Price')</span>
               <div class="value"><span class="providerSymbol"></span>£<span class="unitPrice">0.00</span> </div>
            </li>
             <li> 
              <span class="caption">@lang('Total Fee')</span>
               <div class="value"><span class="sendersymbol"></span>£<span class="providerfee">0.00</span> </div>
            </li>
          </ul>
          <div class="add-money-details-bottom">
            <span class="caption">@lang('Total Payable')</span>
            <div class="value"><span class="providerSymbol"></span>£<span class="totalpayable">0.00</span> </div>
          </div>
        </div>
         <button type="button" class="btn btn-md btn--base w-100 mt-3 sender_confirm">@lang('Next')</button>
      </div>
    </div>
  </div>
</form>
<div  id="confirm" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('Add Recipient and Sender information')</h4>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="" name="product_id">
     <div class="firstSection">
       <div class="row">
       	<div class="col-md-6">
       		<label>Sender Name</label>
       		 <input type="text" class="form--control sender_name" placeholder="enter sender name" name="sender_name">
       	</div>
       	<div class="col-md-6">
       		<label>Email</label>
       		 <input type="email" class="form--control sender_email" placeholder="enter sender email"name="sender_email">
       	</div>
       	 <div class="col-md-12">
       		<label>Mobile Number</label>
       		 <input type="number" class="form--control" placeholder="enter sender mobile"name="sender_mobile">
       	</div>
       	 <div class="row">
       		<div class="col-md-6">
       			<input type="checkbox" class="emailalert" name="mail_gift">
       		 	<label>Send the Gift Card Via Email</label>
	       	</div>
	       	<div class="col-md-6">
	       		 <input type="checkbox" class="mobilealert" name="sms_gift">
	       		 <label>Send the Gift Card Via SMS*</label>
	       	</div>
       	</div>
       </div>
   </div>
   <div class="secondSection">
       <div class="row">
       	<div class="col-md-6">
       		<p><b>Recipient:</b> </br><span class="confSendEmail">Free Fire<span>Info@gfeenhonchos.com</p>
       	</div>
       	<div class="col-md-6">
       		<p><b>Sender:</b> </br><span class="confSendName">Free Fire<span>Shubham Kumar</p>
       	</div>
       	 <div class="col-md-6">
       		<p><b>Gift Card:</b> </br><span class="giftcardname">Free Fire<span></p>
       	</div>
       	<div class="col-md-6">
       		<p><b>Total Amount:</b> </br><span class="giftamount">Free Fire<span>23</p>
       	</div>
       </div>
   </div>
 	  <div class="modal-footer">
 	  	<div class="row">
       	 <div class="col-md-6">
	     <button type="button" class="btn btn-md btn--base w-100 mt-3" >Close</button>
		 </div>
	     <div class="col-md-6 firstNext">
       	 <button type="button" class="btn btn-md btn--base w-100 mt-3 firstnext" >Next</button>
		 </div>
		 <div class="col-md-6">
       	 <button type="button" class="btn btn-md btn--base w-100 mt-3 paybutton " >Pay</button>
		 </div>
       	</div>
      </div>
       </div>
      </div>
     
    </div>

  </div>
</div>


@endsection

@push('script')
   <script>
    $(document).ready(function(){
     $('.req_confirm').hide();
     $('.secondSection').hide();
     $('.secondDiv').hide();
     $('.paybutton').hide();
     $(".firstnext").attr("disabled", true);
     $(".emailalert").click(function () {
		
	 if($('input:checkbox[name=mail_gift]').is(':checked') == true)
	   {
		$(".firstnext").attr("disabled", false);
	   }
	   if($('input:checkbox[name=mail_gift]').is(':checked') == false)
	   {
		$(".firstnext").attr("disabled", true);
	   }
			
     });
     $(".firstnext").click(function () {
	  var sendername = $('.sender_name').val();
	  var senderemail = $('.sender_email').val();
	  var emailalert = $('input:checkbox[name=mail_gift]').is(':checked');
	  var smsalert = $('input:checkbox[name=sms_gift]').is(':checked');
	  if((sendername == '') || (senderemail == '') || (emailalert == '') )
	  {
	  	 notify('error','Validation error!');
         return false
	  }
	      $('.firstNext').css('display','none');
	      $('.paybutton').show();
	       $('.firstSection').hide();
	       $('.secondSection').show();
		  $('.confSendEmail').text(senderemail);
          $('.confSendName').text(sendername);
      });

     var SITEURL = '{{URL::to('')}}';
     $(".firstStep").click(function(e){
      e.preventDefault();
   
        var code  =  $('input[name=country_code]').val();
        //var mobile_number =  $('input[name=mobile]').val();
        var token = "{{ csrf_token() }}";

        $.ajax({
           type:'POST',
           url:"{{ route('/request/giftcard') }}",

           data:{code:code,_token:token },
           beforeSend: function(){
            // Show image container
            on();
           },
           success:function(data){
            const decode = JSON.parse(data);
            console.log(decode);
            off();
              if(decode.success == true)
              {
                 
                const resultDecode = JSON.parse(decode.data);
                console.log('reslut ',resultDecode);
                var region = [];
                $.each(resultDecode, function(key1,val) {

                var counter = 0;
                 $(".add-money-card").addClass("add-money-card-new");
                 $("<div class='col-md-4'><img src='"+val.logoUrls[0]+"'><p style='cursor:pointer;' onclick='showRechargeAmt("+val.productId+")'>"+val.productName+"</p></div>&nbsp;").appendTo('#providerLogo');
                 $.each(val.fixedRecipientToSenderDenominationsMap, function(amt,desc){
                  
                   $('.showAmountRow').append("<div style='display:none;' class='col-lg-3 hideamount_"+val.productId+"'><input type='button' class='btn btn--primary btn--xs selectedRecharge_"+val.productId+"' data-operatoe='"+val.productName+"' id='selectedRecharge' onclick='myFunction("+amt+","+desc+","+val.senderFee+","+val.productId+")' data-amt ='"+desc+"' data-fixed='"+desc+"' style='padding: 3px!important;line-break: auto;'title='"+desc+"' value='USD "+amt+"'></div>&nbsp;&nbsp;");
                    counter++;
                      });
                  });
                $('.firstDiv').hide();
                $('.secondDiv').show();
              }else {
               
               notify('error',decode.message);
                  return false
              }
              
           }
        });
  
    });
    });
    </script>
    <script>
      function myFunction(key,second,third,fourth) {
        if(key == '')
        {
          alert('Please select any amount for topup!');
        }else
        {
          var selectRecharge = key;
          $('.providerpayable').text(selectRecharge);
          $('.providerfee').text(third);
          $('.unitPrice').text(second);
          $('.totalpayable').text((second+third).toFixed(2));
          $('.giftamount').text((second+third).toFixed(2));
          $('.req_confirm').show();
          $('input[name=topup_amount]').val(selectRecharge);
          $('input[name=amountWithFees]').val((second+third).toFixed(2));  
          var Oname = $('.selectedRecharge_'+fourth).data('operatoe');
          $('input[name=operator_name]').val(Oname);
          $('.providerCurrency').text(Oname);
          $('.giftcardname').text(Oname);
          console.log(Oname);
        }
      }

      function showRechargeAmt(val)
      {
        if(val == '')
        {
          alert('Please select any amount for topup!');
        }else
        {
          $('input[name=operator_id]').val(val);
          var preAmt = $('.previous_operator').val();
          if(preAmt != '')
          {
            $('.hideamount_'+preAmt).css('display','none');
            $('.previous_operator').val(val);
            $('.hideamount_'+val).css('display','block');
          }else
          {
             $('.previous_operator').val(val);
             $('.hideamount_'+val).css('display','block');
          }
         
        }
      }
          
    </script>
     <script>
            'use strict';
            (function ($) {
                $('#country').on('change',function () {
                    var gateways = $('#country option:selected').data('mobile_code')
                    var sym = $('#country option:selected').data('sym')
                    var code = $('#country option:selected').data('code')
                    $('.curr_code').text(code)
                    $('.selectCountry').text(gateways)

                    $('input[name=country_code]').val(code)
                    $('.gateway').removeAttr('disabled')
                    $('.gateway').children().remove()
                    var html = `<option value="">@lang('Select Gateway')</option>`;

                    if(gateways.length > 0){
                    $.each(gateways, function (i, val) {
                      html += ` <option data-max="${val.max_amount}" data-min="${val.min_amount}" data-fixcharge = "${val.fixed_charge}" data-percent="${val.percent_charge}"  value="${val.method_code}">${val.name}</option>`
                    });
                    $('.gateway').append(html)
                    $('.gateway-msg').text('')

                  } else{
                    $('.gateway').attr('disabled',true)
                    $('.gateway').append(html)
                    $('.gateway-msg').text('No gateway found with this currency.')
                  }

                })

                $('.mobileNumber').on('keyup',function () {
                    var mobileNumber = parseFloat($(this).val())
                     if(!isNaN(mobileNumber)){
                      $('.charge').text(mobileNumber)
                      $('input[name=topup_number]').val(mobileNumber)
                    } else {
                      $('.charge').text('NA')
                    
                    }
                })
                $('#wallet').on('change',function () {
                    console.log('ok');
                   if($('#wallet option:selected').val() == ''){
                      $('.amount').attr('disabled',true)
                      $('.charge').text('0.00')
                      $('.payable').text(parseFloat($('.amount').val()))
                      $('.limit').text('limit : 0.00 USD')
                      return false
                    }
                    $('input[name=wallet_id]').val($('#wallet option:selected').data('currency'))

                })
                 $('.sender_confirm').on('click',function () {
 						$('#confirm').modal('show')
                 })
                $('.paybutton').on('click',function () {
                  var country_code  = $('input[name=country_code]').val();
                  var currency_id   = $('input[name=currency_id]').val();
                  var topup_amount  = $('input[name=topup_amount]').val();
                  var amountWithFees  = $('input[name=amountWithFees]').val();                  
                  var operator_id   = $('input[name=operator_id]').val();
                  var operator_name = $('input[name=operator_name]').val();
                  var wallet_id     = $('input[name=wallet_id]').val();
                  var sendername    = $('.sender_name').val();
	              var senderemail   = $('.sender_email').val();
                  var mobile        = $('.mobile').val();
                  var token         = "{{ csrf_token() }}";
                if(country_code == '' || topup_amount == ''|| senderemail == ''|| sendername == '' || operator_id == ''|| operator_name == ''){
                  notify('error','All fields are required');
                  return false
                }
                
           $.ajax({
           type:'POST',
           url:"{{ route('user.submit.giftcard') }}",

           data:{amountWithFees:amountWithFees,mobile:mobile,senderemail:senderemail,sendername:sendername,operator_id:operator_id, wallet_id:wallet_id,operator_name:operator_name,topup_amount:topup_amount,country_code:country_code,_token:token },
           beforeSend: function(){
            on();
           },
           success:function(data){
             const resultDecode = JSON.parse(data);
             off();
             $('#confirm').modal('hide');
            if(resultDecode.success == 'true')
            {
               console.log('wallet ',resultDecode);
               notify('success',resultDecode.message);
             }else
            {
                 notify('error',resultDecode.message);
                 return false
            }
            
          },
          complete: function(){
            setTimeout(function() {
                document.location.reload(true);
            }, 2000);
                
            },
          
        });
              });
            })(jQuery);
     </script>
     <script>
function on() {
  document.getElementById("overlay").style.display = "block";
}

function off() {
  document.getElementById("overlay").style.display = "none";
}
</script>
@endpush
