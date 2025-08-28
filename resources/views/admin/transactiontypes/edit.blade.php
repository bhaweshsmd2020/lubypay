@extends('admin.layouts.master')
@section('title', 'Edit Transaction Type')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Transaction Type</h3>
                </div>
                <form action="{{ url('admin/update-transactiontype', $transactiontype->id) }}" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Name
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Name" id="name" name="name" value="{{ $transactiontype->name }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description">{{ $transactiontype->description }}</textarea>
                            </div>
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="orderby">
                                Order By
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Order By" id="orderby" name="orderby" value="{{ $transactiontype->orderby }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="type">
                                Type
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="1" @if($transactiontype->type == '1') selected @endif>Transaction</option>
                                    <option value="2" @if($transactiontype->type == '2') selected @endif>Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1" @if($transactiontype->status == '1') selected @endif>Active</option>
                                    <option value="0" @if($transactiontype->status == '0') selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_transactiontypes'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/transactiontypes') }}">Cancel</a>
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