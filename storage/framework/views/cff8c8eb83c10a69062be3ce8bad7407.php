
<?php $__env->startSection('title', 'Transfers'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Transfers</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>User</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Sub Total</th>
                                    <th>Fees</th>
                                    <th>Total</th>
                                    <th>Card Number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        if(!empty($transaction->local_tran_time)){
                                            $local_time = $transaction->local_tran_time;
                                        }else{
                                            $local_time = $transaction->created_at;
                                        }
                                        
                                        $user = DB::table('users')->where('id', $transaction->user_id)->first();
                                        $card = DB::table('cards')->where('id', $transaction->card_id)->first();
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo e(++$index); ?></td><td>
                                            <?php if(!empty($user)): ?>
                                                <a href="<?php echo e(url('admin/users/edit/' . $transaction->user_id)); ?>"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></a>
                                            <?php else: ?>
                                                <a href="#">-</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($transaction->trx); ?></td>
                                        <td><?php echo e(Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A')); ?></td>
                                        <td>USD <?php echo e(number_format($transaction->sub_total, 2, '.', ',')); ?></td>
                                        <td>USD <?php echo e(number_format($transaction->fees, 2, '.', ',')); ?></td>
                                        <td>USD <?php echo e(number_format($transaction->total, 2, '.', ',')); ?></td>
                                        <td>Ewallet</td>
                                        <td><?php echo e($card->last_four); ?></td>
                                        <td>
                                            <?php if($transaction->status == 'paid'): ?>
                                                <span class="label label-success">Success</span>
                                            <?php else: ?>
                                                <span class="label label-primary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/pdfmake.min.js" ></script>
    <script type="text/javascript"  src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
    
    <script type="text/javascript">
    	$(document).ready(function() {
    		$('#example').DataTable(
    			{
    				"dom": '<"dt-buttons"Bf><"clear">lirtp',
    				"paging": true,
    				"autoWidth": true,
    				"buttons": [
    					'colvis',
    					'copyHtml5',
    	                'csvHtml5',
    					'excelHtml5',
    	                'pdfHtml5',
    					'print'
    				]
    			}
    		);
    	});
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/cards/transfers.blade.php ENDPATH**/ ?>