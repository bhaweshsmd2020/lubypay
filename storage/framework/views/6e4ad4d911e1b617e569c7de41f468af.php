
<?php $__env->startSection('content'); ?>
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
		   <?php echo $__env->make('user_dashboard.layouts.common.tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
				<?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<form action="<?php echo e(url('categories/store')); ?>"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="product_add_form">
					<input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<!--<h4><?php echo app('translator')->get('message.dashboard.button.new-product'); ?></h4>-->
						</div>
						<div class="wap-wed mt20 mb20">
						    <div class="form-group">
								<label>Sort Order</label>
								<input value="<?php echo e(Input::old('order')); ?>" class="form-control" name="order" id="order"  type="text">
								<?php if($errors->has('order')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('order')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
						    
							<div class="form-group">
								<label><?php echo app('translator')->get('message.dashboard.product.add.name'); ?></label>
								<input value="<?php echo e(Input::old('name')); ?>" class="form-control" name="name" id="name"  type="text">
								<?php if($errors->has('name')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('name')); ?></strong>
								</span>
								<?php endif; ?>
							</div>

							

                            <div class="form-group">
                            <label for="exampleInputPassword1"><?php echo app('translator')->get('message.dashboard.product.add.description'); ?></label>
                                <textarea name="description" class="form-control" id="description"><?php echo e(Input::old('description')); ?></textarea>
								<?php if($errors->has('description')): ?>
									<span class="help-block">
										<strong class="text-danger"><?php echo e($errors->first('description')); ?></strong>
									</span>
								<?php endif; ?>
                            </div>

							<div class="form-group">
								<label><?php echo app('translator')->get('message.dashboard.product.add.image'); ?></label>
								<input class="form-control" name="image" id="image" type="file">
								<?php if($errors->has('image')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('image')); ?></strong>
								</span>
								<?php endif; ?>
								<div class="clearfix"></div>
        						<small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(100,80,'user')); ?></strong></small>

        						<?php if(!empty($product->image)): ?>
									<p style="width: 100px !important;"><img src="<?php echo e(url('public/user_dashboard/merchant/'.$merchant->logo)); ?>" width="100" height="80" id="merchant-logo-preview"></p>
								<?php else: ?>
									<p style="width: 100px !important;"><img src='<?php echo e(url('public/uploads/userPic/default-image.png')); ?>' width="100" height="80" id="merchant-demo-logo-preview"></p>
								<?php endif; ?>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-cust col-12" id="product_create">
	                  			<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="product_create_text"><?php echo app('translator')->get('message.dashboard.button.submit'); ?></span>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<script src="<?php echo e(asset('public/user_dashboard/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<script src="<?php echo e(asset('public/user_dashboard/js/additional-methods.min.js')); ?>" type="text/javascript"></script>

<!-- read-file-on-change -->
<?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>

	jQuery.extend(jQuery.validator.messages, {
	    required: "<?php echo e(__('This field is required.')); ?>",
	    url: "<?php echo e(__("Please enter a valid URL.")); ?>",
	})

	// preview currency logo on change
    $(document).on('change','#logo', function()
    {
        let orginalSource = '<?php echo e(url('public/uploads/userPic/default-image.png')); ?>';
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
	        extension: "<?php echo e(__("Please select (png, jpg, jpeg, gif or bmp) file!")); ?>"
	      }
	    },
		submitHandler: function(form)
	    {
	        $("#merchant_create").attr("disabled", true);
	        $(".spinner").show();
	        $("#merchant_create_text").text("<?php echo e(__('Submitting...')); ?>");
	        form.submit();
	    }
	});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/Category/add.blade.php ENDPATH**/ ?>