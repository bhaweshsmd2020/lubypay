@extends('admin.layouts.master')
@section('title', 'Reports')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <style>
        @media screen and (min-width: 992px) {
            .col-md-13 {
                width: 20%;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                float: left;
            }
        }
    </style>
    
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
            <form class="form-horizontal" action="{{ url('admin/report') }}" method="GET">

              <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
              <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
              <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

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

                          <!-- Currency -->
                          <div class="col-md-2">
                              <label for="currency">Currency</label>
                              <select class="form-control select2" name="currency" id="currency">
                                  <option value="all" {{ ($currency =='all') ? 'selected' : '' }} >All</option>
                                  @foreach($revenues_currency as $revenue)
                                      <option value="{{ $revenue->currency_id }}" {{ ($revenue->currency_id == $currency) ? 'selected' : '' }}>
                                          {{ $revenue->currency->code }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                          
                            <div class="col-md-2">
                                <label for="status">Transaction Type</label>
                                <select class="form-control select2" name="type" id="type">
                                    <option value="all" {{ ($type =='all') ? 'selected' : '' }} >All</option>
                                    @foreach($revenues_type as $revenue)
                                        <option value="{{ $revenue->transaction_type_id }}" {{ ($revenue->transaction_type_id == $type) ? 'selected' : '' }}>
                                            {{ ($revenue->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $revenue->transaction_type->name) }}
                                        </option>
                                    @endforeach
                                </select>
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

    @if(!empty($currency_info))
        <div class="box">
          <div class="box-body">
              <div class="row">
                @forelse ($currency_info as $index=>$revenue)
    
                    @if ($revenue > 0)
                      <div class="col-md-13">
                         <div class="panel panel-primary">
                              <div class="panel-body text-center" style="padding:5px; margin-bottom: 0;">
                                <span class="text-info" style="font-size: 15px">Total {{ $index }} Revenue</span>
                                <strong><h4>{{ moneyFormat($index , formatNumber($revenue)) }}</h4></strong>
                              </div>
                         </div>
                      </div>
                    @endif
    
                @empty
                  <h3 class="panel-title text-center">No Revenue Found!</h3>
                @endforelse
              </div>
          </div>
        </div>
    @endif
  
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="text-center">LubyPay Revenue</h4>
                    @foreach ($currency_info as $index=>$revenue)
                        <?php
                            $transactional = DB::table('settings')->where('id', '37')->first(); 
                            $transactional_revenue = ($transactional->value*$revenue)/100;
                        ?>
                        <div style="min-height: 45px; background: #122d83; border-radius: 5px; padding: 7px 18px; color: white; font-size: 23px; margin-bottom: 15px;">
                            <div style="width:60%;float: left;">
                                <div style="min-height: 25px;">{{ $index }}</div><div class="clearfix"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div style="width:40%;float: left;text-align: right;">
                                {{ formatNumber($transactional_revenue) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="col-md-4">
                    <h4 class="text-center">Platform Fees</h4>
                    @foreach ($currency_info as $index=>$revenue)
                        <?php
                            $transactional = DB::table('settings')->where('id', '38')->first(); 
                            $transactional_revenue = ($transactional->value*$revenue)/100;
                        ?>
                        <div style="min-height: 45px; background: #122d83; border-radius: 5px; padding: 7px 18px; color: white; font-size: 23px; margin-bottom: 15px;">
                            <div style="width:60%;float: left;">
                                <div style="min-height: 25px;">{{ $index }}</div><div class="clearfix"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div style="width:40%;float: left;text-align: right;">
                                {{ formatNumber($transactional_revenue) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="col-md-4">
                    <h4 class="text-center">Account Maintainance Fees</h4>
                    @foreach ($currency_info as $index=>$revenue)
                        <?php
                            $transactional = DB::table('settings')->where('id', '39')->first(); 
                            $transactional_revenue = ($transactional->value*$revenue)/100;
                        ?>
                        <div style="min-height: 45px; background: #122d83; border-radius: 5px; padding: 7px 18px; color: white; font-size: 23px; margin-bottom: 15px;">
                            <div style="width:60%;float: left;">
                                <div style="min-height: 25px;">{{ $index }}</div><div class="clearfix"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div style="width:40%;float: left;text-align: right;">
                                {{ formatNumber($transactional_revenue) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive" style="overflow: scroll;">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Transaction Type</th>
                                    <th>Percentage Charge</th>
                                    <th>Fixed Charge</th>
                                    <th>Total</th>
                                    <th>Currency</th>
                                    <th>LubyPay Revenue</th>
                                    <th>Platform Fee</th>
                                    <th>Account Maintenance Fee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $index=>$report)
                                    <?php
                                        $senders = DB::table('users')->whereId($report->user_id)->first();
                                        
                                        if(!empty($report->local_tran_time)){
                                            $local_time = $report->local_tran_time;
                                        }else{
                                            $local_time = $report->created_at;
                                        }
                                        
                                        $new_transaction1 = DB::table('revenues')->where('transaction_id', $report->id)->first();
                                        if($new_transaction1){
                                            $transactional_revenue = $new_transaction1->transactional;
                                        }else{
                                            $transactional = DB::table('settings')->where('id', '37')->first();
                                            $revenue = ($report->charge_percentage + $report->charge_fixed); 
                                            $transactional_revenue = ($transactional->value*$revenue)/100;
                                        }
                                        
                                        $new_transaction2 = DB::table('revenues')->where('transaction_id', $report->id)->first();
                                        if($new_transaction2){
                                            $operational_revenue = $new_transaction2->operational;
                                        }else{
                                            $operational   = DB::table('settings')->where('id', '38')->first();
                                            $revenue = ($report->charge_percentage + $report->charge_fixed); 
                                            $operational_revenue = ($operational->value*$revenue)/100;
                                        }
                                        
                                        $new_transaction3 = DB::table('revenues')->where('transaction_id', $report->id)->first();
                                        if($new_transaction3){
                                            $operationala_revenue = $new_transaction3->operational_a;
                                        }else{
                                            $operational   = DB::table('settings')->where('id', '38')->first();
                                            $operational_a   = DB::table('settings')->where('id', '39')->first();
                                            
                                            $revenue = ($report->charge_percentage + $report->charge_fixed); 
                                            $operational_revenue = ($operational->value*$revenue)/100;
                                            $operationala_revenue = ($operational_a->value*$revenue)/100;
                                        }
                                    ?>
                                    <tr>
                                        <td>{{++$index}}</td>
                                        <td>{{$report->uuid}}</td>
                                        <td>{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}</td>
                                        <td><a href="{{ url('admin/users/edit/' . $senders->id) }}">{{$senders->first_name.' '.$senders->last_name}}</a></td>
                                        <td>{{($report->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $report->transaction_type->name)}}</td>
                                        <td>{{($report->charge_percentage == 0) ?  '-' : formatNumber($report->charge_percentage)}}</td>
                                        <td>{{($report->charge_fixed == 0) ?  '-' : formatNumber($report->charge_fixed)}}</td>
                                        <td>{{($report->charge_percentage == 0) && ($report->charge_fixed == 0) ? '-' : $report->charge_percentage + $report->charge_fixed}}</td>
                                        <td>{{$report->currency->code}}</td>
                                        <td>{{$transactional_revenue}}</td>
                                        <td>{{$operational_revenue}}</td>
                                        <td>{{$operationala_revenue}}</td>
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