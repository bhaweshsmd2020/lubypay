
<?php $__env->startSection('title', 'Store Products'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    
    
    
    
    
    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_stores')): ?>
                    <li>
                      <a href="<?php echo e(url("admin/store/edit/" . $store_detail->id)); ?>">Details</a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_products')): ?>
                    <li class="active">
                      <a href="<?php echo e(url("admin/store/product/list/" . $store_detail->id)); ?>">Products</a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_categories')): ?>    
                    <li>
                      <a href="<?php echo e(url("admin/store/category/list/" . $store_detail->id)); ?>">Categories</a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_orders')): ?>    
                    <li>
                      <a href="<?php echo e(url("admin/store/orders/list/" . $store_detail->id)); ?>">Orders</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left"><?php echo e($store_detail->name); ?> Products</div>
                </div>
                <div class="col-md-2 pull-right">
                        <a href="<?php echo e(url('admin/store-list')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-chevron-left"> &nbsp;</span>Back to Store list</a>
                </div>
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_products')): ?>
              
                    <div class="col-md-2 pull-right">
                        <a href="<?php echo e(url('admin/store/product/create/'.$store_detail->id)); ?>" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Products</a>
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
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Created On</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $category = DB::table('categories')->where('id', $product->category_id)->first();
                                        if(!empty($category)){
                                            $category_name = $category->name;
                                        }else{
                                            $category_name = 'N/A';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo e(++$k); ?></td>
                                        <td>
                                            <?php if(!empty($product->image)): ?>
                                                <img src="<?php echo e(url('public/user_dashboard/product/thumb/' . $product->image)); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                            <?php else: ?>
                                                <img src="<?php echo e(url('public/user_dashboard/profile/user.png')); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($product->name); ?></td>
                                        <td><?php echo e($category_name); ?></td>
                                        <td><?php echo e($currency->symbol); ?><?php echo e($product->price); ?></td>
                                        <td><?php echo e($product->created_at); ?></td>
                                        <td>
                                            <a href="<?php echo e(url('admin/store/product/edit/'.$store_detail->id.'/'.$product->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Edit"></i></a>&nbsp;
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_products')): ?>
                                                <a href="<?php echo e(url('admin/store/product/delete/'.$store_detail->id.'/'.$product->id)); ?>" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Delete"></i></a>&nbsp;
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/stores/products/index.blade.php ENDPATH**/ ?>