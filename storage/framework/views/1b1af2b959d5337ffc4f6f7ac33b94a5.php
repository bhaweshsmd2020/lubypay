
<?php $__env->startSection('title', 'Edit Page'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- summernote -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/editor/summernote.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<?php
    if($language->id == '1'){
        $page_name     = $page->string;
        $page_content  = $page->string_content;
    }if($language->id == '2'){
        $page_name     = $page->en;
        $page_content  = $page->en_content;
    }if($language->id == '3'){
        $page_name     = $page->es;
        $page_content  = $page->es_content;
    }if($language->id == '4'){
        $page_name     = $page->fr;
        $page_content  = $page->fr_content;
    }if($language->id == '5'){
        $page_name     = $page->haiti;
        $page_content  = $page->haiti_content;
    }if($language->id == '6'){
        $page_name     = $page->pt;
        $page_content  = $page->pt_content;
    }
?>

  <div class="row">
    <div class="col-md-3">
       <?php echo $__env->make('admin.common.settings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="col-md-9">
      <div class="box box-default">
        <div class="box-body">
          <div class="row">
            <div class="col-md-10">
             <div class="top-bar-title padding-bottom">Edit Page</div>
            </div>
            <div class="col-md-2">
              <?php if(Common::has_permission(Auth::guard('admin')->user()->id, 'manage_page')): ?>
                <div class="top-bar-title padding-bottom">
                <a href="<?php echo e(url("admin/settings/pages")); ?>" class="btn btn-block btn-default btn-flat btn-border-orange">Pages</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="box">
      <div class="box-body">
        <!-- /.box-header -->
        <form action="<?php echo e(url('admin/settings/page/update')); ?>" method="post" id="page" class="form-horizontal" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>


            <input class="form-control" name="id" value="<?php echo e($page->id); ?>" type="hidden">
            
            <input class="form-control" name="language_id" value="<?php echo e($language->id); ?>" type="hidden">
            
            <div class="row">
                <div class="col-md-3">
                    <ul class="nav nav-pills nav-stacked">
                        <?php $__currentLoopData = $all_languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $all_lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <li class="<?php if($all_lang->id == $language->id) { echo 'active'; } ?>">
                            <a data-spinner="true" href='<?php echo e(url("admin/settings/page/edit/$page->id/$all_lang->id")); ?>'><?php echo e($all_lang->language); ?></a>
                          </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="col-md-9">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label required" for="inputEmail3">Name</label>
                      <div class="col-sm-10">
                        <input class="form-control" name="name" value="<?php echo e($page_name); ?>" type="text">
                          <?php if($errors->has('name')): ?>
                              <span class="error">
                                  <strong><?php echo e($errors->first('name')); ?></strong>
                              </span>
                          <?php endif; ?>
                      </div>
                    </div>
        
                    <div class="form-group">
                      <label class="col-sm-2 control-label required" for="inputEmail3">Content</label>
                      <div class="col-sm-10">
                        <textarea id="content" class="form-control" name="content" placeholder="Content" rows="10" cols="80" ><?php echo e($page_content); ?></textarea>
                          <?php if($errors->has('content')): ?>
                              <span class="error">
                                  <strong><?php echo e($errors->first('content')); ?></strong>
                              </span>
                          <?php endif; ?>
                      </div>
                    </div>
        
                    <div class="form-group">
                        <label class="col-sm-2 control-label required">Position</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label style="margin-right: 15px">
                                    <input type="checkbox" name="header" <?= in_array('header',$page->position)?'checked="true"':'' ?> class="position" id="header">
                                    Header
                                </label>
                                <label>
                                    <input type="checkbox" name="footer" <?= in_array('footer',$page->position)?'checked="true"':'' ?> class="position" id="footer">
                                    Footer
                                </label>
                            </div>
                            <div id="error-message"></div>
                        </div>
                    </div>
        
                    <div class="form-group">
                      <label class="col-sm-2 control-label required" for="inputEmail3">Status</label>
                      <div class="col-sm-10">
                        <select class="select2" name="status">
                            <option value="active" <?= ( $page->status == 'active' ) ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ( $page->status == 'inactive' ) ? 'selected' : '' ?>>Inactive</option>
                          </select>
                            <?php if($errors->has('status')): ?>
                              <span class="error">
                                  <strong><?php echo e($errors->first('status')); ?></strong>
                              </span>
                            <?php endif; ?>
                      </div>
                    </div>
        
                  </div>
                  <!-- /.box-body -->
        
                  <div class="box-footer">
                    <a href="<?php echo e(url("admin/settings/pages")); ?>" class="btn btn-danger btn-flat">Cancel</a>
                    <button class="btn btn-primary pull-right btn-flat" type="submit">Update</button>
                  </div>
                  <!-- /.box-footer -->
                </div>
            </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<script src="<?php echo e(asset('public/dist/editor/summernote.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">
    //summernote.js note script
    // $(function()
    $(window).on('load',function()
    {
        $(".note-group-select-from-files").hide();
        $('#content').summernote({
            tabsize: 2,
            height: 150,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'italic', 'underline']],
              ['fontname', ['fontname']],
              ['fontsize', ['fontsize']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['table', ['table']],
              ['insert', ['link', 'hr','picture']]
            ],
        });
    });

    $(".select2").select2();

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

    $('#page').validate({
        rules: {
            name: {
                required: true,
            },
            content:{
               required: true,
            },
        }
    });

    // Multiple Checkboxes Validation (on submit)
    $(document).ready(function()
    {
      $('form').submit(function()
      {
        checkPosition();
      });
    });

    // Multiple Checkboxes Validation (on change)
    $(document).on('change','.position',function()
    {
        checkPosition();
    });

    function checkPosition()
    {
        var checkedLength = $('input[type=checkbox]:checked').length;
        if(checkedLength > 1)
        {
          $('#error-message').html('');
          return true;
        }
        else
        {
          $('#error-message').addClass('error').html('Please check at least one box.').css("font-weight", "bold");
          return false;
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/test/resources/views/admin/pages/edit.blade.php ENDPATH**/ ?>