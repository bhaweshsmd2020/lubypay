@extends('admin.layouts.master')
@section('title', 'Edit Transfer')
@section('page_content')

<?php
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$transaction->ip_address}/json"));
    
    if(!empty($transaction->local_tran_time)){
	    $trans_time = $transaction->local_tran_time;
	}else{
	    $trans_time = $transaction->created_at;
	}
	
	$user = DB::table('users')->where('id', $transaction->user_id)->first();
    $currency = DB::table('currencies')->where('id', $transaction->currency_id)->first();
?>

	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h4 class="text-left">Transfer Details</h4>
								</div>
								<div class="col-md-3">
									@if ($transaction->status)
										<h4 class="text-left">Status : @if ($transaction->status == 'Success')<span class="text-green">Success</span>@endif
				                    	@if ($transaction->status == 'Pending')<span class="text-blue">Pending</span>@endif
				            			@if ($transaction->status == 'Blocked')<span class="text-red">Cancelled</span>@endif</h4>
									@endif
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<form class="form-horizontal">
									<div class="col-md-6">
									    <div class="panel panel-default">
											<div class="panel-body">
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Amount</label>
													<div class="col-sm-6">
													  <p class="form-control-static">{{ moneyFormat($currency->code, formatNumber($transaction->amount)) }}</p>
													</div>
												</div>
							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="feesTotal">Fees</label>
													<div class="col-sm-6">
													  <p class="form-control-static">{{ moneyFormat($currency->code, 0.00) }}</p>
													</div>
												</div>
												<hr class="increase-hr-height">
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Total</label>
													<div class="col-sm-6">
													  <p class="form-control-static">{{  moneyFormat($currency->code, formatNumber($transaction->amount)) }}</p>
													</div>
												</div>
											</div>
										</div>
										
										<div class="panel panel-default">
											<div class="panel-body">
											    
												<div class="form-group">
													<label class="control-label col-sm-4" for="user">User</label>
													<div class="col-sm-8">
													  <p class="form-control-static">{{$user->first_name}} {{$user->last_name}}</p>
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-4" for="user">From Card</label>
													<div class="col-sm-8">
													  <p class="form-control-static">{{$transaction->from_card}}</p>
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-4" for="user">To Card</label>
													<div class="col-sm-8">
													  <p class="form-control-static">{{$transaction->to_card}}</p>
													</div>
												</div>

							                    <div class="form-group">
													<label class="control-label col-sm-4" for="deposit_uuid">Transaction ID</label>
													<div class="col-sm-8">
													  <p class="form-control-static">{{ $transaction->uuid }}</p>
													</div>
												</div>

												<div class="form-group">
    												<label class="control-label col-sm-4" for="created_at">Date</label>
    												<div class="col-sm-8">
    												  <p class="form-control-static">{{ Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A') }}</p>
    												</div>
    											</div>

						                   		<div class="form-group">
													<label class="control-label col-sm-4" for="status">Status</label>
													<div class="col-sm-8">
														<p class="form-control-static">{{ $transaction->status }}</p>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="col-md-6">
									    @if(!empty($ip_details))
        									<div class="panel panel-default">
        										<div class="panel-body">
        										    <h3 class="text-center">Location</h3>
        										    
        										    <div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">IP Address</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $ip_details->ip }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">City</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $ip_details->city }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Region</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $ip_details->region }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Country</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $ip_details->country }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Postal Code</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ !empty($ip_details->postal) ? $ip_details->postal : '-'}}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Timezone</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $ip_details->timezone }}</p>
        												</div>
        											</div>
        										</div>
        									</div>
        								@endif
    									
    									@if(!empty($transaction_details))
        									<div class="panel panel-default">
        										<div class="panel-body">
        										    <h3 class="text-center">Device</h3>
        										    
        										    <div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Device Name</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $transaction_details->device_name }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Brand</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $transaction_details->device_manufacture }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">Model</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $transaction_details->device_model }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">OS</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $transaction_details->device_os }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">OS Version</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $transaction_details->os_ver }}</p>
        												</div>
        											</div>
        											
        											<div class="form-group" style="margin-bottom: 0px;">
        												<label class="control-label col-sm-6" for="account_name">App Version</label>
        												<div class="col-sm-6">
        												  <p class="form-control-static">{{ $transaction_details->app_ver }}</p>
        												</div>
        											</div>
        										</div>
        									</div>
        								@endif
									</div>

									<div class="row">
										<div class="col-md-11">
											<div class="col-md-2"></div>
											<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="{{ url('admin/card/transfers') }}">Cancel</a></div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection