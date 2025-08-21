

<?php $__env->startSection('css'); ?>
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style type="text/css">
        @media only screen and (min-width: 768px) {
            /*.wallet-currency-div {
                padding: 18px 12px 5px 14px !important;
            }*/
        }
        
        .current {
    width: 100%;
    float: left;
}
        .card{   background-color:transparent!important; border: 0px solid rgba(0,0,0,.125)!important;}
        .card-header {background-color: rgb(236, 240, 245)!important;
    border-top: 0px solid #2bbed8!important;border-bottom: 0px solid rgba(0,0,0,.125)!important;
    padding: 15px 0px!important;
}
.box{    margin-bottom: 16px!important;}
.card-footer {
    padding: .75rem 15px;
    background-color: 0px solid rgba(0,0,0,.125)!important;
    background-color: rgba(255, 255, 255, 0.03)!important;
    border-top: 0px solid rgba(0,0,0,.125);
}
.table-responsive{border-radius: 15px 15px 0px 0px;}
    float: left !important;
    padding: 2px;
}

.right {
    padding: 2px;
}
.center {
    margin: 0px;
    padding: 2px;
}
.dash-btn .btn-cust {
   border-radius: 5px;
    font-size: 14px!important;
    height: 36px;
}
.dash-btn img {
    border: 0;
    border-radius: 0;
    width: 20px!important;
    height: 18px!important;
    background-size: contain;
    margin-left: 0%;
    vertical-align: middle;
}



    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            
            <?php
                $persona_kyc = DB::table('kycdatastores')->where('user_id', auth()->user()->id)->where('status', 'COMPLETED')->first();
                $manual_kyc = DB::table('users')->where('id', auth()->user()->id)->where('photo_verified', '1')->where('address_verified', '1')->where('identity_verified', '1')->first();
            ?>
            
            <?php if((empty($persona_kyc) && !empty($manual_kyc)) || (!empty($persona_kyc) && empty($manual_kyc)) || (!empty($persona_kyc) && !empty($manual_kyc))): ?>
            <?php else: ?>
                <div class="alert alert-danger">
                    <strong>Alert!</strong> Please complete your KYC to use all services.  <a href="<?php echo e(url('/kyc')); ?>">click here..</a>.
                </div>
            <?php endif; ?>

            <!-- for express api merchant payment success/error message-->
            <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="row">
                
                
                <div class="col-md-4 col-xs-12 col-sm-12 mb20 marginTopPlus">
                    <div class="flash-container">
                    </div>
                    
                    <div class="card" style="background: transparent;">
                        
                        
                        
                                
                    <div class="main_overview">
                        <div><span class="viewcard">
                            
                             <h4 class="float-left trans-inline"><?php echo app('translator')->get('message.dashboard.right-table.title'); ?> Balance</h4>
                            
                        </span></div>
            <div class="box box-lg new_box-lg">
                
                <div class="current">
                   <!--LOGO & Currency Code-->
                                                <div class="float-left" style="width: 100%;padding-bottom:15px">
                                                  
                                                  
                                                  
                                                   <div class="wap-wed" style="width: 100%;">
                            <?php if($wallets->count()>0): ?>
                                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $walletCurrencyCode = encrypt(strtolower($wallet->currency->code));
                                        $walletId = encrypt($wallet->id);
                                    ?>
                                    <div class="set-Box clearfix" style="border-bottom: 1px solid #eaeaea">
                                        <div class="row">
                                            <div class="col-md-12 wallet-currency-div" style="padding: 18px 17px 5px 17px">
                                                
                                                  <!--LOGO-->
                                                    <?php if(empty($wallet->currency->logo)): ?>
                                                        <img src="<?php echo e(asset('public/user_dashboard/images/favicon.png')); ?>" class="img-responsive" style="float: none; ">
                                                    <?php else: ?>
                                                        <img src='<?php echo e(asset("public/uploads/currency_logos/".$wallet->currency->logo)); ?>' class="img-responsive" style="float: none; ">
                                                    <?php endif; ?>

                                                    <!--Currency Code-->
                                                    <?php if($wallet->currency->type == 'fiat' && $wallet->is_default == 'Yes'): ?>
                                                        <span><?php echo e($wallet->currency->code); ?>&nbsp;<span class="badge badge-secondary"><?php echo app('translator')->get('message.dashboard.right-table.default-wallet-label'); ?></span></span>
                                                    <?php else: ?>
                                                        <span><?php echo e($wallet->currency->code); ?></span>
                                                    <?php endif; ?>
                                                
                                                <!--BALANCE-->
                                                <span class="float-right" style="position: relative;top: 7px;    font-size: 24px;
    font-weight: bold;">
                                                    <?php if($wallet->balance > 0): ?>
                                                        <?php if($wallet->currency->type != 'fiat'): ?>
                                                            <span class="text-success"><?php echo e('+'.$wallet->balance); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-success"><?php echo e('+'.formatNumber($wallet->balance)); ?></span>
                                                        <?php endif; ?>
                                                    <?php elseif($wallet->balance == 0): ?>
                                                        <?php if($wallet->currency->type != 'fiat'): ?>
                                                            <span><?php echo e($wallet->balance); ?></span>
                                                        <?php else: ?>
                                                            <span><?php echo e('+'.formatNumber($wallet->balance)); ?></span>
                                                        <?php endif; ?>
                                                    <?php elseif($wallet->balance < 0): ?>
                                                        <?php if($wallet->currency->type != 'fiat'): ?>
                                                            <span class="text-danger"><?php echo e($wallet->balance); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-danger"><?php echo e('+'.formatNumber($wallet->balance)); ?></span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>

                                            <!--Crypto Send & Receiv Buttons-->
                                            <?php if($wallet->currency->type != 'fiat' && $wallet->currency->status == 'Active'): ?>
                                                <div class="col-md-12" style="padding: 10px 44px 14px 44px;">
                                                    <div class="text-center">
                                                        <a href="<?php echo e(url("/crpto/send/".$walletCurrencyCode."/".$walletId)); ?>" class="btn btn-cust-crypto float-left"><?php echo app('translator')->get('message.dashboard.right-table.crypto-send'); ?></a>
                                                        <a href="<?php echo e(url("/crpto/receive/".$walletCurrencyCode."/".$walletId)); ?>" class="btn btn-cust-crypto float-right"><?php echo app('translator')->get('message.dashboard.right-table.crypto-receive'); ?></a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <?php echo app('translator')->get('message.dashboard.right-table.no-wallet'); ?>
                            <?php endif; ?>

                            <div class="clearfix"></div>
                        </div>
                                                  
                                                  
                                                  
                                                  
                                                </div>
                    <p class="boxt1">Available Balance</p>
                </div>
               <!-- <div class="available">
                    <h3 style="margin-bottom: 8px;">
                                        <span class="usd">USD</span>
                        10.78   
                                           </h3>
                    <p class="boxt2">Avaialble Balance</p>
                </div>-->
            </div>
            
              <div class="card-footer">
                          <!--  <div class="dash-btn row pb6">
                                <div class="left col-md-8 pb6">
                                    <small class="form-text text-muted"><strong>*Fiat Currencies Only</strong></small>
                                </div>
                            </div>
