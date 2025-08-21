

<?php $__env->startSection('title', 'Add Language'); ?>

<?php $__env->startSection('page_content'); ?>

  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Add Language</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="<?php echo e(url('admin/settings/add_language')); ?>" class="form-horizontal" enctype="multipart/form-data" id="add_language_form">
          <?php echo e(csrf_field()); ?>


          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="name">Name</label>
              <div class="col-sm-6">
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" placeholder="name" id="name">
                <?php if($errors->has('name')): ?>
                <span class="error">
                  <strong class="text-danger"><?php echo e($errors->first('name')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="short_name">Short Name</label>
              <div class="col-sm-6">
                <input type="text" name="short_name" class="form-control" value="<?php echo e(old('short_name')); ?>" placeholder="short name" id="short_name">
                <?php if($errors->has('short_name')): ?>
                <span class="error">
                  <strong class="text-danger"><?php echo e($errors->first('short_name')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="flag">Flag</label>
              <div class="col-sm-6">
                <input type="file" name="flag" class="form-control input-file-field" id="language-flag">
                <?php if($errors->has('flag')): ?>
                  <span class="error">
                    <strong class="text-danger"><?php echo e($errors->first('flag')); ?></strong>
                  </span>
                <?php endif; ?>
                <div class="clearfix"></div>
                <small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(120,80)); ?></strong></small>
                <div class="setting-img">
                  <img src='<?php echo e(url('public/uploads/userPic/default-image.png')); ?>' width="120" height="80" id="language-flag-demo-preview">
                </div>
              </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="status">Status</label>
                <div class="col-sm-6">
                    <select class="select2" name="status" id="status">
                        <option value='Active'>Active</option>
                        <option value='Inactive'>Inactive</option>
                    </select>
                </div>
            </div>
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="<?php echo e(url('admin/settings/language')); ?>">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right">&nbsp; Add &nbsp;</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<!-- jquery.validate additional-methods -->
<script src="<?php echo e(asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js')); ?>" type="text/javascript"></script>

<!-- read-file-on-change -->
<?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

  $(window).on('load',function() {
    $(".select2").select2({});
  });

  // preview language flag on change
  $(document).on('change','#language-flag', function()
  {
      let orginalSource = '<?php echo e(url('public/uploads/userPic/default-image.png')); ?>';
      readFileOnChange(this, $('#language-flag-demo-preview'), orginalSource);
  });

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
  });

  $('#add_language_form').validate({
    rules: {
      name: {
        required: true,
      },
      short_name: {
        required: true,
        maxlength: 2,
        lettersonly: true,
      },
      flag: {
        extension: "png|jpg|jpeg|gif|bmp",
      },
    },
    messages: {
      short_name: {
        lettersonly: "Please enter letters only.",
      },
      flag: {
        extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
      },
    },
  });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/languages/add.blade.php ENDPATH**/ ?>