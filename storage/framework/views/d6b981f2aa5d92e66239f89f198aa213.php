
<?php $__env->startSection('title', 'Admins'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Admins</div>
                </div>
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_admin')): ?>
                    <div class="col-md-2 pull-right">
                        <a href="<?php echo e(url('admin/admin-user/create')); ?>" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Admin</a>
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
                                    <th>S.No</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Group</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td>
                                            <?php if(!empty($admin->picture)): ?>
                                                <img src="<?php echo e(url('public/uploads/userPic/' . $admin->picture)); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                            <?php else: ?>
                                                <img src="<?php echo e(url('public/user_dashboard/profile/user.png')); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?php echo e(url('admin/admin-user/edit/' . $admin->id)); ?>"><?php echo e($admin->first_name.' '.$admin->last_name); ?></a></td>
                                        <td><?php echo e($admin->email); ?></td>
                                        <td>
                                            <?php if($admin->status == 'Active'): ?>
                                                <span class="label label-success">Active</span>
                                            <?php elseif($admin->status == 'Inactive'): ?>
                                                <span class="label label-danger">Inactive</span>';
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($admin->role->display_name); ?></td>
                                        <td>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_admin')): ?>
                                                <a href="<?php echo e(url('admin/admin-user/edit/' . $admin->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                            <?php endif; ?>

                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_admin')): ?>
                                                <a href="<?php echo e(url('admin/admin-user/delete/' . $admin->id)); ?>" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/users/adminList.blade.php ENDPATH**/ ?>