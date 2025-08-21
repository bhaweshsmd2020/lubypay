<?php $__env->startSection('title', 'Add Survey'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Add Survey</h3>
                </div>
                <form action="<?php echo e(url('admin/store-survey')); ?>" class="form-horizontal" method="POST" id="user_form">
                    <?php echo csrf_field(); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Url
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Url" id="url" name="url">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="user_type">
                                Send To
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="user_type" name="user_type">
                                    <option value="1">Ewallet User</option>
                                    <option value="2">Merchant</option>
                                    <option value="3">Both</option>
                                </select>
                            </div>
                        </div>
                        
                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label require" for="message">
                                    Message (<?php echo e($language->name); ?>)
                                </label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" placeholder="Message" id="message" name="message_<?php echo e($language->short_name); ?>"></textarea>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                        
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level')): ?>
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/survey')); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Create</button>
                        </div>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/survey/create.blade.php ENDPATH**/ ?>