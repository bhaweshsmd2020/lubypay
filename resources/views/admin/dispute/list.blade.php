@extends('admin.layouts.master')
@section('title', 'Disputes')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
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
            <form class="form-horizontal" action="{{ url('admin/disputes') }}" method="GET" id='transaction_form'>

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">

                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">

                <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

                <input id="transaction_id" type="hidden" name="transaction_id" value="{{ isset($transaction) ? $transaction : '' }}">

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
                                    <option value="all" {{ ($status =='all') ? 'selected' : '' }} >All</option>
                                    @foreach($dispute_status as $dispute)
                                      <option value="{{ $dispute->status }}" {{ ($dispute->status == $status) ? 'selected' : '' }}>
                                        {{
                                            (
                                                ($dispute->status == 'Reject') ? "Rejected" :
                                                (
                                                    ($dispute->status == 'Solve') ? "Solved" : $dispute->status
                                                )
                                            )
                                        }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3" style="margin-left: 5px;">
                                <label>User</label>
                                <input id="user_input" type="text" name="user" placeholder="Enter Name" class="form-control" value="{{ empty($user) ?  $user : $getName->first_name.' '.$getName->last_name }}"
                                    {{  isset($getName) && ($getName->id == $user) ? 'selected' : '' }}>
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
    
    @if ($summary)
        <div class="box">
            <div class="box-body">
                <div class="row">
                    @foreach($summary as $data)
                        <div class="col-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-body text-center">
                                    <span style="font-size: 20px;">{{ $data->total_status }}</span><br>
                                      @if($data->status =='Open')
                                        <span class="text-primary" style="font-weight: bold;">Open</span>
                                      @elseif($data->status =='Solve')
                                        <span class="text-success" style="font-weight: bold;">Solved</span>
                                      @elseif($data->status =='Close')
                                        <span class="text-danger" style="font-weight: bold;">Closed</span>
                                      @endif
                                </div>
                             </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    
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
                                @foreach($disputes as $k=>$dispute)
                                    <tr>
                                        <td>{{++$k}}</td>
                                        <td>{{dateFormat($dispute->created_at)}}</td>
                                        <td>{{$ticket->code}}</td>
                                        <td>{{$ticket->title}}</td>
                                        <td>{{isset($dispute->claimant) ? $dispute->claimant->first_name .' '.$dispute->claimant->last_name :"-"}}</td>
                                        <td>{{isset($dispute->defendant) ? $dispute->defendant->first_name .' '.$dispute->defendant->last_name :"-"}}</td>
                                        <td>
                                            {{isset($dispute->transaction) ? $dispute->transaction->uuid :"-"}}
                                            
                                            <?php
                                                $transactionUuid = isset($dispute->transaction) ? $dispute->transaction->uuid :"-";

                                                $transactionUuidWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_dispute')) ?
                                                '<a href="' . url('admin/transactions/edit/' . $dispute->transaction->id) . '" target="_blank">'.$transactionUuid.'</a>' : $transactionUuid;
                                            ?>
                                            {{$transactionUuidWithLink}}
                                        </td>
                                        <td>
                                            @if($dispute->status == 'Open')
                                                <span class="label label-success">Open</span>
                                            @elseif($dispute->status == 'Solve')
                                                <span class="label label-success">Solved</span>
                                            @elseif($dispute->status == 'Close')
                                                <span class="label label-danger">Closed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
@endsection 

@push('extra_body_scripts') 
    <script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    
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
            var sessionDate      = '{{ Session::get('date_format_type') }}';
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
    
            var startDate = "{!! $from !!}";
            var endDate   = "{!! $to !!}";
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
                            url:'{{url('admin/ticket_user_search')}}',
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
@endpush

