
<?php $__env->startSection('title', 'Edit Reload'); ?>
<?php $__env->startSection('page_content'); ?>

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $transaction->id)->where('transaction_type_id', '39')->first();
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$transaction->ip_address}/json"));
    
    if(!empty($transaction->local_tran_time)){
	    $trans_time = $transaction->local_tran_time;
	}else{
	    $trans_time = $transaction->created_at;
	}
	
	$user = DB::table('users')->where('id', $transaction->user_id)->first();
    $currency = DB::table('currencies')->where('id', $transaction->currency_id)->first();
?>

	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h4 class="text-left">Reload Details</h4>
								</div>
								<div class="col-md-3">
									<?php if($transaction->status): ?>
										<h4 class="text-left">Status : <?php if($transaction->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
				                    	<?php if($transaction->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
				            			<?php if($transaction->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?></h4>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<form class="form-horizontal">
									<div class="col-md-6">
									    <div class="panel panel-default">
											<div class="panel-body">
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Amount</label>
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($currency->code, formatNumber($transaction->amount))); ?></p>
													</div>
												</div>
							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="feesTotal">Fees</label>
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($currency->code, 0.00)); ?></p>
													</div>
												</div>
												<hr class="increase-hr-height">
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Total</label>
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($currency->code, formatNumber($transaction->amount))); ?></p>
													</div>
												</div>
											</div>
										</div>
										
										<div class="panel panel-default">
											<div class="panel-body">
											    
												<div class="form-group">
													<label class="control-label col-sm-4" for="user">User</label>
													<div class="col-sm-8">
													  <p class="form-control-static"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></p>
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-4" for="user">Card Number</label>
													<div class="col-sm-8">
													  <p class="form-control-static">XXXX XXXX XXXX <?php echo e($transaction->last_four); ?></p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-4" for="deposit_uuid">Transaction ID</label>
													<div class="col-sm-8">
													  <p class="form-control-static"><?php echo e($transaction->uuid); ?></p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-4" for="payment_method">Payment Method</label>
													<div class="col-sm-8">
													    <p class="form-control-static">
													        <?php if($transaction->payment_method_id == '1'): ?>
                                                                Ewallet
                                                            <?php else: ?>
                                                                N/A
                                                            <?php endif; ?>
													    </p>
													</div>
												</div>

												<div class="form-group">
    												<label class="control-label col-sm-4" for="created_at">Date</label>
    												<div class="col-sm-8">
    												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
    												</div>
    											</div>

						                   		<div class="form-group">
													<label class="control-label col-sm-4" for="status">Status</label>
													<div class="col-sm-8">
														<p class="form-control-static"><?php echo e($transaction->status); ?></p>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="col-md-6">
									    <?php if(!empty($ip_details)): ?>
        									<div class="panel panel-default">
        										<div class="panel-body">
        										    <h3 class="text-center">Location</h3>
        										    
        										    <div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">IP Address</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($ip_details->ip); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">City</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($ip_details->city); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Region</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($ip_details->region); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Country</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($ip_details->country); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Postal Code</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e(!empty($ip_details->postal) ? $ip_details->postal : '-'); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Timezone</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($ip_details->timezone); ?></p>
        												</div>
        											</div>
        										</div>
        									</div>
        								<?php endif; ?>
    									
    									<?php if(!empty($transaction_details)): ?>
        									<div class="panel panel-default">
        										<div class="panel-body">
        										    <h3 class="text-center">Device</h3>
        										    
        										    <div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Device Name</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($transaction_details->device_name); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Brand</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($transaction_details->device_manufacture); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Model</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($transaction_details->device_model); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">OS</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($transaction_details->device_os); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">OS Version</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($transaction_details->os_ver); ?></p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">App Version</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static"><?php echo e($transaction_details->app_ver); ?></p>
        												</div>
        											</div>
        										</div>
        									</div>
        								<?php endif; ?>
									</div>

									<div class="row">
										<div class="col-md-11">
											<div class="col-md-2"></div>
											<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/card/reloads')); ?>">Cancel</a></div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/cards/reloaddetails.blade.php ENDPATH**/ ?>