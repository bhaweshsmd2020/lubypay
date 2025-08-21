
<?php $__env->startSection('title', 'Edit Payout'); ?>
<?php $__env->startSection('page_content'); ?>

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $withdrawal->id)->where('payment_method_id', '6')->first();
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$withdrawal->ip_address}/json"));
    
    if(!empty($withdrawal->local_tran_time)){
	    $trans_time = $withdrawal->local_tran_time;
	}else{
	    $trans_time = $withdrawal->created_at;
	}
?>
 <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">
                    Payout Details
                  </div>
                </div>
                <div class="col-md-3 pull-right" style="margin-top:8px;">
                        <a href="<?php echo e(url('admin/withdrawals')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-chevron-left"> &nbsp;</span>Back to deposits list</a>
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
								<h4 class="text-left">Payout Details</h4>
							</div>
							<div class="col-md-3">
								<?php if($withdrawal->status): ?>
									<h4 class="text-left">Status : <?php if($withdrawal->status == 'Success'): ?><span class="text-green">Success</span><?php endif; ?>
		                        	<?php if($withdrawal->status == 'Pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
                        			<?php if($withdrawal->status == 'Blocked'): ?><span class="text-red">Cancelled</span><?php endif; ?></h4>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<form action="<?php echo e(url('admin/withdrawals/update')); ?>" class="form-horizontal" id="withdrawal_form" method="POST">
								<?php echo e(csrf_field()); ?>


						        <input type="hidden" value="<?php echo e($withdrawal->id); ?>" name="id" id="id">
						        <input type="hidden" value="<?php echo e($withdrawal->user_id); ?>" name="user_id" id="user_id">
						        <input type="hidden" value="<?php echo e($withdrawal->currency->id); ?>" name="currency_id" id="currency_id">
						        <input type="hidden" value="<?php echo e($withdrawal->uuid); ?>" name="uuid" id="uuid">

						        <input type="hidden" value="<?php echo e($transaction->transaction_type_id); ?>" name="transaction_type_id" id="transaction_type_id">
						        <input type="hidden" value="<?php echo e($transaction->transaction_type->name); ?>" name="transaction_type" id="transaction_type">
						        <input type="hidden" value="<?php echo e($transaction->status); ?>" name="transaction_status" id="transaction_status">
						        <input type="hidden" value="<?php echo e($transaction->transaction_reference_id); ?>" name="transaction_reference_id" id="transaction_reference_id">


								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

											<?php if($withdrawal->amount): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Requested Amount</label>
													<input type="hidden" class="form-control" name="amount" value="<?php echo e(($withdrawal->amount)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->amount))); ?></p>
													</div>
												</div>
											<?php endif; ?>



							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="feesTotal">Fees
														<span>
															<small class="transactions-edit-fee">
																<?php if(isset($transaction)): ?>
																(<?php echo e((formatNumber($transaction->percentage))); ?>% + <?php echo e(formatNumber($withdrawal->charge_fixed)); ?>)
																<?php else: ?>
																	(<?php echo e(0); ?>%+<?php echo e(0); ?>)
																<?php endif; ?>
															</small>
														</span>
													</label>

													<?php
														$feesTotal = $withdrawal->charge_percentage + $withdrawal->charge_fixed;
													?>

													<input type="hidden" class="form-control" name="feesTotal" value="<?php echo e(($feesTotal)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($withdrawal->currency->symbol, formatNumber($feesTotal))); ?></p>
													</div>
												</div>
											<hr class="increase-hr-height">

											<?php
												$total = $withdrawal->amount-$feesTotal ;
											?>

											<?php if(isset($total)): ?>
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Payable Amount</label>
													<input type="hidden" class="form-control" name="total" value="<?php echo e(($total)); ?>">
													<div class="col-sm-6">
													  <p class="form-control-static"><?php echo e(moneyFormat($withdrawal->currency->symbol, formatNumber($total))); ?></p>
													</div>
												</div>
											<?php endif; ?>

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

											<?php if($withdrawal->user_id): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="user">User</label>
													<input type="hidden" class="form-control" name="user" value="<?php echo e(isset($withdrawal->user) ? $withdrawal->user->first_name.' '.$withdrawal->user->last_name :"-"); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(isset($withdrawal->user) ? $withdrawal->user->first_name.' '.$withdrawal->user->last_name :"-"); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($withdrawal->uuid): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="withdrawal_uuid">Transaction ID</label>
													<input type="hidden" class="form-control" name="withdrawal_uuid" value="<?php echo e($withdrawal->uuid); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($withdrawal->uuid); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($withdrawal->currency): ?>
												<div class="form-group">
													<label class="control-label col-sm-3" for="currency">Currency</label>
													<input type="hidden" class="form-control" name="currency" value="<?php echo e($withdrawal->currency->code); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e($withdrawal->currency->code); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<?php if($withdrawal->payment_method): ?>
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="payment_method">Payment Method</label>
													<input type="hidden" class="form-control" name="payment_method" value="<?php echo e(($withdrawal->payment_method->name == "Mts") ? getCompanyName() : $withdrawal->payment_method->name); ?>">
													<div class="col-sm-9">
													  <p class="form-control-static"><?php echo e(($withdrawal->payment_method->name == "Mts") ? getCompanyName() : $withdrawal->payment_method->name); ?></p>
													</div>
												</div>
											<?php endif; ?>

											<div class="form-group">
												<label class="control-label col-sm-3" for="created_at">Date</label>
												<input type="hidden" class="form-control" name="created_at" value="<?php echo e($withdrawal->created_at); ?>">
												<div class="col-sm-9">
												  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A')); ?></p>
												</div>
											</div>

					                   		<?php if($withdrawal->status): ?>
						                   		<div class="form-group">
													<label class="control-label col-sm-3" for="status">Change Status</label>
													<div class="col-sm-9">
														<select class="form-control select2" name="status" style="width: 60%;">
															<option value="Success" <?php echo e($withdrawal->status ==  'Success'? 'selected':""); ?>>Success</option>
															<option value="Pending"  <?php echo e($withdrawal->status == 'Pending' ? 'selected':""); ?>>Pending</option>
															<option value="Blocked"  <?php echo e($withdrawal->status == 'Blocked' ? 'selected':""); ?>>Cancel</option>
														</select>
													</div>
												</div>
											<?php endif; ?>

										</div>
									</div>
								</div>

								<div class="col-md-6">
								    <div class="panel panel-default">
    									<div class="panel-body">
    								        <?php if(isset($withdrawal->withdrawal_detail)): ?>
    										    <?php if(!empty($withdrawal->withdrawal_detail->bank_id??'')): ?>
    										        
    										        <?php
    										            $check_bank = DB::table('countries_bank')->where('id', $withdrawal->withdrawal_detail->bank_id??'')->first();
    										            $check_country = DB::table('countries')->where('id', $check_bank->country_id??'')->first();
    										        ?>
    										        
    										        <div class="form-group">
    													<label class="control-label col-sm-6" for="account_name">Country</label>
    													<div class="col-sm-6">
    													  <p class="form-control-static"><?php echo e($check_country->name??''); ?></p>
    													</div>
    												</div>
    												
    												<?php
                        
                                                        if(!empty($check_bank)){
                                                            $avail_fields = json_decode($check_bank->bank, true);
    	
                                                        	foreach($avail_fields as $k=>$avail_field){
                                                        	    
                                                        	?>
                                                        	    <div class="form-group">
            														<label class="control-label col-sm-6" for="account_name"><?php echo e($k); ?></label>
            														<input type="hidden" class="form-control" name="account_name" value="<?php echo e($avail_field); ?>">
            														<div class="col-sm-6">
            														  <p class="form-control-static"><?php echo e($avail_field); ?></p>
            														</div>
            													</div>
                                                        	<?php
                                                        	}
                                                        }
                                                    ?>
    										        
    										    <?php elseif($withdrawal->payment_method->name == 'Bank'): ?>
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="account_name">Account Name</label>
    													<input type="hidden" class="form-control" name="account_name" value="<?php echo e($withdrawal->withdrawal_detail->account_name); ?>">
    													<div class="col-sm-6">
    													  <p class="form-control-static"><?php echo e($withdrawal->withdrawal_detail->account_name); ?></p>
    													</div>
    												</div>
    
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="account_number">Account Number/IBAN</label>
    													<input type="hidden" class="form-control" name="account_number" value="<?php echo e($withdrawal->withdrawal_detail->account_number); ?>">
    													<div class="col-sm-6">
    													  <p class="form-control-static"><?php echo e($withdrawal->withdrawal_detail->account_number); ?></p>
    													</div>
    												</div>
    
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="swift_code">SWIFT Code</label>
    													<input type="hidden" class="form-control" name="swift_code" value="<?php echo e($withdrawal->withdrawal_detail->swift_code); ?>">
    													<div class="col-sm-6">
    													  <p class="form-control-static"><?php echo e($withdrawal->withdrawal_detail->swift_code); ?></p>
    													</div>
    												</div>
    
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="bank_name">Bank Name</label>
    													<input type="hidden" class="form-control" name="bank_name" value="<?php echo e($withdrawal->withdrawal_detail->bank_name); ?>">
    													<div class="col-sm-6">
    													  <p class="form-control-static"><?php echo e($withdrawal->withdrawal_detail->bank_name); ?></p>
    													</div>
    												</div>
    											<?php endif; ?>
    										<?php endif; ?>
    								    </div>
    								</div>
    								
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
										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/withdrawals')); ?>">Cancel</a></div>
										<div class="col-md-1">
											<button type="submit" class="btn button-secondary pull-right" id="withdrawal_edit">
                                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="withdrawal_edit_text">Update</span>
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
	$(document).ready(function()
	{
		$('form').submit(function()
		{
		 	$("#withdrawal_edit").attr("disabled", true);
		 	$('#cancel_anchor').attr("disabled","disabled");
		    $(".fa-spin").show();
		    $("#withdrawal_edit_text").text('Updating...');

		    // Click False
			$('#withdrawal_edit').click(false);
			$('#cancel_anchor').click(false);
		});
	});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/withdrawals/edit.blade.php ENDPATH**/ ?>