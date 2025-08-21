<!-- Notifications: style can be found in dropdown.less -->
<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning"><?php echo e($count_noti); ?></span>
    </a>
   
    <ul class="dropdown-menu">
        <?php if($count_noti === 0): ?>
        <li class="header">You have no unread notifications</li>
        <?php else: ?>
        <li class="header">You have <?php echo e($count_noti); ?> unread notifications</li>
        <?php endif; ?>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <?php $__currentLoopData = $unread_noti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $noti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        if(!empty($noti->local_tran_time)){
                            $local_time = $noti->local_tran_time;
                        }else{
                            $local_time = $noti->created_at;
                        }
                    ?>
                    
                    <form method="POST" action="<?php echo e(url('admin/notifications/update/'.$noti->id)); ?>" id="form_noti_<?php echo e($noti->id); ?>">
                        <?php echo e(csrf_field()); ?>

                        <input type="hidden" name="id" value="<?php echo e($noti->id); ?>">
                    </form>
                    <li>
                        <a href="#" onclick="document.getElementById('form_noti_<?php echo e($noti->id); ?>').submit();">
                            <i class="fa fa-users text-aqua"></i> <?php echo nl2br(e($noti->description)); ?><br>
                            <i class="fa fa-clock-o text-aqua"></i><?php echo e(Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A')); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </li>
        <li class="footer"><a href="<?php echo e(url('admin/notifications')); ?>">View all</a></li>
    </ul>
</li>
<?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/layouts/partials/nav_notifications_menu.blade.php ENDPATH**/ ?>