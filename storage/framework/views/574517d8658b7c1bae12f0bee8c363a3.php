
<?php $__env->startSection('title', 'KYC Verifications'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href='<?php echo e(url("admin/users/edit/$users->id")); ?>'>Profile</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/transactions/$users->id")); ?>">Transactions</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/wallets/$users->id")); ?>">Wallets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/tickets/$users->id")); ?>">Tickets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/disputes/$users->id")); ?>">Disputes</a>
                </li>
                <li class="active">
                  <a href="<?php echo e(url("admin/users/kyc-verications/$users->id")); ?>">KYC Verifications</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/bankdetails/$users->id")); ?>">Bank Details</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/address_edit/$users->id")); ?>">Address</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/activity-logs/$users->id")); ?>">Activity Logs</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>
    
    <?php if($users->status == 'Inactive'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-danger">Inactive</span></h3>
    <?php elseif($users->status == 'Suspended'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-warning">Suspended</span></h3>
    <?php elseif($users->status == 'Active'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-success">Active</span></h3>
    <?php endif; ?>
    
    <?php if(count($documentVerificationStatus) > 0): ?>
    	<div class="box">
    		<div class="box-body">
    		    <?php $__currentLoopData = $documentVerificationStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documentVerification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    		        <?php 
    			        $back_photo = DB::table('files')->where('id', $documentVerification->file_back_id)->first();
    			        
    			        if($documentVerification->country){
                             $country=  DB::table('countries')->where('short_name',$documentVerification->country??'')->first()->name??'';
                        }else{
                            $country=$documentVerification->country??'';
                        }
                        
                        $location = $documentVerification->city.' | '. $country;
                        
                        if(!empty($documentVerification->updated_by)){
                            $admins=  DB::table('admins')->where('id', $documentVerification->updated_by)->first();
                            if(!empty($admins)){
                                $updated_by = $admins->first_name.' '.$admins->last_name;
                            }else{
                                $updated_by='-';
                            }
                        }else{
                            $updated_by='-';
                        }
    			    ?>
    		        <div class="panel panel-default">
        			    <div class="panel-body">
        			        <h3 class="text-center"><?php echo e(ucfirst($documentVerification->verification_type)); ?> Proof
            			        <?php if($documentVerification->status ==  'approved'): ?> <span class="badge badge-success" style="padding: 5px 10px; background-color: green;"> Approved </span> <?php endif; ?>
            			        <?php if($documentVerification->status ==  'pending'): ?> <span class="badge badge-primary" style="padding: 5px 10px; background-color: blue;"> Pending </span> <?php endif; ?>
            			        <?php if($documentVerification->status ==  'rejected'): ?> <span class="badge badge-danger" style="padding: 5px 10px; background-color: red;"> Rejected </span> <?php endif; ?>
            			    </h3> 
        			        <hr>
        			        <div class="row">
            					<form action="<?php echo e(url('admin/users/kyc-verications/update')); ?>" class="form-horizontal" method="POST">
            						<?php echo e(csrf_field()); ?>

            				        <input type="hidden" value="<?php echo e($documentVerification->id); ?>" name="id">
            				        <input type="hidden" value="<?php echo e($documentVerification->user_id); ?>" name="user_id">
            				        <input type="hidden" value="<?php echo e($documentVerification->verification_type); ?>" name="verification_type">
            
            						<div class="col-md-6">
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="user">User</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e(isset($documentVerification->user) ? $documentVerification->user->first_name.' '.$documentVerification->user->last_name :"-"); ?></p>
    										</div>
    									</div>
    									
    				                    <div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">Identity Type</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e(str_replace('_', ' ', ucfirst($documentVerification->identity_type))); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_number">Document</label>
    										<div class="col-sm-7">
    										    <?php if(!empty($documentVerification->file->filename)): ?>
        										    <p class="form-control-static">
        										        <?php echo e($documentVerification->file->filename); ?>

        										        <a class="text-info" style="margin-left: 10px" href="<?php echo e(url('public/uploads/user-documents/'.$documentVerification->verification_type.'-proof-files').'/'.$documentVerification->file->filename); ?>" target="_blank">
        													<i class="fa fa-eye"></i>
                                                        </a>
        												<a class="text-info" style="margin-left: 10px" href="<?php echo e(url('public/uploads/user-documents/'.$documentVerification->verification_type.'-proof-files').'/'.$documentVerification->file->filename); ?>" download target="_blank">
        													<i class="fa fa-download"></i>
                                                        </a>
        										    </p>
        										<?php endif; ?>
    										    
    										    <?php if(!empty($back_photo)): ?>
        										    <p class="form-control-static">
        										        <?php echo e($back_photo->filename); ?>

        										        <a class="text-info" style="margin-left: 10px" href="<?php echo e(url('public/uploads/user-documents/identity-proof-files').'/'.$documentVerification->file->filename); ?>" target="_blank">
        													<i class="fa fa-eye"></i>
                                                        </a>
        												<a class="text-info" style="margin-left: 10px" href="<?php echo e(url('public/uploads/user-documents/identity-proof-files').'/'.$documentVerification->file->filename); ?>" download target="_blank">
        													<i class="fa fa-download"></i>
                                                        </a>
        										    </p>
        										<?php endif; ?>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">Uploaded On</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($documentVerification->created_at)->format('d-M-Y h:i A')); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">Updated On</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e(Carbon\Carbon::parse($documentVerification->updated_at)->format('d-M-Y h:i A')); ?></p>
    										</div>
    									</div>
            						</div>
                    
                    				<div class="col-md-6">
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">Updated By</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e($updated_by); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">User Location</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e($location); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">App Version</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e($documentVerification->app_ver); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">User Device</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e($documentVerification->device_manufacture.' | '.$documentVerification->device_name.' | '.$documentVerification->device_model); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">Operating System</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e($documentVerification->device_os.' '.$documentVerification->os_ver); ?></p>
    										</div>
    									</div>
    									
    									<div class="form-group">
    										<label class="control-label col-sm-5" for="identity_type">IP Address</label>
    										<div class="col-sm-7">
    										  <p class="form-control-static"><?php echo e($documentVerification->ip_address); ?></p>
    										</div>
    									</div>
                    				</div>
                    
            						<div class="col-md-12">
            						    <div class="form-group">
    										<label class="control-label col-sm-4" for="status">Change Status</label>
    										<div class="col-sm-3 text-left">
    											<select class="form-control select2" name="status" style="width: 60%;">
    												<option value="approved" <?php echo e($documentVerification->status ==  'approved'? 'selected':""); ?>>Approved</option>
    												<option value="pending"  <?php echo e($documentVerification->status == 'pending' ? 'selected':""); ?>>Pending</option>
    												<option value="rejected"  <?php echo e($documentVerification->status == 'rejected' ? 'selected':""); ?>>Rejected</option>
    											</select>
    										</div>
    										<div class="col-sm-3">
    									        <button type="submit" class="btn button-secondary">Update</button>
    										</div>
    									</div>
    								</div>
            					</form>
                    		</div>
                    	</div>
        		    </div>
            	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    		</div>
    	</div>
    <?php else: ?>
        <div class="box">
    		<div class="box-body">
    		    <h3 class="text-center" style="margin-bottom: 20px;">No Documents Uploaded</h3>
    		</div>
    	</div>
    <?php endif; ?>
	
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>
<script type="text/javascript">
	$(".select2").select2({});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/verifications/list.blade.php ENDPATH**/ ?>