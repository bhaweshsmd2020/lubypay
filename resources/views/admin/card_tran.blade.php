@extends('admin.layouts.master')

@section('title', 'Edit Profile')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')
 <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                   
                  
                 <a href="{{url('admin/card/user-carddetail')}}/60">Open</a>
                 
                </li>
                
                 <li>
                  <a href="">Pause</a>
                </li>
                <li>
                  <a href="">Delete</a>
                </li>
                <li>
                
                 <a href="{{url('admin/card/user-transactions')}}/60">Transaction</a>
                  
                </li>

           </ul>
          <div class="clearfix"></div>
       </div>
    </div>
   
   
    

 <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <!--<div class="panel-heading">-->
                        <!--    <div class="row">-->
                        <!--        <div class="col-md-8">-->
                        <!--            <h3 class="panel-title"></h3>-->
                        <!--        </div>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
                        
                       
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table" >
                                    <thead>
                                        <tr> 
                                          <th>S. No</th>
                                            <th>Name</th>
                                            <th>Card Number</th>
                                            <th>Exp year</th>
                                            <th>Limit </th>
                                            <th>Duration</th>
                                             <th>Type</th>
                                             <th>Action</th>
                                           
                                           
                                        </tr>
                                    </thead>
                                     <?php $i = 0 ?>
                                   @foreach($card_trans as $card)
                                    <?php $i++ ?>
                                       <tr>  
                                             <td>{{ $i}}</td>
                                             <td>{{$card->memo}}</td>
                                              <td>{{$card->pan}}</td>
                                              <td>{{$card->exp_month}}/{{$card->exp_year}}</td>
                                               <td>{{$card->spend_limit}}</td>
                                                <td>{{$card->spend_limit_duration}}</td>
                                               <td>{{$card->type}}</td>
                                                <td>
                                                 <a href="#"<span class="dtr-data"><span class="btn btn-xs btn-info">Edit</span></span></a>
                                             </td>
                                             
                                            
                                             
                                             
                                        </tr>
                                         @endforeach
                                </table>
                                
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
<script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    // flag for button disable/enable
    var hasPhoneError = false;
    var hasEmailError = false;

    $(function () {
        $(".select2").select2({
        });

        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ["us"],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            formatOnDisplay: false,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
        })
        .done(function()
        {
            let formattedPhone = '{{ !empty($users->formattedPhone) ? $users->formattedPhone : NULL }}';
            let carrierCode = '{{ !empty($users->carrierCode) ? $users->carrierCode : NULL }}';
            let defaultCountry = '{{ !empty($users->defaultCountry) ? $users->defaultCountry : NULL }}';
            if (formattedPhone !== null && carrierCode !== null && defaultCountry !== null) {
                $("#phone").intlTelInput("setNumber", formattedPhone);
                $('#user_defaultCountry').val(defaultCountry);
                $('#user_carrierCode').val(carrierCode);
            }
        });
    });

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
            let p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g, "");
            $("#formattedPhone").val(p);
        }
    }

/*
intlTelInput
 */

    function checkInvalidAndDuplicatePhoneNumberForUserProfile(phoneVal, phoneData, userId)
    {
        var that = $("input[name=phone]");
        if ($.trim(that.val()) !== '')
        {
            if (!that.intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim(that.val())))
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

                var id = $('#id').val();
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
                        'phone': phoneVal,
                        'carrierCode': phoneData,
                        'id': userId,
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
    }

    var countryData = $("#phone").intlTelInput("getSelectedCountryData");
    $('#user_defaultCountry').val(countryData.iso2);
    $('#user_carrierCode').val(countryData.dialCode);

    $("#phone").on("countrychange", function(e, countryData)
    {
        $('#user_defaultCountry').val(countryData.iso2);
        $('#user_carrierCode').val(countryData.dialCode);
        formattedPhone();
        var id = $('#id').val();
        //Invalid Phone Number Validation
        checkInvalidAndDuplicatePhoneNumberForUserProfile($.trim($(this).val()), $.trim(countryData.dialCode), id);
    });

    //Duplicated Phone Number Validation
    $("#phone").on('blur', function(e)
    {
        formattedPhone();
        var id = $('#id').val();
        var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
        var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)
        var pluginCarrierCode = $(this).intlTelInput('getSelectedCountryData').dialCode;
        checkInvalidAndDuplicatePhoneNumberForUserProfile(phone, pluginCarrierCode, id);
    });
/*
intlTelInput
 */

    // Validate email via Ajax
    $(document).ready(function()
    {
        $("#email").on('input', function(e)
        {
            var email = $(this).val();
            var id = $('#id').val();
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
                    'user_id': id,
                }
            })
            .done(function(response)
            {
                emptyEmail(email);
                // console.log(response);
                if (response.status == true)
                {

                    if (validateEmail(email))
                    {
                        $('#emailError').addClass('error').html(response.fail).css("font-weight", "bold");
                        $('#email-ok').html('');
                        hasEmailError = true;
                        enableDisableButton();
                    } else {
                        $('#emailError').html('');
                    }
                }
                else if (response.status == false)
                {
                    hasEmailError = false;
                    enableDisableButton();
                    if (validateEmail(email))
                    {
                        $('#emailError').html('');
                    } else {
                        $('#email-ok').html('');
                    }
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
                function emptyEmail(email) {
                    if( email.length === 0 )
                    {
                        $('#emailError').html('');
                        $('#email-ok').html('');
                    }
                }
            });
        });
    });

    // show warnings on user status change
    $(document).on('change', '#status', function() {
        $status = $('#status').val();
        if ($status == 'Inactive') {
            $('#user-status').text('Warning! User won\'t be able to login.');
        } else if ($status == 'Suspended') {
            $('#user-status').text('Warning! User won\'t be able to do any transaction.');
        } else {
            $('#user-status').text('');
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
                minlength: 6,
            },
            password_confirmation: {
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
            $("#users_edit").attr("disabled", true);
            $(".fa-spin").show();
            $("#users_edit_text").text('Updating...');
            $('#users_cancel').attr("disabled","disabled");
            form.submit();
        }
    });

</script>
@endpush
