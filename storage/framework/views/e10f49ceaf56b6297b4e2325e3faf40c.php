
<?php $__env->startSection('title', 'Email Templates'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- Quill Editor CSS -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <style>
    .quill-editor {
        background: white;
        min-height: 300px;
    }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
<div class="row">
  <div class="col-md-3">
     <?php echo $__env->make('admin.common.mail_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </div>
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?php switch($tempId):
              case (1): ?> Compose Transferred Template <?php break; ?>
              <?php case (2): ?> Compose Received Template <?php break; ?>
              <?php case (3): ?> Compose Bank Transfer Template <?php break; ?>
              <?php case (21): ?> Compose Identity/Address Verification Template <?php break; ?>
              <?php case (19): ?> Compose 2-Factor Authentication Template <?php break; ?>
              <?php case (4): ?> Compose Request Creation Template <?php break; ?>
              <?php case (5): ?> Compose Request Acceptance Template <?php break; ?>
              <?php case (6): ?> Compose Transfer Status Change Template <?php break; ?>
              <?php case (7): ?> Compose Bank Transfer Status Change Template <?php break; ?>
              <?php case (8): ?> Compose Request Payment Status Change Template <?php break; ?>
              <?php case (10): ?> Compose Payout Status Change Template <?php break; ?>
              <?php case (11): ?> Compose Ticket Template <?php break; ?>
              <?php case (12): ?> Compose Ticket Reply Template <?php break; ?>
              <?php case (16): ?> Compose Request Payment Status Change Template <?php break; ?>
              <?php case (17): ?> Compose User Verification Template <?php break; ?>
              <?php case (18): ?> Compose Password Reset Template <?php break; ?>
              <?php case (13): ?> Compose Dispute Reply Template <?php break; ?>
              <?php case (14): ?> Compose Merchant Payment Status Change Template <?php break; ?>
              <?php case (29): ?> Compose User Status Change Template <?php break; ?>
              <?php case (23): ?> Compose Deposit Notification Template <?php break; ?>
              <?php case (24): ?> Compose Payout Notification Template <?php break; ?>
              <?php case (25): ?> Compose Exchange Notification Template <?php break; ?>
              <?php case (26): ?> Compose Transfer Notification Template <?php break; ?>
              <?php case (27): ?> Compose Request Acceptance Notification Template <?php break; ?>
              <?php case (28): ?> Compose Payment Notification Template <?php break; ?>
              <?php case (67): ?> Maintenance Break Template <?php break; ?>
              <?php case (66): ?> Card Subscription Renew Template <?php break; ?>
              <?php case (68): ?> Card Subscription Expiry Reminder Template <?php break; ?>
              <?php case (69): ?> Card Subscription Template <?php break; ?>
              <?php case (70): ?> Card Status Template <?php break; ?>
              <?php case (71): ?> Card Status Template <?php break; ?>
              <?php case (72): ?> Card Subscription Renew Template <?php break; ?>
              <?php case (73): ?> Card Subscription Upgrade Template <?php break; ?>
              <?php case (74): ?> Card Subscription Upgrade Template <?php break; ?>
              <?php case (75): ?> ACH Transfer Request Template <?php break; ?>
              <?php case (76): ?> ACH Transfer Request Template <?php break; ?>
              <?php case (77): ?> ACH Transfer Complete Template <?php break; ?>
              <?php case (78): ?> ACH Transfer Complete Template <?php break; ?>
          <?php endswitch; ?>
        </h3>
      </div>

      <form action='<?php echo e(url("admin/template_update/$tempId")); ?>' method="post" id="template">
        <?php echo csrf_field(); ?>


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
                                            <label>Subject</label>
                                            <input class="form-control" name="<?php echo e($language->short_name); ?>[subject]" type="text" value="<?php echo e($temp->subject); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Body</label>
                                            <div id="editor-<?php echo e($language->short_name); ?>" class="quill-editor"><?php echo $temp->body; ?></div>
                                            <input type="hidden" name="<?php echo e($language->short_name); ?>[body]" id="input-<?php echo e($language->short_name); ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

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
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>
<!-- QuillJS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    $(document).ready(function () {
        const editors = {};

        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            let quillEditor<?php echo e($language->id); ?> = new Quill("#editor-<?php echo e($language->short_name); ?>", {
                theme: 'snow',
                placeholder: 'Compose your email content here...',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        ['link', 'blockquote', 'code-block'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['clean']
                    ]
                }
            });

            editors['<?php echo e($language->short_name); ?>'] = quillEditor<?php echo e($language->id); ?>;
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        $('#template').on('submit', function () {
            Object.keys(editors).forEach(function (lang) {
                let html = editors[lang].root.innerHTML;
                $('#input-' + lang).val(html);
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/email_templates/index.blade.php ENDPATH**/ ?>