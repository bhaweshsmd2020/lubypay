
<?php $__env->startSection('title', 'Deposits'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <style>
        #example_wrapper{
            overflow: scroll;
        }
    </style>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Gift Cards</div>
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
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Recipient Email</th>
                                    <th>Amount</th>
                                    <th>Transaction Fee</th>
                                    <th>Total</th>
                                    <th>Currency</th>
                                    <th>Brand/Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $user_name = DB::table('users')->where('id', $card->user_id )->first();
                                        $currency = DB::table('currencies')->where('id', $card->currency_id)->first();
                                        
                                        if(!empty($card->local_tran_time)){
                                            $local_time = $card->local_tran_time;
                                        }else{
                                            $local_time = $card->created_at;
                                        }
                                        
                                        $transaction = DB::table('transactions')->where('transaction_reference_id',$card->id)->where('transaction_type_id',32)->first();
                                        if(!empty($transaction)){
                                            $transaction_fee = number_format($transaction->charge_percentage + $transaction->charge_fixed,2);
                                            $total_amount =  number_format($card->amount +  $transaction->charge_percentage + $transaction->charge_fixed,2);
                                            $uuid = $transaction->uuid;
                                        }else{
                                            $transaction_fee = '-';
                                            $total_amount = '-';
                                            $uuid = '-';
                                        }
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td><?php echo e($uuid); ?></td>
                                        <td><?php echo e(Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A')); ?></td>
                                        <td><a href="<?php echo e(url('admin/users/edit/' . $card->user_id)); ?>"><?php echo e($user_name->first_name.' '.$user_name->last_name); ?></a></td>
                                        <td><?php echo e($card->recipient_email); ?></td>
                                        <td><?php echo e(formatNumber($card->amount)); ?></td>
                                        <td><?php echo e($transaction_fee); ?></td>
                                        <td><?php echo e($total_amount); ?></td>
                                        <td><?php echo e($currency->code); ?></td>
                                        <td><?php echo e($card->brand_name); ?>(<?php echo e($card->product_name); ?>)</td>
                                        <td><?php echo e(formatNumber($card->product_unit_price)); ?></td>
                                        <td><?php echo e($card->quantity); ?></td>
                                        <td><?php echo e($card->status); ?></td>
                                        <td>
                                            <a href="<?php echo e(url('admin/card/edit-gift-card/' . $card->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/giftcard/view.blade.php ENDPATH**/ ?>