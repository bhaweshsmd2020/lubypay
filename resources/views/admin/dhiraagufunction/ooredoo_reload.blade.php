@extends('admin.layouts.master')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@section('title', 'Ooredoo Topup')

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
  
  <div class="col-md-8" id="panel">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Find Ooredoo Customer</h3>
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
            
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/dhiragu-all-reload') }}">Cancel</a>
            <a  id="find_bill"class="btn btn-primary pull-right">&nbsp; Find Bill &nbsp;</a>
          </div>
        </form>
      </div>
     
    </div>
                           
  
    <div class="col-md-12" id="processed">
      <!-- Horizontal Form -->
      
        <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Make Topup With Ooredoo</h3>
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
                    @foreach($customer as $user)
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
             
            <!-- <div class="form-group">-->
            <!--  <label class="col-sm-3 control-label" for="account_id">Phone No</label>-->
            <!--  <div class="col-sm-6">-->
            <!--    <input type="text" name="phone" class="form-control"  placeholder="Enter Phone No" id="phone">-->
            <!--    @if($errors->has('phone'))-->
            <!--    <span class="help-block">-->
            <!--      <strong class="text-danger">{{ $errors->first('phone') }}</strong>-->
            <!--    </span>-->
            <!--    @endif-->
            <!--  </div>-->
            <!--</div>-->
            
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

            <input type="hidden" id="slected_product_id" value="">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="amount">Amount</label>
              <div class="col-sm-6">
                <input type="number" name="amount" class="form-control"  placeholder="Enter Amount" id="amount" >
                @if($errors->has('amount'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('amount') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/dhiragu-all-reload') }}">Back</a>
            <a  id="paycable"class="btn btn-primary pull-right">&nbsp; Proceed to Pay &nbsp;</a>
          </div>
        </form>
      </div>
     </div>
      <div class="col-md-6">
         <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Ooredoo Customer Details</h3>
        </div> 
  
          <table class="table table-bordered" style="background-color: #800000!important; color: #fff;">
            <thead>
              <tr>
                <th><b>Customer Name</b></th>
                <th><b>Address</b></th>
                <th><b>Rateplan</b></th>
              </tr>
            </thead>
            <tbody class="listing">
             
            </tbody>
          </table>

           </div>
           </div>
    </div>
     <div class="overlay"></div>
      <script>
                              $(document).ready(function(){
                                 $('#processed').hide();
                                 $('#find_bill').on('click',function (){
                                    var findaccount_id = $('#findaccount_id').val();
                                   
                                    if(findaccount_id == '')
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
                                        url:"{{url('admin/load-ooredoo-customer')}}",
                                       data : { 
                                        _token: "{{ csrf_token() }}",account_number:findaccount_id
                                       },
                                       beforeSend: function() {
                                            $("body").addClass("loading"); 
                                        }, 
                                       success:function(res){               
                                        if(res){
                                            $("body").removeClass("loading");
                                             var obj = JSON.parse(res);
                                             console.log(obj);
                                             if(obj.status == 'false')
                                             {
                                                  swal({
                                                  title: obj.title,
                                                  text: obj.message,
                                                  icon: "error",
                                                  button: "Error!",
                                                });
                                             }else
                                             {
                                            $("#panel").slideUp("slow");
                                            $("#processed").show();
                                            $('#account_id').val(findaccount_id);
                                            $('#slected_product_id').val(obj.response["User ID"]);
                                            var htmlString  =    '<tr>'
                                                        htmlString +=    '<td>'+ obj.response["Full Name"] +'</td>'
                                                        htmlString +=    '<td>'+ obj.response["Address"] +'</td>'
                                                        htmlString +=    '<td>'+ obj.response["Rateplan"] +'</td>'
                                                        htmlString +=    '</tr>';
                                                 console.log(htmlString);
                                                  $('.listing').append(htmlString); //add the current content to existing one
                                        
                                             }
                                         }else{
                                           
                                        }
                                       }
                                    });
                                 }
                                   });
                              
                                
    
                                  //$('#processed').hide();
                                 $('#paycable').on('click',function (){
                                    var number     = $('#phone').val();
                                    var slected_product_id    = $('#slected_product_id').val();
                                    var account_id = $('#account_id').val();
                                    var amount     = $('#amount').val();
                                    var user_id   = $('#user_id').val();
                                    //alert(user_id);
                                    if((account_id == '')||(amount == '')||(user_id == ''))
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
                                       url:"{{url('admin/pay-ooredoo-topup')}}",
                                       data : { 
                                        _token: "{{ csrf_token() }}",number:number,account_id:account_id,amount:amount,user_id:user_id
                                       },
                                       beforeSend: function() {
                                            $("body").addClass("loading"); 
                                        }, 
                                       success:function(res1){               
                                        if(res1){
                                            var obj = JSON.parse(res1);
                                            $("body").removeClass("loading");
                                            
                                            console.log(obj);
                                            if(obj.status == 'true')
                                            {
                                                swal({
                                                  title: obj.title,
                                                  text: obj.message,
                                                  icon: "success",
                                                  button: "OK!",
                                                });
                                                 setTimeout(function(){
                                              window.location.reload(1);
                                            }, 5000);
                                            }else
                                            {
                                                swal({
                                                  title: obj.title,
                                                  text: obj.message,
                                                  icon: "error",
                                                  button: "Error!",
                                                });
                                                 setTimeout(function(){
                                              window.location.reload(1);
                                            }, 5000);
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
                                            }, 5000);
                                        }
                                       }
                                    });
                                   }
                                   });
                              });
                            
                            </script>
                             <script>
                               var arr = [];
                               function myFunction11(prd_id,id) {
                                var prod_attr = "#prd_"+id;
                                 //alert("#prd_"+id);
                                if($(prod_attr).prop('checked') == true){
                                      var amt = $('#amount').val();
                                      arr.push(prd_id);
                                      sum = 0;
                                      $.each(arr,function(){sum+=parseFloat(this) || 0;});
                                        //alert(sum);
                                      $('#amount').val(sum);
                                      $('#slected_product_id').val(id);
                                      
                                }
                                else{
                                      var amt = $('#amount').val();
                                      //arr.pop(prd_id);
                                     
                                        //alert(sum);
                                        arr = $.grep(arr, function(value) {
                                        return value != prd_id;
                                        });
                                      sum = 0;
                                      $.each(arr,function(){sum+=parseFloat(this) || 0;});
                                      $('#amount').val(sum);
                                 }
                                }
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