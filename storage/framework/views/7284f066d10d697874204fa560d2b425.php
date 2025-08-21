
<?php $__env->startSection('content'); ?>
    <div class="min-vh-100">
        <section class="bg-image mt-93">
            <div class="bgd-blue">
                <div class="container">
                    <div class="row py-5">
                        <div class="col-md-12">
                            <h2 class="color-7EA font-weight-bold text-28"><?php echo e($pageInfo->en); ?> </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End banner Section-->

        <!--Start Section-->
        <section class="mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pageArticle_content">
                            <?php echo $pageInfo->en_content; ?>

                        </div>
                    </div>
                    <!--/col-->
                </div>
                <!--/row-->
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/frontend/pages/detail.blade.php ENDPATH**/ ?>