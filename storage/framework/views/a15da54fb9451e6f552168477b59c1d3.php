<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo e(__('Pay Money')); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset('public/dist/images/default-favicon.png')); ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
        <style type="text/css">
            .card, .card-panel {
                width: 960px;
                margin: 0 auto;
            }
        </style>
        <?php echo $__env->yieldContent('style'); ?>
    </head>
    <body>
        <?php echo $__env->yieldContent('content'); ?>
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
        <?php echo $__env->yieldContent('script'); ?>
    </body>
</html><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/vendor/installer/layout.blade.php ENDPATH**/ ?>