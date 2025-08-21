@extends('user_dashboard.layouts.app')
@section('content')
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
		    @include('user_dashboard.layouts.common.tab')
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
				@include('user_dashboard.layouts.common.alert')

				<form action="{{url('product/update')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="product_add_form">
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<h4>Update Product</h4>
						</div>
						<div class="wap-wed mt20 mb20">
						    
						    
						    <div class="form-group">
								<label>Product SKU</label>
							    <input type="hidden" value="{{$details['id']}}" name="id">
								<input value="<?=$details['user_product_id']?>" class="form-control" name="product_id" id="product_id"  type="text">
								@if($errors->has('product_id'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('product_id') }}</strong>
								</span>
								@endif
							</div>
						    
						    <div class="form-group">
								<label>Select Category</label>
							    <select name="category_id" id="category_id" class="form-control">
							        <?php foreach($categories as $category){?>
							        <option value="{{$category->id}}"  <?=($category->id ==$details['category_id']) ? 'selected' : '' ?> >{{$category->name}}</option>
							        <?php }?>
							    </select>
								
								@if($errors->has('category_id'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('category_id') }}</strong>
								</span>
								@endif
							</div>
							
							<?php foreach($attributes as $attr){?>
						    <div class="form-group">
						        <?php $Attrvalues = DB::table('attribute_values')->where('attribute_id',$attr->id)->where('active',1)->get();
						        
						        if(isset($Attrvalues) && count($Attrvalues) > 0){?>
								<label>Select {{$attr->name}}</label>
								<select name="attributes_{{$attr->id}}[]" id="attribute_values" class="form-control" multiple>
								    
								    <?php 
								        $selecteddetils = DB::table('product_attributes')->where('product_id',$details['id'])->where('attributes',$attr->id)->first();
								        if($selecteddetils && $selecteddetils->attributes_values)
								        {
								            $selected_values = json_decode($selecteddetils->attributes_values,true);
								        }
								        else
								        {
								            $selected_values = array();
								        }
								    ?>
							        <?php foreach($Attrvalues as $vallues){ 
							            
							        ?>
							        <option value="{{$vallues->id}}"  <?=(is_array($selected_values) && in_array($vallues->id, $selected_values)) ? 'selected' :''?>>{{$vallues->value}}</option>
							        <?php }}?>
							    </select>
								
								@if($errors->has('category_id'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('category_id') }}</strong>
								</span>
								@endif
							</div>
							
							<?php  }?>
						    
						   
							
						    
						    
							<div class="form-group">
								<label>@lang('message.dashboard.product.add.name')</label>
								<input value="{{$details['name']}}" class="form-control" name="product_name" id="product_name"  type="text">
								@if($errors->has('product_name'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('product_name') }}</strong>
								</span>
								@endif
							</div>

							<div class="form-group">
								<label>@lang('message.dashboard.product.add.price')</label>
								<input value="{{$details['price']}}" class="form-control" name="price" id="price"  placeholder="0.00" type="number">
								@if($errors->has('price'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('price') }}</strong>
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
								<label>@lang('message.dashboard.product.add.quantity')</label>
								<input value="{{$details['quantity']}}" class="form-control" name="quantity" id="quantity"  placeholder="10" type="number">
								@if($errors->has('quantity'))
								<span class="help-block">
									<strong class="text-danger">{{ $errors->first('quantity') }}</strong>
								</span>
								@endif
							</div>
							
							<!--<div class="form-group">-->
							<!--	<label>@lang('message.dashboard.product.add.weight')</label>-->
							<!--	<input value="{{$details['weight']}}" class="form-control" name="weight" id="weight"  placeholder="10" type="number">-->
							<!--	@if($errors->has('weight'))-->
							<!--	<span class="help-block">-->
							<!--		<strong class="text-danger">{{ $errors->first('weight') }}</strong>-->
							<!--	</span>-->
							<!--	@endif-->
							<!--</div>-->
							
							<!--<div class="form-group">-->
							<!--	<label>@lang('message.dashboard.product.add.height')</label>-->
							<!--	<input value="{{$details['height']}}" class="form-control" name="height" id="height"  placeholder="10" type="number">-->
							<!--	@if($errors->has('height'))-->
							<!--	<span class="help-block">-->
							<!--		<strong class="text-danger">{{ $errors->first('height') }}</strong>-->
							<!--	</span>-->
							<!--	@endif-->
							<!--</div>-->
							
							<!--<div class="form-group">-->
							<!--	<label>@lang('message.dashboard.product.add.length')</label>-->
							<!--	<input value="{{$details['length']}}" class="form-control" name="length" id="length"  placeholder="10" type="number">-->
							<!--	@if($errors->has('length'))-->
							<!--	<span class="help-block">-->
							<!--		<strong class="text-danger">{{ $errors->first('length') }}</strong>-->
							<!--	</span>-->
							<!--	@endif-->
							<!--</div>-->
							
							<!--<div class="form-group">-->
							<!--	<label>@lang('message.dashboard.product.add.width')</label>-->
							<!--	<input value="{{$details['width']}}" class="form-control" name="width" id="width"  placeholder="10" type="number">-->
							<!--	@if($errors->has('width'))-->
							<!--	<span class="help-block">-->
							<!--		<strong class="text-danger">{{ $errors->first('width') }}</strong>-->
							<!--	</span>-->
							<!--	@endif-->
							<!--</div>-->

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

        						@if (!empty($product->image))
									<p style="width: 200px !important;"><img src="{{url('public/user_dashboard/merchant/'.$merchant->logo)}}" width="200" height="200" id="merchant-logo-preview"></p>
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