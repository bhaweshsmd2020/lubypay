
<?php $__env->startSection('title', 'Edit KYC Method'); ?>
<?php $__env->startSection('page_content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Edit KYC Method</h3> 
                </div>
                <form method="POST" action="<?php echo e(url('admin/settings/edit_kyc_methods/'.$result->id)); ?>" class="form-horizontal">
                    <?php echo e(csrf_field()); ?>

        
                    <div class="box-body">
                        <input type="hidden" name="country" value="<?php echo e($result->id); ?>">
                        
                        <div class="form-group">
                            <div class="col-sm-1"></div>
                            <div class="col-sm-6">
                                <input type="checkbox" id="automatic_kyc" name="automatic_kyc" value="1" <?php if($result->automatic_kyc == '1'): ?> checked <?php endif; ?>>
                                <label for="automatic_kyc"> Automatic KYC</label><br>
                                <input type="checkbox" id="manual_kyc" name="manual_kyc" value="1" <?php if($result->manual_kyc == '1'): ?> checked <?php endif; ?>>
                                <label for="manual_kyc"> Manual KYC</label>
                            </div>
                        </div>
                        
                    </div>
            
                    <div class="box-footer">
                        <a class="btn btn-danger" href="<?php echo e(url('admin/settings/country')); ?>">Cancel</a>
                        <button type="submit" class="btn btn-primary pull-right">&nbsp; Submit &nbsp;</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/countries/kyc.blade.php ENDPATH**/ ?>