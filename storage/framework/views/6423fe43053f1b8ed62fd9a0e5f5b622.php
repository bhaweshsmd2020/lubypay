
<?php $__env->startSection('content'); ?>

<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-xs-12 mb20">
				<?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo $__env->make('user_dashboard.layouts.common.tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <br>
				<form action="<?php echo e(url('merchant/submitdata')); ?>"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="merchant_add_form">
					<input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">
                    
					<div class="card">
						<div class="card-header">
							<h4><?php echo app('translator')->get('message.store.my_store'); ?></h4>
						</div>
                
						<div class="wap-wed mt20 mb20">
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.name'); ?></label>
								<input class="form-control" name="name" id="name"  type="text">
								<?php if($errors->has('name')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('name')); ?></strong>
								</span>
								<?php endif; ?>
							</div>

                            <div class="form-group">
                                <label for="exampleInputPassword1"><?php echo app('translator')->get('message.store.currency'); ?></label>
                                <select class="form-control wallet" name="currency_id" id="currencies">
                                    <?php $__currentLoopData = $storeCurrencyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aCurrency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($aCurrency['id']); ?>"><?php echo e($aCurrency['code']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

							<div class="form-group">
								<label>Description</label>
								<textarea name="description" class="form-control" id="description"></textarea>
								<?php if($errors->has('description')): ?>
									<span class="help-block">
										<strong class="text-danger"><?php echo e($errors->first('description')); ?></strong>
									</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label>Store Location</label>
								 <select class="form-control" name="country" id="country">
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>" <?php echo e(($country->id == 840) ? 'selected' :''); ?>><?php echo e($country->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
								<?php if($errors->has('country')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('country')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label>Your Current Address</label>
								<input class="form-control" name="address" id="address"  type="text">
								<?php if($errors->has('address')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('address')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label>City</label>
								<input class="form-control" name="city" id="city" type="text">
								<?php if($errors->has('city')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('city')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label>State</label>
								<input class="form-control" name="state" id="state" type="text">
								<?php if($errors->has('state')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('state')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.postalcode'); ?></label>
								<input class="form-control" name="postalcode" id="postalcode"  type="text">
								<?php if($errors->has('postalcode')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('postalcode')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label>Tax (%)</label>
								<input class="form-control" name="tax" id="tax"  type="text">
								<?php if($errors->has('tax')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('tax')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.logo'); ?></label>
								<input class="form-control" name="image" id="logo" type="file">
								<?php if($errors->has('image')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('image')); ?></strong>
								</span>
								<?php endif; ?>
								<div class="clearfix"></div>
        						<small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(100,80,'user')); ?></strong></small>
                                <p style="width: 100px !important;"><img src="<?php echo e(url('public/uploads/userPic/default-image.png')); ?>" width="100" height="80" id="merchant-demo-logo-preview"></p>
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
		</div>
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('public/user_dashboard/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/additional-methods.min.js')); ?>" type="text/javascript"></script>

<?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
$(function() {
    $('#country').change( function() {
        var val = $(this).val();
        console.log(val);
        $.ajax({
           url: '<?php echo e(url('merchant/states')); ?>',
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
           url: '<?php echo e(url('merchant/citys')); ?>',
           dataType: 'html',
           data: { state : val },
           success: function(datas) {
               console.log(datas);
               $('#city').html( datas );
           }
        });
    });
});

$(".copylink").on('click',function(){
	var copyText = document.getElementById("shareurl");
	    copyText.select();
	    copyText.setSelectionRange(0, 99999); 
	    document.execCommand("copy");
	});
	jQuery.extend(jQuery.validator.messages, {
	    required: "<?php echo e(__('This field is required.')); ?>",
	    url: "<?php echo e(__("Please enter a valid URL.")); ?>",
	})

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
	
	function executeExpressMerchantQrCode(endpoint, userId)
    {
        if (userId != '')
        {
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL + endpoint,
                dataType: "json",
                data: {
                    'userId': userId,
                },
                beforeSend: function () {
                    $('.preloader').show();
                },
            })
            .done(function(response)
            {
                if (response.status == true)
                {
                    $('.express-merchant-qr-code').html(`<br>
                        <p style="font-weight: bold;">Scan QR Code</p>
                        <br>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=${response.secret}&amp;size=200x200"/>
                    `);
                    setTimeout(function(){
                        $('.preloader').hide();
                    },2000);
                }
            })
            .fail(function(error)
            {
                console.log(error);
            });
        }
        else
        {
            $('.express-merchant-qr-code').html('');
        }
    }
    
    $('#storeMerchantQrCodeModal').on('show.bs.modal', function (e)
    {
        var endpoint = "/merchant/generate-store-merchant-qr-code";
        var userId = $(e.relatedTarget).attr('data-clientId');

        $('#user_id').val(userId);

        executeExpressMerchantQrCode(endpoint, userId);
    });

    $(document).on('click','.update-store-merchant-qr-code',function(e)
    {
        e.preventDefault();

        let endpoint = "/merchant/update-store-merchant-qr-code";
        var userId = $('#user_id').val();
        executeExpressMerchantQrCode(endpoint, userId);
    });

    $(document).on('click','#qr-code-print-store',function(e)
    {
        e.preventDefault();

        let userId = $('#user_id').val();
        let printQrCodeUrl = SITE_URL+'/merchant/qr-code-print/'+userId+'/store';
        $(this).attr('href', printQrCodeUrl);
        window.open($(this).attr('href'), '_blank');
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/Merchant/mystorenew.blade.php ENDPATH**/ ?>