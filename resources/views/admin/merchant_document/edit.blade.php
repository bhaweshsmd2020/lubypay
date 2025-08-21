@extends('admin.layouts.master')

@section('title', 'Edit Merchant Group Document')

@section('page_content')

  <div class="row">
    <div class="col-md-3 settings_bar_gap">
      @include('admin.common.settings_bar')
    </div>
    <div class="col-md-9">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Edit Document for Merchant Package </h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/settings/edit-merchant-group-document') }}" class="form-horizontal" enctype="multipart/form-data" id="merchant-group-edit-form">
          {{ csrf_field() }}

          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="exampleFormControlInput1">Name</label>
              <div class="col-sm-6">
                  <input type="hidden" name="id" value="{{ $docs->id }}">
                <input type="text" name="name" class="form-control" value="{{ $docs->name }}" placeholder="{{ $docs->name }}" id="name">
                @if($errors->has('name'))
                  <span class="error">
                    <strong class="text-danger">{{ $errors->first('name') }}</strong>
                  </span>
                @endif
              </div>
            </div>
          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/settings/merchant-document') }}">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right">&nbsp; Edit &nbsp;</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

  $(function () {
      $(".select2").select2({
      });
  });

  jQuery.validator.addMethod("letters_with_spaces", function(value, element)
  {
    return this.optional(element) || /^[A-Za-z0-9 ]+$/i.test(value); //only letters
  }, "Please enter alphanumeric characters only!");

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
     errorPlacement: function (error, element) {
      if (element.prop('type') === 'checkbox') {
        $('#error-message').html(error);
      } else {
        error.insertAfter(element);
      }
    }
  });

  $('#merchant-group-edit-form').validate({
    rules: {
      name: {
        required: true,
        letters_with_spaces: true,
      }
    },
  });

</script>

@endpush
