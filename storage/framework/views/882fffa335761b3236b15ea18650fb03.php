
<style>
    .bg-primary {
    background: rgba(212, 212, 212, 0.9) !important;
    width: 100% !important;
}
.dheader {
    margin-top: -11px;
    max-width: 78%!important;
     height:70px!important; 
    /*margin-left: -7px;*/
}
   
</style>
<?php
$user = Auth::user();
$socialList = getSocialLink();
$menusHeader = getMenuContent('Header');
$logo = getCompanyLogo(); //from session
$logo = getCompanyLogoWithoutSession(); //direct query
?>
<header id="js-header-old">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="padding-bottom: 10px;    background: rgb(255, 255, 255) !important;">
        <div class="container">
            <?php if($logo): ?>
               <!-- <a style="height: 60px;width: 157px;overflow: hidden;"  class="navbar-brand" href="<?php if(request()->path() != 'merchant/payment'): ?> <?php echo e(url('/')); ?> <?php else: ?> <?php echo e('#'); ?> <?php endif; ?>">
                    <img src="<?php echo e(asset('public/images/logos/clogo.png')); ?>" alt="logo" class="img-responsive dheader img-fluid">
                </a>-->
                 <a style="width: 157px;overflow: hidden;"  class="navbar-brand" href="<?php echo e(url('')); ?>">
                    <img src="<?php echo e(asset('public/images/logos/'.$logo)); ?>" alt="logo" class="img-responsive dheader img-fluid">
                </a>
                <!-- <a style="width: 157px;overflow: hidden;"  class="navbar-brand" href="<?php echo e(url('')); ?>">-->
                <!--    <img src="<?php echo e(asset('public/images/logos//logomain.png')); ?>" alt="logo" class="img-responsive dheader img-fluid">-->
                <!--</a>-->
            <?php else: ?>
               <!-- <a style="height: 60px;width: 157px;overflow: hidden;"  class="navbar-brand" href="<?php if(request()->path() != 'merchant/payment'): ?> <?php echo e(url('/')); ?> <?php else: ?> <?php echo e('#'); ?> <?php endif; ?>">
                    <img src="<?php echo e(url('public/images/logos/clogo.png')); ?>" class="img-responsive dheader" width="80" height="50">
                </a>-->
                <a style="height: 60px;width: 157px;overflow: hidden;"  class="navbar-brand" href="<?php echo e(url('')); ?>">
                    <img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" class="img-responsive dheader" width="80" height="50">
                </a>
            <?php endif; ?>

            <?php if(request()->path() != 'merchant/payment'): ?>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
             <!--   <div class="collapse navbar-collapse navbar-toggler-right" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto my-navbar">
                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'home' ) ? 'nav_active': '' ?>"><a href="<?php echo e(url('/')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.home'); ?></a></li>
                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'send-money' ) ? 'nav_active': '' ?>"><a href="<?php echo e(url('/send-money')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.send'); ?></a></li>
                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'request-money' ) ? 'nav_active': '' ?>"><a href="<?php echo e(url('/request-money')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.request'); ?></a></li>
                     <?php if(!empty($menusHeader)): ?>
                        <?php $__currentLoopData = $menusHeader; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top_navbar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item <?= isset( $menu ) && ( $menu == $top_navbar->url ) ? 'nav_active': '' ?>"><a href="<?php echo e(url($top_navbar->url)); ?>" class="nav-link"> <?php echo e($top_navbar->name); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                        <?php if( !Auth::check() ): ?>
                            <li class="nav-item auth-menu"> <a href="<?php echo e(url('/login')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.login'); ?></a></li>
                            <li class="nav-item auth-menu"> <a href="<?php echo e(url('/register')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.register'); ?></a></li>
                        <?php else: ?>
                            <li class="nav-item auth-menu"> <a href="<?php echo e(url('/dashboard')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.dashboard'); ?></a> </li>
                            <li class="nav-item auth-menu"> <a href="<?php echo e(url('/logout')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.logout'); ?></a> </li>
                        <?php endif; ?>
                    </ul>
                </div>-->
            <?php endif; ?>

        <div id="quick-contact" class="collapse navbar-collapse">
                <ul class="ml-auto">
                    <?php if( !Auth::check()): ?>
                        <?php if(request()->path() == 'merchant/payment'): ?>
                            
                        <?php else: ?>
                            <li> <a href="<?php echo e(url('/login')); ?>"><?php echo app('translator')->get('message.home.title-bar.login'); ?></a> </li>
                            <li> <a href="<?php echo e(url('/register')); ?>">Signup</a> </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="<?php echo e(url('/dashboard')); ?>"><?php echo app('translator')->get('message.home.title-bar.dashboard'); ?></a> </li>
                        <li><a href="<?php echo e(url('/logout')); ?>"><?php echo app('translator')->get('message.home.title-bar.logout'); ?></a> </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/frontend/layouts/common/header.blade.php ENDPATH**/ ?>