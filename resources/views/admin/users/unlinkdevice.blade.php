@extends('admin.layouts.master')
@section('title', 'Unlink Device')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Unlink Device</div>
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
                                    <th>Profile</th>
                                    <th>User Id</th>
                                    <th>User</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Request Date</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index=>$user)
                                    <?php
                                        if ($user->document_verification->count() > 0)
                                        {
                                            if ($user->status == 'Active') {
                                                $active = '<span class="label label-success">Active</span><br>';
                                            } else if ($user->status == 'Inactive') {
                                                $active = '<span class="label label-danger">Inactive</span><br>';
                                            } else if ($user->status == 'Suspended') {
                                                $active = '<span class="label label-warning">Suspended</span><br>';
                                            }
                                            
                                            if($user->address_verified == '1'){
                                                $address = '<span class="label label-info">Address Verified</span><br>';
                                            }else{
                                                $address = '';
                                            }
                                            
                                            if($user->identity_verified == '1'){
                                                $identity = '<span class="label label-primary">Identity Verified</span><br>';
                                            }else{
                                                $identity = '';
                                            }
                                            
                                            if($user->photo_verified == '1'){
                                                $photo = '<span class="label label-warning">Photo Verified</span><br>';
                                            }else{
                                                $photo = '';
                                            } 
                                            
                                            if($user->video_verified == '1'){
                                                $video = '<span class="label label-default">Video Verified</span>';
                                            }else{
                                                $video = '';
                                            }
                                            
                                            $status = $active.$address.$identity.$photo.$video;
                                        }
                                        else
                                        {
                                            if ($user->status == 'Active') {
                                                $status = '<span class="label label-success">Active</span>';
                                            } else if ($user->status == 'Inactive') {
                                                $status = '<span class="label label-danger">Inactive</span>';
                                            } else if ($user->status == 'Suspended') {
                                                $status = '<span class="label label-warning">Suspended</span>';
                                            }
                                        }
                                        
                                        $last_location = DB::table('users_login_location')->where('user_id',$user->id)->first();
                                        
                                        $user_log = DB::table('user_device_logs')->where('user_id', $user->id)->orderBy('id', 'desc')->first();
                                    ?>
                                    
                                    <tr>
                                        <td>{{++$index}}</td>
                                        <td>
                                            @if(!empty($user->image))
                                                <a href="{{ url('admin/users/edit/' . $user->id) }}">
                                                    <img src="{{ url('public/user_dashboard/profile/' . $user->image) }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                                </a>
                                            @else
                                                <a href="{{ url('admin/users/edit/' . $user->id) }}">
                                                    <img src="{{ url('public/user_dashboard/profile/user.png') }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{$user->carib_id}}</td>
                                        <td><a href="{{ url('admin/users/edit/' . $user->id) }}">@if(!empty($user)) {{$user->first_name.' '.$user->last_name}} @else - @endif</a></td>
                                        <td>{{$user->formattedPhone}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>
                                            <?php 
                                                if(!empty($status)){
                                                    echo $status; 
                                                }
                                            ?>
                                        </td>
                                        <td>@if($user->user_detail->last_login_at) {{ Carbon\Carbon::parse($user->user_detail->last_login_at)->format('d-M-Y h:i A') }} @endif | {{!empty($last_location) ? $last_location->city.' | '.DB::table('countries')->where('short_name',$last_location->country)->first()->name??'' : '-'}}</td>
                                        <td>@if(!empty($user_log->local_trans_time) && $user_log->local_trans_time) {{ Carbon\Carbon::parse($user_log->local_trans_time)->format('d-M-Y h:i A') }} @endif</td>
                                        <td>
                                            @if($user->request_device == '0')
                                                <a href="{{ url('admin/approve-device/' . $user->id) }}" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Clear Device"></i> Clear Device</a>&nbsp;
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