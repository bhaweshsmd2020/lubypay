
<?php $__env->startSection('title', 'Disputes'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Disputes</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" action="<?php echo e(url('admin/disputes')); ?>" method="GET" id='transaction_form'>

                <input id="startfrom" type="hidden" name="from" value="<?php echo e(isset($from) ? $from : ''); ?>">

                <input id="endto" type="hidden" name="to" value="<?php echo e(isset($to) ? $to : ''); ?>">

                <input id="user_id" type="hidden" name="user_id" value="<?php echo e(isset($user) ? $user : ''); ?>">

                <input id="transaction_id" type="hidden" name="transaction_id" value="<?php echo e(isset($transaction) ? $transaction : ''); ?>">

                <div class="row">
                    <div class="col-md-11">
                        <div class="row">
                            <!-- Date and time range -->
                            <div class="col-md-3" style="margin-right: 5px;">
                                <label>Date Range</label>
                                <button type="button" class="btn btn-default" id="daterange-btn" >
                                    <span id="drp">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <div class="col-md-3"  style="margin-left: 5px;">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="all" <?php echo e(($status =='all') ? 'selected' : ''); ?> >All</option>
                                    <?php $__currentLoopData = $dispute_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dispute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <option value="<?php echo e($dispute->status); ?>" <?php echo e(($dispute->status == $status) ? 'selected' : ''); ?>>
                                        <?php echo e((
                                                ($dispute->status == 'Reject') ? "Rejected" :
                                                (
                                                    ($dispute->status == 'Solve') ? "Solved" : $dispute->status
                                                )
                                            )); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3" style="margin-left: 5px;">
                                <label>User</label>
                                <input id="user_input" type="text" name="user" placeholder="Enter Name" class="form-control" value="<?php echo e(empty($user) ?  $user : $getName->first_name.' '.$getName->last_name); ?>"
                                    <?php echo e(isset($getName) && ($getName->id == $user) ? 'selected' : ''); ?>>
                                <span id="error-user"></span>
                            </div>
                            <div class="col-md-2">
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
    
    <?php if($summary): ?>
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-body text-center">
                                    <span style="font-size: 20px;"><?php echo e($data->total_status); ?></span><br>
                                      <?php if($data->status =='Open'): ?>
                                        <span class="text-primary" style="font-weight: bold;">Open</span>
                                      <?php elseif($data->status =='Solve'): ?>
                                        <span class="text-success" style="font-weight: bold;">Solved</span>
                                      <?php elseif($data->status =='Close'): ?>
                                        <span class="text-danger" style="font-weight: bold;">Closed</span>
                                      <?php endif; ?>
                                </div>
                             </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
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
                                    <th>Dispute Id</th>
                                    <th>Title</th>
                                    <th>Claimant</th>
                                    <th>Defendant</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $disputes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$dispute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$k); ?></td>
                                        <td><?php echo e(dateFormat($dispute->created_at)); ?></td>
                                        <td><?php echo e($ticket->code); ?></td>
                                        <td><?php echo e($ticket->title); ?></td>
                                        <td><?php echo e(isset($dispute->claimant) ? $dispute->claimant->first_name .' '.$dispute->claimant->last_name :"-"); ?></td>
                                        <td><?php echo e(isset($dispute->defendant) ? $dispute->defendant->first_name .' '.$dispute->defendant->last_name :"-"); ?></td>
                                        <td>
                                            <?php echo e(isset($dispute->transaction) ? $dispute->transaction->uuid :"-"); ?>

                                            
                                            <?php
                                                $transactionUuid = isset($dispute->transaction) ? $dispute->transaction->uuid :"-";

                                                $transactionUuidWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_dispute')) ?
                                                '<a href="' . url('admin/transactions/edit/' . $dispute->transaction->id) . '" target="_blank">'.$transactionUuid.'</a>' : $transactionUuid;
                                            ?>
                                            <?php echo e($transactionUuidWithLink); ?>

                                        </td>
                                        <td>
                                            <?php if($dispute->status == 'Open'): ?>
                                                <span class="label label-success">Open</span>
                                            <?php elseif($dispute->status == 'Solve'): ?>
                                                <span class="label label-success">Solved</span>
                                            <?php elseif($dispute->status == 'Close'): ?>
                                                <span class="label label-danger">Closed</span>
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
    		document.title='Disputes';
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


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/dispute/list.blade.php ENDPATH**/ ?>