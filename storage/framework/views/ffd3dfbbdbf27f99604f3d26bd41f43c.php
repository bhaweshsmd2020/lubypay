

<?php $__env->startSection('content'); ?>
    <div class="card darken-1">
        <div class="card-content black-text">
            <div class="center-align">
                <p class="card-title"><?php echo e(__('Pay Money')); ?></p>
                <p><em><?php echo e(env('APP_VERSION')); ?></em></p>
                <hr>
                <p class="card-title"><?php echo e(__('Welcome to the Installer !')); ?></p>
            </div>
            <p class="center-align"><?php echo e(__('Easy installation and setup wizard')); ?></p>
        </div>
        <div class="card-action right-align">
            <a class="btn waves-effect blue waves-light" href="<?php echo e(url('install/requirements')); ?>">
                <?php echo e(__('Start with checking requirements')); ?>

                <i class="material-icons right">send</i>
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('vendor.installer.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/vendor/installer/welcome.blade.php ENDPATH**/ ?>