
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
                <form action="<?php echo e(url('admin/settings/key_informations')); ?>" method="post" class="form-horizontal" id="api-credentials" >
                    <?php echo csrf_field(); ?>


                    <!-- box-body -->
                    <div class="box-body">

                        <!-- Google Captcha Secret key -->
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

                        <!-- Google Captcha Site key -->
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
                    <!--client_secret-->
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
                    <!-- /.box-body -->

                    <!-- box-footer -->
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_keys')): ?>
                        <div class="box-footer">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                    <!-- /.box-footer -->
                </form>
            </div>
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Persona Key Information</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/persona_informations')); ?>" method="post" class="form-horizontal" id="apis-credentials" >
                    <?php echo csrf_field(); ?>


                    <!-- box-body -->
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
                    <!-- /.box-body -->

                    <!-- box-footer -->
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_keys')): ?>
                        <div class="box-footer">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    <?php endif; ?>
                    <!-- /.box-footer -->
                </form>
            </div>
                        <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Ding Key Information</h3>
                </div>
                <form action="<?php echo e(url('admin/settings/ding_informations')); ?>" method="post" class="form-horizontal" id="dingapis-credentials" >
                    <?php echo csrf_field(); ?>


                    <!-- box-body -->
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
                    <!-- /.box-body -->

                    <!-- box-footer -->
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

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/settings/keys.blade.php ENDPATH**/ ?>