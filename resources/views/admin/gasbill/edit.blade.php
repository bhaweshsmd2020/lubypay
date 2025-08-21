@extends('admin.layouts.master')

@section('title', 'Pay Gas Bill')

@section('page_content') 

  
    <div class="col-md-9">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Edit Gas Bill</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/utility/edit-gas-bill') }}" class="form-horizontal" id="pay_cable_form">
          {{ csrf_field() }}

          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="user_id">Select User</label>
              <div class="col-sm-6">
                <input type="text" name="user_id" class="form-control" value="{{ old('user_id') }}" placeholder="Select User" id="user_id">
                @if($errors->has('user_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('user_id') }}</strong>
                </span>
                @endif
              </div>
            </div>  
              
            <div class="form-group">
              <label class="col-sm-3 control-label" for="country_id">Select Country</label>
              <div class="col-sm-6">
                <input type="text" name="country_id" class="form-control" value="{{ old('country_id') }}" placeholder="Select Country" id="country_id">
                @if($errors->has('country_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('country_id') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="operator_name">Operator</label>
              <div class="col-sm-6">
                <input type="text" name="operator_name" class="form-control" value="{{ old('operator_name') }}" placeholder="Select Operator" id="operator_name">
                @if($errors->has('operator_name'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('operator_name') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="account_id">Account ID</label>
              <div class="col-sm-6">
                <input type="text" name="account_id" class="form-control" value="{{ old('account_id') }}" placeholder="Enter your Account ID" id="account_id">
                @if($errors->has('account_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('account_id') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="billing_month">Billing Month</label>
              <div class="col-sm-6">
                <input type="text" name="billing_month" class="form-control" value="{{ old('billing_month') }}" placeholder="Select Billing Month" id="billing_month">
                @if($errors->has('billing_month'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('billing_month') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="amount">Amount</label>
              <div class="col-sm-6">
                <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="Enter Amount" id="amount">
                @if($errors->has('amount'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('amount') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-sm-3 control-label" for="currency_id">Select Currency</label>
              <div class="col-sm-6">
                <input type="number" name="currency_id" class="form-control" value="{{ old('currency_id') }}" placeholder="Select Currency" id="currency_id">
                @if($errors->has('currency_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('currency_id') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-sm-3 control-label" for="payment_method_id">Select Payment Method</label>
              <div class="col-sm-6">
                <input type="number" name="payment_method_id" class="form-control" value="{{ old('payment_method_id') }}" placeholder="Select Payment Method" id="payment_method_id">
                @if($errors->has('payment_method_id'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('payment_method_id') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="remarks">Remarks</label>
              <div class="col-sm-6">
                <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Enter Remarks" id="remarks">
                @if($errors->has('remarks'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('remarks') }}</strong>
                </span>
                @endif
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-sm-3 control-label" for="status">Status</label>
              <div class="col-sm-6">
                <input type="text" name="status" class="form-control" value="{{ old('status') }}" placeholder="Enter Remarks" id="status">
                @if($errors->has('status'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('status') }}</strong>
                </span>
                @endif
              </div>
            </div>

          </div>

          <div class="box-footer">
            <a class="btn btn-danger" href="{{ url('admin/utility/gas') }}">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right">&nbsp; Proceed to Pay &nbsp;</button>
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

  $('#pay_cable_form').validate({
    rules: {
      country_id: {
        required: true,
        maxlength: 10,
        digits: true,
      },
      operator_name: {
        required: true,
        // letters_with_spaces: true,
      }, 
      account_number: {
        required: true,
        maxlength: 30,
        lettersonly: false,
      },
      billing_month: {
        required: true,
        digits: false
      },
      amount: {
        required: true,
        digits: true
      },
      remarks: {
        required: false,
        lettersonly: false,
      },
    },
    messages: {
      amount: {
        digits: "Please enter number only!",
      },
      
    },
  });

</script>
@endpush