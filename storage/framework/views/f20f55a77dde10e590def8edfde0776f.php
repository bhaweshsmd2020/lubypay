<li class="dropdown user user-menu">
    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">

        <img src=<?php echo e($admin_image); ?> class="user-image" alt="User Image" style="width: 25px; height: 25px;"><!-- User image -->
        <span class="hidden-xs"><?php echo e(ucwords($admin_name)); ?></span>
    </a>

    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <img src=<?php echo e($admin_image); ?> class="img-circle" alt="User Image"
                 style="width: 90px; height: 90px;">
            <p>
                <small>Email: <?php echo e($admin_email); ?></small>
            </p>
        </li>

        <li class="user-footer">
            <div class="pull-left">
                <a href="<?php echo e(url('admin/profile')); ?>" class="btn btn-default btn-flat">Profile</a>
            </div>
            <div class="pull-right">
                <a href="<?php echo e(URL::to('/')); ?>/admin/adminlogout" class="btn btn-default btn-flat">Sign out</a>
            </div>
        </li>
    </ul>
</li>
<?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/layouts/partials/nav_user-menu.blade.php ENDPATH**/ ?>