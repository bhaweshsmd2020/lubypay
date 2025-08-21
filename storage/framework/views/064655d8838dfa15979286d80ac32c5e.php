
<?php $__env->startSection('title', 'Notification Types'); ?>

<?php $__env->startSection('page_content'); ?>
    <!-- Main content -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="tabs">
                      <li class="active"><a href="<?php echo e(url('admin/settings/notification-types')); ?>">Notification Types</a></li>
                      <li><a href="<?php echo e(url('admin/settings/notification-settings/email')); ?>">Email Notification Settings</a></li>
                      
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab_1">
                            <div class="box-body" >
                                <?php if($notificationTypes->count() > 0): ?>
                                    <table class="table table-responsive text-center">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $notificationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notificationType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($notificationType->name); ?> </td>
                                                    <?php if($notificationType->status =='Inactive'): ?>
                                                        <td><span class="label label-danger"><?php echo e($notificationType->status); ?></span></td>

                                                    <?php elseif($notificationType->status =='Active'): ?>
                                                        <td><span class="label label-success"><?php echo e($notificationType->status); ?></span></td>
                                                    <?php endif; ?>
                                                    <td>
                                                        <a href="<?php echo e(url('admin/settings/notification-types/edit/'.$notificationType->id)); ?>" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h5 style="padding: 15px 20px; ">Notifications not found!</h5>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/settings/notification_types/index.blade.php ENDPATH**/ ?>