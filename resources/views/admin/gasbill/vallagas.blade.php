@extends('admin.layouts.master')

@section('title', 'Villa Gas Bill')

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
                    <h3 class="box-title">Manage Gas Villa Bill Request</h3>
                      @php
                      $status = DB::table('dhiraagu_services')->where('service_id',5)->first()->is_active??'1';
                    @endphp
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant'))
                      @if($status == 1)
                      <div style="float:right;"><a class="btn btn-success" href="{{ url('admin/utility/pay-villa-gas-bill') }}">Add Villa Gas Bill</a></div>
                      @endif
                    @endif
                    
                  </div>
                  <hr>
                   <div class="box-body table-responsive">
                     <table id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Customer Name</th>
                                <th>Customer Number</th>
                                <th>Account Number</th>
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
                                <td>{{$value->contact_number??'NA'}}</td>
                                 <td>{{$value->account_number}}</td>
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
