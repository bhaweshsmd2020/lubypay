<div class="flash-container">
	<?php if(Session::has('message')): ?>
	  <div class="alert <?php echo e(Session::get('alert-class')); ?> text-center" style="margin-bottom:10px;" role="alert">
	    <?php echo e(Session::get('message')); ?>

	    <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
	  </div>
	<?php endif; ?>

	<?php if(session('status')): ?>
	    <div class="alert alert-success">
	        <?php echo session('status'); ?>

	        <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
	    </div>
	<?php endif; ?>

	<?php if(session('warning')): ?>
	    <div class="alert alert-warning">
	        <?php echo e(session('warning')); ?>

	        <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
	    </div>
	<?php endif; ?>

	<?php if(session('success')): ?>
	    <div class="alert alert-success">
	        <?php echo e(session('success')); ?>

	        <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
	    </div>
	<?php endif; ?>

	<?php if(session('error')): ?>
	    <div class="alert alert-danger">
	        <?php echo e(session('error')); ?>

	        <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
	    </div>
	<?php endif; ?>
</div><?php /**PATH /home/lubypay/public_html/accounts/resources/views/frontend/layouts/common/alert.blade.php ENDPATH**/ ?>