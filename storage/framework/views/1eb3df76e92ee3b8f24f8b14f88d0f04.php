

<?php $__env->startSection('title', 'Transactions'); ?>

<?php $__env->startSection('head_style'); ?>
<!-- Bootstrap daterangepicker -->
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">

<!-- dataTables -->
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css')); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href='<?php echo e(url("admin/users/edit",$users->id??'')); ?>'>Profile</a>
                </li>

                <li  class="active">
                  <a href="<?php echo e(url("admin/users/transactions",$users->id??'')); ?>">Transactions</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/wallets",$users->id??'')); ?>">Wallets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/tickets",$users->id??'')); ?>">Tickets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/disputes",$users->id??'')); ?>">Disputes</a>
                </li>
                
                <li>
                  <a href="<?php echo e(url("admin/users/photoproof",$users->id??'')); ?>">Photo Proof</a>
                </li>
                
                <li >
                  <a href="<?php echo e(url("admin/users/addressproof",$users->id??'')); ?>">Address Proof</a>
                </li>
                
                <li >
                  <a href="<?php echo e(url("admin/users/idproof",$users->id??'')); ?>">Identity Proof</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/bankdetails",$users->id??'')); ?>">Bank Details</a>
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
            <form class="form-horizontal" action="<?php echo e(url("admin/users/transactions/$users->id")); ?>" method="GET">

                <input id="startfrom" type="hidden" name="from" value="<?php echo e(isset($from) ? $from : ''); ?>">
                <input id="endto" type="hidden" name="to" value="<?php echo e(isset($to) ? $to : ''); ?>">

                <input id="user_id" type="hidden" name="user_id" value="<?php echo e($users->id); ?>">

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Date and time range -->
                            <div class="col-md-3">
                                <label>Date Range</label>
                                <button type="button" class="btn btn-default" id="daterange-btn" >
                                    <span id="drp">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <!-- Currency -->
                            <div class="col-md-2">
                                <label for="currency">Currency</label>
                                <select class="form-control select2" name="currency" id="currency">
                                    <option value="all" <?php echo e(($currency =='all') ? 'selected' : ''); ?> >All</option>
                                    <?php $__currentLoopData = $t_currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($transaction->currency_id); ?>" <?php echo e(($transaction->currency_id == $currency) ? 'selected' : ''); ?>>
                                            <?php echo e($transaction->currency->code); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="all" <?php echo e(($status =='all') ? 'selected' : ''); ?> >All</option>
                                    <?php $__currentLoopData = $t_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($t->status); ?>" <?php echo e(($t->status == $status) ? 'selected' : ''); ?>>
                                            <?php echo e((
                                                    ($t->status == 'Blocked') ? "Cancelled" :
                                                    (
                                                        ($t->status == 'Refund') ? "Refunded" : $t->status
                                                    )
                                                )); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="transaction_type">Type</label>
                                <select class="form-control select2" name="type" id="type">
                                    <option value="all" <?php echo e(($type =='all') ? 'selected' : ''); ?> >All</option>
                                    <?php $__currentLoopData = $t_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ttype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ttype->transaction_type->id); ?>" <?php echo e(($ttype->transaction_type->id == $type) ? 'selected' : ''); ?>>
                                        <?php echo e(($ttype->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $ttype->transaction_type->name)); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <div class="input-group" style="margin-top: 25px;">
                                   <button type="submit" name="btn" class="btn btn-primary btn-flat" id="btn">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <?php echo $dataTable->table(['class' => 'table table-striped table-hover dt-responsive transactions', 'width' => '100%', 'cellspacing' => '0']); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- Bootstrap daterangepicker -->
<script src="<?php echo e(asset('public/backend/bootstrap-daterangepicker/daterangepicker.js')); ?>" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="<?php echo e(asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js')); ?>" type="text/javascript"></script>

<?php echo $dataTable->scripts(); ?>


<script type="text/javascript">

    $(".select2").select2({});

    var sDate;
    var eDate;

    //Date range as a button
    $('#daterange-btn').daterangepicker(
        {
            ranges   : {
              'Today'       : [moment(), moment()],
              'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month'  : [moment().startOf('month'), moment().endOf('month')],
              'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
             },
          startDate: moment().subtract(29, 'days'),
          endDate  : moment()
        },
        function (start, end)
        {
        var sessionDate      = '<?php echo e(Session::get('date_format_type')); ?>';
        var sessionDateFinal = sessionDate.toUpperCase();

        sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
        $('#startfrom').val(sDate);

        eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
        $('#endto').val(eDate);

        $('#daterange-btn span').html('&nbsp;' + sDate + ' - ' + eDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    )

    $(document).ready(function()
    {
        $("#daterange-btn").mouseover(function() {
            $(this).css('background-color', 'white');
            $(this).css('border-color', 'grey !important');
        });

        var startDate = "<?php echo $from; ?>";
        var endDate   = "<?php echo $to; ?>";
        // alert(startDate);

        if (startDate == '') {
            $('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
            $('#daterange-btn span').html(startDate + ' - ' +endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    });
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/users/eachusertransaction.blade.php ENDPATH**/ ?>