

<?php $__env->startSection('title', 'Merchant Documents'); ?>

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
    <div class="col-md-9">
      <div class="box box_info">
            <div class="box-header">
              <h3 class="box-title">Manage Merchant Documents</h3>
              <a href="<?php echo e(url('admin/settings/add-merchant-group-document')); ?>"
                       class="btn btn-primary pull-right">Add New Document</a>
            </div>
            <hr>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table recent_activity" align="left">
                    <thead>
                        <tr>
                            <td class="text-left">
                                <strong>Name</strong>
                            </td>
                            <td class="text-left">
                                <strong>Action</strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($document->name); ?></td>
                                <td>
                                    <a href="<?php echo e(url('admin/settings/edit-merchant-document/'.$document->id)); ?>" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="<?php echo e(url('admin/settings/merchant-document/delete/'.$document->id)); ?>" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/test/resources/views/admin/merchant_document/list.blade.php ENDPATH**/ ?>