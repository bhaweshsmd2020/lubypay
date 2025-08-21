@extends('user_dashboard.layouts.app')
@section('content')
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
		   @include('user_dashboard.layouts.common.tab')
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
				@include('user_dashboard.layouts.common.alert')

				<form action="{{url('packeging/store')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="product_add_form">
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<!--<h4>@lang('message.dashboard.button.new-product')</h4>-->
						</div>
						<div class="wap-wed mt20 mb20">
						    
						    <div class="form-group">
								<label>Shipping</label>
							
							<select class="form-control" name="shipping" id="shipping">
							    <?php foreach($list as $lists){?>
							    <option value="{{$lists->id}}" {{(Input::old('name') ==$lists->id) ? 'selected' :''}}>{{$lists->name}}</option>
							    <?php }?>
							</select>
								@if($errors->has('shipping'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('shipping') }}</strong>
								</span>
								@endif
							</div>
							
							
							<div class="form-group">
								<label>Dimensions</label>
								<div class="row">
								    <div class="col-md-2">
								        	<input value="{{Input::old('length')}}" class="form-control" name="length" id="length" placeholder="L"  type="text">
            								@if($errors->has('length'))
            								<span class="help-block">
            									<strong class="text-danger">{{ $errors->first('length') }}</strong>
            								</span>
            								@endif
								    </div>
								    <div class="col-md-2">
								        	<input value="{{Input::old('width')}}" class="form-control" name="width" id="width" placeholder="W"  type="text">
            								@if($errors->has('width'))
            								<span class="help-block">
            									<strong class="text-danger">{{ $errors->first('width') }}</strong>
            								</span>
            								@endif
								    </div>
								    <div class="col-md-2">
								        	<input value="{{Input::old('height')}}" class="form-control" name="height" id="height" placeholder="H" type="text">
            								@if($errors->has('height'))
            								<span class="help-block">
            									<strong class="text-danger">{{ $errors->first('height') }}</strong>
            								</span>
            								@endif
								    </div>
								    
								    <div class="col-md-4">
								        	<select class="form-control" name="dimension_unit" id="dimension_unit">
								        	    <option value="cm">CM</option>
								        	    <option value="m">Meter</option>
								        	</select>
            								@if($errors->has('dimension_unit'))
            								<span class="help-block">
            									<strong class="text-danger">{{ $errors->first('dimension_unit') }}</strong>
            								</span>
            								@endif
								    </div>
								    
								</div>
							
							</div>
							
							
							<div class="form-group">
								<label>Package Weight</label>
								<div class="row">
								    <div class="col-md-6">
								        	<input value="{{Input::old('weight')}}" class="form-control" name="weight" id="weight" placeholder="Weight"  type="text">
            								@if($errors->has('weight'))
            								<span class="help-block">
            									<strong class="text-danger">{{ $errors->first('weight') }}</strong>
            								</span>
            								@endif
								    </div>
								    
								    <div class="col-md-4">
								        	<select class="form-control" name="weight_unit" id="weight_unit">
								        	    <option value="kg">KG</option>
								        	</select>
            								@if($errors->has('weight_unit'))
            								<span class="help-block">
            									<strong class="text-danger">{{ $errors->first('weight_unit') }}</strong>
            								</span>
            								@endif
								    </div>
								    
								</div>
							
							</div>
							
                             <div class="form-group">
								<label>Package Name</label>
							   <input value="{{Input::old('name')}}" class="form-control" name="name" id="name" placeholder=""  type="text">
							    
								@if($errors->has('name'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('name') }}</strong>
								</span>
								@endif
							</div>
							

                           
							
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-cust col-12" id="product_create">
	                  			<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="product_create_text">@lang('message.dashboard.button.submit')</span>
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

	$('#merchant_add_form').validate({
		rules: {
			business_name: {
				required: true,
			},
			site_url: {
				required: true,
				url: true,
			},
			type: {
				required: true,
			},
			note: {
				required: true,
			},
			logo: {
	            extension: "png|jpg|jpeg|gif|bmp",
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