@extends('admin.layouts.master')
@section('title', 'Payout Methods')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">Payout Methods</div>
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
                                    <th>Label</th>
                                    <th>Status</th>
                                    <th>Required</th>
                                    <th>Order</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($labels as $k=>$label)
                                    <?php
                                        $check_label = DB::table('countries_payout')->where('payout_method', $label->id)->where('country', $result->id)->first();
                                    ?>
                                    <tr>
                                        <td>{{++$k}}</td>
                                        <td>{{$label->name}}</td>
                                        <td>
                                            @if(!empty($check_label) && $check_label->status == '1')
                                                Enabled
                                            @else
                                                Disabled
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($check_label) && $check_label->required == '1')
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($check_label->sort_by))
                                                {{$check_label->sort_by}}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class=" dt-center">
                                            <a href="#" data-toggle="modal" data-target="#modal{{$label->id}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="modal{{$label->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">{{$label->payout_method}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ url('admin/settings/edit_label/'.$label->id) }}" class="form-horizontal" id="edit_country_form">
                                                        {{ csrf_field() }}
                                            
                                                        <div class="box-body">
                                                            <input type="hidden" name="country" value="{{$result->id}}">
                                                            
                                                            <div class="form-group"> 
                                                                <label class="col-sm-3 control-label" for="short_name">Label Name</label> 
                                                                <div class="col-sm-6"> 
                                                                    <input type="text" value="{{$label->name}}" class="form-control" placeholder="Label Name" readonly> 
                                                                </div> 
                                                            </div><br><br>
                                                            
                                                            <div class="form-group"> 
                                                                <label class="col-sm-3 control-label" for="short_name">Status</label> 
                                                                <div class="col-sm-6"> 
                                                                    <select name="status" class="form-control" required>
                                                                        <option>Select</option>
                                                                        <option value="1" @if(!empty($check_label) && $check_label->status == '1') selected @endif>Enabled</option>
                                                                        <option value="2" @if(!empty($check_label) && $check_label->status == '2') selected @endif>Disabled</option>
                                                                    </select>
                                                                </div> 
                                                            </div><br><br>
                                                            
                                                            <div class="form-group"> 
                                                                <label class="col-sm-3 control-label" for="short_name">Required</label> 
                                                                <div class="col-sm-6"> 
                                                                    <select name="required" class="form-control" required>
                                                                        <option>Select</option>
                                                                        <option value="1" @if(!empty($check_label) && $check_label->required == '1') selected @endif>Yes</option>
                                                                        <option value="2" @if(!empty($check_label) && $check_label->required == '2') selected @endif>No</option>
                                                                    </select>
                                                                </div> 
                                                            </div><br><br>
                                                            
                                                            <div class="form-group"> 
                                                                <label class="col-sm-3 control-label" for="short_name">Order</label> 
                                                                <div class="col-sm-6"> 
                                                                    <input type="text" name="sort_by" value="@if(!empty($check_label)) {{$check_label->sort_by}} @endif" class="form-control" placeholder="Order" required> 
                                                                </div> 
                                                            </div><br>
                                                        </div>
                                                
                                                        <div class="box-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                                                            <button type="submit" class="btn btn-primary pull-right">&nbsp; Update &nbsp;</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
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
    		document.title='Payout Methods';
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