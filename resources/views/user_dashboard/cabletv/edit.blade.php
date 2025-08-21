@extends('admin.layouts.master')

@section('title', 'Pay Cable Bill')

@section('page_content') 

  
    <div class="col-md-9">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">Pay Cable Bill</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url('admin/utility/edit-cable-bill') }}" class="form-horizontal" id="pay_cable_form">
          {{ csrf_field() }}
          <input type="hidden" name="id" value="{{$result->id}}">
          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="user_id">Select User</label>
              <div class="col-sm-6">
                <select name="user_id" class="form-control select2" placeholder="Select User" id="user_id">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                    <option @if($result->user_id == $user->id) selected  @endif value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endforeach
                </select>
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
                <select name="country_id" class="form-control select2" placeholder="Select Country" id="country_id">
                    <option value="">Select Country</option>
                    @foreach($country as $countries)
                    <option @if($result->country_id == $countries->id) selected  @endif value="{{$countries->id}}">{{$countries->name}}</option>
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
              <label class="col-sm-3 control-label" for="operator_name">Operator</label>
              <div class="col-sm-6">
                <select name="operator_id" class="form-control select2" placeholder="Select Operator" id="operator_id">
                    <option value="">Select Operator</option>
                    @foreach($operators as $operator)
                    <option @if($result->operator_id == $operator->id) selected  @endif value="{{$operator->id}}">{{$operator->type}}</option>
                    @endforeach
                </select>                
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
                <input type="text" name="account_id" class="form-control" value="{{ $result->account_id }}" placeholder="Enter your Account ID" id="account_id">
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
                <div class="col-sm-6">
                <select name="billing_month" class="form-control select2 col-md-3" placeholder="Select Month" id="billing_month">
                    <option value="">Select Month</option>
                    @php 
                    $months = array(
                        'January',
                        'February',
                        'March',
                        'April',
                        'May',
                        'June',
                        'July ',
                        'August',
                        'September',
                        'October',
                        'November',
                        'December',
                    );
                    @endphp
                    @php $i=0; @endphp
                        @foreach($months as $month)
                        <option @if($result->billing_month == $i) selected  @endif value="{{$i}}">{{$month}}</option>
                    @php $i++; @endphp 
                        @endforeach
                </select>
                </div>
                <div class="col-sm-6">
                <select name="billing_year" class="form-control select2 col-md-3" placeholder="Select Year" id="billing_year">
                    <option value="">Select Year</option>
                    @for($i=date('Y');$i<=2050;$i++)
                    <option  @if($result->billing_year == $i) selected  @endif value="{{$i}}">{{$i}}</option>
                    @endfor
                </select>
                </div>                
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
                <input type="number" name="amount" class="form-control" value="{{ $result->amount }}" placeholder="Enter Amount" id="amount">
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
                <select name="currency_id" class="form-control select2" placeholder="Select Currency" id="currency_id">
                    <option value="">Select Currency</option>
                    @foreach($currencys as $currency)
                    <option @if($result->currency_id == $currency->id) selected  @endif value="{{$currency->id}}">{{$currency->name}}</option>
                    @endforeach
                </select>                
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
                <select name="payment_method_id" class="form-control select2" placeholder="Select Payment Method" id="payment_method_id">
                    <option value="">Select Payment Method</option>
                    @foreach($paymentmethods as $paymentmethod)
                    <option @if($result->payment_method_id == $paymentmethod->id) selected  @endif value="{{$paymentmethod->id}}">{{$paymentmethod->name}}</option>
                    @endforeach
                </select>  
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
                <input type="text" name="remarks" class="form-control" value="{{ $result->remarks }}" placeholder="Enter Remarks" id="remarks">
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
                <select name="status" class="form-control select2" placeholder="Select Status" id="status">
                    <option value="">Select Status</option>
                    <option @if($result->status == 'Pending') selected  @endif value="Pending">Pending</option>
                    <option @if($result->status == 'Success') selected  @endif value="Success">Success</option>
                    <option @if($result->status == 'Refund') selected   @endif value="Refund">Refund</option>
                    <option @if($result->status == 'Blocked') selected  @endif value="Blocked">Blocked</option>
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
            <a class="btn btn-danger" href="{{ url('admin/utility/cable') }}">Cancel</a>
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
        digits: false
      },
      remarks: {
        required: false,
        lettersonly: false,
      },
    },
    messages: {
    },
  });

</script>
@endpush