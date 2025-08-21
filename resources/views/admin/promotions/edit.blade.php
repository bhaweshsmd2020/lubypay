@extends('admin.layouts.master')
@section('title', 'Promotion Detail')

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <form class="form-horizontal">
                    <div class="box-header with-border text-center">
                        <h3 class="box-title">Promotion Detail</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Merchants</label>
                            <div class="col-sm-6">
                                <select class="select2" name="user_type" id="user_type" required="">
                                    <option value="All" @if($promotion->user_type == 'All') selected @endif>All</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{$merchant->id}}" @if($promotion->user_type == $merchant->id) selected @endif>{{$merchant->first_name}} {{$merchant->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Title</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Title" name="title" type="text" id="title" value="{{$promotion->title}}" required="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Subject</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Subject" name="subject" type="text" id="subject" value="{{$promotion->subject}}" required="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Type of Notification</label>
                            <div class="col-sm-6">
                                <select class="select2" name="type" id="type" required="">
                                    <option value="Email" @if($promotion->type == 'Email') selected @endif>Email</option>
                                    <option value="Notification" @if($promotion->type == 'Notification') selected @endif>Notification</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Image</label>
                            <div class="col-sm-6">
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Select Redirect Type</label>
                            <div class="col-sm-6">
                                <select class="select2" name="app_redirect" id="app_redirect" required="">
                                    <option value='0' @if($promotion->app_redirect == '0') selected @endif>None</option>
                                    <option value='1' @if($promotion->app_redirect == '1') selected @endif>App Page</option>
                                    <option value='2' @if($promotion->app_redirect == '2') selected @endif>Redirect URL</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">App Page</label>
                            <div class="col-sm-6">
                                <select class="select2" name="app_page" id="app_page">
                                    <option>Select App Page</option>
                                    @foreach($pages as $page)
                                        <option value='{{$page->id}}' @if($promotion->app_page == $page->id) selected @endif>{{$page->page_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Redirect URL</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Redirect URL" name="redirect_url" type="text" id="redirect_url" value="{{$promotion->redirect_url}}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Description</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" name="description" type="text" id="description" required="">{{$promotion->description}}</textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Language</label>
                            <div class="col-sm-6">
                                <select class="select2" name="language" id="language" required>
                                    @foreach($language as $value)
                                        <option value="{{$value->id}}"  @if($promotion->language == $value->id) selected @endif>{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/promotions') }}">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script type="text/javascript">
        $(function () {
            $(".select2").select2({
            });
        });
    </script>
@endpush