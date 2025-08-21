@extends('user_dashboard.layouts.app')
@section('content')

<style>
    .small-box .icon{
        font-size: 50px;
    }
    
    .small-box:hover .icon{
        font-size: 70px;
    }
</style>

<section class="content">
      <div class="row">
        <!--Graph Line Chart last 30 days start-->
          <div class="col-md-12">
          <!-- LINE CHART -->

                        <div class="box box-body">
                            <div class="row" >
                            @foreach ($product as $products)
                             <div class="col-md-3" style=" margin-bottom:10px">
                                 <a href="{{url('/giftcarddetails/'.$products['productId'])}}" >
                                <!-- small box -->
                                <div class="small-box bg-yellow" style="border: 1px solid;border-radius: 5px;">
                                     <img src="{{$products['logoUrls']['0']}}" ></img>
                                </div>
                                <h3 style="text-align: center;">{{$products['productName']}}</h3>
                                <div <div style="margin: 10px; padding-top: 5px; padding-bottom: 5px; text-align: center; border-radius: 10px; border: 1px solid;">
                                    View Details
                                </div>
                                </a>
                            </div>
                            <br>
                            @endforeach
                            </div>
                            <br>
                        </div>


          </div>
      </div>
      
</section>
@endsection

