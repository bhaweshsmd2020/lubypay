@extends('admin.layouts.master')
@section('title', 'Services')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.appsettings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="top-bar-title padding-bottom pull-left">Manage Services</div>
                        </div>
        
                        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_services'))
                            <div class="col-md-2 pull-right">
                                <a href="{{url('admin/settings/services/add')}}" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Service</a>
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
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Page</th>
                                            <th>Image</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th>Sorting</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($services as $k=>$list)
                                            <tr>
                                                <td>{{++$k}}</td>
                                                <td>{{$list->name}}</td>
                                                <td>{{$list->page}}</td>
                                                <td>
                                                    <?php if($list->banner_image) {?>
                                                        <img src="{{url('public/uploads/userPic/'.$list->image)}}" style="width:100px; height:50px" />   
                                                    <?php }?>
                                                </td>
                                                <td>{{$list->position}}</td>
                                                <td>{{$list->status}}</td>
                                                <td>{{$list->sorting}}</td>
                                                <td class=" dt-center">
                                                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_services'))
                                                        <a href="{{url('admin/settings/services_edit/'.$list->id)}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                                    @endif 
                                                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_services'))
                                                        <a href="{{url('admin/settings/services_delete/'.$list->id)}}" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>
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