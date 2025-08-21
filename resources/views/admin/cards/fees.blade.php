@extends('admin.layouts.master')
@section('title', 'Partner Settings')
@section('page_content')

<!-- Main content -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">Card Settings</h3>
            </div>

            <form action="{{ url('admin/card/fees/update') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                @csrf

				<div class="box-body">
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Minimum Limit</label>
					    <div class="col-sm-6">
					        <input type="text" name="min_limit" class="form-control" value="{{ $fee->min_limit }}" placeholder="Minimum Limit">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Maximum Limit</label>
					    <div class="col-sm-6">
					        <input type="text" name="max_limit" class="form-control" value="{{ $fee->max_limit }}" placeholder="Maximum Limit">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Billing Info</label>
					    <div class="col-sm-6">
					        <input type="text" name="billing_info" class="form-control" value="{{ $fee->billing_info }}" placeholder="Billing Info">
					    </div>
					</div>
					<div class="form-group">
					    <label class="col-sm-4 control-label" for="inputEmail3">Recommended Amount</label>
					    <div class="col-sm-6">
					        <input type="text" name="recommended_amount" class="form-control" value="{{ $fee->recommended_amount }}" placeholder="Recommended Amount">
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