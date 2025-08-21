
<?php $__env->startSection('title', 'Partner Settings'); ?>
<?php $__env->startSection('page_content'); ?>

<!-- Main content -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">Card Settings</h3>
            </div>

            <form action="<?php echo e(url('admin/card/fees/update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

				<div class="box-body">
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Minimum Limit</label>
					    <div class="col-sm-6">
					        <input type="text" name="min_limit" class="form-control" value="<?php echo e($fee->min_limit); ?>" placeholder="Minimum Limit">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Maximum Limit</label>
					    <div class="col-sm-6">
					        <input type="text" name="max_limit" class="form-control" value="<?php echo e($fee->max_limit); ?>" placeholder="Maximum Limit">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Billing Info</label>
					    <div class="col-sm-6">
					        <input type="text" name="billing_info" class="form-control" value="<?php echo e($fee->billing_info); ?>" placeholder="Billing Info">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Recommended Amount</label>
					    <div class="col-sm-6">
					        <input type="text" name="recommended_amount" class="form-control" value="<?php echo e($fee->recommended_amount); ?>" placeholder="Recommended Amount">
					    </div>
					</div>
				</div>
          		<?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_general_setting')): ?>
            		<div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat pull-right">Update</button>
                    </div>
          		<?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/cards/fees.blade.php ENDPATH**/ ?>