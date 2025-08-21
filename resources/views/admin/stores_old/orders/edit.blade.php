@extends('admin.layouts.master')

@section('title', 'Edit Store Order')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')

    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href="{{url("admin/store/edit/" . $store_detail->id)}}">Details</a>
                </li>

                <li>
                  <a href="{{url("admin/store/product/list/" . $store_detail->id)}}">Products</a>
                </li>
                
                <li>
                  <a href="{{url("admin/store/category/list/" . $store_detail->id)}}">Categories</a>
                </li>
                
                <li class="active">
                  <a href="{{url("admin/store/orders/list/" . $store_detail->id)}}">Orders</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">{{$store_detail->name}} Orders</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
					    <div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h4 class="text-left">Order Details</h4>
								</div>
								<div class="col-md-3">
								    <h4 class="text-left">Status :
    									@if($order->status == 'success')
                                            <span class="label label-success">Success</span>
                                        @elseif($order->status == 'cancel')
                                            <span class="label label-warning">Cancelled</span>
                                        @elseif($order->status == 'pending')
                                            <span class="label label-primary">Pending</span>
                                        @elseif($order->status == 'failed')
                                            <span class="label label-danger">Failed</span>
                                        @endif
                                    </h4>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">

						                    <div class="form-group">
												<label class="control-label col-sm-6" for="amount">Amount</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">{{$currency->symbol}}{{$order->subtotal}}</p>
												</div>
											</div>
											
											@if(!empty($order->nfc_fee))
    						                    <div class="form-group">
    												<label class="control-label col-sm-6" for="amount">Card Fee</label>
    												<input type="hidden" class="form-control">
    												<div class="col-sm-6">
    												  <p class="form-control-static">{{$currency->symbol}}{{$order->nfc_fee}}</p>
    												</div>
    											</div>
    										@endif
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Tax</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">{{$currency->symbol}}{{$order->tax}}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Discount</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">{{$currency->symbol}}{{$order->discount}}</p>
												</div>
											</div>

											<hr class="increase-hr-height">

											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Total Amount</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">{{$currency->symbol}}{{$order->total_amount}}</p>
												</div>
											</div>
											
											@if(!empty($order->paid_amount))
    											<div class="form-group">
    												<label class="control-label col-sm-6" for="amount">Paid Amount</label>
    												<input type="hidden" class="form-control">
    												<div class="col-sm-6">
    												  <p class="form-control-static">{{ !empty($paid_currency->symbol) ? $paid_currency->symbol : $currency->symbol}}{{$order->paid_amount}}</p>
    												</div>
    											</div>
    										@endif

										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">

						                    <div class="form-group">
												<label class="control-label col-sm-6" for="amount">Order ID</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">#{{ $order->unique_id }}</p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Payment Method</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												    <p class="form-control-static">
												        @if($order->payment_method_id == '1')
                                                            QR Payment
                                                        @elseif($order->payment_method_id == '2')
                                                            Card Payment
                                                        @endif
												    </p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Payment Status</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												    <p class="form-control-static">
												        @if($order->payment_response == 'success')
                                                            <span class="label label-success">Success</span>
                                                        @elseif($order->payment_response == 'cancel')
                                                            <span class="label label-warning">Cancelled</span>
                                                        @elseif($order->payment_response == 'failed')
                                                            <span class="label label-danger">Failed</span>
                                                        @else
                                                            <span class="label label-primary">Pending</span>
                                                        @endif
												    </p>
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Order Status</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												    <p class="form-control-static">
												        @if($order->status == 'success')
                                                            <span class="label label-success">Success</span>
                                                        @elseif($order->status == 'cancel')
                                                            <span class="label label-warning">Cancelled</span>
                                                        @elseif($order->status == 'pending')
                                                            <span class="label label-primary">Pending</span>
                                                        @elseif($order->status == 'failed')
                                                            <span class="label label-danger">Failed</span>
                                                        @endif
												    </p>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-sm-6" for="amount">Date</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-6">
												  <p class="form-control-static">{{ Carbon\Carbon::parse($order->created_at)->format('d-M-Y h:i A') }}</p>
												</div>
											</div>

										</div>
									</div>
								</div>

								<div class="col-md-6">
								    <div class="panel panel-default">
										<div class="panel-body">
										    
										    <h3 class="text-center">Products</h3>
        										    
										    <div class="form-group" style="margin-bottom: 0px;">
										        <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Product</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
    								                    @foreach($products as $product)
    						                                @foreach($order_products as $k=>$ordpr)
    											                @if($ordpr->product_id == $product->id)
    									                            <tr>
                                                                        <th scope="row">{{++$k}}</th>
                                                                        <td>
                                                                            @if(!empty($product->image))
                                                                                <img src="{{ url('public/user_dashboard/product/thumb/' . $product->image) }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                                                            @else
                                                                                <img src="{{ url('public/user_dashboard/profile/user.png') }}" style="width: 40px; height: 40px; border-radius: 50%;">
                                                                            @endif
                                                                        </td>
                                                                        <td>{{$product->name}}</td>
                                                                        <td>{{$ordpr->qty}}</td>
                                                                    </tr>
    											                @endif
    											            @endforeach
    											        @endforeach
                                                    </tbody>
                                                </table>
											</div>
										</div>
									</div>
									
									<div class="panel panel-default">
										<div class="panel-body">
										    
										    <h3 class="text-center">Customer Details</h3>
        										    
										    <div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Name</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												  <p class="form-control-static">{{$order->customer_name}}</p>
												</div>
											</div>
											
											<div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Email</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												  <p class="form-control-static">{{$order->customer_email}}</p>
												</div>
											</div>
											
											<div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Phone</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												  <p class="form-control-static">{{$order->customer_phone_prefix}}{{$order->customer_phone}}</p>
												</div>
											</div>
											
											<div class="form-group" style="margin-bottom: 0px;">
												<label class="control-label col-sm-4" for="amount">Address</label>
												<input type="hidden" class="form-control">
												<div class="col-sm-8">
												    <p class="form-control-static">
												        @if(!empty($order->customer_address1))
												            {{$order->customer_address1}}{{$order->customer_address2}}, 
												        @endif
												        @if(!empty($order->customer_city))
												            {{$order->customer_city}},
												        @endif
												        @if(!empty($order->customer_state))
												            {{$order->customer_state}},
												        @endif
												        @foreach($countries as $country)
												            @if($order->customer_country == $country->id)
												                {{$country->name}},
												            @endif
												        @endforeach
												        @if(!empty($order->customer_zipcode))
												            {{$order->customer_zipcode}}
												        @endif
												    </p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 text-center">
									<a class="btn btn-danger" href="{{ url("admin/store/orders/list/" . $store_detail->id) }}">Back</a>
									<a class="btn btn-primary" href="{{ url("admin/store/orders/invoice/".$store_detail->id.'/'.$order->id) }}">Print</a>
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

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>

@endpush
