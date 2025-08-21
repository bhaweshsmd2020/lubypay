@extends('admin.layouts.master')

@section('title', 'Edit Store Category')

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
                
                <li class="active">
                  <a href="{{url("admin/store/category/list/" . $store_detail->id)}}">Categories</a>
                </li>
                
                <li>
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
                    <div class="top-bar-title padding-bottom pull-left">{{$store_detail->name}} Categories</div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!-- form start -->
                <form action="{{ url('admin/store/category/update') }}" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="hidden" value="{{ $store_detail->id }}" name="store_id" id="id">
                    <input type="hidden" value="{{ $category->id }}" name="cat_id" id="id">

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Name" name="name" type="text" id="name" value="{{ $category->name }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Description
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Description" name="description" type="text" id="description" value="{{ $category->description }}">
                                        </div>
                                    </div>
                                  
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Image" name="image" type="file" id="image">
                                            @if(!empty($category->image))
                                                <img src="{{ asset('public/user_dashboard/categories/thumb/'. $category->image) }}" style="width: 300px; height: auto;">
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_categories'))
                                        <div class="form-group">
                                            <label class="col-sm-4" for="inputEmail3">
                                            </label>
                                            <div class="col-sm-8">
                                                <a class="btn btn-danger btn-flat" href="{{ url("admin/store/category/list/" . $store_detail->id) }}">
                                                    Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary pull-right btn-flat">
                                                    Update
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
