@extends('admin.layouts.master')
@section('title', 'Card Holders')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Card Holders</div>
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
                                    <th>Name</th>
                                    <th>Card Type</th>
                                    <th>Request Status</th>
                                    <th>Card Status</th>
                                    <th>Applied From</th>
                                    <th>Card Last 4 Digit</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cards as $k=>$card)
                                    <?php
                                        $user = DB::table('users')->where('id', $card->user_id)->first();
                                        if(!empty($user)){
                                            $url = url('admin/users/edit/' . $user->id);
                                        }else{
                                            $url = '#';
                                        }
                                    ?>
                                    
                                    <tr>
                                        <td>{{++$k}}</td>
                                        <td><a href="{{ $url }}">{{$user->first_name}} {{$user->last_name}}</a></td>
                                        <td>{{$card->type}}</td>
                                        <td>
                                            @if($card->status == 'success')
                                                <span class="label label-success">Approved</span>
                                            @else
                                                <span class="label label-primary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($card->card_status == 'active')
                                                <span class="label label-success">Active</span>
                                            @else
                                                <span class="label label-primary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($card->applied_from == 'app')
                                                <span class="label label-success">App</span>
                                            @else
                                                <span class="label label-primary">Web</span>
                                            @endif
                                        </td>
                                        <td>{{ $card->last_four ?? '-' }}</td>
                                        <td>{{ Carbon\Carbon::parse($card->created_at)->format('d-M-Y h:i A') }}</td>
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