<?php $__env->startSection('content'); ?>
    <!--Start banner Section-->
    <section class="inner-banner">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo app('translator')->get('message.login.title'); ?> </h1>
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
                                    <h3 class="mb-0 text-left"><?php echo app('translator')->get('message.login.form-title'); ?></h3>
                                </div>
                                <div class="card-body">

                                    <?php echo $__env->make('frontend.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <br>
                                    <style>
                                        .error{
                                            font-weight: bold;
                                        }
                                    </style>
                                    <form action="<?php echo e(url('authenticate')); ?>" method="post" id="login_form">
                                            <?php echo e(csrf_field()); ?>

                                        <input type="hidden" name="has_captcha" value="<?php echo e(isset($setting['has_captcha']) && ($setting['has_captcha'] == 'Enabled') ? 'Enabled' : 'Disabled'); ?>">

                                        <input type="hidden" name="login_via" value="
                                        <?php if(isset($setting['login_via']) && ($setting['login_via'] == 'email_only')): ?>
                                            <?php echo e("email_only"); ?>

                                        <?php elseif(isset($setting['login_via']) && ($setting['login_via'] == 'phone_only')): ?>
                                            <?php echo e("phone_only"); ?>

                                        <?php else: ?>
                                            <?php echo e("email_or_phone"); ?>

                                        <?php endif; ?>
                                        ">

                                        <input type="hidden" name="browser_fingerprint" id="browser_fingerprint" value="test">
                                        
                                        <div class="form-group">
                                            <label for="email_only">User Type</label>
                                            <select class="form-control" name="user_type" id="user_type">
                                                <option value="2">Ewallet Customer</option>
                                                <option value="3">Merchant</option>
                                            </select>
                                        </div>

                                        <?php if(isset($setting['login_via']) && $setting['login_via'] == 'email_only'): ?>
                                            <div class="form-group">
                                                <label for="email_only"><?php echo app('translator')->get('message.login.email'); ?></label>
                                                <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="<?php echo app('translator')->get('message.login.email'); ?>" name="email_only" id="email_only">

                                                <?php if($errors->has('email_only')): ?>
                                                    <span class="error">
                                                     <?php echo e($errors->first('email_only')); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif(isset($setting['login_via']) && $setting['login_via'] == 'phone_only'): ?>
                                            <div class="form-group">
                                                <label for="phone_only"><?php echo app('translator')->get('message.login.phone'); ?></label>
                                                <input type="text" class="form-control" aria-describedby="phoneHelp" placeholder="<?php echo app('translator')->get('message.login.phone'); ?>" name="phone_only" id="phone_only">

                                                <?php if($errors->has('phone_only')): ?>
                                                    <span class="error">
                                                     <?php echo e($errors->first('phone_only')); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                        <?php elseif(isset($setting['login_via']) && $setting['login_via'] == 'email_or_phone'): ?>
                                            <div class="form-group">
                                                <label for="email_or_phone"><?php echo app('translator')->get('message.login.email_or_phone'); ?></label>
                                                <input type="text" class="form-control" aria-describedby="emailorPhoneHelp" placeholder="<?php echo app('translator')->get('message.login.email_or_phone'); ?>" name="email_or_phone" id="email_or_phone">

                                                <?php if($errors->has('email_or_phone')): ?>
                                                    <span class="error">
                                                     <?php echo e($errors->first('email_or_phone')); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <label for="password"><?php echo app('translator')->get('message.login.password'); ?></label>
                                            <input type="password" class="form-control" id="password" placeholder="<?php echo app('translator')->get('message.login.password'); ?>" name="password">

                                            <?php if($errors->has('password')): ?>
                                                <span class="error">
                                                    <strong><?php echo e($errors->first('password')); ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if(isset($setting['has_captcha']) && $setting['has_captcha'] == 'Enabled'): ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo app('captcha')->display(); ?>


                                                    <?php if($errors->has('g-recaptcha-response')): ?>
                                                        <span class="error">
                                                            <strong><?php echo e($errors->first('g-recaptcha-response')); ?></strong>
                                                        </span>
                                                        <br>
                                                    <?php endif; ?>
                                                    <br>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="row">
                                          <input class="form-check-input" type="hidden" value="" id="remember_me" name="remember_me">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-cust float-left" id="login-btn">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                    <span id="login-btn-text" style="font-weight: normal;">
                                                        <?php echo app('translator')->get('message.form.button.login'); ?>
                                                    </span>
                                                </button>
                                            </div>
                                          </div>
                                        <div class="row">
                                            <div class="col-md-12 get-color" style="margin: -2px 0 6px 0px;">
                                                <br>
                                                <a href="<?php echo e(url('forget-password')); ?>"><?php echo app('translator')->get('message.login.forget-password'); ?></a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!--/card-block-->
                            </div>
                            <!-- /form card login -->
                            <div class="signin">
                                <div class="message">
                                    <span><?php echo app('translator')->get('message.login.no-account'); ?> &nbsp; </span>
                                     <a href="<?php echo e(url('register')); ?>"><?php echo app('translator')->get('message.login.sign-up-here'); ?></a>.
                                </div>
                            </div>
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

<script src="<?php echo e(url('public/backend/fpjs2/fpjs2.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/frontend/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<!-- clear storage on dashboard load -->
<script>
    localStorage.removeItem('first');
</script>

<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {
        required: "<?php echo e(__('This field is required.')); ?>",
    })
</script>

<script>
    $('#login_form').validate({
        rules:
        {
            email_only: {
                required: true,
                // email: true
            },
            phone_only: {
                required: true,
                // number: true,
            },
            email_or_phone: {
                required: true,
            },
            password: {
                required: true
            },
        },
        submitHandler: function(form)
        {
            $("#login-btn").attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });
            $(".spinner").show();
            $("#login-btn-text").text("<?php echo e(__('Signing In...')); ?>");
            form.submit();
        }
    });

    $(document).ready(function()
    {
        new Fingerprint2().get(function(result, components)
        {
            $('#browser_fingerprint').val(result);
        });
    });
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/frontend/auth/login.blade.php ENDPATH**/ ?>