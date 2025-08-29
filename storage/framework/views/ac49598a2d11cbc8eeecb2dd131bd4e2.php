<div class="flash-container">
      <?php if(session('message')): ?>
          <div class="alert <?php echo e(session('alert-class')); ?> text-center" style="margin-bottom:10px;" role="alert">
            <?php echo e(session('message')); ?>

            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
          </div>
      <?php endif; ?>

      <?php if(!empty($error)): ?>
          <div class="alert alert-danger text-center" style="margin-bottom:10px;" role="alert">
            <?php echo e($error); ?>

            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
          </div>
      <?php endif; ?>

      <?php if($errors->any()): ?>
          <div class="alert alert-danger text-center" style="margin-bottom:10px;" role="alert">
              <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php echo e($error); ?> <br/>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
      <?php endif; ?>

      <?php if(session('success')): ?>
          <div class="alert alert-success text-center" style="margin-bottom:10px;" role="alert">
            <?php echo e(session('success')); ?>

            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
          </div>
      <?php endif; ?>

      <?php if(session('error')): ?>
          <div class="alert alert-danger text-center" style="margin-bottom:10px;" role="alert">
            <?php echo e(session('error')); ?>

            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
          </div>
      <?php endif; ?>
  </div><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/layouts/common/alert.blade.php ENDPATH**/ ?>