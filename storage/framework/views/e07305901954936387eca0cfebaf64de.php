
<?php $__env->startSection('title', 'Social Settings'); ?>

<?php $__env->startSection('page_content'); ?>

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.settings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Social Links Form</h3>
                </div>

                <form action="<?php echo e(url('admin/settings/social_links')); ?>" method="post" class="form-horizontal"
                      id="social_links">
                <?php echo csrf_field(); ?>


                <!-- box-body -->
                    <div class="box-body">
                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!-- facebook -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo e(str_replace('_',' ',ucfirst($row->name))); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="<?php echo e($row->name); ?>" class="form-control"
                                           value="<?php echo e($row->url); ?>">

                                    <?php if($errors->has($row->name)): ?>
                                        <span class="help-block">
		                          <strong class="text-danger"><?php echo e($errors->first($row->name)); ?></strong>
		                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                    <!-- /.box-body -->

                    <!-- box-footer -->
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_social_links')): ?>
                        <div class="box-footer">
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

    $('#social_links').validate({
        rules: {
            facebook: {
                // required: true,
                url: true,
            },
            google_plus: {
                // required: true,
                url: true,
            },
            twitter: {
                // required: true,
                url: true,
            },
            linkedin: {
                // required: true,
                url: true,
            },
            pinterest: {
                // required: true,
                url: true,
            },
            youtube: {
                // required: true,
                url: true,
            },
            instagram: {
                // required: true,
                url: true,
            },
        },
    });

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/settings/social.blade.php ENDPATH**/ ?>