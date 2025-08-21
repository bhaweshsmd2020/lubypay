<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = getCompanyLogoWithoutSession();
    $persona_kyc = DB::table('kycdatastores')->where('user_id', auth()->user()->id)->where('status', 'COMPLETED')->first();
    $manual_kyc = DB::table('users')->where('id', auth()->user()->id)->where('photo_verified', '1')->where('address_verified', '1')->where('identity_verified', '1')->first();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="description" content="<?php echo e(!isset($exception) ? meta(Route::current()->uri(),'description'):$exception->description); ?>">
        <meta name="keywords" content="<?php echo e(!isset($exception) ? meta(Route::current()->uri(),'keyword'):$exception->keyword); ?>">

        <?php if (! empty(trim($__env->yieldContent('title')))): ?>
            <title> <?php echo $__env->yieldContent('title'); ?></title>
        <?php else: ?>
        <title><?php echo e(!isset($exception) ? meta(Route::current()->uri(),'title'):$exception->title); ?> <?= isset($additionalTitle)?'| '.$additionalTitle :'' ?></title>
        <?php endif; ?>

        <!--css styles-->
        <?php echo $__env->make('user_dashboard.layouts.common.style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!---title logo icon-->
        <link rel="javascript" href="<?php echo e(asset('public/user_dashboard/js/respond.js')); ?>">




        <!---favicon-->
        <?php if(!empty(getfavicon())): ?>
            <link rel="shortcut icon" href="<?php echo e(asset('public/images/logos/'.getfavicon())); ?>" style="background-color:#fff;" />
             
            
        <?php endif; ?>

        <script type="text/javascript">
            var SITE_URL = "<?php echo e(url('/')); ?>";
        </script>

        <style type="text/css">
.skin-blue .sidebar-menu>li:hover>a, .skin-blue .sidebar-menu>li.active>a, .skin-blue .sidebar-menu>li.menu-open>a {
       background-color: #0d172c!important;
    color: #f4f8ff!important;
    border-radius: 5px 5px 5px 5px;
    border-left: 3px solid #ecf0f500!important;
}
.sidebar-menu{    margin-top: -35px!important;}
/*.sidebar-menu > li {*/
/*    position: relative;*/
/*    margin: 0;*/
/*    padding: 0;*/
/*    padding-bottom: 10px;*/
/*}*/
.text-success {
    color: #f7aa32!important;
}
.skin-blue .sidebar-menu>li>a {
    border-left: 3px solid transparent;
    font-size: 15px!important;
}
.skin-blue .sidebar a {
    color:#ffffff!important;
}
.skin-blue .wrapper, .skin-blue .main-sidebar, .skin-blue .left-side {
    background-color: #fdfdfd!important;
}
        .container {
    max-width: 100%!important;
}
        .main-sidebar {
    position: absolute;

    top: 61px!important;}

    .sidebar-menu > li > a {
    padding: 12px 5px 12px 15px!important;
    display: block;
}

.btn-default {
    background-color: #675a5a00!important;

       border-color: #fff0!Important;
}
            #image-dropdown {
              display: inline-block;
              border: 1px solid;
            }
            #image-dropdown {
              height: 30px;
              overflow: hidden;
            }
            /*#image-dropdown:hover {} */

            #image-dropdown .img_holder {
              cursor: pointer;
            }
            #image-dropdown img.flagimgs {
              height: 30px;
            }
            #image-dropdown span.iTEXT {
              position: relative;
              top: -8px;
            }
            .navbar.navbar-expand-lg.navbar-dark.bg-primary.toogleMenuDiv{
                padding:0 !important;
            }
        </style>
        <!-- Theme style -->
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/css/AdminLTE.css')); ?>">

