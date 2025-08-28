@extends('admin.layouts.master')
@section('title', 'Add Transaction Type')
@section('page_content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Add Transaction Type</h3>
                </div>
                <form action="{{ url('admin/store-transactiontype') }}" class="form-horizontal" method="POST" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="url">
                                Name
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Name" id="name" name="name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">
                                Description
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" placeholder="Description" id="description" name="description"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="orderby">
                                Order By
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="Order By" id="orderby" name="orderby">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="type">
                                Type
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="1">Transaction</option>
                                    <option value="2">Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="status">
                                Status
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_transactiontypes'))
                        <div class="box-footer text-center">
                            <a class="btn btn-danger btn-flat" href="{{ url('admin/transactiontypes') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-flat">Create</button>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#has_permission').select2({
                placeholder: "Select permissions",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    
@endsection