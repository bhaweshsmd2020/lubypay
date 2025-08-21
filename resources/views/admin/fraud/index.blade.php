@extends('admin.layouts.master')
@section('title', 'Fraud Detection Settings')

@section('head_style')
   <!-- sweetalert -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css')}}">

  <!-- bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap-select-1.13.12/css/bootstrap-select.min.css')}}">

@endsection

@section('page_content')

<!-- Main content -->
<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="top-bar-title padding-bottom">Fraud Detection Settings</div>
            </div>
        </div>
    </div>
</div>

<div class="box">
  <div class="box-body">
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Currency : <span class="currencyName">{{ $currency->name??'' }}</span>
        <span class="caret"></span></button>
        <ul class="dropdown-menu">
          @foreach($currencyList as $currencyItem)
            <li class="listItem">
              <a href="{{ url('admin/settings/fraud-detection/'.$transact->id.'/'.$currencyItem->id)??'' }}">{{$currencyItem->name??''}}</a>
            </li>
          @endforeach
        </ul>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-3 settings_bar_gap">
        <div class="box box-info box_info">
            <div class="panel-body">
                <h4 class="all_settings">
                    Transaction Types
                </h4>
                <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
                    @foreach($transactions as $transaction)
                        <li class="<?php if($transact->name == $transaction->name){ echo 'active'; } ?>">
                            <a data-group="settings" href="{{ url('admin/settings/fraud-detection/'.$transaction->id.'/'.$currency->id) }}">
                                <i class="glyphicon glyphicon-cog">
                                </i>
                                <span>
                                    {{ $transaction->name }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="box box-info">
            <div class="box-header with-border text-center">
              <h3 class="box-title">{{ $transact->name }}</h3>
            </div>

            <form action="{{ url('admin/settings/update_fraud_detection') }}" method="post" class="form-horizontal" enctype="multipart/form-data" id="general_settings_form">
                {!! csrf_field() !!}
                
                <input type="hidden" name="transaction_type" value="{{ $transact->id??'' }}" class="form-control">
                <input type="hidden" name="currency_type" value="{{ $currency->id??'' }}" class="form-control">

                <!-- box-body -->
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/Hour</label>
					  <div class="col-sm-5">
					    <input type="text" name="transactions_hour" class="form-control" value="{{ $fraud->transactions_hour??'' }}" placeholder="Transactions/Hour">
					  	<span class="text-danger">{{ $errors->first('transactions_hour') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="transactions_day" class="form-control" value="{{ $fraud->transactions_day??'' }}" placeholder="Transactions/Day">
					  	<span class="text-danger">{{ $errors->first('transactions_day') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Hour</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_hour" class="form-control" value="{{ $fraud->amount_hour??'' }}" placeholder="Amount/Hour">
					  	<span class="text-danger">{{ $errors->first('amount_hour') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_day" class="form-control" value="{{ $fraud->amount_day??'' }}" placeholder="Transactions/Day">
					  	<span class="text-danger">{{ $errors->first('amount_day') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Week</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_week" class="form-control" value="{{ $fraud->amount_week??'' }}" placeholder="Transactions/Week">
					  	<span class="text-danger">{{ $errors->first('amount_week') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Amount/Month</label>
					  <div class="col-sm-5">
					    <input type="text" name="amount_month" class="form-control" value="{{ $fraud->amount_month??'' }}" placeholder="Transactions/Month">
					  	<span class="text-danger">{{ $errors->first('amount_month') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Same Amount/Hour</label>
					  <div class="col-sm-5">
					    <input type="text" name="same_amount" class="form-control" value="{{ $fraud->same_amount??'' }}" placeholder="Same Amount/Hour">
					  	<span class="text-danger">{{ $errors->first('same_amount') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/Email/Phone/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="email_day" class="form-control" value="{{ $fraud->email_day??'' }}" placeholder="Transactions/Email/Day">
					  	<span class="text-danger">{{ $errors->first('email_day') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">Transactions/IP Address/Day</label>
					  <div class="col-sm-5">
					    <input type="text" name="ipadd_day" class="form-control" value="{{ $fraud->ipadd_day??'' }}" placeholder="Transactions/IP Address/Day">
					  	<span class="text-danger">{{ $errors->first('ipadd_day') }}</span>
					  </div>
					</div>
				</div>
				
				<div class="box-body">
					<div class="form-group">
					  <label class="col-sm-5 control-label" for="inputEmail3">New User Account(Days)</label>
					  <div class="col-sm-5">
					    <input type="text" name="user_created_at" class="form-control" value="{{ $fraud->user_created_at??'' }}" placeholder="New User Transaction/Day">
					  	<span class="text-danger">{{ $errors->first('user_created_at') }}</span>
					  </div>
					</div>
				</div>
				<!-- /.box-body -->

				<!-- box-footer -->
				<div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat pull-right" id="general-settings-submit">
                        <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="general-settings-submit-text">Submit</span>
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


@endpush