<!-- Skins -->
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/css/skins/_all-skins.min.css')); ?>">
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div id="scroll-top-area">
            <a href="<?php echo e(url()->current()); ?>#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
        </div>

        <div class="wrapper_custom">
                 <header id="js-header-old">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="z-index: 5;padding-top: inherit;">
        <div class="container">
            <?php if(isset($logo)): ?>
                <a  class="navbar-brand newh" href="<?php echo e(url('/')); ?>">
                    <img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" alt="logo" class="img-fluid dheader" style="background-color: #fff;
    border-radius: 10px;">
                </a>
            <?php else: ?>
                <a  class="navbar-brand newh" href="<?php echo e(url('/')); ?>">
                    <img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" class="img-responsive dheader" width="80" height="50" style="background-color: #fff;
    border-radius: 10px;">
                </a>
            <?php endif; ?>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse navbar-toggler-right" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto d-lg-none">
                    <li class="nav-item"><a href="<?php echo e(url('/dashboard')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.dashboard'); ?></a></li>

                    <?php if(Common::has_permission(auth()->id(),'manage_transaction')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/transactions')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.transactions'); ?></a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_deposit')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/deposit')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.button.deposit'); ?></a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_transfer')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/moneytransfer')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.send-req'); ?></a></li>
                    <?php elseif(Common::has_permission(auth()->id(),'manage_request_payment')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/request_payment/add')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.send-req'); ?></a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_exchange')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/exchange')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.exchange'); ?></a></li>
                    <?php endif; ?>
                    
                    <?php if(Common::has_permission(auth()->id(),'manage_deposit')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/utility/cable')); ?>" class="nav-link">Cable</a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_merchant')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/merchants')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.merchants'); ?></a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_withdrawal')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/payouts')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.payout'); ?></a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_dispute')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/disputes')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.disputes'); ?></a></li>
                    <?php endif; ?>

                    <?php if(Common::has_permission(auth()->id(),'manage_ticket')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/tickets')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.tickets'); ?></a></li>
                    <?php endif; ?>



                    <?php if(Common::has_permission(auth()->id(),'manage_setting')): ?>
                        <li class="nav-item"><a href="<?php echo e(url('/profile')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.settings'); ?></a></li>
                    <?php endif; ?>

                    <li class="nav-item"><a href="<?php echo e(url('/logout')); ?>" class="nav-link"><?php echo app('translator')->get('message.dashboard.nav-menu.logout'); ?></a></li>
                </ul>

                <?php if(auth()->user()->type == 'merchant'): ?>
                <a class="btn btn-cust col-md-2 pull-right" style="    border-radius: 35px;margin: unset;margin-left:auto; margin-right:5px ;">
                    <?php echo e(auth()->user()->package); ?>

                </a>
                <?php endif; ?>
            </div>

            <div class="d-none d-lg-block">

                <div class="row">







                    <div class="col-md-3" style="padding-top: 10px">

                        <?php if(Auth::user()->picture): ?>
                            <img src="<?php echo e(url('public/user_dashboard/profile/'.Auth::user()->picture)); ?>"
                                 class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                        <?php else: ?>
                            <img src="<?php echo e(url('public/user_dashboard/images/avatar.jpg')); ?>" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                        <?php endif; ?>
                    </div>

                    <?php
                        $fullName = strlen($user->first_name.' '.$user->last_name) > 20 ? substr($user->first_name.' '.$user->last_name,0,20)."..." : $user->first_name.' '.$user->last_name; //change in pm_v2.1
                    ?>
                    <div class="col-md-9 username text-left">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="subStringUserName" title="<?php echo e($user->first_name.' '.$user->last_name); ?>"><?php echo e($fullName); ?>-<b><?php echo e($user->carib_id); ?></b></span>
                        
                        </a> <!--change in pm_v2.1-->

                        <ul class="dropdown-menu" style="color:#545b62;min-width: 155px;">
                            <?php if(Common::has_permission(auth()->id(),'manage_setting')): ?>
                                <li class="" style="padding: 0px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">
                                    <i class="fa fa-cog"></i><a style="line-height: 0;color:#7d95b6" href="<?php echo e(url('/profile')); ?>" class="btn btn-default btn-flat"><?php echo app('translator')->get('message.dashboard.nav-menu.settings'); ?></a>
                                </li>
                                
                                <?php if(empty($persona_kyc) && !empty($manual_kyc)): ?>
                                    <!--<li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">-->
                                    <!--    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="#" class="btn btn-default btn-flat">KYC Updated</a>-->
                                    <!--</li>-->
                                <?php elseif(!empty($persona_kyc) && empty($manual_kyc)): ?>
                                    <!--<li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">-->
                                    <!--    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="#" class="btn btn-default btn-flat">KYC Updated</a>-->
                                    <!--</li>-->
                                <?php elseif(!empty($persona_kyc) && !empty($manual_kyc)): ?>
                                    <!--<li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">-->
                                    <!--    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="#" class="btn btn-default btn-flat">KYC Updated</a>-->
                                    <!--</li>-->
                                <?php else: ?>
                                    <li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">
                                        <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="<?php echo e(url('/kyc')); ?>" class="btn btn-default btn-flat">KYC</a>
                                    </li>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                            <li class="" style="padding: 5px 14px 5px 14px; text-align: left;">
                                <i class="fa fa-sign-out"></i><a style="line-height: 0;color:#7d95b6" href="<?php echo e(url('/logout')); ?>" class="btn btn-default btn-flat"><?php echo app('translator')->get('message.dashboard.nav-menu.logout'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>


          <!-- sidebar -->
            <aside class="main-sidebar">
                <section class="sidebar">
                   <?php echo $__env->make('user_dashboard.layouts.common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </section>
            </aside>

            <div class="content-wrapper">
                <!-- Main content -->
                <section class="content">
            <?php echo $__env->yieldContent('content'); ?>
                </section>
            </div>

            <!-- footer -->
            <footer class="main-footer">
                <?php echo $__env->make('admin.layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </footer>
            <div class="control-sidebar-bg"></div>
        </div>


        <?php echo $__env->make('user_dashboard.layouts.common.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldContent('js'); ?>
    </body>
</html>


<?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/layouts/app.blade.php ENDPATH**/ ?>