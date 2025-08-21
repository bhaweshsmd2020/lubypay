@extends('admin.layouts.master')
@section('title', 'NFC Credentials Settings')

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
        @include('admin.common.appsettings_bar')
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">NFC Credentials Settings</h3>
            </div>

            <form action="{{ url('admin/settings/nfc-update') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                {!! csrf_field() !!}

                <!-- box-body -->
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Publish key</label>
					  <div class="col-sm-10">
					    <input type="text" name="pub_key" class="form-control" value="{{ $credential->pub_key }}" placeholder="Publish key">
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Secret key</label>
					  <div class="col-sm-10">
					    <input type="text" name="sec_key" class="form-control" value="{{ $credential->sec_key }}" placeholder="Secret key">
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Mode</label>
					  <div class="col-sm-10">
					    <select name="mode" class="form-control">
					        <option value="production" @if($credential->mode == 'production') selected @endif>Production</option>
					        <option value="sandbox" @if($credential->mode == 'sandbox') selected @endif>Sandbox</option>
					    </select>
					  </div>
					</div>
					  
					<div class="form-group">
					  <label class="col-sm-2 control-label" for="inputEmail3">Status</label>
					  <div class="col-sm-10">
					    <select name="status" class="form-control">
					        <option value="1" @if($credential->status == '1') selected @endif>Active</option>
					        <option value="2" @if($credential->status == '2') selected @endif>Inactive</option>
					    </select>
					  </div>
					</div>
				</div>
				<!-- /.box-body -->

				<!-- box-footer -->
				@if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_nfc_credentials'))
        			<div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat pull-right">Submit</button>
                    </div>
                @endif
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


