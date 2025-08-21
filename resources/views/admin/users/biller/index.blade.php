@extends('admin.layouts.master')

@section('title', 'Cable TV Bill')

@section('head_style') 
    <!-- dataTables -->
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <!-- Main content -->
    <div class="row">
         @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible" style="width: fit-content;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success!</strong> {{ Session::get('success') }}
        </div>
       
        @endif
        <div class="col-md-12">
            <div class="box box_info"> 
                  <div class="box-header">
                    <h3 class="box-title">Manage Biller</h3>
                   
                  </div>
                  <hr>
                  <div class="box-body table-responsive">
                     <table id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Bill Name</th>
                                <th>Service Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             @php
                             $num = 1;
                            @endphp
                            
                              @foreach($biller as $value)
                            <tr>
                               <td>{{$num}}</td>
                               <td>{{$value->bill_name}}</td>
                               <td>{{DB::table('dhiraagu_services')->where('service_id',$value->service_id)->first()->service_name??''}}</td>
                               <td><a href="{{url('admin/users/transactions',$value->user_id)}}" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-user" data-toggle="tooltip" title="Biller"></i> Transaction</a> &nbsp;
                               <a data-toggle="modal" data-target="#myModal_{{$value->service_id}}" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Biller"></i> Edit</a>
                               &nbsp;<a href="{{url('admin/users/delete-bill',$value->id)}}"><i class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Delete Bill"></i></a>
                               </td>
                              
                            </tr>

                            <!-- Modal -->
                            <div id="myModal_{{$value->service_id}}" class="modal fade" role="dialog">
                              <div class="modal-dialog">
                            
                                <!-- Modal content-->
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">{{ $value->bill_name }}</h4>
                                  </div>
                                  <div class="modal-body">
                                   <form action="{{url('admin/user/update_biller')}}" method="post" enctype="multipart/form-data" >
                                        @csrf 
                                        <input type="hidden" name="service_id" value="{{$value->service_id}}">
                                        <input type="hidden" name="user_id" value="{{$value->user_id}}">
                                   <div class="row">
                                     <div class="col-md-6">
                                      <div class="form-group">
                                         <label  for="notification_type_name">Bill Name</label>
                                           <input type="text" name="bill_name" class="form-control" value="{{ $value->bill_name }}" id="notification_type_name" >
                                                <span id="type_error"></span>
                                               
                                         </div>
                                     </div>
                                     <div class="col-md-6">
                                      <div class="form-group">
                                         <label  for="notification_type_name">Account Number</label>
                                           <input type="text" name="account_num"  class="form-control" value="{{ $value->account_num }}" id="notification_type_name" >
                                                <span id="type_error"></span>
                                               
                                         </div>
                                     </div>
                                     @if($value->service_id == 2)
                                     <div class="col-md-6">
                                      <div class="form-group">
                                         <label  for="notification_type_name">Meter Number</label>
                                           <input type="text" name="meter_num" class="form-control" value="{{ $value->meter_num }}" id="notification_type_name" >
                                                <span id="type_error"></span>
                                               
                                         </div>
                                     </div>
                                     @endif
                                     @if($value->service_id == 1)
                                     <div class="col-md-6">
                                      <div class="form-group">
                                         <label  for="notification_type_name">Number</label>
                                           <input type="text" name="number" class="form-control" value="{{ $value->number }}" id="notification_type_name" >
                                                <span id="type_error"></span>
                                               
                                         </div>
                                     </div>
                                     @endif
                                     @if($value->service_id == 4)
                                     <div class="col-md-6">
                                      <div class="form-group">
                                         <label  for="notification_type_name">Nic Number</label>
                                           <input type="text" name="nic_num" class="form-control" value="{{ $value->nic_num }}" id="notification_type_name" >
                                                <span id="type_error"></span>
                                               
                                         </div>
                                     </div>
                                     @endif
                                     
                                  </div>
                                  <div class="modal-footer">
                                      <button type="submit" class="btn btn-success" >Update</button>&nbsp;
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                  </form>
                                </div>
                            
                              </div>
                            </div>
                           @php
                             $num++;
                            @endphp
                            @endforeach
                             </tbody>
                      
                    </table>
                  </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
@endpush
