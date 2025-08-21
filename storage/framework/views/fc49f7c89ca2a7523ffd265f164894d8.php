
<?php $__env->startSection('title', 'Security'); ?>

<?php $__env->startSection('head_style'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="box"> 
       <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <h3><?php echo e(ucwords(Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name)); ?></h3>
                </div>
                <div class="col-md-4 text-right">
                    <?php if(Auth::guard('admin')->user()->status == 'Inactive'): ?>
                        <h3>Status: <span class="label label-danger">Inactive</span></h3>
                    <?php elseif(Auth::guard('admin')->user()->status == 'Suspended'): ?>
                        <h3>Status: <span class="label label-warning">Suspended</span></h3>
                    <?php elseif(Auth::guard('admin')->user()->status == 'Active'): ?>
                        <h3>Status: <span class="label label-success">Active</span></h3>
                    <?php endif; ?>
                </div>
            </div>
       </div>
    </div>
    
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href='<?php echo e(url('admin/profile')); ?>'>Profile</a>
                </li>

                <li class="active">
                  <a href="<?php echo e(url("admin/change-password")); ?>">Security</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    
                    <form action='<?php echo e(url("admin/change-password")); ?>' method="POST" class="form-horizontal" id="password_form" >
                        <?php echo csrf_field(); ?>

        
                        <input type="hidden" value="<?php echo e($admin_id); ?>" name="id" id="id" />
        
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="old_pass">Old Password</label>
                                      <div class="col-sm-8">
                                        <input type="password" class="form-control" name="old_pass" id="old_pass">
                                        <span id="password_error"></span>
                                      </div>
                                    </div>
                    
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="password">New Password</label>
                                      <div class="col-sm-8">
                                        <input type="password" class="form-control" id="new_pass" name="new_pass">
                                      </div>
                                    </div>
                    
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="new_pass_confirmation">Confirm Password</label>
                                      <div class="col-sm-8">
                                        <input type="password" class="form-control" id="new_pass_confirmation" name="new_pass_confirmation">
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4" for="inputEmail3">
                                        </label>
                                        <div class="col-sm-8">
                                            <button type="submit" class="btn btn-primary btn-flat">
                                                <span id="users_edit_text">Update</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-5">
                                    <center>
                                        <?php if(!empty(Auth::guard('admin')->user()->picture)): ?>
                                            <img src='<?php echo e(url("public/uploads/userPic/". Auth::guard('admin')->user()->picture)); ?>' style="border-radius:100%; width:250px; height:250px; margin-bottom: 30px;">
                                        <?php else: ?>
                                            <img src='<?php echo e(url("public/admin_dashboard/img/avatar.jpg")); ?>' style="border-radius: 50%; width: auto; height: 300px; margin-bottom: 30px;">
                                        <?php endif; ?>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <h4>Two-Factor Security Option</h4>
                    
                    <p>
                        Two-factor authentication is a method for protection your web account. 
                        When it is activated you need to enter not only your password, but also a special code. 
                        You can receive this code by in mobile app. 
                        Even if third person will find your password, then cant access with that code.')}}
                    </p>
                    
                    <?php if(Auth::guard('admin')->user()->fa_status==0): ?>
                        <span class="label label-danger badge-pill">Disabled</span>
                    <?php else: ?>
                        <span class="label label-primary badge-pill">Active</span>
                    <?php endif; ?>
                    
                    <ul style="margin-top: 20px;">
                        <li>Install an authentication app on your device. Any app that supports the Time-based One-Time Password (TOTP) protocol should work.</li>
                        <li>Use the authenticator app to scan the barcode below.</li>
                        <li>Enter the code generated by the authenticator app.</li>
                    </ul>
                    
                    <a data-toggle="modal" data-target="#modal-form2fa" href="" class="btn btn-neutral">
                        <?php if(Auth::guard('admin')->user()->fa_status==0): ?>
                            <span class="label label-success badge-pill">Enable 2fa</span>
                        <?php elseif(Auth::guard('admin')->user()->fa_status==1): ?>
                            <span class="label label-warning badge-pill">Disable 2fa</span>
                        <?php endif; ?>
                    </a>
                    
                    <div class="modal fade" id="modal-form2fa" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                          <div class="modal-content">
                            <div class="modal-body text-center">
                              <?php if(Auth::guard('admin')->user()->fa_status==0): ?>
                              <img src="<?php echo e($image); ?>" class="mb-3 user-profile">
                              <?php endif; ?>
                              <form action="<?php echo e(url("admin/2fa")); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="form-group row">
                                  <div class="col-lg-12">
                                    <input type="text" pattern="\d*" name="code" class="form-control" minlength="6" maxlength="6" placeholder="Six digit code" required>
                                    <input type="hidden" name="vv" value="<?php echo e($secret); ?>">
                                    <?php if(Auth::guard('admin')->user()->fa_status==0): ?>
                                    <input type="hidden" name="type" value="1">
                                    <?php elseif(Auth::guard('admin')->user()->fa_status==1): ?>
                                    <input type="hidden" name="type" value="0">
                                    <?php endif; ?>
                                  </div>
                                </div>
                                <div class="text-right">
                                  <button type="submit" class="btn btn-neutral btn-block">
                                    <?php if(Auth::guard('admin')->user()->fa_status==0): ?>
                                        <span class="label label-success badge-pill">Activate</span>
                                    <?php elseif(Auth::guard('admin')->user()->fa_status==1): ?>
                                        <span class="label label-warning badge-pill">Disable</span>
                                    <?php endif; ?>
                                  </button>
                                </div>
                              </form>
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

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/profile/change_password.blade.php ENDPATH**/ ?>