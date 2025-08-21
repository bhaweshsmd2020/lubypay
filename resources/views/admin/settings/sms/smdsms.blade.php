@extends('admin.layouts.master')

@section('title', 'SMS Settings')



@section('head_style')

    <!-- bootstrap-toggle -->

    <link rel="stylesheet" href="{{ asset('public/backend/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">

@endsection



@section('page_content')

    <!-- Main content -->

    <div class="row">

        <div class="col-md-12">

            <div class="box box-info">

                <div class="nav-tabs-custom">

                    <ul class="nav nav-tabs" id="tabs">

                        <li><a href="{{ url('admin/settings/sms/twilio') }}">Twilio</a></li>

                        <li><a href="{{ url('admin/settings/sms/nexmo')}}">Nexmo</a></li>
                        <li><a href="{{ url('admin/settings/sms/oneway')}}">Oneway</a></li>
                        <li class="active"><a href="{{ url('admin/settings/sms/smdsms')}}">SMD SMS</a></li>

                    </ul>



                    <div class="tab-content">

                        <div class="tab-pane fade in active" id="tab_1">

                            <div class="card">

                                <div class="card-header">

                                    <h4></h4>

                                </div>

                                <div class="container-fluid">

                                    <div class="tab-pane" id="tab_3">
                                        <form action="{{ url('admin/settings/sms/smdsms') }}" method="POST" class="form-horizontal" id="smd_sms_setting_form">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="type" value="{{ base64_encode($smdsms->type) }}">
                                            <div class="box-body">



                                                {{-- Name --}}

                                                <div class="form-group" style="display: none;">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Name</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="name" class="form-control" value="{{ $smdsms->type == 'smdsms' ? 'SMD SMS' : '' }}" placeholder="Enter SMD Sms Gateway Name" id="" readonly>

                                                            @if ($errors->has('name'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('name') }}</span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>
                                                
                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Key</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="smdsms[key]" class="form-control" value="{{ isset($credentials->key) ? $credentials->key : '' }}" placeholder="Enter SMD SMS key" id="">

                                                            @if ($errors->has('smdsms.key'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('smdsms.key') }}</span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>


                                                {{-- Key --}}

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Account SID</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="smdsms[account_sid]" class="form-control" value="{{ isset($credentials->account_sid) ? $credentials->account_sid : '' }}" placeholder="Enter SMD sms Account SID" id="">

                                                            @if ($errors->has('smdsms.account_sid'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('smdsms.account_sid') }}</span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">User name</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="smdsms[username]" class="form-control" value="{{ isset($credentials->username) ? $credentials->username : '' }}" placeholder="Enter SMD SMS user name" id="">

                                                            @if ($errors->has('smdsms.username'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('smdsms.username') }}</span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>
                                                
                                                
                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Password</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="smdsms[password]" class="form-control" value="{{ isset($credentials->password) ? $credentials->password : '' }}" placeholder="Enter SMD SMS password" id="">

                                                            @if ($errors->has('smdsms.password'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('smdsms.password') }}</span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>


                                                {{-- Secret --}}

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Auth Token</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="smdsms[auth_token]" class="form-control" value="{{ isset($credentials->auth_token) ? $credentials->auth_token : '' }}" placeholder="Enter smdsms Auth Token" id="">

                                                            @if ($errors->has('smdsms.auth_token'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('smdsms.auth_token') }}</span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>



                                                {{-- Secret --}}

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Default Phone Number</label>

                                                        <div class="col-md-8">

                                                            <input type="text" name="smdsms[default_smdsms_phone_number]" class="form-control"

                                                            value="{{ isset($credentials->default_smdsms_phone_number) ? $credentials->default_smdsms_phone_number : '' }}" placeholder="Enter smdsms Default Phone Number" id="">

                                                            @if ($errors->has('smdsms.default_smdsms_phone_number'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('smdsms.default_smdsms_phone_number') }}</span>

                                                            @endif

                                                            <div class="clearfix"></div>

                                                            <h6 class="form-text text-muted"><strong>*Must enter phone number without(+) symbol.</strong></h6>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>



                                                {{-- Status --}}

                                                <div class="form-group">

                                                    <div class="col-md-10 col-md-offset-1">

                                                        <label class="col-md-3 control-label">Status</label>

                                                        <div class="col-md-8">

                                                            <select name="status" class="select2 select2-hidden-accessible" id="">

                                                                <option {{ $smdsms->status == 'Active' ? 'selected' : '' }} value="Active">Active</option>

                                                                <option {{ $smdsms->status == 'Inactive' ? 'selected' : '' }} value="Inactive">Inactive</option>

                                                            </select>

                                                            @if ($errors->has('status'))

                                                                <span style="color:red;font-weight:bold;">{{ $errors->first('status') }}</span>

                                                            @endif

                                                            <div class="clearfix"></div>

                                                            <h6 class="form-text text-muted"><strong>*Incoming SMS messages might be delayed by {{ ucfirst($smdsms->type) }}.</strong></h6>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="clearfix"></div>

                                            </div>



                                            <div class="row">

                                                <div class="col-md-12">

                                                    <div style="margin-top:10px">

                                                        <a id="cancel_anchor" href="{{ url('admin/settings/sms/smdsms') }}" class="btn btn-danger btn-flat">Cancel</a>

                                                        <button type="submit" class="btn btn-primary pull-right btn-flat" id="sms-settings-smdsms-submit-btn">

                                                            <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="sms-settings-smdsms-submit-btn-text">Update</span>

                                                        </button>

                                                    </div>

                                                </div>

                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection



@push('extra_body_scripts')



    <!-- jquery.validate -->

    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>



    <script type="text/javascript">



        $(function () {

            $(".select2").select2({

            });

        });



        $.validator.setDefaults({

            highlight: function(element) {

                $(element).parent('div').addClass('has-error');

            },

            unhighlight: function(element) {

                $(element).parent('div').removeClass('has-error');

            },

            errorPlacement: function (error, element) {

                error.insertAfter(element);

            }

        });





        $('#smdsms_sms_setting_form').validate({

            rules: {

                "smdsms[account_sid]": {

                    required: true,

                },

                "smdsms[auth_token]": {

                    required: true,

                },

                "smdsms[default_smdsms_phone_number]": {

                    required: true,

                    digits: true,

                },

            },

            messages: {

                "smdsms[account_sid]": {

                    required: "smdsms Account SID is required!",

                },

                "smdsms[auth_token]": {

                    required: "smdsms Auth Token is required!",

                },

                "smdsms[default_smdsms_phone_number]": {

                    required: "smdsms Default Phone Number is required",

                },

            },

            submitHandler: function(form)

            {

                $("#sms-settings-smdsms-submit-btn").attr("disabled", true);

                $(".fa-spin").show();

                $("#sms-settings-smdsms-submit-btn-text").text('Updating...');

                $('#cancel_anchor').attr("disabled",true);

                $('#sms-settings-smdsms-submit-btn').click(false);

                form.submit();

            }

        });



    </script>

@endpush

