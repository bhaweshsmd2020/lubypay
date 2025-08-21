
<?php $__env->startSection('title', 'Fees & Limits'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- custom-checkbox -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/css/custom-checkbox.css')); ?>">
  <style type="text/css">
    @media only screen and (max-width: 767px) {
        .default_currency_side_text {
            float: right;
            margin-right: 30px;
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
          <div class="col-md-2">
            <div class="dropdown pull-left" style="margin-top: 10px;">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Currency : <span class="currencyName"><?php echo e($currency->name); ?></span>
              <span class="caret"></span></button>
              <ul class="dropdown-menu">
                <?php $__currentLoopData = $currencyList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currencyItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <li class="listItem" data-rel="<?php echo e($currencyItem->id); ?>" data-default="<?php echo e($currencyItem->default); ?>">
                    <a href="#"><?php echo e($currencyItem->name); ?></a>
                  </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
            </div>
          </div>

          <div class="col-md-8"></div>

          
            <div class="col-md-2 defaultCurrencyDiv" style="display: none;">
              <h4 class="form-control-static pull-right"><span class="label label-success">Default Currency</span></h4>
            </div>
          

      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-md-3">
       <?php echo $__env->make('admin.common.currency_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <div class="col-md-9">
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title">

            <?php if($list_menu == 'request_payment'): ?>
              <?php echo e(ucwords(str_replace('_', ' ', $list_menu))); ?> Settings
            <?php elseif($list_menu == 'collect_payment'): ?>
              <?php echo e(ucwords(str_replace('_', ' ', $list_menu))); ?> Settings  
            <?php elseif($list_menu == 'withdrawal'): ?>
              <?php echo e("Payout Settings"); ?>

            <?php else: ?>
              <?php echo e(ucfirst($list_menu)); ?> Settings
            <?php endif; ?>
          </h3>
        </div>

        <form action='<?php echo e(url('admin/settings/feeslimit/update-deposit-limit')); ?>' class="form-horizontal" method="POST" id="deposit_limit_form">
          <?php echo csrf_field(); ?>

          <input type="hidden" value="<?php echo e(isset($feeslimit->id) ? $feeslimit->id : ''); ?>" name="id" id="id">
          <input type="hidden" value="<?php echo e($currency->id); ?>" name="currency_id" id="currency_id">
          <input type="hidden" value="<?php echo e($transaction_type); ?>" name="transaction_type" id="transaction_type">
          <input type="hidden" value="<?php echo e($list_menu); ?>" name="tabText" id="tabText">

          <input type="hidden" value="<?php echo e($currency->default); ?>" name="defaultCurrency" id="defaultCurrency">

          <div class="box-body">

              <!-- has_transaction -->
              <?php if($currency->default == 1): ?>
                 <div class="form-group">
                  
                  <label class="col-sm-3 control-label default_currency_label" for="has_transaction">Is Activated</label>
                  <div class="col-sm-5">
                      <label class="checkbox-container">
                        <input type="checkbox" class="has_transaction" name="has_transaction" value="Yes" <?php echo e(isset($feeslimit->has_transaction) && $feeslimit->has_transaction == 'Yes' ? 'checked' : ''); ?> id="has_transaction" disabled="disabled">
                        <span class="checkmark"></span>

                      <?php if($errors->has('has_transaction')): ?>
                          <span class="help-block">
                              <strong><?php echo e($errors->first('has_transaction')); ?></strong>
                          </span>
                      <?php endif; ?>
                 </div>
                 <div class="col-sm-4">
                      <p><span class="default_currency_side_text">Default currency is always active</span></p>
                  </div>
                </div>
                <div class="clearfix"></div>

              <?php elseif($currency->default == 1 && !isset($feeslimit)): ?>
                 <div class="form-group">
                  <label class="col-sm-3 control-label default_currency_label" for="has_transaction">Is Activated</label>
                  
                  <div class="col-sm-5">
                      <label class="checkbox-container">
                        <input type="checkbox" class="has_transaction" name="has_transaction" value="Yes" <?php echo e($currency->default == 1 ? 'checked' : ''); ?> id="has_transaction">
                        <span class="checkmark"></span>

                      <?php if($errors->has('has_transaction')): ?>
                          <span class="help-block">
                              <strong><?php echo e($errors->first('has_transaction')); ?></strong>
                          </span>
                      <?php endif; ?>
                 </div>
                 <div class="col-sm-4">
                      
                      <p><span class="default_currency_side_text"></span></p>
                  </div>
                </div>
                <div class="clearfix"></div>
              <?php else: ?>

                <div class="form-group">
                  <label class="col-sm-3 control-label default_currency_label" for="has_transaction">Is Activated</label>
                  <div class="col-sm-5">
                      <label class="checkbox-container">
                        <input type="checkbox" class="has_transaction" name="has_transaction"
                        value="Yes" <?php echo e(isset($feeslimit->has_transaction) && $feeslimit->has_transaction == 'Yes' ? 'checked' : ''); ?> id="has_transaction">
                        <span class="checkmark"></span>
                      </label>
                      <?php if($errors->has('has_transaction')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('has_transaction')); ?></strong>
                            </span>
                      <?php endif; ?>
                 </div>
                 <div class="col-sm-4 default_currency_side_text">
                      <p><span class="default_currency_side_text"></span></p>
                  </div>
                </div>
                <div class="clearfix"></div>
              <?php endif; ?>

              <!-- Minimum Limit -->
              <div class="form-group">
                    <label class="col-sm-3 control-label" for="min_limit">Minimum Limit</label>
                    <div class="col-sm-5">
                      <input class="form-control min_limit" name="min_limit" type="text" value="<?php echo e(isset($feeslimit->min_limit) ? number_format((float)$feeslimit->min_limit, $preference['decimal_format_amount'], '.', '') : number_format((float)1.00000000, $preference['decimal_format_amount'], '.', '')); ?>" id="min_limit" oninput="restrictNumberToPrefdecimal(this)">
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

              <!-- Maximum Limit -->
              <div class="form-group">
                    <label class="col-sm-3 control-label" for="max_limit">Maximum Limit</label>
                    <div class="col-sm-5">
                        <input class="form-control max_limit" name="max_limit" type="text"
                        value="<?php echo e(isset($feeslimit->max_limit) ? number_format((float)$feeslimit->max_limit, $preference['decimal_format_amount'], '.', '') : ''); ?>"

                        id="max_limit" oninput="restrictNumberToPrefdecimal(this)">
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

              <!-- Charge Percentage -->
              <div class="form-group">
                    <label class="col-sm-3 control-label" for="charge_percentage">Charge Percentage</label>
                    <div class="col-sm-5">
                        <input class="form-control charge_percentage" name="charge_percentage" type="text"
                        value="<?php echo e(isset($feeslimit->charge_percentage) ?  number_format((float)$feeslimit->charge_percentage, $preference['decimal_format_amount'], '.', '') :
                        number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>"

                        id="charge_percentage" oninput="restrictNumberToPrefdecimal(this)">
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

              <!-- Charge Fixed -->
              <div class="form-group">
                    <label class="col-sm-3 control-label" for="charge_fixed">Charge Fixed</label>
                    <div class="col-sm-5">
                        <input class="form-control charge_fixed" name="charge_fixed" type="text"
                        value="<?php echo e(isset($feeslimit->charge_fixed) ? number_format((float)$feeslimit->charge_fixed, $preference['decimal_format_amount'], '.', '') :
                        number_format((float)0.00000000, $preference['decimal_format_amount'], '.', '')); ?>"

                        id="charge_fixed" oninput="restrictNumberToPrefdecimal(this)">
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
              
              <!-- Charge Fixed -->
              <div class="form-group">
                    <label class="col-sm-3 control-label" for="recom_amt">Recommended Amount</label>
                    <div class="col-sm-5">
                        <input class="form-control recom_amt" name="recom_amt" type="text"
                        value="<?php echo e($feeslimit->recom_amt??''); ?>"
                            id="recom_amt" oninput="restrictNumberToPrefdecimal(this)">
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<?php echo $__env->make('common.restrict_number_to_pref_decimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('common.format_number_to_pref_decimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

    if ($('#defaultCurrency').val() == 1)
    {
      $('.defaultCurrencyDiv').show();
    }
    else
    {
      $('.defaultCurrencyDiv').hide();
    }


    $('#deposit_limit_form').validate({
        rules: {
            min_limit: {
                // required:true,
                number: true,
                // min: 1,
            },
            max_limit:{
              number: true,
            },
            charge_percentage:{
               number: true,
               // min: 0,
            },
            charge_fixed:{
               number: true,
               // min: 0,
            },
            processing_time:{
               number: true,
               // min: 0,
            },
        },
        submitHandler: function(form)
        {
            $("#deposit_limit_update").attr("disabled", true);
            $(".fa-spin").show();
            $("#deposit_limit_update_text").text('Updating...');
            form.submit();
        }
    });

    $("#has_transaction").click(function()
    {
        if($(".has_transaction").prop('checked') == false)
        {
            $('#has_transaction').val('No');
            $("#min_limit").prop("readonly", true);
            $("#max_limit").prop("readonly", true);
            $("#charge_percentage").prop("readonly", true);
            $("#charge_fixed").prop("readonly", true);
            $("#processing_time").prop("readonly", true);
        }else{
            $('#has_transaction').val('Yes');
            $("#min_limit").prop("readonly", false);
            $("#max_limit").prop("readonly", false);
            $("#charge_percentage").prop("readonly", false);
            $("#charge_fixed").prop("readonly", false);
            $("#processing_time").prop("readonly", false);
        }
    });


    //currency dropdown
    $('.listItem').on('click',function()
    {
      // alert('list');
      if($(".has_transaction").prop('checked') == false)
      {
          $('#has_transaction').val('No');
          $("#min_limit").prop("readonly", true);
          $("#max_limit").prop("readonly", true);
          $("#charge_percentage").prop("readonly", true);
          $("#charge_fixed").prop("readonly", true);
          $("#processing_time").prop("readonly", true);
      }else{
          $('#has_transaction').val('Yes');
          $("#min_limit").prop("readonly", false);
          $("#max_limit").prop("readonly", false);
          $("#charge_percentage").prop("readonly", false);
          $("#charge_fixed").prop("readonly", false);
          $("#processing_time").prop("readonly", false);
      }
      var currencyId       = $(this).attr('data-rel');
      var currencyName     = $(this).text();

      var defaultCurrency       = $(this).attr('data-default');
      // alert(defaultCurrency);

      if(defaultCurrency == 1)
      {
        $('.defaultCurrencyDiv').show();
      }
      else
      {
        $('.defaultCurrencyDiv').hide();
      }

      localStorage.setItem('currencyId',currencyId);
      localStorage.setItem('currencyName',currencyName);
      localStorage.setItem('defaultCurrency',defaultCurrency);

      $('.currencyName').text(currencyName);
      $('#currency_id').val(currencyId);
      $('#defaultCurrency').val(defaultCurrency);

      getFeesLimitDetails();
    });



    //on load
    $(window).on('load',function()
    {
        var previousUrl = document.referrer;
        var urlByOwn    = SITE_URL+'/admin/settings/currency';
        if(previousUrl==urlByOwn)
        {
          localStorage.removeItem('currencyId');
          localStorage.removeItem('currencyName');
          localStorage.removeItem('defaultCurrency');
        }
        else
        {
          if((localStorage.getItem('currencyName')) && (localStorage.getItem('currencyId')) && (localStorage.getItem('defaultCurrency')) )
          {
                $('.currencyName').text(localStorage.getItem('currencyName'));
                $('#currency_id').val(localStorage.getItem('currencyId'));
                $('#defaultCurrency').val(localStorage.getItem('defaultCurrency'));
                getFeesLimitDetails();
          }else{
                getSpecificCurrencyDetails();
          }

        }
        if($(".has_transaction").prop('checked') == false)
        {
          $("#min_limit").prop("readonly", true);
          $("#max_limit").prop("readonly", true);
          $("#charge_percentage").prop("readonly", true);
          $("#charge_fixed").prop("readonly", true);
          $("#processing_time").prop("readonly", true);
        }
    });


    //Window on load/click on list item get fees limit details
    function getFeesLimitDetails()
    {
        var currencyId       = $('#currency_id').val();
        var checkDefaultCurrency = $('#defaultCurrency').val();
        var tabText = $('#tabText').val();
        var transaction_type = $('#transaction_type').val();
        var token            = $("input[name=_token]").val();
        var url              = SITE_URL+'/admin/settings/get-feeslimit-details';

        $.ajax({
          url : url,
          type : "post",
          data : {
            'currency_id':currencyId,
            'transaction_type':transaction_type,
            '_token':token
          },
          dataType : 'json',
          success:function(data)
          {
              // console.log(data);
              if(data.status == 200)
              {
                  if (checkDefaultCurrency == 1)
                  {
                    $('.defaultCurrencyDiv').show();

                    $('.default_currency_label').html('Is Activated');
                    $('.default_currency_side_text').text('Default currency is always active');

                    $(".has_transaction").prop('checked', true);
                    $(".has_transaction").prop('disabled', true);
                    $('.has_transaction').val('Yes');

                    $("#min_limit").prop("readonly", false);
                    $("#max_limit").prop("readonly", false);
                    $("#charge_percentage").prop("readonly", false);
                    $("#charge_fixed").prop("readonly", false);

                  }
                  else
                  {
                    $('.defaultCurrencyDiv').hide();

                    $('.default_currency_label').html('Is Activated');
                    $('.default_currency_side_text').text('');
                    $("#has_transaction").prop('checked', false);
                    $('#has_transaction').removeAttr('disabled');
                    $('.has_transaction').val('No');

                    $("#min_limit").prop("readonly", true);
                    $("#max_limit").prop("readonly", true);
                    $("#charge_percentage").prop("readonly", true);
                    $("#charge_fixed").prop("readonly", true);
                  }

                  if(data.feeslimit.has_transaction=='Yes')
                  {
                    $(".has_transaction").prop('checked', true);
                    $('.has_transaction').val(data.feeslimit.has_transaction);

                    $("#min_limit").prop("readonly", false);
                    $("#max_limit").prop("readonly", false);
                    $("#charge_percentage").prop("readonly", false);
                    $("#charge_fixed").prop("readonly", false);
                  }
                  else
                  {
                    $("#has_transaction").prop('checked', false);
                    $('.has_transaction').val('No');

                    $("#min_limit").prop("readonly", true);
                    $("#max_limit").prop("readonly", true);
                    $("#charge_percentage").prop("readonly", true);
                    $("#charge_fixed").prop("readonly", true);
                  }
                  $('#id').val(data.feeslimit.id);
                  $('.min_limit').val(formatNumberToPrefDecimal(data.feeslimit.min_limit));
                  if (data.feeslimit.max_limit != null)
                  {
                      $('.max_limit').val(formatNumberToPrefDecimal(data.feeslimit.max_limit));
                  }
                  else
                  {
                      $('.max_limit').val('');
                  }
                  $('.charge_percentage').val(formatNumberToPrefDecimal(data.feeslimit.charge_percentage));
                  $('.charge_fixed').val(formatNumberToPrefDecimal(data.feeslimit.charge_fixed));
                  $('#processing_time').val(data.feeslimit.processing_time);
              }
              else
              {
                if (checkDefaultCurrency == 1)
                {
                  $('.defaultCurrencyDiv').show();

                  $('.default_currency_label').html('Is Activated');
                  $('.default_currency_side_text').text('Default currency is always active');
                  $(".has_transaction").prop('checked', true);
                  $('.has_transaction').val('Yes');
                }
                else
                {
                  $('.defaultCurrencyDiv').hide();
                  $('.default_currency_label').html('Is Activated');
                  $('.default_currency_side_text').text('');
                  $('#has_transaction').removeAttr('disabled'); //
                  $(".has_transaction").prop('checked', false);
                  $('.has_transaction').val('No');
                }
                $('#id').val('');
                $('.min_limit').val(formatNumberToPrefDecimal('1.00000000'));
                $('.max_limit').val('');
                $('.charge_percentage').val(formatNumberToPrefDecimal('0.00000000'));
                $('.charge_fixed').val(formatNumberToPrefDecimal('0.00000000'));
                $("#min_limit").prop("readonly", true);
                $("#max_limit").prop("readonly", true);
                $("#charge_percentage").prop("readonly", true);
                $("#charge_fixed").prop("readonly", true);
              }
          },
          error: function(error){
              console.log(error);
          }
        });
    }

    // Get Specific Currency Details
    function getSpecificCurrencyDetails()
    {
        var currencyId       = $('#currency_id').val();
        var checkDefaultCurrency = $('#defaultCurrency').val();
        var transaction_type = $('#transaction_type').val();
        var token            = $("input[name=_token]").val();

        var tabText = $('#tabText').val();
        var url              = SITE_URL+'/admin/settings/get-specific-currency-details';
        $.ajax({
          url : url,
          type : "post",
          data : {
            'currency_id':currencyId,
            'transaction_type':transaction_type,
            '_token':token
          },
          dataType : 'json',
          success:function(data)
          {
            if(data.status == 200)
            {
              if (checkDefaultCurrency == 1)
              {
                $('.defaultCurrencyDiv').show();

                $('.default_currency_label').html('Is Activated');
                $('.default_currency_side_text').text('Default currency is always active');
                $(".has_transaction").prop('checked', true);
                $('#has_transaction').attr('disabled', true);
                $('#has_transaction').val('Yes');

                $("#min_limit").prop("readonly", false);
                $("#max_limit").prop("readonly", false);
                $("#charge_percentage").prop("readonly", false);
                $("#charge_fixed").prop("readonly", false);
              }
              else
              {
                $('.defaultCurrencyDiv').hide();

                $('.default_currency_label').html('Is Activated');
                $('.default_currency_side_text').hide();
                $("#has_transaction").prop('checked', false);
                $('#has_transaction').removeAttr('disabled');
                $('.has_transaction').val('No');

                $("#min_limit").prop("readonly", true);
                $("#max_limit").prop("readonly", true);
                $("#charge_percentage").prop("readonly", true);
                $("#charge_fixed").prop("readonly", true);
              }

              if(data.feeslimit.has_transaction=='Yes')
              {
                $(".has_transaction").prop('checked', true);
                $('.has_transaction').val('Yes');
                $("#min_limit").prop("readonly", false);
                $("#max_limit").prop("readonly", false);
                $("#charge_percentage").prop("readonly", false);
                $("#charge_fixed").prop("readonly", false);
              }
              else
              {
                $("#has_transaction").prop('checked', false);
                $('.has_transaction').val('No');
                $("#min_limit").prop("readonly", true);
                $("#max_limit").prop("readonly", true);
                $("#charge_percentage").prop("readonly", true);
                $("#charge_fixed").prop("readonly", true);
              }
              $('#id').val(data.feeslimit.id);
              $('.currencyName').text(data.currency.name);
              $('#currency_id').val(data.currency.id);
              $('.min_limit').val(formatNumberToPrefDecimal(data.feeslimit.min_limit));
              if (data.feeslimit.max_limit != null)
              {
                  $('.max_limit').val(formatNumberToPrefDecimal(data.feeslimit.max_limit));
              }
              else
              {
                  $('.max_limit').val('');
              }
              $('.charge_percentage').val(formatNumberToPrefDecimal(data.feeslimit.charge_percentage));
              $('.charge_fixed').val(formatNumberToPrefDecimal(data.feeslimit.charge_fixed));
              $('#processing_time').val(data.feeslimit.processing_time);
            }
            else
            {
              if (checkDefaultCurrency == 1)
              {
                $('.defaultCurrencyDiv').show();

                $('.default_currency_label').html('Is Activated');
                $('.default_currency_side_text').text('Default currency is always active');
                $('#has_transaction').removeAttr('disabled');
              }
              else
              {
                $('.defaultCurrencyDiv').hide();

                $('.default_currency_label').html('Is Activated');
                $('.default_currency_side_text').text('');
              }
              $('#id').val('');
              $('.currencyName').text(data.currency.name);
              $('#currency_id').val(data.currency.id);
              $(".has_transaction").prop('checked', false);
              $('.has_transaction').val('No');
              $('.min_limit').val(formatNumberToPrefDecimal(formatNumberToPrefDecimal('1.00000000')));
              $('.max_limit').val('');
              $('.charge_percentage').val(formatNumberToPrefDecimal('0.00000000'));
              $('.charge_fixed').val(formatNumberToPrefDecimal('0.00000000'));
            }
          },
          error: function(error){
              console.log(error);
          }
        });
    }
</script>

<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/feeslimits/deposit_limit_single.blade.php ENDPATH**/ ?>