@extends('user_dashboard.layouts.app')
@section('content')
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
		   @include('user_dashboard.layouts.common.tab')
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
				@include('user_dashboard.layouts.common.alert')

				<form action="{{url('attributes/storevalue')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="product_add_form">
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<!--<h4>@lang('message.dashboard.button.new-product')</h4>-->
						</div>
						<div class="wap-wed mt20 mb20">
						    <div class="form-group">
								<label>Attributes</label>
								<select class="form-control select2" id="attribute_id" name="attribute_id">
								    <?php foreach($list as $lists){?>
								    <option value="{{$lists->id}}" <?=(Input::old('attribute_id') ==$lists->id) ? 'selected' :''?>>{{$lists->name}}</option>
								    <?php }?>
								</select>
								
								@if($errors->has('attribute_id'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('attribute_id') }}</strong>
								</span>
								@endif
							</div>
						    
							<div class="form-group">
								<label>Name</label>
								<input value="{{Input::old('value')}}" class="form-control" name="value" id="value"  type="text">
								@if($errors->has('value'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('value') }}</strong>
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