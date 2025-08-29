
<?php $__env->startSection('title', 'Fees & Limits'); ?>

<?php $__env->startSection('head_style'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/css/custom-checkbox.css')); ?>">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
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
              Subscription : <span class="SubscriptionTitle"><?php echo e($subscription->title); ?></span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu subscriptionSelect">
              <?php $__currentLoopData = $subscriptionList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscriptionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="listItem 
                    <?php echo e($subscriptionItem->id == $subscription->id ? 'active' : ''); ?>"
                    data-rel="<?php echo e($subscriptionItem->id); ?>">
                  <a href="#"><?php echo e(ucfirst($subscriptionItem->title)); ?></a>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        </div>

        <div class="col-md-6">          
          <div class="dropdown pull-right" style="margin-top: 10px;">
            <button class="btn btn-default dropdown-toggle currencySelectBtn" type="button" data-toggle="dropdown">
              Currency : <span class="currencyName"><?php echo e($currency->name); ?></span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu currencySelect">
              <?php $__currentLoopData = $currencyList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currencyItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="listItem 
                    <?php echo e($currencyItem->id == $currency->id ? 'active' : ''); ?>"
                    data-rel="<?php echo e($currencyItem->id); ?>">
                  <a href="#"><?php echo e($currencyItem->name); ?></a>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php $__currentLoopData = $transactionTypeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactionTypeItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li <?php echo e(isset($list_menu) &&  $list_menu == $transactionTypeItem->slug ? 'class=active' : ''); ?>>
                <a data-spinner="true" href="<?php echo e(url('admin/settings/feeslimit/'.$transactionTypeItem->slug.'/'.$subscription->id.'/'.$currency->id)); ?>">
                  <?php echo e($transactionTypeItem->name); ?>

                </a>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title"><?php echo e($transaction_name); ?> Settings</h3>
        </div>

        <form action='<?php echo e(url('admin/settings/feeslimit/update-deposit-limit')); ?>' class="form-horizontal" method="POST" id="deposit_limit_form">
          <?php echo csrf_field(); ?>


          <input type="hidden" value="<?php echo e($currency->id); ?>" name="currency_id" id="currency_id">
          <input type="hidden" value="<?php echo e($transaction_type); ?>" name="transaction_type_id" id="transaction_type_id">
          <input type="hidden" value="<?php echo e($trans_type); ?>" name="transaction_type" id="transaction_type">
          <input type="hidden" value="<?php echo e($list_menu); ?>" name="tabText" id="tabText">
          <input type="hidden" value="<?php echo e($currency->default); ?>" name="defaultCurrency" id="defaultCurrency">
          <input type="hidden" value="<?php echo e($subscription->id); ?>" name="subscription_id" id="subscription_id">

          <div class="box-body">
            <div class="col-md-11 col-md-offset-1">
              <div class="panel-group" id="accordion">
                <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <input type="hidden" name="payment_method_id[]" value="<?php echo e($method->id); ?>">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo e($method->id); ?>"> <?php echo e($method->name); ?> </a>
                      </h4>
                    </div>
                    <div id="collapse<?php echo e($method->id); ?>" class="panel-collapse collapse">
                      <div class="panel-body fee-form" data-method_id="<?php echo e($method->id); ?>">
                        <div class="form-group">
                          <label class="col-sm-3 control-label default_currency_label" for="has_transaction_<?php echo e($method->id); ?>">Is Activated</label>
                          <div class="col-sm-5">
                            <label class="checkbox-container">
                              <input 
                                type="checkbox" 
                                class="has_transaction" 
                                data-method_id="<?php echo e($method->id); ?>" 
                                name="has_transaction[<?php echo e($method->id); ?>]" 
                                value="Yes" 
                                id="has_transaction_<?php echo e($method->id); ?>"
                                <?php if($currency->default == 1): ?>
                                    checked disabled
                                <?php elseif(isset($method->fees_limit->has_transaction) && $method->fees_limit->has_transaction == "Yes"): ?>
                                    checked
                                <?php endif; ?>
                              >
                              <span class="checkmark"></span>
                            </label>

                            <?php if($errors->has("has_transaction.{$method->id}")): ?>
                              <span class="help-block">
                                <strong><?php echo e($errors->first("has_transaction.{$method->id}")); ?></strong>
                              </span>
                            <?php endif; ?>
                          </div>

                          <div class="col-sm-4">
                            <?php if($currency->default == 1): ?>
                              <p><span class="default_currency_side_text">Default currency is always active</span></p>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="clearfix"></div>

                        <?php if($trans_type == 1): ?>
                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="min_balance">Minimum Balance</label>
                            <div class="col-sm-5">
                              <input class="form-control min_balance" name="min_balance[]" type="text" value="<?php echo e(isset($method->fees_limit->min_balance) ? number_format((float)$method->fees_limit->min_balance, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="min_balance_<?php echo e($method->id); ?>">
                              <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                              <?php if($errors->has('min_balance')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('min_balance')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, minimum balance is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <h4><strong>Limit 1</strong></h4>
                          <div class="charge-range border p-3">
                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="min_limit">Minimum Limit</label>
                              <div class="col-sm-5">
                                <input class="form-control min_limit" name="min_limit[]" type="text" value="<?php echo e(isset($method->fees_limit->min_limit) ? number_format((float)$method->fees_limit->min_limit, $preference['decimal_format_amount'], '.', '') : number_format((float)1.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="min_limit_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('min_limit')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('min_limit')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, minimum limit is <?php echo e(number_format((float)1.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="max_limit">Maximum Limit</label>
                              <div class="col-sm-5">
                                <input class="form-control max_limit" name="max_limit[]" type="text" value="<?php echo e(isset($method->fees_limit->max_limit) ? number_format((float)$method->fees_limit->max_limit, $preference['decimal_format_amount'], '.', '') : ''); ?>" id="max_limit_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('max_limit')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('max_limit')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, maximum limit is infinity</p>
                              </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="charge_percentage">Charge Percentage</label>
                              <div class="col-sm-5">
                                <input class="form-control charge_percentage" name="charge_percentage[]" type="text" value="<?php echo e(isset($method->fees_limit->charge_percentage) ? number_format((float)$method->fees_limit->charge_percentage, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="charge_percentage_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('charge_percentage')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('charge_percentage')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, charge percentage is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="charge_fixed">Charge Fixed</label>
                              <div class="col-sm-5">
                                <input class="form-control charge_fixed" name="charge_fixed[]" type="text" value="<?php echo e(isset($method->fees_limit->charge_fixed) ? number_format((float)$method->fees_limit->charge_fixed, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="charge_fixed_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('charge_fixed')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('charge_fixed')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, charge fixed is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                          </div>

                          <h4><strong>Limit 2</strong></h4>
                          <div class="charge-range border p-3">
                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="second_min_limit">Minimum Limit</label>
                              <div class="col-sm-5">
                                <input class="form-control second_min_limit" name="second_min_limit[]" type="text" value="<?php echo e(isset($method->fees_limit->second_min_limit) ? number_format((float)$method->fees_limit->second_min_limit, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="second_min_limit_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('second_min_limit')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('second_min_limit')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, minimum limit is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="second_max_limit">Maximum Limit</label>
                              <div class="col-sm-5">
                                <input class="form-control second_max_limit" name="second_max_limit[]" type="text" value="<?php echo e(isset($method->fees_limit->second_max_limit) ? number_format((float)$method->fees_limit->second_max_limit, $preference['decimal_format_amount'], '.', '') : ''); ?>" id="second_max_limit_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('second_max_limit')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('second_max_limit')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, maximum limit is infinity</p>
                              </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="second_charge_percentage">Charge Percentage</label>
                              <div class="col-sm-5">
                                <input class="form-control second_charge_percentage" name="second_charge_percentage[]" type="text" value="<?php echo e(isset($method->fees_limit->second_charge_percentage) ? number_format((float)$method->fees_limit->second_charge_percentage, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="second_charge_percentage_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('second_charge_percentage')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('second_charge_percentage')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, charge percentage is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label" for="second_charge_fixed">Charge Fixed</label>
                              <div class="col-sm-5">
                                <input class="form-control second_charge_fixed" name="second_charge_fixed[]" type="text" value="<?php echo e(isset($method->fees_limit->second_charge_fixed) ? number_format((float)$method->fees_limit->second_charge_fixed, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="second_charge_fixed_<?php echo e($method->id); ?>">
                                <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                                <?php if($errors->has('second_charge_fixed')): ?>
                                  <span class="help-block">
                                    <strong><?php echo e($errors->first('second_charge_fixed')); ?></strong>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="col-sm-4">
                                <p>If not set, charge fixed is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                          </div>
                            
                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="recom_amt">Recommended Amount</label>
                            <div class="col-sm-5">
                              <input class="form-control recom_amt" name="recom_amt[]" type="text" value="<?php echo e($method->fees_limit->recom_amt??'0'); ?>" id="recom_amt_<?php echo e($method->id); ?>"  oninput="restrictNumberToPrefdecimal(this)">
                              <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                              <?php if($errors->has('recom_amt')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('recom_amt')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                            <div class="col-sm-4">
                              <p>Add Recommended Amount , Comma Seprated!</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">Description</label>
                            <div class="col-sm-5">
                              <textarea class="form-control description" name="description[]" id="description_<?php echo e($method->id); ?>"><?php echo e($method->fees_limit->description??''); ?></textarea>
                              <?php if($errors->has('description')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('description')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        <?php else: ?>                         
                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="card_limit">Card Limit</label>
                            <div class="col-sm-5">
                              <input class="form-control card_limit" name="card_limit[]" type="text" value="<?php echo e($method->fees_limit->card_limit??'1'); ?>" id="card_limit_<?php echo e($method->id); ?>"  oninput="restrictNumberToPrefdecimal(this)">
                              <?php if($errors->has('card_limit')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('card_limit')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, card limit is 1</p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="charge_percentage">Charge Percentage</label>
                            <div class="col-sm-5">
                              <input class="form-control charge_percentage" name="charge_percentage[]" type="text" value="<?php echo e(isset($method->fees_limit->charge_percentage) ? number_format((float)$method->fees_limit->charge_percentage, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="charge_percentage_<?php echo e($method->id); ?>">
                              <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                              <?php if($errors->has('charge_percentage')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('charge_percentage')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, charge percentage is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="charge_fixed">Charge Fixed</label>
                            <div class="col-sm-5">
                              <input class="form-control charge_fixed" name="charge_fixed[]" type="text" value="<?php echo e(isset($method->fees_limit->charge_fixed) ? number_format((float)$method->fees_limit->charge_fixed, $preference['decimal_format_amount'], '.', '') : number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="charge_fixed_<?php echo e($method->id); ?>">
                              <small class="form-text text-muted"><strong><?php echo e(allowedDecimalPlaceMessage($preference['decimal_format_amount'])); ?></strong></small>
                              <?php if($errors->has('charge_fixed')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('charge_fixed')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                            <div class="col-sm-4">
                              <p>If not set, charge fixed is <?php echo e(number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?></p>
                            </div>
                          </div>
                          <div class="clearfix"></div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">Description</label>
                            <div class="col-sm-5">
                              <textarea class="form-control description" name="description[]" id="description_<?php echo e($method->id); ?>"><?php echo e($method->fees_limit->description??''); ?></textarea>
                              <?php if($errors->has('description')): ?>
                                <span class="help-block">
                                  <strong><?php echo e($errors->first('description')); ?></strong>
                                </span>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </div>
          </div>

          <div class="box-footer">
            <a href="<?php echo e(url("admin/settings/currency")); ?>" class="btn btn-danger btn-flat">Cancel</a>
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
          url: "<?php echo e(route('settings.feesLimitDetails')); ?>",
          type: "POST",
          data: {
            subscription_id: subscriptionId,
            currency_id: currencyId,
            transaction_type: transactionType,
            transaction_type_id: transactionTypeId,
            tab: tabText,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            if (response.status === 200) {
              $('.fee-form').each(function () {
                let methodId = $(this).data('method_id');
                let fee = response.feeslimit[methodId];

                if (fee) {
                  $(this).find('.min_balance').val(fee.min_balance);
                  $(this).find('.min_limit').val(fee.min_limit);
                  $(this).find('.max_limit').val(fee.max_limit);
                  $(this).find('.charge_percentage').val(fee.charge_percentage);
                  $(this).find('.charge_fixed').val(fee.charge_fixed);
                  $(this).find('.second_min_limit').val(fee.second_min_limit);
                  $(this).find('.second_max_limit').val(fee.second_max_limit);
                  $(this).find('.second_charge_percentage').val(fee.second_charge_percentage);
                  $(this).find('.second_charge_fixed').val(fee.second_charge_fixed);
                  $(this).find('.recom_amt').val(fee.recom_amt);
                  $(this).find('.description').val(fee.description);
                  $(this).find('.card_limit').val(fee.card_limit);
                } else {
                  $(this).find('input, textarea, select').val('');
                }
              });
            } else {
              $('.fee-form').each(function () {
                $(this).find('input, textarea, select').val('');
              });
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/feeslimits/deposit_limit.blade.php ENDPATH**/ ?>