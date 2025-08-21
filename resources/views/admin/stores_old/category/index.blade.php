@extends('admin.layouts.master')
@section('title', 'Store Categories')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href="{{url("admin/store/edit/" . $store_detail->id)}}">Details</a>
                </li>

                <li>
                  <a href="{{url("admin/store/product/list/" . $store_detail->id)}}">Products</a>
                </li>
                
                <li class="active">
                  <a href="{{url("admin/store/category/list/" . $store_detail->id)}}">Categories</a>
                </li>
                
                <li>
                  <a href="{{url("admin/store/orders/list/" . $store_detail->id)}}">Orders</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">{{$store_detail->name}} Categories</div>
                </div>
                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_categories'))
                    <div class="col-md-2 pull-right">
                        <a href="{{url('admin/store/category/create/'.$store_detail->id)}}" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Categories</a>
                    </div>
                @endif
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
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Created On</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $k=>$category)
                                    <tr>
                                        <td>{{++$k}}</td>
                                        <td>
                                            @if(!empty($category->image))
                                                <img src="{{ url('public/user_dashboard/categories/thumb/' . $category->image) }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                            @else
                                                <img src="{{ url('public/user_dashboard/profile/user.png') }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                            @endif
                                        </td>
                                        <td>{{$category->name}}</td>
                                        <td>{{$category->description}}</td>
                                        <td>{{$category->created_at}}</td>
                                        <td>
                                            <a href="{{ url('admin/store/category/edit/'.$store_detail->id.'/'.$category->id) }}" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Edit"></i></a>&nbsp;
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_categories'))  
                                                <a href="{{ url('admin/store/category/delete/'.$store_detail->id.'/'.$category->id) }}" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Delete"></i></a>&nbsp;
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