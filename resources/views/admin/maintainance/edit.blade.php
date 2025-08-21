@extends('admin.layouts.master')
@section('title', 'Edit Maintenance Settings')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Maintenance Settings</h3>
                </div>
                <form action="{{ url('admin/update-maintainance-settings', $setting->id) }}" class="form-horizontal" method="POST" id="user_form">
                    @csrf
                    <div class="box-body">
                        <!--<div class="form-group">-->
                        <!--    <label class="col-sm-3 control-label require" for="subject">-->
                        <!--        Subject-->
                        <!--    </label>-->
                        <!--    <div class="col-sm-6">-->
                        <!--        <input type="text" class="form-control" value="{{ $setting->subject }}" placeholder="Subject" id="subject" name="subject">-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="user_type">
                                Send To
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="user_type" name="user_type">
                                    <option value="1" @if($setting->user_type == '1') selected @endif>Ewallet User</option>
                                    <option value="2" @if($setting->user_type == '2') selected @endif>Merchant</option>
                                    <option value="3" @if($setting->user_type == '3') selected @endif>Both</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="date">
                                Date
                            </label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" value="{{ $setting->date }}" placeholder="Date" id="date" name="date">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="from_time">
                                From Time
                            </label>
                            <div class="col-sm-6">
                                <input type="time" class="form-control" value="{{ $setting->from_time }}" placeholder="From Time" id="from_time" name="from_time">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="to_time">
                                To Time
                            </label>
                            <div class="col-sm-6">
                                <input type="time" class="form-control" value="{{ $setting->to_time }}" placeholder="To Time" id="to_time" name="to_time">
                            </div>
                        </div>
                        
                        @foreach($languages as $language)
                            <?php 
                                $message = 'message_'.$language['short_name'];
                            ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label require" for="message">
                                    Message ({{$language->name}})
                                </label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" placeholder="Message" id="message" name="message_{{$language->short_name}}">{{ $setting->$message }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/maintainance-settings') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>
    </div>
    
@endsection