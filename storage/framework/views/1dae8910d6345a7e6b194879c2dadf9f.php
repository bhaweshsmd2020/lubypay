
<?php $__env->startSection('title', 'Email Settings'); ?>

<?php $__env->startSection('page_content'); ?>

    <!-- Main content -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Email Settings <?php if(@$result['status']==1): ?>(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i> Verified</span>) <?php endif; ?> </h3>
                </div>

                <form action="<?php echo e(url('admin/settings/email')); ?>" method="post" class="form-horizontal" id="emai_settings">
                <?php echo csrf_field(); ?>


                <!-- box-body -->
                    <div class="box-body">
                        <!-- driver -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Driver</label>
                            <div class="col-sm-6">
                                
                                <select id="driver" name="driver" class="form-control">
                                    <option value="smtp" <?= isset($result['email_protocol']) && $result['email_protocol'] == "smtp" ? "selected" : "" ?> >
                                        SMTP
                                    </option>
                                    <option value="sendmail" <?= isset($result['email_protocol']) && $result['email_protocol'] == "sendmail" ? "selected" : "" ?> >
                                        Send Mail
                                    </option>
                                </select>
                                <?php if($errors->has('email_protocol')): ?>
                                    <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('email_protocol')); ?></strong>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="smtpFields" <?php if($result['email_protocol']=="smtp"): ?> style="display: block;" <?php else: ?> style="display: none;" <?php endif; ?>>
                            <!-- host -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Host</label>
                                <div class="col-sm-6">
                                    <input type="text" name="host" class="form-control" value="<?php echo e(@$result['smtp_host']); ?>"
                                           placeholder="Host" id="host">

                                    <?php if($errors->has('smtp_host')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('smtp_host')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- port -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Port</label>
                                <div class="col-sm-6">
                                    <input type="text" name="port" class="form-control" value="<?php echo e(@$result['smtp_port']); ?>"
                                           placeholder="Port" id="port">

                                    <?php if($errors->has('smtp_port')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('smtp_port')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- from_address -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">From Address</label>
                                <div class="col-sm-6">
                                    <input type="text" name="from_address" class="form-control"
                                           value="<?php echo e(@$result['from_address']); ?>" placeholder="From Address"
                                           id="from_address">

                                    <?php if($errors->has('from_address')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('from_address')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- from_name -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">From Name</label>
                                <div class="col-sm-6">
                                    <input type="text" name="from_name" class="form-control"
                                           value="<?php echo e(@$result['from_name']); ?>" placeholder="From Name" id="from_name">

                                    <?php if($errors->has('from_name')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('from_name')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- encryption -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Encryption</label>
                                <div class="col-sm-6">
                                    <input type="text" name="encryption" class="form-control"
                                           value="<?php echo e(@$result['email_encryption']); ?>" placeholder="Encryption"
                                           id="encryption">

                                    <?php if($errors->has('email_encryption')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('email_encryption')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- username -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Username</label>
                                <div class="col-sm-6">
                                    <input type="text" name="username" class="form-control"
                                           value="<?php echo e(@$result['smtp_username']); ?>" placeholder="Username" id="username">

                                    <?php if($errors->has('smtp_username')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('smtp_username')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- password -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-6">
                                    <input type="password" name="password" class="form-control"
                                           value="<?php echo e(@$result['smtp_password']); ?>" placeholder="Password" id="password">

                                    <?php if($errors->has('smtp_password')): ?>
                                        <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('smtp_password')); ?></strong>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Notification Email</label>
                            <div class="col-sm-6">
                                <input type="text" name="notification_email" class="form-control" value="<?php echo e(@$result['notification_email']); ?>" placeholder="Notification Email" id="notification_email">
                                <?php if($errors->has('notification_email')): ?>
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo e($errors->first('notification_email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- box-footer -->
                    <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_email_setting')): ?>
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

    $('#emai_settings').validate({
        rules: {
            driver: {
                required: true,
            },
            host: {
                required: true,
            },
            port: {
                required: true,
                number: true,
            },
            from_address: {
                required: true,
                email: true,
            },
            from_name: {
                required: true,
            },
            encryption: {
                required: true,
            },
            username: {
                required: true,
            },
            password: {
                required: true,
            },
        },
    });

    $("#driver").on('change',function(e){
        e.preventDefault();
        smtpfield=$("#smtpFields");
        if($(this).val()=="smtp"){
            smtpfield.show();
        }else{
            smtpfield.hide();
        }
    });
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/settings/email.blade.php ENDPATH**/ ?>