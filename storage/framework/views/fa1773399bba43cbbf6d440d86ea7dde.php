
<?php $__env->startSection('title', 'Edit Services'); ?>

<?php $__env->startSection('page_content'); ?>

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Services</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/services_edit/' . $details->id)); ?>" method="post" class="form-horizontal" id="api-credentials" enctype="multipart/form-data" >
                    <?php echo csrf_field(); ?>


                    <!-- box-body -->
                    <div class="box-body">
                         <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Name" name="name" type="text" id="name" value="<?php echo e($details->name??''); ?>">
                                         <?php if($errors->has('name')): ?>
                                        <span class="help-block">
                                          <strong class="text-danger"><?php echo e($errors->first('name')); ?></strong>
                                        </span>
                                <?php endif; ?>
                            </div>
                        </div>
                       <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Page
                                        </label>
                                        <div class="col-sm-8">
                                         <select class="form-control" name="page" id="page">
                                            <?php if(!empty($app_pages)): ?>
                                               <?php $__currentLoopData = $app_pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                 <option value="<?php echo e($value->app_page); ?>"  <?php if($details->page==$value->app_page): ?> Selected  <?php endif; ?>><?php echo e($value->page_name); ?></option>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                         </select>    
                                         <?php if($errors->has('page')): ?>
                                         <span class="help-block">
                                          <strong class="text-danger"><?php echo e($errors->first('page')); ?></strong>
                                        </span>
                                <?php endif; ?>
                            </div>
                        </div>
                       <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                        Position
                                        </label>
                                        <div class="col-sm-8">
                                         <select class="form-control" name="position" id="position">
                                            <option value="Top"  <?php if($details->position=="Top"): ?> Selected  <?php endif; ?>>Top</option>
                                            <option value="Bottom"  <?php if($details->position=="Bottom"): ?> Selected  <?php endif; ?>>Bottom</option>
                                         </select>    
                                         <?php if($errors->has('position')): ?>
                                        <span class="help-block">
                                          <strong class="text-danger"><?php echo e($errors->first('position')); ?></strong>
                                        </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Status
                                        </label>
                                        <div class="col-sm-8">
                                         <select class="form-control" name="status" id="status">
                                              <option value="Active" <?php if($details->status=="Active"): ?> Selected  <?php endif; ?> >Active</option>
                                              <option value="Inactive" <?php if($details->status=="Inactive"): ?> Selected  <?php endif; ?>>Inactive</option>
                                              <option value="ComingSoon" <?php if($details->status=="ComingSoon"): ?> Selected  <?php endif; ?>>Coming Soon</option>
                                              <option value="ServiceDown" <?php if($details->status=="ServiceDown"): ?> Selected  <?php endif; ?>>Service Down</option>
                                         </select>    
                                         <?php if($errors->has('status')): ?>
                                        <span class="help-block">
                                          <strong class="text-danger"><?php echo e($errors->first('status')); ?></strong>
                                        </span>
                                <?php endif; ?>
                            </div>
                        </div>
                       <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Sorting
                                        </label>
                                        <div class="col-sm-8">
                                       <input class="form-control" placeholder="Sorting" name="sorting" type="number" id="name" min=1 value="<?php echo e($details->sorting); ?>">
                                         <?php if($errors->has('sorting')): ?>
                                        <span class="help-block">
                                          <strong class="text-danger"><?php echo e($errors->first('sorting')); ?></strong>
                                        </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update First Name" name="image" type="file" id="image" value="">
                                            <span><?php echo e($details->image); ?></span>
                                         <?php if($errors->has('image')): ?>
                                        <span class="help-block">
                                          <strong class="text-danger"><?php echo e($errors->first('image')); ?></strong>
                                        </span>
                                       <?php endif; ?>
                            </div>
                        </div>
                    <!-- box-footer -->
                      <div class="box-footer col-sm-8"></div>
                    <div class="box-footer col-sm-2">
                          <a href="<?php echo e(url('admin/settings/services/view')); ?>" class="btn btn-danger btn-flat pull-right" type="submit">Cancel</a>
                    </div>
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_api_credentials')): ?>
                        <div class="box-footer col-sm-2">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">

$.validator.setDefaults({
    highlight: function(element) {
        $(element).parent('div').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).parent('div').removeClass('has-error');
    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    }
});

$('#api-credentials').validate({
    rules: {
        name: {
            required: true,
        },
        page: {
            required: true,
        },
         position: {
            required: true,
        },
         status: {
            required: true,
        },
        sorting:{
            required: true,
        },
    
    },
});


</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/services/edit.blade.php ENDPATH**/ ?>