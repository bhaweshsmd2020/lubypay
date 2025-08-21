

<?php $__env->startSection('css'); ?>
    <style>
        @media only screen and (max-width: 508px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- personal_address -->
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li><a href="<?php echo e(url('/profile')); ?>"><?php echo app('translator')->get('message.dashboard.setting.title'); ?></a></li>
                                    <?php if($two_step_verification != 'disabled'): ?>
                                        <li><a href="<?php echo e(url('/profile/2fa')); ?>"><?php echo app('translator')->get('message.2sa.title-short-text'); ?></a></li>
                                    <?php endif; ?>

                                    <li><a href="<?php echo e(url('/profile/personal-id')); ?>"><?php echo app('translator')->get('message.personal-id.title'); ?>
                                        <?php if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' ): ?>(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) <?php endif; ?>
                                        </a>
                                    </li>
                                    <li><a href="<?php echo e(url('/profile/personal-address')); ?>"><?php echo app('translator')->get('message.personal-address.title'); ?>
                                        <?php if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' ): ?>(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="active"><a href="<?php echo e(url('/profile/personal-photo')); ?>"><?php echo app('translator')->get('message.personal-photo.title'); ?>
                                        </a>
                                    </li>
                                    <?php if(auth()->user()->type == 'merchant'): ?>
                                        <li>
                                            <a href="<?php echo e(url('/profile/business-verification')); ?>">
                                                Business Verification
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo e(url('/profile/upgrade')); ?>">
                                                Account Upgrade
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- form -->
                                    <form action="<?php echo e(url('profile/personal-photo-update')); ?>" method="POST" class="form-horizontal" id="personal_photo" enctype="multipart/form-data">
                                        <?php echo e(csrf_field()); ?>


                                        <input type="hidden" value="<?php echo e($user->id); ?>" name="user_id" id="user_id" />

                                        <input type="hidden" value="<?php echo e(isset($documentVerification->file_id) ? $documentVerification->file_id : ''); ?>" name="existingAddressFileID" id="existingAddressFileID" />

                                        <div class="row">
                                            <div class="form-group col-md-5">
                                                <label for="address_file"><?php echo app('translator')->get('message.personal-photo.upload-photo-proof'); ?>(upto 10MB)</label>
                                                <input type="file" name="photo_file" class="form-control input-file-field">
                                            </div>
                                        </div>

                                        <?php if(!empty($documentVerification->file)): ?>
                                            <h5>
                                                <a class="text-info" href="<?php echo e(url('public/uploads/user-documents/photo-proof-files').'/'.$documentVerification->file->filename); ?>"><i class="fa fa-download"></i>
                                                    <?php echo e($documentVerification->file->filename); ?>

                                                </a>
                                            </h5>
                                            <br>
                                        <?php endif; ?>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            <div class="form-group col-md-5">
                                                <button type="submit" class="btn btn-cust col-12" id="personal_photo_submit">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="personal_photo_submit_text"><?php echo app('translator')->get('message.dashboard.button.submit'); ?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- /form -->
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<script src="<?php echo e(asset('public/user_dashboard/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/additional-methods.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">

jQuery.extend(jQuery.validator.messages, {
    required: "<?php echo e(__('This field is required.')); ?>",
})

$('#personal_photo').validate({
    rules: {
        photo_file: {
            required: true,
            extension: "docx|rtf|doc|pdf|png|jpg|jpeg|csv|txt|gif|bmp",
        },
    },
    messages: {
      address_file: {
        extension: "<?php echo e(__("Please select (docx, rtf, doc, pdf, png, jpg, jpeg, csv, txt, gif or bmp) file!")); ?>"
      }
    },
    submitHandler: function(form)
    {
        $("#personal_photo_submit").attr("disabled", true);
        $(".spinner").show();
        $("#personal_photo_submit_text").text('Submitting...');
        form.submit();
    }
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/users/personal_photo.blade.php ENDPATH**/ ?>