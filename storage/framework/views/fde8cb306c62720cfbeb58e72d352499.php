
<?php $__env->startSection('title', 'Banners'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">App Banners</div>
                </div>

                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_banner')): ?>
                    <div class="col-md-2 pull-right">
                        <a href="<?php echo e(url('admin/banner/add')); ?>" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Banner</a>
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
                                    <th>Banner</th>
                                    <th>Position</th>
                                    <th>Redirection Type</th>
                                    <th>Redirect To</th>
                                    <th>Language</th>
                                    <th>Platform</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$k); ?></td>
                                        <td>
                                            <?php if($list->banner_image) {?>
                                                <img src="<?php echo e(url('public/uploads/banner/'.$list->banner_image)); ?>" style="width:100px; height:50px" />   
                                            <?php }?>
                                        </td>
                                        <td><?php echo e($list->position); ?></td>
                                        <td>
                                            <?php if($list->app_redirect=='0'): ?>
                                                None
                                            <?php elseif($list->app_redirect=='1'): ?>
                                                App Page
                                            <?php elseif($list->app_redirect=='2'): ?>
                                                Redirect URL
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($list->app_redirect=='0'): ?>
                                                None
                                            <?php elseif($list->app_redirect=='1'): ?>
                                                <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($page->id == $list->app_page): ?>
                                                        <?php echo e($page->page_name); ?>

                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php elseif($list->app_redirect=='2'): ?>
                                                <?php echo e($list->redirect_url); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $language=DB::table('languages')->where('id',$list->language)->where('status','Active')->first();
                                            ?>
                                            <?php echo e($language->name); ?>

                                        </td>
                                        <td>
                                            <?php if($list->platform == 'ewallet'): ?>
                                                Ewallet
                                            <?php else: ?>
                                                Mpos
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($list->status); ?></td>
                                        <td><?php echo e($list->created_at); ?></td>
                                        <td class=" dt-center">
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_banner')): ?>
                                                <a href="<?php echo e(url('admin/banner/edit/'.$list->banner_id)); ?>" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                            <?php endif; ?> 
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_banner')): ?>
                                                <a href="<?php echo e(url('admin/banner/delete/'.$list->banner_id)); ?>" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/banner/list.blade.php ENDPATH**/ ?>