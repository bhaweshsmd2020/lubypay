<footer style="background:#ab185a!important;">
  <div class="container">
      <div class="row">
          <div class="col-md-12">
          	<?php
          		$company_name = getCompanyName();
          	?>
             <p class="copyright"><?php echo app('translator')->get('message.footer.copyright'); ?>&nbsp;© <?php echo e(date('Y')); ?> &nbsp; <?php echo e($company_name); ?> | &nbsp;All rights reserved. |<!-- <?php echo app('translator')->get('message.footer.copyright-text'); ?>-->
             Powered By &nbsp;<a href="https://quickewallet.com" target="_blank" style="color:white">Quickewallet</a></p>
             <p style="color:white;    text-align: center;">
                 All brand names and logos are the property of their respective owners. The images and logos are used for identification purposes only, and do not imply product endorsement or affiliation with Luby Pay
             </p>
          </div>
      </div>
  </div>
</footer><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/frontend/layouts/common/footer.blade.php ENDPATH**/ ?>