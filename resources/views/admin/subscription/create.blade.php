@extends('admin.layouts.master')
@section('title', 'Add Subscription')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Add Subscription</h3>
                </div>
                <form action="{{ url('admin/store-subscription') }}" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Title
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Title" id="title" name="title" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description" required></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Price
                            </label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" placeholder="Price" id="price" name="price" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="icon">
                                Icon
                            </label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Icon" name="icon" type="file" id="icon" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="duration">
                                Duration
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="duration" name="duration" required>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="featured">
                                Featured
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="featured" name="featured" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_subscriptions'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/subscriptions') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Create</button>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>
    </div>
    
@endsection