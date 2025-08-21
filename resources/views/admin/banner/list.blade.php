@extends('admin.layouts.master')
@section('title', 'Banners')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">App Banners</div>
                </div>

                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_banner'))
                    <div class="col-md-2 pull-right">
                        <a href="{{url('admin/banner/add')}}" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Banner</a>
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
                                    <th>Banner</th>
                                    <th>Position</th>
                                    <th>Redirection Type</th>
                                    <th>Redirect To</th>
                                    <th>Language</th>
                                    <th>Platform</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lists as $k=>$list)
                                    <tr>
                                        <td>{{++$k}}</td>
                                        <td>
                                            <?php if($list->banner_image) {?>
                                                <img src="{{url('public/uploads/banner/'.$list->banner_image)}}" style="width:100px; height:50px" />   
                                            <?php }?>
                                        </td>
                                        <td>{{$list->position}}</td>
                                        <td>
                                            @if($list->app_redirect=='0')
                                                None
                                            @elseif($list->app_redirect=='1')
                                                App Page
                                            @elseif($list->app_redirect=='2')
                                                Redirect URL
                                            @endif
                                        </td>
                                        <td>
                                            @if($list->app_redirect=='0')
                                                None
                                            @elseif($list->app_redirect=='1')
                                                @foreach($pages as $page)
                                                    @if($page->id == $list->app_page)
                                                        {{$page->page_name}}
                                                    @endif
                                                @endforeach
                                            @elseif($list->app_redirect=='2')
                                                {{$list->redirect_url}}
                                            @endif
                                        </td>
                                        <td>
                                            @php 
                                                $language=DB::table('languages')->where('id',$list->language)->where('status','Active')->first();
                                            @endphp
                                            {{$language->name}}
                                        </td>
                                        <td>
                                            @if($list->platform == 'ewallet')
                                                Ewallet
                                            @else
                                                Mpos
                                            @endif
                                        </td>
                                        <td>{{$list->status}}</td>
                                        <td>{{$list->created_at}}</td>
                                        <td class=" dt-center">
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_banner'))
                                                <a href="{{url('admin/banner/edit/'.$list->banner_id)}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                            @endif 
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_banner'))
                                                <a href="{{url('admin/banner/delete/'.$list->banner_id)}}" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>
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