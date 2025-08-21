@extends('admin.layouts.master')
@section('title', 'Survey List')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <style>
        #example_wrapper{
            overflow: scroll;
        }
    </style>

    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">Survey List</div>
                </div>
                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level'))

                <div class="col-md-3 text-right">
                    <a href="{{url('admin/add-survey')}}" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Survey</a>
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
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $k=>$val)
                                    <tr>
                                        <td>{{++$k}}.</td>
                                        <td><a href="{{$val->url}}" target="_blank">{{$val->url}}</a></td>
                                        <td>
                                            @if($val->status==0)
                                            <span class="badge badge-pill badge-danger">{{__('Inactive')}}</span>
                                            @elseif($val->status==1)
                                            <span class="badge badge-pill badge-success">{{__('Active')}}</span>
                                            @endif
                                        </td>
                                        <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                        <td>
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level'))
                                                <a href="{{url('admin/edit-survey/'.$val->id)}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                            @endif
                                            
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level'))
                                                <a href="{{url('admin/delete-survey/'.$val->id)}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                                            @endif
                                            
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_app_level'))
                                                <a href="{{url('admin/remind-survey/'.$val->id)}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Reminder"><i class="fa fa-reply"></i></a>
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