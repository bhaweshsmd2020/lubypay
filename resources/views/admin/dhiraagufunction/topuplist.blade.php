@extends('admin.layouts.master')
@section('title', 'Mobile Reload')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Mobile Reload</div>
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
                                    <th>S. No</th>
                                    <th>Transaction ID</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Transaction Fee</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $k => $value)
                                    <?php
                                        if(!empty($value->local_tran_time)){
                                            $local_time = $value->local_tran_time;
                                        }else{
                                            $local_time = $value->created_at;
                                        }
                                    ?>
                                    <tr>
                                        <td>{{++$k}}</td>
                                        <td>{{$value->uuid}}</td>
                                        <td>{{App\Models\User::find($value->user_id)->first_name??'NA'}}</td>
                                        <td>{{$value->phone}}</td>
                                        <td>{{App\Models\Currency::find($value->currency_id)->code??'NA'}}</td>
                                        <td>{{$value->subtotal}}</td>
                                        <td>{{$value->charge_fixed}}</td>
                                        <td>{{$value->total}}</td>
                                        <td>{{$value->status}}</td>
                                        <td>{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}</td>
                                        <td>
                                            <a href="{{ url('admin/edit-topup-list/' . $value->id) }}" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
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