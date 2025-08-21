

<?php $__env->startSection('title', 'Wallets'); ?>

<?php $__env->startSection('head_style'); ?>
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
               <li >
                  <a href='<?php echo e(url("admin/users/edit/$users->id")); ?>'>Profile</a>
                </li>

                <li>
                  <a href="<?php echo e(url("admin/users/transactions/$users->id")); ?>">Transactions</a>
                </li>
                <li class="active">
                  <a href="<?php echo e(url("admin/users/wallets/$users->id")); ?>">Wallets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/tickets/$users->id")); ?>">Tickets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/disputes/$users->id")); ?>">Disputes</a>
                </li>
                
                <li>
                  <a href="<?php echo e(url("admin/users/photoproof/$users->id")); ?>">Photo Proof</a>
                </li>
                
                <li >
                  <a href="<?php echo e(url("admin/users/addressproof/$users->id")); ?>">Address Proof</a>
                </li>
                
                <li >
                  <a href="<?php echo e(url("admin/users/idproof/$users->id")); ?>">Identity Proof</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/bankdetails/$users->id")); ?>">Bank Details</a>
                </li>
                 <li>
                  <a href="<?php echo e(url("admin/users/address_edit/$users->id")); ?>">Address</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <?php if($users->status == 'Inactive'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;<span class="label label-danger">Inactive</span></h3>
    <?php elseif($users->status == 'Suspended'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;<span class="label label-warning">Suspended</span></h3>
    <?php elseif($users->status == 'Active'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;<span class="label label-success">Active</span></h3>
    <?php endif; ?>

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover" id="eachuserwallet">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Balance</th>
                            <th>Currency</th>
                            <th>Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($wallets): ?>
                            <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(dateFormat($wallet->created_at)); ?></td>

                                    <td><?php echo e($wallet->currency->type != 'fiat' ? $wallet->balance : formatNumber($wallet->balance)); ?></td>

                                    <td><?php echo e($wallet->currency->code); ?></td>

                                    <?php if($wallet->is_default == 'Yes'): ?>
                                        <td><span class="label label-success">Yes</span></td>
                                    <?php elseif($wallet->is_default == 'No'): ?>
                                        <td><span class="label label-danger">No</span></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            No wallet Found!
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.dataTables js -->
<script src="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
      $("#eachuserwallet").DataTable({
            "order": [],
            "columnDefs": [
            {
                "className": "dt-center",
                "targets": "_all"
            }
            ],
            "language": '<?php echo e(Session::get('dflt_lang')); ?>',
            "pageLength": '<?php echo e(Session::get('row_per_page')); ?>'
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/users/eachuserwallet.blade.php ENDPATH**/ ?>