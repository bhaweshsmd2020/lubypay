@extends('admin.layouts.master')
@section('title', 'Stores')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Stores</div>
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
                                    <th>Store Profile</th>
                                    <th>Store Name</th>
                                    <th>Merchant Name</th>
                                    <th>Currency</th>
                                    <th>Country</th>
                                    <th>Created On</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stores as $index=>$store)
                                    <?php
                                        $user = DB::table('users')->where('id', $store->user_id)->first();
                                        $currency = DB::table('currencies')->where('id', $store->currency_id)->first();
                                        $country = DB::table('countries')->where('id', $store->country)->first();
                                    ?>
                                    
                                    <tr>
                                        <td>{{++$index}}</td>
                                        <td>
                                            @if(!empty($store->image))
                                                <a href="{{ url('admin/store/edit/' . $store->id) }}">
                                                    <img src="{{ url('public/uploads/store/' . $store->image) }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                                </a>
                                            @else
                                                <a href="{{ url('admin/store/edit/' . $store->id) }}">
                                                    <img src="{{ url('public/user_dashboard/profile/user.png') }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                                </a>
                                            @endif
                                        </td>
                                        <td><a href="{{ url('admin/store/edit/' . $store->id) }}">{{$store->name}}</a></td>
                                        <td><a href="{{ url('admin/users/edit/' . $store->user_id) }}">@if(!empty($user)) {{$user->first_name.' '.$user->last_name}} @else - @endif</a></td>
                                        <td>{{$currency->code}}</td>
                                        <td>{{$country->name}}</td>
                                        <td>{{$store->created_at}}</td>
                                        <td>
                                            <a href="{{ url('admin/store/edit/' . $store->id) }}" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Edit"></i></a>&nbsp;
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_stores'))
                                                <a href="{{ url('admin/store/delete/' . $store->id) }}" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Delete"></i></a>&nbsp;
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