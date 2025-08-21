
<?php $__env->startSection('content'); ?>
<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
			<div class="col-md-7 col-xs-12 mb20 marginTopPlus">
				<?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<form action="<?php echo e(url('merchant/store')); ?>"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="merchant_add_form">
					<input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<h4><?php echo app('translator')->get('message.dashboard.button.new-merchant'); ?></h4>
						</div>
						<div class="wap-wed mt20 mb20">
							<div class="form-group">
								<label><?php echo app('translator')->get('message.dashboard.merchant.add.name'); ?></label>
								<input value="<?php echo e(Input::old('business_name')); ?>" class="form-control" name="business_name" id="business_name"  type="text">
								<?php if($errors->has('business_name')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('business_name')); ?></strong>
								</span>
								<?php endif; ?>
							</div>

							<div class="form-group">
								<label><?php echo app('translator')->get('message.dashboard.merchant.add.site-url'); ?></label>
								<input value="<?php echo e(Input::old('site_url')); ?>" class="form-control" name="site_url" id="site_url"  placeholder="http://www.example.com" type="text">
								<?php if($errors->has('site_url')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('site_url')); ?></strong>
								</span>
								<?php endif; ?>
							</div>

                            <div class="form-group">
                            <label for="exampleInputPassword1"><?php echo app('translator')->get('message.dashboard.send-request.common.currency'); ?></label>
                                <select class="form-control" name="currency_id">
                                    <?php $__currentLoopData = $activeCurrencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($result->id); ?>" <?php echo e($defaultWallet->currency_id == $result->id ? 'selected="selected"' : ''); ?>><?php echo e($result->code); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

							<div class="form-group">
								<label><?php echo app('translator')->get('message.dashboard.merchant.add.type'); ?></label>
								<select class="form-control" name="type" id="type">
									<option <?= old('type')=='standard'?'selected':''?> value="standard">Standard</option>
									<option <?= old('type')=='express'?'selected':''?> value="express">Express</option>
								</select>
								<?php if($errors->has('type')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('type')); ?></strong>
								</span>
								<?php endif; ?>
							</div>

								<div class="form-group">
									<label><?php echo app('translator')->get('message.dashboard.merchant.add.note'); ?></label>
									<textarea name="note" class="form-control" id="note"><?php echo e(Input::old('note')); ?></textarea>
									<?php if($errors->has('note')): ?>
										<span class="help-block">
											<strong class="text-danger"><?php echo e($errors->first('note')); ?></strong>
										</span>
									<?php endif; ?>
								</div>

							<div class="form-group">
								<label><?php echo app('translator')->get('message.dashboard.merchant.add.logo'); ?></label>
								<input class="form-control" name="logo" id="logo" type="file">
								<?php if($errors->has('logo')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('logo')); ?></strong>
								</span>
								<?php endif; ?>
								<div class="clearfix"></div>
        						<small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(100,80,'user')); ?></strong></small>

        						<p style="width: 100px !important;"><img src='<?php echo e(url('public/uploads/userPic/default-image.png')); ?>' width="100" height="80" id="merchant-demo-logo-preview"></p>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-cust col-12" id="merchant_create">
	                  			<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="merchant_create_text"><?php echo app('translator')->get('message.dashboard.button.submit'); ?></span>
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
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/Merchant/add.blade.php ENDPATH**/ ?>