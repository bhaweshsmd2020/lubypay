@extends('admin.layouts.master')
@section('title', 'Edit Payment Method')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Payment Method</h3>
                </div>
                <form action="{{ url('admin/update-paymentmethod', $paymentmethod->id) }}" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Name
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Name" id="name" name="name" value="{{ $paymentmethod->name }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description">{{ $paymentmethod->description }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="icon">
                                Icon
                            </label>
                            <div class="col-sm-6">
                                <input class="form-control" placeholder="Icon" name="icon" type="file" id="icon">
                                @if(!empty($paymentmethod->icon))
                                    <img src="{{ url('public/uploads/paymentmethods/' . $paymentmethod->icon) }}"  style="width: auto; height: 50px; margin-top: 15px;">
                                @else
                                    <img src="{{ url('public/uploads/paymentmethods/paymentmethods.png') }}"  style="width: auto; height: 50px; margin-top: 15px;">
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="has_permission">
                                Has permission
                            </label>
                            <div class="col-sm-6">
                                @php
                                    $permissions = $paymentmethod->has_permission ? explode(',', $paymentmethod->has_permission) : [];
                                @endphp

                                <select class="form-control select2" id="has_permission" name="has_permission[]" multiple>
                                    <option value="Subscription" {{ in_array('Subscription', $permissions) ? 'selected' : '' }}>Subscription</option>
                                    <option value="Deposit" {{ in_array('Deposit', $permissions) ? 'selected' : '' }}>Deposit</option>
                                    <option value="Withdrawal" {{ in_array('Withdrawal', $permissions) ? 'selected' : '' }}>Withdrawal</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Active" @if($paymentmethod->status == 'Active') selected @endif>Active</option>
                                    <option value="Inactive" @if($paymentmethod->status == 'Inactive') selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_paymentmethods'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/paymentmethods') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Update</button>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#has_permission').select2({
                placeholder: "Select permissions"
            });
        });
    </script>    
@endsection