<div class="box box-primary">

  <div class="box-header with-border">
    <h3 class="box-title underline">Payment Methods</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'stripe' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/stripe/'.$currency->id)); ?>'>Stripe</a>
      </li>

      <li  <?php echo e(isset($list_menu) &&  $list_menu == 'paypal' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/paypal/'.$currency->id)); ?>'>PayPal</a>
      </li>

      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'twoCheckout' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/twoCheckout/'.$currency->id)); ?>'>2Checkout</a>-->
      <!--</li>-->

      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'payUMoney' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/payUMoney/'.$currency->id)); ?>'>PayUMoney</a>-->
      <!--</li>-->

      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'coinPayments' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/coinPayments/'.$currency->id)); ?>'>CoinPayments</a>-->
      <!--</li>-->

      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'Payeer' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/Payeer/'.$currency->id)); ?>'>Payeer</a>-->
      <!--</li>-->


      <li <?php echo e(isset($list_menu) &&  $list_menu == 'bank' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/bank/'.$currency->id)); ?>'>Banks</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'braintree' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/braintree/'.$currency->id)); ?>'>Braintree</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'plaid' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/payment-methods/plaid/'.$currency->id)); ?>'>ACH</a>
      </li>
      
    </ul>
  </div>
</div>

<?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/common/paymentMethod_menu.blade.php ENDPATH**/ ?>