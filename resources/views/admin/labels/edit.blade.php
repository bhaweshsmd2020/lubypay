@extends('admin.layouts.master')

@section('title', 'Add Labels')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Edit Label</h3>
                </div>
                <form action="{{ url('admin/labels/update/'.$allabels->id) }}" class="form-horizontal" method="POST">

                    <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

                        <div class="box-body">
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="string">
                                    String
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="string" name="string" value="{{ $allabels->string }}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="english">
                                    English
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="english" name="english" value="{{ $allabels->en }}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="spanish">
                                    Spanish
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="spanish" name="spanish" value="{{ $allabels->es }}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="portogues">
                                    Portogues
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="portogues" name="portogues" value="{{ $allabels->pt }}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="vietnamese">
                                    Vietnamese
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="vietnamese" name="vietnamese" value="{{ $allabels->vn }}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label require" for="active">Status</label>
                                <div class="col-sm-6">
                                    <select class="select2" name="active" id="active">
                                        <option value='1' <?php if($allabels->active == 1){ echo "selected"; } ?>>Active</option>
                                        <option value='0' <?php if($allabels->active == 0){ echo "selected"; } ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="box-footer">
                                <a class="btn btn-danger btn-flat pull-left" href="{{ url('admin/labels') }}" id="users_cancel">Cancel</a>
                                <button type="submit" class="btn btn-primary pull-right btn-flat">Update</button>
                            </div>
                        </div>
                    </input>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(function () {
            $(".select2").select2({
            });
        });
    </script>
@endsection

