@extends('admin.layouts.master')

@section('title', 'Utility')

@section('page_content') 

  
    <div class="col-md-9">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Utility</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/utility/add') }}" class="form-horizontal" id="utility_form">
          {{ csrf_field() }}

          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="country_id">Select Country</label>
              <div class="col-sm-6">
                <select name="country_id" class="form-control select2" placeholder="Select Country" id="country_id">
                    <option value="">Select Country</option>
                    @foreach($country as $countries)
                    <option value="{{$countries->id}}">{{$countries->name}}</option>
                    @endforeach
                </select>
                @if($errors->has('country_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('country_id') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="type">Type</label>
              <div class="col-sm-6">
                <input type="text" name="type" class="form-control" value="{{ old('type') }}" placeholder="Select Type" id="type">
                @if($errors->has('type'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('type') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="provider_name">Provider Name</label>
              <div class="col-sm-6">
                <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name') }}" placeholder="Enter your Provider Name" id="provider_name">
                @if($errors->has('provider_name'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('provider_name') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-sm-3 control-label" for="status">Status</label>
              <div class="col-sm-6">
                <select name="status" class="form-control select2" placeholder="Select Status" id="status">
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                @if($errors->has('status'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('status') }}</strong>
                </span>
                @endif
              </div>
            </div>

          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/utility') }}">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right">&nbsp; Add &nbsp;</button>
          </div>
        </form>
      </div>
    </div>

@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<!-- jquery.validate additional-methods -->
<script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

  jQuery.validator.addMethod("letters_with_spaces", function(value, element)
  {
    return this.optional(element) || /^[A-Za-z ]+$/i.test(value); //only letters
  }, "Please enter letters only!");

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
  });

  $('#utility_form').validate({
    rules: {
      country_id: {
        required: true,
      },
      provider_name: {
        required: true,
      }, 
      type: {
        required: true,
      },
      status: {
        required: true,
      },
    },
    messages: {
    },
  });

</script>
@endpush