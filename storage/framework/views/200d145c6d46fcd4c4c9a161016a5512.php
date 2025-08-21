
<?php $__env->startSection('title', 'App Pages'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-md-9">
    
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="top-bar-title padding-bottom pull-left">App Pages</div>
                        </div>
        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_banner')): ?>
                            <div class="col-md-7">
                                <a href="<?php echo e(url('admin/apppages/add')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-plus"> &nbsp;</span>Add App Pages</a>
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
                                            <th>App Page</th>
                                            <th>Page Name</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(++$k); ?></td>
                                                <td><?php echo e($list->app_page); ?></td>
                                                <td><?php echo e($list->page_name); ?></td>
                                                <td><?php echo e($list->status); ?></td>
                                                <td><?php echo e($list->created_at); ?></td>
                                                <td class=" dt-center">
                                                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_banner')): ?>
                                                        <a href="<?php echo e(url('admin/apppages/edit/'.$list->id)); ?>" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                                    <?php endif; ?> 
                                                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_banner')): ?>
                                                        <a href="<?php echo e(url('admin/apppages/delete/'.$list->id)); ?>" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/app_pages/index.blade.php ENDPATH**/ ?>