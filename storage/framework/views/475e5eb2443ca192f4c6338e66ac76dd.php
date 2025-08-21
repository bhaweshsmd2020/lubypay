
<?php $__env->startSection('title', 'Edit Deposit'); ?>
<?php $__env->startSection('page_content'); ?>

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $deposit->id)->where('payment_method_id', '2')->first();
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$deposit->ip_address}/json"));
    
    if(!empty($deposit->local_tran_time)){
	    $trans_time = $deposit->local_tran_time;
	}else{
	    $trans_time = $deposit->created_at;
	}
?>
     <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">
                    Deposit Details
                  </div>
                </div>
                <div class="col-md-3 pull-right" style="margin-top:8px;">
                        <a href="<?php echo e(url('admin/deposits')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-chevron-left"> &nbsp;</span>Back to deposits list</a>
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
									<h4 class="text-left">Deposit Details</h4>
								</div>
								<div class="col-md-3">
									<?php if($deposit->status): ?>
										<h4 class="text-left">Status : <?php if($deposit->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
				                    	<?php if($deposit->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
				            			<?php if($deposit->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?></h4>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<form action="<?php echo e(url('admin/deposits/update')); ?>" class="form-horizontal" id="deposit_form" method="POST">
										<?php echo e(csrf_field()); ?>

							        <input type="hidden" value="<?php echo e($deposit->id); ?>" name="id" id="id">
							        <input type="hidden" value="<?php echo e($deposit->user_id); ?>" name="user_id" id="user_id">
							        <input type="hidden" value="<?php echo e($deposit->currency->id); ?>" name="currency_id" id="currency_id">
							        <input type="hidden" value="<?php echo e($deposit->uuid); ?>" name="uuid" id="uuid">
							        <input type="hidden" value="<?php echo e(($deposit->charge_percentage)); ?>" name="charge_percentage" id="charge_percentage">
							        <input type="hidden" value="<?php echo e(($deposit->charge_fixed)); ?>" name="charge_fixed" id="charge_fixed">

							        <input type="hidden" value="<?php echo e($transaction->transaction_type_id??''); ?>" name="transaction_type_id" id="transaction_type_id">
							        <input type="hidden" value="<?php echo e($transaction->transaction_type->name??''); ?>" name="transaction_type" id="transaction_type">
							        <input type="hidden" value="<?php echo e($transaction->status??''); ?>" name="transaction_status" id="transaction_status">
							        <input type="hidden" value="<?php echo e($transaction->transaction_reference_id??''); ?>" name="transaction_reference_id" id="transaction_reference_id">

									<div class="col-md-6">
									    <div class="panel panel-default">
											<div class="panel-body">

												<?php if($deposit->amount): ?>
								                    <div class="form-group">
														<label class="control-label col-sm-6" for="amount">Amount</label>
														<input type="hidden" class="form-control" name="amount" value="<?php echo e(($deposit->amount)); ?>">
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e(moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount))); ?></p>
														</div>
													</div>
												<?php endif; ?>

							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="feesTotal">Fees
														<span>
															<small class="transactions-edit-fee">
																<?php if(isset($transaction)): ?>
																(<?php echo e((formatNumber($transaction->percentage??''))); ?>% + <?php echo e(formatNumber($deposit->charge_fixed)); ?>)
																<?php else: ?>
																	(<?php echo e(0); ?>%+<?php echo e(0); ?>)
																<?php endif; ?>
															</small>
														</span>
													</label>

													<?php
														$feesTotal = $deposit->charge_percentage + $deposit->charge_fixed;
													?>

													<input type="hidden" class="form-control" name="feesTotal" value="<?php echo e(($feesTotal)); ?>">

													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($deposit->currency->symbol, formatNumber($feesTotal))); ?></p>
													</div>
												</div>

												<hr class="increase-hr-height">

												<?php
													$total = $feesTotal + $deposit->amount;
												?>

												<?php if(isset($total)): ?>
								                    <div class="form-group total-deposit-space">
														<label class="control-label col-sm-6" for="total">Total</label>
														<input type="hidden" class="form-control" name="total" value="<?php echo e(($total)); ?>">
														<div class="col-sm-6">
														  <p class="form-control-static"><?php echo e(moneyFormat($deposit->currency->symbol, formatNumber($total))); ?></p>
														</div>
													</div>
												<?php endif; ?>

											</div>
										</div>
										
										<div class="panel panel-default">
											<div class="panel-body">

												<?php if($deposit->user_id): ?>
													<div class="form-group">
														<label class="control-label col-sm-3" for="user">User</label>
														<input type="hidden" class="form-control" name="user" value="<?php echo e(isset($deposit->user) ? $deposit->user->first_name.' '.$deposit->user->last_name :"-"); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e(isset($deposit->user) ? $deposit->user->first_name.' '.$deposit->user->last_name :"-"); ?></p>
														</div>
													</div>
												<?php endif; ?>

												<?php if($deposit->uuid): ?>
								                    <div class="form-group">
														<label class="control-label col-sm-3" for="deposit_uuid">Transaction ID</label>
														<input type="hidden" class="form-control" name="deposit_uuid" value="<?php echo e($deposit->uuid); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e($deposit->uuid); ?></p>
														</div>
													</div>
												<?php endif; ?>

												<?php if($deposit->currency): ?>
													<div class="form-group">
														<label class="control-label col-sm-3" for="currency">Currency</label>
														<input type="hidden" class="form-control" name="currency" value="<?php echo e($deposit->currency->code); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e($deposit->currency->code); ?></p>
														</div>
													</div>
												<?php endif; ?>

												<?php if($deposit->payment_method): ?>
								                    <div class="form-group">
														<label class="control-label col-sm-3" for="payment_method">Payment Method</label>
														<input type="hidden" class="form-control" name="payment_method" value="<?php echo e(($deposit->payment_method->name == "Mts") ? getCompanyName() : $deposit->payment_method->name); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static">
														      <?php if($deposit->payment_method->name == "Mts"): ?>
    													          <?php echo e(getCompanyName()); ?>

    													      <?php elseif($deposit->payment_method->name == "Stripe"): ?>
    													          Debit/Credit Card
    													      <?php else: ?>
    													          <?php echo e($deposit->payment_method->name); ?>

    													      <?php endif; ?>
														  </p>
														</div>
													</div>
												<?php endif; ?>

												<?php if($deposit->bank): ?>
								                    <div class="form-group">
														<label class="control-label col-sm-3" for="bank_name">Bank Name</label>
														<input type="hidden" class="form-control" name="bank_name" value="<?php echo e($deposit->bank->bank_name); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e($deposit->bank->bank_name); ?></p>
														</div>
													</div>

								                    <div class="form-group">
														<label class="control-label col-sm-3" for="bank_branch_name">Branch Name</label>
														<input type="hidden" class="form-control" name="bank_branch_name" value="<?php echo e($deposit->bank->bank_branch_name); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e($deposit->bank->bank_branch_name); ?></p>
														</div>
													</div>

								                    <div class="form-group">
														<label class="control-label col-sm-3" for="account_name">Account Name</label>
														<input type="hidden" class="form-control" name="account_name" value="<?php echo e($deposit->bank->account_name); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e($deposit->bank->account_name); ?></p>
														</div>
													</div>
												<?php endif; ?>

												<?php if($deposit->file): ?>
													<div class="form-group">
														<label class="control-label col-sm-3" for="attached_file">Attached File</label>
														<div class="col-sm-9">
														  <p class="form-control-static">
										                  	<a href="<?php echo e(url('public/uploads/files/bank_attached_files').'/'.$deposit->file->filename); ?>" download=<?php echo e($deposit->file->filename); ?>><i class="fa fa-fw fa-download"></i>
											                  	<?php echo e($deposit->file->originalname); ?>

											                  </a>
														  </p>
														</div>
													</div>
												<?php endif; ?>

												<div class="form-group">
    												<label class="control-label col-sm-3" for="created_at">Date</label>
    												<input type="hidden" class="form-control" name="created_at" value="<?php echo e($deposit->created_at); ?>">
    												<div class="col-sm-9">
    												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
    												</div>
    											</div>

						                   		<?php if($deposit->status): ?>
							                   		<div class="form-group">
														<label class="control-label col-sm-3" for="status">Change Status</label>
														<div class="col-sm-9">
															<select class="form-control select2" name="status" style="width: 60%;">
																<option value="Success" <?php echo e($deposit->status ==  'Success'? 'selected':""); ?>>Success</option>
																<option value="Pending"  <?php echo e($deposit->status == 'Pending' ? 'selected':""); ?>>Pending</option>
																<option value="Blocked"  <?php echo e($deposit->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
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
											<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/deposits')); ?>">Cancel</a></div>
											<div class="col-md-1">
												<button type="submit" class="btn button-secondary pull-right" id="deposits_edit">
	                                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposits_edit_text">Update</span>
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

	// disabling submit and cancel button after form submit
	$(document).ready(function()
	{
	  $('form').submit(function()
	  {
	     	$("#deposits_edit").attr("disabled", true);

	     	$('#cancel_anchor').attr("disabled","disabled");

            $(".fa-spin").show();

            $("#deposits_edit_text").text('Updating...');

            // Click False
			$('#deposits_edit').click(false);
			$('#cancel_anchor').click(false);
	  });
	});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/deposits/edit.blade.php ENDPATH**/ ?>