@extends('admin.layouts.master')
@section('title', 'SMS Reminder')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Maintenance SMS Reminder</h3>
                </div>
                <form action="{{ url('admin/remind-maintainance-settings-sms-send', $ms_id) }}" class="form-horizontal" method="POST" id="user_form">
                    @csrf
                    <div class="box-body">
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="subject">
                                Subject
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" value="{{ $subject }}" placeholder="Subject" id="subject" name="subject">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="message">
                                Message
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" value="{{ $message }}" placeholder="Message" id="message" name="message">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="user_type">
                                Send To
                            </label>
                            <div class="col-sm-6">
                                <select class="select2" id="user_type" name="user">
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}} ({{$user->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_app_level'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/maintainance-settings') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Send</button>
                        </div>
                    @endif
                    
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