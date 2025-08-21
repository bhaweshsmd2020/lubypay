
<?php $__env->startSection('title', 'Push Notifications'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Push Notifications</div>
                </div>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_push_notifications')): ?>
                    <div class="col-md-4 text-right">
                        <a href="<?php echo e(url('admin/promotions/create')); ?>">
                            <button type="button" name="btn" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Send New Notifications</button>
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
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td>
                                            <?php
                                                if($value->user_type == "All"){
                                                    $user = $value->user_type;
                                                }else{
                                                    $check = DB::table('users')->where('id', $value->user_type)->first();
                                                    $user = $check->first_name.' '.$check->last_name;
                                                }
                                            ?>
                                            <?php echo e($user); ?>

                                        </td>
                                        <td><?php echo e($value->title); ?></td>
                                        <td><?php echo e($value->subject); ?></td>
                                        <td><?php echo e($value->type); ?></td>
                                        <td><?php echo e($value->description); ?></td>
                                        <td><?php echo e(date("Y-m-d h:i:s A", strtotime($value->created_at))); ?></td>
                                        <td>
                                            <?php if($value->status == '1'): ?>
                                                <span class="label label-success">Success</span>
                                            <?php else: ?>
                                                <span class="label label-primary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_push_notifications')): ?>
                                                <a href="<?php echo e(url('admin/promotions/edit/' . $value->id)); ?>" class="label label-primary"><i class="fa fa-edit"></i></a>
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
    		document.title='Push Notifications';
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/promotions/index.blade.php ENDPATH**/ ?>