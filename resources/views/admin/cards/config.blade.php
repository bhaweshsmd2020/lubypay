@extends('admin.layouts.master')
@section('title', 'Partner Settings')
@section('page_content')

<!-- Main content -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">Partner Settings Form</h3>
            </div>

            <form action="{{ url('admin/partner/update') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                @csrf

				<div class="box-body">
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Partner ID</label>
					    <div class="col-sm-6">
					        <input type="text" name="partner_id" class="form-control" value="{{ $card['partner_id'] }}" placeholder="Partner ID">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Card URL</label>
					    <div class="col-sm-6">
					        <input type="text" name="card_url" class="form-control" value="{{ $card['card_url'] }}" placeholder="Card URL">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Card Key</label>
					    <div class="col-sm-6">
					        <input type="text" name="card_key" class="form-control" value="{{ $card['card_key'] }}" placeholder="Card Key">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Card Secret</label>
					    <div class="col-sm-6">
					        <input type="text" name="card_secret" class="form-control" value="{{ $card['card_secret'] }}" placeholder="Card Secret">
					    </div>
					</div>
				</div>
          		@if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_general_setting'))
            		<div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat pull-right">Update</button>
                    </div>
          		@endif
            </form>
        </div>
    </div>
</div>

@endsection