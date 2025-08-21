@extends('admin.layouts.master')
@section('title', 'Upgrade Account')

@section('head_style')
    <!-- sweetalert -->

    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/user_dashboard/css/bootstrap.min.css')}}">
    <style>
        .custom-select,
        .custom-file-label{
            height: auto;
        }
        .sidebar-toggle{
            position: absolute;
        }
        .navbar-custom-menu{
            margin-right: -80%;
        }
        .btn-cust{    color: white!important;}
    </style>
@endsection

@section('page_content')
    <div class="box">
        <div class="panel-body">
            <h4>Account Upgrade</h4>
            {{--<ul class="nav nav-tabs cus" role="tablist">
                <li>
                    <a href='{{url("admin/merchant/edit/$merchant->id")}}'>Profile</a>
                </li>

                <li>
                    <a href="{{url("admin/merchant/payments/$merchant->id")}}">Payments</a>
                </li>

                <li class="active">
                    <a href='{{url("admin/merchant/upgrade-package/$merchant->id")}}'>Account Upgrade</a>
                </li>
            </ul>
            <div class="clearfix"></div>--}}
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">


                    <div class="col-md-12">
                        <h4 class="">{{$user->first_name .' '.$user->last_name}} want to Upgrade business account
                            @if($merchantGroups->count())
                                {{$user->package}}
                                to {{$requestPackage->MerchantGroup->name}}
                            @endif </h4>
                    </div>

                    <div class="col-md-12" id="upgrade-verify"
                         style="">
                        <div class="col-12" id="lc-docs" style="">
                            <div class="alert alert-info" style="margin:0;" role="alert">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="alert-heading"><i class="fa fa-info"></i> Press
                                            <strong>"Browse"</strong> to upload the following documents:
                                        </h5>
                                    </div>
                                </div>
                                <hr class="mt-0">
                                <div class="row">
                                    @if($merchantGroupDocuments->count())
                                        @foreach($merchantGroupDocuments as $key => $doc)
                                            <div class="col-12">
                                                <span>{{$key+1}}. {{$doc->name}}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                @if(!is_null($user->MerchantDocument) && $user->MerchantDocument->count())
                                    <hr class="mt-0">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="alert-heading">
                                                User`s uploaded documents:
                                            </h5>
                                        </div>
                                    </div>
                                    <hr class="mt-0">
                                    <div class="row">
                                            @foreach($user->MerchantDocument as $key => $doc)
                                                <div class="col-12">
                                                    <span>{{$key+1}}
                                                        <a href="{{asset('public/'.$doc->path)}}" target="_blank">
                                                            {{basename($doc->path)}}
                                                        </a>
                                                    </span>
                                                </div>
                                            @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!--  HERE  -->
                        <form action="{{url('admin/merchant/upgrade-package-update')}}"
                              enctype="multipart/form-data" id="upgrade-verify_form" method="POST"
                              style="width:100%;" class="was-validated">
                            @csrf
                            <input type="hidden" name="package_id" value="{{$requestPackage->merchant_group_id}}">
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            @if(collect($businessDetail)->count())
                                <input type="hidden" name="detail_id" value="{{$businessDetail->id}}">
                            @endif
                            <div class="col-12" style="display:none;">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="structure"
                                           id="structure" maxlength="256"
                                           aria-describedby="business structure"
                                           placeholder="Business Structure">
                                </div>
                            </div>
                            <div class="col-12 mt-4 ">
                                <div class="voucher_option alert alert-warning animated fadeIn"
                                     style="padding:5px;" role="alert">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div id="documents_prepend" class="input-group-text"
                                                 style="height: 39.5938px;">Files:<span
                                                        style="color:transparent;">.</span><span
                                                        id="num_files">0</span></div>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="documents"
                                                   name="document[]" multiple=""
                                                   @if(!collect($businessDetail)->count())
                                                   required="required"
                                                    @endif
                                            >
                                            <label class="custom-file-label" for="documents">Choose
                                                Files...</label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <span><i class="fa fa-info"></i> You <strong>can</strong> upload multiple files at once.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3 text-left">
                                <div class="form-group">
                                    <label for="category">What is the nature of your business?</label>
                                    <select id="business_nature" class="custom-select" name="business_nature"
                                            required="required">
                                        <option value="" selected="">Please select. . .</option>
                                        @if(collect(config('businessdetails.business_nature'))->count())
                                            @foreach(collect(config('businessdetails.business_nature')) as $val)
                                                <option {{@$businessDetail->business_nature == $val ?  'selected' :''}}>{{$val}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="product_service">Does your business sell products and/or
                                        provide services?</label>
                                    <select id="sell" class="custom-select"
                                            name="sell" required="required">
                                        <option value="" selected="">Please select. . .</option>
                                        @if(collect(config('businessdetails.sell'))->count())
                                            @foreach(collect(config('businessdetails.sell')) as $val)
                                                <option {{@$businessDetail->sell == $val ?  'selected' :''}}>{{$val}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="description">Please provide a brief description of your
                                        products and/or services:</label>
                                    <textarea name="description" id="description" class="form-control"
                                              rows="3" style="width:100%;resize:none;" maxlength="255"
                                              form="upgrade-verify_form"
                                              placeholder="Details described here. . ."
                                              required="required">{{@$businessDetail->description}}</textarea>
                                    <small class="float-right mb-2"><span><strong
                                                    id="chars_remain">255</strong></span> Characters
                                        left</small>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <label>What type of customers does your business have?</label>
                                <br>
                                <div class="col-12  text-left">
                                    <div class="row mt-2">
                                        @if(collect(config('businessdetails.sell'))->count())
                                            @foreach(collect(config('businessdetails.customer_type')) as $val)
                                                <div class="col-xs-12 col-sm-6 mt-2">
                                                    <div>
                                                        <label for="3ct" style="margin:0;">
                                                            <input type="checkbox"
                                                                   name="customer_type"
                                                                   value="{{$val}}"
                                                                   {{@$businessDetail->customer_type == $val ?  'checked' :''}}
                                                                   required="">
                                                            {{$val}}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <label id="customer_type-error" class="error" for="customer_type" style="display: none;"></label>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="form-group">
                                    <label for="avg_trxn">What is your business' expected average
                                        transaction value? (per transaction)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input min="0.01" type="number" class="form-control"
                                               id="average_transaction" name="average_transaction" value="{{@$businessDetail->average_transaction}}"
                                               placeholder="Average transaction">
                                    </div>
                                    <label id="average_transaction-error" class="error" for="average_transaction" style="display: none;"></label>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="turnover">What is your business' expected annual
                                        turnover?</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input min="0.01" type="number" class="form-control"
                                               id="annual_turnover" name="annual_turnover" value="{{@$businessDetail->annual_turnover}}"
                                               placeholder="Annual turnover">
                                    </div>
                                    <label id="annual_turnover-error" class="error" for="annual_turnover" style="display: none;"></label>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="number_employees">How many employees does your business
                                        employ, including yourself?</label>
                                    <div class="input-group">
                                        <input min="1" step="1" type="number" class="form-control"
                                               id="employees" name="employees" value="{{@$businessDetail->employees}}"
                                               placeholder="Number of employees">
                                    </div>
                                    <label id="employees-error" class="error" for="employees" style="display: none;"></label>
                                </div>
                            </div>
                            <div class="col-12 mt-3 text-left">
                                <div class="form-group">
                                    <label>Will your business be expecting to recieve foreign currency
                                        payments regularly from other businesses/entities/indiviuals
                                        outside of St. Lucia?</label>
                                    <br>
                                    <div class="col-12 mt-3 text-left">
                                        <div class="row">
                                            @if(collect(config('businessdetails.foreign_currency_payment'))->count())
                                                @foreach(collect(config('businessdetails.foreign_currency_payment')) as $val)
                                                    <div class="col-xs-6 col-sm-3 mt-2">
                                                        <div>
                                                            <label for="3ct" style="margin:0;">
                                                                <input type="radio"
                                                                       name="foreign_currency_payment"
                                                                       value="{{$val}}"
                                                                       {{@$businessDetail->foreign_currency_payment == $val ?  'checked' :''}}
                                                                       required="">
                                                                {{$val}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <label id="foreign_currency_payment-error" class="error" for="foreign_currency_payment" style="display: none;"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="company_phone">What is the official 7-digit phone number
                                        for your company?</label>
                                    <input type="text" class="form-control" name="official_phone"
                                           id="official_phone" aria-describedby="company phone"
                                           value="{{@$businessDetail->official_phone}}"
                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                           pattern="[\d]{7}" placeholder="XXXXXXX">
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="street">On what Street is your business located?</label>
                                    <input type="text" class="form-control" name="street" id="street"
                                           maxlength="255" aria-describedby="business street"
                                           value="{{@$businessDetail->street}}"
                                           placeholder="Street" required="required">
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="town_city">In what Town/City is your business
                                        located?</label>
                                    <input type="text" class="form-control" name="city"
                                           id="city" maxlength="100"
                                           value="{{@$businessDetail->city}}"
                                           aria-describedby="business town or city"
                                           placeholder="Town/City" required="required">
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="premises">Where does your business operate from?</label>
                                    <select id="operate_from" class="custom-select" name="operate_from"
                                            required="required">
                                        <option value="" selected="">Please select. . .</option>
                                        @if(collect(config('businessdetails.operate_from'))->count())
                                            @foreach(collect(config('businessdetails.operate_from')) as $val)
                                                <option {{@$businessDetail->operate_from == $val ?  'selected' :''}}>{{$val}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="established">What date was your business
                                        established?</label>
                                    <div class="input-group date">
                                        <input type="date" class="form-control datepicker"
                                               id="establish_date" name="establish_date"
                                               value="{{@$businessDetail->establish_date}}"
                                               >
                                    </div>
                                    <label id="establish_date-error" class="error" for="establish_date" style="display: none;"></label>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="trade_name">Registered trading name (where it differs
                                        from business name)</label>
                                    <input type="text" class="form-control" name="trading_name"
                                           id="trading_name" maxlength="255" aria-describedby="trade name"
                                           value="{{@$businessDetail->trading_name}}"
                                           placeholder="Trade name" required="required">
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="signup_reason">What influenced your decision to use
                                        CaribPay?</label>
                                    <select id="use_caribPay" class="custom-select"
                                            name="use_caribPay">
                                        <option value="" selected="">Please select. . .</option>
                                        @if(collect(config('businessdetails.use_caribPay'))->count())
                                            @foreach(collect(config('businessdetails.use_caribPay')) as $val)
                                                <option {{@$businessDetail->use_caribPay == $val ?  'selected' :''}}>{{$val}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-12 mt-4 text-left">
                                <div class="form-group">
                                    <label for="premises">Change Status</label>
                                    <select id="changestatus" class="custom-select" name="changestatus"
                                            required="required">
                                        <option value="Approved" >Approved</option>
                                        <option value="Moderation" selected="">Moderation</option>
                                        <option value="Disapproved">Disapproved</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 offset-3">
                                <button id="form_submit"
                                        class="btn btn-lg btn-block btn-tertiary btn-primary"
                                        type="submit">Verify account
                                </button>
                            </div>
                        </form>
                    </div>





                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

    <!-- jquery.validate -->
    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

    <!-- jquery.validate additional-methods -->
    <script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}"
            type="text/javascript"></script>

    <!-- sweetalert -->
    <script src="{{ asset('public/backend/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

    @include('common.restrict_number_to_pref_decimal')

    @include('common.format_number_to_pref_decimal')

    <!-- read-file-on-change -->
    @include('common.read-file-on-change')

    <script type="text/javascript">

        function getMerchantGroupFee(merchant_group_id) {
            let currentMerchantGroupId = '{{ $merchant->merchant_group_id }}';
            if (currentMerchantGroupId != merchant_group_id) {
                $.ajax({
                    headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    method: "POST",
                    url: SITE_URL + "/admin/merchants/change-fee-with-group-change",
                    dataType: "json",
                    data: {
                        'merchant_group_id': merchant_group_id,
                    }
                })
                    .done(function (response) {
                        if (response.status == true) {
                            $('#fee').val(formatNumberToPrefDecimal(response.fee));
                        }
                    });
            } else {
                let merchantFee = '{{ $merchant->fee }}';
                $('#fee').val(formatNumberToPrefDecimal(merchantFee));
            }
        }

        $(window).on('load', function () {
            $(".select2").select2({});
            let merchant_group_id = $('#merchantGroup option:selected').val();
            getMerchantGroupFee(merchant_group_id);
        });

        $(document).on('change', '#merchantGroup', function (e) {
            e.preventDefault();
            let merchant_group_id = $('#merchantGroup option:selected').val();
            getMerchantGroupFee(merchant_group_id);
        });

        // preview logo on change
        $(document).on('change', '#logo', function () {
            let orginalSource = '{{ url('public/uploads/userPic/default-image.png') }}';
            let logo = $('#logo').attr('data-rel');
            if (logo != '') {
                readFileOnChange(this, $('#merchant-logo-preview'), orginalSource);
                $('.remove_merchant_preview').remove();
            }
            readFileOnChange(this, $('#merchant-demo-logo-preview'), orginalSource);
        });

        $(document).ready(function () {
            $('.remove_merchant_preview').click(function () {
                var logo = $('#logo').attr('data-rel');
                var merchant_id = $('#id').val();
                if (logo) {
                    $.ajax(
                        {
                            headers:
                                {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                            type: "POST",
                            url: SITE_URL + "/admin/merchant/delete-merchant-logo",
                            // async : false,
                            data: {
                                'logo': logo,
                                'merchant_id': merchant_id,
                            },
                            dataType: 'json',
                            success: function (reply) {
                                if (reply.success == 1) {
                                    swal({title: "Deleted!", text: reply.message, type: "success"},
                                        function () {
                                            location.reload();
                                        }
                                    );
                                } else {
                                    alert(reply.message);
                                    location.reload();
                                }
                            }
                        });
                }
            });
        });

        $.validator.setDefaults({
            highlight: function (element) {
                $(element).parent('div').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).parent('div').removeClass('has-error');
            },
        });

        $('#merchant_edit_form').validate({
            rules: {
                business_name: {
                    required: true,
                },
                site_url: {
                    required: true,
                    url: true,
                },
                type: {
                    required: true,
                    lettersonly: true,
                },
                fee: {
                    required: true,
                    number: true,
                },
                logo: {
                    extension: "png|jpg|jpeg|gif|bmp",
                },
            },
            messages: {
                logo: {
                    extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
                },
                type: {
                    lettersonly: "Please enter letters only!"
                }
            },
            submitHandler: function (form) {
                $("#merchant_edit").attr("disabled", true);
                $(".fa-spin").show();
                $("#merchant_edit_text").text('Updating...');
                $('#cancel_anchor').attr("disabled", true);
                form.submit();
            }
        });

    </script>
    <script type="text/javascript">

        jQuery.extend(jQuery.validator.messages, {
            required: "{{__('This field is required.')}}",
        })

        $(document).on({
            'change': function (event) {
                var len = $(this).get(0).files.length;
                $('#num_files').text(len);
                if(len == 0){
                    $(this).siblings('label').text('Choose Files...');
                }
            },
        }, '#documents');
        $(document).on({
            'keyup keydown': function (event) {
                $("#chars_remain").text(255 - $(this).val().length);
            },
        }, '#description');

        $('#upgrade-verify_form').validate({
            submitHandler: function (form) {
                $("#form_submit").attr("disabled", true);
                $(".spinner").show();
                $("#personal_address_submit_text").text('Submitting...');
                form.submit();
            }
        });


    </script>
@endpush
