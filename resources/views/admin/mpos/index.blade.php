@extends('admin.layouts.master')
@section('title', 'Transactions')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <style>
        #ui-id-1{
            background: #fff;
            list-style: none;
            padding: 5px;
        }
        
        #ui-id-1 .ui-menu-item:hover{
            background: #000;
            color: #fff;
            cursor: pointer;
            padding: 5px;
            width: 100%;
        }
    </style>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Transactions</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" action="{{ url('admin/mpos') }}" method="GET" id='transaction_form'>

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
                <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Date and time range -->
                            <div class="col-md-3">
                                <label>Date Range</label>
                                <button type="button" class="btn btn-default" id="daterange-btn" >
                                    <span id="drp">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <!-- Currency -->
                            <div class="col-md-2">
                                <label for="currency">Currency</label>
                                <select class="form-control select2" name="currency" id="currency">
                                    <option value="all" {{ ($currency =='all') ? 'selected' : '' }} >All</option>
                                    @foreach($t_currency as $transaction)
                                        <?php
                                            $curr = DB::table('currencies')->where('id', $transaction->currency_id)->first();
                                        ?>
                                        <option value="{{ $transaction->currency_id }}" {{ ($transaction->currency_id == $currency) ? 'selected' : '' }}>
                                            {{$curr->code}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="status">Status</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="all" {{ ($status =='all') ? 'selected' : '' }} >All</option>
                                    @foreach($t_status as $t)
                                        <option value="{{ $t->status }}" {{ ($t->status == $status) ? 'selected' : '' }}>
                                            {{
                                                (
                                                    ($t->status == 'Blocked') ? "Cancelled" :
                                                    (
                                                        ($t->status == 'Refund') ? "Refunded" : $t->status
                                                    )
                                                )
                                            }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="transaction_type">Type</label>
                                <select class="form-control select2" name="type" id="type">
                                    <option value="all" {{ ($type =='all') ? 'selected' : '' }} >All</option>
                                    @foreach($t_type as $ttype)
                                    <option value="{{ $ttype->transaction_type->id }}" {{ ($ttype->transaction_type->id == $type) ? 'selected' : '' }}>
                                        {{ ($ttype->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $ttype->transaction_type->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>User</label>
                                <input id="user_input" type="text" name="user" placeholder="Enter Name" class="form-control" value="{{ empty($user) ?  $user : $getName->first_name.' '.$getName->last_name }}"
                                {{  isset($getName) && ($getName->id == $user) ? 'selected' : '' }}>
                                <span id="error-user"></span>
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
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Transaction Fee</th>
                                    <th>Total</th>
                                    <th>Store Fee</th>
                                    <th>Currency</th>
                                    <th>Type</th>
                                    <th>Receiver</th>
                                    <th>Status</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index=>$transaction)
                                    <?php
                                        $senderWithLink = '-';
                                        switch ($transaction->transaction_type_id)
                                        {
                                            case Deposit:
                                            case Transferred:
                                            case Exchange_From:
                                            case Exchange_To:
                                            case Request_From:
                                            case Withdrawal:
                                            case Payment_Sent:
                                            case Crypto_Sent:
                                            case Recharge:
                                            case 32:
                                            case Cable:
                                                if (!empty($transaction->user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                            case Gas_Bill:
                                                if (!empty($transaction->user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                            case Water_Bill:
                                                if (!empty($transaction->user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                            case Internet_Bill:
                                                if (!empty($transaction->user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                            case Electricity_Bill:
                                                if (!empty($transaction->user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                            case Insurance:
                                                if (!empty($transaction->user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                            case Received:
                                            case Request_To:
                                            case Payment_Received:
                                            case 34:
                                            case 35:
                                            case Crypto_Received:
                                                if (!empty($transaction->end_user))
                                                {
                                                    $sender = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                    $senderWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url('admin/users/edit/' . $transaction->end_user_id) . '">'.$sender.'</a>' : $sender;
                                                }
                                                else
                                                {
                                                    $sender = '-';
                                                }
                                                break;
                                        }
                                        
                                        $receiverWithLink = '-';
                                        switch ($transaction->transaction_type_id)
                                        {
                                             case Recharge:
                                                 if (!empty($transaction->end_user))
                                                {
                                                    $receiver = $transaction->end_user->first_name.' '.$transaction->end_user->last_name;
                                                }
                                                else
                                                {
                                                    $receiver = '-------';
                                                }
                                                break;
                                            case Deposit:
                                            case Exchange_From:
                                            case Exchange_To:
                                            case Withdrawal:
                                            case Payment_Sent:
                                            case Crypto_Sent:
                                           
                                                if (!empty($transaction->end_user))
                                                {
                                                    $receiver = $transaction->end_user->first_name.' '.$transaction->end_user->last_name;
                                                }
                                                else
                                                {
                                                    $receiver = '-';
                                                }
                                                break;
                                            case Transferred:
                                                if (!empty($transaction->end_user))
                                                {
                                                    $receiver = $transaction->end_user->first_name.' '.$transaction->end_user->last_name;
                                                }
                                                else
                                                {
                                                    if (!empty($transaction->transfer->email))
                                                    {
                                                        $receiver = $transaction->transfer->email;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    elseif (!empty($transaction->transfer->phone))
                                                    {
                                                        $receiver         = $transaction->transfer->phone;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    else
                                                    {
                                                        $receiver = '-';
                                                    }
                                                }
                                                break;
                                            case Received:
                                                if (!empty($transaction->user))
                                                {
                                                    $receiver = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    if (!empty($transaction->transfer->email))
                                                    {
                                                        $receiver = $transaction->transfer->email;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    elseif (!empty($transaction->transfer->phone))
                                                    {
                                                        $receiver         = $transaction->transfer->phone;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    else
                                                    {
                                                        $receiver = '-';
                                                    }
                                                }
                                                break;
                                            case Request_From:
                                                if (!empty($transaction->end_user))
                                                {
                                                    $receiver = $transaction->end_user->first_name.' '.$transaction->end_user->last_name;
                                                }
                                                else
                                                {
                                                    if (!empty($transaction->request_payment->email))
                                                    {
                                                        $receiver = $transaction->request_payment->email;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    elseif (!empty($transaction->request_payment->phone))
                                                    {
                                                        $receiver         = $transaction->request_payment->phone;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    else
                                                    {
                                                        $receiver         = '-';
                                                    }
                                                }
                                                break;
                                            case Request_To:
                                                if (!empty($transaction->user))
                                                {
                                                    $receiver = $transaction->end_user->first_name.' '.$transaction->end_user->last_name;
                                                }
                                                else
                                                {
                                                    if (!empty($transaction->request_payment->email))
                                                    {
                                                        $receiver = $transaction->request_payment->email;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    elseif (!empty($transaction->request_payment->phone))
                                                    {
                                                        $receiver         = $transaction->request_payment->phone;
                                                        $receiverWithLink = $receiver;
                                                    }
                                                    else
                                                    {
                                                        $receiver         = '-';
                                                    }
                                                }
                                                break;
                                            case Payment_Received:
                                            case 34:
                                            case 35:
                                            case Crypto_Received:
                                                if (!empty($transaction->user))
                                                {
                                                    $receiver = $transaction->user->first_name.' '.$transaction->user->last_name;
                                                }
                                                else
                                                {
                                                    $receiver = '-';
                                                }
                                                break;
                                        }
                                        
                                        if(!empty($transaction->local_tran_time)){
                                            $local_time = $transaction->local_tran_time;
                                        }else{
                                            $local_time = $transaction->created_at;
                                        }
                                    ?>
                                    
                                    @if($transaction->transaction_type_id != '2' || ($transaction->transaction_type_id == '2' && $transaction->pay_type == '2'))
                                        <tr>
                                            <td>{{++$index}}</td>
                                            <td>{{$transaction->uuid}}</td>
                                            <td>{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}</td>
                                            <td><a href="{{ url('admin/users/edit/' . $transaction->user_id) }}">{{$sender}}</a></td>
                                            <td>{{$transaction->currency->type != 'fiat' ? $transaction->subtotal : formatNumber($transaction->subtotal)}}</td>
                                            <td>{{($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0) ? '-' : ($transaction->currency->type != 'fiat' ? $transaction->charge_fixed : formatNumber($transaction->charge_percentage + $transaction->charge_fixed))}}</td>
                                            <td>{{$transaction->total}}</td>
                                            <td>{{$transaction->store_fee}}</td>
                                            <td>{{$transaction->currency->code}}</td>
                                            <td>
                                                @if($transaction->is_card == '1')
                                                    Virtual Card
                                                @else
                                                    {{ ($transaction->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $transaction->transaction_type->name)}}
                                                @endif
                                            </td>
                                            <td><a href="{{ url('admin/users/edit/' . $transaction->end_user_id)}}">{{$receiver}}</a></td>
                                            <td>
                                                @if ($transaction->status == 'Success')
                                                    <span class="label label-success">Success</span>
                                                @elseif ($transaction->status == 'Pending')
                                                    <span class="label label-primary">Pending</span>
                                                @elseif ($transaction->status == 'Refund')
                                                    <span class="label label-warning">Refunded</span>
                                                @elseif ($transaction->status == 'Blocked')
                                                    <span class="label label-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_mpos_transactions'))
                                                    <a href="{{ url('admin/transactions/edit/' . $transaction->id)}}" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
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
    <!--<script src="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.js') }}" type="text/javascript"></script>-->
    
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