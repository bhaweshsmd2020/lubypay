
<?php $__env->startSection('title', 'Help & Support'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Help & Support</div>
                </div>
                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_ticket')): ?>
                    <div class="col-md-4">
                        <a href="<?php echo e(url('admin/tickets/add')); ?>" class="btn btn-success btn-flat pull-right"><span class="fa fa-plus"> &nbsp;</span>Add Ticket</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" action="<?php echo e(url('admin/tickets/list')); ?>" method="GET">

                <input id="startfrom" type="hidden" name="from" value="<?php echo e(isset($from) ? $from : ''); ?>">

                <input id="endto" type="hidden" name="to" value="<?php echo e(isset($to) ? $to : ''); ?>">

                <input id="user_id" type="hidden" name="user_id" value="<?php echo e(isset($user) ? $user : ''); ?>">

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Date and time range -->
                            <div class="col-md-3">
                                <label>Date Range</label>
                                <button type="button" class="btn btn-default" id="daterange-btn">
                                    <span id="drp">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="all" <?php echo e(($status =='all') ? 'selected' : ''); ?> >All</option>
                                    <?php $__currentLoopData = $ticket_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <option value="<?php echo e($ticket->ticket_status->id); ?>" <?php echo e(($ticket->ticket_status->id == $status) ? 'selected' : ''); ?>>
                                        <?php echo e($ticket->ticket_status->name); ?>

                                      </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="user">User</label>
                                <input id="user_input" type="text" name="user" placeholder="Enter Name" class="form-control"
                                    value="<?php echo e(empty($user) ?  $user : $getName->first_name.' '.$getName->last_name); ?>"
                                    <?php echo e(isset($getName) && ($getName->id == $user) ? 'selected' : ''); ?>>
                                <span id="error-user"></span>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group" style="margin-top: 25px;">
                                   <button type="submit" name="btn" class="btn btn-primary btn-flat" id="btn">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
           <div class="row">
            <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    if($data->color){
                        $color = $data->color;
                    }else{
                        $color  = '008000';
                    }
                ?>

                <div class="col-md-2">
                    <div class="panel panel-primary">
                        <div class="panel-body text-center">
                            <span style="font-size: 20px;"><?php echo e($data->total_status); ?></span><br>
                            <span style="font-weight: bold; color: #<?php echo e($color); ?> ;"><?php echo e($data->name); ?></span>
                        </div>
                     </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Ticket Id</th>
                                    <th>User</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Last Reply</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$k); ?></td>
                                        <td><?php echo e(dateFormat($ticket->created_at)); ?></td>
                                        <td><a href="<?php echo e(url('admin/tickets/edit/' . $ticket->id)); ?>"><?php echo e($ticket->code); ?></a></td>
                                        <td>
                                            <?php if(!empty($ticket->user->first_name)): ?>
                                                <a href="<?php echo e(url('admin/users/edit/' . $ticket->user->id)); ?>"><?php echo e($ticket->user->first_name .' '.$ticket->user->last_name); ?></a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?php echo e(url('admin/tickets/edit/' . $ticket->id)); ?>"><?php echo e($ticket->subject); ?></a></td>
                                        <td>
                                            <?php if($ticket->ticket_status->name == 'Open'): ?>
                                                <span class="label label-success">Open</span>
                                            <?php elseif($ticket->ticket_status->name == 'In Progress'): ?>
                                                <span class="label label-primary">In Progress</span>
                                            <?php elseif($ticket->ticket_status->name == 'Hold'): ?>
                                                <span class="label label-warning">Hold</span>
                                            <?php elseif($ticket->ticket_status->name == 'Closed'): ?>
                                                <span class="label label-danger">Closed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($ticket->priority); ?></td>
                                        <td>
                                            <?php if(!empty($ticket->last_reply)): ?>
                                                <?php echo e(dateFormat($ticket->last_reply)); ?>

                                            <?php else: ?>
                                                No Reply Yet
                                            <?php endif; ?>
                                        </td>
                                        <td class=" dt-center">
                                            <?php
                                                $ticket_unread = DB::table('ticket_replies')->where('ticket_id',$ticket->id)->where('status',0)->get()->count();
                                                if($ticket_unread>0){
                                                    $ticket_unreads = '<span class="btn btn-xs btn-danger">'.$ticket_unread.'</span>';
                                                }else{
                                                    $ticket_unreads = "";
                                                }
                                                
                                                echo $ticket_unreads;
                                            ?>
                                            
                                            <a href="<?php echo e(url('admin/tickets/reply/' . $ticket->id)); ?>" class="label label-success"><i class="fa fa-reply"></i></a>&nbsp;
                                            
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_ticket')): ?>
                                                <a href="<?php echo e(url('admin/tickets/edit/' . $ticket->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                            <?php endif; ?>
                        
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_ticket')): ?>
                                                <a href="<?php echo e(url('admin/tickets/delete/' . $ticket->id)); ?>" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/pdfmake.min.js" ></script>
    <script type="text/javascript"  src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
    
    <script type="text/javascript">
    	$(document).ready(function() {
    		$('#example').DataTable(
    			{
    				"dom": '<"dt-buttons"Bf><"clear">lirtp',
    				"paging": true,
    				"autoWidth": true,
    				"buttons": [
    					'colvis',
    					'copyHtml5',
    	                'csvHtml5',
    					'excelHtml5',
    	                'pdfHtml5',
    					'print'
    				]
    			}
    		);
    	});
    </script>
