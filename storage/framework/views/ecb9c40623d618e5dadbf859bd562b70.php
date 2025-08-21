
<?php $__env->startSection('title', 'General Settings'); ?>

<?php $__env->startSection('head_style'); ?>
   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/sweetalert/sweetalert.css')); ?>">

  <!-- bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/css/bootstrap-select.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<!-- Main content -->
<div class="row">
    <div class="col-md-3 settings_bar_gap">
        <?php echo $__env->make('admin.common.settings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">Revenue Sharing Form</h3>
            </div>

            <form action="<?php echo e(url('admin/settings/revenues')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" id="form">
                <?php echo csrf_field(); ?>


                <!-- box-body -->
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Ticktappay Fee(%) :</label>
					  <div class="col-sm-6">
					    <input type="number" name="transactional" class="form-control" value="<?php echo e(@$result['transactional']); ?>" placeholder="Transactional Expenses" id="value1">
					  	<span class="text-danger"><?php echo e($errors->first('transactional')); ?></span>
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Support Maintenance Fee(%) :</label>
					  <div class="col-sm-6">
					    <input type="number" name="operational" class="form-control" value="<?php echo e(@$result['operational']); ?>" placeholder="Operational Expenses" id="value2">
					  	<span class="text-danger"><?php echo e($errors->first('operational')); ?></span>
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Account Maintenance Fee(%) :</label>
					  <div class="col-sm-6">
					    <input type="number" name="operational_a" class="form-control" value="<?php echo e(@$result['operational_a']); ?>" placeholder="Operational Expenses A" id="value3">
					  	<span class="text-danger"><?php echo e($errors->first('operational_a')); ?></span>
					  </div>
					</div>
				</div>
				<!-- /.box-body -->

				<!-- box-footer -->
				<?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_revenue_sharing')): ?>
    			<div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat pull-right" id="general-settings-submit">
                        <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="general-settings-submit-text">Submit</span>
                    </button>
                </div>
                <?php endif; ?>    
  	            <!-- /.box-footer -->
            </form>
        </div>
        
        <div class="box box-info">
            <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <div class="row">
                          <div class="col-md-8">
                              <h3 class="panel-title">Logs</h3>
                          </div>
                          <!--<div class="col-md-4">-->
                          <!--    <div class="btn-group pull-right">-->
                          <!--        <a href="" class="btn btn-sm btn-default btn-flat" id="csv">CSV</a>&nbsp;&nbsp;-->
                          <!--        <a href="" class="btn btn-sm btn-default btn-flat" id="pdf">PDF</a>-->
                          <!--    </div>-->
                          <!--</div>-->
                      </div>
                    </div>
                    <div class="panel-body">
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">S.No.</th>
                              <th scope="col">Ticktappay Fee(%)</th>
                              <th scope="col">Support Maintenance Fee(%)</th>
                              <th scope="col">Account Maintenance Fee(%)</th>
                              <th scope="col">Changed On</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th scope="row"><?php echo e(++$k); ?></th>
                                    <td><?php echo e($log->transactional); ?></td>
                                    <td><?php echo e($log->operational); ?></td>
                                    <td><?php echo e($log->operational_a); ?></td>
                                    <td><?php echo e(Carbon\Carbon::parse($log->created_at)->format('d-M-Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
  
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

  <!-- jquery.validate -->
  <script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

  <!-- jquery.validate additional-methods -->
  <script src="<?php echo e(asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js')); ?>" type="text/javascript"></script>

  <!-- sweetalert -->
  <script src="<?php echo e(asset('public/backend/sweetalert/sweetalert.min.js')); ?>" type="text/javascript"></script>

  <!-- bootstrap-select -->
  <script src="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/js/bootstrap-select.min.js')); ?>" type="text/javascript"></script>

  <!-- read-file-on-change -->
  <?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script>
    $('#form').on('submit', function() {
      var value1 = parseInt($("#value1").val()) > 0 ? parseInt($("#value1").val()) : 0;
      var value2 = parseInt($("#value2").val()) > 0 ? parseInt($("#value2").val()) : 0;
      var value3 = parseInt($("#value3").val()) > 0 ? parseInt($("#value3").val()) : 0;
      var sumOfValues = value1 + value2 + value3;
      if (sumOfValues > 100) {
        alert('Your sum of Fees are ' + sumOfValues + ' which is more than 100');
        return false;
      }
    });
  </script>

<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/settings/revenues.blade.php ENDPATH**/ ?>