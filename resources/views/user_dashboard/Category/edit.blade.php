@extends('user_dashboard.layouts.app')
@section('content')
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
		    @include('user_dashboard.layouts.common.tab')
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
			    
			    	
				@include('user_dashboard.layouts.common.alert')

				<form action="{{url('categories/update')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="product_add_form">
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<h4>Category</h4>
						</div>
						<div class="wap-wed mt20 mb20">
						    
						   <input value="{{$details['id']}}" class="form-control" name="id" id="id"  type="hidden">
						    
							<div class="form-group">
								<label>@lang('message.dashboard.product.add.name')</label>
								<input value="{{$details['name']}}" class="form-control" name="name" id="name"  type="text">
								@if($errors->has('name'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('name') }}</strong>
								</span>
								@endif
							</div>

							
                            <div class="form-group">
                            <label for="exampleInputPassword1">@lang('message.dashboard.product.add.description')</label>
                                <textarea name="description" class="form-control" id="description">{{$details['description']}}</textarea>
								@if($errors->has('description'))
									<span class="help-block">
										<strong class="text-danger">{{ $errors->first('description') }}</strong>
									</span>
								@endif
                            </div>
                            
                           
						
							
						
							
						
							
						

							<div class="form-group">
								<label>@lang('message.dashboard.product.add.image')</label>
								<input class="form-control" name="image" id="image" type="file">
								@if($errors->has('image'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('image') }}</strong>
								</span>
								@endif
								<div class="clearfix"></div>
        						<small class="form-text text-muted"><strong>{{ allowedImageDimension(200,200,'user') }}</strong></small>

        						@if (!empty($details['image']))
									<p style="width: 200px !important;"><img src="{{url('public/user_dashboard/categories/'.$details['image'])}}" width="200" height="200" id="merchant-logo-preview"></p>
								@else
									<p style="width: 200px !important;"><img src='{{ url('public/uploads/userPic/default-image.png') }}' width="200" height="200" id="merchant-demo-logo-preview"></p>
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