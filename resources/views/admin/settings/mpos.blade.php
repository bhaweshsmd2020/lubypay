@extends('admin.layouts.master')
@section('title', 'Fee Settings')

@section('head_style')
   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css')}}">

  <!-- bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap-select-1.13.12/css/bootstrap-select.min.css')}}">

@endsection

@section('page_content')

<!-- Main content -->
<div class="row">
    <div class="col-md-3 settings_bar_gap">
        @include('admin.common.settings_bar')
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">MPOS Fee Settings</h3>
            </div>

            <form action="{{ url('admin/settings/fee-update') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                {!! csrf_field() !!}

                <!-- box-body -->
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-4 control-label" for="inputEmail3">Fee</label>
					  <div class="col-sm-6">
					    <input type="text" name="mpos_fee" class="form-control" value="{{ @$result['mpos_fee'] }}" placeholder="Fee">
					  </div>
					</div>
				</div>
				<!-- /.box-body -->

				<!-- box-footer -->
    			<div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat pull-right">
                        Submit
                    </button>
                </div>
  	            <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>

@endsection

@push('extra_body_scripts')

  <!-- jquery.validate -->
  <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

  <!-- jquery.validate additional-methods -->
  <script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>

  <!-- sweetalert -->
  <script src="{{ asset('public/backend/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

  <!-- bootstrap-select -->
  <script src="{{ asset('public/backend/bootstrap-select-1.13.12/js/bootstrap-select.min.js') }}" type="text/javascript"></script>

  <!-- read-file-on-change -->
  @include('common.read-file-on-change')

@endpush


