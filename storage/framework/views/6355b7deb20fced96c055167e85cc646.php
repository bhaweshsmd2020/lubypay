
<?php $__env->startSection('title', 'App Versions'); ?>

<?php $__env->startSection('head_style'); ?>
   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/sweetalert/sweetalert.css')); ?>">

  <!-- bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/css/bootstrap-select.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<!-- Main content -->
<div class="row">
    <div class="col-md-3 settings_bar_gap">
        <?php echo $__env->make('admin.common.appsettings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
                <h3 class="box-title">App Versions</h3>
            </div>

            <form action="<?php echo e(url('admin/settings/appversions')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" id="general_settings_form">
                <?php echo csrf_field(); ?>

				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Android Version</label>
					  <div class="col-sm-6">
					    <input type="text" name="android_version" class="form-control" value="<?php echo e(@$result['android_version']); ?>" placeholder="Android Version">
					  	<span class="text-danger"><?php echo e($errors->first('android_version')); ?></span>
					</div>
					</div>
						<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Android URL</label>
					  <div class="col-sm-6">
					    <input type="text" name="android_url" class="form-control" value="<?php echo e(@$result['android_url']); ?>" placeholder="Android URL">
					  	<span class="text-danger"><?php echo e($errors->first('android_url')); ?></span>
					</div>
					</div>
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">IOS Version</label>
					  <div class="col-sm-6">
					    <input type="text" name="ios_version" class="form-control" value="<?php echo e(@$result['ios_version']); ?>" placeholder="IOS Version">
					  	<span class="text-danger"><?php echo e($errors->first('ios_version')); ?></span>
					  </div>
					</div>
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">IOS URL</label>
					  <div class="col-sm-6">
					    <input type="text" name="ios_url" class="form-control" value="<?php echo e(@$result['ios_url']); ?>" placeholder="IOS URL">
					  	<span class="text-danger"><?php echo e($errors->first('ios_url')); ?></span>
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Mpos Android Version</label>
					  <div class="col-sm-6">
					    <input type="text" name="mpos_android_version" class="form-control" value="<?php echo e(@$result['mpos_android_version']); ?>" placeholder="Mpos Android Version">
					  	<span class="text-danger"><?php echo e($errors->first('mpos_android_version')); ?></span>
					</div>
					</div>
						<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Mpos Android URL</label>
					  <div class="col-sm-6">
					    <input type="text" name="mpos_android_url" class="form-control" value="<?php echo e(@$result['mpos_android_url']); ?>" placeholder="Mpos Android URL">
					  	<span class="text-danger"><?php echo e($errors->first('mpos_android_url')); ?></span>
					</div>
					</div>
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Mpos IOS Version</label>
					  <div class="col-sm-6">
					    <input type="text" name="mpos_ios_version" class="form-control" value="<?php echo e(@$result['mpos_ios_version']); ?>" placeholder="Mpos IOS Version">
					  	<span class="text-danger"><?php echo e($errors->first('mpos_ios_version')); ?></span>
					  </div>
					</div>
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Mpos IOS URL</label>
					  <div class="col-sm-6">
					    <input type="text" name="mpos_ios_url" class="form-control" value="<?php echo e(@$result['mpos_ios_url']); ?>" placeholder="Mpos IOS URL">
					  	<span class="text-danger"><?php echo e($errors->first('mpos_ios_url')); ?></span>
					  </div>
					</div>
				</div>
        				
  				<?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_general_setting')): ?>
    				<div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat pull-right" id="general-settings-submit">
                            <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="general-settings-submit-text">Submit</span>
                        </button>
                    </div>
  				<?php endif; ?>
            </form>
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

  <!-- bootstrap-select -->
  <script src="<?php echo e(asset('public/backend/bootstrap-select-1.13.12/js/bootstrap-select.min.js')); ?>" type="text/javascript"></script>

  <!-- read-file-on-change -->
  <?php echo $__env->make('common.read-file-on-change', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <script type="text/javascript">

      function updateSideBarCompanySmallLogo(file)
      {
          if (file.name.match(/.(png|jpg|jpeg|gif|bmp)$/i))
          {
            $.ajax(
            {
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: SITE_URL+"/admin/settings/update-sidebar-company-logo",
                data: new FormData($('#general_settings_form')[0]),
                cache:false,
                contentType: false,
                processData: false,
            })
            .done(function(res)
            {
                $('.company-logo').attr('src', SITE_URL+'/public/images/logos/'+ res.filename);
            })
            .fail(function(error)
            {
                console.log(error.responseText);
            });
          }
          else
          {
            $('.company-logo').attr('src', SITE_URL+'/public/uploads/userPic/default-logo.jpg');
          }
      }

      $(window).on('load', function()
      {
          $(".has_captcha, .login_via, .default_currency, .default_language").select2({});
          $('#default_crypto_currencies').selectpicker();

          let selectedCryptoCurrencies = '<?php echo e($result['default_crypto_currencies']); ?>';
          // console.log(selectedCryptoCurrencies);
          if (selectedCryptoCurrencies != 'none')
          {
              $.each(selectedCryptoCurrencies.split(","), function(i,e)
              {
                  $("#default_crypto_currencies option[value='" + e + "']").prop("selected", true);
                  $('#default_crypto_currencies').selectpicker('refresh');
              });
          }
      });

      // preview company logo on change
      $(document).on('change','#logo', function()
      {
          let orginalSource = '<?php echo e(url('public/uploads/userPic/default-logo.jpg')); ?>';
          let logo = $('#logo').attr('data-rel');
          if (logo != '') {
            readFileOnChange(this, $('#logo-preview'), orginalSource);
            $('.remove_img_preview_site_logo').remove();
            updateSideBarCompanySmallLogo(this.files[0]);
          }
          else
          {
            readFileOnChange(this, $('#logo-demo-preview'), orginalSource);
            updateSideBarCompanySmallLogo(this.files[0]);
          }
      });

      // preview company favicon on change
      $(document).on('change','#favicon', function()
      {
          let orginalSource = '<?php echo e(url('public/uploads/userPic/default-image.png')); ?>'
          let favicon = $('#favicon').attr('data-favicon');
          if (favicon != '') {
            readFileOnChange(this, $('#favicon-preview'), orginalSource);
            $('.remove_fav_preview').remove();
          }
          else
          {
            readFileOnChange(this, $('#favicon-demo-preview'), orginalSource);
          }
      });

      //Delete logo preview
      $(document).on('click','.remove_img_preview_site_logo', function()
      {
          var logo = $('#logo').attr('data-rel');
          if(logo)
          {
            $.ajax(
            {
              headers:
              {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type : "POST",
              url : SITE_URL+"/admin/settings/delete-logo",
              data: {
                'logo' : logo,
              },
              dataType : 'json',
              success: function(reply)
              {
                if (reply.success == 1)
                {
                  swal({title: "", text: reply.message, type: "success"},
                    function(){
                      window.location.reload();
                    }
                  );
                }
                else{
                    alert(reply.message);
                    window.location.reload();
                }
              }
            });
          }
      });

      //Delete favicon preview
      $(document).on('click','.remove_fav_preview', function()
      {
          var favicon = $('#favicon').attr('data-favicon');
          if(favicon)
          {
            $.ajax(
            {
              headers:
              {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type : "POST",
              url : SITE_URL+"/admin/settings/delete-favicon",
              data: {
                'favicon' : favicon,
              },
              dataType : 'json',
              success: function(reply)
              {
                if (reply.success == 1){
                  // window.location.reload();
                  swal({title: "", text: reply.message, type: "success"},
                    function(){
                      window.location.reload();
                    }
                  );
                }else{
                    alert(reply.message);
                    window.location.reload();
                }
              }
            });
          }
      });

      $(document).on('change','#login_via', function()
      {
          if ($(this).val() == 'email_or_phone' || $(this).val() == 'phone_only')
          {
            $.ajax({
              headers:
              {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              method: "POST",
              url: SITE_URL+"/admin/settings/check-sms-settings",
              dataType: "json",
              contentType: false,
              processData: false,
              cache: false,
            })
            .done(function(response)
            {
                // console.log(response);
                if (response.status == false)
                {
                    $('#sms-error').addClass('error').html(response.message).css("font-weight", "bold");
                    $('form').find("button[type='submit']").prop('disabled',true);
                }
                else if (response.status == true)
                {
                    $('#sms-error').html('');

                    $('form').find("button[type='submit']").prop('disabled',false);
                }
            });
          }
          else
          {
            $('#sms-error').html('');
            $('form').find("button[type='submit']").prop('disabled',false);
          }
      });

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

      $('#general_settings_form').validate({
          rules: {
              name: {
                  required: true,
              },
              "photos[logo]": {
                  extension: "png|jpg|jpeg|gif|bmp",
              },
              "photos[favicon]": {
                  extension: "png|jpg|jpeg|gif|bmp",
              },
          },
          messages: {
            "photos[logo]": {
              extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
            },
            "photos[favicon]": {
              extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
            }
          },
          submitHandler: function(form)
          {
              $("#general-settings-submit").attr("disabled", true).click(function (e) {
                  e.preventDefault();
              });
              $(".fa-spin").show();
              $("#general-settings-submit-text").text('Submitting...');
              form.submit();
          }
      });

  </script>

<?php $__env->stopPush(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/settings/version.blade.php ENDPATH**/ ?>