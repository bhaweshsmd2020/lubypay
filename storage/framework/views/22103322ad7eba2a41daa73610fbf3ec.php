
<?php $__env->startSection('title', 'Currencies'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Currencies & Fees</div>
                </div>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_currency')): ?>
                    <div class="col-md-4 text-right">
                        <a href="<?php echo e(url('admin/settings/add_currency')); ?>">
                            <button type="button" name="btn" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Currency</button>
                        </a>
                    </div>
                <?php endif; ?>
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
                                    <th>S. No</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Symbol</th>
                                    <th>Rate</th>
                                    <th>Logo</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td><?php echo e($value->name); ?></td>
                                        <td><?php echo e($value->code); ?></td>
                                        <td><?php echo e($value->symbol); ?></td>
                                        <td><?php echo e(formatNumber($value->rate)); ?></td>
                                        <td><img src="<?php echo e(url('public/uploads/currency_logos/'.$value->logo)); ?>" style="width: 100px; height: 50px;"></td>
                                        <td><?php echo e($value->position); ?></td>
                                        <td>
                                            <?php if($value->status == 'Active'): ?>
                                                <span class="label label-success">Active</span>
                                            <?php else: ?>
                                                <span class="label label-primary">Inactive</span>
                                            <?php endif; ?>
                                            <?php if($value->default): ?>
                                             <span class="label label-warning">Default Currency</span>
                                            <?php endif; ?> 
                                        </td>
                                        <td>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_currency')): ?>
                                                <a href="<?php echo e(url('admin/settings/edit_currency/' . encrypt($value->id))); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                            <?php endif; ?>
                                            
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_currency')): ?>
                                                <a href="<?php echo e(url('admin/settings/delete_currency/' . encrypt($value->id))); ?>" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;
                                            <?php endif; ?>
                            
                                            <a href="<?php echo e(url('admin/settings/feeslimit/deposit/' . $value->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-view">Fees</i></a>&nbsp;
                            
                                            <a href="<?php echo e(url('admin/settings/payment-methods/stripe/' . $value->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-view">Payment-Methods</i></a>&nbsp;
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
    		document.title='Currencies';
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/currencies/view.blade.php ENDPATH**/ ?>