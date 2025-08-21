

<?php $__env->startSection('css'); ?>
    <style>
        @media only screen and (max-width: 259px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!--Start Section-->
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="right mb10">
                        <a href="<?php echo e(url('/merchant/add')); ?>" class="btn btn-cust ticket-btn"><i class="fa fa-user"></i>&nbsp;
                            <?php echo app('translator')->get('message.dashboard.button.new-merchant'); ?></a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li>
                                        <a href="<?php echo e(url('/merchants')); ?>"><?php echo app('translator')->get('message.dashboard.merchant.menu.merchant'); ?></a>
                                    </li>
                                    <li class="active"><a href="#"><?php echo app('translator')->get('message.dashboard.merchant.menu.payment'); ?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="">
                            <div class="table-responsive">
                                <?php if($merchant_payments->count() > 0): ?>
                                    <table class="table recent_activity" id="merchant">
                                        <thead>
                                        <tr>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.created-at'); ?></strong>
                                            </td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.merchant'); ?></strong>
                                            </td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.method'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.order-no'); ?></strong>
                                            </td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.amount'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.fee'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.total'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.currency'); ?></strong>
                                            </td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.merchant.payment.status'); ?></strong></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $merchant_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(dateFormat($result->created_at)); ?></td>
                                                <td><?php echo e($result->merchant->business_name); ?></td>

                                                <td><?php echo e(($result->payment_method->name == "Mts") ? getCompanyName() : $result->payment_method->name); ?></td>

                                                <td><?php echo e(!empty($result->order_no) ? $result->order_no : "-"); ?></td>

                                                <td><?php echo e(formatNumber($result->amount)); ?></td>

                                                <td><?php echo e((($result->charge_percentage + $result->charge_fixed) == 0) ? '-' : formatNumber($result->charge_percentage + $result->charge_fixed)); ?></td>

                                                <td><?php echo e(formatNumber($result->total)); ?></td>

                                                <td><?php echo e($result->currency->code); ?></td>

                                                <?php if($result->status == 'Pending'): ?>
                                                    <td>
                                                        <span class="badge badge-primary"><?php echo app('translator')->get('message.dashboard.merchant.payment.pending'); ?></span>
                                                    </td>
                                                <?php elseif($result->status == 'Success'): ?>
                                                    <td>
                                                        <span class="badge badge-success"><?php echo app('translator')->get('message.dashboard.merchant.payment.success'); ?></span>
                                                    </td>
                                                <?php elseif($result->status == 'Blocked'): ?>
                                                    <td>
                                                        <span class="badge badge-danger"><?php echo app('translator')->get('message.dashboard.merchant.payment.block'); ?></span>
                                                    </td>
                                                <?php elseif($result->status == 'Refund'): ?>
                                                    <td>
                                                        <span class="badge badge-warning"><?php echo app('translator')->get('message.dashboard.transaction.refund'); ?></span>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h5 style="padding: 15px 10px; text-align: center;"> <img src="<?php echo e(url('public/images/nodata.png')); ?>" style="    margin: 35px 0px;"></h5>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer">
                            <?php echo e($merchant_payments->links('vendor.pagination.bootstrap-5')); ?>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!--End Section-->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/Merchant/payments.blade.php ENDPATH**/ ?>