@extends('admin.layouts.master')

@section('title', 'Gas Bill')

@section('head_style') 
    <!-- dataTables -->
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <!-- Main content -->
    <div class="row">
        
        <div class="col-md-12">
            <div class="box box_info"> 
                  <div class="box-header">
                    <h3 class="box-title">Manage Gas Bill Request</h3>

                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant'))
                      <div style="float:right;"><a class="btn btn-success" href="{{ url('admin/utility/pay-gas-bill') }}">Add Gas Bill Request</a></div>
                    @endif
                  </div>
                  <hr>
                   <div class="box-body table-responsive">
                     <table id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Customer Name</th>
                                <th>Customer Account Number</th>
                                <th>NIC Number</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                             $num = 1;
                             
                            @endphp
                            @foreach($details as $value)
                            <tr>
                                <td>{{$num}}</td>
                                <td>{{App\Models\User::find($value->user_id)->first_name??'NA'}}</td>
                               
                                 <td>{{$value->account_number}}</td>
                                  <td>{{$value->nic_num??'NA'}}</td>
                                <td>{{$value->transaction_id}}</td>
                                <td>{{number_format($value->amount,2)}}</td>
                             
                                <td>{{$value->status}}</td> 
                               
                              <td>{{$value->created_at}}</td>
                            </tr>
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
