@extends('user_dashboard.layouts.app')
@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mb20 marginTopPlus">
                    @include('user_dashboard.layouts.common.alert')
                    <div class="card">
                        <div class="card-header"><h4 class="float-left">@lang('message.dashboard.deposit.deposit-stripe-form.title')</h4></div>
                        <div class="card-body">
                            <form action="{{URL::to('deposit/stripe_payment_store')}}" method="post" id="payment-form">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="text-center" for="usr">@lang('message.dashboard.deposit.deposit-stripe-form.card-no')</label>
                                                    <!--<div id="card-number"></div>-->
                                                    <input type="text" class="form-control" name="cardNumber" maxlength="19" id="cardNumber" onkeypress="return isNumber(event)">
                                                    <div id="card-errors" class="error"></div>
                                                </div>
                                            </div>
            
                                            <div class="col-md-12">
                                                <div class="form-group mb-0">
                                                    <div class="row">
                                                        <div class="col-md-3 pr-4">
                                                            <label for="usr">{{ __('Month') }}</label>
                                                            <div>
                                                                <select class="form-control" name="month" id="month">
                                                                    <option value="01">01</option>
                                                                    <option value="02">02</option>
                                                                    <option value="03">03</option>
                                                                    <option value="04">04</option>
                                                                    <option value="05">05</option>
                                                                    <option value="06">06</option>
                                                                    <option value="07">07</option>
                                                                    <option value="08">08</option>
                                                                    <option value="09">09</option>
                                                                    <option value="10">10</option>
                                                                    <option value="10">11</option>
                                                                    <option value="12">12</option>
                                                                </select>
                                                            </div>
                                                        </div>
            
                                                        <div class="col-md-4 mt-4 mt-md-0 pr-4">
                                                            <label for="usr">{{ __('Year') }}</label>
                                                            <input type="text" class="form-control" name="year" id="year" maxlength="2" onkeypress="return isNumber(event)">
                                                        </div>
            
                                                        <div class="col-md-5 mt-4 mt-md-0">
                                                            <div class="form-group">
                                                                <label for="usr">{{ __('cvc') }}</label>
                                                                <input type="text" class="form-control" name="cvc" id="cvc" maxlength="4" onkeypress="return isNumber(event)">
                                                                <div id="card-cvc"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <p class="text-danger" id="stripeError"></p>
                                                </div>
                                            </div>
            
                                            <div class="col-md-12">
                                                <div class="row m-0 justify-content-between">
                                                    <div>
                                                        <a href="#" class="deposit-confirm-back-btn">
                                                            <button class="btn btn-grad deposit-confirm-back-btn"><strong>@lang('message.dashboard.button.back')</strong></button>
                                                        </a>
            
                                                    </div>
            
                                                    <div>
                                                        <button type="submit" class="btn btn-grad px-4 py-2 float-left" id="deposit-stripe-submit-btn">
                                                            <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit-stripe-submit-btn-txt" style="font-weight: bolder;">@lang('message.form.submit')</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('user_dashboard.layouts.common.help')
@endsection
@section('js')

    <!--<script src="{{asset('public/dist/js/stripe-v3.js') }}" type="text/javascript"></script>-->
    <script src="https://js.stripe.com/v3/"></script>

    <script type="text/javascript">

        // Create a Stripe client
        var stripe = Stripe('{{$publishable}}');

        // Create an instance of Elements
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                position: 'relative',
                display: 'block',
                width: '100%',
                height: '34px ',
                border: '1px solid #d2d2d2',
                padding: '0 12px',
                color: 'rgba(56, 56, 56, 0.85)',
                margin: '0 0 10px 0',
                background: '#FFFFFF',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create an instance of the card Element
        var cardNumber = elements.create('cardNumber', {style: style});
        cardNumber.mount('#card-number');
        var cardExpiry = elements.create('cardExpiry', {style: style});
        cardExpiry.mount('#card-expiry');
        var cardCvc = elements.create('cardCvc', {style: style});
        cardCvc.mount('#card-cvc');

        // Handle real-time validation errors from the card Element.
        cardNumber.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event)
        {
            event.preventDefault();

            $("#deposit-stripe-submit-btn").attr("disabled", true);
            $(".fa-spin").show();
            $("#deposit-stripe-submit-btn-txt").text("{{__('Submitting...')}}");

            stripe.createToken(cardNumber).then(function (result)
            {
                if (result.error)
                {
                    $(".fa-spin").hide();
                    $("#deposit-stripe-submit-btn-txt").text('Submit');
                    $("#deposit-stripe-submit-btn").removeAttr("disabled");

                    // Inform the user if there was an error
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    return false;
                }
                else
                {
                    // Send the token to your server
                    stripeTokenHandler(result.token);
                    form.submit();
                }
            });
        });
        function stripeTokenHandler(token) {
            $('#payment-form').append('<input type="hidden" name="stripeToken" value="' + token.id + '">');
        }
        $(document).ready(function() {
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };
        });
    </script>
@endsection