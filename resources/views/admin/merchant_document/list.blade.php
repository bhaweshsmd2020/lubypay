@extends('admin.layouts.master')

@section('title', 'Merchant Documents')

@section('head_style')
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
  <!-- Main content -->
  <div class="row">
    <div class="col-md-3 settings_bar_gap">
    @include('admin.common.settings_bar')
    </div>
    <div class="col-md-9">
      <div class="box box_info">
            <div class="box-header">
              <h3 class="box-title">Manage Merchant Documents</h3>
              <a href="{{ url('admin/settings/add-merchant-group-document') }}"
                       class="btn btn-primary pull-right">Add New Document</a>
            </div>
            <hr>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table recent_activity" align="left">
                    <thead>
                        <tr>
                            <td class="text-left">
                                <strong>Name</strong>
                            </td>
                            <td class="text-left">
                                <strong>Action</strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>
                                    <a href="{{ url('admin/settings/edit-merchant-document/'.$document->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="{{ url('admin/settings/merchant-document/delete/'.$document->id) }}" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
      </div>
    </div>
  </div>
@endsection

