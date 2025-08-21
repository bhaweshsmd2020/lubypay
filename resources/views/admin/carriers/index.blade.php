@extends('admin.layouts.master')

@section('title', 'Users')

@section('head_style')
<!-- Bootstrap daterangepicker -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">

<!-- dataTables -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Carriers</div>
                </div>
                <div class="col-md-2 pull-right">
                   <a href="" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Carriers</a>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="box-body table-responsive">
                        <table class="table datatable" id="dataTableBuilder">
                                    <thead>
                                    <tr>
                                        <td><strong>ID</strong></td>
                                        <td><strong>Name</strong></td>
                                        <td><strong>Email</strong></td>
                                        <td><strong>URL</strong></td>
                                        <td><strong>@lang('message.dashboard.product.table.action')</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($list as $result)
                                        <tr>
                                            <td>{{ $result->id }}</td>
                                            <td>{{ $result->name}} </td>
                                            <td>{{ $result->email }} </td>
                                            
                                            <td><a href="{{ $result->tracking_url }}" > {{ $result->tracking_url }} </a></td>
                                            <td>
                                                <a href="" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i></a>
                                                <a href="" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
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
@endsection

@push('extra_body_scripts')

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>



<script type="text/javascript">
</script>
@endpush
