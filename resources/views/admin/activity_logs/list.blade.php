@extends('admin.layouts.master')
@section('title', 'Activity Logs')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Activity Logs</div>
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
                                    <th>Date</th>
                                    <th>User Type</th>
                                    <th>Username</th>
                                    <th>IP Address</th>
                                    <th>City|Country</th>
                                    <th>Browser/Platform</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $index=>$activityLog)
                                    <tr>
                                        <td>{{++$index}}</td>
                                        <td>{{dateFormat($activityLog->created_at)}}</td>
                                        <td>
                                            @if($activityLog->type == 'User')
                                                Customer
                                            @else
                                                {{$activityLog->type}}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($activityLog->type == 'Admin')
                                                <?php
                                                    $admin = $activityLog->admin->first_name.' '. $activityLog->admin->last_name;
                                                ?>
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_admin'))
                                                    <a href="{{ url('admin/admin-user/edit/' . $activityLog->admin->id) }}">{{$admin}}</a>
                                                @else
                                                    {{$admin}}
                                                @endif
                                            @else
                                                <?php
                                                    $user = $activityLog->user->first_name.' '. $activityLog->user->last_name;
                                                ?>
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user'))
                                                    <a href="{{ url('admin/users/edit/' . $activityLog->user->id) }}">{{$user}}</a>
                                                @else
                                                    {{$user}}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{$activityLog->ip_address}}</td>
                                        <td>
                                            {{$activityLog->city}}|{{$activityLog->country}}
                                        </td>
                                        <td>{{$activityLog->browser_agent}}</td>
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