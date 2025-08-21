

<?php $__env->startSection('title', 'Edit Country'); ?>

<?php $__env->startSection('page_content'); ?>

  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Edit Country</h3> 
        </div>

        <!-- form start -->
        <form method="POST" action="<?php echo e(url('admin/settings/edit_country/'.$result->id)); ?>" class="form-horizontal" id="edit_country_form">
          <?php echo e(csrf_field()); ?>


          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="short_name">Short Name</label>
              <div class="col-sm-6">
                <input type="text" name="short_name" class="form-control" placeholder="short name" id="short_name" value="<?php echo e($result->short_name); ?>">
                <?php if($errors->has('short_name')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('short_name')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="name">Long Name</label>
              <div class="col-sm-6">
                <input type="text" name="name" class="form-control" placeholder="long name" id="name" value="<?php echo e($result->name); ?>">
                <?php if($errors->has('name')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('name')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="iso3">ISO3</label>
              <div class="col-sm-6">
                <input type="text" name="iso3" class="form-control" placeholder="iso3" id="iso3" value="<?php echo e($result->iso3); ?>">
                <?php if($errors->has('iso3')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('iso3')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="number_code">Number Code</label>
              <div class="col-sm-6">
                <input type="text" name="number_code" class="form-control" placeholder="number code" id="number_code" value="<?php echo e($result->number_code); ?>">
                <?php if($errors->has('number_code')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('number_code')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="phone_code">Phone Code</label>
              <div class="col-sm-6">
                <input type="text" name="phone_code" class="form-control" placeholder="phone code" id="phone_code" value="<?php echo e($result->phone_code); ?>">
                <?php if($errors->has('phone_code')): ?>
                <span class="help-block">
                  <strong class="text-danger"><?php echo e($errors->first('phone_code')); ?></strong>
                </span>
                <?php endif; ?>
              </div>
            </div>
             <div class="form-group">
              <label class="col-sm-3 control-label" for="phone_code">Status</label>
              <div class="col-sm-6">
                <select class="form-control" name="status" id="status">
                <option value="1" <?php if($result->status ==1): ?> selected <?php endif; ?>>Active</option>
                <option value="0"  <?php if($result->status ==0): ?>  selected <?php endif; ?>>Inactive</option>
                </select>
              </div>
            </div>

          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="<?php echo e(url('admin/settings/country')); ?>">Cancel</a>
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
<!-- /dist -->

<script type="text/javascript">

  jQuery.validator.addMethod("letters_with_spaces", function(value, element)
  {
    return this.optional(element) || /^[A-Za-z ]+$/i.test(value); //only letters
  }, "Please enter letters only!");

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
  });

  $('#edit_country_form').validate({
    rules: {
      short_name: {
        required: true,
        maxlength: 2,
        lettersonly: true,
      },
      name: {
        required: true,
        // letters_with_spaces: true,
      },
      iso3: {
        required: true,
        maxlength: 3,
        lettersonly: true,
      },
      number_code: {
        required: true,
        digits: true
      },
      phone_code: {
        required: true,
        digits: true
      },
    },
    messages: {
      short_name: {
        lettersonly: "Please enter letters only!",
      },
      iso3: {
        lettersonly: "Please enter letters only!",
      },
    },
  });

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/countries/edit.blade.php ENDPATH**/ ?>