<style>
    
        .ticket-btn {
    /* border: 2px solid #7d95b6; */
    border-radius: 2px;
    color: #ffffff!important;
    background-color: #f7ab33!important;
}
</style>


<?php $__env->startSection('css'); ?>
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/user_dashboard/css/sweetalert.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="right mb10">
                        <a href="<?php echo e(url('/payout')); ?>" class="btn btn-cust ticket-btn" style="    padding: 1px 10px;"><i class="fa fa-arrow-up"></i>&nbsp;<?php echo app('translator')->get('message.dashboard.payout.new-payout.title'); ?></a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                                <div class="chart-list float-left">
                                    <ul>
                                        <li class="active"><a href="<?php echo e(url('/payouts')); ?>"><?php echo app('translator')->get('message.dashboard.payout.menu.payouts'); ?></a></li>
                                        <li><a href="<?php echo e(url('/payout/setting')); ?>"><?php echo app('translator')->get('message.dashboard.payout.menu.payout-setting'); ?></a></li>
                                    </ul>
                              </div>
                        </div>
                        <div class="table-responsive">
                            <?php if($payouts->count() > 0): ?>
                                <table class="table recent_activity">
                                    <thead>
                                        <tr>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.date'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.method'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.method-info'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.fee'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.amount'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.currency'); ?></strong></td>
                                            <td><strong><?php echo app('translator')->get('message.dashboard.payout.list.status'); ?></strong></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>
                                            <td><?php echo e(dateFormat($payout->created_at)); ?></td>
                                            <td><?php echo e(($payout->payment_method->name == "Mts") ? getCompanyName() : $payout->payment_method->name); ?></td>

                                            <td>
                                                <?php if($payout->payment_method->name == "Bank"): ?>
                                                    <?php if($payout->withdrawal_detail): ?>
                                                        <?php echo e($payout->withdrawal_detail->account_name); ?> (*****<?php echo e(substr($payout->withdrawal_detail->account_number,-4)); ?>

                                                        )<br/>
                                                        <?php echo e($payout->withdrawal_detail->bank_name); ?>

                                                    <?php else: ?>
                                                        <?php echo e('-'); ?>

                                                    <?php endif; ?>
                                                <?php elseif($payout->payment_method->name == "Mts"): ?>
                                                    <?php echo e('-'); ?>

                                                <?php else: ?>
                                                    <?php echo e($payout->payment_method_info); ?>

                                                <?php endif; ?>
                                            </td>

                                            <?php
                                                $payoutFee = ($payout->amount-$payout->subtotal);
                                            ?>

                                            <td><?php echo e(($payoutFee == 0) ? '-' : formatNumber($payoutFee)); ?></td>
                                            <td><?php echo e(formatNumber($payout->amount)); ?></td>
                                            <td><?php echo e($payout->currency->code); ?></td>
                                            <td>
                                               <?php
                                                    if ($payout->status == 'Success') {
                                                        echo '<span class="badge badge-success">'.$payout->status.'</span>';
                                                    }
                                                    elseif ($payout->status == 'Pending') {
                                                        echo '<span class="badge badge-primary">'.$payout->status.'</span>';
                                                    }
                                                    elseif ($payout->status == 'Blocked') {
                                                        echo '<span class="badge badge-danger">Cancelled</span>';
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                        </div>
                            <?php else: ?>
                              <h5 style="padding: 15px 10px; "><?php echo app('translator')->get('message.dashboard.payout.list.not-found'); ?></h5>
                            <?php endif; ?>
                        <div class="card-footer">
                            <?php echo e($payouts->links('vendor.pagination.bootstrap-5')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('public/user_dashboard/js/sweetalert.min.js')); ?>" type="text/javascript"></script>
<script>
    $(document).ready(function()
    {
        var payoutSetting = <?php echo count($payoutSettings); ?>

        $( ".ticket-btn" ).click(function()
        {
            if ( payoutSetting <= 0 )
            {
                swal({
                        title: "<?php echo e(__("Error")); ?>!",
                        text: "<?php echo e(__("No Payout Setting Exists!")); ?>",
                        type: "error"
                    }
                );
                event.preventDefault();
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/withdrawal/payouts.blade.php ENDPATH**/ ?>