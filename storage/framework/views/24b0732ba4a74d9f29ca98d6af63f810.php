
<!-- temp-9, temp-15 and temp-20 - not in database, can be used later-->

<!-- start temp ID = 1 and ending temp-22, we should add from temp-23-->

<div class="box box-primary">

  
  <div class="box-header with-border">
    <h3 class="box-title underline">Email Templates</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-17' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/17")); ?>">Email Verification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-19' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/19")); ?>">2-Factor Authentication</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-21' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/21")); ?>">Identity/Address Verification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-18' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/18")); ?>">Password Reset</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-33' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/33")); ?>">Deposit Notification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-1' ? 'class=active' : ''); ?> ><!--1-->
        <a href="<?php echo e(URL::to("admin/template/1")); ?>">Transferred Payments</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-2' ? 'class=active' : ''); ?> ><!--2-->
        <a href="<?php echo e(URL::to("admin/template/2")); ?>">Received Payments</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-48' ? 'class=active' : ''); ?> ><!--4-->
        <a href="<?php echo e(URL::to("admin/template/48")); ?>">Request Payment Sender</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-4' ? 'class=active' : ''); ?> ><!--4-->
        <a href="<?php echo e(URL::to("admin/template/4")); ?>">Request Payment Receiver</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-5' ? 'class=active' : ''); ?> ><!--5-->
        <a href="<?php echo e(URL::to("admin/template/5")); ?>">Request Payment Acceptance (Requestor)</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-49' ? 'class=active' : ''); ?> ><!--5-->
        <a href="<?php echo e(URL::to("admin/template/49")); ?>">Request Payment Acceptance (Acceptor)</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-50' ? 'class=active' : ''); ?> ><!--5-->
        <a href="<?php echo e(URL::to("admin/template/50")); ?>">Request Payment Cancellation (Requestor)</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-51' ? 'class=active' : ''); ?> ><!--5-->
        <a href="<?php echo e(URL::to("admin/template/51")); ?>">Request Payment Cancellation (Acceptor)</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-44' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/44")); ?>">Payout Notification</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-45' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/45")); ?>">Exchange Notification</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-46' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/46")); ?>">Mobile Reload</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-47' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/47")); ?>">Gift Card</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-11' ? 'class=active' : ''); ?> ><!--11-->
        <a href="<?php echo e(URL::to("admin/template/11")); ?>">Ticket</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-12' ? 'class=active' : ''); ?> ><!--12-->
        <a href="<?php echo e(URL::to("admin/template/12")); ?>">Ticket Reply</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-13' ? 'class=active' : ''); ?> ><!--13-->
        <a href="<?php echo e(URL::to("admin/template/13")); ?>">Dispute Reply</a>
      </li>
      
      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-34' ? 'class=active' : ''); ?> >-->
      <!--  <a href="<?php echo e(URL::to("admin/template/34")); ?>">Create Store</a>-->
      <!--</li>-->

      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-35' ? 'class=active' : ''); ?> >-->
      <!--  <a href="<?php echo e(URL::to("admin/template/35")); ?>">Add Product</a>-->
      <!--</li>-->

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-36' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/36")); ?>">Place Order</a>
      </li>

    </ul>
  </div>
</div>

<div class="box box-primary">
  
  <div class="box-header with-border">
    <h3 class="box-title underline">Email Templates of Admin actions</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-29' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/29")); ?>">User Status Change</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-14' ? 'class=active' : ''); ?> ><!--14-->
        <a href="<?php echo e(URL::to("admin/template/14")); ?>">Merchant Payment</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-10' ? 'class=active' : ''); ?> ><!--10-->
        <a href="<?php echo e(URL::to("admin/template/10")); ?>">Payout</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-6' ? 'class=active' : ''); ?> ><!--6-->
        <a href="<?php echo e(URL::to("admin/template/6")); ?>">Transfers</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-8' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/8")); ?>">Request Payments (Success/Refund)</a><!--8-->
      </li>
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-16' ? 'class=active' : ''); ?> > <!--15-->
        <a href="<?php echo e(URL::to("admin/template/16")); ?>">Request Payments (Cancel/Pending)</a>
      </li>

    </ul>
  </div>
</div>

<div class="box box-primary">
  
  <div class="box-header with-border">
    <h3 class="box-title underline">Admin Notifications</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-23' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/23")); ?>">Deposit Notification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-24' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/24")); ?>">Payout Notification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-25' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/25")); ?>">Exchange Notification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-26' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/26")); ?>">Transfer Notification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-27' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/27")); ?>">Request Acceptance Notification</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-28' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/28")); ?>">Payment Notification</a>
      </li>
        <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-30' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/30")); ?>">Gift Card</a>
      </li>
       <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-31' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/31")); ?>">Mobile Reload</a>
      </li>
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-32' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/32")); ?>">User Verification</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-53' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/53")); ?>">Create Merchant</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-38' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/38")); ?>">Create Store</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-39' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/39")); ?>">Add Product</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-40' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/40")); ?>">Place Order</a>
      </li>

    </ul>
  </div>
</div>

<div class="box box-primary">
  
  <div class="box-header with-border">
    <h3 class="box-title underline">Merchant Notifications</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-52' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/52")); ?>">Create Merchant</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-41' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/41")); ?>">Create Store</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-42' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/42")); ?>">Add Product</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-43' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/43")); ?>">Place Order</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'menu-37' ? 'class=active' : ''); ?> >
        <a href="<?php echo e(URL::to("admin/template/37")); ?>">Push Notifications</a>
      </li>
      
    </ul>
  </div>
</div><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/common/mail_menu.blade.php ENDPATH**/ ?>