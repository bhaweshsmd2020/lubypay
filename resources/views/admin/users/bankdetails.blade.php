@php

use App\Models\DocumentVerification;

$documents = DocumentVerification::where(['user_id' => $users->id])->get(['id', 'verification_type']);

@endphp

@extends('admin.layouts.master')

@section('title', 'Bank Details')

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
                <li>
                  <a href="{{url("admin/users/kyc-verications/$users->id")}}">KYC Verifications</a>
                </li>
                <li class="active">
                  <a href="{{url("admin/users/bankdetails/$users->id")}}">Bank Details</a>
                </li>
                <li>
                  <a href="{{url("admin/users/address_edit/$users->id")}}">Address</a>
                </li>
                <li>
                  <a href="{{url("admin/users/activity-logs/$users->id")}}">Activity Logs</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    @if ($users->status == 'Inactive')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;( {{ $users->carib_id }} )&nbsp;<span class="label label-danger">Inactive</span></h3>
    @elseif ($users->status == 'Suspended')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;( {{ $users->carib_id }} )&nbsp;<span class="label label-warning">Suspended</span></h3>
    @elseif ($users->status == 'Active')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;( {{ $users->carib_id }} )&nbsp;<span class="label label-success">Active</span></h3>
    @endif

    <div class="box">
      <div class="box-body">
        <h3 class="text-center" style="margin-bottom: 20px;">Bank Details</h3>
        <div class="row">
            @foreach($banks as $bank)
                <div class="col-md-6">
                    <div style="border: 1px solid gray; border-radius: 10px; margin-bottom: 15px; padding: 15px;">
                        <?php
                            $check_bank = json_decode($bank->bank, true);
                            $check_country = DB::table('countries')->where('id', $bank->country_id)->first();
                        ?>
                        
                        <p><strong>Country</strong> : {{$check_country->name}}</p>
                        
                        @foreach($check_bank as $k=>$val)
                            <p><strong>{{$k}}</strong> : {{$val}}</p>
                        @endforeach
                    </div>
                </div>
            @endforeach
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
