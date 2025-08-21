@extends('admin.layouts.master')
@section('title', 'Gift Card Details')
@section('page_content')

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $card->id)->where('transaction_type_id', '32')->first();
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$card->ip_address}/json"));

    $user_name = DB::table('users')->where('id', $card->user_id )->first();
    $currency = DB::table('currencies')->where('id', $card->currency_id)->first();
    
    if(!empty($card->local_tran_time)){
        $local_time = $card->local_tran_time;
    }else{
        $local_time = $card->created_at;
    }
    
    if(!empty($check_transaction)){
        $transaction_fee = number_format($check_transaction->charge_percentage + $check_transaction->charge_fixed,2);
        $total_amount =  number_format($card->amount +  $check_transaction->charge_percentage + $check_transaction->charge_fixed,2);
    }else{
        $transaction_fee = '-';
        $total_amount = '-';
    }
?>

	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h4 class="text-left">Gift Card Details</h4>
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">
										    <div class="form-group">
												<label class="control-label col-sm-6" for="total">User</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$user_name->first_name.' '.$user_name->last_name}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Recipient Email</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$card->recipient_email}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Amount</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{formatNumber($card->amount)}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Transaction Fee</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{formatNumber($transaction_fee)}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Total</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{formatNumber($total_amount)}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Currency</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$currency->code}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Brand/Product</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$card->brand_name}}({{$card->product_name}})</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Unit Price</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{formatNumber($card->product_unit_price)}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Number</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$card->gift_card_number}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Pin Code</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$card->gift_pin_code}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Quantity</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$card->quantity}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Status</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{$card->status}}</p>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-sm-6" for="total">Date</label>
												<div class="col-sm-6">
												  <p class="form-control-static">{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}</p>
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
									<div class="col-md-12">
										<a id="cancel_anchor" class="btn btn-danger pull-left" href="{{ url('admin/card/gift-card') }}">Cancel</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('extra_body_scripts')
<script type="text/javascript">

	$(".select2").select2({});

	// disabling submit and cancel button after form submit
	$(document).ready(function()
	{
	  $('form').submit(function()
	  {
	     	$("#deposits_edit").attr("disabled", true);

	     	$('#cancel_anchor').attr("disabled","disabled");

            $(".fa-spin").show();

            $("#deposits_edit_text").text('Updating...');

            // Click False
			$('#deposits_edit').click(false);
			$('#cancel_anchor').click(false);
	  });
	});
</script>
@endpush
