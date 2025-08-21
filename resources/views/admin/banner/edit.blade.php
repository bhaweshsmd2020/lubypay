@extends('admin.layouts.master')

@section('title', 'Add User')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection
@php 
      $language=DB::table('languages')->where('status','Active')->get();
@endphp
@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                    <div class="box-header with-border text-center">
                      <h3 class="box-title">Edit Banner</h3>
                    </div>
                    <form action="{{ url('admin/banner/update') }}" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                       
                        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                        <input type="hidden" value="{{$banners->banner_id}}" name="id" id="id">
                        <input type="hidden" value="{{$banners->banner_id}}" name="old_banner_image" id="old_banner_image">
                            <div class="box-body">
                                
                                
                                <div class="form-group">

                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Banner
                                    </label>
                                    <div class="col-sm-6">
                                    <?php if($banners->banner_image){?>
                                             <img src="{{url('public/uploads/banner/'.$banners->banner_image)}}" class="img-responsive" style="width:100%;height:150px">
                                            <br>
                                    <?php }?>
                                   
                                        <input type="file" name="banner_image" class="form-control input-file-field">
                                        @if($errors->has('banner_image'))
                                            <span class="error">
                                                {{ $errors->first('banner_image') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Banner Title
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" placeholder="Banner Title" name="banner_title" type="text" id="banner_title" value="{{$banners->banner_title}}">
                                        </input>

                                        @if($errors->has('banner_title'))
                                            <span class="error">
                                                {{ $errors->first('first_name') }}
                                            </span>
                                        @endif

                                    </div>
                                </div>
                                  <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Banner Text
                                    </label>
                                    <div class="col-sm-6">
                                        <textarea class="form-control" name="banner_text" id="banner_text">{{$banners->banner_text}}</textarea>
                                        @if($errors->has('banner_text'))
                                            <span class="error">
                                                {{ $errors->first('banner_text') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                  <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="app_redirect">Select Redirect Type</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="app_redirect" id="app_redirect">
                                            <option value='0' {{($banners->app_redirect=='0') ? 'selected' :''}}>None</option>
                                            <option value='1' {{($banners->app_redirect=='1') ? 'selected' :''}}>App Page</option>
                                            <option value='2' {{($banners->app_redirect=='2') ? 'selected' :''}}>Redirect URL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        App Page
                                    </label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="app_page" id="app_page">
                                            <option value="">Select App Page</option>
                                            @foreach($pages as $page)
                                                <option value='{{$page->id}}' {{($page->id ==$banners->app_page) ? 'selected' :''}}>{{$page->page_name}}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="inputEmail3">
                                        Redirect URL
                                    </label>
                                    <div class="col-sm-6">
                                        <input class="form-control" name="redirect_url" id="redirect_url">{{$banners->redirect_url}}</input>
                                        @if($errors->has('banner_text'))
                                            <span class="error">
                                                {{ $errors->first('banner_text') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                              
                                 <!-- Status -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Position</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="position" id="position">
                                            <option value='Top' {{($banners->position=='Top') ? 'selected' :''}}>Top</option>
                                            <option value='Bottom' {{($banners->position=='Bottom') ? 'selected' :''}}>Bottom</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Platform</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="platform" id="platform">
                                            <option value='ewallet' {{($banners->platform=='ewallet') ? 'selected' :''}}>Ewallet</option>
                                            <option value='mpos' {{($banners->platform=='mpos') ? 'selected' :''}}>Mpos</option>
                                        </select>
                                    </div>
                                </div>


                            <!-- Language -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Language</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="language" id="language" required>
                                            @foreach($language as $value)
                                              <option value="{{$value->id}}" @if($value->id==$banners->language) selected  @endif>{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                

                                <!-- Status -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label require" for="status">Status</label>
                                    <div class="col-sm-6">
                                        <select class="select2" name="status" id="status">
                                            <option value='Active' {{($banners->status=='Active') ? 'selected' :''}}>Active</option>
                                            <option value='Inactive' {{($banners->status=='Inactive') ? 'selected' :''}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                              

                                <div class="box-footer">
                                    <a class="btn btn-danger btn-flat pull-left" href="{{ url('admin/banner') }}" id="users_cancel">Cancel</a>
                                    <button type="submit" class="btn btn-primary pull-right btn-flat" id="banner_create"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">Update</span></button>
                                </div>
                            </div>
                        </input>
                    </form>
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

<script type="text/javascript">

// flag for button disable/enable
var hasPhoneError = false;
var hasEmailError = false;

/**
* [check submit button should be disabled or not]
* @return {void}
*/
function enableDisableButton()
{
    if (!hasPhoneError && !hasEmailError) {
        $('form').find("button[type='submit']").prop('disabled',false);
    } else {
        $('form').find("button[type='submit']").prop('disabled',true);
    }
}

function formattedPhone()
{
    if ($('#phone').val != '')
    {
        var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g,"");
        $("#formattedPhone").val(p);
    }
}

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


$('#user_form').validate({
    rules: {
        first_name: {
            required: true,
            // letters_with_spaces_and_dot: true,
        },
        last_name: {
            required: true,
            // letters_with_spaces: true,
        },
        email: {
            required: true,
            email: true,
        },
        password: {
            required: true,
            minlength: 6,
        },
        password_confirmation: {
            required: true,
            minlength: 6,
            equalTo: "#password",
        },
    },
    messages: {
        password_confirmation: {
            equalTo: "Please enter same value as the password field!",
        },
    },
    submitHandler: function(form)
    {
        $("#users_create").attr("disabled", true);
        $(".fa-spin").show();
        $("#users_create_text").text('Creating...');
        $('#users_cancel').attr("disabled",true);
        form.submit();
    }
});


/*
intlTelInput
 */

$(document).ready(function()
{
    $("#phone").intlTelInput({
        separateDialCode: true,
        nationalMode: true,
        preferredCountries: ["us"],
        autoPlaceholder: "polite",
        placeholderNumberType: "MOBILE",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
    });

    var countryData = $("#phone").intlTelInput("getSelectedCountryData");
    $('#defaultCountry').val(countryData.iso2);
    $('#carrierCode').val(countryData.dialCode);

    $("#phone").on("countrychange", function(e, countryData)
    {
        formattedPhone();
        // log(countryData);
        $('#defaultCountry').val(countryData.iso2);
        $('#carrierCode').val(countryData.dialCode);

        if ($.trim($(this).val()) !== '')
        {
            if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
            {
                // alert('invalid');
                $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                hasPhoneError = true;
                enableDisableButton();
                $('#phone-error').hide();
            }
            else
            {
                $('#tel-error').html('');
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/admin/duplicate-phone-number-check",
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': $.trim($(this).val()),
                        'carrierCode': $.trim(countryData.dialCode),
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        $('#tel-error').html('');
                        $('#phone-error').show();

                        $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                        hasPhoneError = true;
                        enableDisableButton();
                    }
                    else if (response.status == false)
                    {
                        $('#tel-error').show();
                        $('#phone-error').html('');

                        hasPhoneError = false;
                        enableDisableButton();
                    }
                });
            }
        }
        else
        {
            $('#tel-error').html('');
            $('#phone-error').html('');
            hasPhoneError = false;
            enableDisableButton();
        }
    });
});

//Invalid Number Validation - admin create
$(document).ready(function()
{
    $("input[name=phone]").on('blur', function(e)
    {
        formattedPhone();
        if ($.trim($(this).val()) !== '')
        {
            if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
            {
                // alert('invalid');
                $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                hasPhoneError = true;
                enableDisableButton();
                $('#phone-error').hide();
            }
            else
            {
                var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
                var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)

                // console.log(phone);

                var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/admin/duplicate-phone-number-check",
                    dataType: "json",
                    data: {
                        'phone': phone,
                        'carrierCode': pluginCarrierCode,
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        if(phone.length == 0)
                        {
                            $('#phone-error').html('');
                        }
                        else{
                            $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                            hasPhoneError = true;
                            enableDisableButton();
                        }
                    }
                    else if (response.status == false)
                    {
                        $('#phone-error').html('');
                        hasPhoneError = false;
                        enableDisableButton();
                    }
                });
                $('#tel-error').html('');
                $('#phone-error').show();
                hasPhoneError = false;
                enableDisableButton();
            }
        }
        else
        {
            $('#tel-error').html('');
            $('#phone-error').html('');
            hasPhoneError = false;
            enableDisableButton();
        }
    });
});

/*
intlTelInput
 */

// Validate Emal via Ajax
$(document).ready(function()
{
    $("#email").on('blur', function(e)
    {
        var email = $('#email').val();
        $.ajax({
            headers:
            {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: SITE_URL+"/admin/email_check",
            dataType: "json",
            data: {
                'email': email,
            }
        })
        .done(function(response)
        {
            // console.log(response);
            if (response.status == true)
            {
                emptyEmail();
                if (validateEmail(email))
                {
                    $('#email_error').addClass('error').html(response.fail).css("font-weight", "bold");
                    $('#email_ok').html('');
                    hasEmailError = true;
                    enableDisableButton();
                } else {
                    $('#email_error').html('');
                }
            }
            else if (response.status == false)
            {
                emptyEmail();
                if (validateEmail(email))
                {
                    $('#email_error').html('');
                } else {
                    $('#email_ok').html('');
                }
                hasEmailError = false;
                enableDisableButton();
            }

            /**
             * [validateEmail description]
             * @param  {null} email [regular expression for email pattern]
             * @return {null}
             */
            function validateEmail(email) {
              var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
            }

            /**
             * [checks whether email value is empty or not]
             * @return {void}
             */
            function emptyEmail() {
                if( email.length === 0 )
                {
                    $('#email_error').html('');
                    $('#email_ok').html('');
                }
            }
        });
    });
});

// $(document).ready(function()
// {
//     $("#email").on('keyup keypress', function(e)
//     {
//        if (e.type=="keyup" || e.type=="keypress")
//        {
//        }
//     });
// });

</script>
@endpush


