
<?php $__env->startSection('title', 'API Credentials Settings'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- custom-checkbox -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/css/custom-checkbox.css')); ?>">

   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/sweetalert/sweetalert.css')); ?>">

  <style type="text/css">
    @media only screen and (max-width: 767px) {
        .checkbox-container {
            padding-bottom: 7px;
        }
    }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <div class="col-md-9">
            <div class="box box-info">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="tabs">
                      <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="false">Google Play Store</a></li>
                      <li><a href="#tab_2" data-toggle="tab" aria-expanded="false">Apple Store</a></li>
                    </ul>

                    <div class="tab-content">

                        <!-- Play Store -->
                        <div class="tab-pane fade in active" id="tab_1">
                            <form action="<?php echo e(url('admin/settings/app-store-credentials/update-google-credentials')); ?>" method="POST" class="form-horizontal" id="app-store-google-credentials" enctype="multipart/form-data" >
                                <?php echo csrf_field(); ?>


                                <input type="hidden" name="playstoreid" id="playstoreid" value="<?php echo e(isset($appStoreCredentialsForGoogle) ? $appStoreCredentialsForGoogle->id : ''); ?>">
                                <input type="hidden" name="playstorecompany" id="playstorecompany" value="<?php echo e(isset($appStoreCredentialsForGoogle) ? $appStoreCredentialsForGoogle->company : ''); ?>">

                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="has_transaction">Available</label>
                                        <div class="col-sm-6">
                                            <label class="checkbox-container">
                                              <input type="checkbox" class="has_app_playstore_credentials" name="has_app_playstore_credentials" value="Yes" id="has_app_playstore_credentials"
                                              <?php echo e(isset($appStoreCredentialsForGoogle->has_app_credentials) && $appStoreCredentialsForGoogle->has_app_credentials == 'Yes' ? 'checked' : ''); ?>

                                              >
                                              <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <!-- PlayStore Link -->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="playstore[link]">Play Store Link</label>
                                        <div class="col-sm-5">
                                            <input class="form-control playstore-link" name="playstore[link]" type="text"
                                            value="<?php echo e(isset($appStoreCredentialsForGoogle->link) ? $appStoreCredentialsForGoogle->link : ''); ?>"
                                            id="playstore-link">
                                            <?php if($errors->has('playstore[link]')): ?>
                                                  <span class="error">
                                                      <strong><?php echo e($errors->first('playstore[link]')); ?></strong>
                                                  </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <!-- Play Store Logo -->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="playstore[logo]">Play Store Logo</label>
                                        <div class="col-sm-5">
                                           <input type="file" name="playstore[logo]" class="form-control input-file-field" id="playstore-logo"
                                            data-rel="<?php echo e(isset($appStoreCredentialsForGoogle->logo) ? $appStoreCredentialsForGoogle->logo : ''); ?>" value="<?php echo e(isset($appStoreCredentialsForGoogle->logo) ? $appStoreCredentialsForGoogle->logo : ''); ?>">

                                            <?php if($errors->has('playstore[logo]')): ?>
                                                <span class="error">
                                                    <strong><?php echo e($errors->first('playstore[logo]')); ?></strong>
                                                </span>
                                            <?php endif; ?>

                                            <div class="clearfix"></div>
                                            <small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(125,50)); ?></strong></small>

                                            <?php if(!empty($appStoreCredentialsForGoogle->logo)): ?>
                                                <div class="setting-img">
                                                    <div class="img-wrap">
                                                    <img src='<?php echo e(url('public/uploads/app-store-logos/'.$appStoreCredentialsForGoogle->logo)); ?>' width="125" height="50" id="play-store-logo-preview">
                                                    </div>
                                                    <span class="remove-store-logos" id="playstore-logo-delete"></span>
                                                </div>
                                            <?php else: ?>
                                                <img src='<?php echo e(url('public/uploads/app-store-logos/default-logo.jpg')); ?>' width="125" height="50" id="play-store-logo-demo-preview">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="row">
                                  <div class="col-md-12">
                                      <div style="margin-top:10px">
                                        <a href="<?php echo e(url('admin/settings/app-store-credentials')); ?>" class="btn btn-danger btn-flat">Cancel</a>
                                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_appstore_credentials')): ?>
                                            <button class="btn btn-primary pull-right btn-flat" type="submit">Sumbit</button>
                                        <?php endif; ?>
                                      </div>
                                  </div>
                                </div>
                            </form>
                        </div>

                        <!-- Apple Store -->
                        <div class="tab-pane" id="tab_2">
                            <form action="<?php echo e(url('admin/settings/app-store-credentials/update-apple-credentials')); ?>" method="POST" class="form-horizontal" id="app-store-apple-credentials" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>


                                <input type="hidden" name="appstoreid" id="appstoreid" value="<?php echo e(isset($appStoreCredentialsForApple) ? $appStoreCredentialsForApple->id : ''); ?>">
                                <input type="hidden" name="appstorecompany" id="appstorecompany" value="<?php echo e(isset($appStoreCredentialsForApple) ? $appStoreCredentialsForApple->company : ''); ?>">

                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="has_transaction">Available</label>
                                        <div class="col-sm-6">
                                            <label class="checkbox-container">
                                              <input type="checkbox" class="has_app_appstore_credentials" name="has_app_appstore_credentials" value="Yes" id="has_app_appstore_credentials" <?php echo e(isset($appStoreCredentialsForApple->has_app_credentials) && $appStoreCredentialsForApple->has_app_credentials == 'Yes' ? 'checked' : ''); ?>>
                                              <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <!-- Apple Store Link -->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="applestore[link]">Apple Store Link</label>
                                        <div class="col-sm-5">
                                            <input class="form-control applestore-link" name="applestore[link]" type="text"
                                            value="<?php echo e(isset($appStoreCredentialsForApple->link) ? $appStoreCredentialsForApple->link : ''); ?>"
                                            id="applestore-link">
                                            <?php if($errors->has('applestore[link]')): ?>
                                                  <span class="error">
                                                      <strong><?php echo e($errors->first('applestore[link]')); ?></strong>
                                                  </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <!-- Apple Store Logo -->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="applestore[logo]">Apple Store Logo</label>
                                        <div class="col-sm-5">
                                          <input type="file" name="applestore[logo]" class="form-control input-file-field" id="applestore-logo"
                                            data-rel="<?php echo e(isset($appStoreCredentialsForApple->logo) ? $appStoreCredentialsForApple->logo : ''); ?>" value="<?php echo e(isset($appStoreCredentialsForApple->logo) ? $appStoreCredentialsForApple->logo : ''); ?>">

                                            <?php if($errors->has('applestore[logo]')): ?>
                                                <span class="error">
                                                    <strong><?php echo e($errors->first('applestore[logo]')); ?></strong>
                                                </span>
                                            <?php endif; ?>

                                            <div class="clearfix"></div>
                                            <small class="form-text text-muted"><strong><?php echo e(allowedImageDimension(125,50)); ?></strong></small>

                                            <?php if(!empty($appStoreCredentialsForApple->logo)): ?>
                                                <div class="setting-img">
                                                    <div class="img-wrap">
                                                    <img src='<?php echo e(url('public/uploads/app-store-logos/'.$appStoreCredentialsForApple->logo)); ?>' width="125" height="50" id="apple-store-logo-preview">
                                                    </div>
                                                    <span class="remove-store-logos" id="applestore-logo-delete"></span>
                                                </div>
                                            <?php else: ?>
                                                <img src='<?php echo e(url('public/uploads/app-store-logos/default-logo.jpg')); ?>' width="125" height="50" id="apple-store-logo-demo-preview">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="row">
                                  <div class="col-md-12">
                                      <div style="margin-top:10px">
                                        <a href="<?php echo e(url('admin/settings/app-store-credentials')); ?>" class="btn btn-danger btn-flat">Cancel</a>

                                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_appstore_credentials')): ?>
                                            <button class="btn btn-primary pull-right btn-flat" type="submit">Sumbit</button>
                                        <?php endif; ?>
                                      </div>
                                  </div>
                                </div>
                            </form>
                        </div>

                    </div><!-- /.tab-content -->
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<!-- jquery.validate additional-methods -->
<script src="<?php echo e(asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js')); ?>" type="text/javascript"></script>

