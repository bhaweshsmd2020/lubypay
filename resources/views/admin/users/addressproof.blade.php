@php

use App\Models\DocumentVerification;

$documents = DocumentVerification::where(['user_id' => $users->id])->get(['id', 'verification_type']);

@endphp

@extends('admin.layouts.master')

@section('title', 'Photo Proof')

@section('head_style')
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href='{{url("admin/users/edit/$users->id")}}'>Profile</a>
                </li>

                <li>
                  <a href="{{url("admin/users/transactions/$users->id")}}">Transactions</a>
                </li>
                <li>
                  <a href="{{url("admin/users/wallets/$users->id")}}">Wallets</a>
                </li>
                <li>
                  <a href="{{url("admin/users/tickets/$users->id")}}">Tickets</a>
                </li>
                <li >
                  <a href="{{url("admin/users/disputes/$users->id")}}">Disputes</a>
                </li>
                <li >
                  <a href="{{url("admin/users/photoproof/$users->id")}}">Photo Proof</a>
                </li>
                <li class="active">
                  <a href="{{url("admin/users/addressproof/$users->id")}}">Address Proof</a>
                </li>
                
                <li >
                  <a href="{{url("admin/users/idproof/$users->id")}}">Identity Proof</a>
                </li>
                <li>
                  <a href="{{url("admin/users/bankdetails/$users->id")}}">Bank Details</a>
                </li>
                 <li>
                  <a href="{{url("admin/users/address_edit/$users->id")}}">Address</a>
                </li>
                <!-- identity verification tabs -->
                
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    @if ($users->status == 'Inactive')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-danger">Inactive</span></h3>
    @elseif ($users->status == 'Suspended')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-warning">Suspended</span></h3>
    @elseif ($users->status == 'Active')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-success">Active</span></h3>
    @endif

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover" id="eachuserdispute">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($documentVerificationStatus)
                            @foreach($documentVerificationStatus as $list)
                                <tr>
                                   

                                    <td>{{ $list->id }}</td>
                                     <td>{{ dateFormat($list->created_at) }}</td>

                                    <td>{{ $users->first_name.' '.$users->last_name }}</td>
                                    <td>
                                        <?php 
                                        if ($list->status == 'approved')
                                            { 
                                               echo '<span class="label label-success">Approved</span>';
                                            } 
                                            elseif ($list->status == 'pending')
                                            { 
                                                echo '<span class="label label-primary">Pending</span>';
                                             }
                                            elseif ($list->status == 'rejected')
                                            { 
                                           echo '<span class="label label-danger">Rejected</span>';
                                           }
                                        
                                        ?>
                                    </td>
                                    
                                   <td>
                                        <a href="{{url('admin/address-proofs/edit/'.$list->id)}}" class="btn btn-xs btn-primary">
                                            <i class="glyphicon glyphicon-edit"></i>
                                        </a>
                                        
                                    </td>
                                   
                                </tr>
                            @endforeach
                        @else
                            No Dispute Found!
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.layouts.partials.message_boxes')
@endsection

@push('extra_body_scripts')

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
      $("#eachuserdispute").DataTable({
            "order": [],
            "columnDefs": [
            {
                "className": "dt-center",
                "targets": "_all"
            }
            ],
            "language": '{{Session::get('dflt_lang')}}',
            "pageLength": '{{Session::get('row_per_page')}}'
        });
    });
</script>
@endpush
