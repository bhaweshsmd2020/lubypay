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

                        <li><a href="<?php echo e(url('admin/settings/sms/twilio')); ?>">Twilio</a></li>

                        <li><a href="<?php echo e(url('admin/settings/sms/nexmo')); ?>">Nexmo</a></li>
                        <li class="active"><a href="<?php echo e(url('admin/settings/sms/oneway')); ?>">Oneway</a></li>
                        <li><a href="<?php echo e(url('admin/settings/sms/smdsms')); ?>">SMD SMS</a></li>

                    </ul>



                    <div class="tab-content">

                        <div class="tab-pane fade in active" id="tab_1">

                            <div class="card">

                                <div class="card-header">

                                    <h4></h4>

                                </div>

                                <div class="container-fluid">

                                    <div class="tab-pane" id="tab_3">



                                        <form action="<?php echo e(url('admin/settings/sms/oneway')); ?>" method="POST" class="form-horizontal" id="oneway_sms_setting_form">

                                            <?php echo csrf_field(); ?>




                                            <input type="hidden" name="type" value="<?php echo e(base64_encode($oneway->type)); ?>">



                                            <div class="box-body">



                                                

                                                <div class="form-group" style="display: none;">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Name</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="name" class="form-control" value="<?php echo e($oneway->type == 'oneway' ? 'Oneway' : ''); ?>" placeholder="Enter Oneway Sms Gateway Name" id="" readonly>

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

                                                            <input type="text" name="oneway[account_sid]" class="form-control" value="<?php echo e(isset($credentials->account_sid) ? $credentials->account_sid : ''); ?>" placeholder="Enter Oneway Account SID" id="">

                                                            <?php if($errors->has('oneway.account_sid')): ?>

                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('oneway.account_sid')); ?></span>

                                                            <?php endif; ?>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>



                                                

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Auth Token</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="oneway[auth_token]" class="form-control" value="<?php echo e(isset($credentials->auth_token) ? $credentials->auth_token : ''); ?>" placeholder="Enter Oneway Auth Token" id="">

                                                            <?php if($errors->has('oneway.auth_token')): ?>

                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('oneway.auth_token')); ?></span>

                                                            <?php endif; ?>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>



                                                

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Default Phone Number</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="oneway[default_oneway_phone_number]" class="form-control"

                                                            value="<?php echo e(isset($credentials->default_oneway_phone_number) ? $credentials->default_oneway_phone_number : ''); ?>" placeholder="Enter Oneway Default Phone Number" id="">

                                                            <?php if($errors->has('oneway.default_oneway_phone_number')): ?>

                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('oneway.default_oneway_phone_number')); ?></span>

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

                                                                <option <?php echo e($oneway->status == 'Active' ? 'selected' : ''); ?> value="Active">Active</option>

                                                                <option <?php echo e($oneway->status == 'Inactive' ? 'selected' : ''); ?> value="Inactive">Inactive</option>

                                                            </select>

                                                            <?php if($errors->has('status')): ?>

                                                                <span style="color:red;font-weight:bold;"><?php echo e($errors->first('status')); ?></span>

                                                            <?php endif; ?>

                                                            <div class="clearfix"></div>

                                                            <h6 class="form-text text-muted"><strong>*Incoming SMS messages might be delayed by <?php echo e(ucfirst($oneway->type)); ?>.</strong></h6>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>

                                            </div>



                                            <div class="row">

                                                <div class="col-md-12">

                                                    <div style="margin-top:10px">

                                                        <a id="cancel_anchor" href="<?php echo e(url('admin/settings/sms/oneway')); ?>" class="btn btn-danger btn-flat">Cancel</a>

                                                        <button type="submit" class="btn btn-primary pull-right btn-flat" id="sms-settings-oneway-submit-btn">

                                                            <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="sms-settings-oneway-submit-btn-text">Update</span>

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





        $('#oneway_sms_setting_form').validate({

            rules: {

                "oneway[account_sid]": {

                    required: true,

                },

                "oneway[auth_token]": {

                    required: true,

                },

                "oneway[default_oneway_phone_number]": {

                    required: true,

                    digits: true,

                },

            },

            messages: {

                "oneway[account_sid]": {

                    required: "Oneway Account SID is required!",

                },

                "oneway[auth_token]": {

                    required: "Oneway Auth Token is required!",

                },

                "oneway[default_oneway_phone_number]": {

                    required: "Oneway Default Phone Number is required",

                },

            },

            submitHandler: function(form)

            {

                $("#sms-settings-oneway-submit-btn").attr("disabled", true);

                $(".fa-spin").show();

                $("#sms-settings-oneway-submit-btn-text").text('Updating...');

                $('#cancel_anchor').attr("disabled",true);

                $('#sms-settings-oneway-submit-btn').click(false);

                form.submit();

            }

        });



    </script>

<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/settings/sms/oneway.blade.php ENDPATH**/ ?>