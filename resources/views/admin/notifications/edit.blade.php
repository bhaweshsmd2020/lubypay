@extends('admin.layouts.master')
@section('title', 'Edit Deposit')

<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">

@section('page_content')
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h4 class="text-left">Update Offers for Customers</h4>
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<form action="{{ url('admin/update-offer/'.$offerData->id) }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
									{{ csrf_field() }}
									
									<div class="form-group">
										<label class="control-label col-sm-3" for="deposit_uuid">Offer Title</label>
										<div class="col-sm-9">
										    <input type="text" class="form-control" name="title" value="{{ $offerData->offer_title }}">
									    </div>
									</div>
								
									<div class="form-group">
                                        <label class="col-sm-3 control-label require" for="app_redirect">Select Redirect Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="app_redirect" id="app_redirect">
                                                <option value='0' {{($offerData->app_redirect=='0') ? 'selected' :''}}>None</option>
                                                <option value='1' {{($offerData->app_redirect=='1') ? 'selected' :''}}>App Page</option>
                                                <option value='2' {{($offerData->app_redirect=='2') ? 'selected' :''}}>Redirect URL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="inputEmail3"> App Page </label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="app_page" id="app_page">
                                                <option value='Top'>Select App Page</option>
                                                @foreach($pages as $page)
                                                    <option value='{{$page->id}}' {{($page->id ==$offerData->app_page) ? 'selected' :''}}>{{$page->page_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                	<div class="form-group">
										<label class="control-label col-sm-3" for="deposit_uuid">Redirect URL</label>
										<div class="col-sm-9">
										    <input type="text" class="form-control" name="add_url" value="{{ $offerData->offer_url }}">
									    </div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3" for="deposit_uuid">Description</label>
										<div class="col-sm-9">
										    <input type="text" class="form-control" name="message" value="{{ $offerData->offer_desc }}">
									    </div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3" for="deposit_uuid">Offer Date & Time</label>
										<div class="col-sm-9">
										    <input required type="datetime-local" class="form-control" name="date_time" value="{{ $offerData->expire_date_time }}">
										    <p>{{ $offerData->expire_date_time }}</p>
									    </div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3" for="deposit_uuid">Offer Image</label>
										<div class="col-sm-9">
										    <input  type="file"  name="offer_image" class="form-control" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])"><br>
										    <img src="{{asset('public/'. $offerData->offer_image)}}" alt="your image" height="100" />
									    </div>
									</div>
									@php 
                                        $language = DB::table('languages')->where('status','Active')->get();
                                    @endphp
    								<div class="form-group">
                                        <label class="col-sm-3 control-label" for="inputEmail3"> Language </label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="language" id="language" required>
                                                @foreach($language as $page)
                                                <option value='{{$page->id}}' @if($page->id==$offerData->language) selected @endif  >{{$page->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="inputEmail3"> Platform </label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="platform" id="platform" required>
                                                <option value='ewallet' @if($offerData->platform== 'ewallet') selected @endif  >Ewallet</option>
                                                <option value='mpos' @if($offerData->platform== 'mpos') selected @endif  >Mpos</option>
                                            </select>
                                        </div>
                                    </div>

									<div class="row">
										<div class="col-md-11">
											<div class="col-md-2"></div>
											<div class="col-md-2"><a id="cancel_anchor" class="btn btn-danger pull-left" href="{{ url('admin/pushsms') }}">Cancel</a></div>
											<div class="col-md-1">
												<button type="submit" class="btn button-secondary pull-right">
	                                                 <span id="deposits_edit_text">Update</span>
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
@endsection
