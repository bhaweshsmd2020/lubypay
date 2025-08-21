
<?php $__env->startSection('title', 'Partner Settings'); ?>
<?php $__env->startSection('page_content'); ?>

<!-- Main content -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">Partner Settings Form</h3>
            </div>

            <form action="<?php echo e(url('admin/partner/update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

				<div class="box-body">
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Partner ID</label>
					    <div class="col-sm-6">
					        <input type="text" name="partner_id" class="form-control" value="<?php echo e($card['partner_id']); ?>" placeholder="Partner ID">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Card URL</label>
					    <div class="col-sm-6">
					        <input type="text" name="card_url" class="form-control" value="<?php echo e($card['card_url']); ?>" placeholder="Card URL">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Card Key</label>
					    <div class="col-sm-6">
					        <input type="text" name="card_key" class="form-control" value="<?php echo e($card['card_key']); ?>" placeholder="Card Key">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Card Secret</label>
					    <div class="col-sm-6">
					        <input type="text" name="card_secret" class="form-control" value="<?php echo e($card['card_secret']); ?>" placeholder="Card Secret">
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/cards/config.blade.php ENDPATH**/ ?>