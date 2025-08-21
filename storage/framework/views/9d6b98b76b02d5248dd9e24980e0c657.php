<style>
    .ticket-btn {
    /* border: 2px solid #7d95b6; */
    border-radius: 2px;
    color: #ffffff!important;
    background-color: #f7ab33!important;
}
</style>

<?php $__env->startSection('content'); ?>
<section class="section-06 history padding-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="right mb10">
                    <a href="<?php echo e(url('/ticket/add')); ?>" class="btn btn-cust ticket-btn" style="    padding: 2px 10px;"><i class="fa fa-ticket"></i>&nbsp; <?php echo app('translator')->get('message.dashboard.button.new-ticket'); ?></a>
                </div>
                <div class="clearfix"></div>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo app('translator')->get('message.dashboard.ticket.title'); ?></h4>
                    </div>
                    <div class="table-responsive">
                        <?php if($tickets->count() > 0): ?>

                        <table class="table recent_activity">
                            <thead>
                                <tr>
                                    <td class="text-left" width="16%"><strong><?php echo app('translator')->get('message.dashboard.ticket.ticket-no'); ?></strong></td>
                                    <td class="text-left"><strong><?php echo app('translator')->get('message.dashboard.ticket.subject'); ?></strong></td>
                                    <td width="15%"><strong><?php echo app('translator')->get('message.dashboard.ticket.status'); ?></strong></td>
                                    <td width="6%"><strong><?php echo app('translator')->get('message.dashboard.ticket.priority'); ?></strong></td>
                                    <td width="15%"><strong><?php echo app('translator')->get('message.dashboard.ticket.date'); ?></strong></td>
                                    <td width="6%"><strong><?php echo app('translator')->get('message.dashboard.ticket.action'); ?></strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-left"><?php echo e($result->code); ?> </td>
                                    <td class="text-left"><a href="<?php echo e(url('ticket/reply').'/'.$result->id); ?>"><?php echo e($result->subject); ?></a></td>

                                    <?php if($result->ticket_status->name =='Closed'): ?>
                                        <td><span class="badge badge-danger"><?php echo e($result->ticket_status->name); ?></span></td>
                                    <?php elseif($result->ticket_status->name =='Hold'): ?>
                                        <td><span class="badge badge-warning"><?php echo e($result->ticket_status->name); ?></span></td>
                                    <?php elseif($result->ticket_status->name =='In Progress'): ?>
                                        <td><span class="badge badge-primary"><?php echo e($result->ticket_status->name); ?></span></td>
                                    <?php elseif($result->ticket_status->name =='Open'): ?>
                                        <td><span class="badge badge-success"><?php echo e($result->ticket_status->name); ?></span></td>
                                    <?php endif; ?>

                                    <td><?php echo e($result->priority); ?> </td>
                                    <td><?php echo e(dateFormat($result->created_at)); ?> </td>
                                    <td>
                                    <a href="<?php echo e(url('ticket/reply').'/'.$result->id); ?>" class="btn btn-sm btn-secondary"><?php echo app('translator')->get('message.dashboard.button.details'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <!--<h5 style="padding: 15px 20px; "><?php echo app('translator')->get('message.dashboard.ticket.no-ticket'); ?></h5>-->
                            <h5 style="padding: 15px 10px; text-align: center;"> <img src="<?php echo e(url('public/images/nodata.png')); ?>" style="    margin: 35px 0px;"></h5>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <?php echo e($tickets->links('vendor.pagination.bootstrap-5')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/Ticket/index.blade.php ENDPATH**/ ?>