<!-- sweetalert -->
<script src="<?php echo e(asset('public/backend/sweetalert/sweetalert.min.js')); ?>" type="text/javascript"></script>

<!-- read-file-on-change -->
<?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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

    $('#app-store-google-credentials').validate({
        rules: {
            "playstore[link]": {
                required: true,
                url: true,
            },
            "playstore[logo]":{
                required: true,
                extension: "png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
          "playstore[logo]": {
            extension: "Please select images(png|jpg|jpeg|gif|bmp) only!"
          },
        },
    });

    $('#app-store-apple-credentials').validate({
        rules: {
            "applestore[link]": {
                required: true,
                url: true,
            },
            "applestore[logo]":{
                required: true,
                extension: "png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
          "applestore[logo]": {
            extension: "Please select images(png|jpg|jpeg|gif|bmp) only!"
          },
        },
    });

    // preview play store logo on change
    $(document).on('change','#playstore-logo', function()
    {
        let orginalSource = '<?php echo e(url('public/uploads/app-store-logos/default-logo.jpg')); ?>';
        let playstoreLogo = $(this).attr('data-rel');
        if (playstoreLogo != '') {
          readFileOnChange(this, $('#play-store-logo-preview'), orginalSource);
          $('#playstore-logo-delete').remove();
        }
        readFileOnChange(this, $('#play-store-logo-demo-preview'), orginalSource);
    });

    // preview apple store logo on change
    $(document).on('change','#applestore-logo', function()
    {
        let orginalSource = '<?php echo e(url('public/uploads/app-store-logos/default-logo.jpg')); ?>';
        let applestore = $(this).attr('data-rel');
        if (applestore != '') {
          readFileOnChange(this, $('#apple-store-logo-preview'), orginalSource);
          $('#applestore-logo-delete').remove();
        }
        readFileOnChange(this, $('#apple-store-logo-demo-preview'), orginalSource);
    });

    //Delete playstoreLogo logo preview
    $(document).ready(function()
    {
        $('#playstore-logo-delete').click(function(){
            var playstoreLogo = $('#playstore-logo').attr('data-rel');
            var playstorecompany = $('#playstorecompany').val();

            if(playstoreLogo)
            {
              $.ajax(
              {
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "POST",
                url : SITE_URL+"/admin/settings/app-store-credentials/delete-playstore-logo",
                // async : false,
                data: {
                  'playstoreLogo' : playstoreLogo,
                  'playstorecompany' : playstorecompany,
                },
                dataType : 'json',
                success: function(reply)
                {
                  if (reply.success == 1)
                  {
                    swal({title: "", text: reply.message, type: "success"},function(){
                        location.reload();
                    });
                  }
                  else
                  {
                      alert(reply.message);
                      location.reload();
                  }
                }
              });
            }
        });
    });

    //Delete applestore logo preview
    $(document).ready(function()
    {
        $('#applestore-logo-delete').click(function(){
            var appleStoreLogo = $('#applestore-logo').attr('data-rel');
            var appstorecompany = $('#appstorecompany').val();

            if(appleStoreLogo)
            {
              $.ajax(
              {
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "POST",
                url : SITE_URL+"/admin/settings/app-store-credentials/delete-appstore-logo",
                // async : false,
                data: {
                  'appleStoreLogo' : appleStoreLogo,
                  'appstorecompany' : appstorecompany,
                },
                dataType : 'json',
                success: function(reply)
                {
                  if (reply.success == 1){
                    swal({title: "", text: reply.message, type: "success"},
                        function(){
                            location.reload();
                        }
                    );
                  }else{
                      alert(reply.message);
                      location.reload();
                  }
                }
              });
            }
        });
    });
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/settings/appStoreCredentials.blade.php ENDPATH**/ ?>