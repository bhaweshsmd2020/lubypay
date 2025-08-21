<?php $__env->startSection('title', 'Email Templates'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- wysihtml5 -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')); ?>">
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_content'); ?>
    <div class="row">
      <div class="col-md-3">
         <?php echo $__env->make('admin.common.notification_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
                <?php if($tempId == 1): ?>
                    Deposit Template
                <?php elseif($tempId == 2): ?>
                    Payout Template
                <?php elseif($tempId == 3): ?>
                    Send Money Template
                <?php elseif($tempId == 4): ?>
                    Request Money Receiver Template
                <?php elseif($tempId == 5): ?>
                    Exchange Money Template
                <?php elseif($tempId == 6): ?>
                    Gift Card Template
                <?php elseif($tempId == 7): ?>
                    Topup Template
                <?php elseif($tempId == 12): ?>
                    Approve Request Money Sender Template
                <?php elseif($tempId == 8): ?>
                    Approve Request Money Receiver Template
                <?php elseif($tempId == 13): ?>
                    Reject Request Money Sender Template
                <?php elseif($tempId == 9): ?>
                    Reject Request Money Receiver Template
                <?php elseif($tempId == 10): ?>
                    Money Received Template
                <?php elseif($tempId == 11): ?>
                    Request Money Sender Template
                <?php elseif($tempId == 14): ?>
                    QR Store Payment Template
                <?php elseif($tempId == 15): ?>
                    New Store Template
                <?php elseif($tempId == 16): ?>
                    New Product Template
                <?php elseif($tempId == 17): ?>
                    New Payment Template
                <?php elseif($tempId == 18): ?>
                    Photo Verification Template
                <?php elseif($tempId == 19): ?>
                    Address Verification Template
                <?php elseif($tempId == 20): ?>
                    Identity Verification Template
                <?php elseif($tempId == 21): ?>
                    Payout Request Template
                <?php elseif($tempId == 22): ?>
                    Ticket Reply Template
                <?php elseif($tempId == 23): ?>
                    Manual KYC Template
                <?php elseif($tempId == 24): ?>
                    Auto KYC Template
                elseif($tempId == 25)
                    Create Ticket Template
                <?php endif; ?>
            </h3>
          </div>

        <form action='<?php echo e(url('admin/notification/template_update/'.$tempId)); ?>' method="post" id="template">
            <?php echo csrf_field(); ?>


            <!-- /.box-header -->
            <div class="box-body">
                <div class="box-group" id="accordion">
                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="panel box box-primary">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo e($language->id); ?>" aria-expanded="false" class="collapsed">
                                    <?php echo e($language->name); ?>

                                  </a>
                                </h4>
                            </div>
                            
                            <?php $__currentLoopData = $temp_Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $temp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($language->id == $temp->language_id): ?>
                                    <div id="collapse<?php echo e($language->id); ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input class="form-control" name="<?php echo e($language->short_name); ?>[title]" type="text" value="<?php echo e($temp->title); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Sub Header</label>
                                                <input class="form-control" name="<?php echo e($language->short_name); ?>[subheader]" type="text" value="<?php echo e($temp->subheader); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Content</label>
                                                <input class="form-control" name="<?php echo e($language->short_name); ?>[content]" type="text" value="<?php echo e($temp->content); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="pull-right">
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_email_template')): ?>
                      <button type="submit" class="btn btn-primary btn-flat" id="email_edit">
                          <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="email_edit_text">Update</span>
                      </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
          <!-- /.box-footer -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<!-- wysihtml5 -->
<script src="<?php echo e(asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')); ?>" type="text/javascript"></script>

<script>
    $(function () {
      $(".editor").wysihtml5();
    });

    $('#template').validate({
        rules: {
            subject: {
                required: true
            },
            content:{
               required: true
            }
        },
        submitHandler: function(form)
        {
            $("#email_edit").attr("disabled", true);
            $(".fa-spin").show();
            $("#email_edit_text").text('Updating...');
            form.submit();
        }
    });
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/notification_templates/index.blade.php ENDPATH**/ ?>