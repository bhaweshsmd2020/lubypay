<?php $__env->startSection('title', 'Edit Ambassador Code'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Ambassador Code</h3>
                </div>
                <form action="<?php echo e(url('admin/update-ambassador-code', $ambassadorcode->id)); ?>" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="created_for">
                                Select Ambassador
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="created_for" name="created_for" required>
                                    <?php $__currentLoopData = $ambassadors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ambassador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($ambassador->id); ?>" <?php if($ambassadorcode->created_for == $ambassador->id): ?> selected <?php endif; ?>><?php echo e($ambassador->first_name); ?> <?php echo e($ambassador->last_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="code">
                                Code
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Code" id="code" name="code" value="<?php echo e($ambassadorcode->code); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="fixed_discount">
                                Fixed Discount
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Fixed Discount" id="fixed_discount" name="fixed_discount" value="<?php echo e($ambassadorcode->fixed_discount); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="percentage_discount">
                                Percentage Discount
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Percentage Discount" id="percentage_discount" name="percentage_discount" value="<?php echo e($ambassadorcode->percentage_discount); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="total_uses">
                                Total Code Uses
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Total Code Uses" id="total_uses" name="total_uses" value="<?php echo e($ambassadorcode->total_uses); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="individual_uses">
                                Individual Code Uses
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Individual Code Uses" id="individual_uses" name="individual_uses" value="<?php echo e($ambassadorcode->individual_uses); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="expires_on">
                                Expire On
                            </label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" placeholder="Expire On" id="expires_on" name="expires_on" value="<?php echo e($ambassadorcode->expires_on); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1" <?php if($ambassadorcode->status == '1'): ?> selected <?php endif; ?>>Active</option>
                                    <option value="0" <?php if($ambassadorcode->status == '0'): ?> selected <?php endif; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description"><?php echo e($ambassadorcode->description); ?></textarea>
                            </div>
                        </div>
                    </div>
                        
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_ambassador_codes')): ?>
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/ambassador-codes')); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/ambassadorcodes/edit.blade.php ENDPATH**/ ?>