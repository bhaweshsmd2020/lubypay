@extends('admin.layouts.master')
@section('title','New Cashin')
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
                      <h3 class="box-title">Add Cashin</h3>
                    </div>
                    <div class="box-body">
                            
                               <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="description11">Choose Customer<span class="text-danger">*</span></label>
                                         <select name="" id="cid" class="form-control">
                                          <option value="" selected disabled>Select</option>
                                          @foreach($customer as $value)
                                          <option value="{{$value->id}}">{{$value->first_name}}</option>
                                          @endforeach
                                          </seelct>
                                    </div>
                                </div> 
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="description">Customer Number<span class="text-danger">*</span></label>
                                         <input class="form-control" placeholder="Enter Customer Number..."name="number" readonly id="destinationnumber" type="number" >
                                        @if($errors->has('description'))
                                            <span class="help-block">
    											<strong class="text-danger">{{ $errors->first('description') }}</strong>
    										</span>
                                        @endif
                                        <p id="description-error" class="text-danger"></p>
                                    </div>
                                    </div>
                                  
                                 <script>
                                 $('#cid').change(function(){
                                        var customerID = $(this).val();  
                                   //alert(customerID);	
                                        if(customerID){
                                            $.ajax({
                                               type:"GET",
                                               url:"{{url('admin/get-customer-id')}}?customerID="+customerID,
                                               success:function(res){               
                                                if(res){
                                                    console.log(res);
                                                    $("#destinationnumber").val(res);
                                                }else{
                                                   $("#destinationnumber").empty();
                                                }
                                               }
                                            });
                                        }else{
                                            $("#destinationnumber").empty();
                                            
                                        }      
                                       });
                                    </script>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="description">Amount<span class="text-danger">*</span></label>
                                         <input class="form-control" placeholder="Enter amount..."name="amount" id="amount" type="text" >
                                        @if($errors->has('description'))
                                            <span class="help-block">
    											<strong class="text-danger">{{ $errors->first('description') }}</strong>
    										</span>
                                        @endif
                                        <p id="description-error" class="text-danger"></p>
                                    </div>
                                 </div>
                                 </div>
                                 
                                  <div class="row">
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Transaction Description<span class="text-danger">*</span></label>
                                    <textarea placeholder="Add transaction description..." name="tran_description" class="form-control"
                                              id="tran_description"></textarea>
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
                                <a href="{{url('admin/all-cashin')}}" style="border: none;font-weight: 400;"type="submit" class="btn btn-success" >
                                 <span >Back</span>
                                </a>
                               <button style="background-color: #800000!important;float:right; border: none;font-weight: 400; " class="btn btn-success" id="cashin">
                                  <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="ticket_create_text">@lang('message.dashboard.button.submit')</span>
                                </button>
                                  
                            </div>
                       
                        </div>
                         <script>
                          $(document).ready(function(){
                           
                             $('#cashin').on('click',function (){
                                var destinationnumber  = $('#destinationnumber').val();
                                var amount             = $('#amount').val();
                                var tran_description   = $('#tran_description').val();
                                var cid = $('#cid').val();
                                if((destinationnumber != '')&&(amount != '')&&(tran_description != ''))
                                {
                                   $.ajax({
                                   type:"POST",
                                   url:"{{url('admin/dhiraagu-cashin')}}",
                                   data : { 
                                        _token: "{{ csrf_token() }}" ,destinationnumber:destinationnumber,amount:amount,tran_description:tran_description,cid:cid
                                    },
                                   beforeSend: function() {
                                        $("body").addClass("loading"); 
                                    }, 
                                   success:function(res){               
                                    if(res){
                                        var obj = JSON.parse(res);
                                        $("body").removeClass("loading");
                                        
                                        if(obj.msg == 'true')
                                        {
                                            swal({
                                              title: 'Congrulations!',
                                              text: 'Request Accepted(1079)',
                                              icon: "success",
                                              button: "Ok!",
                                            });
                                             setTimeout(function(){
                                               window.location.reload(1);
                                            }, 2000);
                                        }else
                                        {
                                            swal({
                                              title: 'Number Not Valid!',
                                              text: 'QUERYPROFILE:Destination number in wrong format(1137)',
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
                                         setTimeout(function(){
                                               window.location.reload(1);
                                            }, 2000);
                                }
                               });
                            });
                   </script>
                 </div>
                  <div class="overlay"></div>
                <!--/col-->
            </div>
            <!--/row-->
       
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