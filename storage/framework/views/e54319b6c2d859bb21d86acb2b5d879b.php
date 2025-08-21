<?php

use App\Models\DocumentVerification;

$documents = DocumentVerification::where(['user_id' => $users->id])->get(['id', 'verification_type']);

?>



<?php $__env->startSection('title', 'Bank Details'); ?>

<?php $__env->startSection('head_style'); ?>
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
     <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">
                        <?php if($users->status == 'Inactive'): ?>
                            <p><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-danger">Inactive</span></p>
                        <?php elseif($users->status == 'Suspended'): ?>
                            <p><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-warning">Suspended</span></p>
                        <?php elseif($users->status == 'Active'): ?>
                            <p><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-success">Active</span></p>
                        <?php endif; ?>
                  </div>
                </div>
                <div class="col-md-3 pull-right" style="margin-top:8px;">
                        <a href="<?php echo e(url('admin/users')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-chevron-left"> &nbsp;</span>Back to customers list</a>
                </div>
            </div>
        </div>
    </div>
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href='<?php echo e(url("admin/users/edit/$users->id")); ?>'>Profile</a>
                </li>

                <li>
                  <a href="<?php echo e(url("admin/users/transactions/$users->id")); ?>">Transactions</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/wallets/$users->id")); ?>">Wallets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/tickets/$users->id")); ?>">Tickets</a>
                </li>
                <li >
                  <a href="<?php echo e(url("admin/users/disputes/$users->id")); ?>">Disputes</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/kyc-verications/$users->id")); ?>">KYC Verifications</a>
                </li>
                <li class="active">
                  <a href="<?php echo e(url("admin/users/bankdetails/$users->id")); ?>">Bank Details</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/address_edit/$users->id")); ?>">Address</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/activity-logs/$users->id")); ?>">Activity Logs</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <div class="box">
      <div class="box-body">
        <h3 class="text-center" style="margin-bottom: 20px;">Bank Details</h3>
        <div class="row">
            <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <div style="border: 1px solid gray; border-radius: 10px; margin-bottom: 15px; padding: 15px;">
                        <?php
                            $check_bank = json_decode($bank->bank, true);
                            $check_country = DB::table('countries')->where('id', $bank->country_id)->first();
                        ?>
                        
                        <p><strong>Country</strong> : <?php echo e($check_country->name); ?></p>
                        
                        <?php $__currentLoopData = $check_bank; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><strong><?php echo e($k); ?></strong> : <?php echo e($val); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php echo $__env->make('admin.layouts.partials.message_boxes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.dataTables js -->
<script src="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
      $("#eachuserdispute").DataTable({
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

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/users/bankdetails.blade.php ENDPATH**/ ?>