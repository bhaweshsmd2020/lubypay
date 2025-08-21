

<?php $__env->startSection('title', 'Edit Store Category'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href="<?php echo e(url("admin/store/edit/" . $store_detail->id)); ?>">Details</a>
                </li>

                <li>
                  <a href="<?php echo e(url("admin/store/product/list/" . $store_detail->id)); ?>">Products</a>
                </li>
                
                <li class="active">
                  <a href="<?php echo e(url("admin/store/category/list/" . $store_detail->id)); ?>">Categories</a>
                </li>
                
                <li>
                  <a href="<?php echo e(url("admin/store/orders/list/" . $store_detail->id)); ?>">Orders</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left"><?php echo e($store_detail->name); ?> Categories</div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!-- form start -->
                <form action="<?php echo e(url('admin/store/category/update')); ?>" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                    <?php echo e(csrf_field()); ?>


                    <input type="hidden" value="<?php echo e($store_detail->id); ?>" name="store_id" id="id">
                    <input type="hidden" value="<?php echo e($category->id); ?>" name="cat_id" id="id">

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Name" name="name" type="text" id="name" value="<?php echo e($category->name); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Description
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Description" name="description" type="text" id="description" value="<?php echo e($category->description); ?>">
                                        </div>
                                    </div>
                                  
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Image" name="image" type="file" id="image">
                                            <?php if(!empty($category->image)): ?>
                                                <img src="<?php echo e(asset('public/user_dashboard/categories/thumb/'. $category->image)); ?>" style="width: 300px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_categories')): ?>
                                        <div class="form-group">
                                            <label class="col-sm-4" for="inputEmail3">
                                            </label>
                                            <div class="col-sm-8">
                                                <a class="btn btn-danger btn-flat" href="<?php echo e(url("admin/store/category/list/" . $store_detail->id)); ?>">
                                                    Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary pull-right btn-flat">
                                                    Update
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')); ?>" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="<?php echo e(asset('public/dist/js/isValidPhoneNumber.js')); ?>" type="text/javascript"></script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/stores/category/edit.blade.php ENDPATH**/ ?>