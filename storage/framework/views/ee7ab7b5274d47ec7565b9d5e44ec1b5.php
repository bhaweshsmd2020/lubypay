<?php $__env->startSection('title', 'Edit Maintenance Settings'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Maintenance Settings</h3>
                </div>
                <form action="<?php echo e(url('admin/update-maintainance-settings', $setting->id)); ?>" class="form-horizontal" method="POST" id="user_form">
                    <?php echo csrf_field(); ?>
                    <div class="box-body">
                        <!--<div class="form-group">-->
                        <!--    <label class="col-sm-3 control-label require" for="subject">-->
                        <!--        Subject-->
                        <!--    </label>-->
                        <!--    <div class="col-sm-6">-->
                        <!--        <input type="text" class="form-control" value="<?php echo e($setting->subject); ?>" placeholder="Subject" id="subject" name="subject">-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="user_type">
                                Send To
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="user_type" name="user_type">
                                    <option value="1" <?php if($setting->user_type == '1'): ?> selected <?php endif; ?>>Ewallet User</option>
                                    <option value="2" <?php if($setting->user_type == '2'): ?> selected <?php endif; ?>>Merchant</option>
                                    <option value="3" <?php if($setting->user_type == '3'): ?> selected <?php endif; ?>>Both</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="date">
                                Date
                            </label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" value="<?php echo e($setting->date); ?>" placeholder="Date" id="date" name="date">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="from_time">
                                From Time
                            </label>
                            <div class="col-sm-6">
                                <input type="time" class="form-control" value="<?php echo e($setting->from_time); ?>" placeholder="From Time" id="from_time" name="from_time">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="to_time">
                                To Time
                            </label>
                            <div class="col-sm-6">
                                <input type="time" class="form-control" value="<?php echo e($setting->to_time); ?>" placeholder="To Time" id="to_time" name="to_time">
                            </div>
                        </div>
                        
                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $message = 'message_'.$language['short_name'];
                            ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label require" for="message">
                                    Message (<?php echo e($language->name); ?>)
                                </label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" placeholder="Message" id="message" name="message_<?php echo e($language->short_name); ?>"><?php echo e($setting->$message); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                        
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level')): ?>
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/maintainance-settings')); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/maintainance/edit.blade.php ENDPATH**/ ?>