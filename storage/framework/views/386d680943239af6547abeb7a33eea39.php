<?php $__env->startSection('title', 'Edit Subscription'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Subscription</h3>
                </div>
                <form action="<?php echo e(url('admin/update-subscription', $subscription->id)); ?>" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Title
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Title" id="title" name="title" value="<?php echo e($subscription->title); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description" required><?php echo e($subscription->description); ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Price
                            </label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" placeholder="Price" id="price" name="price" value="<?php echo e($subscription->price); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="icon">
                                Icon
                            </label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Icon" name="icon" type="file" id="icon">
                                <?php if($subscription->icon): ?>
                                    <img src="<?php echo e($subscription->icon); ?>" style="width: auto; height: 50px; margin-top: 15px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="duration">
                                Duration
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="duration" name="duration" required>
                                    <option value="monthly" <?php if($subscription->duration == '1'): ?> selected <?php endif; ?>>Monthly</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="featured">
                                Featured
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="featured" name="featured" required>
                                    <option value="0" <?php if($subscription->featured == '1'): ?> selected <?php endif; ?>>No</option>
                                    <option value="1" <?php if($subscription->featured == '1'): ?> selected <?php endif; ?>>Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1" <?php if($subscription->status == '1'): ?> selected <?php endif; ?>>Active</option>
                                    <option value="0" <?php if($subscription->status == '0'): ?> selected <?php endif; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_subscriptions')): ?>
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/subscriptions')); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/subscription/edit.blade.php ENDPATH**/ ?>