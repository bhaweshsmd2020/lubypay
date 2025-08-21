<?php
    $transactions = DB::table('transactions')->count();
    $deposits = DB::table('deposits')->count();
    $payouts = DB::table('withdrawals')->count();
    $transfers = DB::table('transfers')->count();
    $request_payments = DB::table('request_payments')->count();
    $exchange_currencies = DB::table('currency_exchanges')->count();
    $mpos_transactions = DB::table('transactions')->whereNotNull('store_fee')->count();
    $topups = DB::table('transactions')->where(['transaction_type_id' => 15])->count();
    $gift_cards = DB::table('gift_cards')->count();
?>

<style>
    .fa-circle{
        font-size: 10px !important;
        margin-left: 5px;
        color: #122d83;
    }
</style>

<ul class="sidebar-menu">
    <li <?= $menu == 'dashboard' ? ' class="active"' : 'treeview'?>>
        <a href="<?php echo e(url('admin/home')); ?>">
            <i class="fa fa-calculator"></i><span>Overview</span>
        </a>
    </li>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_admins')): ?>
        <li <?= $menu == 'admin_users_list' ? ' class="active"' : 'treeview'?>>
            <a href="<?php echo e(url('admin/admin_users')); ?>">
                <i class="fa fa-user-md"></i><span>Administrators</span>
                <?php if(Common::unread_count('admins') > 0): ?>
                    <span class="label label-warning"><?php echo e(Common::unread_count('admins')); ?></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>

    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_user') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_admins') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_transaction') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_deposit') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_withdrawal') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_transfer') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_request_payment') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_exchange') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mobile_topup') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_report') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_revenue')): ?>
        <li <?= $menu == 'users' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class="glyphicon glyphicon-user"></i><span>Ewallet Customers</span>
                <?php if(Common::user_unread_count('users', 'type', 'user') > 0 || Common::kyc_unread_count('users', 'type', 'user') > 0 || Common::unread_tansactions('transactions') > 0 || Common::unread_count('deposits') > 0 || Common::user_unread_count('withdrawals', 'pay_type', '1') > 0 || Common::unread_count('transfers') > 0 || Common::unread_count('request_payments') > 0 || Common::unread_count('currency_exchanges') > 0 || Common::user_unread_count('transactions', 'transaction_type_id', '15') > 0 || Common::unread_count('gift_cards') > 0 || Common::unread_revenues('transactions') > 0 || Common::unread_report('transactions') > 0): ?>
                    <i class="fa fa-circle"></i>
                <?php endif; ?>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_user')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'users_list' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/users')); ?>">
                            <i class="fa fa-user-circle-o"></i><span >Customers</span> 
                            <?php if(Common::user_unread_count('users', 'role_id', '2') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::user_unread_count('users', 'role_id', '2')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                
                    <li <?= isset($sub_menu) && $sub_menu == 'manualkyc' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/manual-kyc')); ?>">
                            <i class="fa fa-crosshairs"></i><span>KYC Status</span>
                            <?php if(Common::kyc_unread_count('users', 'role_id', '2') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::kyc_unread_count('users', 'role_id', '2')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>  
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_transaction')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'all_transactions' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/transactions')); ?>">
                            <i class="fa fa-history"></i><span >Transactions</span>
                            <?php if(Common::unread_tansactions('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_tansactions('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_deposit')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'deposits' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/deposits')); ?>">
                            <i class="fa fa-plus-square"></i><span >Deposits</span>
                            <?php if(Common::unread_count('deposits') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('deposits')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_withdrawal')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'withdrawals' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/withdrawals')); ?>">
                            <i class="fa fa-money"></i><span >Payouts</span>
                            <?php if(Common::user_unread_count('withdrawals', 'pay_type', '1') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::user_unread_count('withdrawals', 'pay_type', '1')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_transfer')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'transfers' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/transfers')); ?>">
                            <i class="fa fa-upload"></i><span >Transfers</span>
                            <?php if(Common::unread_count('transfers') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('transfers')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_request_payment')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'request_payments' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/request_payments')); ?>">
                            <i class="fa fa-calculator"></i><span >Request Payments</span>
                            <?php if(Common::unread_count('request_payments') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('request_payments')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_exchange')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'exchanges' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/exchanges')); ?>">
                            <i class="fa fa-exchange"></i><span >Currency Exchange</span>
                            <?php if(Common::unread_count('currency_exchanges') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('currency_exchanges')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mobile_topup')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'prepaid' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/topup-list')); ?>">
                            <i class="fa fa-mobile"></i><span >Mobile Reload</span>
                            <?php if(Common::topup_unread_count('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::topup_unread_count('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'gift-card' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/card/gift-card')); ?>">
                            <i class="fa fa-credit-card-alt"></i><span >Gift Cards</span>
                            <?php if(Common::unread_count('gift_cards') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('gift_cards')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_revenue')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'wallet_revenues' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/revenues')); ?>">
                            <i class="fa fa-book"></i><span >Revenues</span>
                            <?php if(Common::unread_revenues('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_revenues('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_report')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'wallet_reports' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/report')); ?>">
                            <i class="fa fa-line-chart"></i><span >Reports</span>
                            <?php if(Common::unread_report('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_report('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_stores') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_kyc') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_transactions') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_payouts') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_revenues') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_reports')): ?>
        <li <?= $menu == 'mpos' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class="fa fa-user-secret"></i><span>Virtual Terminal</span>
                <?php if(Common::user_unread_count('users', 'type', 'merchant') > 0 || Common::unread_count('stores') > 0 || Common::kyc_unread_count('users', 'type', 'merchant') > 0 || Common::unread_mpos_tansactions('transactions') > 0 || Common::user_unread_count('withdrawals', 'pay_type', '2') > 0 || Common::unread_mpos_revenues('transactions') > 0 || Common::unread_mpos_report('transactions') > 0): ?>
                    <i class="fa fa-circle"></i>
                <?php endif; ?>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'merchant_list' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/merchant-list')); ?>">
                            <i class="fa fa-user-secret"></i><span>Merchants</span>
                            <?php if(Common::user_unread_count('users', 'role_id', '3') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::user_unread_count('users', 'role_id', '3')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_stores')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'store_list' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/store-list')); ?>">
                            <i class="fa fa-home"></i><span>Stores</span>
                            <?php if(Common::unread_count('stores') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('stores')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_kyc')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'manualkyc' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/mpos-manual-kyc')); ?>">
                            <i class="fa fa-crosshairs"></i><span>KYC Status</span>
                            <?php if(Common::kyc_unread_count('users', 'role_id', '3') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::kyc_unread_count('users', 'role_id', '3')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li> 
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_transactions')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'store_transactions' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/mpos')); ?>">
                            <i class='fa fa-history'></i><span>Transactions</span>
                            <?php if(Common::unread_mpos_tansactions('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_mpos_tansactions('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_payouts')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'store_payouts' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/mpos/payouts')); ?>">
                            <i class='fa fa-money'></i><span>Payouts</span>
                            <?php if(Common::user_unread_count('withdrawals', 'pay_type', '2') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::user_unread_count('withdrawals', 'pay_type', '2')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_revenues')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'store_revenues' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/store/revenues')); ?>">
                            <i class="fa fa-line-chart"></i><span>Revenues</span>
                            <?php if(Common::unread_mpos_revenues('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_mpos_revenues('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                    
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_reports')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'store_reports' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/store/report')); ?>">
                            <i class="fa fa-book"></i><span>Reports</span>
                            <?php if(Common::unread_mpos_report('transactions') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_mpos_report('transactions')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_partner') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_holders') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_reloads') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_transfers') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_subscriptions')): ?>
        <li <?= $menu == 'cards' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class="fa fa-credit-card-alt"></i><span>Virtual Cards</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_partner')): ?>
                    <!--<li <?= isset($sub_menu) && $sub_menu == 'partner' ? ' class="active"' : ''?> >-->
                    <!--    <a href="<?php echo e(url('admin/partner')); ?>">-->
                    <!--        <i class="fa fa-user-secret"></i><span>Partner</span>-->
                    <!--    </a>-->
                    <!--</li>-->
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_holders')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'requests' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/card/requests')); ?>">
                            <i class="fa fa-user-o"></i><span>Cardholders</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_reloads')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'reloads' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/card/reloads')); ?>">
                            <i class="fa fa-plus-square"></i><span>Reloads</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_transfers')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'transfers' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/card/transfers')); ?>">
                            <i class="fa fa-upload"></i><span>Transactions</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_subscriptions')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'subscriptions' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/card/subscriptions')); ?>">
                            <i class="fa fa-paper-plane"></i><span>Subscriptions</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_tickets') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_disputes')): ?>
        <li <?= $menu == 'support' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class='fa fa-ticket'></i><span>Support</span>
                <?php if(Common::unread_count('tickets') > 0): ?>
                    <i class="fa fa-circle"></i>
                <?php endif; ?>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_tickets')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'ticket' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/tickets/list')); ?>">
                            <i class="fa fa-spinner"></i><span >Help & Support</span>
                            <?php if(Common::unread_count('tickets') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('tickets')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_disputes')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'dispute' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/disputes')); ?>">
                            <i class="fa fa-ticket"></i><span >Disputes</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_banner') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_offer') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_push_notifications') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_currency') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_country') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_template') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_template') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_language') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_app_level') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_notifications')): ?>
        <li <?= $menu == 'configurations' || $menu == 'email' || $menu == 'sms' || $menu == 'language' || $menu == 'notification' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class='fa fa-cog'></i><span>Configurations</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_banner')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'banner' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/banner')); ?>">
                            <i class="fa fa-image"></i><span >App Banners</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_offer')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'pushsms' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/pushsms')); ?>">
                            <i class="fa fa-bell"></i><span >App Offers</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_push_notifications')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'promotions' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/promotions')); ?>">
                            <i class="fa fa-upload"></i><span >Push Notifications</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_currency')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'currency' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/settings/currency')); ?>">
                            <i class="fa fa-money"></i><span>Currencies & Fees</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_currency')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'fees' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/card/fees')); ?>">
                            <i class="fa fa-money"></i><span>Card Settings</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_country')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'country' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/settings/country')); ?>">
                            <i class="fa fa-flag"></i><span>Countries</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_template')): ?>
                    <li <?= $menu == 'email' ? ' class="active treeview"' : 'treeview'?> >
                        <a href="#">
                            <i class="fa fa-envelope"></i><span>Email</span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_setting')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'email_config' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/settings/email')); ?>">
                                        <i class="fa fa-cog"></i><span>Config</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_template')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'email_template' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/template/17')); ?>">
                                        <i class="fa fa-newspaper-o"></i><span>Templates</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_notifications')): ?>
                    <li <?= $menu == 'notification' ? ' class="active treeview"' : 'treeview'?> >
                        <a href="#">
                            <i class="fa fa-bell"></i><span>Notifications</span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_notifications')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'notification-settings' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/settings/notification-types')); ?>">
                                        <i class="fa fa-cog"></i><span>Config</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_notifications')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'notification_template' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/notification/template/1')); ?>">
                                        <i class="fa fa-newspaper-o"></i><span>Templates</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_template')): ?>
                    <li <?= $menu == 'sms' ? ' class="active treeview"' : 'treeview'?> >
                        <a href="#">
                            <i class="glyphicon glyphicon-phone"></i><span>SMS</span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_setting')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'sms_config' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/settings/sms/twilio')); ?>">
                                        <i class="fa fa-cog"></i><span>Config</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_template')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'sms_template' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/sms-template/21')); ?>">
                                        <i class="fa fa-newspaper-o"></i><span>Templates</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_language') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_app_level')): ?>
                    <li <?= $menu == 'language' ? ' class="active treeview"' : 'treeview'?> >
                        <a href="#">
                            <i class="fa fa-language"></i><span>Languages</span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_language')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'language_config' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/settings/language')); ?>">
                                        <i class="fa fa-cog"></i><span>Config</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_app_level')): ?>
                                <li <?= isset($sub_menu) && $sub_menu == 'language_contents' ? ' class="active"' : ''?>>
                                    <a href="<?php echo e(url('admin/labels')); ?>">
                                        <i class="fa fa-newspaper-o"></i><span>Contents</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_general_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_blockio_settings') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_social_links') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant_group') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_group') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_role') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_database_backup') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_meta') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_page') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_enable_woocommerce') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_preference') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_utility_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_revenue_sharing') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_api_credentials') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_appstore_credentials') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_services') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_nfc_credentials') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_keys')): ?>
        <li <?= $menu == 'settings' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class='fa fa-wrench'></i><span>Settings</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_general_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_blockio_settings') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_social_links') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant_group') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_group') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_role') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_database_backup') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_meta') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_page') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_enable_woocommerce') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_preference') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_utility_setting') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_revenue_sharing')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'general' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/settings')); ?>">
                            <i class="fa fa-tv"></i><span >General</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_api_credentials') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_appstore_credentials') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_services') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_nfc_credentials') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_keys')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'app-store-credentials' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/settings/app-store-credentials')); ?>">
                            <i class="fa fa-phone"></i><span >App Settings</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'fraud_settings_view') || Common::has_permission(\Auth::guard('admin')->user()->id, 'fraud_report_view') || Common::has_permission(\Auth::guard('admin')->user()->id, 'view_activity_log')): ?>
        <li <?= $menu == 'fraud_detection' ? ' class="active treeview"' : 'treeview'?> >
            <a href="#">
                <i class='fa fa-lock'></i><span>AML</span>
                <?php if(Common::unread_count('rule_reports') > 0): ?>
                    <i class="fa fa-circle"></i>
                <?php endif; ?>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'fraud_settings_view')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'settings' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/settings/fraud-detection/1/9')); ?>">
                            <i class="fa fa-cog"></i>
                            <span>Config</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'fraud_report_view')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'reports' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/fraud-reports')); ?>">
                            <i class="fa fa-book"></i><span>Report</span>
                            <?php if(Common::unread_count('rule_reports') > 0): ?>
                                <span class="label label-warning"><?php echo e(Common::unread_count('rule_reports')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_activity_log')): ?>
                    <li <?= isset($sub_menu) && $sub_menu == 'activity_logs' ? ' class="active"' : ''?> >
                        <a href="<?php echo e(url('admin/activity_logs')); ?>">
                            <i class="fa fa-eye"></i>
                            <span>Activity Logs</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_unlink_device')): ?>
        <li <?= $menu == 'unlinkdevice' ? ' class="active"' : 'treeview'?>>
            <a href="<?php echo e(url('admin/unlink-device')); ?>">
                <i class="fa fa-phone"></i><span>Unlink Device</span>
                <?php if(Common::lnkdev_unread_count('users') > 0): ?>
                    <span class="label label-warning"><?php echo e(Common::lnkdev_unread_count('users')); ?></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_maintainance')): ?>
        <li <?= $menu == 'maintainance' ? ' class="active"' : 'treeview'?>>
            <a href="<?php echo e(url('admin/maintainance-settings')); ?>">
                <i class="fa fa-cogs"></i><span>Maintenance</span>
            </a>
        </li>
    <?php endif; ?>
    
    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_survey')): ?>
        <li <?= $menu == 'survey' ? ' class="active"' : 'treeview'?>>
            <a href="<?php echo e(url('admin/survey')); ?>">
                <i class="fa fa-cubes"></i><span>Survey</span>
            </a>
        </li>
    <?php endif; ?>
</ul><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/layouts/partials/sidebar_menu.blade.php ENDPATH**/ ?>