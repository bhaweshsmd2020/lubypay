@extends('admin.layouts.master')
@section('title', 'Edit Deposit')
@section('page_content')

<?php
    $check_transaction = DB::table('transactions')->where('transaction_reference_id', $deposit->id)->where('payment_method_id', '2')->first();
    $transaction_details = DB::table('trans_device_info')->where('trans_id', $check_transaction->id)->first();
    $ip_details = json_decode(file_get_contents("http://ipinfo.io/{$deposit->ip_address}/json"));
    
    if(!empty($deposit->local_tran_time)){
	    $trans_time = $deposit->local_tran_time;
	}else{
	    $trans_time = $deposit->created_at;
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
									<h4 class="text-left">Deposit Details</h4>
								</div>
								<div class="col-md-3">
									@if ($deposit->status)
										<h4 class="text-left">Status : @if ($deposit->status == 'Success')<span class="text-green">Success</span>@endif
				                    	@if ($deposit->status == 'Pending')<span class="text-blue">Pending</span>@endif
				            			@if ($deposit->status == 'Blocked')<span class="text-red">Cancelled</span>@endif</h4>
									@endif
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<form action="{{ url('admin/deposits/update') }}" class="form-horizontal" id="deposit_form" method="POST">
										{{ csrf_field() }}
							        <input type="hidden" value="{{ $deposit->id }}" name="id" id="id">
							        <input type="hidden" value="{{ $deposit->user_id }}" name="user_id" id="user_id">
							        <input type="hidden" value="{{ $deposit->currency->id }}" name="currency_id" id="currency_id">
							        <input type="hidden" value="{{ $deposit->uuid }}" name="uuid" id="uuid">
							        <input type="hidden" value="{{ ($deposit->charge_percentage)  }}" name="charge_percentage" id="charge_percentage">
							        <input type="hidden" value="{{ ($deposit->charge_fixed)  }}" name="charge_fixed" id="charge_fixed">

							        <input type="hidden" value="{{ $transaction->transaction_type_id??'' }}" name="transaction_type_id" id="transaction_type_id">
							        <input type="hidden" value="{{ $transaction->transaction_type->name??'' }}" name="transaction_type" id="transaction_type">
							        <input type="hidden" value="{{ $transaction->status??'' }}" name="transaction_status" id="transaction_status">
							        <input type="hidden" value="{{ $transaction->transaction_reference_id??'' }}" name="transaction_reference_id" id="transaction_reference_id">

									<div class="col-md-6">
									    <div class="panel panel-default">
											<div class="panel-body">

												@if ($deposit->amount)
								                    <div class="form-group">
														<label class="control-label col-sm-6" for="amount">Amount</label>
														<input type="hidden" class="form-control" name="amount" value="{{ ($deposit->amount) }}">
														<div class="col-sm-6">
														  <p class="form-control-static">{{  moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)) }}</p>
														</div>
													</div>
												@endif

							                    <div class="form-group total-deposit-feesTotal-space">
													<label class="control-label col-sm-6" for="feesTotal">Fees
														<span>
															<small class="transactions-edit-fee">
																@if (isset($transaction))
																({{(formatNumber($transaction->percentage??''))}}% + {{ formatNumber($deposit->charge_fixed) }})
																@else
																	({{0}}%+{{0}})
																@endif
															</small>
														</span>
													</label>

													@php
														$feesTotal = $deposit->charge_percentage + $deposit->charge_fixed;
													@endphp

													<input type="hidden" class="form-control" name="feesTotal" value="{{ ($feesTotal) }}">

													<div class="col-sm-6">
													  <p class="form-control-static">{{  moneyFormat($deposit->currency->symbol, formatNumber($feesTotal)) }}</p>
													</div>
												</div>

												<hr class="increase-hr-height">

												@php
													$total = $feesTotal + $deposit->amount;
												@endphp

												@if (isset($total))
								                    <div class="form-group total-deposit-space">
														<label class="control-label col-sm-6" for="total">Total</label>
														<input type="hidden" class="form-control" name="total" value="{{ ($total) }}">
														<div class="col-sm-6">
														  <p class="form-control-static">{{  moneyFormat($deposit->currency->symbol, formatNumber($total)) }}</p>
														</div>
													</div>
												@endif

											</div>
										</div>
										
										<div class="panel panel-default">
											<div class="panel-body">

												@if ($deposit->user_id)
													<div class="form-group">
														<label class="control-label col-sm-3" for="user">User</label>
														<input type="hidden" class="form-control" name="user" value="{{ isset($deposit->user) ? $deposit->user->first_name.' '.$deposit->user->last_name :"-" }}">
														<div class="col-sm-9">
														  <p class="form-control-static">{{ isset($deposit->user) ? $deposit->user->first_name.' '.$deposit->user->last_name :"-" }}</p>
														</div>
													</div>
												@endif

												@if ($deposit->uuid)
								                    <div class="form-group">
														<label class="control-label col-sm-3" for="deposit_uuid">Transaction ID</label>
														<input type="hidden" class="form-control" name="deposit_uuid" value="{{ $deposit->uuid }}">
														<div class="col-sm-9">
														  <p class="form-control-static">{{ $deposit->uuid }}</p>
														</div>
													</div>
												@endif

												@if ($deposit->currency)
													<div class="form-group">
														<label class="control-label col-sm-3" for="currency">Currency</label>
														<input type="hidden" class="form-control" name="currency" value="{{ $deposit->currency->code }}">
														<div class="col-sm-9">
														  <p class="form-control-static">{{ $deposit->currency->code }}</p>
														</div>
													</div>
												@endif

												@if ($deposit->payment_method)
								                    <div class="form-group">
														<label class="control-label col-sm-3" for="payment_method">Payment Method</label>
														<input type="hidden" class="form-control" name="payment_method" value="{{ ($deposit->payment_method->name == "Mts") ? getCompanyName() : $deposit->payment_method->name }}">
														<div class="col-sm-9">
														  <p class="form-control-static">
														      @if($deposit->payment_method->name == "Mts")
    													          {{ getCompanyName() }}
    													      @elseif($deposit->payment_method->name == "Stripe")
    													          Debit/Credit Card
    													      @else
    													          {{ $deposit->payment_method->name }}
    													      @endif
														  </p>
														</div>
													</div>
												@endif

												@if ($deposit->bank)
								                    <div class="form-group">
														<label class="control-label col-sm-3" for="bank_name">Bank Name</label>
														<input type="hidden" class="form-control" name="bank_name" value="{{ $deposit->bank->bank_name }}">
														<div class="col-sm-9">
														  <p class="form-control-static">{{ $deposit->bank->bank_name }}</p>
														</div>
													</div>

								                    <div class="form-group">
														<label class="control-label col-sm-3" for="bank_branch_name">Branch Name</label>
														<input type="hidden" class="form-control" name="bank_branch_name" value="{{ $deposit->bank->bank_branch_name }}">
														<div class="col-sm-9">
														  <p class="form-control-static">{{ $deposit->bank->bank_branch_name }}</p>
														</div>
													</div>

								                    <div class="form-group">
														<label class="control-label col-sm-3" for="account_name">Account Name</label>
														<input type="hidden" class="form-control" name="account_name" value="{{ $deposit->bank->account_name }}">
														<div class="col-sm-9">
														  <p class="form-control-static">{{ $deposit->bank->account_name }}</p>
														</div>
													</div>
												@endif

												@if ($deposit->file)
													<div class="form-group">
														<label class="control-label col-sm-3" for="attached_file">Attached File</label>
														<div class="col-sm-9">
														  <p class="form-control-static">
										                  	<a href="{{ url('public/uploads/files/bank_attached_files').'/'.$deposit->file->filename }}" download={{ $deposit->file->filename }}><i class="fa fa-fw fa-download"></i>
											                  	{{ $deposit->file->originalname }}
											                  </a>
														  </p>
														</div>
													</div>
												@endif

												<div class="form-group">
    												<label class="control-label col-sm-3" for="created_at">Date</label>
    												<input type="hidden" class="form-control" name="created_at" value="{{ $deposit->created_at }}">
    												<div class="col-sm-9">
    												  <p class="form-control-static">{{ Carbon\Carbon::parse($trans_time)->format('d-M-Y h:i A') }}</p>
    												</div>
    											</div>

						                   		@if ($deposit->status)
							                   		<div class="form-group">
														<label class="control-label col-sm-3" for="status">Change Status</label>
														<div class="col-sm-9">
															<select class="form-control select2" name="status" style="width: 60%;">
																<option value="Success" {{ $deposit->status ==  'Success'? 'selected':"" }}>Success</option>
																<option value="Pending"  {{ $deposit->status == 'Pending' ? 'selected':"" }}>Pending</option>
																<option value="Blocked"  {{ $deposit->status == 'Blocked' ? 'selected':"" }}>Cancel</option>
															</select>
														</div>
													</div>
												@endif

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
											<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="{{ url('admin/deposits') }}">Cancel</a></div>
											<div class="col-md-1">
												<button type="submit" class="btn button-secondary pull-right" id="deposits_edit">
	                                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposits_edit_text">Update</span>
	                                            </button>
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
