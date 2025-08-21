@extends('admin.layouts.master')
@section('title', 'KYC Verifications')
@section('page_content')

    <style>
        .link-active {
          color: #00a65a !important;
          text-decoration: underline !important;
        }
        
        .tab-links-link{
            padding: 0px 20px;
        }
        
        .tab-hide {
          display: none;
        }
        
        .tab-links{
            width: fit-content;
            background-color: #fff;
            font-size: 20px;
            padding: 20px 40px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>

    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href="{{url("admin/users/edit/$users->id")}}">Profile</a>
                </li>
                <li>
                  <a href="{{url("admin/users/transactions/$users->id")}}">Transactions</a>
                </li>
                <li>
                  <a href="{{url("admin/users/wallets/$users->id")}}">Wallets</a>
                </li>
                <li>
                  <a href="{{url("admin/users/tickets/$users->id")}}">Tickets</a>
                </li>
                <li>
                  <a href="{{url("admin/users/disputes/$users->id")}}">Disputes</a>
                </li>
                <li class="active">
                  <a href="{{url("admin/users/kyc-verications/$users->id")}}">KYC Verifications</a>
                </li>
                <li>
                  <a href="{{url("admin/users/bankdetails/$users->id")}}">Bank Details</a>
                </li>
                <li>
                  <a href="{{url("admin/users/address_edit/$users->id")}}">Address</a>
                </li>
                <li>
                  <a href="{{url("admin/users/activity-logs/$users->id")}}">Activity Logs</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>
    
    @if ($users->status == 'Inactive')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;( {{ $users->carib_id }} )&nbsp;<span class="label label-danger">Inactive</span></h3>
    @elseif ($users->status == 'Suspended')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;( {{ $users->carib_id }} )&nbsp;<span class="label label-warning">Suspended</span></h3>
    @elseif ($users->status == 'Active')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;( {{ $users->carib_id }} )&nbsp;<span class="label label-success">Active</span></h3>
    @endif
    
    <div class="page-wrapper">
        <div class="tab-container">
            <center><div class="tab-links">
                <a class="tab-links-link link-active" href="#autokyc">Auto KYC</a>
                <a class="tab-links-link" href="#manualkyc">Manual KYC</a>
            </div></center>
          
            <div class="tab-content-container">
                <div class="content-active" id="autokycdiv">
                    <div class="box">
                    	<div class="box-body">
                    	    @if($autokyc_details != 'N/A')
                    	        <div class="row"> 
                                    <div class="col-md-8">
                                          <div class="box mt-4">
                                      <div class="box-body">
                                          <h4 class="mb-2"><strong>Verification/Selfie</strong></h4>
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->left_photo_url}}" alt="left_photo_url"  width="100%" height="250" width="100%">
                                                <h4>Left</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->center_photo_url}}" alt="center_photo_url"  width="100%" height="250" width="100%">
                                                <h4>Center</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->right_photo_url}}" alt="right_photo_url" width="100%" height="250" width="100%">
                                                            <h4>Right</h4>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                     <div class="box mt-3">
                                      <div class="box-body">
                                          <h4 class="mb-2"><strong>Verification/Government Id</strong></h4>
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->front_photo_url}}" alt="front_photo_url"  height="250" width="100%">
                                                <h4>Front</h4>
                                            </div>
                                            <div class="col-md-4">
                                                @if(!empty($autokyc_details->back_photo_url))
                                                    <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->back_photo_url}}" alt=""  height="250" width="100%">
                                                    <h4>Back</h4>
                                                @else
                                                    <h4>Not Available</h4>         
                                                @endif            
                                            </div>
                                            <div class="col-md-4">
                                                <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->selfie_photo_url}}" alt="selfie_photo_url" height="250" width="100%">
                                                <h4>Selfie</h4>
                                            </div>
                                        </div>
                                        <hr>
                                        <h4 class="mb-2"><strong>Attributes</strong></h4>
                                        <div class="row">
                                            <div class="col-md-4">
                                                  <h5><strong>NAME</strong></h5>
                                                 <h5>{{$autokyc_details->name_first}}&nbsp;&nbsp;{{$autokyc_details->name_middle}}&nbsp;&nbsp;{{$autokyc_details->name_last}}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>GOVERNMENT ID NUMBER</strong></h5>
                                                 <h5>{{$autokyc_details->identification_number}}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>BIRTHDATE</strong></h5>
                                                 <h5>{{$autokyc_details->birthdate}}</h5>
                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-4">
                                                  <h5><strong>ADDRESS</strong></h5>
                                                 <h5>{{$autokyc_details->addressstreet1}}&nbsp;&nbsp;{{$autokyc_details->addressstreet2}}&nbsp;&nbsp;{{$autokyc_details->address_city}},{{$autokyc_details->address_subdivision_abb}}&nbsp;{{$autokyc_details->address_postal_code_abbr}}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>EMAIL ADDRESS</strong></h5>
                                                 <h5>{{$autokyc_details->email_address??'No email address collected'}}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>PHONE NUMBER</strong></h5>
                                                 <h5>{{$autokyc_details->phone_number??'No phone number collected'}}</h5>
                                            </div>
                                        </div>
                                          <div class="row">
                                            <div class="col-md-4">
                                                  <h5><strong><strong>SELECTED COUNTRY CODE</strong></strong></h5>
                                                 <h5>{{$autokyc_details->selected_country_code}}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                  <h5><strong>SELECTED ID CLASS</strong></h5>
                                                 <h5>{{$autokyc_details->selected_id_class}}</h5>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                   
                                      
                                    <!-- <div class="box mt-4">-->
                                    <!--  <div class="box-body">-->
                                    <!--      <h4 class="mb-2">Document/Government-Id</h4>-->
                                    <!--    <div class="row text-center">-->
                                    <!--        <div class="col-md-4">-->
                                    <!--            <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->front_photo}}" alt="front_photo"  height="250">-->
                                    <!--        </div>-->
                                    <!--        <div class="col-md-4">-->
                                    <!--            <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->back_photo}}" alt="back_photo"  height="250">-->
                                    <!--        </div>-->
                                    <!--        <div class="col-md-4">-->
                                    <!--            <img src="{{url('public/kyc_documents/').'/'.$autokyc_details->selfie_photo}}" alt="selfie_photo" height="250">-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--  </div>-->
                                    <!--</div>-->
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box mt-3">
                                          <div class="box-body">
                                              <h4 class="mb-2"><strong>INFO</strong></h4>
                                            <div class="row">
                                                 <div class="col-md-12">
                                                    <h5><strong>STATUS</strong></h5>
                                                     <h5>{{ucfirst($autokyc_details->status)}}</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>INQUIRY ID</strong></h5>
                                                     <h5>{{$autokyc_details->proof_id}}</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>REFERENCE ID</strong></h5>
                                                     <h5>{{$autokyc_details->reference_id}}</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>ACCOUNT ID</strong></h5>
                                                     <h5>{{$autokyc_details->account_id}}</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5><strong>CREATED AT</strong></h5>
                                                     <h5>{{$autokyc_details->created_at}}</h5>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="box mt-4">
                                          <div class="box-body">
                                              <h4 class="mb-2"><strong>Location</strong></h4>
                                                <div class="row">
                                                 <div class="col-md-12">
                                                    <div>{{$autokyc_details->region_name}}</div>
                                                    <div>{{$autokyc_details->country_name}}</div>
                                                    <div><strong>DEVICE</strong> ({{$autokyc_details->os_name}}  {{$autokyc_details->os_full_version}} {{$autokyc_details->device_name}})</div>
                                                 </div>
                                                </div>
                                          </div>
                                        </div>
                                        <div class="box mt-4">
                                          <div class="box-body">
                                              <h4 class="mb-2"><strong>Network  Details</strong></h4>
                                                <div class="row">
                                                 <div class="col-md-12">
                                                    <strong>IP ADDRESS:</strong>	{{$autokyc_details->ip_address}}
                                                </div>
                                                </div>
                                                <div class="row">
                                                <div class="col-md-12">
                                                    <strong>NETWORK THREAT LEVEL:</strong>	{{$autokyc_details->threat_level}}
                                                 </div>
                                                 <div class="col-md-12">
                                                      <strong>LATITUDE:</strong>	{{$autokyc_details->latitude}}
                                                 </div>
                                                </div>
                                                <div class="row">
                                                <div class="col-md-12">
                                                   <strong>LONGITUDE:</strong>	{{$autokyc_details->longitude}}
                                                 </div>
                                                 <div class="col-md-12">
                                                  <strong>DEVICE TYPE:</strong>	{{$autokyc_details->os_name}},{{$autokyc_details->os_full_version}},{{$autokyc_details->device_name}}
                                                 </div>
                                                </div>
                                                <div class="row">
                                                  <div class="col-md-12">
                                                 <strong> DEVICE OS:</strong>	{{$autokyc_details->os_name}}
                                                 </div>
                                                 <div class="col-md-12">
                                                      <strong>BROWSER:</strong> {{$autokyc_details->browser_name}}
                                                 </div>
                                                </div>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                    	    @else
                    	        <div class="box">
                            		<div class="box-body">
                            		    <h3 class="text-center" style="margin-bottom: 20px;">KYC Not Updated</h3>
                            		</div>
                            	</div>
                    	    @endif
                    	</div>
                    </div>
                </div>
                <div class="tab-hide fade" id="manualkycdiv">
                    @if(count($documentVerificationStatus) > 0)
                    	<div class="box">
                    		<div class="box-body">
                    		    @foreach($documentVerificationStatus as $documentVerification)
                    		        <?php 
                    			        $back_photo = DB::table('files')->where('id', $documentVerification->file_back_id)->first();
                    			        
                    			        if($documentVerification->country){
                                             $country=  DB::table('countries')->where('short_name',$documentVerification->country??'')->first()->name??'';
                                        }else{
                                            $country=$documentVerification->country??'';
                                        }
                                        
                                        $location = $documentVerification->city.' | '. $country;
                                        
                                        if(!empty($documentVerification->updated_by)){
                                            $admins=  DB::table('admins')->where('id', $documentVerification->updated_by)->first();
                                            if(!empty($admins)){
                                                $updated_by = $admins->first_name.' '.$admins->last_name;
                                            }else{
                                                $updated_by='-';
                                            }
                                        }else{
                                            $updated_by='-';
                                        }
                    			    ?>
                    		        <div class="panel panel-default">
                        			    <div class="panel-body">
                        			        <h3 class="text-center">{{ucfirst($documentVerification->verification_type)}} Proof
                            			        @if($documentVerification->status ==  'approved') <span class="badge badge-success" style="padding: 5px 10px; background-color: green;"> Verified </span> @endif
                            			        @if($documentVerification->status ==  'pending') <span class="badge badge-primary" style="padding: 5px 10px; background-color: blue;"> Unverified </span> @endif
                            			        @if($documentVerification->status ==  'rejected') <span class="badge badge-danger" style="padding: 5px 10px; background-color: red;"> Rejected </span> @endif
                            			    </h3> 
                        			        <hr>
                        			        <div class="row">
                            					<form action="{{ url('admin/users/kyc-verications/update') }}" class="form-horizontal" method="POST">
                            						{{ csrf_field() }}
                            				        <input type="hidden" value="{{ $documentVerification->id }}" name="id">
                            				        <input type="hidden" value="{{ $documentVerification->user_id }}" name="user_id">
                            				        <input type="hidden" value="{{ $documentVerification->verification_type }}" name="verification_type">
                            
                            						<div class="col-md-6">
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="user">User</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{ isset($documentVerification->user) ? $documentVerification->user->first_name.' '.$documentVerification->user->last_name :"-" }}</p>
                    										</div>
                    									</div>
                    									
                    				                    <div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">Identity Type</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{ str_replace('_', ' ', ucfirst($documentVerification->identity_type)) }}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_number">Document</label>
                    										<div class="col-sm-7">
                    										    @if(!empty($documentVerification->file->filename))
                        										    <p class="form-control-static">
                        										        {{ $documentVerification->file->filename }}
                        										        <a class="text-info" style="margin-left: 10px" href="{{ url('public/uploads/user-documents/'.$documentVerification->verification_type.'-proof-files').'/'.$documentVerification->file->filename }}" target="_blank">
                        													<i class="fa fa-eye"></i>
                                                                        </a>
                        												<a class="text-info" style="margin-left: 10px" href="{{ url('public/uploads/user-documents/'.$documentVerification->verification_type.'-proof-files').'/'.$documentVerification->file->filename }}" download target="_blank">
                        													<i class="fa fa-download"></i>
                                                                        </a>
                        										    </p>
                        										@endif
                    										    
                    										    @if(!empty($back_photo))
                        										    <p class="form-control-static">
                        										        {{ $back_photo->filename }}
                        										        <a class="text-info" style="margin-left: 10px" href="{{ url('public/uploads/user-documents/identity-proof-files').'/'.$documentVerification->file->filename }}" target="_blank">
                        													<i class="fa fa-eye"></i>
                                                                        </a>
                        												<a class="text-info" style="margin-left: 10px" href="{{ url('public/uploads/user-documents/identity-proof-files').'/'.$documentVerification->file->filename }}" download target="_blank">
                        													<i class="fa fa-download"></i>
                                                                        </a>
                        										    </p>
                        										@endif
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">Uploaded On</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{ Carbon\Carbon::parse($documentVerification->created_at)->format('d-M-Y h:i A') }}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">Updated On</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{ Carbon\Carbon::parse($documentVerification->updated_at)->format('d-M-Y h:i A') }}</p>
                    										</div>
                    									</div>
                            						</div>
                                    
                                    				<div class="col-md-6">
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">Updated By</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{ $updated_by }}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">User Location</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{ $location }}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">App Version</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{$documentVerification->app_ver}}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">User Device</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{$documentVerification->device_manufacture.' | '.$documentVerification->device_name.' | '.$documentVerification->device_model}}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">Operating System</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{$documentVerification->device_os.' '.$documentVerification->os_ver}}</p>
                    										</div>
                    									</div>
                    									
                    									<div class="form-group">
                    										<label class="control-label col-sm-5" for="identity_type">IP Address</label>
                    										<div class="col-sm-7">
                    										  <p class="form-control-static">{{$documentVerification->ip_address}}</p>
                    										</div>
                    									</div>
                                    				</div>
                                    
                            						<div class="col-md-12">
                            						    <div class="form-group">
                    										<label class="control-label col-sm-4" for="status">Change Status</label>
                    										<div class="col-sm-3 text-left">
                    											<select class="form-control select2" name="status" style="width: 60%;">
                    												<option value="approved" {{ $documentVerification->status ==  'approved'? 'selected':"" }}>Verified</option>
                    												<option value="pending"  {{ $documentVerification->status == 'pending' ? 'selected':"" }}>Unverified</option>
                    												<option value="rejected"  {{ $documentVerification->status == 'rejected' ? 'selected':"" }}>Rejected</option>
                    											</select>
                    										</div>
                    										<div class="col-sm-3">
                    									        <button type="submit" class="btn button-secondary">Update</button>
                    										</div>
                    									</div>
                    								</div>
                            					</form>
                                    		</div>
                                    	</div>
                        		    </div>
                            	@endforeach
                    		</div>
                    	</div>
                    @else
                        <div class="box">
                    		<div class="box-body">
                    		    <h3 class="text-center" style="margin-bottom: 20px;">No Documents Uploaded</h3>
                    		</div>
                    	</div>
                    @endif 
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script type="text/javascript">
    	$(".select2").select2({});
    	
    	$('.tab-links-link').click(function() {
          $('.tab-links-link').each(function() {
            var hashValue = this.href.split('#');
            $('a[href$="'+hashValue[1]+'"]').removeClass("link-active");
            $("#" + hashValue[1] + 'div').removeClass("content-active").addClass( "tab-hide fade" );
          });
          
          var hashValue = this.href.split('#');
          $('a[href$="'+hashValue[1]+'"]').addClass("link-active");
          $("#" + hashValue[1] + 'div').removeClass( "tab-hide fade" ).addClass( "content-active" );
        }); 
    </script>
@endpush