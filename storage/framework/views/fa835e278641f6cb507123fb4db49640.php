<?php $__env->startSection('title', 'Edit Transaction Type'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Transaction Type</h3>
                </div>
                <form action="<?php echo e(url('admin/update-transactiontype', $transactiontype->id)); ?>" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Name
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Name" id="name" name="name" value="<?php echo e($transactiontype->name); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description"><?php echo e($transactiontype->description); ?></textarea>
                            </div>
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="orderby">
                                Order By
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Order By" id="orderby" name="orderby" value="<?php echo e($transactiontype->orderby); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="type">
                                Type
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="1" <?php if($transactiontype->type == '1'): ?> selected <?php endif; ?>>Transaction</option>
                                    <option value="2" <?php if($transactiontype->type == '2'): ?> selected <?php endif; ?>>Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1" <?php if($transactiontype->status == '1'): ?> selected <?php endif; ?>>Active</option>
                                    <option value="0" <?php if($transactiontype->status == '0'): ?> selected <?php endif; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_transactiontypes')): ?>
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/transactiontypes')); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#has_permission').select2({
                placeholder: "Select permissions"
            });
        });
    </script>    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/transactiontypes/edit.blade.php ENDPATH**/ ?>