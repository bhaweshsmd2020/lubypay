@extends('user_dashboard.layouts.app')
@section('title','Account upgrade')
@section('css')
    <style>
        @media only screen and (max-width: 508px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
        label.error{
            margin-bottom: 0px;
        }
    </style>
@endsection

@section('content')
    <!-- personal_address -->
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    @include('user_dashboard.layouts.common.alert')
                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li><a href="{{url('/profile')}}">@lang('message.dashboard.setting.title')</a></li>
                                    @if ($two_step_verification != 'disabled')
                                        <li><a href="{{url('/profile/2fa')}}">@lang('message.2sa.title-short-text')</a>
                                        </li>
                                    @endif

                                    <li><a href="{{url('/profile/personal-id')}}">@lang('message.personal-id.title')
                                            @if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' )
                                                (<span style="color: green"><i class="fa fa-check"
                                                                               aria-hidden="true"></i>Verified</span>
                                                ) @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{url('/profile/personal-address')}}">@lang('message.personal-address.title')
                                            @if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' )
                                                (<span style="color: green"><i class="fa fa-check"
                                                                               aria-hidden="true"></i>Verified</span>
                                                ) @endif
                                        </a>
                                    </li>
                                    <li><a href="{{url('/profile/personal-photo')}}">@lang('message.personal-photo.title')
                                        </a>
                                    </li>
                                    @if(auth()->user()->type == 'merchant')
                                        <li>
                                            <a href="{{url('/profile/business-verification')}}">
                                                Business Verification
                                            </a>
                                        </li>
                                        <li class="active">
                                            <a href="{{url('/profile/upgrade')}}">
                                                Account Upgrade
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <div class="row">
<div class="col-6">
                            @if(collect($requestPackage)->count())
                                <div class="alert alert-danger fade in alert-dismissible show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="font-size:20px">×</span>
                                    </button>
                                    Your request to upgrade {{auth()->user()->package}} to {{$requestPackage->MerchantGroup->name}} is in {{$requestPackage->status}}.
                                </div>
                            @endif

                                <h4>Current package :
                                    
                                </h4>
                                <div>
                                    <p style="    line-height: 22px;
    color: grey;
    padding: 10px 0px;
">If you need to upgrade your current package. You can upgrade your package anytime by click on Business </p><br>
                                    <a class="btn btn-tertiary text-white btn-lg btn-block btn btn-cust" style="margin: unset;">
                                        {{auth()->user()->package}}
                                    </a></div>
</div>

<div class="col-6">
                            <div class="mt-3" style="    margin-top: 0px!important;">
                                @if(!is_numeric(request('package')))
                                    <div id="kyc-opts_title">
                                        <div class="row ml-2">
                                            <h4 style="color:orange">Upgrade to Business Verified</h4>
                                        </div>
                                        <div class="row ml-2">
                                          <p style="    line-height: 22px;
    color: grey;
    padding: 10px 0px;    letter-spacing: initial;    font-size: 15px!important;
