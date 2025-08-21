@extends('admin.layouts.master')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

@section('title', 'Pay Water Bill')

@section('page_content') 
<style>
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

</style>
  
  <div class="col-md-12" id="panel">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Find Bill</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/utility/pay-cable-bill') }}" class="form-horizontal" id="find_cable">
          {{ csrf_field() }}

          <div class="box-body">
              <div class="form-group">
              <label class="col-sm-3 control-label" for="account_id">Account ID</label>
              <div class="col-sm-6">
                <input type="text" name="findaccount_id" class="form-control"  placeholder="Enter your Account ID" id="findaccount_id" required>
                @if($errors->has('account_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('account_id') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="amount">Meter No</label>
              <div class="col-sm-6">
                <input type="text" name="find_meterno" class="form-control"  placeholder="Enter Meter No..." id="find_meterno" required>
                @if($errors->has('meter'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('meter') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/utility/all-water-bill') }}">Cancel</a>
            <a  id="find_bill"class="btn btn-primary pull-right">&nbsp; Find Bill &nbsp;</a>
          </div>
        </form>
      </div>
     
    </div>
                           
  
    <div class="col-md-12" id="processed">
      <!-- Horizontal Form -->
      
        <div class="col-md-8">
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Pay Water Bill</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/utility/pay-cable-bill') }}" class="form-horizontal" id="pay_cable_form">
          {{ csrf_field() }}

          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="user_id">Select User</label>
              <div class="col-sm-6">
               <select name="user_id" class="form-control select2" placeholder="Select User" id="user_id" required>
                    <option value="">Select User</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endforeach
                </select>
                @if($errors->has('user_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('user_id') }}</strong>
                </span>
                @endif
              </div>
            </div>  
             
             <div class="form-group">
              <label class="col-sm-3 control-label" for="account_id">Phone No</label>
              <div class="col-sm-6">
                <input type="text" name="phone" class="form-control"  placeholder="Enter Phone No" id="phone">
                @if($errors->has('phone'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
             <div class="form-group">
              <label class="col-sm-3 control-label" for="account_id">Account ID</label>
              <div class="col-sm-6">
                <input type="text" name="account_id" class="form-control"  placeholder="Enter your Account ID" id="account_id" readonly>
                @if($errors->has('account_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('account_id') }}</strong>
                </span>
                @endif
              </div>
            </div>


            <div class="form-group">
              <label class="col-sm-3 control-label" for="amount">Amount</label>
              <div class="col-sm-6">
                <input type="number" name="amount" class="form-control"  placeholder="Enter Amount" id="amount">
                @if($errors->has('amount'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('amount') }}</strong>
                </span>
                @endif
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label" for="amount">Meter No</label>
              <div class="col-sm-6">
                <input type="text" name="meterno" class="form-control"  placeholder="Enter Meter No..." id="meter" required readonly>
                @if($errors->has('meter'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('meter') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/utility/all-water-bill') }}">Cancel</a>
            <a  id="paycable"class="btn btn-primary pull-right">&nbsp; Proceed to Pay &nbsp;</a>
          </div>
        </form>
      </div>
     </div>
      <div class="col-md-4">
         
  <h3>Account Details</h3>
  <ul class="list-group">
    <li class="list-group-item"  >ID <span  id="ID"class="badge">12</span></li>
    <li class="list-group-item"  >Name <span id="Name" class="badge">5</span></li>
    <li class="list-group-item"  >Due <span id="Due"class="badge">3</span></li>
    <li class="list-group-item"  >City <span id="City"class="badge">3</span></li>
    <li class="list-group-item"  >Street <span id="Street"class="badge">3</span></li>
    <li class="list-group-item"  >PostalCode <span id="PostalCode" class="badge">3</span></li>
  </ul>

           </div>
    </div>
     <div class="overlay"></div>
      <script>
                              $(document).ready(function(){
                                 $('#processed').hide();
                                 $('#find_bill').on('click',function (){
                                    var findaccount_id = $('#findaccount_id').val();
                                    var findmeter          = $('#find_meterno').val();
                                    
                                   if((findmeter == '')||(findaccount_id == ''))
                                   {
                                       swal({
                                              title: "Validation Error!",
                                              text: "All fields are required!",
                                              icon: "error",
                                              button: "Error!",
                                            });
                                   }else
                                   {
                                    //alert(meter);
                                    $.ajax({
                                       type:"POST",
                                       url:"{{url('admin/utility/find-cable-bill')}}",
                                       data : { 
                                        _token: "{{ csrf_token() }}",findmeter:findmeter,findaccount_id:findaccount_id
                                       },
                                       beforeSend: function() {
                                            $("body").addClass("loading"); 
                                        }, 
                                       success:function(res){               
                                        if(res){
                                            var obj = JSON.parse(res);
                                            if(obj.status == 'true')
                                            {
                                                $("body").removeClass("loading");
                                                $("#panel").slideUp("slow");
                                                $("#processed").show();
                                                 
                                                 console.log(obj.due);
                                                 $("#Due").text(obj.due);
                                                 $("#ID").text(obj.id);
                                                 $("#Name").text(obj.name);
                                                  $("#City").text(obj.city);
                                                  $("#Street").text(obj.street);
                                                  $("#PostalCode").text(obj.postal);
                                                  
                                                  //Set value for pay
                                                  $('#account_id').val(findaccount_id);
                                                  $('#meter').val(findmeter);
                                                  $('#amount').val(obj.due);
                                            }else
                                            {
                                                 $("body").removeClass("loading");
                                                swal({
                                                  title: "No Data Found!",
                                                  text: "Could not get bill details !",
                                                  icon: "error",
                                                  button: "Error!",
                                                });
                                              setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                            }
                                            
                                              
                                        }else{
                                            swal({
                                              title: "Loading Error!",
                                              text: "may be server or internet issue!",
                                              icon: "error",
                                              button: "Error!",
                                            });
                                        }
                                       }
                                    });
                                 }
                                   });
                               
                            
                                  //$('#processed').hide();
                                 $('#paycable').on('click',function (){
                                    var number     = $('#phone').val();
                                    var account_id = $('#account_id').val();
                                    var amount     = $('#amount').val();
                                    var meter      = $('#meter').val();
                                    var user_id   = $('#user_id').val();
                                        //alert(user_id);
                                    if((account_id == '')||(amount == '')||(meter == '')||(user_id == '')||(user_id == null))
                                   {
                                       swal({
                                              title: "Validation Error!",
                                              text: "All fields are required!",
                                              icon: "error",
                                              button: "Error!",
                                            });
                                   }else
                                   {    
                                    $.ajax({
                                       type:"POST",
                                       url:"{{url('admin/utility/pay-cable-bill')}}",
                                       data : { 
                                        _token: "{{ csrf_token() }}",number:number,account_id:account_id,amount:amount,meter:meter,user_id:user_id
                                       },
                                       beforeSend: function() {
                                            $("body").addClass("loading"); 
                                        }, 
                                       success:function(res1){               
                                        if(res1){
                                            var obj = JSON.parse(res1);
                                            $("body").removeClass("loading");
                                            
                                            console.log(obj);
                                            if(obj.msg == 'true')
                                            {
                                                swal({
                                                  title: "Congrulations!",
                                                  text: "Your Water Bill payment successful!",
                                                  icon: "success",
                                                  button: "OK!",
                                                });
                                                 setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                            }else
                                            {
                                                swal({
                                                  title: "Not Pay!",
                                                  text: "may be server or internet issue!",
                                                  icon: "error",
                                                  button: "Error!",
                                                });
                                                 setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                            }
                                        }else{
                                             $("body").removeClass("loading");
                                            swal({
                                              title: "Server Error!",
                                              text: "may be server or internet issue!",
                                              icon: "error",
                                              button: "Error!",
                                            });
                                             setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                        }
                                       }
                                    });
                                   }
                                   });
                              });
                            
                            </script>
@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<!-- jquery.validate additional-methods -->
<script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

  jQuery.validator.addMethod("letters_with_spaces", function(value, element)
  {
    return this.optional(element) || /^[A-Za-z ]+$/i.test(value); //only letters
  }, "Please enter letters only!");

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
  });


 
  $('#pay_cable_form').validate({
    rules: {
     
      account_id: {
        required: true,
        maxlength: 30,
        lettersonly: false,
      },
      
      amount: {
        required: true,
        digits: true
      },
      meter: {
        required: true
        
      },
      
    },
    messages: {
      amount: {
        digits: "Please enter number only!",
      },
      meter: {
        digits: "Please enter number only!",
      },
      
    },
  });

</script>
@endpush