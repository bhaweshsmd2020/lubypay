
<?php $__env->startSection('title', 'Promotion Detail'); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <form class="form-horizontal">
                    <div class="box-header with-border text-center">
                        <h3 class="box-title">Promotion Detail</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Merchants</label>
                            <div class="col-sm-6">
                                <select class="select2" name="user_type" id="user_type" required="">
                                    <option value="All" <?php if($promotion->user_type == 'All'): ?> selected <?php endif; ?>>All</option>
                                    <?php $__currentLoopData = $merchants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $merchant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($merchant->id); ?>" <?php if($promotion->user_type == $merchant->id): ?> selected <?php endif; ?>><?php echo e($merchant->first_name); ?> <?php echo e($merchant->last_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Title</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Title" name="title" type="text" id="title" value="<?php echo e($promotion->title); ?>" required="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Subject</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Subject" name="subject" type="text" id="subject" value="<?php echo e($promotion->subject); ?>" required="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Type of Notification</label>
                            <div class="col-sm-6">
                                <select class="select2" name="type" id="type" required="">
                                    <option value="Email" <?php if($promotion->type == 'Email'): ?> selected <?php endif; ?>>Email</option>
                                    <option value="Notification" <?php if($promotion->type == 'Notification'): ?> selected <?php endif; ?>>Notification</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Image</label>
                            <div class="col-sm-6">
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Select Redirect Type</label>
                            <div class="col-sm-6">
                                <select class="select2" name="app_redirect" id="app_redirect" required="">
                                    <option value='0' <?php if($promotion->app_redirect == '0'): ?> selected <?php endif; ?>>None</option>
                                    <option value='1' <?php if($promotion->app_redirect == '1'): ?> selected <?php endif; ?>>App Page</option>
                                    <option value='2' <?php if($promotion->app_redirect == '2'): ?> selected <?php endif; ?>>Redirect URL</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">App Page</label>
                            <div class="col-sm-6">
                                <select class="select2" name="app_page" id="app_page">
                                    <option>Select App Page</option>
                                    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value='<?php echo e($page->id); ?>' <?php if($promotion->app_page == $page->id): ?> selected <?php endif; ?>><?php echo e($page->page_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Redirect URL</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Redirect URL" name="redirect_url" type="text" id="redirect_url" value="<?php echo e($promotion->redirect_url); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Description</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" name="description" type="text" id="description" required=""><?php echo e($promotion->description); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Language</label>
                            <div class="col-sm-6">
                                <select class="select2" name="language" id="language" required>
                                    <?php $__currentLoopData = $language; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>"  <?php if($promotion->language == $value->id): ?> selected <?php endif; ?>><?php echo e($value->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/promotions')); ?>">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>
    <script type="text/javascript">
        $(function () {
            $(".select2").select2({
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/promotions/edit.blade.php ENDPATH**/ ?>