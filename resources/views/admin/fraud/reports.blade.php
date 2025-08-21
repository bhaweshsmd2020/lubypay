@extends('admin.layouts.master')
@section('title', 'Reports')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Reports</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" action="{{ url('admin/fraud-reports') }}" method="GET">

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
                <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-3">
                                <label>Date Range</label><br>
                                <button type="button" class="btn btn-default" id="daterange-btn" >
                                    <span id="drp">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <div class="col-md-3">
                                <label for="transaction_type">Type</label><br>
                                <select class="form-control select2" name="type" id="type">
                                    <option value="all" <?php if($type == 'all'){ echo 'selected'; } ?> >All</option>
                                    <option value="1" <?php if($type == '1'){ echo 'selected'; } ?> >Transactions/Hour</option>
                                    <option value="2" <?php if($type == '2'){ echo 'selected'; } ?> >Transactions/Day</option>
                                    <option value="3" <?php if($type == '3'){ echo 'selected'; } ?> >Amount/Hour</option>
                                    <option value="4" <?php if($type == '4'){ echo 'selected'; } ?> >Amount/Day</option>
                                    <option value="5" <?php if($type == '5'){ echo 'selected'; } ?> >Amount/Week</option>
                                    <option value="6" <?php if($type == '6'){ echo 'selected'; } ?> >Amount/Month</option>
                                    <option value="7" <?php if($type == '7'){ echo 'selected'; } ?> >Same Amount/Hour</option>
                                    <option value="8" <?php if($type == '8'){ echo 'selected'; } ?> >Transactions/Email/Phone/Day</option>
                                    <option value="9" <?php if($type == '9'){ echo 'selected'; } ?> >Transactions/IP Address/Day</option>
                                    <option value="9" <?php if($type == '10'){ echo 'selected'; } ?> >New User Account(Days)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="transaction_type">Transaction Type</label><br>
                                <select class="form-control select2" name="trans_type" id="trans_type">
                                    <option value="all" {{ ($trans_type =='all') ? 'selected' : '' }} >All</option>
                                    @if(!empty($transactionTypes))
                                    @foreach($transactionTypes as $value)
                                    <option value="{{ $value->id }}" {{ ($value->id == $trans_type) ? 'selected' : '' }}>
                                        {{ ($value->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $value->name) }}
                                    </option>
                                    @endforeach
                                    @endif
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
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>End User</th>
                                    <th>Transaction Type</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($frauds as $index=>$report)
                                    <?php
                                        if(!empty($report->transactions_hour)){
                                            $title = 'Transactions/Hour';
                                        }elseif(!empty($report->transactions_day)){
                                            $title = 'Transactions/Day';
                                        }elseif(!empty($report->amount_hour)){
                                            $title = 'Amount/Hour';
                                        }elseif(!empty($report->amount_day)){
                                            $title = 'Amount/Day';
                                        }elseif(!empty($report->amount_week)){
                                            $title = 'Amount/Week';
                                        }elseif(!empty($report->amount_month)){
                                            $title = 'Amount/Month';
                                        }elseif(!empty($report->same_amount)){
                                            $title = 'Same Amount/Hour';
                                        }elseif(!empty($report->email_day)){
                                            $title = 'Transactions/Email/Phone/Day';
                                        }elseif(!empty($report->ipadd_day)){
                                            $title = 'Transactions/IP Address/Day';
                                        }elseif(!empty($report->user_created_at)){
                                            $title = 'New User Account(Days)';
                                        }
                                        
                                        $username = DB::table('users')->where('id', $report->user_id)->first();
                                        if(!empty($username)){
                                            $userna = $username->first_name.' '.$username->last_name;
                                        }else{
                                            $userna = '-';
                                        }
                                        
                                        $trans = DB::table('transaction_types')->where('id', $report->trans_type)->first();
                                        
                                        $currency = DB::table('currencies')->where('id', $report->currency_id)->first();
                                    ?>
                                    
                                    <tr>
                                        <td>{{++$index}}</td>
                                        <td>{{$title}}</td>
                                        <td>{{$userna}}</td>
                                        <td>{{$report->end_user_id}}</td>
                                        <td>{{$trans->name}}</td>
                                        <td>{{number_format($report->amount, 2, '.', ',')}}</td>
                                        <td>{{$currency->code}}</td>
                                        <td>{{ Carbon\Carbon::parse($report->created_at)->format('d-M-Y h:i A') }}</td>
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