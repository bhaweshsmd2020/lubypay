
<?php $__env->startSection('title', 'Identity Verification'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <style>
        #example_wrapper{
            overflow: scroll;
        }
    </style>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Identity Verification</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" action="<?php echo e(url('admin/identity-proofs')); ?>" method="GET">

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
                                    <span id="drp"><i class="fa fa-calendar"></i></span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <!-- Status -->
                            <div class="col-md-2">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="all" <?php echo e(($status =='all') ? 'selected' : ''); ?> >All</option>
                                    <?php $__currentLoopData = $documentVerificationStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documentVerification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($documentVerification->status); ?>" <?php echo e(($documentVerification->status == $status) ? 'selected' : ''); ?>>
                                            <?php echo e(ucfirst($documentVerification->status)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-1">
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
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>User Id</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Uploaded On</th>
                                    <th>Updated on</th>
                                    <th>Updated By</th>
                                    <th>User Location</th>
                                    <th>App Version</th>
                                    <th>User Device</th>
                                    <th>OS</th>
                                    <th>IP</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                    <?php
                                        $sender = isset($document->user) ? $document->user->first_name.' '.$document->user->last_name :"-";
                                        $user_id = DB::table('users')->where('id', $document->user_id)->first();
                                        
                                        if($document->country){
                                             $country=  DB::table('countries')->where('short_name',$document->country??'')->first()->name??'';
                                        }else{
                                            $country=$document->country??'';
                                        }
                                        
                                        $location = $document->city.' | '. $country;
                                        
                                        if(!empty($document->updated_by)){
                                            $admins=  DB::table('admins')->where('id', $document->updated_by)->first();
                                            if(!empty($admins)){
                                                $updated_by = $admins->first_name.' '.$admins->last_name;
                                            }else{
                                                $updated_by='-';
                                            }
                                        }else{
                                            $updated_by='-';
                                        }
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo e(++$k); ?></td>
                                        <td><?php echo e($user_id->carib_id); ?></td>
                                        <td><a href="<?php echo e(url('admin/users/edit/' . $document->user_id)); ?>"><?php echo e($sender); ?></a></td>
                                        <td>
                                            <?php if($document->status == 'approved'): ?>
                                                <span class="label label-success">Approved</span>
                                            <?php elseif($document->status == 'pending'): ?>
                                                <span class="label label-primary">Pending</span>
                                            <?php elseif($document->status == 'rejected'): ?>
                                                <span class="label label-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(Carbon\Carbon::parse($document->created_at)->format('d-M-Y h:i A')); ?></td>
                                        <td><?php echo e(Carbon\Carbon::parse($document->updated_at)->format('d-M-Y h:i A')); ?></td>
                                        <td><?php echo e($updated_by); ?></td>
                                        <td><?php echo e($location); ?></td>
                                        <td><?php echo e($document->app_ver); ?></td>
                                        <td><?php echo e($document->device_manufacture.' | '.$document->device_name.' | '.$document->device_model); ?></td>
                                        <td><?php echo e($document->device_os.' '.$document->os_ver); ?></td>
                                        <td><?php echo e($document->ip_address); ?></td>
                                        <td class=" dt-center">
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_identity_verfication')): ?>
                                                <a href="<?php echo e(url('admin/identity-proofs/edit/' . $document->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>
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


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubynet/public_html/lubypaynew/resources/views/admin/verifications/identity_proofs/list.blade.php ENDPATH**/ ?>