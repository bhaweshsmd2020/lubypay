@extends('admin.layouts.master')
@section('title', 'Add Promotions')

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <form action="{{ url('admin/promotions/store') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-header with-border text-center">
                        <h3 class="box-title">Send Notifications</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">User Type</label>
                            <div class="col-sm-6">
                                <select class="select2" name="user_type" id="getFname" onchange="admSelectCheck(this);" required="">
                                    <option id="samOption" value="ewallet_user">Ewallet User</option>
                                    <option id="admOption" value="merchant">Merchant</option>
                                </select>
                            </div>
                        </div>
                         
                        <div class="form-group" id="admDivCheck" style="display:none;">
                            <label class="col-sm-3 control-label require">Merchants</label>
                            <div class="col-sm-6">
                                <select class="select2" name="merchant" id="merchant" required="">
                                    <option value="All">All</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{$merchant->id}}">{{$merchant->first_name}} {{$merchant->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group" id="samDivCheck">
                            <label class="col-sm-3 control-label require">Users</label>
                            <div class="col-sm-6">
                                <select class="select2" name="user" id="user" required="">
                                    <option value="All">All</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Title</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Title" name="title" type="text" id="title" required="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Subject</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Subject" name="subject" type="text" id="subject" required="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Type of Notification</label>
                            <div class="col-sm-6">
                                <select class="select2" name="type" id="type" required="">
                                    <option value="Email">Email</option>
                                    <option value="Notification">Notification</option>
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
                                    <option value='0'>None</option>
                                    <option value='1'>App Page</option>
                                    <option value='2'>Redirect URL</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">App Page</label>
                            <div class="col-sm-6">
                                <select class="select2" name="app_page" id="app_page">
                                    <option>Select App Page</option>
                                    @foreach($pages as $page)
                                        <option value='{{$page->id}}'>{{$page->page_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Redirect URL</label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Redirect URL" name="redirect_url" type="text" id="redirect_url">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require">Description</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" name="description" type="text" id="description" required=""></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Language</label>
                            <div class="col-sm-6">
                                <select class="select2" name="language" id="language" required>
                                    @foreach($language as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/promotions') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Create</span></button>
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
        
        function admSelectCheck(nameSelect)
        {
            if(nameSelect){
                admOptionValue = document.getElementById("admOption").value;
                if(admOptionValue == nameSelect.value){
                    document.getElementById("admDivCheck").style.display = "block";
                }
                else{
                    document.getElementById("admDivCheck").style.display = "none";
                }
        
                samOptionValue = document.getElementById("samOption").value;
                if(samOptionValue == nameSelect.value){
                    document.getElementById("samDivCheck").style.display = "block";
                }
                else{
                    document.getElementById("samDivCheck").style.display = "none";
                }
            }
            else{
                document.getElementById("admDivCheck").style.display = "none";
                document.getElementById("samDivCheck").style.display = "none";
            }
        }
    </script>
@endpush