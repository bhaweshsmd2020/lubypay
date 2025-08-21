<div class="box box-primary">

  <div class="box-header with-border">
    <h3 class="box-title underline">Transaction Type</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li  <?php echo e(isset($list_menu) &&  $list_menu == 'deposit' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/deposit/'.$currency->id)); ?>'>Deposit</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'withdrawal' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/withdrawal/'.$currency->id)); ?>'>Payout</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'transfer' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/transfer/'.$currency->id)); ?>'>Transfer</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'request_payment' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/request_payment/'.$currency->id)); ?>'>Request Payment</a>
      </li>

      <li <?php echo e(isset($list_menu) &&  $list_menu == 'exchange' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/exchange/'.$currency->id)); ?>'>Exchange</a>
      </li>
      
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'recharge' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/recharge/'.$currency->id)); ?>'>Recharge</a>
      </li>
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'gift_card' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/gift_card/'.$currency->id)); ?>'>Gift Card</a>
      </li>
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'mpos' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/mpos/'.$currency->id)); ?>'>MPOS</a>
      </li>
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'nfc' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/nfc/'.$currency->id)); ?>'>NFC</a>
      </li>
      <li <?php echo e(isset($list_menu) &&  $list_menu == 'collect_payment' ? 'class=active' : ''); ?> >
        <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/collect_payment/'.$currency->id)); ?>'>Collect Payment</a>
      </li>
      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'electricity_bill' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/electricity_bill/'.$currency->id)); ?>'>Electricity Bill</a>-->
      <!--</li>-->
      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'cable' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/cable/'.$currency->id)); ?>'>Cable TV</a>-->
      <!--</li>-->
      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'gas_bill' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/gas_bill/'.$currency->id)); ?>'>Gas Bill</a>-->
      <!--</li>-->
      <!--<li <?php echo e(isset($list_menu) &&  $list_menu == 'water_bill' ? 'class=active' : ''); ?> >-->
      <!--  <a data-spinner="true" href='<?php echo e(url('admin/settings/feeslimit/water_bill/'.$currency->id)); ?>'>Water Bill</a>-->
      <!--</li>-->

    </ul>
  </div>
</div>

<?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/common/currency_menu.blade.php ENDPATH**/ ?>