
<?php $__env->startSection('title', 'KYC Verifications'); ?>
<?php $__env->startSection('page_content'); ?>

    <style>
        .link-active {
          color: #00a65a !important;
          text-decoration: underline !important;
        }
        
        .tab-links-link{
            padding: 0px 20px;
        }
        
        .tab-hide {
          display: none;
        }
        
        .tab-links{
            width: fit-content;
            background-color: #fff;
            font-size: 20px;
            padding: 20px 40px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>

    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href="<?php echo e(url("admin/users/edit/$users->id")); ?>">Profile</a>
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
    
    <div class="page-wrapper">
        <div class="tab-container">
            <center><div class="tab-links">
                <a class="tab-links-link link-active" href="#autokyc">Auto KYC</a>
                <a class="tab-links-link" href="#manualkyc">Manual KYC</a>
            </div></center>
          
            <div class="tab-content-container">
                <div class="content-active" id="autokycdiv">
                    <div class="box">
                    	<div class="box-body">
                    	    <?php if($autokyc_details != 'N/A'): ?>
                    	        <div class="row"> 
                                    <div class="col-md-8">
                                          <div class="box mt-4">
                                      <div class="box-body">
                                          <h4 class="mb-2"><strong>Verification/Selfie</strong></h4>
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->left_photo_url); ?>" alt="left_photo_url"  width="100%" height="250" width="100%">
                                                <h4>Left</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->center_photo_url); ?>" alt="center_photo_url"  width="100%" height="250" width="100%">
                                                <h4>Center</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->right_photo_url); ?>" alt="right_photo_url" width="100%" height="250" width="100%">
                                                            <h4>Right</h4>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                     <div class="box mt-3">
                                      <div class="box-body">
                                          <h4 class="mb-2"><strong>Verification/Government Id</strong></h4>
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->front_photo_url); ?>" alt="front_photo_url"  height="250" width="100%">
                                                <h4>Front</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <?php if(!empty($autokyc_details->back_photo_url)): ?>
                                                    <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->back_photo_url); ?>" alt=""  height="250" width="100%">
                                                    <h4>Back</h4>
                                                <?php else: ?>
                                                    <h4>Not Available</h4>         
                                                <?php endif; ?>            
                                            </div>
                                            <div class="col-md-4">
                                                <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->selfie_photo_url); ?>" alt="selfie_photo_url" height="250" width="100%">
                                                <h4>Selfie</h4>
                                            </div>
                                        </div>
                                        <hr>
                                        <h4 class="mb-2"><strong>Attributes</strong></h4>
                                        <div class="row">
                                            <div class="col-md-4">
                                                  <h5><strong>NAME</strong></h5>
                                                 <h5><?php echo e($autokyc_details->name_first); ?>&nbsp;&nbsp;<?php echo e($autokyc_details->name_middle); ?>&nbsp;&nbsp;<?php echo e($autokyc_details->name_last); ?></h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>GOVERNMENT ID NUMBER</strong></h5>
                                                 <h5><?php echo e($autokyc_details->identification_number); ?></h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>BIRTHDATE</strong></h5>
                                                 <h5><?php echo e($autokyc_details->birthdate); ?></h5>
                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-4">
                                                  <h5><strong>ADDRESS</strong></h5>
                                                 <h5><?php echo e($autokyc_details->addressstreet1); ?>&nbsp;&nbsp;<?php echo e($autokyc_details->addressstreet2); ?>&nbsp;&nbsp;<?php echo e($autokyc_details->address_city); ?>,<?php echo e($autokyc_details->address_subdivision_abb); ?>&nbsp;<?php echo e($autokyc_details->address_postal_code_abbr); ?></h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>EMAIL ADDRESS</strong></h5>
                                                 <h5><?php echo e($autokyc_details->email_address??'No email address collected'); ?></h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>PHONE NUMBER</strong></h5>
                                                 <h5><?php echo e($autokyc_details->phone_number??'No phone number collected'); ?></h5>
                                            </div>
                                        </div>
                                          <div class="row">
                                            <div class="col-md-4">
                                                  <h5><strong><strong>SELECTED COUNTRY CODE</strong></strong></h5>
                                                 <h5><?php echo e($autokyc_details->selected_country_code); ?></h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>SELECTED ID CLASS</strong></h5>
                                                 <h5><?php echo e($autokyc_details->selected_id_class); ?></h5>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                   
                                      
                                    <!-- <div class="box mt-4">-->
                                    <!--  <div class="box-body">-->
                                    <!--      <h4 class="mb-2">Document/Government-Id</h4>-->
                                    <!--    <div class="row text-center">-->
                                    <!--        <div class="col-md-4">-->
                                    <!--            <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->front_photo); ?>" alt="front_photo"  height="250">-->
                                    <!--        </div>-->
                                    <!--        <div class="col-md-4">-->
                                    <!--            <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->back_photo); ?>" alt="back_photo"  height="250">-->
                                    <!--        </div>-->
                                    <!--        <div class="col-md-4">-->
                                    <!--            <img src="<?php echo e(url('public/kyc_documents/').'/'.$autokyc_details->selfie_photo); ?>" alt="selfie_photo" height="250">-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--  </div>-->
                                    <!--</div>-->
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box mt-3">
                                          <div class="box-body">
                                              <h4 class="mb-2"><strong>INFO</strong></h4>
                                            <div class="row">
                                                 <div class="col-md-12">
                                                    <h5><strong>STATUS</strong></h5>
                                                     <h5><?php echo e(ucfirst($autokyc_details->status)); ?></h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>INQUIRY ID</strong></h5>
                                                     <h5><?php echo e($autokyc_details->proof_id); ?></h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>REFERENCE ID</strong></h5>
                                                     <h5><?php echo e($autokyc_details->reference_id); ?></h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>ACCOUNT ID</strong></h5>
                                                     <h5><?php echo e($autokyc_details->account_id); ?></h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>CREATED AT</strong></h5>
                                                     <h5><?php echo e($autokyc_details->created_at); ?></h5>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="box mt-4">
                                          <div class="box-body">
                                              <h4 class="mb-2"><strong>Location</strong></h4>
                                                <div class="row">
                                                 <div class="col-md-12">
                                                    <div><?php echo e($autokyc_details->region_name); ?></div>
                                                    <div><?php echo e($autokyc_details->country_name); ?></div>
                                                    <div><strong>DEVICE</strong> (<?php echo e($autokyc_details->os_name); ?>  <?php echo e($autokyc_details->os_full_version); ?> <?php echo e($autokyc_details->device_name); ?>)</div>
                                                 </div>
                                                </div>
                                          </div>
                                        </div>
                                        <div class="box mt-4">
                                          <div class="box-body">
                                              <h4 class="mb-2"><strong>Network  Details</strong></h4>
                                                <div class="row">
                                                 <div class="col-md-12">
                                                    <strong>IP ADDRESS:</strong>	<?php echo e($autokyc_details->ip_address); ?>

                                                </div>
                                                </div>
                                                <div class="row">
                                                <div class="col-md-12">
                                                    <strong>NETWORK THREAT LEVEL:</strong>	<?php echo e($autokyc_details->threat_level); ?>

                                                 </div>
                                                 <div class="col-md-12">
                                                      <strong>LATITUDE:</strong>	<?php echo e($autokyc_details->latitude); ?>

                                                 </div>
                                                </div>
                                                <div class="row">
                                                <div class="col-md-12">
                                                   <strong>LONGITUDE:</strong>	<?php echo e($autokyc_details->longitude); ?>

                                                 </div>
                                                 <div class="col-md-12">
                                                  <strong>DEVICE TYPE:</strong>	<?php echo e($autokyc_details->os_name); ?>,<?php echo e($autokyc_details->os_full_version); ?>,<?php echo e($autokyc_details->device_name); ?>

                                                 </div>
                                                </div>
                                                <div class="row">
                                                  <div class="col-md-12">
                                                 <strong> DEVICE OS:</strong>	<?php echo e($autokyc_details->os_name); ?>

                                                 </div>
                                                 <div class="col-md-12">
                                                      <strong>BROWSER:</strong> <?php echo e($autokyc_details->browser_name); ?>

                                                 </div>
                                                </div>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                    	    <?php else: ?>
                    	        <div class="box">
                            		<div class="box-body">
                            		    <h3 class="text-center" style="margin-bottom: 20px;">KYC Not Updated</h3>
                            		</div>
                            	</div>
                    	    <?php endif; ?>
                    	</div>
                    </div>
                </div>
                <div class="tab-hide fade" id="manualkycdiv">
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
                            			        <?php if($documentVerification->status ==  'approved'): ?> <span class="badge badge-success" style="padding: 5px 10px; background-color: green;"> Verified </span> <?php endif; ?>
                            			        <?php if($documentVerification->status ==  'pending'): ?> <span class="badge badge-primary" style="padding: 5px 10px; background-color: blue;"> Unverified </span> <?php endif; ?>
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
                    												<option value="approved" <?php echo e($documentVerification->status ==  'approved'? 'selected':""); ?>>Verified</option>
                    												<option value="pending"  <?php echo e($documentVerification->status == 'pending' ? 'selected':""); ?>>Unverified</option>
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
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>
    <script type="text/javascript">
    	$(".select2").select2({});
    	
    	$('.tab-links-link').click(function() {
          $('.tab-links-link').each(function() {
            var hashValue = this.href.split('#');
            $('a[href$="'+hashValue[1]+'"]').removeClass("link-active");
            $("#" + hashValue[1] + 'div').removeClass("content-active").addClass( "tab-hide fade" );
          });
          
          var hashValue = this.href.split('#');
          $('a[href$="'+hashValue[1]+'"]').addClass("link-active");
          $("#" + hashValue[1] + 'div').removeClass( "tab-hide fade" ).addClass( "content-active" );
        }); 
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/verifications/list.blade.php ENDPATH**/ ?>