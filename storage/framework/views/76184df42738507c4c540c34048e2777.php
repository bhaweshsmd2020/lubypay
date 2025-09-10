

<?php $__env->startSection('css'); ?>
    <style>
    .marginTopPlus {
    margin-top: 0px!important;
}
        @media only screen and (max-width: 259px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
        
        .ticket-btn {
    /* border: 2px solid #7d95b6; */
    border-radius: 2px;
    color: #ffffff!important;
    background-color: #f7ab33!important;
}
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                <?php echo $__env->make('user_dashboard.layouts.common.tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <br>
                    
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <!--<div class="right mb10">-->
                    <!--    <a href="<?php echo e(url('/product/add')); ?>" class="btn btn-cust ticket-btn" style="padding: 2px 10px;"><i class="fa fa-shopping-basket"></i>&nbsp;-->
                    <!--        <?php echo app('translator')->get('message.dashboard.button.new-product'); ?></a>-->
                    <!--</div>-->
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            
                        </div>

                        <div class="table-responsive">
                            <?php if($list->count() > 0): ?>
                                <table class="table recent_activity">
                                    <thead>
                                    <tr>
                                        <td><strong>Sr. No.</strong></td>
                                        <td><strong>Name</strong></td>
                                        <td><strong>Email</strong></td>
                                        <td><strong>Phone</strong></td>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1 ?>
                                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($i); ?></td>
                                             <td><?php echo e($result->customer_name); ?> </td>
                                            <td><?php echo e($result->customer_email); ?> </td>
                                            <td><?php echo e($result->customer_phone); ?> </td>
                                            
                                        </tr>
                                        
                                        <?php $i++ ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                        </div>
                        <?php else: ?>
                            <h5 style="padding:15px 10px;"><?php echo app('translator')->get('message.dashboard.product.table.not-found'); ?></h5>
                        <?php endif; ?>


                        <div class="card-footer">
                            <?php echo e($list->links('vendor.pagination.bootstrap-5')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/users/customers.blade.php ENDPATH**/ ?>