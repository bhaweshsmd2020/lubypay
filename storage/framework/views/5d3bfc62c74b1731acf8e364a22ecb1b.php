

<?php $__env->startSection('title', 'Edit Store'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_stores')): ?>
                    <li class="active">
                      <a href="<?php echo e(url("admin/store/edit/" . $store->id)); ?>">Details</a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_products')): ?>
                    <li>
                      <a href="<?php echo e(url("admin/store/product/list/" . $store->id)); ?>">Products</a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_categories')): ?>    
                    <li>
                      <a href="<?php echo e(url("admin/store/category/list/" . $store->id)); ?>">Categories</a>
                    </li>
                <?php endif; ?>
                
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_orders')): ?>    
                    <li>
                      <a href="<?php echo e(url("admin/store/orders/list/" . $store->id)); ?>">Orders</a>
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
                    <div class="top-bar-title padding-bottom pull-left"><?php echo e($store->name); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!-- form start -->
                <form action="<?php echo e(url('admin/store/update')); ?>" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                    <?php echo e(csrf_field()); ?>


                    <input type="hidden" value="<?php echo e($store->id); ?>" name="id" id="id">

                    <div class="box-body">
                        <?php if(count($errors) > 0): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Name" name="name" type="text" id="name" value="<?php echo e($store->name); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Description
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Description" name="description" type="text" id="description" value="<?php echo e($store->description); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Address
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Address" name="address" type="text" id="address" value="<?php echo e($store->address); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           City
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="City" name="city" type="text" id="city" value="<?php echo e($store->city); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           State
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="State" name="state" type="text" id="state" value="<?php echo e($store->state); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Country
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="country" id="country">
                                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($country->id); ?>" <?php if($country->id == $store->country): ?> selected <?php endif; ?>><?php echo e($country->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Postal Code
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Postal Code" name="postalcode" type="text" id="postalcode" value="<?php echo e($store->postalcode); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Tax
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Tax" name="tax" type="text" id="tax" value="<?php echo e($store->tax); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Image" name="image" type="file" id="image">
                                            <?php if(!empty($store->image)): ?>
                                                <img src="<?php echo e(asset('public/uploads/store/'. $store->image)); ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_stores')): ?>
                                        <div class="form-group">
                                            <label class="col-sm-4" for="inputEmail3">
                                            </label>
                                            <div class="col-sm-8">
                                                <a class="btn btn-danger btn-flat" href="<?php echo e(url('admin/store-list')); ?>">
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

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/stores/edit.blade.php ENDPATH**/ ?>