@extends('admin.layouts.master')

@section('title', 'Edit Store')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')

    <div class="box">
        <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                  <a href="{{url("admin/store/edit/" . $store->id)}}">Details</a>
                </li>

                <li>
                  <a href="{{url("admin/store/product/list/" . $store->id)}}">Products</a>
                </li>
                
                <li>
                  <a href="{{url("admin/store/category/list/" . $store->id)}}">Categories</a>
                </li>
                
                <li>
                  <a href="{{url("admin/store/orders/list/" . $store->id)}}">Orders</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">{{$store->name}}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!-- form start -->
                <form action="{{ url('admin/store/update') }}" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="hidden" value="{{ $store->id }}" name="id" id="id">

                    <div class="box-body">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Name" name="name" type="text" id="name" value="{{ $store->name }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Description
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Description" name="description" type="text" id="description" value="{{ $store->description }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Address
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Address" name="address" type="text" id="address" value="{{ $store->address }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           City
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="City" name="city" type="text" id="city" value="{{ $store->city }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           State
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="State" name="state" type="text" id="state" value="{{ $store->state }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                            Country
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="country" id="country">
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}" @if($country->id == $store->country) selected @endif>{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Postal Code
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Postal Code" name="postalcode" type="text" id="postalcode" value="{{ $store->postalcode }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Tax
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Tax" name="tax" type="text" id="tax" value="{{ $store->tax }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="inputEmail3">
                                           Image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Image" name="image" type="file" id="image">
                                            @if(!empty($store->image))
                                                <img src="{{ asset('public/uploads/store/'. $store->image) }}">
                                            @endif
                                        </div>
                                    </div>
                                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_stores'))
                                        <div class="form-group">
                                            <label class="col-sm-4" for="inputEmail3">
                                            </label>
                                            <div class="col-sm-8">
                                                <a class="btn btn-danger btn-flat" href="{{ url('admin/store-list') }}">
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
