<footer style="background:#ab185a!important;">
  <div class="container">
      <div class="row">
          <div class="col-md-12">
          	<?php
          		$company_name = getCompanyName();
          	?>
             <p class="copyright"><?php echo app('translator')->get('message.footer.copyright'); ?>&nbsp;© <?php echo e(date('Y')); ?> &nbsp;&nbsp; <?php echo e($company_name); ?>. &nbsp;All rights reserved. |<!-- <?php echo app('translator')->get('message.footer.copyright-text'); ?>-->
             Powered By &nbsp;<a href="<?php echo e(url('/')); ?>" target="_blank" style="color:white">Quickewallet</a></p>
             <p style="color:white;    text-align: center;">
                 All brand names and logos are the property of their respective owners, are used for identification purposes only, and do not imply product endorsement or affiliation with TickTap Pay
             </p>
          </div>
      </div>
  </div>
</footer><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/frontend/layouts/common/footer.blade.php ENDPATH**/ ?>