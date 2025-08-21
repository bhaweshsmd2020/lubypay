
<?php $__env->startSection('content'); ?>
    <section class="inner-banner">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo app('translator')->get('message.form.forget-password-form'); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <!--End banner Section-->
    <!--Start Section-->
    <section class="section-01 padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-6 mx-auto">
                            <!-- form card login -->
                            <div class="card rounded-0">
                                <div class="card-header">
                                    <h3 class="mb-0 text-left"><?php echo app('translator')->get('message.form.forget-password-form'); ?></h3>
                                </div>
                                <div class="card-body">
                                    <style>
                                        .error{
                                            font-weight: bold;
                                        }
                                    </style>
                                    <?php echo $__env->make('frontend.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <br>

                                    <form action="<?php echo e(url('forget-password')); ?>" method="post" id="forget-password-form">
                                            <?php echo e(csrf_field()); ?>

                                        <div class="form-group">
                                            <label for="email"><?php echo app('translator')->get('message.form.email'); ?></label>
                                            <input type="email" class="form-control" aria-describedby="emailHelp" placeholder="<?php echo app('translator')->get('message.form.email'); ?>" name="email" id="email">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-cust float-right" id="forget-password-submit-btn">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                    <span id="forget-password-submit-btn-text" style="font-weight: bolder;">
                                                        <?php echo app('translator')->get('message.form.submit'); ?>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!--/card-block-->
                            </div>
                            <!-- /form card login -->
                        </div>
                    </div>
                    <!--/row-->
                </div>
                <!--/col-->
            </div>
            <!--/row-->
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('public/frontend/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
    <script>

        jQuery.extend(jQuery.validator.messages, {
            required: "<?php echo e(__('This field is required.')); ?>",
            email: "<?php echo e(__("Please enter a valid email address.")); ?>",
        });

        $('#forget-password-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                }
            },
            submitHandler: function(form)
            {
                $("#forget-password-submit-btn").attr("disabled", true).click(function (e)
                {
                    e.preventDefault();
                });
                $(".spinner").show();
                $("#forget-password-submit-btn-text").text("<?php echo e(__('Submitting...')); ?>");
                form.submit();
            }
        });
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/frontend/auth/forgetPassword.blade.php ENDPATH**/ ?>