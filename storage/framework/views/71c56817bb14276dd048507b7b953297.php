

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class=""> Account Verification </h4>
                            <div class="text-center">
                                <img src="<?php echo e(asset('public/images/kyc.jpg')); ?>" style="max-width: 300px; margin: 50px;"><br>
                                <a href="https://ticktappay.withpersona.com/verify?inquiry-template-id=itmpl_zqJtwdgxFxziYaq8EZvt5X8p&environment=sandbox&reference-id=<?php echo e($user->carib_id); ?>&languagen-US=&fields[name-first]=<?php echo e($user->first_name); ?>&redirect-uri=<?php echo e(route('user.kyc')); ?>">
                                    <button class="btn btn-primary">Auto Verification</button>
                                </a>
                                
                                <a href="<?php echo e(url('/profile/personal-id')); ?>">
                                    <button class="btn btn-primary">Manual Verification</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/users/kyc.blade.php ENDPATH**/ ?>