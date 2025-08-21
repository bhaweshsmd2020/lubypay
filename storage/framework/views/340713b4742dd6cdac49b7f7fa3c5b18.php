
<?php $__env->startSection('title', 'Maintenance Settings'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <style>
        #example_wrapper{
            overflow: scroll;
        }
    </style>

    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">Maintenance Settings</div>
                </div>
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level')): ?>

                <div class="col-md-3 text-right">
                    <a href="<?php echo e(url('admin/add-maintainance-settings')); ?>" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Maintenance</a>
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
                                    <th>S/N</th>
                                    <!--<th>Subject</th>-->
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$k); ?>.</td>
                                        <!--<td><?php echo e($val->subject); ?></td>-->
                                        <td><?php echo e($val->date); ?></td>
                                        <td><?php echo e(date("h:i:A", strtotime($val->from_time))); ?> - <?php echo e(date("h:i:A", strtotime($val->to_time))); ?></td>
                                        <td>
                                            <?php if($val->status==0): ?>
                                            <span class="badge badge-pill badge-danger"><?php echo e(__('Inactive')); ?></span>
                                            <?php elseif($val->status==1): ?>
                                            <span class="badge badge-pill badge-success"><?php echo e(__('Active')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(date("Y/m/d h:i:A", strtotime($val->created_at))); ?></td>
                                        <td>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level')): ?>
                                                <a href="<?php echo e(url('admin/edit-maintainance-settings/'.$val->id)); ?>" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                            <?php endif; ?>
                                            
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level')): ?>
                                                <a href="<?php echo e(url('admin/delete-maintainance-settings/'.$val->id)); ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                                            <?php endif; ?>
                                            
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level')): ?>
                                                <a href="<?php echo e(url('admin/remind-maintainance-settings/'.$val->id)); ?>" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Reminder"><i class="fa fa-reply"></i></a>
                                            <?php endif; ?>
                                            
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level')): ?>
                                                <a href="<?php echo e(url('admin/remind-maintainance-settings-sms/'.$val->id)); ?>" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="SMS Reminder"><i class="fa fa-paper-plane"></i></a>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/maintainance/index.blade.php ENDPATH**/ ?>