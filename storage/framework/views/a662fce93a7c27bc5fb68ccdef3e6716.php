<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title underline">Notification Templates</h3>
    </div>
    <div class="box-body no-padding" style="display: block;">
        <ul class="nav nav-pills nav-stacked">
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-29' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/29")); ?>">New User</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-1' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/1")); ?>">Deposit</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-2' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/2")); ?>">Payout</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-3' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/3")); ?>">Send Money</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-10' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/10")); ?>">Money Received</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-11' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/11")); ?>">Request Money Sender</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-4' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/4")); ?>">Request Money Receiver</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-12' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/12")); ?>">Approve Request Money Sender</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-8' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/8")); ?>">Approve Request Money Receiver</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-13' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/13")); ?>">Reject Request Money Sender</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-9' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/9")); ?>">Reject Request Money Receiver</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-5' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/5")); ?>">Exchange Money</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-6' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/6")); ?>">Gift Card</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-7' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/7")); ?>">Topup</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-23' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/23")); ?>">Manual KYC</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-24' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/24")); ?>">Auto KYC</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-25' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/25")); ?>">Create Ticket</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-14' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/14")); ?>">QR Store Payment</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-26' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/26")); ?>">Apply Card</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-27' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/27")); ?>">Card Reload</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-28' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/28")); ?>">Card Transfer (Sender)</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-32' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/32")); ?>">Card Transfer (Receiver)</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-31' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/31")); ?>">Card Subscription</a>
            </li>
        </ul>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title underline">mPOS Notification Templates</h3>
    </div>
    <div class="box-body no-padding" style="display: block;">
        <ul class="nav nav-pills nav-stacked">
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-30' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/30")); ?>">New User</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-15' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/15")); ?>">New Store</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-16' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/16")); ?>">New Product</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-17' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/17")); ?>">New Payment</a>
            </li>
        </ul>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title underline">Admin Notification Templates</h3>
    </div>
    <div class="box-body no-padding" style="display: block;">
        <ul class="nav nav-pills nav-stacked">
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-18' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/18")); ?>">Photo Verification</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-19' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/19")); ?>">Address Verification</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-20' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/20")); ?>">Identity Verification</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-21' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/21")); ?>">Payout Request</a>
            </li>
            <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-22' ? 'class=active' : ''); ?> >
                <a href="<?php echo e(URL::to("admin/notification/template/22")); ?>">Ticket Reply</a>
            </li>
        </ul>
    </div>
</div><?php /**PATH /home/lubynet/public_html/lubypaynew/resources/views/admin/common/notification_menu.blade.php ENDPATH**/ ?>