<?php $__env->stopSection(); ?> 

<?php $__env->startPush('extra_body_scripts'); ?> 
    <script src="<?php echo e(asset('public/backend/bootstrap-daterangepicker/daterangepicker.js')); ?>" type="text/javascript"></script>
    
    <script type="text/javascript">
        $(".select2").select2({});
    
        var sDate;
        var eDate;
    
        //Date range as a button
        $('#daterange-btn').daterangepicker(
          {
            ranges   : {
              'Today'       : [moment(), moment()],
              'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month'  : [moment().startOf('month'), moment().endOf('month')],
              'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment()
          },
          function (start, end)
          {
            var sessionDate      = '<?php echo e(Session::get('date_format_type')); ?>';
            var sessionDateFinal = sessionDate.toUpperCase();
    
            sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#startfrom').val(sDate);
    
            eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#endto').val(eDate);
    
            $('#daterange-btn span').html('&nbsp;' + sDate + ' - ' + eDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
          }
        )
    
        $(document).ready(function()
        {
            $("#daterange-btn").mouseover(function() {
                $(this).css('background-color', 'white');
                $(this).css('border-color', 'grey !important');
            });
    
            var startDate = "<?php echo $from; ?>";
            var endDate   = "<?php echo $to; ?>";
            // alert(startDate);
            if (startDate == '') {
                $('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
            } else {
                $('#daterange-btn span').html(startDate + ' - ' + endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
            }
    
            $("#user_input").on('keyup keypress', function(e)
            {
                if (e.type=="keyup" || e.type=="keypress")
                {
                    var user_input = $('form').find("input[type='text']").val();
                    if(user_input.length === 0)
                    {
                        $('#user_id').val('');
                        $('#error-user').html('');
                        $('form').find("button[type='submit']").prop('disabled',false);
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
                                // console.log(req.term.length);
    
                                $('form').find("button[type='submit']").prop('disabled',true);
    
                                if(response.status == 'success')
                                {
                                    res($.map(response.data, function (item)
                                    {
                                            return {
                                                    id : item.user_id,
                                                    value: item.first_name + ' ' + item.last_name, //don't change value
                                                }
                                            }
                                        ));
                                }
                                else if(response.status == 'fail')
                                {
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
    
                    $('#error-user').html('');
    
                    $('#user_id').val(e.id);
    
                    // console.log(e.sender_id);
    
                    $('form').find("button[type='submit']").prop('disabled',false);
                },
                minLength: 0,
                autoFocus: true
            });
        });
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/tickets/list.blade.php ENDPATH**/ ?>