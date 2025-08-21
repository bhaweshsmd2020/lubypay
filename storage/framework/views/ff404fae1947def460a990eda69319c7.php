
<?php $__env->startSection('title', 'NFC Credentials Settings'); ?>

<?php $__env->startSection('head_style'); ?>
   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/sweetalert/sweetalert.css')); ?>">

  <!-- bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/css/bootstrap-select.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<!-- Main content -->
<div class="row">
    <div class="col-md-3 settings_bar_gap">
        <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">NFC Credentials Settings</h3>
            </div>

            <form action="<?php echo e(url('admin/settings/nfc-update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>


                <!-- box-body -->
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Publish key</label>
					  <div class="col-sm-10">
					    <input type="text" name="pub_key" class="form-control" value="<?php echo e($credential->pub_key); ?>" placeholder="Publish key">
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Secret key</label>
					  <div class="col-sm-10">
					    <input type="text" name="sec_key" class="form-control" value="<?php echo e($credential->sec_key); ?>" placeholder="Secret key">
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Mode</label>
					  <div class="col-sm-10">
					    <select name="mode" class="form-control">
					        <option value="production" <?php if($credential->mode == 'production'): ?> selected <?php endif; ?>>Production</option>
					        <option value="sandbox" <?php if($credential->mode == 'sandbox'): ?> selected <?php endif; ?>>Sandbox</option>
					    </select>
					  </div>
					</div>
					  
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Status</label>
					  <div class="col-sm-10">
					    <select name="status" class="form-control">
					        <option value="1" <?php if($credential->status == '1'): ?> selected <?php endif; ?>>Active</option>
					        <option value="2" <?php if($credential->status == '2'): ?> selected <?php endif; ?>>Inactive</option>
					    </select>
					  </div>
					</div>
				</div>
				<!-- /.box-body -->

				<!-- box-footer -->
				<?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_nfc_credentials')): ?>
        			<div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat pull-right">Submit</button>
                    </div>
                <?php endif; ?>
  	            <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

  <!-- jquery.validate -->
  <script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

  <!-- jquery.validate additional-methods -->
  <script src="<?php echo e(asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js')); ?>" type="text/javascript"></script>

  <!-- sweetalert -->
  <script src="<?php echo e(asset('public/backend/sweetalert/sweetalert.min.js')); ?>" type="text/javascript"></script>

  <!-- bootstrap-select -->
  <script src="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/js/bootstrap-select.min.js')); ?>" type="text/javascript"></script>

  <!-- read-file-on-change -->
  <?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/settings/nfc.blade.php ENDPATH**/ ?>