-->
                            <div class="dash-btn row">
                                <?php if((empty($persona_kyc) && !empty($manual_kyc)) || (!empty($persona_kyc) && empty($manual_kyc))): ?>
                                    <?php if(Common::has_permission(auth()->id(),'manage_deposit')): ?>
                                        <div class="left col-md-4 pb6">
                                            <a href="<?php echo e(url('deposit')); ?>" class="btn btn-cust col-md-12">
                                                <!--<img src="<?php echo e(asset('public/user_dashboard/images/deposit.png')); ?>"
                                                     class="img-responsive" style="margin-top:3px;">-->
                                                &nbsp;<?php echo app('translator')->get('message.dashboard.button.deposit'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(Common::has_permission(auth()->id(),'manage_withdrawal')): ?>
                                        <div class="right col-md-4">
                                            <a href="<?php echo e(url('payouts')); ?>" class="btn btn-cust col-md-12 ">
                                                <!--<img src="<?php echo e(asset('public/user_dashboard/images/withdrawal.png')); ?>" class="img-responsive">-->
                                                &nbsp;<?php echo app('translator')->get('message.dashboard.button.payout'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                 <?php if(Common::has_permission(auth()->id(),'manage_exchange')): ?>
                                    <!--<div class="center col-md-4">-->
                                    <!--    <a href="<?php echo e(url('exchange')); ?>" class="btn btn-cust col-md-12">-->
                                    <!--        <img src="<?php echo e(asset('public/user_dashboard/images/exchange.png')); ?>" class="img-responsive" style="margin-top:3px;">-->
                                    <!--        <?php echo app('translator')->get('message.dashboard.button.exchange'); ?>-->
                                    <!--    </a>-->
                                    <!--</div>-->
                                <?php endif; ?>
                                
                                
                                
                            </div>
                           

                        </div>
            
            
            
            
        </div>
        
        <div class="debit"><div><span class="viewcard">View Card</span></div>
            
              <a href="#"><img src="http://caribpay.emoneywallets.com/images/debit.png" style="    width: 100%;   max-width: 100%;"></a>
        </div>
                 
                        
                        
                        
                        
                       <!-- 
                        <div class="card-header">
                            <h4 class="float-left trans-inline"><?php echo app('translator')->get('message.dashboard.right-table.title'); ?></h4>
                            <div class="chart-list trans-inline float-right ">
                            </div>
                        </div>
                       
                      -->
                    </div>
                </div>
                
                
                <div class="col-md-8 col-xs-12 col-sm-12 mb20 marginTopPlus">
                    <div class="flash-container">
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="float-left trans-inline"><?php echo app('translator')->get('message.dashboard.left-table.title'); ?></h4>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table recent_activity">
                                    <thead>
                                        <tr>
                                            <td></td>
                                            <td width="25%" class="text-left">
                                                <strong><?php echo app('translator')->get('message.dashboard.left-table.date'); ?></strong></td>
                                            <td class="text-left">
                                                <strong><?php echo app('translator')->get('message.dashboard.left-table.description'); ?></strong></td>
                                                <!--<td class="text-left">-->
                                                <!--<strong>Card #</strong></td>-->
                                            <td class="text-left">
                                                <strong><?php echo app('translator')->get('message.dashboard.left-table.status'); ?></strong></td>
                                            <td class="text-left">
                                                <strong><?php echo app('translator')->get('message.dashboard.left-table.amount'); ?></strong></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($transactions->count()>0): ?>
                                            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr click="0" data-toggle="collapse" data-target="#collapseRow<?php echo e($key); ?>" aria-expanded="false" aria-controls="collapseRow<?php echo e($key); ?>"
                                                    class="show_area" trans-id="<?php echo e($transaction->id); ?>" id="<?php echo e($key); ?>">

                                                    <!-- Arrow -->
                                                    <td class="text-center arrow-size">
                                                        <strong>
                                                            <i class="fa fa-arrow-circle-right text-blue"
                                                               id="icon_<?php echo e($key); ?>"></i>
                                                        </strong>
                                                    </td>

                                                    <!-- Created At -->
                                                    <td class="text-left date_td" width="17%"><?php echo e(dateFormat($transaction->created_at)); ?></td>

                                                    <!-- Transaction Type -->
                                                    <?php if(empty($transaction->merchant_id)): ?>

                                                        <?php if(!empty($transaction->end_user_id)): ?>
                                                            <td class="text-left">
                                                                <?php if($transaction->transaction_type_id): ?>
                                                                    <?php if($transaction->transaction_type_id==Request_From): ?>
                                                                        <p>
                                                                            
                                                                        </p>
                                                                        <p><?php echo app('translator')->get('Request Sent'); ?></p>
                                                                    <?php elseif($transaction->transaction_type_id==Request_To): ?>
                                                                        <p>
                                                                            
                                                                        </p>
                                                                        <p><?php echo app('translator')->get('Request Received'); ?></p>

                                                                    <?php elseif($transaction->transaction_type_id == Transferred): ?>
                                                                        <p>
                                                                            <?php echo e($transaction->end_user->first_name??''); ?>

                                                                        </p>
                                                                        <p><?php echo app('translator')->get('Transferred'); ?></p>

                                                                    <?php elseif($transaction->transaction_type_id == Received): ?>
                                                                        <p>
                                                                            <?php echo e($transaction->end_user->first_name??''); ?>

                                                                        </p>
                                                                        <p><?php echo app('translator')->get('Received'); ?></p>
                                                                    <?php else: ?>
                                                                        <p><?php echo e(__(str_replace('_',' ',$transaction->transaction_type->name??''))); ?></p>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        <?php else: ?>

                                                           <?php
                                                                if (isset($transaction->payment_method->name))
                                                                {
                                                                    if ($transaction->payment_method->name == 'Mts')
                                                                    {
                                                                        $payment_method = getCompanyName();
                                                                    }
                                                                    elseif ($transaction->payment_method->name == 'Stripe'){
                                                                        $payment_method = 'Debit/Credit Card';
                                                                    }
                                                                    else
                                                                    {
                                                                        $payment_method = $transaction->payment_method->name;
                                                                    }
                                                                }
                                                            ?>
                                                            <td class="text-left">
                                                                <p>
                                                                    <?php if($transaction->transaction_type->name == 'Deposit'): ?>
                                                                        <?php if($transaction->payment_method->name == 'Bank'): ?>
                                                                            <?php echo e($payment_method); ?> (<?php echo e($transaction->bank->bank_name); ?>)
                                                                        <?php else: ?>
                                                                            <?php if(!empty($payment_method)): ?>
                                                                                <?php echo e($payment_method); ?>

                                                                            <?php endif; ?>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>

                                                                    <?php if($transaction->transaction_type->name == 'Withdrawal'): ?>
                                                                        <?php if(!empty($payment_method)): ?>
                                                                            <?php echo e($payment_method); ?>

                                                                        <?php endif; ?>
                                                                    <?php endif; ?>

                                                                    <?php if($transaction->transaction_type->name == 'Transferred' || $transaction->transaction_type->name == 'Request_From' && $transaction->user_type = 'unregistered'): ?>
                                                                        <?php echo e(($transaction->email) ? $transaction->email : $transaction->phone); ?> <!--for send money by phone - mobile app-->
                                                                    <?php endif; ?>
                                                                </p>

                                                                <?php if($transaction->transaction_type_id): ?>
                                                                    <?php if($transaction->transaction_type_id==Request_From): ?>
                                                                        <p><?php echo app('translator')->get('Request Sent'); ?></p>
                                                                    <?php elseif($transaction->transaction_type_id==Request_To): ?>
                                                                        <p><?php echo app('translator')->get('Request Received'); ?></p>

                                                                    <?php elseif($transaction->transaction_type_id == Withdrawal): ?>
                                                                        <p><?php echo app('translator')->get('Payout'); ?></p>
                                                                    <?php else: ?>
                                                                        <p><?php echo e(__(str_replace('_',' ',$transaction->transaction_type->name))); ?></p>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <td class="text-left">
                                                            <p><?php echo e($transaction->merchant->business_name); ?></p>
                                                            <?php if($transaction->transaction_type_id): ?>
                                                                <p><?php echo e(__(str_replace('_',' ',$transaction->transaction_type->name))); ?></p>
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php endif; ?>

<!--<td class="text-left">-->
<!--    <p>-->
<!--        4321993181002031-->
<!--    </p>-->
<!--</td>-->

                                                    <!-- Status -->
                                                    <td class="text-left">
                                                        <p id="status_<?php echo e($transaction->id); ?>">
                                                            <?php echo e((
                                                                    ($transaction->status == 'Blocked') ? __("Cancelled") :
                                                                    (
                                                                        ($transaction->status == 'Refund') ? __("Refunded") : __($transaction->status)
                                                                    )
                                                                )); ?>

                                                        </p>
                                                    </td>

                                                    <!-- Amount -->
                                                    <?php if($transaction->transaction_type_id == Deposit): ?>
                                                        <?php if($transaction->subtotal > 0): ?>
                                                            <td>
                                                                <p class="text-left text-success">+<?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                                <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php elseif($transaction->transaction_type_id == Payment_Received): ?>
                                                        <?php if($transaction->subtotal > 0): ?>
                                                            <?php if($transaction->status == 'Refund'): ?>
                                                                <td>
                                                                    <p class="text-left text-danger">-<?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                                    <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                                </td>
                                                            <?php else: ?>
                                                                <td>
                                                                    <p class="text-left text-success">+<?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                                    <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                                </td>
                                                            <?php endif; ?>
                                                        <?php elseif($transaction->subtotal == 0): ?>
                                                            <td class="text-left">
                                                                <p><?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                                <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                            </td>
                                                        <?php elseif($transaction->subtotal < 0): ?>
                                                            <td>
                                                                <p class="text-left text-danger"><?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                                <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php if($transaction->total > 0): ?>
                                                            <td>
                                                                <p class="text-left text-success"><?php echo e($transaction->currency->type != 'fiat' ? "+".$transaction->total : "+".formatNumber($transaction->total)); ?></p>
                                                                <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                            </td>
                                                        <?php elseif($transaction->total == 0): ?>
                                                            <td class="text-left">
                                                                <p><?php echo e(formatNumber($transaction->total)); ?></p>
                                                                <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                            </td>
                                                        <?php elseif($transaction->total < 0): ?>
                                                            <td>
                                                                <p class="text-left text-danger"><?php echo e($transaction->currency->type != 'fiat' ? $transaction->total : formatNumber($transaction->total)); ?></p>
                                                                <p class="text-left"><?php echo e($transaction->currency->code); ?></p>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </tr>

                                                <tr id="collapseRow<?php echo e($key); ?>" class="collapse">
                                                    <td colspan="8" class="">
                                                        <div class="row activity-details" id="loader_<?php echo e($transaction->id); ?>"
                                                             style="min-height: 200px">
                                                            <div class="col-md-7 col-sm-12 text-left" id="html_<?php echo e($key); ?>"></div>
                                                            <div class="col-md-3 col-sm-12">
                                                                <div class="right">
                                                                    <?php if( $transaction->transaction_type_id == Payment_Sent && $transaction->status == 'Success' && !isset($transaction->dispute->id)): ?>
                                                                        <a id="dispute_<?php echo e($transaction->id); ?>" href="<?php echo e(url('/dispute/add/').'/'.$transaction->id); ?>" class="btn btn-secondary btn-sm"><?php echo app('translator')->get('message.dashboard.transaction.open-dispute'); ?></a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-12">
                                                            </div>

                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <tr>
                                                <!--<td colspan="6"> <?php echo app('translator')->get('message.dashboard.left-table.no-transaction'); ?></td>-->
                                                 <td colspan="6"> <img src="<?php echo e(url('public/images/nodata.png')); ?>" style="    margin: 36px 0px;"></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-center ash-color"><a class="font-weight-bold" href="<?php echo e(url('transactions')); ?>"><?php echo app('translator')->get('message.dashboard.left-table.view-all'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<!-- sweetalert -->
<script src="<?php echo e(asset('public/user_dashboard/js/sweetalert/sweetalert-unpkg.min.js')); ?>" type="text/javascript"></script>

<?php if(Auth::user()->role_id == '3' && (Auth::user()->account_verified == '0' || Auth::user()->account_verified == null || Auth::user()->account_verified == '')): ?>
<script>
    function toBeExecutedOnFirstLoad(){
        swal({
            title: 'Account not verified!',
            text: 'Verify your account to continue access',
            type: 'warning',
            closeOnClickOutside: false,
            closeOnEsc: false,
        }).then(function() {
            window.location.href = "<?php echo e(url('profile/upgrade?package='.auth()->user()->packageid)); ?>";
        });
    }
    if(localStorage.getItem('first') === null){
        toBeExecutedOnFirstLoad();
        localStorage.setItem('first', 'nope!');
    }
</script>
<?php endif; ?>


<?php echo $__env->make('user_dashboard.layouts.common.check-user-status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('common.user-transactions-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/user_dashboard/layouts/dashboard.blade.php ENDPATH**/ ?>