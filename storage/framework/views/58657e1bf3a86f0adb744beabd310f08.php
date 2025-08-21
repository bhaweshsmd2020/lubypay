

<?php $__env->startSection('title', 'Deposit'); ?>

<?php $__env->startSection('page_content'); ?>

<div class="box">
   <div class="panel-body">
        <ul class="nav nav-tabs cus" role="tablist">
            <li class="active">
              <a href='<?php echo e(url("admin/users/edit/$users->id")); ?>'>Profile</a>
            </li>

            <li>
              <a href="<?php echo e(url("admin/users/transactions/$users->id")); ?>">Transactions</a>
            </li>
            <li>
              <a href="<?php echo e(url("admin/users/wallets/$users->id")); ?>">Wallets</a>
            </li>
            <li>
              <a href="<?php echo e(url("admin/users/tickets/$users->id")); ?>">Tickets</a>
            </li>
            <li>
              <a href="<?php echo e(url("admin/users/disputes/$users->id")); ?>">Disputes</a>
            </li>
       </ul>
      <div class="clearfix"></div>
   </div>
</div>

<div class="row">
    <div class="col-md-2">
        &nbsp;&nbsp;&nbsp;<button style="margin-top: 15px;"  type="button" class="btn button-secondary btn-flat active">Deposit</button>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-4">
        <div class="pull-right">
            <h3><?php echo e($users->first_name.' '.$users->last_name); ?></h3>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-7">

                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3 class="text-center"><strong>Details</strong></h3>
                                <div class="row">
                                    <div class="col-md-6 pull-left">Amount</div>
                                    <div class="col-md-6  text-right"><strong><?php echo e(moneyFormat($transInfo['currSymbol'], isset($transInfo['amount']) ? formatNumber($transInfo['amount']) : 0.00)); ?></strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pull-left">Fee</div>
                                    <div class="col-md-6 text-right"><strong><?php echo e(moneyFormat($transInfo['currSymbol'], isset($transInfo['fee']) ? formatNumber($transInfo['fee']) : 0.00)); ?></strong></div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-6 pull-left"><strong>Total</strong></div>
                                    <div class="col-md-6 text-right"><strong><?php echo e(moneyFormat($transInfo['currSymbol'], isset($transInfo['totalAmount']) ? formatNumber($transInfo['totalAmount']) : 0.00)); ?></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div style="margin-left: 0 auto">
                            <div style="float: left;">
                                <a href="#" class="admin-user-deposit-confirm-back-link">
                                    <button class="btn button-secondary admin-user-deposit-confirm-back-btn"><strong><i class="fa fa-angle-left"></i>&nbsp;&nbsp;Back</strong></button>
                                </a>
                            </div>
                            <div style="float: right;">
                                <form action="<?php echo e(url('admin/users/deposit/storeFromAdmin')); ?>" style="display: block;" method="POST" accept-charset="UTF-8" id="admin-user-deposit-confirm" novalidate="novalidate">
                                    <input value="<?php echo e(csrf_token()); ?>" name="_token" id="token" type="hidden">
                                    <input value="<?php echo e($transInfo['totalAmount']); ?>" name="amount" id="amount" type="hidden">
                                    <input value="<?php echo e($users->id); ?>" name="user_id" type="hidden">

                                    <button type="submit" class="btn button-secondary" id="admin-user-deposit-confirm-btn">
                                        <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                                        <span id="admin-user-deposit-confirm-btn-text">
                                            <strong>Confirm&nbsp; <i class="fa fa-angle-right"></i></strong>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">

    $('#admin-user-deposit-confirm').validate({
        rules: {
            amount: {
                required: false,
            },
        },
        submitHandler: function(form)
        {
            $("#admin-user-deposit-confirm-btn").attr("disabled", true);
            $(".fa-spin").show();
            var pretext=$("#admin-user-deposit-confirm-btn-text").text();
            $("#admin-user-deposit-confirm-btn-text").text('Confirming...');

            //Make back button disabled and prevent click
            $('.admin-user-deposit-confirm-back-btn').attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });

            //Make back anchor prevent click
            $('.admin-user-deposit-confirm-back-link').click(function (e)
            {
                e.preventDefault();
            });

            form.submit();
            setTimeout(function(){
                $("#admin-user-deposit-confirm-btn-text").html(pretext + '<i class="fa fa-angle-right"></i>');
                $("#admin-user-deposit-confirm-btn").removeAttr("disabled");
                $(".fa-spin").hide();
            },10000);
        }
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.admin-user-deposit-confirm-back-btn', function (e)
    {
        e.preventDefault();
        window.history.back();
    });

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/users/deposit/confirmation.blade.php ENDPATH**/ ?>