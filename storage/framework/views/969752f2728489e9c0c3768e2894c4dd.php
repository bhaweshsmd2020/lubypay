<?php $__env->startSection('title', 'Add Payment Method'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Add payment Method</h3>
                </div>
                <form action="<?php echo e(url('admin/store-paymentmethod')); ?>" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Name
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Name" id="name" name="name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description" required></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="icon">
                                Icon
                            </label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Icon" name="icon" type="file" id="icon" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="has_permission">
                                Has permission
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control select2" id="has_permission" name="has_permission[]" multiple required>
                                    <option value="Subscription">Subscription</option>
                                    <option value="Deposit">Deposit</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_paymentmethods')): ?>
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/paymentmethods')); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Create</button>
                        </div>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#has_permission').select2({
                placeholder: "Select permissions",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/paymentmethods/create.blade.php ENDPATH**/ ?>