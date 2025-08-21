
<?php $__env->startSection('title', 'Stores'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Stores</div>
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
                                    <th>Store Profile</th>
                                    <th>Store Name</th>
                                    <th>Merchant Name</th>
                                    <th>Currency</th>
                                    <th>Country</th>
                                    <th>Created On</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $user = DB::table('users')->where('id', $store->user_id)->first();
                                        $currency = DB::table('currencies')->where('id', $store->currency_id)->first();
                                        $country = DB::table('countries')->where('id', $store->country)->first();
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td>
                                            <?php if(!empty($store->image)): ?>
                                                <a href="<?php echo e(url('admin/store/edit/' . $store->id)); ?>">
                                                    <img src="<?php echo e(url('public/uploads/store/' . $store->image)); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(url('admin/store/edit/' . $store->id)); ?>">
                                                    <img src="<?php echo e(url('public/user_dashboard/profile/user.png')); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?php echo e(url('admin/store/edit/' . $store->id)); ?>"><?php echo e($store->name); ?></a></td>
                                        <td><a href="<?php echo e(url('admin/users/edit/' . $store->user_id)); ?>"><?php if(!empty($user)): ?> <?php echo e($user->first_name.' '.$user->last_name); ?> <?php else: ?> - <?php endif; ?></a></td>
                                        <td><?php echo e($currency->code); ?></td>
                                        <td><?php echo e($country->name); ?></td>
                                        <td><?php echo e($store->created_at); ?></td>
                                        <td>
                                            <a href="<?php echo e(url('admin/store/edit/' . $store->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Edit"></i></a>&nbsp;
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_stores')): ?>
                                                <a href="<?php echo e(url('admin/store/delete/' . $store->id)); ?>" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Delete"></i></a>&nbsp;
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/stores/index.blade.php ENDPATH**/ ?>