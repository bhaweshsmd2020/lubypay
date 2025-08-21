@extends('admin.layouts.master')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

@section('title', 'Pay Electricy Bill')

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
              <label class="col-sm-3 control-label" for="account_id">NIC ID</label>
              <div class="col-sm-6">
                <input type="text" name="nic_id" class="form-control"  placeholder="Enter your NIC ID" id="nic_id" required>
                @if($errors->has('nic_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('nic_id') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/utility/maldive-gas') }}">Cancel</a>
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
          <h3 class="box-title">Pay Cable Bill</h3>
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
              <label class="col-sm-3 control-label" for="amount">NIC ID</label>
              <div class="col-sm-6">
                <input type="text" name="newnic_id" class="form-control"  placeholder="Enter NIC ID" id="newnic_id">
                @if($errors->has('amount'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('amount') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/utility/maldive-gas') }}">Cancel</a>
            <a  id="paycable"class="btn btn-primary pull-right">&nbsp; Proceed to Pay &nbsp;</a>
          </div>
        </form>
      </div>
     </div>
      <div class="col-md-4">
         
  <h3>Account Details</h3>
  <ul class="list-group">
    <li class="list-group-item"  >Product Id <span  id="ID"class="badge">12</span></li>
    <li class="list-group-item"  >Product Name <span id="Name" class="badge">5</span></li>
    <li class="list-group-item"  >Due Amount<span id="Due"class="badge">3</span></li>
    
  </ul>

           </div>
    </div>
     <div class="overlay"></div>
      <script>
                              $(document).ready(function(){
                                  $('#processed').hide();
                                 $('#find_bill').on('click',function (){
                                    var findaccount_id = $('#findaccount_id').val();
                                    var nic_id         = $('#nic_id').val();
                                    if((findaccount_id == '')||(nic_id == ''))
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
                                       url:"{{url('admin/utility/find-maldive-gas-bill')}}",
                                       data : { 
                                        _token: "{{ csrf_token() }}",findaccount_id:findaccount_id,nic_id:nic_id
                                       },
                                       beforeSend: function() {
                                            $("body").addClass("loading"); 
                                        }, 
                                       success:function(res){               
                                        if(res){
                                            var obj = JSON.parse(res);
                                            if ("error" in obj)
                                             {
                                                  $("body").removeClass("loading");
                                                   swal({
                                                      title: "No Data Found!",
                                                      text: "Could not get any data!",
                                                      icon: "error",
                                                      button: "Error!",
                                                    });
                                             }else
                                             {
                                                $("body").removeClass("loading");
                                                $("#panel").slideUp("slow");
                                                $("#processed").show();
                                                 
                                                 console.log(obj.product[0].product_price);
                                                 $("#Due").text(obj.product[0].product_price);
                                                 $("#ID").text(obj.product[0].product_id);
                                                 $("#Name").text(obj.product[0].product_name);
                                                 
                                                  //Set value for pay
                                                  $('#account_id').val(findaccount_id);
                                                  $('#amount').val(obj.product[0].product_price);
                                                  $('#newnic_id').val(nic_id);
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
                                    var user_id   = $('#user_id').val();
                                     var newnic_id   = $('#newnic_id').val();
                                    
                                        //alert(user_id);
                                    if((account_id == '')||(amount == '')||(user_id == '')||(newnic_id == ''))
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
                                       url:"{{url('admin/utility/pay-maldive-gas-bill')}}",
                                       data : { 
                                        _token: "{{ csrf_token() }}",number:number,account_id:account_id,amount:amount,user_id:user_id,newnic_id:newnic_id
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
                                                  text: "Your Gas Bill payment successfully Done!",
                                                  icon: "success",
                                                  button: "OK!",
                                                });
                                                 setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                            }else
                                            {
                                                swal({
                                                  title: "No Data Found!",
                                                  text: "Could not get any response!",
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

</script>
@endpush