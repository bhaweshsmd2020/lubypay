<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo e(!isset($exception) ? meta(Route::current()->uri(),'description'):$exception->description); ?>">
    <meta name="keywords" content="<?php echo e(!isset($exception) ? meta(Route::current()->uri(),'keyword'):$exception->keyword); ?>">
    <?php if(Route::current()->uri() == 'mpos-register'){ ?>
        <title>Virtual Mobile Terminal (mPOS)</title>
    <?php }else{ ?>
        <title><?php echo e(!isset($exception) ? meta(Route::current()->uri(),'title'):$exception->title); ?> <?= isset($additionalTitle)?'| '.$additionalTitle :'' ?></title>
    <?php } ?>
    
    <?php if(isset($images)) {?>
    <meta property="og:image" content="<?=$images?>"/>
    <meta property="og:image:secure_url" content="<?=$images?>" />
    <?php }?>
    <?php echo $__env->make('frontend.layouts.common.style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!---title logo icon-->
    <link rel="javascript" href="<?php echo e(asset('public/frontend/js/respond.js')); ?>">

    <!---favicon-->
    <?php if(!empty(getfavicon())): ?>
        <link rel="shortcut icon" href="<?php echo e(url('public/images/logos/'.getfavicon())); ?>" />
    <?php else: ?>
        <link rel="shortcut icon" href="<?php echo e(url('public/frontend/images/favicon.png')); ?>" />
    <?php endif; ?>

    <script type="text/javascript">
        var SITE_URL = "<?php echo e(url('/')); ?>";
    </script>
</head>

<body class="send-money request-page">
    <!-- Start scroll-top button -->
    <div id="scroll-top-area">
        <a href="<?php echo e(url()->current()); ?>#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
    </div>
    <!-- End scroll-top button -->
    <!--Start Header-->
    <?php echo $__env->make('frontend.layouts.common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!--End Header-->

    <?php echo $__env->yieldContent('content'); ?>

    <!--Start Contact Section-->
  <!--  <?php echo $__env->make('frontend.layouts.common.footer_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>-->
    <!--End Contact Section-->
    <!--Start Footer-->
    <?php echo $__env->make('frontend.layouts.common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!--End Footer-->
    <?php echo $__env->make('frontend.layouts.common.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldContent('js'); ?>
</body><?php /**PATH /home/lubynet/public_html/lubypaynew/resources/views/frontend/layouts/app.blade.php ENDPATH**/ ?>