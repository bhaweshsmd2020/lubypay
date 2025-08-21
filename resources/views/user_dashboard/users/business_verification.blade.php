@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        @media only screen and (max-width: 508px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
    </style>
@endsection

@section('content')

    <!-- business_verification -->
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
                                        <li><a href="{{url('/profile/2fa')}}">@lang('message.2sa.title-short-text')</a></li>
                                    @endif

                                    <li><a href="{{url('/profile/personal-id')}}">@lang('message.personal-id.title')
                                        @if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' )(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) @endif
                                        </a>
                                    </li>
                                    <li><a href="{{url('/profile/personal-address')}}">@lang('message.personal-address.title')
                                        @if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' )(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) @endif
                                        </a>
                                    </li>
                                    <li><a href="{{url('/profile/personal-photo')}}">@lang('message.personal-photo.title')
                                        </a>
                                    </li>
                                    @if(auth()->user()->type == 'merchant')
                                        <li class="active">
                                            <a href="{{url('/profile/business-verification')}}">
                                                Business Verification
                                            </a>
                                        </li>
                                        <li>
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
                    			<div class="col-md-12">
                    				<div class="panel panel-default">
                    					<!-- <div class="panel-heading">
                    						<div class="row">
                    							<div class="col-md-12">
                    								<h4 class="text-left">Business Verification</h4>
                    							</div>
                    						</div>
                    					</div> -->
                    
                    					<div class="panel-body">
                    					    
                    					    <!-- general information -->
                    					    <div class="row" style="margin-top: 10px;">
                    							<div class="col-md-12">
                    								<div class="panel panel-default">
                    									<div class="panel-body">
                    									    <h5>Business Information</h5>
                                    					    @if($business_details == null)
                                    					        <p style="padding-top: 5px;padding-bottom: 10px;"><a href="{{ url('profile/upgrade?package='.auth()->user()->packageid) }}" style="    color: #fa9d29;
    text-decoration: underline;">Start Verifying Your Business</a></p>
                                    					    @else
                    									    
                    									    @if($business_details->business_name != null && $business_details->business_name != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Business Name</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $business_details->business_name }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->trading_name != null && $business_details->trading_name != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Trading Name</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">
            													        {{ $business_details->trading_name }}
            													    </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->establish_date != null && $business_details->establish_date != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Date of Incorporation</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">{{-- date('d-m-Y', strtotime($business_details->establish_date)) --}} {{ $business_details->establish_date }}</p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->business_no != null && $business_details->business_no != '')
                    									    <div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Business Registration Number</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">{{ $business_details->business_no }}</p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->business_type != null && $business_details->business_type != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Business Type</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">
            													        {{ $business_details->business_type }}
            													    </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->website_url != null && $business_details->website_url != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Website URL</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static"><a href="{{ $business_details->website_url }}">{{ $business_details->website_url }}</a></p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->official_phone != null && $business_details->official_phone != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Official Phone Number</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">{{ $business_details->official_phone }}</p>
            													</div>
            												</div>
            												@endif
            												
            												@if(($business_details->street != null && $business_details->street != '') && ($business_details->city != null && $business_details->city != '') &&
            												($business_details->postcode != null && $business_details->postcode != '') && ($business_details->region != null && $business_details->region != '') &&
            												($business_details->country != null && $business_details->country != ''))
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Registered Business Address</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">{{ $business_details->street.", ".$business_details->city }}<br/>
            													    {{ $business_details->postcode.", ".$business_details->region.", ".$business_details->country }}
            													    </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->business_nature != null && $business_details->business_nature != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Business Nature</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $business_details->business_nature }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->sell != null && $business_details->sell != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Selling</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $business_details->sell }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($business_details->description != null && $business_details->description != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Business Description</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ ($business_details->description != null && $business_details->description != '') ? $business_details->description : '-' }}
            													       {{-- $business_details->description --}}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
                    									</div>
                    								</div>
                    							</div>
                    					    </div>
                    					    @endif
                    					    
                    					    <!-- personal information -->
                    					    <div class="row" style="margin-top: 10px;">
                    							<div class="col-md-12">
                    								<div class="panel panel-default">
                    									<div class="panel-body">
                    									    <h5>Personal Information</h5>
                                    					    @if($null_verification->count() > 0)
                                    					        <p style="padding-top: 5px;padding-bottom: 10px;"><a href="{{ url('profile') }}" style="color: #fa9d29;
    text-decoration: underline;">Update Your Personal Information</a></p>
                                    					    @else
                                    					    
                                    					    <div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Full Name</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $user->last_name.", ".$user->first_name }}
            													   </p>
            													</div>
            												</div>
            												
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Email</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $user->email }}
            													   </p>
            													</div>
            												</div>
            												
            												@if(($user->user_detail->address_1 != null && $user->user_detail->address_1 != '') && ($user->user_detail->city != null && $user->user_detail->city != '') &&
            												($user->postal_code != null && $user->postal_code != '') && ($user->user_detail->state != null && $user->user_detail->state != '') &&
            												($user->user_detail->country_id != null && $user->user_detail->country_id != ''))
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Address</strong></label>
            													<div class="col-sm-9">
            													    <p class="form-control-static">
            													        {{ $user->user_detail->address_1.", ".$user->user_detail->address_2 }}<br/>
            													        {{ $user->user_detail->city.", ".$user->user_detail->state.", ".$countries[$user->user_detail->country_id-1]->name }}<br/>
            													    </p>
            													</div>
            												</div>
            												@endif
                                    					    
                                    					    @if($user->dob != null && $user->dob != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Date of Birth</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $user->dob }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($user->job_title != null && $user->job_title != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Job Title</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $user->job_title }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($user->nationality != null && $user->nationality != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Nationality</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $user->nationality }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($user->id_number != null && $user->id_number != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>ID Number</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ $user->id_number }}
            													   </p>
            													</div>
            												</div>
            												@endif
            												
            												@if($user->id_type != null && $user->id_type != '')
            												<div class="form-group" style="margin-top: 10px;">
            													<label class="control-label col-sm-3"><strong>Type of ID</strong></label>
            													<div class="col-sm-9">
            													   <p class="form-control-static">
            													       {{ __(ucwords(str_replace('_',' ',$user->id_type))) }}
            													   </p>
            													</div>
            												</div>
            												@endif
                                    					    
                                    					    @endif
            									        </div>
            									    </div>
        									    </div>
    									    </div>
    									    
                    					    <!-- documents -->
                    					    <div class="row" style="margin-top: 10px;">
                    							<div class="col-md-12">
                    								<div class="panel panel-default">
                    									<div class="panel-body">
                    									    <h5>Documents</h5>
                    									    @if(isset($documents) && $documents->count() > 0)
                    									    <ol>
                    									        @foreach($documents as $document)
                    									            @php
                        									            if($document->original_file_name != null)
                        									                $document_name = $document->original_file_name;
                        									            else
                        									                $document_name = explode('/', $document->path);
                        									                $document_name = $document_name[1];
                    									            @endphp
                    									            <li>
                    									                {{ ($document->original_file_name != null) ? $document->original_file_name : explode('/', $document->path)[1] }}
                    									                <span style="margin-left: 50px;">{{ date('M d, Y', strtotime($document->created_at)) }}</span>
                    									            </li>
                    									        @endforeach
                    									    </ol>
                    									    @else
                    									        <p style="padding-top: 5px;padding-bottom: 10px;">No documents uploaded! <a href="{{ url('profile/upgrade?package='.auth()->user()->packageid) }}" style="color: #fa9d29;
    text-decoration: underline;">Upload Now</a></p>
                    									    @endif
                    									</div>
                    								</div>
                    							</div>
                    					    </div>
                    					</div>
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

$('#personal_address').validate({
    rules: {
        address_file: {
            required: true,
            extension: "docx|rtf|doc|pdf|png|jpg|jpeg|csv|txt|gif|bmp",
        },
    },
    messages: {
      address_file: {
        extension: "{{__("Please select (docx, rtf, doc, pdf, png, jpg, jpeg, csv, txt, gif or bmp) file!")}}"
      }
    },
    submitHandler: function(form)
    {
        $("#personal_address_submit").attr("disabled", true);
        $(".spinner").show();
        $("#personal_address_submit_text").text('Submitting...');
        form.submit();
    }
});

</script>
@endsection
