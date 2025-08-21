
<?php $__env->startSection('title', 'Edit Transfer'); ?>
<?php $__env->startSection('page_content'); ?>

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $transfer->id)->where('transaction_type_id', '4')->first();
    if(!empty($check_transaction)){
        $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    }else{
        $check_transaction1 = DB::table('transactions')->where('transaction_reference_id', $transfer->id)->where('transaction_type_id', '3')->first();
        $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction1->id)->first();
    }
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$transfer->ip_address}/json"));
    
    if(!empty($transfer->local_tran_time)){
	    $trans_time = $transfer->local_tran_time;
	}else{
	    $trans_time = $transfer->created_at;
	}
?>

 <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">
                    Transfer Details
                  </div>
                </div>
                <div class="col-md-3 pull-right" style="margin-top:8px;">
                        <a href="<?php echo e(url('admin/transfers')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-chevron-left"> &nbsp;</span>Back to transfers list</a>
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
								<h4 class="text-left">Transfer Details</h4>
							</div>
							<div class="col-md-3">
								<?php if($transfer->status): ?>
									<h4 class="text-left">Status : <?php if($transfer->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
                        			<?php if($transfer->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
                        			<?php if($transfer->status == 'Refund'): ?><span class="text-orange">Refunded</span><?php endif; ?>
                        			<?php if($transfer->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?></h4>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<form action="<?php echo e(url('admin/transfers/update')); ?>" class="form-horizontal" id="transfers_form" method="POST">
								<?php echo e(csrf_field()); ?>


						        <input type="hidden" value="<?php echo e($transfer->id); ?>" name="id" id="id">
						        <input type="hidden" value="<?php echo e($transfer->uuid); ?>" name="uuid" id="uuid">
						        <input type="hidden" value="<?php echo e($transfer->sender_id); ?>" name="sender_id" id="sender_id">
						        <input type="hidden" value="<?php echo e($transfer->receiver_id); ?>" name="receiver_id" id="receiver_id">
						        <input type="hidden" value="<?php echo e($transfer->currency->id); ?>" name="currency_id" id="currency_id">
						        <input type="hidden" value="<?php echo e($transfer->note); ?>" name="note" id="note">
						        <input type="hidden" value="<?php echo e($transfer->email); ?>" name="email" id="email">
						        <input type="hidden" value="<?php echo e($transfer->phone); ?>" name="phone" id="phone">

								<input type="hidden" value="<?php echo e($transaction->transaction_type_id); ?>" name="transaction_type_id" id="transaction_type_id">
								<input type="hidden" value="<?php echo e($transaction->transaction_type->name); ?>" name="transaction_type" id="transaction_type">
								<input type="hidden" value="<?php echo e($transaction->status); ?>" name="transaction_status" id="transaction_status">
								<input type="hidden" value="<?php echo e($transaction->transaction_reference_id); ?>" name="transaction_reference_id" id="transaction_reference_id">

								<input type="hidden" value="<?php echo e($transaction->percentage); ?>" name="percentage" id="percentage">
								<input type="hidden" value="<?php echo e($transaction->charge_percentage); ?>" name="charge_percentage" id="charge_percentage">
								<input type="hidden" value="<?php echo e($transaction->charge_fixed); ?>" name="charge_fixed" id="charge_fixed">


								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

											<?php if($transfer->amount): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Amount</label>
													<input type="hidden" class="form-control" name="amount" value="<?php echo e(($transfer->amount)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($transfer->currency->symbol, formatNumber($transfer->amount))); ?></p>
													</div>
												</div>
											<?php endif; ?>

						                    <div class="form-group total-deposit-feesTotal-space">
												<label class="control-label col-sm-6" for="feesTotal">Fees
													<span>
														<small class="transactions-edit-fee">
															<?php if(isset($transaction)): ?>
																(<?php echo e((formatNumber($transaction->percentage))); ?>% + <?php echo e(formatNumber($transaction->charge_fixed)); ?>)
															<?php else: ?>
																(<?php echo e(0); ?>%+<?php echo e(0); ?>)
															<?php endif; ?>
														</small>
													</span>
												</label>
												<input type="hidden" class="form-control" name="feesTotal" value="<?php echo e(($transfer->fee)); ?>">

												<div class="col-sm-6">
												  <p class="form-control-static"><?php echo e(moneyFormat($transfer->currency->symbol, formatNumber($transfer->fee))); ?></p>
												</div>
											</div>
											<hr class="increase-hr-height">

											<?php
												$total = $transfer->fee + $transfer->amount;
											?>

											<?php if(isset($total)): ?>
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Total</label>
													<input type="hidden" class="form-control" name="total" value="<?php echo e(($total)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($transfer->currency->symbol, formatNumber($total))); ?></p>
													</div>
												</div>
											<?php endif; ?>

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

						                    
												<div class="form-group">

													<?php if(!empty($transfer->bank)): ?>
														<label class="control-label col-sm-3" for="sender">Transferred By</label>
													<?php else: ?>
														<label class="control-label col-sm-3" for="sender">Paid By</label>
													<?php endif; ?>

													<input type="hidden" class="form-control" name="sender" value="<?php echo e(isset($transfer->sender) ? $transfer->sender->first_name.' '.$transfer->sender->last_name :"-"); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($transfer->sender) ? $transfer->sender->first_name.' '.$transfer->sender->last_name :"-"); ?></p>
													</div>
												</div>
											

											<div class="form-group">
												<label class="control-label col-sm-3" for="receiver">Paid To</label>
												<?php if($transfer->receiver): ?>
													<input type="hidden" class="form-control" name="receiver" value="<?php echo e(isset($transfer->receiver) ? $transfer->receiver->first_name.' '.$transfer->receiver->last_name :"-"); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($transfer->receiver) ? $transfer->receiver->first_name.' '.$transfer->receiver->last_name :"-"); ?></p>
													</div>
												<?php else: ?>
													<input type="hidden" class="form-control" name="receiver" value="<?php echo e(isset($transfer->email) ? $transfer->email : $transfer->phone); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($transfer->email) ? $transfer->email : $transfer->phone); ?></p>
													</div>
												<?php endif; ?>
											</div>

											<?php if($transfer->uuid): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="transfer_uuid">Transaction ID</label>
													<input type="hidden" class="form-control" name="transfer_uuid" value="<?php echo e($transfer->uuid); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transfer->uuid); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transfer->currency): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="currency">Currency</label>
													<input type="hidden" class="form-control" name="currency" value="<?php echo e($transfer->currency->code); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transfer->currency->code); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transfer->bank): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="bank_name">Bank Name</label>
													<input type="hidden" class="form-control" name="bank_name" value="<?php echo e($transfer->bank->bank_name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transfer->bank->bank_name); ?></p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-3" for="bank_branch_name">Branch Name</label>
													<input type="hidden" class="form-control" name="bank_branch_name" value="<?php echo e($transfer->bank->bank_branch_name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transfer->bank->bank_branch_name); ?></p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-3" for="account_name">Account Name</label>
													<input type="hidden" class="form-control" name="account_name" value="<?php echo e($transfer->bank->account_name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($transfer->bank->account_name); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($transfer->file): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="attached_file">Attached File</label>
													<div class="col-sm-9">
													  <p class="form-control-static">
										                  <a href="<?php echo e(url('public/uploads/files/bank_attached_files/transfers').'/'.$transfer->file->filename); ?>"><i class="fa fa-fw fa-download"></i>
										                  	<?php echo e($transfer->file->originalname); ?>

										                  </a>
													  </p>
													</div>
												</div>
											<?php endif; ?>
											
											<div class="form-group">
												<label class="control-label col-sm-3" for="created_at">Date</label>
												<input type="hidden" class="form-control" name="created_at" value="<?php echo e($transfer->created_at); ?>">
												<div class="col-sm-9">
												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
												</div>
											</div>

					                   		<?php if($transfer->status): ?>
						                   		<div class="form-group">
													<label class="control-label col-sm-3" for="status">Change Status</label>
													<div class="col-sm-9">

														<?php if(isset($transactionOfRefunded) && isset($transferOfRefunded)): ?>

								                          <p class="form-control-static"><span class="label label-success">Already Refunded</span></p>

								                          <p class="form-control-static"><span class="label label-danger">Refund Reference: <i>
										                          	<a id="transferOfRefunded" href="<?php echo e(url("admin/transfers/edit/$transferOfRefunded->id")); ?>">( <?php echo e($transactionOfRefunded->refund_reference); ?> )</a>
										                          </i>
										                      </span>
										                  </p>

								                        <?php else: ?>
									                        <select class="form-control select2" name="status" style="width: 60%;">

									                        	<?php if($transfer->status == 'Success'): ?>

										                        	<?php if(!empty($transfer->bank)): ?>
																		<option value="Success" <?php echo e($transfer->status ==  'Success'? 'selected':""); ?>>Success</option>
										                            	<option value="Pending"  <?php echo e($transfer->status == 'Pending' ? 'selected':""); ?>>Pending</option>
										                            	<option value="Blocked"  <?php echo e($transfer->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
																	<?php else: ?>
																		<option value="Success" <?php echo e($transfer->status ==  'Success'? 'selected':""); ?>>Success</option>
										                            	<option value="Pending"  <?php echo e($transfer->status == 'Pending' ? 'selected':""); ?>>Pending</option>
										                            	<option value="Refund" <?php echo e($transfer->status ==  'Refund' ? 'selected':""); ?>>Refund</option>
										                            	<option value="Blocked"  <?php echo e($transfer->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
																	<?php endif; ?>

									                        	<?php else: ?>
									                        		<option value="Success" <?php echo e($transfer->status ==  'Success'? 'selected':""); ?>>Success</option>
									                            	<option value="Pending"  <?php echo e($transfer->status == 'Pending' ? 'selected':""); ?>>Pending</option>
									                            	<option value="Blocked"  <?php echo e($transfer->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
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
										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/transfers')); ?>">Cancel</a></div>

										<?php if(!isset($transactionOfRefunded->refund_reference)): ?>
											<div class="col-md-1">
												<button type="submit" class="btn button-secondary pull-right" id="transfers_edit">
					                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="transfers_edit_text">Update</span>
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
	     	$("#transfers_edit").attr("disabled", true);
	     	$('#cancel_anchor').attr("disabled","disabled");
            $(".fa-spin").show();
            $("#transfers_edit_text").text('Updating...');

            // Click False
			$('#transfers_edit').click(false);
			$('#cancel_anchor').click(false);
	  });

	  $('#transferOfRefunded').css('color', 'white');
	});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/transfers/edit.blade.php ENDPATH**/ ?>