@extends('admin.layouts.master')
@section('title', 'Reloads')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Reloads</div>
                </div>
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
                                    <th>User</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Sub Total</th>
                                    <th>Fees</th>
                                    <th>Total</th>
                                    <th>Card Number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index=>$transaction)
                                    <?php
                                        if(!empty($transaction->local_tran_time)){
                                            $local_time = $transaction->local_tran_time;
                                        }else{
                                            $local_time = $transaction->created_at;
                                        }
                                        
                                        $user = DB::table('users')->where('id', $transaction->user_id)->first();
                                        $card = DB::table('cards')->where('id', $transaction->card_id)->first();
                                    ?>
                                    
                                    <tr>
                                        <td>{{++$index}}</td><td>
                                            @if(!empty($user))
                                                <a href="{{ url('admin/users/edit/' . $transaction->user_id) }}">{{$user->first_name}} {{$user->last_name}}</a>
                                            @else
                                                <a href="#">-</a>
                                            @endif
                                        </td>
                                        <td>{{$transaction->trx}}</td>
                                        <td>{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}</td>
                                        <td>USD {{number_format($transaction->sub_total, 2, '.', ',')}}</td>
                                        <td>USD {{number_format($transaction->fees, 2, '.', ',')}}</td>
                                        <td>USD {{number_format($transaction->total, 2, '.', ',')}}</td>
                                        <td>Ewallet</td>
                                        <td>{{$card->last_four}}</td>
                                        <td>
                                            @if ($transaction->status == 'paid')
                                                <span class="label label-success">Success</span>
                                            @else
                                                <span class="label label-primary">Pending</span>
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