@extends('user_dashboard.layouts.app')
@section('content')
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
				@include('user_dashboard.layouts.common.alert')

				<form action="{{url('merchant/shippingcoststore')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="shippingcost_add_form">
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<h4>@lang('message.dashboard.button.new-shipment-cost')</h4>
						</div>
						<div class="wap-wed mt20 mb20">
						    
						    <div class="form-group">
								<label>@lang('message.dashboard.merchant.add.name')</label>
								<input value="{{Input::old('name')}}" class="form-control" name="name" id="name"  type="text">
								@if($errors->has('name'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('name') }}</strong>
								</span>
								@endif
							</div>
							
						    <div class="form-group">
								<label>@lang('message.store.carriers')</label>
								<?php $carrier = DB::table('carriers')->get();?>
								 <select class="form-control" name="carriers" id="carriers">
								     <option value="" >Select Shipping Carrier</option>
                                    @foreach ($carrier as $carriers)
                                        <option value="{{ $carriers->id }}" >{{ $carriers->name }}</option>
                                    @endforeach
                                </select>
								@if($errors->has('carriers'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('carriers') }}</strong>
								</span>
								@endif
							</div>
						    
						    
						    <div class="form-group">
								<label>@lang('message.store.country')</label>
								<?php $countries = DB::table('countries')->get();?>
								 <select class="form-control" name="country" id="country">
								     <option value="" >Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" >{{ $country->name }}</option>
                                    @endforeach
                                </select>
								@if($errors->has('country'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('country') }}</strong>
								</span>
								@endif
							</div>
							<div class="form-group">
								<label>@lang('message.store.state')</label>
									<?php $states = DB::table('states')->get();?>
								<select class="form-control" name="states" id="states">
                                   <option value="" >Select State</option>
                                </select>
                                
								@if($errors->has('state'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('state') }}</strong>
								</span>
								@endif
							</div>
							<div class="form-group">
								<label>@lang('message.store.city')</label>
								<?php $citys = DB::table('city')->get();?>
								<select class="form-control" name="city" id="city">
								    <option value="" >Select City</option>
                                </select>
								@if($errors->has('city'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('city') }}</strong>
								</span>
								@endif
							</div>
							
							<div class="form-group">
								<label>@lang('message.dashboard.merchant.add.duration')</label>
								<input value="{{Input::old('duration')}}" class="form-control" name="duration" id="duration"  type="number">
								@if($errors->has('duration'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('duration') }}</strong>
								</span>
								@endif
							</div>
							
							<div class="form-group">
								<label>@lang('message.dashboard.merchant.add.min-amount')</label>
								<input value="{{Input::old('min-amount')}}" class="form-control" name="min_amount" id="min-amount"  type="number">
								@if($errors->has('min-amount'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('min-amount') }}</strong>
								</span>
								@endif
							</div>
							
							<div class="form-group">
								<label>@lang('message.dashboard.merchant.add.max-amount')</label>
								<input value="{{Input::old('max-amount')}}" class="form-control" name="max_amount" id="max-amount"  type="number">
								@if($errors->has('max-amount'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('max-amount') }}</strong>
								</span>
								@endif
							</div>
							
							
                            <div class="form-group">
                            <label for="exampleInputPassword1">@lang('message.dashboard.send-request.common.currency')</label>
                                <select class="form-control" name="currency_id">
                                    @foreach($activeCurrencies as $result)
                                            <option value="{{ $result->id }}" {{ $defaultWallet->currency_id == $result->id ? 'selected="selected"' : '' }}>{{ $result->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
								<label>@lang('message.dashboard.merchant.add.price')</label>
								<input value="{{Input::old('price')}}" class="form-control" name="price" id="price"  type="number">
								@if($errors->has('price'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('price') }}</strong>
								</span>
								@endif
							</div>
						

								

						
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-cust col-12" id="merchant_create">
	                  			<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="merchant_create_text">@lang('message.dashboard.button.submit')</span>
	                  		</button>
						</div>
					</div>
				</form>
			</div>
			<!--/col-->
		</div>
		<!--/row-->
	</div>
</section>
@endsection

@section('js')

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>

<!-- read-file-on-change -->
@include('common.read-file-on-change')

<script>
    $(function() {
    $('#country').change( function() {
        var val = $(this).val();
        console.log(val);
        $.ajax({
           url: '{{ url('merchant/states') }}',
           dataType: 'html',
           data: { country : val },
           success: function(data) {
               console.log(data);
               $('#state').html( data );
           }
        });
    });
    
    
    $('#state').change( function() {
        var val = $(this).val();
        console.log(val);
        $.ajax({
           url: '{{ url('merchant/citys') }}',
           dataType: 'html',
           data: { state : val },
           success: function(datas) {
               console.log(datas);
               $('#city').html( datas );
           }
        });
    });
});




	jQuery.extend(jQuery.validator.messages, {
	    required: "{{__('This field is required.')}}",
	    url: "{{__("Please enter a valid URL.")}}",
	})

	// preview currency logo on change
    $(document).on('change','#logo', function()
    {
        let orginalSource = '{{ url('public/uploads/userPic/default-image.png') }}';
        readFileOnChange(this, $('#merchant-demo-logo-preview'), orginalSource);
    });

	$('#shippingcost_add_form').validate({
		rules: {
			country: {
				required: true,
			},
			state: {
				required: false,
			},
			city: {
				required: false,
			},
			price: {
				required: true,
			},
		
		},
		messages: {
	      logo: {
	        extension: "{{__("Please select (png, jpg, jpeg, gif or bmp) file!")}}"
	      }
	    },
		submitHandler: function(form)
	    {
	        $("#merchant_create").attr("disabled", true);
	        $(".spinner").show();
	        $("#merchant_create_text").text("{{__('Submitting...')}}");
	        form.submit();
	    }
	});

</script>
@endsection