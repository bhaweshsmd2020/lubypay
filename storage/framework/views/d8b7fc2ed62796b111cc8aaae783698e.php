
<?php $__env->startSection('title', 'Key Information'); ?>
<?php $__env->startSection('page_content'); ?>

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Gift Card Key Information</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/giftcard_informations')); ?>" method="post" class="form-horizontal" id="api-credentials" >
                    <?php echo csrf_field(); ?>


                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Gift Card Main Url</label>
                            <div class="col-sm-6">
                                <input type="text" name="main_url" class="form-control" value="<?php echo e($giftcard['main_url']); ?>" placeholder="Gift Card Main Ur">
                                <?php if($errors->has('main_url')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('main_url')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Gift Card Client Id</label>
                            <div class="col-sm-6">
                                <input type="text" name="client_id" class="form-control" value="<?php echo e($giftcard['client_id']); ?>" placeholder="Gift Card Client Id">
                                <?php if($errors->has('client_id')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('client_id')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Gift Card Client Secret Key</label>
                            <div class="col-sm-6">
                                <input type="text" name="client_secret" class="form-control" value="<?php echo e($giftcard['client_secret']); ?>" placeholder="Gift Card Client Secret Key">
                                <?php if($errors->has('client_secret')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('client_secret')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_keys')): ?>
                        <div class="box-footer">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Persona Key Information</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/persona_informations')); ?>" method="post" class="form-horizontal" id="apis-credentials" >
                    <?php echo csrf_field(); ?>


                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Persona Templete Id</label>
                            <div class="col-sm-6">
                                <input type="text" name="persona_templete" class="form-control" value="<?php echo e($persona['persona_templete']); ?>" placeholder="Persona Templete">
                                <?php if($errors->has('persona_templete')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('persona_templete')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Persona Api key</label>
                            <div class="col-sm-6">
                                <input type="text" name="persona_api_key" class="form-control" value="<?php echo e($persona['persona_api_key']); ?>" placeholder="Persona Api key">
                                <?php if($errors->has('persona_api_key')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('persona_api_key')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Mode</label>
                            <div class="col-sm-6">
                                <select name="mode" class="form-control" value="<?php echo e($persona['mode']); ?>" placeholder="mode">
                                    <option value="SANDBOX" <?php if($persona['mode']==='SANDBOX'): ?> selected <?php endif; ?>)>SANDBOX</option>
                                    <option value="PRODUCTION" <?php if($persona['mode']==='PRODUCTION'): ?> selected <?php endif; ?>>PRODUCTION</option>
                                </select>
                                <?php if($errors->has('mode')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('mode')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_keys')): ?>
                        <div class="box-footer">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Ding Key Information</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/ding_informations')); ?>" method="post" class="form-horizontal" id="dingapis-credentials" >
                    <?php echo csrf_field(); ?>


                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Ding main url</label>
                            <div class="col-sm-6">
                                <input type="text" name="ding_main_url" class="form-control" value="<?php echo e($ding['ding_main_url']); ?>" placeholder="Ding main url">
                                <?php if($errors->has('ding_main_url')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('ding_main_url')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Ding Api key</label>
                            <div class="col-sm-6">
                                <input type="text" name="ding_api_key" class="form-control" value="<?php echo e($ding['ding_api_key']); ?>" placeholder="Ding Api key">
                                <?php if($errors->has('ding_api_key')): ?>
                                    <span class="help-block">
                                      <strong class="text-danger"><?php echo e($errors->first('ding_api_key')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_keys')): ?>
                        <div class="box-footer">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Plaid Key Information</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/plaid_informations')); ?>" method="post" class="form-horizontal" id="plaidapis-credentials" >
                    <?php echo csrf_field(); ?>


                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Plaid Base URL</label>
                            <div class="col-sm-6">
                                <input type="text" name="plaid_base_url" class="form-control" value="<?php echo e($plaid['plaid_base_url']); ?>" placeholder="Plaid Base URL">
                                <?php if($errors->has('plaid_base_url')): ?>
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo e($errors->first('plaid_base_url')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Plaid Client Id</label>
                            <div class="col-sm-6">
                                <input type="text" name="plaid_client_id" class="form-control" value="<?php echo e($plaid['plaid_client_id']); ?>" placeholder="Plaid Client Id">
                                <?php if($errors->has('plaid_client_id')): ?>
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo e($errors->first('plaid_client_id')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Plaid Client Secret</label>
                            <div class="col-sm-6">
                                <input type="text" name="plaid_client_secret" class="form-control" value="<?php echo e($plaid['plaid_client_secret']); ?>" placeholder="Plaid Client Secret">
                                <?php if($errors->has('plaid_client_secret')): ?>
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo e($errors->first('plaid_client_secret')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Stripe Webhook Secret</label>
                            <div class="col-sm-6">
                                <input type="text" name="stripe_webhook_key" class="form-control" value="<?php echo e($plaid['stripe_webhook_key']); ?>" placeholder="Stripe Webhook Secret">
                                <?php if($errors->has('stripe_webhook_key')): ?>
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo e($errors->first('stripe_webhook_key')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_keys')): ?>
                        <div class="box-footer">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">

$.validator.setDefaults({
    highlight: function(element) {
        $(element).parent('div').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).parent('div').removeClass('has-error');
    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    }
});

$('#api-credentials').validate({
    rules: {
        main_url: {
            required: true,
        },
        client_id: {
            required: true,
        },
         client_secret: {
            required: true,
        }
    },
});
$('#apis-credentials').validate({
    rules:{
        persona_templete: {
            required: true,
        },
        persona_api_key:{
            required: true,
        },
        mode:{
            required: true, 
        }
        
    },
});
$('#dingapis-credentials').validate({
    rules:{
        ding_main_url: {
            required: true,
        },
        ding_api_key:{
            required: true,
        }
    },
});
$('#plaidapis-credentials').validate({
    rules:{
        plaid_base_url: {
            required: true,
        },
        plaid_client_id:{
            required: true,
        },
        plaid_client_secret:{
            required: true,
        },
        stripe_webhook_key:{
            required: true,
        }
    },
});
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/settings/keys.blade.php ENDPATH**/ ?>