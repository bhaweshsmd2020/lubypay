<?php $__env->startSection('title', 'SMS Settings'); ?>

<?php $__env->startSection('head_style'); ?>
    <!-- bootstrap-toggle -->
    <link rel="stylesheet" href="<?php echo e(asset('public/backend/bootstrap-toggle/css/bootstrap-toggle.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <!-- Main content -->
    <div class="row">

        <div class="col-md-12">
            <div class="box box-info">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="<?php echo e(url('admin/settings/sms/twilio')); ?>">Twilio</a></li>
                        <li><a href="<?php echo e(url('admin/settings/sms/nexmo')); ?>">Nexmo</a></li>                        
                        <li><a href="<?php echo e(url('admin/settings/sms/oneway')); ?>">Oneway</a></li>
                        <li><a href="<?php echo e(url('admin/settings/sms/smdsms')); ?>">SMD SMS</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab_1">
                            <div class="card">
                                <div class="card-header">
                                    <h4></h4>
                                </div>
                                <div class="container-fluid">
                                    <div class="tab-pane" id="tab_2">

                                        <form action="<?php echo e(url('admin/settings/sms/twilio')); ?>" method="POST" class="form-horizontal" id="twilio_sms_setting_form">
                                            <?php echo csrf_field(); ?>


                                            <input type="hidden" name="type" value="<?php echo e(base64_encode($twilio->type)); ?>">

                                            <div class="box-body">

                                                
                                                <div class="form-group" style="display: none;">
                                                    <div class="col-md-10 col-md-offset-1">
                                                        <label class="col-md-3 control-label">Name</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="name" class="form-control" value="<?php echo e($twilio->type == 'twilio' ? 'Twilio' : ''); ?>" placeholder="Enter Twilio Sms Gateway Name" id="" readonly>
                                                            <?php if($errors->has('name')): ?>
                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('name')); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                
                                                <div class="form-group">
                                                    <div class="col-md-10 col-md-offset-1">
                                                        <label class="col-md-3 control-label">Account SID</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="twilio[account_sid]" class="form-control" value="<?php echo e(isset($credentials->account_sid) ? $credentials->account_sid : ''); ?>" placeholder="Enter Twilio Account SID" id="">
                                                            <?php if($errors->has('twilio.account_sid')): ?>
                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('twilio.account_sid')); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                
                                                <div class="form-group">
                                                    <div class="col-md-10 col-md-offset-1">
                                                        <label class="col-md-3 control-label">Auth Token</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="twilio[auth_token]" class="form-control" value="<?php echo e(isset($credentials->auth_token) ? $credentials->auth_token : ''); ?>" placeholder="Enter Twilio Auth Token" id="">
                                                            <?php if($errors->has('twilio.auth_token')): ?>
                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('twilio.auth_token')); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                
                                                <div class="form-group">
                                                    <div class="col-md-10 col-md-offset-1">
                                                        <label class="col-md-3 control-label">Default Phone Number</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="twilio[default_twilio_phone_number]" class="form-control"
                                                            value="<?php echo e(isset($credentials->default_twilio_phone_number) ? $credentials->default_twilio_phone_number : ''); ?>" placeholder="Enter Twilio Default Phone Number" id="">
                                                            <?php if($errors->has('twilio.default_twilio_phone_number')): ?>
                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('twilio.default_twilio_phone_number')); ?></span>
                                                            <?php endif; ?>
                                                            <div class="clearfix"></div>
                                                            <h6 class="form-text text-muted"><strong>*Must enter phone number without(+) symbol.</strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                
                                                <div class="form-group">
                                                    <div class="col-md-10 col-md-offset-1">
                                                        <label class="col-md-3 control-label">Europian Phone Number</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="twilio[europe_twilio_phone_number]" class="form-control"
                                                            value="<?php echo e(isset($credentials->europe_twilio_phone_number) ? $credentials->europe_twilio_phone_number : ''); ?>" placeholder="Enter Twilio Default Phone Number" id="">
                                                            <?php if($errors->has('twilio.europe_twilio_phone_number')): ?>
                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('twilio.europe_twilio_phone_number')); ?></span>
                                                            <?php endif; ?>
                                                            <div class="clearfix"></div>
                                                            <h6 class="form-text text-muted"><strong>*Must enter phone number without(+) symbol.</strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                
                                                <div class="form-group">
                                                    <div class="col-md-10 col-md-offset-1">
                                                        <label class="col-md-3 control-label">Status</label>
                                                        <div class="col-md-8">
                                                            <select name="status" class="select2 select2-hidden-accessible" id="">
                                                                <option <?php echo e($twilio->status == 'Active' ? 'selected' : ''); ?> value="Active">Active</option>
                                                                <option <?php echo e($twilio->status == 'Inactive' ? 'selected' : ''); ?> value="Inactive">Inactive</option>
                                                            </select>
                                                            <?php if($errors->has('status')): ?>
                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('status')); ?></span>
                                                            <?php endif; ?>
                                                            <div class="clearfix"></div>
                                                            <h6 class="form-text text-muted"><strong>*Incoming SMS messages might be delayed by <?php echo e(ucfirst($twilio->type)); ?>.</strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div style="margin-top:10px">
                                                        <a id="cancel_anchor" href="<?php echo e(url('admin/settings/sms/twilio')); ?>" class="btn btn-danger btn-flat">Cancel</a>
                                                        <button type="submit" class="btn btn-primary pull-right btn-flat" id="sms-settings-twilio-submit-btn">
                                                            <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="sms-settings-twilio-submit-btn-text">Update</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

    <!-- jquery.validate -->
    <script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

    <script type="text/javascript">

        $(function () {
            $(".select2").select2({
            });
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


        $('#twilio_sms_setting_form').validate({
            rules: {
                "twilio[account_sid]": {
                    required: true,
                },
                "twilio[auth_token]": {
                    required: true,
                },
                "twilio[default_twilio_phone_number]": {
                    required: true,
                    digits: true,
                },
                "twilio[europe_twilio_phone_number]": {
                    required: true,
                    digits: true,
                },
            },
            messages: {
                "twilio[account_sid]": {
                    required: "Twilio Account SID is required!",
                },
                "twilio[auth_token]": {
                    required: "Twilio Auth Token is required!",
                },
                "twilio[default_twilio_phone_number]": {
                    required: "Twilio Default Phone Number is required",
                },
                "twilio[europe_twilio_phone_number]": {
                    required: "Twilio Europian Phone Number is required",
                },
            },
            submitHandler: function(form)
            {
                $("#sms-settings-twilio-submit-btn").attr("disabled", true);
                $(".fa-spin").show();
                $("#sms-settings-twilio-submit-btn-text").text('Updating...');
                $('#cancel_anchor').attr("disabled",true);
                $('#sms-settings-twilio-submit-btn').click(false);
                form.submit();
            }
        });

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/settings/sms/twilio.blade.php ENDPATH**/ ?>