@extends('user_dashboard.layouts.app')
@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-xs-12 mb20 marginTopPlus">
                    <div class="card">
                        <div class="card-header">
                             @include('user_dashboard.layouts.common.alert')
                            <div class="chart-list float-left">
                                <ul>
                                    <li class="active">Details
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Order ID</h4>
                                        <p>{{ $orders->unique_id }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Customer Name</h4>
                                        <p>{{ $orders->customer_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Email</h4>
                                        <p>{{ $orders->customer_email }}</p>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Address</h4>
                                        <p>{{$orders->customer_address1}} ,
                                            {{$orders->customer_address2}},
                                             {{$orders->customer_city}},
                                              {{$orders->customer_state}},
                                               {{$orders->customer_country}}-
                                               {{$orders->customer_zipcode}},
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">@lang('message.dashboard.merchant.details.date')</h4>
                                        <p>{{ dateFormat($orders->create_at) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Products</h4>
                                        <p><?php $productarray = json_decode($orders->products,true);
                                                
                                                foreach($productarray as $product)
                                                {
                                                   $productdata = DB::table('product')->where('id',$product['product_id'])->first();
                                                   if($productdata)
                                                   {
                                                       echo 'Name : '.$productdata->name .', Qty :'.$product['qty'];
                                                       echo ",";
                                                   }
                                                }
                                            
                                        ?>
                                            
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Total Amount</h4>
                                        <p>{{ $orders->total_amount }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 style="    font-size: 13px;
    padding-bottom: 4px;
    color: grey;">Status</h4>
                                        <p>{{ $orders->status }}
                                            
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            

                            <div class="row">
                                <div class="col-md-6 pull-left">
                                    <!--<a class="btn btn-cust" href="{{url('orders/changestatus/'.$orders->id)}}" id="changestataus">@lang('message.form.edit')</a>-->
                                    <button class="btn btn-cust" id="changestataus" data-id="<?=$orders->id?>">Change Order Status</button>
                                </div>
                                
                                <div class="col-md-6 pull-right">
                                    <a class="btn btn-cust pull-right" href="{{url('orders')}}">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div id="merchantModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Order Status</h4>
                        <button type="button" class="close" data-dismiss="modal" id="form-modal-cross">&times;</button>
                    </div>
                    <form action="{{url('orders/changestatus')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="merchant_add_form">
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
           
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="form-group">
                                    <input type="hidden" name="order_id" id="order_id" value="<?=$orders->id?>"/>
                                </div>
                                
                                <div class="form-group">
                                    <label>Order Status</label>
                                    <select class="form-control" name="status">
                                        <option value="success" <?=($orders->status=='success') ?'selected' : ''?>>Success</option>
                                        <option value="cancel" <?=($orders->status=='cancel') ?'selected' : ''?>>Cancel</option>
                                        <option value="failed" <?=($orders->status=='failed') ?'selected' : ''?>>Failed</option>
                                        <option value="pending" <?=($orders->status=='pending') ?'selected' : ''?>>Pending</option>
                                    </select>
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary mr-auto standard-payment-form-close" data-dismiss="modal" id="form-modal-close">
                            @lang('message.dashboard.merchant.html-form-generator.close')
                        </button>
                        <button type="submit" class="btn btn-secondary" id="">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        
    </section>
    <!--End Section-->
@endsection
@section('js')
    <script>
      $(document).on('click','#changestataus',function(e)
        {
            e.preventDefault();
            var order_id = $(this).attr('data-id');
            console.log(order_id);
            $('#merchantModal').modal('show');
            
        });
    </script>
@endsection