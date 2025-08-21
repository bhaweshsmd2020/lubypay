

<?php $__env->startSection('title', 'Database Backups'); ?>

<?php $__env->startSection('head_style'); ?>
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
  <!-- Main content -->
  <div class="row">
    <div class="col-md-3 settings_bar_gap">
      <?php echo $__env->make('admin.common.settings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="col-xs-9">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Manage Databases</h3>
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_database_backup') && $is_demo == false): ?>
              <div style="float:right;"><a class="btn btn-success" href="<?php echo e(url('admin/backup/save')); ?>">New Backup</a></div>
            <?php endif; ?>
        </div>
        <hr>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <?php echo $dataTable->table(['class' => 'table table-striped table-hover dt-responsive', 'width' => '100%', 'cellspacing' => '0']); ?>

        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.dataTables js -->
<script src="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js')); ?>" type="text/javascript"></script>

<?php echo $dataTable->scripts(); ?>

<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/backups/view.blade.php ENDPATH**/ ?>