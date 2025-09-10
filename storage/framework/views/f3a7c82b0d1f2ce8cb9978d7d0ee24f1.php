<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = getCompanyLogoWithoutSession();
    $persona_kyc = DB::table('kycdatastores')->where('user_id', auth()->user()->id)->where('status', 'COMPLETED')->first();
    $manual_kyc = DB::table('users')->where('id', auth()->user()->id)->where('photo_verified', '1')->where('address_verified', '1')->where('identity_verified', '1')->first();
?>
<style>
    a{
        text-decoration: none;
    }
</style>


<ul class="sidebar-menu">
    <li class="<?= isset($menu) && ($menu == 'dashboard') ? 'active' : '' ?>">
        <a href="<?php echo e(url('/dashboard')); ?>"><i class="fa fa-dashboard"></i><?php echo app('translator')->get('message.dashboard.nav-menu.dashboard'); ?></a>
    </li>
    <?php if(Common::has_permission(auth()->id(),'manage_merchant')): ?>
        <!--<li class="<?= isset($menu) && ($menu == 'merchant') ? 'active' : '' ?>">-->
        <!--    <a href="<?php echo e(url('/merchants')); ?>"><i class="fa fa-user"></i><?php echo app('translator')->get('message.dashboard.nav-menu.merchants'); ?></a>-->
        <!--</li>-->
    <?php endif; ?>
    
    <?php if(auth()->user()->type == 'merchant'): ?>                    
        <li class="<?= isset($menu) && ($menu == 'my_store') ? 'active' : '' ?>">                        
             <a href="<?php echo e(url('/mystore')); ?>" class="nav-link"> <i class="fa fa-spinner"></i> My Store </a>                    
        </li> 
    <?php endif; ?>
    
    <?php if(Common::has_permission(auth()->id(),'manage_transaction')): ?>
        <li class="<?= isset($menu) && ($menu == 'transactions') ? 'active' : '' ?>">
            <a href="<?php echo e(url('/transactions')); ?>"><i class="fa fa-file"></i><?php echo app('translator')->get('message.dashboard.nav-menu.transactions'); ?></a>
        </li>
    <?php endif; ?>

    <li class="<?php echo e(isset($menu) && $menu == 'wallet' ? 'active' : ''); ?>">
        <a href="<?php echo e(route('wallet')); ?>"> <i class="bi bi-wallet-fill" style="margin-right: 18px;"></i> Wallet </a>
    </li>

     <li class=" <?php echo e(isset($menu) && $menu == 'comissions' ? 'active' : ''); ?>">
        <a href="<?php echo e(route('comissions')); ?>"> <i class="bi bi-file-earmark-text" style="margin-right: 18px;"></i>  Commissions </a>
    </li>
    
    <?php if((empty($persona_kyc) && !empty($manual_kyc)) || (!empty($persona_kyc) && empty($manual_kyc)) || (!empty($persona_kyc) && !empty($manual_kyc))): ?>
        <?php if(auth()->user()->role_id == 2): ?>   
            <li class="<?= isset($menu) && ($menu == 'deposit') ? 'active' : '' ?>">
                <?php if(Common::has_permission(auth()->id(),'manage_deposit')): ?>
                    <a href="<?php echo e(url('deposit')); ?>"><i class="fa fa-money"></i><?php echo app('translator')->get('message.dashboard.button.deposit'); ?></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    
        <li class="<?= isset($menu) && ($menu == 'payouts') ? 'active' : '' ?>">
            <?php if(Common::has_permission(auth()->id(),'manage_withdrawal')): ?>
                <a href="<?php echo e(url('payouts')); ?>"><i class="fa fa-usd"></i>&nbsp;<?php echo app('translator')->get('message.dashboard.button.payout'); ?></a>
            <?php endif; ?>
        </li>
        
        <?php if(auth()->user()->role_id == 2): ?>  
            <li class="<?= isset($menu) && ($menu == 'exchange') ? 'active' : '' ?>">
                <?php if(Common::has_permission(auth()->id(),'manage_exchange')): ?>
                    <a href="<?php echo e(url('exchange')); ?>"><i class="fa fa-exchange"></i> <?php echo app('translator')->get('message.dashboard.button.exchange'); ?></a>
                <?php endif; ?>
            </li>
          
            <li class="<?= isset($menu) && ($menu == 'payouts') ? 'active' : '' ?>">
                <?php if(Common::has_permission(auth()->id(),'manage_withdrawal')): ?>
                    <a href="<?php echo e(url('giftcard')); ?>"><i class="fa fa-usd"></i>Gift Card</a>
                <?php endif; ?>
            </li>
        
            <?php if(Common::has_permission(auth()->id(),'manage_deposit')): ?>
                <li>
                    <a href="<?php echo e(url('/ding-connect')); ?>"><i class="fa fa-mobile"></i><span>Mobile Reload</span></a>
                </li>
            <?php endif; ?>
            
            <?php if(Common::has_permission(auth()->id(),'manage_transfer')): ?>
                <li class="<?= isset($menu) && ($menu == 'send_receive') ? 'active' : '' ?>">
                    <a href="<?php echo e(url('/moneytransfer')); ?>"><i class="fa fa-share-square"></i><?php echo app('translator')->get('message.dashboard.nav-menu.send-req'); ?></a>
                </li>
            <?php elseif(Common::has_permission(auth()->id(),'manage_request_payment')): ?>
                <li class="<?= isset($menu) && ($menu == 'request_payment') ? 'active' : '' ?>">
                    <a href="<?php echo e(url('/request_payment/add')); ?>"><i class="fa fa-exchange"></i><?php echo app('translator')->get('message.dashboard.nav-menu.send-req'); ?></a>
                </li>
            <?php endif; ?>
            
            <?php if(Common::has_permission(auth()->id(),'manage_dispute')): ?>
                <li class="<?= isset($menu) && ($menu == 'dispute') ? 'active' : '' ?>">
                    <a href="<?php echo e(url('/disputes')); ?>"><i class="fa fa-ticket"></i><?php echo app('translator')->get('message.dashboard.nav-menu.disputes'); ?></a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if(Common::has_permission(auth()->id(),'manage_ticket')): ?>
            <li class="<?= isset($menu) && ($menu == 'ticket') ? 'active' : '' ?>">
                <a href="<?php echo e(url('/tickets')); ?>"><i class="fa fa-question-circle"></i><?php echo app('translator')->get('message.dashboard.nav-menu.tickets'); ?></a>
            </li>
        <?php endif; ?>
    <?php endif; ?>
</ul><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/layouts/common/header.blade.php ENDPATH**/ ?>