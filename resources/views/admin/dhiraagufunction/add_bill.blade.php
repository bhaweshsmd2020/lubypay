@extends('admin.layouts.master')
@section('title','Add New Bill')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
   <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                    <div class="box-header with-border text-center">
                      <h3 class="box-title">Pay Postpaid Bill</h3>
                    </div>
                    <div class="box-body">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 ">
                    <div class="right mb10">
                     <span><a href="{{url('admin/all-dhiragu-bill')}}" style="background-color: #800000!important;float:right; border: none;font-weight: 400;"  class="btn btn-success"><i class="fa fa-arrow"></i>&nbsp; Back</a> </span>
                    </div>
                    <div class="clearfix"></div>
                    @include('user_dashboard.layouts.common.alert')
                    
                        <div class="card">
                           
                           <div class="row">
                               <div class="col-md-6">
                                <div class="wap-wed mt20 mb20">
                                   <div class="row">
                                       <div class="col-md-6">
                                        <b>Load Pending Bill via phone number</b>
                                    </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Destination Number<span class="text-danger">*</span></label>
                                         <input class="form-control" placeholder="Enter dhiraagu phone ..."name="phone" id="phone" type="number" value="" required>
                                        @if($errors->has('description'))
                                            <span class="help-block">
    											<strong class="text-danger">{{ $errors->first('description') }}</strong>
    										</span>
                                        @endif
                                        <p id="description-error" class="text-danger"></p>
                                    </div>
                                    </div>
                                     <div class="col-md-4"></div>
                                      <div class="col-md-4">
                                          
                                           <div class="card-footer" style="background-color: #fff!important; border-top: none;">
                                            <div class="form-group">
                                          <button style="background-color: #800000!important;float:right; border: none;font-weight: 400;"  class="btn btn-success" id="load_bill">
                                          <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="ticket_create_text">Find Bill</span>
                                        </button>
                                        </div>
                                                  
                                            </div>
                            
                                       
                                    </div>
                                    <div class="col-md-4"></div><br><br>
                                  </div>
                                </div>
                               </div>
                           
                           <div class="col-md-6 billdetails">
                            <div class="wap-wed mt20 mb20">
                               <div class="row">
                                   <ul class="list-group" style="width: -webkit-fill-available;">
                                        <li class="list-group-item"><b>Account Number</b> <span class="badge account" style="float:right;"></span></li>
                                        <li class="list-group-item"><b>Total OutStanding  </b><span class="badge outstanding" style="float:right;"></span></li>
                                        <li class="list-group-item"><b>Bill Reference </b><span class="badge reference" style="float:right;"></span></li>
                                        <li class="list-group-item"><b>Issue Date </b><span class="badge issue" style="float:right;"></span></li>
                                        <li class="list-group-item"><b>Due Date </b><span class="badge due" style="float:right;"></span></li>
                                      </ul>
                               </div>
                           
                             </div>
                           </div>
                            </div>
                            <script>
                              $(document).ready(function(){
                                 $('.paybill').hide(); 
                                 $('.billdetails').hide(); 
                                 $('#load_bill').on('click',function (){
                                    var number = $('#phone').val();
                                    if(number == '')
                                    {
                                        swal({
                                              title: "Validation Error!",
                                              text: "phone number is required",
                                              icon: "error",
                                              button: "Error!",
                                            });
                                    }else
                                    {
                                    $.ajax({
                                       type:"GET",
                                       url:"{{url('admin/load-dhiraagu-bill')}}?number="+number,
                                       beforeSend: function() {
                                            $("body").addClass("loading"); 
                                        }, 
                                       success:function(res){               
                                        if(res){
                                            $("body").removeClass("loading");
                                            var obj = JSON.parse(res);
                                            console.log(obj.status);
                                            if(obj.status == 'true')
                                            {
                                                // swal({
                                                //   title: "Invalid / unsuccessful pending bill query",
                                                //   text: "The server returned an error [Opcode=CUS_OP_SEARCH(202) Code=Server, ErrorDescription=ERR_NAP_CONNECT_FAILED",
                                                //   icon: "error",
                                                //   button: "Error!",
                                                // });
                                                 $('.billdetails').show(); 
                                                 $('.paybill').show();
                                                 $('#load_bill').hide();
                                                 
                                                 $("#PaymentIdentifier").val(obj.response.accountNumber); 
                                                 $("#bill_number").val(obj.response.billSummaryDetails[0].billReference); 
                                                 $(".account").text(obj.response.accountNumber);
                                                 $(".outstanding").text(obj.response.totalOutstanding); 
                                                 $("#pay_amount").val(obj.response.totalOutstanding); 
                                                 
                                                 $(".reference").text(obj.response.billSummaryDetails[0].billReference);
                                                 $(".issue").text(obj.response.billSummaryDetails[0].issueDate);
                                                 $(".due").text(obj.response.billSummaryDetails[0].dueDate);
                                                 console.log(obj.response.accountNumber)    
                                            }else
                                            {
                                                swal({
                                                  title: "Invalid / Unsuccessful pending Bill query",
                                                  text: obj.message,
                                                  icon: "error",
                                                  button: "Error!",
                                                });
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
                                });
                            
                            </script>
                            <div class="wap-wed mt20 mb20 paybill">
                               <div class="row">
                                   <div class="col-md-4">
                                <div class="form-group">
                                    <label for="subject">Payment Identifier<span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Payment Identifier..." name="PaymentIdentifier" id="PaymentIdentifier" type="text"readonly >
                                    @if($errors->has('subject'))
                                        <span class="help-block">
        									<strong class="text-danger">{{ $errors->first('subject') }}</strong>
        								</span>
                                    @endif
                                </div>
                                </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Amount<span class="text-danger">*</span></label>
                                     <input class="form-control" placeholder="Enter amount..."name="pay_amount" id="pay_amount" type="text" readonly>
                                    @if($errors->has('description'))
                                        <span class="help-block">
											<strong class="text-danger">{{ $errors->first('description') }}</strong>
										</span>
                                    @endif
                                    <p id="description-error" class="text-danger"></p>
                                </div>
                                </div>
                                 <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Bill No<span class="text-danger">*</span></label>
                                     <input class="form-control" placeholder="Enter bill Number..."name="bill_number" id="bill_number" type="text" readonly>
                                    @if($errors->has('description'))
                                        <span class="help-block">
											<strong class="text-danger">{{ $errors->first('description') }}</strong>
										</span>
                                    @endif
                                    <p id="description-error" class="text-danger"></p>
                                </div>
                                </div>
                                  <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Add Remark<span class="text-danger">*</span></label>
                                    <textarea name="description" placeholder="Add remark..."class="form-control"
                                              id="add_remark">{{old('description')}}</textarea>
                                    @if($errors->has('description'))
                                        <span class="help-block">
											<strong class="text-danger">{{ $errors->first('description') }}</strong>
										</span>
                                    @endif
                                    <p id="description-error" class="text-danger"></p>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Transaction Description<span class="text-danger">*</span></label>
                                    <textarea placeholder="Add transaction description..." name="description" class="form-control"
                                              id="description">{{old('description')}}</textarea>
                                    @if($errors->has('description'))
                                        <span class="help-block">
											<strong class="text-danger">{{ $errors->first('description') }}</strong>
										</span>
                                    @endif
                                    <p id="description-error" class="text-danger"></p>
                                </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="" style="border: none;font-weight: 400;" class="btn btn-success" >
                                 <span >Refresh</span>
                                </a>
                               <button style="background-color: #800000!important;float:right; border: none;font-weight: 400;"  id="pay_postpaid" class="btn btn-success" >
                                  <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="ticket_create_text">Pay Bill</span>
                               </button>
                            </div>
                        </div>
                        </div>
                         <script>
                          $(document).ready(function(){
                           
                             $('#pay_postpaid').on('click',function (){
                                var PaymentIdentifier  = $('#PaymentIdentifier').val();
                                var pay_amount         = $('#pay_amount').val();
                                var bill_number        = $('#bill_number').val();
                                var add_remark         = $('#add_remark').val();
                                var description        = $('#description').val();
                                var number = $('#phone').val();
                                if((PaymentIdentifier != '')&&(pay_amount != '')&&(bill_number != '')&&(add_remark != '')&&(description != ''))
                                {
                                   $.ajax({
                                   type:"POST",
                                   url:"{{url('admin/dhiraagu-pay-postpaid')}}",
                                   data : { 
                                        _token: "{{ csrf_token() }}" ,number:number,PaymentIdentifier:PaymentIdentifier,pay_amount:pay_amount,bill_number:bill_number,add_remark:add_remark,description:description
                                    },
                                   beforeSend: function() {
                                        $("body").addClass("loading"); 
                                    }, 
                                   success:function(res){               
                                    if(res){
                                        var obj = JSON.parse(res);
                                        console.log(obj);
                                        $("body").removeClass("loading");
                                        
                                        if(obj.status == 'true')
                                        {
                                            swal({
                                              title: obj.title,
                                              text: obj.message,
                                              icon: "success",
                                              button: "Ok!",
                                            });
                                             setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                         }else  
                                        {
                                            swal({
                                              title: obj.title,
                                              text:  obj.message,
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
                                         setTimeout(function(){
                                              window.location.reload(1);
                                            }, 2000);
                                    }
                                   }
                                });
                                }else
                                {
                                    swal({
                                          title: "Validation Error!",
                                          text: "All fields are required!",
                                          icon: "error",
                                          button: "Error!",
                                        });
                                        //  setTimeout(function(){
                                        //       window.location.reload(1);
                                        //     }, 2000);
                                }
                               });
                            });
                   </script>
                    <div class="overlay"></div>
                </div>
                <!--/col-->
            </div>
            <!--/row-->
        </div>
    </div>
     </div>
      </div>
@endsection

@push('extra_body_scripts')

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script>

jQuery.extend(jQuery.validator.messages, {
    required: "{{__('This field is required.')}}",
})

$('#ticket').validate({
    rules: {
        subject: {
            required: true
        },
        description: {
            required: true
        }
    },
    submitHandler: function(form)
    {
        $("#ticket_create").attr("disabled", true);
        $(".spinner").show();
        $("#ticket_create_text").text("{{__('Submitting...')}}");
        form.submit();
    }
});

</script>

@endpush