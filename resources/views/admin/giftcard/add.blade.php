@extends('admin.layouts.master')

@section('title', 'Add Gift Card')

@section('page_content') 

  
    <div class="col-md-9">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Add Gift Card</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/card/add-gift-card') }}" class="form-horizontal" id="gift_card_form">
          {{ csrf_field() }}

          <div class="box-body">

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
              <label class="col-sm-3 control-label" for="card_number">Card Number</label>
              <div class="col-sm-6">
                <input type="text" name="card_number" class="form-control" value="{{ old('card_number') }}" placeholder="Enter Card Number" id="card_number">
                @if($errors->has('card_number'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('card_number') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="card_expiry">Expiry Date</label>
              <div class="col-sm-6">
                <input type="text" name="card_expiry" class="form-control" value="{{ old('card_expiry') }}" placeholder="Enter Card Expiry" id="card_expiry">
                @if($errors->has('card_expiry'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('card_expiry') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="card_cvv">Security CVV</label>
              <div class="col-sm-6">
                <input type="text" name="card_cvv" class="form-control" value="{{ old('card_cvv') }}" placeholder="Enter Card Security CVV" id="card_cvv">
                @if($errors->has('card_cvv'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('card_cvv') }}</strong>
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
              <label class="col-sm-3 control-label" for="issue_date">Card Issue Date</label>
              <div class="col-sm-6">
                <input type="number" name="issue_date" class="form-control" value="{{ old('issue_date') }}" placeholder="Enter Card Issue Date" id="issue_date">
                @if($errors->has('issue_date'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('issue_date') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="validity">Card Validity</label>
              <div class="col-sm-6">
                <input type="text" name="validity" class="form-control" value="{{ old('validity') }}" placeholder="Select Card Validity" id="validity">
                @if($errors->has('validity'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('validity') }}</strong>
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
            <a class="btn btn-danger" href="{{ url('admin/card/gift-card') }}">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right">&nbsp; Add Gift Card &nbsp;</button>
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

  $('#pay_insurance_form').validate({
    rules: {
      country_id: {
        required: true,
        maxlength: 10,
        digits: true,
      },
      card_number: {
        required: true,
        // letters_with_spaces: true,
      }, 
      card_expiry: {
        required: true,
        maxlength: 30,
        lettersonly: false,
      },
      card_cvv: {
        required: true,
        digits: false
      },
      amount: {
        required: true,
        digits: true
      },
      status: {
        required: true,
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