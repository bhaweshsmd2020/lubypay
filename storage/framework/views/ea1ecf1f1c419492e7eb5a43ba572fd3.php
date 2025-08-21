
<?php $__env->startSection('title', 'Add App Pages'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-md-9">
    
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="top-bar-title padding-bottom pull-left">Edit App Pages</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="<?php echo e(url('admin/apppages/update/'. $page->id)); ?>" method="post" class="form-horizontal" id="api-credentials" enctype="multipart/form-data" >
                                <?php echo csrf_field(); ?>
        
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">App Page</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" placeholder="App Page" name="app_page" type="text" id="app_page" value="<?php echo e($page->app_page); ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">Page Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" placeholder="Page Name" name="page_name" type="text" id="page_name" value="<?php echo e($page->page_name); ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">Status</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="status" id="status">
                                            <option value="Active" <?php if($page->status == 'Active'): ?> selected <?php endif; ?>>Active</option>
                                            <option value="Inactive" <?php if($page->status == 'Inactive'): ?> selected <?php endif; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="box-footer col-sm-9"></div>
                                    <div class="box-footer col-sm-3">
                                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_api_credentials')): ?>
                                            <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(url('admin/apppages')); ?>" class="btn btn-danger btn-flat pull-right" style="margin-right: 10px;">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/app_pages/edit.blade.php ENDPATH**/ ?>