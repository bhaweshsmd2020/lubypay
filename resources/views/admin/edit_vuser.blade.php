@extends('admin.layouts.master')

@section('title', 'Edit Profile')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                
                <div class="panel panel-default">
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-9">
								<h4 class="text-left">{{ $user_detail->memo }} Details</h4>
							</div>
							<div class="col-md-3">
								@if ($user_detail->status)
									<h4 class="text-left">Status : @if ($user_detail->status == '1')<span class="text-green">Active</span>@endif
		                        	@if ($user_detail->status == '0')<span class="text-blue">Inactive</span>@endif
								@endif
							</div>
						</div>
					</div>

					<div class="panel-body">
                        <!-- form start -->
                        <form action="{{ url('admin/card/vuser-update/'.$user_detail->id) }}" class="form-horizontal" id="user_form" method="POST">
                            {{ csrf_field() }}
        
                            <div class="box-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label" for="inputEmail3">
                                                    Card Number
                                                </label>
                                                <div class="col-sm-8">
                                                    @if(empty($user_detail->pan))
                                                        <input class="form-control" placeholder="Update Card Number" name="pan" type="text" id="first_name" value="{{ $user_detail->last_four_digit }}">
                                                    @else
                                                        <input class="form-control" placeholder="Update Card Number" name="pan" type="text" id="first_name" value="{{ $user_detail->pan }}">
                                                    @endif
                                                </div>
                                            </div>
        
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label" for="inputEmail3">
                                                    Exp Month
                                                </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" placeholder="Update Exp Month" name="exp_month" type="text" id="last_name" value="{{ $user_detail->exp_month }}">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label" for="inputEmail3">
                                                    Exp Year
                                                </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" placeholder="Update Exp Year" name="exp_year" type="text" id="last_name" value="{{ $user_detail->exp_year }}">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label" for="inputEmail3">
                                                    CVV
                                                </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" placeholder="Update CVV" name="cvv" type="text" id="last_name" value="{{ $user_detail->cvv }}">
                                                </div>
                                            </div>
                                            
                                            <div class="row">
            									<div class="col-md-17">
            										<div class="col-md-2"></div>
            										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="{{ url('admin/card/user-list') }}">Cancel</a></div>
            										<div class="col-md-2"></div>
            										@if ($user_detail->status == '0')
                										<div class="col-md-1">
                											<button type="submit" class="btn button-secondary pull-right" id="withdrawal_edit">
                                                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="withdrawal_edit_text">Update</span>
                                                            </button>
                										</div>
                									@endif
            									</div>
            								</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
@endsection