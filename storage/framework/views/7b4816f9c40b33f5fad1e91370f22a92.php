
<?php $__env->startSection('title', 'Fraud Detection Settings'); ?>

<?php $__env->startSection('head_style'); ?>
   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/sweetalert/sweetalert.css')); ?>">

  <!-- bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/css/bootstrap-select.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<!-- Main content -->
<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="top-bar-title padding-bottom">Fraud Detection Settings</div>
            </div>
        </div>
    </div>
</div>

<div class="box">
  <div class="box-body">
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Currency : <span class="currencyName"><?php echo e($currency->name??''); ?></span>
        <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <?php $__currentLoopData = $currencyList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currencyItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="listItem">
              <a href="<?php echo e(url('admin/settings/fraud-detection/'.$transact->id.'/'.$currencyItem->id)??''); ?>"><?php echo e($currencyItem->name??''); ?></a>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-3 settings_bar_gap">
        <div class="box box-info box_info">
            <div class="panel-body">
                <h4 class="all_settings">
                    Transaction Types
                </h4>
                <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="<?php if($transact->name == $transaction->name){ echo 'active'; } ?>">
                            <a data-group="settings" href="<?php echo e(url('admin/settings/fraud-detection/'.$transaction->id.'/'.$currency->id)); ?>">
                                <i class="glyphicon glyphicon-cog">
                                </i>
                                <span>
                                    <?php echo e($transaction->name); ?>

                                </span>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title"><?php echo e($transact->name); ?></h3>
            </div>

            <form action="<?php echo e(url('admin/settings/update_fraud_detection')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" id="general_settings_form">
                <?php echo csrf_field(); ?>

                
                <input type="hidden" name="transaction_type" value="<?php echo e($transact->id??''); ?>" class="form-control">
                <input type="hidden" name="currency_type" value="<?php echo e($currency->id??''); ?>" class="form-control">

                <!-- box-body -->
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/Hour</label>
					  <div class="col-sm-5">
					    <input type="text" name="transactions_hour" class="form-control" value="<?php echo e($fraud->transactions_hour??''); ?>" placeholder="Transactions/Hour">
					  	<span class="text-danger"><?php echo e($errors->first('transactions_hour')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="transactions_day" class="form-control" value="<?php echo e($fraud->transactions_day??''); ?>" placeholder="Transactions/Day">
					  	<span class="text-danger"><?php echo e($errors->first('transactions_day')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Hour</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_hour" class="form-control" value="<?php echo e($fraud->amount_hour??''); ?>" placeholder="Amount/Hour">
					  	<span class="text-danger"><?php echo e($errors->first('amount_hour')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_day" class="form-control" value="<?php echo e($fraud->amount_day??''); ?>" placeholder="Transactions/Day">
					  	<span class="text-danger"><?php echo e($errors->first('amount_day')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Week</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_week" class="form-control" value="<?php echo e($fraud->amount_week??''); ?>" placeholder="Transactions/Week">
					  	<span class="text-danger"><?php echo e($errors->first('amount_week')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Month</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_month" class="form-control" value="<?php echo e($fraud->amount_month??''); ?>" placeholder="Transactions/Month">
					  	<span class="text-danger"><?php echo e($errors->first('amount_month')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Same Amount/Hour</label>
					  <div class="col-sm-5">
					    <input type="text" name="same_amount" class="form-control" value="<?php echo e($fraud->same_amount??''); ?>" placeholder="Same Amount/Hour">
					  	<span class="text-danger"><?php echo e($errors->first('same_amount')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/Email/Phone/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="email_day" class="form-control" value="<?php echo e($fraud->email_day??''); ?>" placeholder="Transactions/Email/Day">
					  	<span class="text-danger"><?php echo e($errors->first('email_day')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/IP Address/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="ipadd_day" class="form-control" value="<?php echo e($fraud->ipadd_day??''); ?>" placeholder="Transactions/IP Address/Day">
					  	<span class="text-danger"><?php echo e($errors->first('ipadd_day')); ?></span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">New User Account(Days)</label>
					  <div class="col-sm-5">
					    <input type="text" name="user_created_at" class="form-control" value="<?php echo e($fraud->user_created_at??''); ?>" placeholder="New User Transaction/Day">
					  	<span class="text-danger"><?php echo e($errors->first('user_created_at')); ?></span>
					  </div>
					</div>
				</div>
				<!-- /.box-body -->

				<!-- box-footer -->
				<div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat pull-right" id="general-settings-submit">
                        <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="general-settings-submit-text">Submit</span>
                    </button>
                </div>
  	            <!-- /.box-footer -->
            </form>
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


<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/fraud/index.blade.php ENDPATH**/ ?>