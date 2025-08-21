@extends('admin.layouts.master')
@section('title', 'Add Services')

@section('page_content')

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.appsettings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                  <h3 class="box-title">Add Services</h3>
                </div>
                <form action="{{ url('admin/settings/services/add') }}" method="post" class="form-horizontal" id="api-credentials" enctype="multipart/form-data" >
                    {!! csrf_field() !!}

                    <!-- box-body -->
                    <div class="box-body">
                         <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Name
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Name" name="name" type="text" id="name" value="">
                                         @if($errors->has('name'))
                                        <span class="help-block">
                                          <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                       <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Page
                                        </label>
                                        <div class="col-sm-8">
                                         <select class="form-control" name="page" id="page">
                                            @if(!empty($app_pages))
                                               @foreach($app_pages as $value)
                                                 <option value="{{$value->app_page}}">{{$value->page_name}}</option>
                                               @endforeach
                                            @endif
                                         </select>    
                                         @if($errors->has('page'))
                                         <span class="help-block">
                                          <strong class="text-danger">{{ $errors->first('page') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                       <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                        Position
                                        </label>
                                        <div class="col-sm-8">
                                         <select class="form-control" name="position" id="position">
                                            <option value="Top">Top</option>
                                            <option value="Bottom">Bottom</option>
                                         </select>    
                                         @if($errors->has('position'))
                                        <span class="help-block">
                                          <strong class="text-danger">{{ $errors->first('position') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Status
                                        </label>
                                        <div class="col-sm-8">
                                         <select class="form-control" name="status" id="status">
                                              <option value="Active">Active</option>
                                              <option value="Inactive">Inactive</option>
                                              <option value="ComingSoon">Coming Soon</option>
                                              <option value="ServiceDown">Service Down</option>
                                         </select>    
                                         @if($errors->has('status'))
                                        <span class="help-block">
                                          <strong class="text-danger">{{ $errors->first('status') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            Sorting
                                        </label>
                                        <div class="col-sm-8">
                                       <input class="form-control" placeholder="Sorting" name="sorting" type="number" id="sorting" min=1 >
                                         @if($errors->has('sorting'))
                                        <span class="help-block">
                                          <strong class="text-danger">{{ $errors->first('sorting') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                             <label class="col-sm-4 control-label" for="inputEmail3">
                                            image
                                        </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" placeholder="Update First Name" name="image" type="file" id="image" value="">
                                         @if($errors->has('image'))
                                        <span class="help-block">
                                          <strong class="text-danger">{{ $errors->first('image') }}</strong>
                                        </span>
                                       @endif
                            </div>
                        </div>
                    <!-- box-footer -->
                    <div class="box-footer col-sm-8"></div>
                    <div class="box-footer col-sm-2">
                          <a href="{{url('admin/settings/services/view')}}" class="btn btn-danger btn-flat pull-right" type="submit">Cancel</a>
                    </div>
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_api_credentials'))
                        <div class="box-footer col-sm-2">
                          <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                        </div>
                    @endif
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>

@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

$.validator.setDefaults({
    highlight: function(element) {
        $(element).parent('div').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).parent('div').removeClass('has-error');
    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    }
});

$('#api-credentials').validate({
    rules: {
        name: {
            required: true,
        },
        page: {
            required: true,
        },
         position: {
            required: true,
        },
         status: {
            required: true,
        },
        sorting:{
            required: true,
        },
        image:{
            required: true,
        }
    },
});


</script>

@endpush
