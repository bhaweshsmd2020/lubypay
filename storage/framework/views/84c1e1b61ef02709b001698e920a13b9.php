

<?php $__env->startSection('title', 'Edit Store Order'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href="<?php echo e(url("admin/store/edit/" . $store_detail->id)); ?>">Details</a>
                </li>

                <li>
                  <a href="<?php echo e(url("admin/store/product/list/" . $store_detail->id)); ?>">Products</a>
                </li>
                
                <li>
                  <a href="<?php echo e(url("admin/store/category/list/" . $store_detail->id)); ?>">Categories</a>
                </li>
                
                <li class="active">
                  <a href="<?php echo e(url("admin/store/orders/list/" . $store_detail->id)); ?>">Orders</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left"><?php echo e($store_detail->name); ?> Orders</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
					    <div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h4 class="text-left">Order Details</h4>
								</div>
								<div class="col-md-3">
								    <h4 class="text-left">Status :
    									<?php if($order->status == 'success'): ?>
                                            <span class="label label-success">Success</span>
                                        <?php elseif($order->status == 'cancel'): ?>
                                            <span class="label label-warning">Cancelled</span>
                                        <?php elseif($order->status == 'pending'): ?>
                                            <span class="label label-primary">Pending</span>
                                        <?php elseif($order->status == 'failed'): ?>
                                            <span class="label label-danger">Failed</span>
                                        <?php endif; ?>
                                    </h4>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

						                    <div class="form-group">
												<label class="control-label col-sm-6" for="amount">Amount</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static"><?php echo e($currency->symbol); ?><?php echo e($order->subtotal); ?></p>
												</div>
											</div>
											
											<?php if(!empty($order->nfc_fee)): ?>
    						                    <div class="form-group">
    												<label class="control-label col-sm-6" for="amount">Card Fee</label>
    												<input type="hidden" class="form-control">
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e($currency->symbol); ?><?php echo e($order->nfc_fee); ?></p>
    												</div>
    											</div>
    										<?php endif; ?>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Tax</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static"><?php echo e($currency->symbol); ?><?php echo e($order->tax); ?></p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Discount</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static"><?php echo e($currency->symbol); ?><?php echo e($order->discount); ?></p>
												</div>
											</div>

											<hr class="increase-hr-height">

											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Total Amount</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static"><?php echo e($currency->symbol); ?><?php echo e($order->total_amount); ?></p>
												</div>
											</div>
											
											<?php if(!empty($order->paid_amount)): ?>
    											<div class="form-group">
    												<label class="control-label col-sm-6" for="amount">Paid Amount</label>
    												<input type="hidden" class="form-control">
    												<div class="col-sm-6">
    												  <p class="form-control-static"><?php echo e(!empty($paid_currency->symbol) ? $paid_currency->symbol : $currency->symbol); ?><?php echo e($order->paid_amount); ?></p>
    												</div>
    											</div>
    										<?php endif; ?>

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

						                    <div class="form-group">
												<label class="control-label col-sm-6" for="amount">Order ID</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">#<?php echo e($order->unique_id); ?></p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Payment Method</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												    <p class="form-control-static">
												        <?php if($order->payment_method_id == '1'): ?>
                                                            QR Payment
                                                        <?php elseif($order->payment_method_id == '2'): ?>
                                                            Card Payment
                                                        <?php endif; ?>
												    </p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Payment Status</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												    <p class="form-control-static">
												        <?php if($order->payment_response == 'success'): ?>
                                                            <span class="label label-success">Success</span>
                                                        <?php elseif($order->payment_response == 'cancel'): ?>
                                                            <span class="label label-warning">Cancelled</span>
                                                        <?php elseif($order->payment_response == 'failed'): ?>
                                                            <span class="label label-danger">Failed</span>
                                                        <?php else: ?>
                                                            <span class="label label-primary">Pending</span>
                                                        <?php endif; ?>
												    </p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Order Status</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												    <p class="form-control-static">
												        <?php if($order->status == 'success'): ?>
                                                            <span class="label label-success">Success</span>
                                                        <?php elseif($order->status == 'cancel'): ?>
                                                            <span class="label label-warning">Cancelled</span>
                                                        <?php elseif($order->status == 'pending'): ?>
                                                            <span class="label label-primary">Pending</span>
                                                        <?php elseif($order->status == 'failed'): ?>
                                                            <span class="label label-danger">Failed</span>
                                                        <?php endif; ?>
												    </p>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Date</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($order->created_at)->format('d-M-Y h:i A')); ?></p>
												</div>
											</div>

										</div>
									</div>
								</div>

								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">
										    
										    <h3 class="text-center">Products</h3>
        										    
										    <div class="form-group" style="margin-bottom: 0px;">
										        <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Product</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
    								                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    						                                <?php $__currentLoopData = $order_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$ordpr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    											                <?php if($ordpr->product_id == $product->id): ?>
    									                            <tr>
                                                                        <th scope="row"><?php echo e(++$k); ?></th>
                                                                        <td>
                                                                            <?php if(!empty($product->image)): ?>
                                                                                <img src="<?php echo e(url('public/user_dashboard/product/thumb/' . $product->image)); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                                                            <?php else: ?>
                                                                                <img src="<?php echo e(url('public/user_dashboard/profile/user.png')); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td><?php echo e($product->name); ?></td>
                                                                        <td><?php echo e($ordpr->qty); ?></td>
                                                                    </tr>
    											                <?php endif; ?>
    											            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    											        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
											</div>
										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">
										    
										    <h3 class="text-center">Customer Details</h3>
        										    
										    <div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Name</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												  <p class="form-control-static"><?php echo e($order->customer_name); ?></p>
												</div>
											</div>
											
											<div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Email</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												  <p class="form-control-static"><?php echo e($order->customer_email); ?></p>
												</div>
											</div>
											
											<div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Phone</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												  <p class="form-control-static"><?php echo e($order->customer_phone_prefix); ?><?php echo e($order->customer_phone); ?></p>
												</div>
											</div>
											
											<div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Address</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												    <p class="form-control-static">
												        <?php if(!empty($order->customer_address1)): ?>
												            <?php echo e($order->customer_address1); ?><?php echo e($order->customer_address2); ?>, 
												        <?php endif; ?>
												        <?php if(!empty($order->customer_city)): ?>
												            <?php echo e($order->customer_city); ?>,
												        <?php endif; ?>
												        <?php if(!empty($order->customer_state)): ?>
												            <?php echo e($order->customer_state); ?>,
												        <?php endif; ?>
												        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												            <?php if($order->customer_country == $country->id): ?>
												                <?php echo e($country->name); ?>,
												            <?php endif; ?>
												        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												        <?php if(!empty($order->customer_zipcode)): ?>
												            <?php echo e($order->customer_zipcode); ?>

												        <?php endif; ?>
												    </p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 text-center">
									<a class="btn btn-danger" href="<?php echo e(url("admin/store/orders/list/" . $store_detail->id)); ?>">Back</a>
									<a class="btn btn-primary" href="<?php echo e(url("admin/store/orders/invoice/".$store_detail->id.'/'.$order->id)); ?>">Print</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')); ?>" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="<?php echo e(asset('public/dist/js/isValidPhoneNumber.js')); ?>" type="text/javascript"></script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/stores/orders/edit.blade.php ENDPATH**/ ?>