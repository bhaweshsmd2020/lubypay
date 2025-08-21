@extends('admin.layouts.master')
@section('title', 'Add Survey')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Add Survey</h3>
                </div>
                <form action="{{ url('admin/store-survey') }}" class="form-horizontal" method="POST" id="user_form">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Url
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Url" id="url" name="url">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="user_type">
                                Send To
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="user_type" name="user_type">
                                    <option value="1">Ewallet User</option>
                                    <option value="2">Merchant</option>
                                    <option value="3">Both</option>
                                </select>
                            </div>
                        </div>
                        
                        @foreach($languages as $language)
                            <div class="form-group">
                                <label class="col-sm-3 control-label require" for="message">
                                    Message ({{$language->name}})
                                </label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" placeholder="Message" id="message" name="message_{{$language->short_name}}"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/survey') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Create</button>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>
    </div>
    
@endsection