">Select
                                                    the structure of your Business and then submit the required
                                                    information.</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3" id="lv3-kyc-opts">
                                        @if($merchantGroups->count())
                                            @foreach($merchantGroups as $group)
                                                @if(auth()->user()->packageid != $group->id)
                                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                                        <a href="{{url('/profile/upgrade?').http_build_query(['package'=>$group->id])}}"
                                                           class="btn btn-tertiary text-white btn-lg btn-block btn btn-cust">
                                                            {{$group->name}}
                                                        </a>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                @else
                                    <div id="kyc-opts_title">
                                        <div class="row ml-2">
                                            <h4 class="">Upgrade your business account
                                                @if($merchantGroups->count())
                                                    {{auth()->user()->package}}
                                                    to {{$merchantGroups->where('id',request('package'))->first()->name}}
                                                @endif </h4>
                                        </div>
                                        <div class="row ml-2">
                                            <h4 class="text-left"><small class="text-muted" style="font-size: 1.0rem">
                                                    Please submit the required information below.
                                                </small></h4>
                                        </div>
                                    </div>

                                    <div class="row ml-1 mr-1 mt-3 text-left animated fadeIn" id="upgrade-verify"
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

                                                {{--@if(auth()->user()->MerchantDocument->count())
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
                                                        @foreach(auth()->user()->MerchantDocument as $key => $doc)
                                                            <div class="col-12">
                                                    <span>{{$key+1}}
                                                        <a href="{{asset('public/'.$doc->path)}}" target="_blank">{{$doc->path}}</a>
                                                    </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif--}}
                                            </div>
                                        </div>
                                        <!--  HERE  -->
                                        <form action="{{url('profile/upgrade-update')}}"
                                              enctype="multipart/form-data" id="upgrade-verify_form" method="POST"
                                              style="width:100%;" class="was-validated">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{request('package')}}">
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
                                    
                                            <!-- document sections -->
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
                                            
                                            <!-- business information -->
                                            <div class="col-12 mt-3 text-left">
                                                <div class="form-group">
                                                    <label for="category">What is your business type?</label>
                                                    <select id="business_type" class="custom-select" name="business_type"
                                                            required="required">
                                                        <option value="" selected="">Please select. . .</option>
                                                        @if(collect(config('businessdetails.business_type'))->count())
                                                            @foreach(collect(config('businessdetails.business_type')) as $val)
                                                                <option {{@$businessDetail->business_type == $val ?  'selected' :''}}>{{$val}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="business_name">What is your business name?</label>
                                                    <input type="text" class="form-control" name="business_name" id="business_name"
                                                           maxlength="255" aria-describedby="business name"
                                                           value="{{@$businessDetail->business_name}}"
                                                           placeholder="Business name" required="required">
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
                                                    <label for="website_url">Website URL</label>
                                                    <input type="text" class="form-control" name="website_url"
                                                           id="website_url" maxlength="255" aria-describedby="website url"
                                                           value="{{@$businessDetail->website_url}}"
                                                           placeholder="Website URL" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="business_no">What is your business registration number?</label>
                                                    <input type="text" class="form-control" name="business_no" id="business_no"
                                                           maxlength="255" aria-describedby="business_no name"
                                                           value="{{@$businessDetail->business_no}}"
                                                           placeholder="Business Registration Number" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="company_phone">What is the official phone number
                                                        for your company?</label>
                                                    <input type="text" class="form-control" name="official_phone"
                                                           id="official_phone" aria-describedby="company phone"
                                                           value="{{@$businessDetail->official_phone}}"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                             placeholder="XXXXXXX">
                                                            <!--pattern="[\d]{10}"-->
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="company_statement_phone">What is the official  customer statement phone number
                                                        for your company?</label>
                                                    <input type="text" class="form-control" name="customer_statement_phone"
                                                           id="customer_statement_phone" aria-describedby="customer statement phone"
                                                           value="{{@$businessDetail->customer_statement_phone}}"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                            placeholder="XXXXXXX">
                                                            <!--pattern="[\d]{7}"-->
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
                                                    <label for="region">In what Region is your business located?</label>
                                                    <input type="text" class="form-control" name="region" id="region"
                                                           maxlength="255" aria-describedby="business region"
                                                           value="{{@$businessDetail->region}}"
                                                           placeholder="Region" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="country">In what Country is your business located?</label>
                                                    <input type="text" class="form-control" name="country" id="country"
                                                           maxlength="255" aria-describedby="business country"
                                                           value="{{@$businessDetail->country}}"
                                                           placeholder="Country" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="postcode">What is the Postal Code of your business location?</label>
                                                    <input type="text" class="form-control" name="postcode" id="country"
                                                           maxlength="10" aria-describedby="business postcode"
                                                           value="{{@$businessDetail->postcode}}"
                                                           placeholder="Postal Code" required="required">
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
                                                <div class="form-group">
                                                    <label for="established">What date was your business
                                                        established?</label>
                                                    <div class="input-group date">
                                                        <input type="date" class="form-control datepicker"
                                                               id="establish_date" name="establish_date"
                                                               value="{{@$businessDetail->establish_date}}"
                                                               required="required">
                                                    </div>
                                                    <label id="establish_date-error" class="error" for="establish_date" style="display: none;"></label>
                                                </div>
                                            </div>
                                            
                                            <!-- optional fields -->
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="days_deliver">In general, how many days does it take to provide your product/service?</label>
                                                    <input type="text" class="form-control" name="days_deliver"
                                                           id="days_deliver" aria-describedby="days_deliver"
                                                           value="{{@$businessDetail->days_deliver}}"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                           pattern="[\d]{3}" placeholder="7">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-3 text-left">
                                                <div class="form-group">
                                                    <label for="charged">When are your customers charged?</label>
                                                    <select id="when_charged" class="custom-select" name="when_charged">
                                                        <option value="" selected="">Please select. . .</option>
                                                        @if(collect(config('businessdetails.when_charged'))->count())
                                                            @foreach(collect(config('businessdetails.when_charged')) as $val)
                                                                <option {{@$businessDetail->when_charged == $val ?  'selected' :''}}>{{$val}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="based">Where is your company based?</label>
                                                    <input type="text" class="form-control" name="based" id="based"
                                                           maxlength="10" aria-describedby="business based"
                                                           value="{{@$businessDetail->based}}"
                                                           placeholder="Base Location">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="target_country">Which countries in Eastern Caribbean are you looking to sell?</label>
                                                    <input type="text" class="form-control" name="target_country" id="target_country"
                                                           maxlength="50" aria-describedby="target_country"
                                                           value="{{@$businessDetail->target_country}}"
                                                           placeholder="Target Country">
                                                </div>
                                            </div>
                                            
                                            <!-- <div class="col-12 mt-3 text-left">
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
                                                    <label for="signup_reason">What influenced your decision to use
                                                        WiPay?</label>
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
                                            </div>-->
                                            
                                            <div class="col-6 offset-3">
                                                <button id="form_submit"
                                                        class="btn btn-lg btn-block btn-tertiary btn btn-cust"
                                                        type="submit">Verify
                                                </button>
                                            </div>
                                        </form>
                                    </div>







                                @endif
                            </div>
                            </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')

    <script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>

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

                $('#establish_date').click(function(event){
           $('#establish_date ').data("DateTimePicker").show();
        });     
    </script>
@endsection
