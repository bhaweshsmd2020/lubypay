
<?php $__env->startSection('title', 'Edit Currency Exchange'); ?>
<?php $__env->startSection('page_content'); ?>

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $exchange->id)->where('transaction_type_id', '6')->first();
    if(!empty($check_transaction)){
        $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    }else{
        $check_transaction1 = DB::table('transactions')->where('transaction_reference_id', $exchange->id)->where('transaction_type_id', '5')->first();
        $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction1->id)->first();
    }
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$exchange->ip_address}/json"));
    
    if(!empty($exchange->local_tran_time)){
	    $trans_time = $exchange->local_tran_time;
	}else{
	    $trans_time = $exchange->created_at;
	}
?>

<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-9">
								<h4 class="text-left">Exchange Details</h4>
							</div>
							<div class="col-md-3">
								<?php if($exchange->status): ?>
									<h4 class="text-left">Status : <?php if($exchange->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
					                   <?php if($exchange->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?>
	                        		</h4>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<form action="<?php echo e(url('admin/exchange/update')); ?>" class="form-horizontal" id="exchange_form" method="POST">
								<?php echo e(csrf_field()); ?>

						        <input type="hidden" value="<?php echo e($exchange->id); ?>" name="id" id="id">
						        <input type="hidden" value="<?php echo e($exchange->uuid); ?>" name="uuid" id="uuid">
						        <input type="hidden" value="<?php echo e($exchange->type); ?>" name="type" id="type">
						        <input type="hidden" value="<?php echo e($exchange->user_id); ?>" name="user_id" id="user_id">
						        <input type="hidden" value="<?php echo e($exchange->currency->id); ?>" name="currency_id" id="currency_id">

						        <input type="hidden" value="<?php echo e($transaction->transaction_type_id); ?>" name="transaction_type_id" id="transaction_type_id">
								<input type="hidden" value="<?php echo e($transaction->transaction_type->name); ?>" name="transaction_type" id="transaction_type">
								<input type="hidden" value="<?php echo e($transaction->status); ?>" name="transaction_status" id="transaction_status">
								<input type="hidden" value="<?php echo e($transaction->transaction_reference_id); ?>" name="transaction_reference_id" id="transaction_reference_id">
						        <input type="hidden" value="<?php echo e($transaction->uuid); ?>" name="transaction_uuid" id="transaction_uuid">

								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

											<?php if($exchange->amount): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Amount</label>
													<input type="hidden" class="form-control" name="amount" value="<?php echo e(($exchange->amount)); ?>">
													<div class="col-sm-6">
													    <?php 
													          
													          $transaction=App\Models\Transaction::where('transaction_reference_id',$exchange->id)->where('transaction_type_id',5)->first()??'';
													    ?>
													  <p class="form-control-static"><?php echo e(moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal))); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if(isset($exchange->fee)): ?>
							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="fee">Fees
														<span>
															<small class="transactions-edit-fee">
																<?php if(isset($transaction) && $transaction->transaction_type_id == Exchange_From): ?>
																	(<?php echo e((formatNumber($transaction->percentage))); ?>% + <?php echo e(formatNumber($transaction->charge_fixed)); ?>)
																<?php else: ?>
																	(<?php echo e(0); ?>%+<?php echo e(0); ?>)
																<?php endif; ?>
															</small>
														</span>
													</label>
													<input type="hidden" class="form-control" name="fee" value="<?php echo e(($exchange->fee)); ?>">

													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($transaction->currency->symbol, formatNumber($exchange->fee))); ?></p>
													</div>
												</div>
											<?php endif; ?>
											<hr class="increase-hr-height">

											<?php
												$total = $exchange->amount;
											?>

											<?php if(isset($total)): ?>
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Total</label>
													<input type="hidden" class="form-control" name="total" value="<?php echo e(($total)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($transaction->currency->symbol, formatNumber($total))); ?></p>
													</div>
												</div>
											<?php endif; ?>

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

						                    <?php if($exchange->user_id): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="user">User</label>
													<input type="hidden" class="form-control" name="user" value="<?php echo e(isset($exchange->user) ? $exchange->user->first_name.' '.$exchange->user->last_name :"-"); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($exchange->user) ? $exchange->user->first_name.' '.$exchange->user->last_name :"-"); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($exchange->uuid): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="exchange_uuid">Transaction ID</label>
													<input type="hidden" class="form-control" name="exchange_uuid" value="<?php echo e($exchange->uuid); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($exchange->uuid); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($exchange->from_wallet): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="from_wallet">Exchange From</label>
													<input type="hidden" class="form-control" name="from_wallet" value="<?php echo e($exchange->fromWallet->currency->code); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($exchange->fromWallet->currency->code); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($exchange->to_wallet): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="to_wallet">Exchange To</label>
													<input type="hidden" class="form-control" name="to_wallet" value="<?php echo e($exchange->toWallet->currency->code); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($exchange->toWallet->currency->code); ?></p>
													</div>
												</div>
											<?php endif; ?>
											
											<?php
											    $transaction = DB::table('transactions')->where('transaction_reference_id',$exchange->id)->where('transaction_type_id',6)->first();
											?>
											
											    <div class="form-group">
													<label class="control-label col-sm-3" for="to_wallet">Received Amount</label>
													<input type="hidden" class="form-control" name="to_wallet" value="<?php echo e($exchange->toWallet->currency->code); ?>">
													<div class="col-sm-9">
													    <p class="form-control-static">
													        <?php if(!empty($transaction->subtotal)): ?>
                                                                <?php echo e($transaction->subtotal); ?>

                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
													    </p>
													</div>
												</div>

											<?php if($exchange->exchange_rate): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="exchange_rate">Exchange Rate</label>
													<input type="hidden" class="form-control" name="exchange_rate" value="<?php echo e(($exchange->exchange_rate)); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(moneyFormat($exchange->currency->symbol, (float)($exchange->exchange_rate))); ?></p>
													</div>
												</div>
											<?php endif; ?>
											
											<div class="form-group">
												<label class="control-label col-sm-3" for="created_at">Date</label>
												<input type="hidden" class="form-control" name="created_at" value="<?php echo e($exchange->created_at); ?>">
												<div class="col-sm-9">
												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
												</div>
											</div>

					                   		<?php if($exchange->status): ?>
						                   		<div class="form-group">
													<label class="control-label col-sm-3" for="status">Change Status</label>
													<div class="col-sm-9">
														<select class="form-control select2" name="status" style="width: 50%;">
									                        <option value="Success" <?php echo e(isset($exchange->status) && $exchange->status ==  'Success'? 'selected':""); ?>>Success</option>
									                        <option value="Blocked"  <?php echo e(isset($exchange->status) && $exchange->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
													  	</select>
													</div>
												</div>
											<?php endif; ?>

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
										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/exchanges')); ?>">Cancel</a></div>
										<div class="col-md-1">
											<button type="submit" class="btn button-secondary pull-right" id="exchange_edit">
				                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="exchange_edit_text">Update</span>
				                            </button>
										</div>
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

<?php $__env->startPush('extra_body_scripts'); ?>
<script type="text/javascript">

	$(".select2").select2({});

	// disabling submit and cancel button after clicking it
	$(document).ready(function() {
		$('form').submit(function() {

			$("#exchange_edit").attr("disabled", true);
			$('#cancel_anchor').attr("disabled","disabled");
			$(".fa-spin").show();
			$("#exchange_edit_text").text('Updating...');

			// Click False
			$('#exchange_edit').click(false);
			$('#cancel_anchor').click(false);
		});
	});
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/exchange/edit.blade.php ENDPATH**/ ?>