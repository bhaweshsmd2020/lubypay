@extends('admin.layouts.master')
@section('title', 'Fees & Limits')

@section('head_style')
  <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/custom-checkbox.css') }}">
  <style type="text/css">
    .charge-range{
      border: 1px solid #ddd; 
      border-radius: 3px; 
      padding: 15px 0px 0px;
      margin-bottom: 15px;
    }

    @media only screen and (max-width: 767px) {
      .default_currency_side_text {
        font-size: 12px !important;
        float: right;
        margin: 0 0 10px;
      }
    }
  </style>
@endsection

@section('page_content')
  <div class="box box-default">
      <div class="box-body">
          <div class="row">
              <div class="col-md-12">
                  <div class="top-bar-title padding-bottom">Fees &amp; Limits</div>
              </div>
          </div>
      </div>
  </div>

  <div class="box">
    <div class="box-body">
      <div class="row">
        <div class="col-md-6">          
          <div class="dropdown pull-left" style="margin-top: 10px;">
            <button class="btn btn-default dropdown-toggle subscriptionSelectBtn" type="button" data-toggle="dropdown">
              Subscription : <span class="SubscriptionTitle">{{ $subscription->title }}</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu subscriptionSelect">
              @foreach($subscriptionList as $subscriptionItem)
                <li class="listItem 
                    {{ $subscriptionItem->id == $subscription->id ? 'active' : '' }}"
                    data-rel="{{ $subscriptionItem->id }}">
                  <a href="#">{{ ucfirst($subscriptionItem->title) }}</a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>

        <div class="col-md-6">          
          <div class="dropdown pull-right" style="margin-top: 10px;">
            <button class="btn btn-default dropdown-toggle currencySelectBtn" type="button" data-toggle="dropdown">
              Currency : <span class="currencyName">{{ $currency->name }}</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu currencySelect">
              @foreach($currencyList as $currencyItem)
                <li class="listItem 
                    {{ $currencyItem->id == $currency->id ? 'active' : '' }}"
                    data-rel="{{ $currencyItem->id }}">
                  <a href="#">{{ $currencyItem->name }}</a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3">       
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title underline">Transaction Type</h3>
        </div>
        <div class="box-body no-padding" style="display: block;">
          <ul class="nav nav-pills nav-stacked">
            @foreach($transactionTypeList as $transactionTypeItem)
              <li {{ isset($list_menu) &&  $list_menu == $transactionTypeItem->slug ? 'class=active' : ''}}>
                <a data-spinner="true" href="{{ url('admin/settings/feeslimit/'.$transactionTypeItem->slug.'/'.$subscription->id.'/'.$currency->id) }}">
                  {{ $transactionTypeItem->name }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">{{ $transaction_name }} Settings</h3>
        </div>

        <form action='{{url('admin/settings/feeslimit/update-deposit-limit')}}' class="form-horizontal" method="POST" id="deposit_limit_form">
          {!! csrf_field() !!}
          <input type="hidden" value="{{ isset($feeslimit->id) ? $feeslimit->id : '' }}" name="id" id="id">
          <input type="hidden" value="{{ $currency->id }}" name="currency_id" id="currency_id">
          <input type="hidden" value="{{ $transaction_type }}" name="transaction_type_id" id="transaction_type_id">
          <input type="hidden" value="{{ $trans_type }}" name="transaction_type" id="transaction_type">
          <input type="hidden" value="{{ $list_menu }}" name="tabText" id="tabText">
          <input type="hidden" value="{{ $currency->default }}" name="defaultCurrency" id="defaultCurrency">
          <input type="hidden" value="{{ $subscription->id }}" name="subscription_id" id="subscription_id">

          <div class="box-body">
            <div class="col-md-11 col-md-offset-1">
              <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <!-- <a> 1234 </a> -->
                    </h4>
                  </div>

                  <div class="panel-collapse">
                    <div class="panel-body">
                      @if ($currency->default == 1)
                          <div class="form-group">
                              <label class="col-sm-3 control-label default_currency_label" for="has_transaction">Is Activated</label>
                              <div class="col-sm-5">
                                  <label class="checkbox-container">
                                      <input 
                                          type="checkbox" 
                                          class="has_transaction" 
                                          name="has_transaction" 
                                          value="Yes"
                                          {{ isset($feeslimit) ? 'checked disabled' : 'checked' }}
                                          id="has_transaction"
                                      >
                                      <span class="checkmark"></span>
                                  </label>
                                  @if ($errors->has('has_transaction'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('has_transaction') }}</strong>
                                      </span>
                                  @endif
                              </div>
                              <div class="col-sm-4">
                                  <p>
                                      <span class="default_currency_side_text">
                                          {{ isset($feeslimit) ? 'Default currency is always active' : '' }}
                                      </span>
                                  </p>
                              </div>
                          </div>
                          <div class="clearfix"></div>
                      @else
                          <div class="form-group">
                              <label class="col-sm-3 control-label default_currency_label" for="has_transaction">Is Activated</label>
                              <div class="col-sm-5">
                                  <label class="checkbox-container">
                                      <input 
                                          type="checkbox" 
                                          class="has_transaction" 
                                          name="has_transaction" 
                                          value="Yes"
                                          {{ isset($feeslimit->has_transaction) && $feeslimit->has_transaction == 'Yes' ? 'checked' : '' }}
                                          id="has_transaction"
                                      >
                                      <span class="checkmark"></span>
                                  </label>
                                  @if ($errors->has('has_transaction'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('has_transaction') }}</strong>
                                      </span>
                                  @endif
                              </div>
                              <div class="col-sm-4 default_currency_side_text">
                                  <p><span class="default_currency_side_text"></span></p>
                              </div>
                          </div>
                          <div class="clearfix"></div>
                      @endif

                      @if($trans_type == 1)
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="min_balance">Minimum Balance</label>
                          <div class="col-sm-5">
                            <input class="form-control min_balance" name="min_balance" type="text" value="{{ isset($feeslimit->min_balance) ? number_format((float)$feeslimit->min_balance, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="min_balance" oninput="restrictNumberToPrefdecimal(this)">
                            <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                            @if ($errors->has('min_balance'))
                              <span class="help-block">
                                <strong>{{ $errors->first('min_balance') }}</strong>
                              </span>
                            @endif
                          </div>
                          <div class="col-sm-4">
                            <p>If not set, minimum balance is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                        <h4><strong>Limit 1</strong></h4>
                        <div class="charge-range border p-3">
                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="min_limit">Minimum Limit</label>
                            <div class="col-sm-5">
                              <input class="form-control min_limit" name="min_limit" type="text" value="{{ isset($feeslimit->min_limit) ? number_format((float)$feeslimit->min_limit, $preference['decimal_format_amount'], '.', '') : number_format((float)1.00000000, $preference['decimal_format_amount'], '.', '') }}" id="min_limit" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('min_limit'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('min_limit') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, minimum limit is {{ number_format((float)1.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="max_limit">Maximum Limit</label>
                            <div class="col-sm-5">
                              <input class="form-control max_limit" name="max_limit" type="text" value="{{ isset($feeslimit->max_limit) ? number_format((float)$feeslimit->max_limit, $preference['decimal_format_amount'], '.', '') : '' }}" id="max_limit" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('max_limit'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('max_limit') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, maximum limit is infinity</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="charge_percentage">Charge Percentage</label>
                            <div class="col-sm-5">
                              <input class="form-control charge_percentage" name="charge_percentage" type="text" value="{{ isset($feeslimit->charge_percentage) ?  number_format((float)$feeslimit->charge_percentage, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="charge_percentage" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('charge_percentage'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('charge_percentage') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, charge percentage is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="charge_fixed">Charge Fixed</label>
                            <div class="col-sm-5">
                              <input class="form-control charge_fixed" name="charge_fixed" type="text" value="{{ isset($feeslimit->charge_fixed) ? number_format((float)$feeslimit->charge_fixed, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="charge_fixed" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('charge_fixed'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('charge_fixed') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, charge fixed is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <h4><strong>Limit 2</strong></h4>
                        <div class="charge-range border p-3">
                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="second_min_limit">Minimum Limit</label>
                            <div class="col-sm-5">
                              <input class="form-control second_min_limit" name="second_min_limit" type="text" value="{{ isset($feeslimit->second_min_limit) ? number_format((float)$feeslimit->second_min_limit, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="second_min_limit" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('second_min_limit'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('second_min_limit') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, minimum limit is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="second_max_limit">Maximum Limit</label>
                            <div class="col-sm-5">
                              <input class="form-control second_max_limit" name="second_max_limit" type="text" value="{{ isset($feeslimit->second_max_limit) ? number_format((float)$feeslimit->second_max_limit, $preference['decimal_format_amount'], '.', '') : '' }}" id="second_max_limit" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('second_max_limit'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('second_max_limit') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, maximum limit is infinity</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="second_charge_percentage">Charge Percentage</label>
                            <div class="col-sm-5">
                              <input class="form-control second_charge_percentage" name="second_charge_percentage" type="text" value="{{ isset($feeslimit->second_charge_percentage) ?  number_format((float)$feeslimit->second_charge_percentage, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="second_charge_percentage" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('second_charge_percentage'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('second_charge_percentage') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, charge percentage is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="second_charge_fixed">Charge Fixed</label>
                            <div class="col-sm-5">
                              <input class="form-control second_charge_fixed" name="second_charge_fixed" type="text" value="{{ isset($feeslimit->second_charge_fixed) ? number_format((float)$feeslimit->second_charge_fixed, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="second_charge_fixed" oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                              @if ($errors->has('second_charge_fixed'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('second_charge_fixed') }}</strong>
                                </span>
                              @endif
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, charge fixed is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        </div>
                          
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="recom_amt">Recommended Amount</label>
                          <div class="col-sm-5">
                            <input class="form-control recom_amt" name="recom_amt" type="text" value="{{$feeslimit->recom_amt??''}}" id="recom_amt" oninput="restrictNumberToPrefdecimal(this)">
                            <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                            @if ($errors->has('recom_amt'))
                              <span class="help-block">
                                <strong>{{ $errors->first('recom_amt') }}</strong>
                              </span>
                            @endif
                          </div>
                          <div class="col-sm-4">
                            <p>Add Recommended Amount , Comma Seprated!</p>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="description">Description</label>
                          <div class="col-sm-5">
                            <textarea class="form-control description" name="description" id="description">{{$feeslimit->description??''}}</textarea>
                            @if ($errors->has('description'))
                              <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                              </span>
                            @endif
                          </div>
                        </div>
                        <div class="clearfix"></div>
                      @else
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="card_limit">Card Limit</label>
                          <div class="col-sm-5">
                            <input class="form-control card_limit" name="card_limit" type="text" value="{{$feeslimit->card_limit??'1'}}" id="card_limit"  oninput="restrictNumberToPrefdecimal(this)">
                            @if ($errors->has('card_limit'))
                              <span class="help-block">
                                <strong>{{ $errors->first('card_limit') }}</strong>
                              </span>
                            @endif
                          </div>
                          <div class="col-sm-4">
                            <p>If not set, card limit is 1</p>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="charge_percentage">Charge Percentage</label>
                          <div class="col-sm-5">
                            <input class="form-control charge_percentage" name="charge_percentage" type="text" value="{{ isset($feeslimit->charge_percentage) ?  number_format((float)$feeslimit->charge_percentage, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="charge_percentage" oninput="restrictNumberToPrefdecimal(this)">
                            <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                            @if ($errors->has('charge_percentage'))
                              <span class="help-block">
                                <strong>{{ $errors->first('charge_percentage') }}</strong>
                              </span>
                            @endif
                          </div>
                          <div class="col-sm-4">
                            <p>If not set, charge percentage is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="charge_fixed">Charge Fixed</label>
                          <div class="col-sm-5">
                            <input class="form-control charge_fixed" name="charge_fixed" type="text" value="{{ isset($feeslimit->charge_fixed) ? number_format((float)$feeslimit->charge_fixed, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}" id="charge_fixed" oninput="restrictNumberToPrefdecimal(this)">
                            <small class="form-text text-muted"><strong>{{ allowedDecimalPlaceMessage($preference['decimal_format_amount']) }}</strong></small>
                            @if ($errors->has('charge_fixed'))
                              <span class="help-block">
                                <strong>{{ $errors->first('charge_fixed') }}</strong>
                              </span>
                            @endif
                          </div>
                          <div class="col-sm-4">
                            <p>If not set, charge fixed is {{ number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '') }}</p>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="description">Description</label>
                          <div class="col-sm-5">
                            <textarea class="form-control description" name="description" id="description">{{$feeslimit->description??''}}</textarea>
                            @if ($errors->has('description'))
                              <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                              </span>
                            @endif
                          </div>
                        </div>
                        <div class="clearfix"></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="box-footer">
            <a href="{{ url("admin/settings/currency") }}" class="btn btn-danger btn-flat">Cancel</a>
            <button type="submit" class="btn btn-primary btn-flat pull-right" id="deposit_limit_update">
              <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit_limit_update_text">Update</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      $('.subscriptionSelect .listItem').each(function () {
        if ($(this).data('default') == 1) {
          $(this).addClass('active');
        }
      });

      $('.currencySelect .listItem').each(function () {
        if ($(this).data('default') == 1) {
          $(this).addClass('active');
        }
      });

      $(document).on('click', '.subscriptionSelect .listItem', function (e) {
        e.preventDefault();
        let subscriptionId = $(this).data('rel');
        let title = $(this).text();
        $('.SubscriptionTitle').text(title);
        $(this).addClass('active').siblings().removeClass('active');
        $('#subscription_id').val(subscriptionId);
        let currencyId = $('.currencySelect .listItem.active').data('rel') || 0;
        loadFees(subscriptionId, currencyId, $('#transaction_type').val(), $('#transaction_type_id').val(), $('#tabText').val());
      });

      $(document).on('click', '.currencySelect .listItem', function (e) {
        e.preventDefault();
        let currencyId = $(this).data('rel');
        let name = $(this).text();
        $('.currencyName').text(name);
        $(this).addClass('active').siblings().removeClass('active');
        $('#currency_id').val(currencyId);
        let subscriptionId = $('.subscriptionSelect .listItem.active').data('rel') || 0;
        loadFees(subscriptionId, currencyId, $('#transaction_type').val(), $('#transaction_type_id').val(), $('#tabText').val());
      });

      function loadFees(subscriptionId, currencyId, transactionType, transactionTypeId, tabText) {
        $.ajax({
          url: "{{ route('settings.feesLimitDetails') }}",
          type: "POST",
          data: {
            subscription_id: subscriptionId,
            currency_id: currencyId,
            transaction_type: transactionType,
            transaction_type_id: transactionTypeId,
            tab: tabText,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
              if (response.status === 200) {
                  let f = response.feeslimit;

                  $('#min_balance').val(f.min_balance ?? '');
                  $('#min_limit').val(f.min_limit ?? '');
                  $('#max_limit').val(f.max_limit ?? '');
                  $('#charge_percentage').val(f.charge_percentage ?? '');
                  $('#charge_fixed').val(f.charge_fixed ?? '');
                  $('#second_min_limit').val(f.second_min_limit ?? '');
                  $('#second_max_limit').val(f.second_max_limit ?? '');
                  $('#second_charge_percentage').val(f.second_charge_percentage ?? '');
                  $('#second_charge_fixed').val(f.second_charge_fixed ?? '');
                  $('#recom_amt').val(f.recom_amt ?? '');
                  $('#description').val(f.description ?? '');
                  $('#card_limit').val(f.card_limit ?? '');
              } else {
                  toastr.error(response.message);
              }
          },
          error: function () {
            alert("Failed to load fees data.");
          }
        });
      }

      function setDefaultHiddenInputs() {
        let subscriptionId = $('.subscriptionSelect .listItem.active').data('rel');
        let currencyId = $('.currencySelect .listItem.active').data('rel');
        if (subscriptionId) $('#subscription_id').val(subscriptionId);
        if (currencyId) $('#currency_id').val(currencyId);
      }
      setDefaultHiddenInputs();
    });
  </script>
@endsection