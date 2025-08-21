@extends('admin.layouts.master')
@section('title', 'Add App Pages')
@section('page_content')

    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.appsettings_bar')
        </div>
        <div class="col-md-9">
    
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="top-bar-title padding-bottom pull-left">Edit App Pages</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ url('admin/apppages/update/'. $page->id) }}" method="post" class="form-horizontal" id="api-credentials" enctype="multipart/form-data" >
                                @csrf
        
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">App Page</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" placeholder="App Page" name="app_page" type="text" id="app_page" value="{{ $page->app_page }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">Page Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" placeholder="Page Name" name="page_name" type="text" id="page_name" value="{{ $page->page_name }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">Status</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="status" id="status">
                                            <option value="Active" @if($page->status == 'Active') selected @endif>Active</option>
                                            <option value="Inactive" @if($page->status == 'Inactive') selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="box-footer col-sm-9"></div>
                                    <div class="box-footer col-sm-3">
                                        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_api_credentials'))
                                            <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                                        @endif
                                        <a href="{{url('admin/apppages')}}" class="btn btn-danger btn-flat pull-right" style="margin-right: 10px;">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection