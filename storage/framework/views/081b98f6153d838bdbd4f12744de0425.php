

<?php $__env->startSection('title', 'Edit Language'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/sweetalert/sweetalert.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Edit Language</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="<?php echo e(url('admin/settings/edit_language/'.$result->id)); ?>" class="form-horizontal" enctype="multipart/form-data" id="edit_language_form">
          <?php echo e(csrf_field()); ?>


          <input type="hidden" value="<?php echo e($result->id); ?>" name="id" id="id">

          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="name">Name</label>
              <div class="col-sm-6">
                <input type="text" name="name" class="form-control" value="<?php echo e($result->name); ?>" placeholder="name" id="name">
                <?php if($errors->has('name')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('name')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="short_name">Short Name</label>
              <div class="col-sm-6">
                <input type="text" name="short_name" class="form-control" value="<?php echo e($result->short_name); ?>" placeholder="short name" id="short_name">
                <?php if($errors->has('short_name')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('short_name')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-3 control-label">Flag</label>
              <div class="col-sm-6">
                <input type="file" name="flag" class="form-control input-file-field" data-rel="<?php echo e(isset($result->flag) ? $result->flag : ''); ?>" id="flag"
                value="<?php echo e(isset($result->flag) ? $result->flag : ''); ?>">
                <strong class="text-danger"><?php echo e($errors->first('flag')); ?></strong>
                <div class="clearfix"></div>
                <small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(120,80)); ?></strong></small>

                <?php if(isset($result->flag)): ?>
                  <div class="setting-img">
                      <img src='<?php echo e(url('public/uploads/languages-flags/'.$result->flag)); ?>' width="120" height="80" id="language-flag-preview">
                      <span class="remove_language_preview" id="flag_preview"></span>
                  </div>
                <?php else: ?>
                  <div class="setting-img">
                    <img src='<?php echo e(url('public/uploads/userPic/default-image.png')); ?>' width="120" height="80" id="language-demo-flag-preview">
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="status">Status</label>
                <div class="col-sm-6">
                    <select class="select2" name="status" id="status">
                        <option value='Active' <?php echo e($result->status == 'Active' ? 'selected':""); ?>>Active</option>
                        <option value='Inactive' <?php echo e($result->status == 'Inactive' ? 'selected':""); ?>>Inactive</option>
                    </select>
                </div>
            </div>
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="<?php echo e(url('admin/settings/language')); ?>">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right">&nbsp; Update &nbsp;</button>
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

<!-- sweetalert -->
<script src="<?php echo e(asset('public/backend/sweetalert/sweetalert.min.js')); ?>" type="text/javascript"></script>

<!-- read-file-on-change -->
<?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

  $(window).on('load',function() {
    $(".select2").select2({});
  });

  // preview language logo on change
  $(document).on('change','#flag', function()
  {
      let orginalSource = '<?php echo e(url('public/uploads/userPic/default-image.png')); ?>';
      let flag = $('#flag').attr('data-rel');
      if (flag != '') {
        readFileOnChange(this, $('#language-flag-preview'), orginalSource);
        $('.remove_language_preview').remove();
      }
      readFileOnChange(this, $('#language-demo-flag-preview'), orginalSource);
  });

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
  });

  $('#edit_language_form').validate({
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

  $(document).ready(function()
  {
    $('#flag_preview').click(function()
    {
      var flag = $('#flag').attr('data-rel');
      var language_id = $('#id').val();

      if(flag)
      {
        $.ajax(
        {
          headers:
          {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type : "POST",
          url : SITE_URL+"/admin/settings/language/delete-flag",
          // async : false,
          data: {
            'flag' : flag,
            'language_id' : language_id,
          },
          dataType : 'json',
          success: function(reply)
          {
            if (reply.success == 1){
                swal({title: "Deleted!", text: reply.message, type: "success"},
                   function(){
                       location.reload();
                   }
                );
            }else{
                alert(reply.message);
                location.reload();
            }
          }
        });
      }
    });
  });
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/languages/edit.blade.php ENDPATH**/ ?>