
<?php $__env->startSection('title', 'Edit Request Payment'); ?>
<?php $__env->startSection('page_content'); ?>

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $request_payments->id)->where('transaction_type_id', '10')->first();
    if(!empty($check_transaction)){
        $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    }else{
        $check_transaction1 = DB::table('transactions')->where('transaction_reference_id', $request_payments->id)->where('transaction_type_id', '9')->first();
        $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction1->id)->first();
    }
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$request_payments->ip_address}/json"));
    
    if(!empty($request_payments->local_tran_time)){
	    $trans_time = $request_payments->local_tran_time;
	}else{
	    $trans_time = $request_payments->created_at;
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
								<h4 class="text-left">Request Payment Details</h4>
							</div>
							<div class="col-md-3">
								<?php if($request_payments->status): ?>
									<h4 class="text-left">Status : <?php if($request_payments->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        	<?php if($request_payments->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
                        			<?php if($request_payments->status == 'Refund'): ?><span class="text-warning">Refunded</span><?php endif; ?>
                        			<?php if($request_payments->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?></h4>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<form action="<?php echo e(url('admin/request_payments/update')); ?>" class="form-horizontal" method="POST">
								<?php echo e(csrf_field()); ?>


						        <input type="hidden" value="<?php echo e($request_payments->id); ?>" name="id" id="id">
						        <input type="hidden" value="<?php echo e($request_payments->uuid); ?>" name="uuid" id="uuid">
						        <input type="hidden" value="<?php echo e($request_payments->user_id); ?>" name="user_id" id="user_id">
						        <input type="hidden" value="<?php echo e($request_payments->currency->id); ?>" name="currency_id" id="currency_id">
						        <input type="hidden" value="<?php echo e($request_payments->note); ?>" name="note" id="note">

								<?php if(isset($transaction)): ?>
									<input type="hidden" value="<?php echo e($transaction->transaction_type_id); ?>" name="transaction_type_id" id="transaction_type_id">
									<input type="hidden" value="<?php echo e($transaction->transaction_type->name); ?>" name="transaction_type" id="transaction_type">
									<input type="hidden" value="<?php echo e($transaction->status); ?>" name="transaction_status" id="transaction_status">
									<input type="hidden" value="<?php echo e($transaction->transaction_reference_id); ?>" name="transaction_reference_id" id="transaction_reference_id">

									<input type="hidden" value="<?php echo e($transaction->user_type); ?>" name="user_type" id="user_type">

									<input type="hidden" value="<?php echo e(($transaction->percentage)); ?>" name="percentage" id="percentage">
									<input type="hidden" value="<?php echo e(($transaction->charge_percentage)); ?>" name="charge_percentage" id="charge_percentage">
						        	<input type="hidden" value="<?php echo e(($transaction->charge_fixed)); ?>" name="charge_fixed" id="charge_fixed">
								
								<?php endif; ?>


								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

											<?php if($request_payments->amount): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Requested Amount</label>
													<input type="hidden" class="form-control" name="amount" value="<?php echo e(($request_payments->amount)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static pull-left"><?php echo e(moneyFormat($request_payments->currency->symbol, formatNumber($request_payments->amount))); ?></p>
													</div>
												</div>
											<?php endif; ?>

							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="accept_amount">Accepted Amount</label>
													<input type="hidden" class="form-control" name="accept_amount" value="<?php echo e(($request_payments->accept_amount)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static pull-left"><?php echo e(moneyFormat($request_payments->currency->symbol, formatNumber($request_payments->accept_amount))); ?></p>
													</div>
												</div>

							                    <div class="form-group total-deposit-feesTotal-space-request-payment">
													<label class="control-label col-sm-6" for="fee">Fees
														<span>
															<small class="transactions-edit-fee">
																<?php if(isset($transaction) && $transaction->transaction_type_id == Request_To): ?>
																	(<?php echo e((formatNumber($transaction->percentage))); ?>% + <?php echo e(formatNumber($transaction->charge_fixed)); ?>)
																<?php else: ?>
																	(<?php echo e(0); ?>%+<?php echo e(0); ?>)
																<?php endif; ?>
															</small>
														</span>
													</label>
													<input type="hidden" class="form-control" name="fee" value="<?php echo e(isset($transaction) ? ($transaction->charge_percentage + $transaction->charge_fixed) :"0"); ?>">

													<div class="col-sm-6">
													  <p class="form-control-static pull-left"><?php echo e(isset($transaction) ? moneyFormat($request_payments->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) :  moneyFormat($request_payments->currency->symbol, formatNumber(0.00))); ?></p>
													</div>
												</div>

												<hr class="increase-hr-height-request-payment">
												<?php
													if (isset($transaction))
													{
														$total = $transaction->charge_percentage + $transaction->charge_fixed + $request_payments->accept_amount ;
													}
													else
													{
														$total = $request_payments->amount;
													}
												?>

							                    <div class="form-group total-deposit-space-request-payment">
													<label class="control-label col-sm-6" for="total">Total</label>
													<input type="hidden" class="form-control" name="total" value="<?php echo e(($total)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($request_payments->currency->symbol, formatNumber($total))); ?></p>
													</div>
												</div>

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

						                    <?php if($request_payments->user): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="user">Request From</label>
													<input type="hidden" class="form-control" name="user" value="<?php echo e(isset($request_payments->user) ? $request_payments->user->first_name.' '.$request_payments->user->last_name :"-"); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($request_payments->user) ? $request_payments->user->first_name.' '.$request_payments->user->last_name :"-"); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($request_payments->receiver): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="receiver">Request To</label>
													<input type="hidden" class="form-control" name="receiver" value="<?php echo e(isset($request_payments->receiver) ? $request_payments->receiver->first_name.' '.$request_payments->receiver->last_name :"-"); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($request_payments->receiver) ? $request_payments->receiver->first_name.' '.$request_payments->receiver->last_name :"-"); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($request_payments->uuid): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="request_payments_uuid">Transaction ID</label>
													<input type="hidden" class="form-control" name="request_payments_uuid" value="<?php echo e($request_payments->uuid); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($request_payments->uuid); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($request_payments->email): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="request_payments_email">Email</label>
													<input type="hidden" class="form-control" name="request_payments_email" value="<?php echo e($request_payments->email); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($request_payments->email); ?></p>
													</div>
												</div>
											<?php endif; ?>


											<?php if($request_payments->currency): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="currency">Currency</label>
													<input type="hidden" class="form-control" name="currency" value="<?php echo e($request_payments->currency->code); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($request_payments->currency->code); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<div class="form-group">
												<label class="control-label col-sm-3" for="created_at">Date</label>
												<input type="hidden" class="form-control" name="created_at" value="<?php echo e($request_payments->created_at); ?>">
												<div class="col-sm-9">
												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
												</div>
											</div>

						               		<?php if($request_payments->status): ?>
						                   		<div class="form-group">
													<label class="control-label col-sm-3" for="status">Change Status</label>
													<div class="col-sm-9">

														<?php if(isset($transactionOfRefunded) && isset($requestPaymentsOfRefunded)): ?>
								                          <p class="form-control-static"><span class="label label-success">Already Refunded</span></p>

								                          <p class="form-control-static"><span class="label label-danger">Refund Reference: <i>
										                          	<a id="requestPaymentsOfRefunded" href="<?php echo e(url("admin/request_payments/edit/$requestPaymentsOfRefunded->id")); ?>">( <?php echo e($transactionOfRefunded->refund_reference); ?> )</a>
										                          </i>
										                      </span>
										                  </p>
								                        <?php else: ?>
									                        <select class="form-control select2" name="status" style="width: 60%;">

																<?php if(isset($transaction->status) && $transaction->status == 'Success'): ?>
																	<option value="Success" <?php echo e(isset($request_payments->status) && $request_payments->status ==  'Success'? 'selected':""); ?>>Success</option>
																	<option value="Refund"  <?php echo e(isset($request_payments->status) && $request_payments->status == 'Refund' ? 'selected':""); ?>>Refund</option>

																<?php elseif($request_payments->status == 'Pending'): ?>
										                        	<option value="Pending" <?php echo e(isset($request_payments->status) && $request_payments->status ==  'Pending'? 'selected':""); ?>>Pending</option>
																	<option value="Blocked"  <?php echo e(isset($request_payments->status) && $request_payments->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>

																<?php elseif($request_payments->status == 'Blocked'): ?>
										                        	<option value="Pending" <?php echo e(isset($request_payments->status) && $request_payments->status ==  'Pending'? 'selected':""); ?>>Pending</option>
																	<option value="Blocked"  <?php echo e(isset($request_payments->status) && $request_payments->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
							                        			<?php endif; ?>
															</select>
								                        <?php endif; ?>
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
										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/request_payments')); ?>">Cancel</a></div>

										<?php if(!isset($transactionOfRefunded->refund_reference)): ?>
											<div class="col-md-1">
												<button type="submit" class="btn button-secondary pull-right" id="request_payment">
							                        <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="request_payment_text">Update</span>
							                    </button>
											</div>
										<?php endif; ?>
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
			$("#request_payment").attr("disabled", true);
			$('#cancel_anchor').attr("disabled","disabled");
			$(".fa-spin").show();
			$("#request_payment_text").text('Updating...');

			// Click False
			$('#request_payment').click(false);
			$('#cancel_anchor').click(false);
		});

		$('#requestPaymentsOfRefunded').css('color', 'white');
	});
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/RequestPayment/edit.blade.php ENDPATH**/ ?>