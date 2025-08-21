@extends('admin.layouts.master')

@section('title', 'Edit Profile')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')
 <!--<div class="box">-->
 <!--      <div class="panel-body">-->
 <!--           <ul class="nav nav-tabs cus" role="tablist">-->
 <!--               <li class="active">-->
                   
                  
 <!--                <a href="{{url('admin/card/user-carddetail')}}/60">Open</a>-->
                 
 <!--               </li>-->
                
 <!--                <li>-->
 <!--                 <a href="">Pause</a>-->
 <!--               </li>-->
 <!--               <li>-->
 <!--                 <a href="">Delete</a>-->
 <!--               </li>-->
 <!--               <li>-->
                
 <!--                <a href="{{url('admin/card/user-transactions')}}/60">Transaction</a>-->
                  
 <!--               </li>-->

 <!--          </ul>-->
 <!--         <div class="clearfix"></div>-->
 <!--      </div>-->
 <!--   </div>-->
   
   
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="top-bar-title padding-bottom pull-left">Users Card List</div>
                                </div>
                                <div class="col-md-2 pull-right">
                                    <a href="{{ url('admin/card/user-card')}}" class="btn btn-success btn-flat"><span class=""> &nbsp;</span>Show All User</a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                               <table id="example" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr> 
                                            <th>S. No</th>
                                            <th>Name</th>
                                            <th>Card Number</th>
                                            <th>Exp year</th>
                                            <th>Card Limit($)</th>
                                            <th>Available Limit($)</th>
                                            <th>Duration</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Created On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user_view_card as $k=>$card)
                                            <?php
                                                $limit = DB::table('virtual_card_transactions')->where('card_token',$card->token)->sum('amount');
                                            ?>
                                            <tr>  
                                                <td>{{ ++$k }}</td>
                                                <td>{{$card->memo}}</td>
                                                <td>{{$card->pan}}</td>
                                                <td>{{$card->exp_month}}/{{$card->exp_year}}</td>
                                                <td>{{ bcdiv($card->spend_limit, 1, 2) }}</td>
                                                <td>{{ bcdiv($card->spend_limit - $limit, 1, 2) }}</td>
                                                <td>{{$card->spend_limit_duration}}</td>
                                                <td>{{$card->type}}</td>
                                                <td>{{$card->card_state}}</td>
                                                <td>{{ Carbon\Carbon::parse($card->created_at)->format('d-M-Y') }}</td>
                                                <td>
                                                    <a href="{{url('admin/card/user-transactions')}}/{{$card->token}}" class="btn btn-xs btn-info" data-toggle="tooltip2" data-placement="top" title="Transactions"><i class="fa fa-list"></i></a>
                                                    <a href="{{url('admin/card/vuser-details/'. $card->id)}}" class="btn btn-xs btn-info" data-toggle="tooltip2" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                                 
                                                    @if($card->card_state == 'OPEN')
                                                    <a data-toggle="modal" data-target="#pause{{$card->id}}" href="" class="btn btn-xs btn-info" data-toggle="tooltip2" data-placement="top" title="Pause"><i class="fa fa-pause-circle"></i></a>
                                                    @endif
                                                    @if($card->card_state == 'PAUSED')
                                                    <a data-toggle="modal" data-target="#unpause{{$card->id}}" href="" class="btn btn-xs btn-info"><i class="fa fa-play-circle" data-toggle="tooltip3" data-placement="top" title="Unpause"></i></a>
                                                    @endif
                                                     @if($card->card_state != 'CLOSED')
                                                    <a data-toggle="modal" data-target="#close{{$card->id}}" href="" class="btn btn-xs btn-warning" data-toggle="tooltip4" data-placement="top" title="Close"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                    @if($card->card_state == 'CLOSED')
                                                    <!--<a data-toggle="modal" data-target="#delete{{$card->id}}" href="" class="btn btn-xs btn-danger" data-toggle="tooltip4" data-placement="top" title="Delete"><i class="fa fa-times-circle" aria-hidden="true"></i></a>-->
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                            <div class="modal fade" id="edit{{$card->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                                <div class="modal-dialog modal- modal-dialog-centered modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0">
                                                            <div class="card bg-white border-0 mb-0">
                                                                <div class="card-header">
                                                                    <h3 class="mb-0"> {{__('Card Details')}} {{'('}}{{__($card->last_four_digit)}}{{')'}}</h3>
                                                                </div>
                                                                  <form action="{{route('admin.edit_virtual_card')}}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{$card->user_id}}">
                                                                    <input type="hidden" name="card_token" value="{{$card->token}}">
                                                                    <input type="hidden" name="last_four_digit" value="{{$card->last_four_digit}}">
                                                                    <div class="row p-3">
                                                                        <div class="col-sm-3">
                                                                            {{__('Card Number')}}</label>
                                                                        </div>
                                                                         <div class="col-sm-9">
                                                                             <input class="form-control" type="text" name="pan" placeholder="e.g. 4111186115678945" minlength="16" maxlength="16" value="{{$card->pan}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row p-3">
                                                                        <div class="col-sm-3">
                                                                            {{__('Card Exp Months')}}</label>
                                                                        </div>
                                                                         <div class="col-sm-9">
                                                                             <input class="form-control" type="text" name="exp_month" placeholder="e.g. 03" minlength="2" maxlength="2" value="{{$card->exp_month}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row p-3">
                                                                        <div class="col-sm-3">
                                                                            {{__('Card Exp Year')}}</label>
                                                                        </div>
                                                                         <div class="col-sm-9">
                                                                             <input class="form-control" type="text" name="exp_year" placeholder="e.g. 2024" minlength="4" maxlength="4" value="{{$card->exp_year}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row p-3">
                                                                        <div class="col-sm-3">
                                                                            {{__('Card CVV')}}</label>
                                                                        </div>
                                                                         <div class="col-sm-9">
                                                                             <input class="form-control" type="text" name="cvv" placeholder="e.g. 123" minlength="3" maxlength="3" value="{{$card->cvv}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
                                                                        </div>
                                                                    </div>
                                                                <div class="card-body px-lg-5 py-lg-5 text-right">
                                                                    <button type="button" class="btn btn-neutral" data-dismiss="modal">{{__('Close')}}</button>
                                                                    <button  type="submit" class="btn btn-success">{{__('Update Now')}}</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="close{{$card->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                                <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0">
                                                            <div class="card bg-white border-0 mb-0">
                                                                <div class="card-header">
                                                                    <h3 class="mb-0">{{__('Are you sure you want to closed this?')}}</h3>
                                                                </div>
                                                                <form action="{{route('admin.close_virtual_card')}}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{$card->user_id}}">
                                                                    <input type="hidden" name="card_token" value="{{$card->token}}">
                                                                <div class="card-body px-lg-5 py-lg-5 text-right">
                                                                    <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                                                    <button  type="submit" class="btn btn-danger btn-sm">{{__('Closed Now')}}</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="unpause{{$card->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                                <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0">
                                                            <div class="card bg-white border-0 mb-0">
                                                                <div class="card-header">
                                                                    <h3 class="mb-0">{{__('Are you sure you want to unpause this?')}}</h3>
                                                                </div>
                                                                <form action="{{route('admin.open_virtual_card')}}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{$card->user_id}}">
                                                                    <input type="hidden" name="card_token" value="{{$card->token}}">
                                                                <div class="card-body px-lg-5 py-lg-5 text-right">
                                                                    <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                                                    <button  type="submit" class="btn btn-danger btn-sm">{{__('Unpause Now')}}</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="pause{{$card->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                                <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0">
                                                            <div class="card bg-white border-0 mb-0">
                                                                <div class="card-header">
                                                                    <h3 class="mb-0">{{__('Are you sure you want to pause this?')}}</h3>
                                                                </div>
                                                                <form action="{{route('admin.pause_virtual_card')}}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{$card->user_id}}">
                                                                    <input type="hidden" name="card_token" value="{{$card->token}}">
                                                                <div class="card-body px-lg-5 py-lg-5 text-right">
                                                                    <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                                                    <button  type="submit" class="btn btn-danger btn-sm">{{__('Pause Now')}}</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="delete{{$card->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                                <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0">
                                                            <div class="card bg-white border-0 mb-0">
                                                                <div class="card-header">
                                                                    <h3 class="mb-0">{{__('Are you sure you want to Close this?')}}</h3>
                                                                </div>
                                                                <div class="card-body px-lg-5 py-lg-5 text-right">
                                                                    <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                                                    <a  href="{{route('admin.delete_virtual_card', ['id' => $card->id])}}" class="btn btn-danger btn-sm">{{__('Proceed')}}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
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
<script type="text/javascript">
  $(document).ready(function() {
$('#example').DataTable();
} );
</script>
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
