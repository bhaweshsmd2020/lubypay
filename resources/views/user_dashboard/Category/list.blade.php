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
                        <a href="{{url('/categories/add')}}" class="btn btn-cust ticket-btn" style="padding: 2px 10px;"><i class="fa fa-shopping-basket"></i>&nbsp;
                            New category</a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            
                        </div>

                        <div class="table-responsive">
                            @if($list->count() > 0)
                                <table class="table recent_activity">
                                    <thead>
                                    <tr>
                                        <td><strong>ID</strong></td>
                                        <td><strong>Name</strong></td>
                                        <td><strong>@lang('message.dashboard.product.table.description')</strong></td>
                                         <td><strong>Image</strong></td>
                                        <td><strong>@lang('message.dashboard.product.table.action')</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @php $i=1@endphp
                                    @foreach($list as $result)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $result->name}} </td>
                                            <td>{{ (isset($result->description)) ? ucfirst($result->description) : '-' }} </td>
                                            <td>
                                                <?php if(!empty($result->image)) {?>
                                                <img src="{{url('public/user_dashboard/categories/thumb/'.$result->image)}}" width="50" height="50" id="merchant-logo-preview">
                                                <?php }?>
                                            </td>
                                            <td>
                                                
                                                <a href="{{url('categories/edit/'.$result->id)}}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @php $i++ @endphp
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
