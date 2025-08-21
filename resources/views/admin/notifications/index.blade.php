@extends('admin.layouts.master')
@section('title', 'Notifications')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Notifications</div>
                </div>
                <div class="col-md-2 pull-right">
                    <a href="{{ url('admin/notifications/update/all') }}" class="btn btn-success btn-flat notifications-warning"><span class="fa fa-bell"> &nbsp;</span>Read All</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ url('admin/notifications/read') }}" class="form-horizontal" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $index=>$notification)
                                        <?php
                                            $notification_type = DB::table('notification_types')->where('id', $notification->notification_type_id)->first();
                    
                                            if(!empty($notification->local_tran_time)){
                                                $local_time = $notification->local_tran_time;
                                            }else{
                                                $local_time = $notification->created_at;
                                            }
                                            
                                            if($notification->clicked == '1'){
                                                $color = '#000';
                                            }else{
                                                $color = '#122d83';
                                            }
                                        ?>
                                        
                                        <tr>
                                            <td>{{++$index}}</td>
                                            <td>{{$notification_type->name}}</td>
                                            <td>
                                                <form method="POST" action="{{ url('admin/notifications/update/'.$notification->id) }}" id="form_noti_{{ $notification->id }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="id" value="{{ $notification->id }}">
                                                </form>
                                                <a href="#" onclick="document.getElementById('form_noti_{{ $notification->id }}').submit();" style="color: {{$color}}">
                                                    {{ $notification->description }}
                                                </a>
                                            </td>
                                            <td style="width: 200px;">{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}</td>
                                            <td> 
                                                @if($notification->clicked == '0')
                                                    <input type="checkbox" id="checkItem" name="check[]" value="{{$notification->id}}"> </td>
                                                @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary btn-flat">Mark as Read</button>
                    </form>
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