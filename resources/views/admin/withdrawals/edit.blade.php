@extends('admin.layouts.master')
@section('title', 'Edit Payout')
@section('page_content')

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $withdrawal->id)->where('payment_method_id', '6')->first();
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$withdrawal->ip_address}/json"));
    
    if(!empty($withdrawal->local_tran_time)){
	    $trans_time = $withdrawal->local_tran_time;
	}else{
	    $trans_time = $withdrawal->created_at;
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
								<h4 class="text-left">Payout Details</h4>
							</div>
							<div class="col-md-3">
								@if ($withdrawal->status)
									<h4 class="text-left">Status : @if ($withdrawal->status == 'Success')<span class="text-green">Success</span>@endif
		                        	@if ($withdrawal->status == 'Pending')<span class="text-blue">Pending</span>@endif
                        			@if ($withdrawal->status == 'Blocked')<span class="text-red">Cancelled</span>@endif</h4>
								@endif
							</div>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<form action="{{ url('admin/withdrawals/update') }}" class="form-horizontal" id="withdrawal_form" method="POST">
								{{ csrf_field() }}

						        <input type="hidden" value="{{ $withdrawal->id }}" name="id" id="id">
						        <input type="hidden" value="{{ $withdrawal->user_id }}" name="user_id" id="user_id">
						        <input type="hidden" value="{{ $withdrawal->currency->id }}" name="currency_id" id="currency_id">
						        <input type="hidden" value="{{ $withdrawal->uuid }}" name="uuid" id="uuid">

						        <input type="hidden" value="{{ $transaction->transaction_type_id }}" name="transaction_type_id" id="transaction_type_id">
						        <input type="hidden" value="{{ $transaction->transaction_type->name }}" name="transaction_type" id="transaction_type">
						        <input type="hidden" value="{{ $transaction->status }}" name="transaction_status" id="transaction_status">
						        <input type="hidden" value="{{ $transaction->transaction_reference_id }}" name="transaction_reference_id" id="transaction_reference_id">


								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

											@if ($withdrawal->amount)
							                    <div class="form-group">
													<label class="control-label col-sm-6" for="amount">Amount</label>
													<input type="hidden" class="form-control" name="amount" value="{{ ($withdrawal->amount) }}">
													<div class="col-sm-6">
													  <p class="form-control-static">{{  moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->amount)) }}</p>
													</div>
												</div>
											@endif



							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="feesTotal">Fees
														<span>
															<small class="transactions-edit-fee">
																@if (isset($transaction))
																({{(formatNumber($transaction->percentage))}}% + {{ formatNumber($withdrawal->charge_fixed) }})
																@else
																	({{0}}%+{{0}})
																@endif
															</small>
														</span>
													</label>

													@php
														$feesTotal = $withdrawal->charge_percentage + $withdrawal->charge_fixed;
													@endphp

													<input type="hidden" class="form-control" name="feesTotal" value="{{ ($feesTotal) }}">
													<div class="col-sm-6">
													  <p class="form-control-static">{{  moneyFormat($withdrawal->currency->symbol, formatNumber($feesTotal)) }}</p>
													</div>
												</div>
											<hr class="increase-hr-height">

											@php
												$total = $withdrawal->amount-$feesTotal ;
											@endphp

											@if (isset($total))
							                    <div class="form-group total-deposit-space">
													<label class="control-label col-sm-6" for="total">Total</label>
													<input type="hidden" class="form-control" name="total" value="{{ ($total) }}">
													<div class="col-sm-6">
													  <p class="form-control-static">{{  moneyFormat($withdrawal->currency->symbol, formatNumber($total)) }}</p>
													</div>
												</div>
											@endif

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

											@if ($withdrawal->user_id)
												<div class="form-group">
													<label class="control-label col-sm-3" for="user">User</label>
													<input type="hidden" class="form-control" name="user" value="{{ isset($withdrawal->user) ? $withdrawal->user->first_name.' '.$withdrawal->user->last_name :"-" }}">
													<div class="col-sm-9">
													  <p class="form-control-static">{{ isset($withdrawal->user) ? $withdrawal->user->first_name.' '.$withdrawal->user->last_name :"-" }}</p>
													</div>
												</div>
											@endif

											@if ($withdrawal->uuid)
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="withdrawal_uuid">Transaction ID</label>
													<input type="hidden" class="form-control" name="withdrawal_uuid" value="{{ $withdrawal->uuid }}">
													<div class="col-sm-9">
													  <p class="form-control-static">{{ $withdrawal->uuid }}</p>
													</div>
												</div>
											@endif

											@if ($withdrawal->currency)
												<div class="form-group">
													<label class="control-label col-sm-3" for="currency">Currency</label>
													<input type="hidden" class="form-control" name="currency" value="{{ $withdrawal->currency->code }}">
													<div class="col-sm-9">
													  <p class="form-control-static">{{ $withdrawal->currency->code }}</p>
													</div>
												</div>
											@endif

											@if ($withdrawal->payment_method)
							                    <div class="form-group">
													<label class="control-label col-sm-3" for="payment_method">Payment Method</label>
													<input type="hidden" class="form-control" name="payment_method" value="{{ ($withdrawal->payment_method->name == "Mts") ? getCompanyName() : $withdrawal->payment_method->name }}">
													<div class="col-sm-9">
													  <p class="form-control-static">{{ ($withdrawal->payment_method->name == "Mts") ? getCompanyName() : $withdrawal->payment_method->name }}</p>
													</div>
												</div>
											@endif

											<div class="form-group">
												<label class="control-label col-sm-3" for="created_at">Date</label>
												<input type="hidden" class="form-control" name="created_at" value="{{ $withdrawal->created_at }}">
												<div class="col-sm-9">
												  <p class="form-control-static">{{ Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A') }}</p>
												</div>
											</div>

					                   		@if ($withdrawal->status)
						                   		<div class="form-group">
													<label class="control-label col-sm-3" for="status">Change Status</label>
													<div class="col-sm-9">
														<select class="form-control select2" name="status" style="width: 60%;">
															<option value="Success" {{ $withdrawal->status ==  'Success'? 'selected':"" }}>Success</option>
															<option value="Pending"  {{ $withdrawal->status == 'Pending' ? 'selected':"" }}>Pending</option>
															<option value="Blocked"  {{ $withdrawal->status == 'Blocked' ? 'selected':"" }}>Cancel</option>
														</select>
													</div>
												</div>
											@endif

										</div>
									</div>
								</div>

								<div class="col-md-6">
								    <div class="panel panel-default">
    									<div class="panel-body">
    								        @if (isset($withdrawal->withdrawal_detail))
    										    @if(!empty($withdrawal->withdrawal_detail->bank_id))
    										        
    										        <?php
    										            $check_bank = DB::table('countries_bank')->where('id', $withdrawal->withdrawal_detail->bank_id)->first();
    										            $check_country = DB::table('countries')->where('id', $check_bank->country_id)->first();
    										        ?>
    										        
    										        <div class="form-group">
    													<label class="control-label col-sm-6" for="account_name">Country</label>
    													<div class="col-sm-6">
    													  <p class="form-control-static">{{ $check_country->name }}</p>
    													</div>
    												</div>
    												
    												<?php
                        
                                                        if(!empty($check_bank)){
                                                            $avail_fields = json_decode($check_bank->bank, true);
    	
                                                        	foreach($avail_fields as $k=>$avail_field){
                                                        	    
                                                        	?>
                                                        	    <div class="form-group">
            														<label class="control-label col-sm-6" for="account_name">{{ $k }}</label>
            														<input type="hidden" class="form-control" name="account_name" value="{{ $avail_field }}">
            														<div class="col-sm-6">
            														  <p class="form-control-static">{{ $avail_field }}</p>
            														</div>
            													</div>
                                                        	<?php
                                                        	}
                                                        }
                                                    ?>
    										        
    										    @elseif ($withdrawal->payment_method->name == 'Bank')
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="account_name">Account Name</label>
    													<input type="hidden" class="form-control" name="account_name" value="{{ $withdrawal->withdrawal_detail->account_name }}">
    													<div class="col-sm-6">
    													  <p class="form-control-static">{{ $withdrawal->withdrawal_detail->account_name }}</p>
    													</div>
    												</div>
    
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="account_number">Account Number/IBAN</label>
    													<input type="hidden" class="form-control" name="account_number" value="{{ $withdrawal->withdrawal_detail->account_number }}">
    													<div class="col-sm-6">
    													  <p class="form-control-static">{{ $withdrawal->withdrawal_detail->account_number }}</p>
    													</div>
    												</div>
    
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="swift_code">SWIFT Code</label>
    													<input type="hidden" class="form-control" name="swift_code" value="{{ $withdrawal->withdrawal_detail->swift_code }}">
    													<div class="col-sm-6">
    													  <p class="form-control-static">{{ $withdrawal->withdrawal_detail->swift_code }}</p>
    													</div>
    												</div>
    
    												<div class="form-group">
    													<label class="control-label col-sm-6" for="bank_name">Bank Name</label>
    													<input type="hidden" class="form-control" name="bank_name" value="{{ $withdrawal->withdrawal_detail->bank_name }}">
    													<div class="col-sm-6">
    													  <p class="form-control-static">{{ $withdrawal->withdrawal_detail->bank_name }}</p>
    													</div>
    												</div>
    											@endif
    										@endif
    								    </div>
    								</div>
    								
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

								@if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_withdrawal'))
    								<div class="row">
    									<div class="col-md-11">
    										<div class="col-md-2"></div>
    										<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="{{ url('admin/withdrawals') }}">Cancel</a></div>
    										<div class="col-md-1">
    											<button type="submit" class="btn button-secondary pull-right" id="withdrawal_edit">
                                                    <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="withdrawal_edit_text">Update</span>
                                                </button>
    										</div>
    									</div>
    								</div>
    							@endif
							</form>
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

	// disabling submit and cancel button after clicking it
	$(document).ready(function()
	{
		$('form').submit(function()
		{
		 	$("#withdrawal_edit").attr("disabled", true);
		 	$('#cancel_anchor').attr("disabled","disabled");
		    $(".fa-spin").show();
		    $("#withdrawal_edit_text").text('Updating...');

		    // Click False
			$('#withdrawal_edit').click(false);
			$('#cancel_anchor').click(false);
		});
	});
</script>
@endpush

