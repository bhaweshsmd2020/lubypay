
<?php $__env->startSection('title', 'Edit Photo Verification'); ?>

<?php $__env->startSection('page_content'); ?>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-10">
									<h4 class="text-left">Photo Verification Details</h4>
								</div>
								<div class="col-md-2">
									<?php if($documentVerification->status): ?>
										<h4 class="text-left">Status : <?php if($documentVerification->status == 'approved'): ?><span class="text-green">Approved</span><?php endif; ?>
				                    	<?php if($documentVerification->status == 'pending'): ?><span class="text-blue">Pending</span><?php endif; ?>
				            			<?php if($documentVerification->status == 'rejected'): ?><span class="text-red">Rejected</span><?php endif; ?></h4>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<form action="<?php echo e(url('admin/photo-proofs/update')); ?>" class="form-horizontal" id="deposit_form" method="POST">
										<?php echo e(csrf_field()); ?>

							        <input type="hidden" value="<?php echo e($documentVerification->id); ?>" name="id" id="id">
							        <input type="hidden" value="<?php echo e($documentVerification->user_id); ?>" name="user_id" id="user_id">
							        <input type="hidden" value="<?php echo e($documentVerification->verification_type); ?>" name="verification_type" id="verification_type">

									<div class="col-md-7">
										<div class="panel panel-default">
											<div class="panel-body">

												<?php if($documentVerification->user_id): ?>
													<div class="form-group">
														<label class="control-label col-sm-3" for="user">User</label>
														<input type="hidden" class="form-control" name="user" value="<?php echo e(isset($documentVerification->user) ? $documentVerification->user->first_name.' '.$documentVerification->user->last_name :"-"); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e(isset($documentVerification->user) ? $documentVerification->user->first_name.' '.$documentVerification->user->last_name :"-"); ?></p>
														</div>
													</div>
												<?php endif; ?>

												<!--<?php if($documentVerification->identity_type): ?>-->
								    <!--                <div class="form-group">-->
												<!--		<label class="control-label col-sm-3" for="identity_type">Identity Type</label>-->
												<!--		<input type="hidden" class="form-control" name="identity_type" value="<?php echo e($documentVerification->identity_type); ?>">-->
												<!--		<div class="col-sm-9">-->
												<!--		  <p class="form-control-static"><?php echo e(str_replace('_', ' ', ucfirst($documentVerification->identity_type))); ?></p>-->
												<!--		</div>-->
												<!--	</div>-->
												<!--<?php endif; ?>-->

												<!--<?php if($documentVerification->identity_number): ?>-->
								    <!--                <div class="form-group">-->
												<!--		<label class="control-label col-sm-3" for="identity_number">Identity Number</label>-->
												<!--		<input type="hidden" class="form-control" name="identity_number" value="<?php echo e($documentVerification->identity_number); ?>">-->
												<!--		<div class="col-sm-9">-->
												<!--		  <p class="form-control-static"><?php echo e($documentVerification->identity_number); ?></p>-->
												<!--		</div>-->
												<!--	</div>-->
												<!--<?php endif; ?>-->

												<?php if($documentVerification->created_at): ?>
													<div class="form-group">
														<label class="control-label col-sm-3" for="created_at">Date</label>
														<input type="hidden" class="form-control" name="created_at" value="<?php echo e($documentVerification->created_at); ?>">
														<div class="col-sm-9">
														  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($documentVerification->created_at)->format('d-M-Y h:i A')); ?></p>
														</div>
													</div>
						                   		<?php endif; ?>

						                   		<?php if($documentVerification->status): ?>
							                   		<div class="form-group">
														<label class="control-label col-sm-3" for="status">Change Status</label>
														<div class="col-sm-9">
															<select class="form-control select2" name="status" style="width: 60%;">
																<option value="approved" <?php echo e($documentVerification->status ==  'approved'? 'selected':""); ?>>Approved</option>
																<option value="pending"  <?php echo e($documentVerification->status == 'pending' ? 'selected':""); ?>>Pending</option>
																<option value="rejected"  <?php echo e($documentVerification->status == 'rejected' ? 'selected':""); ?>>Rejected</option>
															</select>
														</div>
													</div>
												<?php endif; ?>

											</div>
										</div>
									</div>

									<div class="col-md-5">
										<div class="panel panel-default">
											<div class="panel-body">

	                                            <?php if($documentVerification->file): ?>
	                                            <div>

	                                            	<input type="hidden" class="form-control" name="photo_file" value="<?php echo e($documentVerification->file->filename); ?>">
	                                                <ul style="list-style-type: none;">
	                                                	<h4 style="text-decoration: underline;">Photo Proof</h4>
													    <li> <?php echo e($documentVerification->file->filename); ?>

													        <a class="text-info pull-right" href="<?php echo e(url('public/uploads/user-documents/photo-proof-files').'/'.$documentVerification->file->filename); ?>"  target="_blank">
													  			<i class="fa fa-eye"></i>
			                                                </a>
															<a class="text-info pull-right " style="margin-right:10px" href="<?php echo e(url('public/uploads/user-documents/photo-proof-files').'/'.$documentVerification->file->filename); ?>" download target="_blank">
																<i class="fa fa-download"></i>
			                                                </a>&nbsp;&nbsp;&nbsp;&nbsp;
													    </li>
													</ul>
												</div>
												<?php endif; ?>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-11">
											<div class="col-md-2"></div>
											<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="<?php echo e(url('admin/photo-proofs')); ?>">Cancel</a></div>
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

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/verifications/photo_proofs/edit.blade.php ENDPATH**/ ?>