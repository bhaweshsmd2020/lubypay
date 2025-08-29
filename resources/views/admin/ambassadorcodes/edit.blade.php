@extends('admin.layouts.master')
@section('title', 'Edit Ambassador Code')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Ambassador Code</h3>
                </div>
                <form action="{{ url('admin/update-ambassador-code', $ambassadorcode->id) }}" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="created_for">
                                Select Ambassador
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="created_for" name="created_for" required>
                                    @foreach($ambassadors as $ambassador)
                                        <option value="{{ $ambassador->id }}" @if($ambassadorcode->created_for == $ambassador->id) selected @endif>{{ $ambassador->first_name }} {{ $ambassador->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="code">
                                Code
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Code" id="code" name="code" value="{{ $ambassadorcode->code }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="fixed_discount">
                                Fixed Discount
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Fixed Discount" id="fixed_discount" name="fixed_discount" value="{{ $ambassadorcode->fixed_discount }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="percentage_discount">
                                Percentage Discount
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Percentage Discount" id="percentage_discount" name="percentage_discount" value="{{ $ambassadorcode->percentage_discount }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="total_uses">
                                Total Code Uses
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Total Code Uses" id="total_uses" name="total_uses" value="{{ $ambassadorcode->total_uses }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="individual_uses">
                                Individual Code Uses
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Individual Code Uses" id="individual_uses" name="individual_uses" value="{{ $ambassadorcode->individual_uses }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="expires_on">
                                Expire On
                            </label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" placeholder="Expire On" id="expires_on" name="expires_on" value="{{ $ambassadorcode->expires_on }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1" @if($ambassadorcode->status == '1') selected @endif>Active</option>
                                    <option value="0" @if($ambassadorcode->status == '0') selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description">{{ $ambassadorcode->description }}</textarea>
                            </div>
                        </div>
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_ambassador_codes'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/ambassador-codes') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>
    </div>
    
@endsection