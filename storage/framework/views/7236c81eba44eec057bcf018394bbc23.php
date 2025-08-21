<header class="main-header">
    <!-- Logo -->
    <div class="full-width">

    <a href="<?php echo e(route('dashboard')); ?>" class="logo" style="background-color: #000!important; border-radius: 0px 0px 0px 25px;">
        <span class="logo-mini"><b><?php echo e($app_name_short); ?></b></span>

        <?php if(!empty($company_logo)): ?>
            <img src="<?php echo e(asset('public/images/logos/'.$company_logo)); ?>" alt="logo" width="180" height="59" class="company-logo" style="background-color: #fff;
             border-radius: 10px;padding: 5px;">
            <!--<img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" width="180" height="59" class="company-logo">-->
         <?php else: ?>
            <img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" width="180" height="59" class="company-logo" style="background-color: #fff;
             border-radius: 10px;padding: 5px;">
         <?php endif; ?>
    </a>
    </div>

    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="mobile-width">
            <a href="<?php echo e(route('dashboard')); ?>" class="mobile-logo">
                <span class="logo-lg" style="font-size: 13px;"><b><?php echo e($app_name_long); ?></b></span>
            </a>
        </div>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                
                <?php echo $__env->make('admin.layouts.partials.nav_notifications_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </ul>
            <ul class="nav navbar-nav">
                
                <?php echo $__env->make('admin.layouts.partials.nav_user-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </ul>
            
        </div
       
    </nav>
</header>

<!-- Flash Message  -->
<div class="flash-container">
    <?php if(Session::has('message')): ?>
        <div class="alert <?php echo e(Session::get('alert-class')); ?> text-center" style="margin-bottom:0px;" role="alert">
          <?php echo e(Session::get('message')); ?>

          <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
        </div>
    <?php endif; ?>
    <div class="alert alert-success text-center" id="success_message_div" style="margin-bottom:0px;display:none;" role="alert">
        <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
        <p id="success_message"></p>
    </div>

    <div class="alert alert-danger text-center" id="error_message_div" style="margin-bottom:0px;display:none;" role="alert">
        <p><a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a></p>
        <p id="error_message"></p>
    </div>
</div>
<!-- /.Flash Message  -->


<?php /**PATH /home/lubynet/public_html/lubypaynew/resources/views/admin/layouts/partials/header.blade.php ENDPATH**/ ?>