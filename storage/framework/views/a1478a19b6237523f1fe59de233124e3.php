<?php $__env->startSection('content'); ?>
<style>
    .marginTopPlus {
    margin-top: 0px!important;
}
</style>

<section class="section-06 history padding-30">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-xs-12 mb20 marginTopPlus">
				<?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				
				
				
                <?php echo $__env->make('user_dashboard.layouts.common.tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                <br>
				<form action="<?php echo e(url('merchant/submitdata')); ?>"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="merchant_add_form">
					<input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">

					<div class="card">
						<div class="card-header">
							<h4><?php echo app('translator')->get('message.store.my_store'); ?></h4>
							<input type="textbox" id="shareurl" value="<?php echo $store_url;?>" style="width:500px;padding:10px;" >
							<i class="fa fa-clipboard fa-2 copylink" aria-hidden="true" style="font-size:32px;margin-left:5px;cursor:pointer;" title="Copy Url"></i>
							<i class="fa fa-qrcode" style="font-size:32px;margin-left:5px;cursor:pointer;" data-clientId="<?php echo e($data['user_id']); ?>" data-toggle="modal" data-target="#storeMerchantQrCodeModal"></i>
						</div>
						
						<input type="hidden" class="form-control" name="user_id" id="user_id" value="<?php echo e($data['user_id']); ?>">
                
                        <div id="storeMerchantQrCodeModal" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Store QR Code</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="preloader" style="display: none;">
                                                        <div class="preloader-img"></div>
                                                    </div>
                                                    <div class="express-merchant-qr-code" style="text-align: center;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal"><?php echo app('translator')->get('message.dashboard.merchant.html-form-generator.close'); ?></button>
                                        <!--<a href="#" class="btn btn-secondary" id="qr-code-print-store">-->
                                        <!--    <strong>Print</strong>-->
                                        <!--</a>-->
                                        <button type="button" class="btn btn-secondary update-store-merchant-qr-code"><?php echo app('translator')->get('message.dashboard.button.update'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
						<div class="wap-wed mt20 mb20">
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.name'); ?></label>
								<input value="<?php if(isset($data['name'])){ echo $data['name'];}else{ ?><?php echo e(Input::old('name')); ?> <?php }?>" class="form-control" name="name" id="name"  type="text">
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

                                                        <option value="<?php echo e($aCurrency['id']); ?>"<?php echo e(isset($data['currency_id']) && $data['currency_id'] == $aCurrency['id'] ? 'selected="selected"' : ''); ?>><?php echo e($aCurrency['code']); ?></option>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>

                                                

                                            </div>

                                      

							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.description'); ?></label>
								<textarea name="description" class="form-control" id="description"><?php if(isset($data['description'])){ echo strip_tags($data['description']);}else{ ?><?php echo e(Input::old('description')); ?><?php }?></textarea>
									<?php if($errors->has('description')): ?>
										<span class="help-block">
											<strong class="text-danger"><?php echo e($errors->first('description')); ?></strong>
										</span>
									<?php endif; ?>
								
							</div>
							
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.address'); ?></label>
								<input value="<?php if(isset($data['address'])){ echo $data['address'];}else{ ?><?php echo e(Input::old('address')); ?> <?php }?>" class="form-control" name="address" id="address"  type="text">
								<?php if($errors->has('address')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('address')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.country'); ?></label>
								<?php $countries = DB::table('countries')->get();?>
								 <select class="form-control" name="country" id="country">
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>" <?php echo e($data['country'] == $country->id ? 'selected="selected"' : ''); ?>><?php echo e($country->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
								<?php if($errors->has('country')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('country')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.state'); ?></label>
									<?php $states = DB::table('states')->where('country_id',$data['country'])->get();?>
								<select class="form-control" name="state" id="state">
                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($state->id); ?>" <?php echo e($data['state'] == $state->id ? 'selected="selected"' : ''); ?>><?php echo e($state->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                
								<?php if($errors->has('state')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('state')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.city'); ?></label>
								<?php $citys = DB::table('city')->where('state_id',$data['state'])->get();?>
								<select class="form-control" name="city" id="city">
								    <option value="" >Select City</option>
                                    <?php $__currentLoopData = $citys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($city->id); ?>" <?php echo e($data['city'] == $city->id ? 'selected="selected"' : ''); ?>><?php echo e($city->city_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
								<?php if($errors->has('city')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('city')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							<div class="form-group">
								<label><?php echo app('translator')->get('message.store.postalcode'); ?></label>
								<input value="<?php if(isset($data['postalcode'])){ echo $data['postalcode'];}else{ ?><?php echo e(Input::old('postalcode')); ?> <?php }?>" class="form-control" name="postalcode" id="postalcode"  type="text">
								<?php if($errors->has('postalcode')): ?>
								<span class="help-block">
									<strong class="text-danger"><?php echo e($errors->first('postalcode')); ?></strong>
								</span>
								<?php endif; ?>
							</div>
							
							<div class="form-group">
								<label>Tax (%)</label>
								<input value="<?php if(isset($data['tax'])){ echo $data['tax'];}else{ ?><?php echo e(Input::old('tax')); ?> <?php }?>" class="form-control" name="tax" id="tax"  type="text">
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
                                <?php 
								
								 if(isset($data['image']) && $data['image']!='' && file_exists('public/uploads/store/'.$data['image'])){
									 
								 ?>
        						<p style="width: 100px !important;"><img src="<?php echo url('public/uploads/store/'.$data['image']);?>" width="100" height="80" id="merchant-demo-logo-preview"></p>
								 <?php }else{?>
								 <p style="width: 100px !important;"><img src="<?php echo e(url('public/uploads/userPic/default-image.png')); ?>" width="100" height="80" id="merchant-demo-logo-preview"></p>
								 <?php }?>
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

		  /* Select the text field */
		  copyText.select();
		  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

		  /* Copy the text inside the text field */
		  document.execCommand("copy");
	});
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
    
	//modal on show - generate express merchant qr code
    $('#storeMerchantQrCodeModal').on('show.bs.modal', function (e)
    {
        var endpoint = "/merchant/generate-store-merchant-qr-code";
        var userId = $(e.relatedTarget).attr('data-clientId');

        $('#user_id').val(userId);

        executeExpressMerchantQrCode(endpoint, userId);
    });

    //on click - update store merchant qr code
    $(document).on('click','.update-store-merchant-qr-code',function(e)
    {
        e.preventDefault();

        let endpoint = "/merchant/update-store-merchant-qr-code";
        var userId = $('#user_id').val();
        executeExpressMerchantQrCode(endpoint, userId);
    });

    //on click - print express merchant qr code
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
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/Merchant/mystore.blade.php ENDPATH**/ ?>