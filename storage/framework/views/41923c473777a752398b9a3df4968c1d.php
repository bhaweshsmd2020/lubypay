

<?php $__env->startSection('title', 'Add Ticket'); ?>

<?php $__env->startSection('head_style'); ?>
    <!-- wysihtml5 -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')); ?>">

    <!-- jquery-ui-1.12.1 -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="top-bar-title padding-bottom">Add Ticket</div>
            </div>
            <div class="col-md-6">
                <div class="top-bar-title padding-bottom">
                         <?php if($errors->has('file')): ?>
                            <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('file')); ?></strong>
                                </span>
                        <?php endif; ?>
              </div>
            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-body">

        <form id="add_ticket_form" class="form-horizontal" action="<?php echo e(url('admin/tickets/store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>


            <input id="user_id" type="hidden" name="user_id" value="">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="col-sm-2 control-label require">Subject</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="subject" id="subject">
                            <?php if($errors->has('subject')): ?>
                                <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('subject')); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label require">Message</label>
                        <div class="col-sm-10">
                            <textarea class="message form-control" name="message" id="message" cols="30" rows="10"></textarea>
                            <?php if($errors->has('message')): ?>
                                <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('message')); ?></strong>
                                </span>
                            <?php endif; ?>
                            <div id="error-message"></div>
                        </div>
                    </div>

                </div>
               <!--<div class="col-md-6">-->
               <!--       <div class="form-group">-->
               <!--           <label class="col-sm-4 control-label">File</label>-->
               <!--           <div class="col-sm-8">-->
               <!--             <input type="file" name="file" class="form-control input-file-field">-->
               <!--           </div>-->
               <!--       </div>-->
               <!--   </div>-->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label require">Assignee</label>
                        <div class="col-sm-8">
                            <select name="assignee" class="form-control select2">
                                <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($admin->id); ?>"><?php echo e($admin->first_name.' '.$admin->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Status</label>
                        <div class="col-sm-8">
                            <select name="status" class="form-control select2">
                                <?php $__currentLoopData = $ticket_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket_status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($ticket_status->isdefault == '1' ? 'selected' : ''); ?> value="<?php echo e($ticket_status->id); ?>"><?php echo e($ticket_status->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label require">User</label>
                        <div class="col-sm-8">
                            <input id="user_input" type="text" name="user" placeholder="Enter Name" class="form-control">
                            <span id="error-user"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label require">Priority</label>
                        <div class="col-sm-8">
                            <select name="priority" id="priority" class="form-control select2">
                                <option value="Low">Low</option>
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" id="assigned_email_div">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-8">
                            <input id="assigned_email" type="text" class="form-control" readonly name="email">
                        </div>
                    </div>
                </div>
              

                <div class="col-md-12">
                    <a id="cancel_anchor" class="btn btn-danger btn-flat pull-left" href="<?php echo e(url('admin/tickets/list')); ?>">Cancel</a>
                    <button type="submit" class="btn btn-primary pull-right btn-flat" id="create_ticket"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="create_ticket_text">Add</span></button>
                </div>

            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<!-- wysihtml5 -->
<script src="<?php echo e(asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')); ?>" type="text/javascript"></script>

<!-- jquery-ui-1.12.1 -->
<script src="<?php echo e(asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
        $('.message').wysihtml5({
            events: {
                change: function () {
                    if($('#message').val().length === 0 )
                    {
                        $('#error-message').addClass('error').html('This field is required.').css("font-weight", "bold");
                    }
                    else
                    {
                        $('#error-message').html('');
                    }
                }
            }
        });
        $(".select2").select2({});
    });

    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
        errorPlacement: function (error, element)
        {
            if (element.prop('name') === 'message')
            {
                $('#error-message').html(error);
            } else {
                error.insertAfter(element);
            }
        }
    });

    $('#add_ticket_form').validate({
        ignore: ":hidden:not(textarea)",
        rules: {
            subject: {
                required: true,
            },
            message: "required",
            user: {
                required: true,
            },
        },
        submitHandler: function(form)
        {
            $("#create_ticket").attr("disabled", true);
            $(".fa-spin").show();
            $("#create_ticket_text").text('Creating...');
            $('#cancel_anchor').attr("disabled",true);
            $('#create_ticket').click(false);
            form.submit();
        }
    });

    $(document).ready(function()
    {
        $('#assigned_email_div').hide();

        $("#user_input").on('keyup keypress', function(e)
        {
            if (e.type=="keyup" || e.type=="keypress")
            {
                $('#assigned_email_div').hide();

                var user_input = $('form').find("input[name='user']").val();

                if(user_input.length === 0)
                {
                    $('#user_id').val('');
                    $('#error-user').html('');
                }
            }
        });

        $('#user_input').autocomplete(
        {
            source:function(req,res)
            {
                if (req.term.length > 0)
                {
                    $.ajax({
                        url:'<?php echo e(url('admin/ticket_user_search')); ?>',
                        dataType:'json',
                        type:'get',
                        data:{
                            search:req.term
                        },
                        success:function (response)
                        {
                            // console.log(response);

                            $('form').find("button[type='submit']").prop('disabled',true);

                            if(response.status == 'success')
                            {
                                res($.map(response.data, function (item)
                                {
                                    $('#assigned_email_div').show();

                                    var name = item.first_name + ' ' + item.last_name;
                                    // name = name.toLowerCase().replace(/\b[a-z]/g, function(letter) { return letter.toUpperCase(); });

                                    return {
                                            id : item.user_id,
                                            first_name : item.first_name,
                                            last_name : item.last_name,
                                            value: name, //don't change value
                                            email: item.email,
                                        }
                                    }
                                ));
                            }
                            else if(response.status == 'fail')
                            {
                                $('#assigned_email').val('');
                                $('#assigned_email_div').hide();
                                $('#error-user').addClass('text-danger').html('User Does Not Exist!');
                            }
                        }
                    })
                }
                else
                {
                    // console.log(req.term.length);
                    $('#user_id').val('');
                }
            },
            select: function (event, ui)
            {
                var e = ui.item;

                $('form').find("button[type='submit']").prop('disabled',false);

                $('#error-user').html('');


                $('#user_id').val(e.id);

                $('#assigned_email').val(e.email);
            },
            minLength: 0,
            autoFocus: true
        });
    });

</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/tickets/add.blade.php ENDPATH**/ ?>