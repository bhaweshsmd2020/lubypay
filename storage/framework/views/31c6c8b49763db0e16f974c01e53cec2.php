
<?php $__env->startSection('title', 'Email Notification Settings'); ?>

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
                        <li><a href="<?php echo e(url('admin/settings/notification-types')); ?>">Notification Types</a></li>
                        <li class="active"><a href="<?php echo e(url('admin/settings/notification-settings/email')); ?>">Email Notification Settings</a></li>
                        
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab_1">
                            <div class="card">
                                <div class="card-header">
                                    <h4></h4>
                                </div>
                                <div class="container-fluid">
                                    <div class="tab-pane" id="tab_2">

                                        <form action="<?php echo e(url('admin/settings/notification-settings/update')); ?>" method="POST" class="form-horizontal" id="email_notification_setting_form">
                                            <?php echo csrf_field(); ?>


                                            <div class="box-body">
                                                <?php $__currentLoopData = $notificationSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notificationEmailSetting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                    <input type="hidden" name="notification[<?php echo e($notificationEmailSetting->recipient_type); ?>][<?php echo e($notificationEmailSetting->notification_type->alias); ?>][id]" value="<?php echo e($notificationEmailSetting->id); ?>">

                                                    <div class="form-group">
                                                        <div class="col-md-9 col-md-offset-1">
                                                            
                                                            <label class="col-md-3 control-label"><?php echo e($notificationEmailSetting->name); ?></label>

                                                            
                                                            <div class="col-md-2">
                                                                <input type="checkbox" data-toggle="toggle"  name="notification[<?php echo e($notificationEmailSetting->recipient_type); ?>][<?php echo e($notificationEmailSetting->alias); ?>][status]" <?php echo e(isset($notificationEmailSetting->status) && $notificationEmailSetting->status == 'Yes' ? 'checked' : ''); ?>

                                                                    class="email_checkbox"
                                                                    data-rel="<?php echo e($notificationEmailSetting->alias); ?>"
                                                                    id="notification[<?php echo e($notificationEmailSetting->recipient_type); ?>][<?php echo e($notificationEmailSetting->alias); ?>][status]" >
                                                            </div>

                                                            
                                                            <div class="col-md-7">
                                                                <input type="text" name="notification[<?php echo e($notificationEmailSetting->recipient_type); ?>][<?php echo e($notificationEmailSetting->alias); ?>][recipient]" class="form-control"
                                                                value="<?php echo e(isset($notificationEmailSetting->recipient) ? $notificationEmailSetting->recipient : ''); ?>"
                                                                placeholder="Enter email for <?php echo e($notificationEmailSetting->name); ?>"
                                                                id="email_<?php echo e($notificationEmailSetting->alias); ?>" <?php echo e(isset($notificationEmailSetting->status) && $notificationEmailSetting->status == 'No' ? 'readonly' : ''); ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div style="margin-top:10px">
                                                        <a href="<?php echo e(url('admin/settings/notification-types')); ?>" class="btn btn-danger btn-flat">Cancel</a>
                                                        <button class="btn btn-primary pull-right btn-flat" type="submit">Update</button>
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
    <!-- bootstrap-toggle -->
    <script src="<?php echo e(asset('public/backend/bootstrap-toggle/js/bootstrap-toggle.min.js')); ?>" type="text/javascript"></script>

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

        // Email validation
        $('#email_notification_setting_form').validate({
            rules: {
                "notification[email][deposit][recipient]": {
                    email: true,
                    required: function(element){
                        var statusCheck = $('input[name="notification[email][deposit][status]"]:checked').length;
                        if(statusCheck == 1)
                        {
                            return $("#email_deposit").val()=="";
                        }
                        else
                        {
                            return false;
                        }
                    },
                },
                "notification[email][payout][recipient]": {
                    email: true,
                    required: function(element){
                        var statusCheck = $('input[name="notification[email][payout][status]"]:checked').length;
                        if(statusCheck == 1)
                        {
                            return $("#email_payout").val()=="";
                        }
                        else
                        {
                            return false;
                        }
                    },
                },
                "notification[email][send][recipient]": {
                    email: true,
                    required: function(element){
                        var statusCheck = $('input[name="notification[email][send][status]"]:checked').length;
                        if(statusCheck == 1)
                        {
                            return $("#email_send").val()=="";
                        }
                        else
                        {
                            return false;
                        }
                    },
                },
                "notification[email][request][recipient]": {
                    email: true,
                    required: function(element){
                        var statusCheck = $('input[name="notification[email][request][status]"]:checked').length;
                        if(statusCheck == 1)
                        {
                            return $("#email_request").val()=="";
                        }
                        else
                        {
                            return false;
                        }
                    },
                },
                "notification[email][exchange][recipient]": {
                    email: true,
                    required: function(element){
                        var statusCheck = $('input[name="notification[email][exchange][status]"]:checked').length;
                        if(statusCheck == 1)
                        {
                            return $("#email_exchange").val()=="";
                        }
                        else
                        {
                            return false;
                        }
                    },
                },
                "notification[email][payment][recipient]": {
                    email: true,
                    required: function(element){
                        var statusCheck = $('input[name="notification[email][payment][status]"]:checked').length;
                        if(statusCheck == 1)
                        {
                            return $("#email_payment").val()=="";
                        }
                        else
                        {
                            return false;
                        }
                    },
                },
            },
        });

        // Email - on change due to http://www.bootstraptoggle.com/
        $(document).on('change', '.email_checkbox', function() {
            var inputName = $(this).attr("data-rel");
            if (this.checked == true) {
                $("#email_"+inputName).prop('readonly',false);
            } else {
                $("#email_"+inputName).prop('readonly',true);

            }
        });

    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/settings/notification_email_settings/index.blade.php ENDPATH**/ ?>