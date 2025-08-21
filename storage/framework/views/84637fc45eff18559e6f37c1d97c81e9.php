
<?php $__env->startSection('content'); ?>
<!--Start Section-->
<section class="section-06 history padding-30">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
        <div class="card">
          <div class="card-header">
           <h4 class=""> <?php echo app('translator')->get('message.dashboard.dispute.dispute'); ?></h4>
          </div>
          <div class="wap-wed mt20 mb20">

            <?php if($list->count() > 0): ?>
              <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="card-body-custom">
                <div class="row">
                  <div class="col-md-10">
                    <div class="h4"><?php echo app('translator')->get('message.dashboard.dispute.title'); ?> :<span class="ash-font"> <?php echo e($result->title); ?> </span></div>

                    <h5 class="mt10"><strong><ins><?php echo app('translator')->get('message.dashboard.dispute.dispute-id'); ?></ins></strong>: <?php echo e(isset($result->code) ? $result->code :"-"); ?></h5>

                    <h5 class="mt10"><strong><ins><?php echo app('translator')->get('message.dashboard.dispute.transaction-id'); ?></ins></strong>: <?php echo e(isset($result->transaction) ? $result->transaction->uuid :"-"); ?></h5>

                    <?php if(Auth::user()->id != $result->claimant_id): ?>
                    <div class="mt10"><strong><ins><?php echo app('translator')->get('message.dashboard.dispute.claimant'); ?></ins></strong> :
                        <?php echo e($result->claimant->first_name .' '.$result->claimant->last_name); ?>

                    </div>
                    <?php endif; ?>

                    <?php if(Auth::user()->id != $result->defendant_id): ?>
                    <div class="mt10"><strong><ins><?php echo app('translator')->get('message.dashboard.dispute.defendant'); ?></ins></strong> :
                      <?php echo e($result->defendant->first_name .' '.$result->defendant->last_name); ?>

                    </div>
                    <?php endif; ?>

                    <div class="mt10"><strong><ins><?php echo app('translator')->get('message.dashboard.dispute.created-at'); ?></ins></strong> : <?php echo e(dateFormat($result->created_at)); ?> </div>
                    <div class="mt10"><strong><?php echo app('translator')->get('message.dashboard.dispute.status'); ?></strong> :

                  <?php if($result->status =='Open'): ?>
                    <span class="badge badge-primary"><?php echo app('translator')->get('message.dashboard.dispute.status-type.open'); ?></span>
                  <?php elseif($result->status =='Solve'): ?>
                    <span class="badge badge-success"><?php echo app('translator')->get('message.dashboard.dispute.status-type.solved'); ?></span>
                  <?php elseif($result->status =='Close'): ?>
                    <span class="badge badge-danger"><?php echo app('translator')->get('message.dashboard.dispute.status-type.closed'); ?></span>
                  <?php endif; ?>

                    </div>
                  </div>
                  <div class="col-md-2">
                    <p class="text-right">
                      <a href='<?php echo e(url("dispute/discussion/$result->id")); ?>' class="btn btn-cust">
                        <?php echo app('translator')->get('message.dashboard.button.details'); ?>
                      </a>
                    </p>
                  </div>
                </div>
              </div>
            <hr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <!--<h4><?php echo app('translator')->get('message.dashboard.dispute.no-dispute'); ?></h4>-->
            <h5 style="padding: 15px 10px; text-align: center;"> <img src="<?php echo e(url('public/images/nodata.png')); ?>" style="    margin: 35px 0px;"></h5>
            <br>
            <?php endif; ?>

          </div>
          <div class="card-footer">
             <?php echo e($list->links('vendor.pagination.bootstrap-5')); ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--End Section-->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/dispute/list.blade.php ENDPATH**/ ?>