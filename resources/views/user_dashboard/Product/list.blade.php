@extends('user_dashboard.layouts.app')

@section('css')
    <style>
    .marginTopPlus {
    margin-top: 0px!important;
}
        @media only screen and (max-width: 259px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
        
        .ticket-btn {
    /* border: 2px solid #7d95b6; */
    border-radius: 2px;
    color: #ffffff!important;
    background-color: #f7ab33!important;
}
    </style>
@endsection

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                @include('user_dashboard.layouts.common.tab')
                    
                    
                    @include('user_dashboard.layouts.common.alert')

                    <div class="right mb10">
                        <a href="{{url('/product/add')}}" class="btn btn-cust ticket-btn" style="padding: 2px 10px;"><i class="fa fa-shopping-basket"></i>&nbsp;
                            @lang('message.dashboard.button.new-product')</a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li class="active"><a href="{{url('/products')}}">@lang('message.dashboard.product.menu.product')</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-responsive">
                            @if($list->count() > 0)
                                <table class="table recent_activity">
                                    <thead>
                                    <tr>
                                        <td><strong>@lang('message.dashboard.product.table.id')</strong></td>
                                        <td><strong>Category</strong></td>
                                        <!--<td><strong>Attibutes</strong></td>-->
                                        <td><strong>@lang('message.dashboard.product.table.product-name')</strong></td>
                                        <td><strong>@lang('message.dashboard.product.table.product-price')</strong></td>
                                        <td><strong>@lang('message.dashboard.product.table.description')</strong></td>
                                        <!--<td><strong>@lang('message.dashboard.product.table.unique-url')</strong></td>-->
                                        <td><strong>@lang('message.dashboard.product.table.action')</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($list as $result)
                                        <tr>
                                            <td>{{ $result->user_product_id }}</td>
                                             <td>{{ ($result->category) ? $result->category->name :'-'}} </td>
                                             
                                             <!--<td>-->
                                                 <?php 
                                                 
                                                //  $product_attributes = DB::table('product_attributes')->where('product_id',$result->id)->get();
                                                //  if(isset($product_attributes) && count($product_attributes))
                                                //  {
                                                //      foreach($product_attributes as $pattr)
                                                //      {
                                                //          $attrdetails = DB::table('attributes')->where('id',$pattr->attributes)->first();
                                                //          if($attrdetails)
                                                //          {
                                                //              echo "<b>".$attrdetails->name." : </b>";
                                                //                 echo "<br>"; 
                                                //          }
                                                        
                                                //          $attrvaluess = $pattr->attributes_values;
                                                //          if($attrvaluess)
                                                //          {
                                                //              $attrvaluess_array = json_decode($attrvaluess,true);
                                                //              foreach($attrvaluess_array as $val)
                                                //              {
                                                                 
                                                //                  $valuedtls = DB::table('attribute_values')->where('attribute_id',$pattr->attributes)->first();
                                                //                  if($valuedtls)
                                                //                  {
                                                //                       echo  $valuedtls->value;
                                                //                 echo ",";
                                                //                  }
                                                               
                                                //              }
                                                //              echo "<br>";
                                                //          }
                                                         
                                                         
                                                //      }
                                                     
                                                    
                                                //  }
                                                //  else
                                                //  {
                                                //      echo "-";
                                                //  }
                                                 
                                                 ?>
                                                 
                                             <!--</td>-->
                                             
                                             
                                            <td>{{ $result->name}} </td>
                                            <td>{{ formatNumber($result->price) }} </td>
                                            <td>{{ (isset($result->description)) ? ucfirst($result->description) : '-' }} </td>
                                            <!--<td><a href="{{ $result->url }}" > {{ $result->url }} </a></td>-->
                                            
                                            <td>
                                                <a href="{{url('product/detail/'.$result->id)}}" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i></a>
                                                <a href="{{url('product/edit/'.$result->id)}}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                        </div>
                        @else
                            <h5 style="padding:15px 10px;">@lang('message.dashboard.product.table.not-found')</h5>
                        @endif


                        <div class="card-footer">
                            {{ $list->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
@endsection
