

<?php $__env->startSection('title', 'Ticket Reply'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- wysihtml5 -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>

<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-9">
                <div class="top-bar-title padding-bottom">Ticket Reply</div>
            </div>
            <div class="col-md-3">
             <h4 class="pull-right">Ticket Status: <span class="label label-info" id="status_label"><?php echo e($ticket->ticket_status->name); ?></span></h4>
            </div>
        </div>
    </div>
</div>

<!-- Reply Form -->
<div class="box">
    <div class="box-header with-border"><h4> <strong>Subject  : </strong> <?php echo e($ticket->subject); ?></h4></div>

    <div class="box-header with-border">
        <div class="col-md-9">
            <span class="label label-default" style="font-size: 14px">Priority : <?php echo e($ticket->priority); ?></span>
            <?php if(isset($ticket->admin_id)): ?>
                <span class="label label-warning" style="font-size: 14px">Assignee : <?php echo e($ticket->admin->first_name.' '.$ticket->admin->last_name); ?></span>
            <?php endif; ?>
        </div>

        <div class="col-md-3">
             <div class="float-right">
            <span>Ticket ID : </span>
            <span class="label label-info" >
                 <?php echo e($ticket->code); ?>

                <!--<select id="status_ticket" class="form-control">-->
                <!--    <?php $__currentLoopData = $ticket_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>-->
                <!--        <option <?php echo e($status->id == $ticket->ticket_status_id ? 'selected':''); ?>  value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>-->
                <!--    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->
                <!--</select>-->
            </span>
            </div>
        </div>
    </div>


    <div class="box-body">

        <form class="form-horizontal" id="reply_form" action="<?php echo e(url('admin/tickets/reply/store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>


            <input type="hidden" name="ticket_id" value="<?php echo e($ticket->id); ?>">

            <input type="hidden" name="user_id" value="<?php echo e($ticket->user_id); ?>">

            <input type="hidden" name="name" value="<?php echo e($ticket->user->first_name.' '.$ticket->user->last_name); ?>">

            <input type="hidden" name="email" value="<?php echo e($ticket->user->email); ?>">


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="col-sm-1 control-label require">Reply</label>
                        <div class="col-sm-11">
                            <textarea name="message" id="message" class="message form-control" cols="30" rows="10"></textarea>
                            <?php if($errors->has('message')): ?>
                                <span class="help-block">
                                  <strong class="text-danger"><?php echo e($errors->first('message')); ?></strong>
                                </span>
                            <?php endif; ?>
                            <div id="error-message"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                 <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label class="col-sm-3 control-label">Status</label>
                          <div class="col-sm-6">
                              <select name="status_id" class="form-control select2">
                                <?php $__currentLoopData = $ticket_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($status->id == $ticket->ticket_status_id ? 'selected':''); ?>  value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label class="col-sm-3 control-label">File</label>
                          <div class="col-sm-9">
                            <input type="file" name="file" class="form-control input-file-field">
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <button type="submit" class="btn btn-primary pull-right btn-flat" id="reply"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="reply_text">Reply</span></button>
                  </div>
                </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Show Customer Query -->
<?php if($ticket->admin_id != NULL): ?>
    <div class="box">
        <div class="box-body"  style="background-color: #FFFFE6">
            <div class="col-sm-1">
              <h5><a href="<?php echo e(url('admin/users/edit/'. $ticket->user->id)); ?>"><?php echo e(($ticket->user->first_name.' '.$ticket->user->last_name)); ?></a></h5>

              <?php if(!empty($ticket->user->picture)): ?>
                <img alt="User profile picture" src="<?php echo e(url('public/user_dashboard/profile/'.$ticket->user->picture)); ?>" class="img-responsive img-circle asa">
              <?php else: ?>
                <img alt="Default picture" src='<?php echo e(url("public/uploads/userPic/default-image.png")); ?>' class="img-responsive img-circle asa">
              <?php endif; ?>

            </div>
            <div class="col-sm-11">
                <p style="margin-top: 10px; text-align: justify;"><?php echo ucfirst($ticket->message); ?></p>
                <hr  style="border-top: dotted 1px; width: 200px; float: left; margin-top: 0px">
                  <?php if($ticket->file): ?>
                      <a href="<?php echo e(url('public/uploads/ticketFile').'/'.$ticket->file->filename); ?>" class="pull-right"><i class="fa fa-fw fa-download"></i><?php echo e($ticket->file->originalname); ?></a>
                  <?php endif; ?>
            </div>
        </div>
        <div class="box-footer">
            
            <span><i class="fa fa-fw fa-clock-o"></i><small><i><?php echo e(dateFormat($ticket->created_at)); ?></i></small></span>
        </div>
    </div>
<?php else: ?>
  <!-- Show Admin Query -->
   <div class="box">
      <div class="box-body" style="background-color: #F2F4F4">
        <div class="col-sm-11">
           <p style="margin-top: 10px; text-align: justify;"><?php echo ucfirst($ticket->message); ?></p>
           <hr  style="border-top: dotted 1px; width: 200px; float: left; margin-top: 0px">
        </div>
        <div class="col-sm-1" style="text-align: center;">

          <span><a href="<?php echo e(url('admin/admin-user/edit/'. $ticket->admin->id)); ?>"><?php echo e(($ticket->admin->first_name.' '.$ticket->admin->last_name)); ?></a></span>

          <?php if(!empty($ticket->admin->picture)): ?>
            <img alt="Admin profile picture" src="<?php echo e(url('public/uploads/userPic/'.$ticket->admin->picture)); ?>" class=" img-responsive img-circle asa">
          <?php else: ?>
            <img alt="Default picture" src='<?php echo e(url("public/uploads/userPic/default-image.png")); ?>' class="img-responsive img-circle asa">
          <?php endif; ?>

        </div>
      </div>
       <div class="box-footer">
          
          <span class="pull-right"><i class="fa fa-fw fa-clock-o"></i><small><i><?php echo e(dateFormat($ticket->created_at)); ?></i></small></span>
      </div>
   </div>
<?php endif; ?>


<?php $__currentLoopData = $ticket_replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket_reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <!-- Show Customer Reply -->
    <?php if($ticket_reply->user_type == 'user'): ?>
        <div class="box">
            <div class="box-body"  style="background-color: #FFFFE6">
              <div class="col-sm-1" style="text-align: center;">

                  <h5><a href="<?php echo e(url('admin/users/edit/'. $ticket_reply->user->id)); ?>"><?php echo e(($ticket_reply->user->first_name.' '.$ticket_reply->user->last_name)); ?></a></h5>

                  <?php if(!empty($ticket_reply->user->picture)): ?>
                    <img alt="User profile picture" src="<?php echo e(url('public/user_dashboard/profile/'.$ticket_reply->user->picture)); ?>" class="img-responsive img-circle asa">
                  <?php else: ?>
                    <img alt="Default picture" src='<?php echo e(url("public/uploads/userPic/default-image.png")); ?>' class="img-responsive img-circle asa">
                  <?php endif; ?>

                  <hr style="margin: 5px 0px; width: 75px; color: #F2F4F4">
                    <form action="<?php echo e(url('admin/tickets/reply/delete/')); ?>" accept-charset="UTF-8" method="POST" style="display:inline">
                        <?php echo e(csrf_field()); ?>


                        <input type="hidden" name="id" value="<?php echo e($ticket_reply->id); ?>">
                        <input type="hidden" name="ticket_id" value="<?php echo e($ticket_reply->ticket_id); ?>">

                        <button class="btn btn-xs btn-danger btn-flat" data-message="Are you sure you want to delete this reply?" data-target="#confirmDelete" data-title="Delete Reply" data-toggle="modal" title="Delete" type="button" id="customer_reply_button">Delete</button>
                    </form>
              </div>
              <div class="col-sm-10">
                 <p style="margin-top: 10px; text-align: justify;"><?php echo ucfirst($ticket_reply->message); ?></p>

                 <hr  style="border-top: dotted 1px; width: 200px; float: left; margin-top: 0px">

                 <?php if($ticket_reply->file): ?>
                     <a href="<?php echo e(url('public/uploads/ticketFile').'/'.$ticket_reply->file->filename); ?>" class="pull-right"><i class="fa fa-fw fa-download"></i><?php echo e($ticket_reply->file->originalname); ?></a>
                 <?php endif; ?>
              </div>
              <div class="col-sm-1">
                <span class="btn btn-xs btn-primary pull-right edit-btn" data-id="<?php echo e($ticket_reply->id); ?>" data-message="<?php echo e($ticket_reply->message); ?>" data-toggle="modal" data-target="#modal-default"><i class="glyphicon glyphicon-edit"></i></span>
              </div>
            </div>
            <div class="box-footer">
                
                <span><i class="fa fa-fw fa-clock-o"></i><small><i><?php echo e(dateFormat($ticket_reply->created_at)); ?></i></small></span>
            </div>
        </div>
    <?php else: ?>
      <!--  Show Admin Reply -->
      <div class="box">
          <div class="box-body" style="background-color: #F2F4F4">

              <div class="col-sm-1">
                <span style="margin-top: 15px;" class="btn btn-xs btn-primary btn-flat edit-btn" data-toggle="modal" data-id="<?php echo e($ticket_reply->id); ?>" data-message="<?php echo e($ticket_reply->message); ?>" data-target="#modal-default" ><i class="glyphicon glyphicon-edit"></i></span>
              </div>

              <div class="col-sm-10">
                  <p style="margin-top: 10px; text-align: justify;"> <?php echo ucfirst($ticket_reply->message); ?> </p>
                  <hr style="border-top: dotted 1px; width: 200px; float: left; margin-top: 0px">

                  <?php if($ticket_reply->file): ?>
                      <a href="<?php echo e(url('public/uploads/ticketFile').'/'.$ticket_reply->file->filename); ?>" class="pull-right"><i class="fa fa-fw fa-download"></i><?php echo e($ticket_reply->file->originalname); ?></a>
                  <?php endif; ?>
              </div>


              <div class="col-sm-1" style="text-align: center;">
                  <h5><a href="<?php echo e(url('admin/admin-user/edit/'. $ticket_reply->admin->id)); ?>"><?php echo e(($ticket_reply->admin->first_name.' '.$ticket_reply->admin->last_name)); ?></a></h5>

                  <?php if(!empty($ticket_reply->admin->picture)): ?>
                    <img alt="Admin profile picture" src="<?php echo e(url('public/uploads/userPic/'.$ticket_reply->admin->picture)); ?>" class=" img-responsive img-circle asa">
                  <?php else: ?>
                    <img alt="Default picture" src='<?php echo e(url("public/uploads/userPic/default-image.png")); ?>' class="img-responsive img-circle asa">
                  <?php endif; ?>

                  <hr style="margin: 5px 0px;">

                      <form action="<?php echo e(url('admin/tickets/reply/delete')); ?>" accept-charset="UTF-8" method="POST" style="display:inline" >
                      <?php echo e(csrf_field()); ?>


                          <input type="hidden" name="id" value="<?php echo e($ticket_reply->id); ?>">
                          <input type="hidden" name="ticket_id" value="<?php echo e($ticket_reply->ticket_id); ?>">

                          <button class="btn btn-xs btn-danger btn-flat" data-message="Are you sure you want to delete this reply?" data-target="#confirmDelete" data-title="Delete Reply" data-toggle="modal" title="Delete" type="button" id="admin_reply_button">Delete</button>

                      </form>
              </div>
          </div>

          <div class="box-footer">
              
              <span class="pull-right"><i class="fa fa-fw fa-clock-o"></i><small><i><?php echo e(dateFormat($ticket_reply->created_at)); ?></i></small></span>
          </div>
      </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Modal Start -->
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">

        <form  method="POST" action="<?php echo e(url('admin/tickets/reply/update')); ?>" id="replyModal">
            <?php echo e(csrf_field()); ?>


            <input type="hidden" name="id" id="reply_id">

            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Reply</h4>
              </div>

              <div class="modal-body">
                <div class="form-group">

                  <div class="modal_editor_textarea">
                      <textarea name="message" class="form-control editor" style="height: 200px"></textarea>
                  </div>

                  <div id="error-message-modal"></div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-flat pull-left" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-flat">Update</button>
              </div>
            </div>
        </form>

    </div>
</div>
<!-- /.Modal End -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<!-- jquery.validate additional-methods -->
<script src="<?php echo e(asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js')); ?>" type="text/javascript"></script>

<script src="<?php echo e(asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">

    $(function () {
        $(".select2").select2({
        });
    });

    $(function () {
        $('.message').wysihtml5({
            events: {
                change: function () {
                    if($('.message').val().length === 0 )
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
    });

    $(function () {
        $('.editor').wysihtml5({
            events: {
                change: function () {
                    if($('.editor').val().length === 0 )
                    {
                        $('#error-message-modal').addClass('error').html('This field is required.').css("font-weight", "bold");
                    }
                    else
                    {
                        $('#error-message-modal').html('');
                    }
                }
            }
        });
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
            }
            else if (element.prop('id') === 'editor')
            {
                $('#error-message-modal').html(error);
            }
            else {
                error.insertAfter(element);
            }
        }
    });

    $('#reply_form').validate({
        ignore: ":hidden:not(textarea)",
        rules: {
            message: "required",
            file: {
                extension: "docx|rtf|doc|pdf|png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
          file: {
            extension: "Please select (docx, rtf, doc, pdf, png, jpg, jpeg, gif or bmp) file!"
          },
        },
        submitHandler: function(form)
        {
            $("#reply").attr("disabled", true);
            $(".fa-spin").show();
            $("#reply_text").text('Replying...');

            $("#customer_reply_button").attr("disabled", true);
            $("#admin_reply_button").attr("disabled", true);
            $(".edit-btn").attr("disabled", true);

            $('#customer_reply_button').click(false);
            $('#admin_reply_button').click(false);
            $('.edit-btn').click(false);
            form.submit();
        }
    });

    $('#replyModal').validate({
        rules: {
            message:{
               required: true,
            }
        }
    });


    $( document ).ready(function(e)
    {
        $(".edit-btn").on('click', function()
        {
            var  id     = $(this).attr('data-id');
            var message = $(this).attr('data-message');
            if (message) {
              $('#replyModal iframe').contents().find('.wysihtml5-editor').html(message);
            }
            $("#reply_id").val(id);
        });
    });

    $(document).on('input','#status_ticket',function()
    {
        var status_id = $("#status_ticket").val();
        var ticket_id = "<?php echo e($ticket->id); ?>";
        var token = "<?php echo csrf_token(); ?>";

        var url   = "<?php echo e(url('admin/tickets/change_ticket_status')); ?>";

        $.ajax({
            url:url,
            method:"POST",
            data:{
                'status_id':status_id,
                'ticket_id':ticket_id,
                '_token':token
            },
            dataType:"json",
            success:function(data)
            {
               if(data.status == '1' )
               {
                 console.log(data.message);
                 $('#status_label').html(data.message);
               }
            }
        });
    });
</script>

<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/tickets/reply.blade.php ENDPATH**/ ?>