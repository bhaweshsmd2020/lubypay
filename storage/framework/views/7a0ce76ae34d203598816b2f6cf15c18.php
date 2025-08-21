

<?php $__env->startSection('css'); ?>
    <!--daterangepicker-->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/user_dashboard/css/daterangepicker.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-12 mb20 marginTopPlus">
                    <div class="flash-container">
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="float-left trans-inline"><?php echo app('translator')->get('message.dashboard.nav-menu.transactions'); ?></h4>
                        </div>

                        <div style="margin: 15px 15px 15px 10px;">

                            <form action="" method="get">
                                <input id="startfrom" type="hidden" name="from" value="<?php echo e(isset($from) ? $from : ''); ?>">
                                <input id="endto" type="hidden" name="to" value="<?php echo e(isset($to) ? $to : ''); ?>">
                                <div class="">
                                    <div class="filter_panel">
                                        <div class="daterange_btn" id="daterange-btn" style="width: 100%;">
                                            <span id="drp" style="text-align: left; "><i class="fa fa-calendar"></i> <?php echo app('translator')->get('message.dashboard.transaction.date-range'); ?></span>
                                        </div>
                                        <!-- calculate total sales and due -->
                                        <?php if(Auth::user()->type == 'merchant'): ?>
                                        <?php
                                            $totalSales = $totalDue = 0;
                                            if($sale_transactions->count()>0){
                                                foreach($sale_transactions as $key=>$transaction){
                                                    $totalSales += $transaction->subtotal;
                                                    $totalDue += $transaction->subtotal;
                                                }
                                                if($withdrawable_transactions != null && $withdrawable_transactions->count() > 0){
                                                    $totalDue = 0;
                                                    foreach($withdrawable_transactions as $transaction){
                                                        $totalDue += $transaction->subtotal;
                                                    }
                                                }
                                            }
                                                
                                        ?>
                                        <p style="margin-top: 15px; margin-bottom: 15px; margin-right: 10px;"><?php echo app('translator')->get('message.dashboard.transaction.total-sales'); ?>: 
                                            <strong><?php echo e($wallets[0]->currency->code); ?> <?php echo e(formatNumber($totalSales)); ?></strong>
                                        </p>
                                        <p style="margin-top: 15px; margin-bottom: 15px; margin-right: 10px;"><?php echo app('translator')->get('message.dashboard.transaction.total-due'); ?>: 
                                            <strong><?php echo e($wallets[0]->currency->code); ?> <?php echo e(formatNumber($totalDue)); ?></strong>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="filter_panel">
                                        <select class="form-control" id="type" name="type">

                                            <option value="all" <?= ($type == 'all') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.all-trans-type'); ?>
                                            </option>

                                            <option value="<?php echo e(Deposit); ?>" <?= ($type == Deposit) ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.button.deposit'); ?>
                                            </option>

                                            <option value="<?php echo e(Withdrawal); ?>" <?= ($type == Withdrawal) ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.button.withdraw'); ?>
                                            </option>

                                            <option value="sent" <?= ($type == 'sent') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.payment-sent'); ?>
                                            </option>

                                            <option value="request" <?= ($type == 'request') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.payment-req'); ?>
                                            </option>

                                            <option value="received" <?= ($type == 'received') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.payment-receive'); ?>
                                            </option>

                                            <option value="exchange" <?= ($type == 'exchange') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.exchanges'); ?>
                                            </option>

                                            <!--<option value="crypto_sent" <?= ($type == 'crypto_sent') ? 'selected' : '' ?>>-->
                                                <!-- TODO: translation -->
                                            <!--    Crypto Sent-->
                                            <!--</option>-->

                                            <!--<option value="crypto_received" <?= ($type == 'crypto_received') ? 'selected' : '' ?>>-->
                                                <!-- TODO: translation -->
                                            <!--    Crypto Received-->
                                            <!--</option>-->

                                        </select>
                                    </div>
                                    <div class="filter_panel">
                                        <select class="form-control" id="status" name="status">
                                            <option value="all" <?= ($status == 'all') ? 'selected' : '' ?>><?php echo app('translator')->get('message.dashboard.transaction.all-status'); ?>
                                            </option>
                                            <option value="Success" <?= ($status == 'Success') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.success'); ?>
                                            </option>
                                            <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.pending'); ?>
                                            </option>
                                            <option value="Refund" <?= ($status == 'Refund') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.refund'); ?>
                                            </option>
                                            <option value="Blocked" <?= ($status == 'Blocked') ? 'selected' : '' ?>>
                                                <?php echo app('translator')->get('message.dashboard.transaction.blocked'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="filter_panel">
                                        <select class="form-control" id="wallet" name="wallet">
                                            <option value="all" <?= ($wallet == 'all') ? 'selected' : '' ?>><?php echo app('translator')->get('message.dashboard.transaction.all-currency'); ?>
                                            </option>
                                            <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($res->currency->id); ?>" <?= ($res->currency_id == $wallet) ? 'selected' : '' ?>><?php echo e($res->currency->code); ?> </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="">
                                        <button type="submit" class="btn btn-cust" style="    padding: 5px 12px;"><?php echo app('translator')->get('message.dashboard.button.filter'); ?></button>
                                    </div>

                                </div>
                            </form>
                            
                            
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table recent_activity" align="left">
                                    <thead>
                                    <tr>
                                        <td></td>
                                        <td class="text-left" width="15%">
                                            <strong><?php echo app('translator')->get('message.dashboard.left-table.date'); ?></strong></td>
                                        
                                        <td class="text-left">
                                            <strong><?php echo app('translator')->get('message.dashboard.left-table.description'); ?></strong></td>
                                            <!--<td class="text-left">-->
                                            <!--    <strong>Card #</strong></td>-->
                                        <td class="text-left">
                                            <strong><?php echo app('translator')->get('message.dashboard.left-table.status'); ?></strong></td>
                                        <td class="text-left">
                                            <strong><?php echo app('translator')->get('message.dashboard.left-table.currency'); ?></strong></td>
                                        <td class="text-left">
                                            <strong>Phone Number</strong></td>
                                        <td class="text-left"><strong><?php echo app('translator')->get('message.dashboard.left-table.fee'); ?></strong>
                                        </td>
                                        <td class="text-left">
                                            <strong><?php echo app('translator')->get('message.dashboard.left-table.amount'); ?></strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if($transactions->count()>0): ?>
                                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <tr click="0" data-toggle="collapse" data-target="#collapseRow<?php echo e($key); ?>"
                                                aria-expanded="false" aria-controls="collapseRow<?php echo e($key); ?>"
                                                class="show_area" trans-id="<?php echo e($transaction->id); ?>" id="<?php echo e($key); ?>">

                                                <!-- Arrow -->
                                                <td class="text-center arrow-size">
                                                    <strong>
                                                        <i class="fa fa-arrow-circle-right text-blue"
                                                           id="icon_<?php echo e($key); ?>"></i>
                                                    </strong>
                                                </td>

                                                <!-- Created At -->
                                                <td class="text-left date_td" width="10%"><?php echo e(dateFormat($transaction->created_at)); ?></td>

                                                <!-- Transaction Type -->
                                                <?php if(empty($transaction->merchant_id)): ?>

                                                    <?php if($transaction->end_user_id): ?>
                                                        <td class="text-left">
                                                            <?php if($transaction->transaction_type_id): ?>
                                                                <?php if($transaction->transaction_type_id==Request_From): ?>

                                                                    <p><?php echo e($transaction->end_user->first_name??''); ?></p>
                                                                    <p><?php echo app('translator')->get('Request Sent'); ?></p>

                                                                <?php elseif($transaction->transaction_type_id==Request_To): ?>

                                                                    <p><?php echo e($transaction->end_user->first_name??''); ?></p>
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
                                                                    <p><?php echo e(__(str_replace('_',' ',$transaction->transaction_type->name))); ?></p>
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

                                                                <?php if( $transaction->transaction_type->name == 'Transferred' || $transaction->transaction_type->name == 'Request_From' && $transaction->user_type = 'unregistered'): ?>
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
                                                <td class="text-left"><p>
                                                    <p id="status_<?php echo e($transaction->id); ?>">
                                                        <?php echo e((
                                                                ($transaction->status == 'Blocked') ? __("Cancelled") :
                                                                (
                                                                    ($transaction->status == 'Refund') ? __("Refunded") : __($transaction->status)
                                                                )
                                                            )); ?>

                                                    </p>
                                                </td>

                                                <!-- Currency Code -->
                                                <td class="text-left"><p><?php echo e($transaction->currency->code); ?> </p></td>
                                                
                                                <!-- Phone -->
                                                <td class="text-left"><p><?php echo e($transaction->phone); ?> </p></td>

                                                <!-- Fee -->
                                                <td class="text-left">
                                                    <?php if($transaction->currency->type != 'fiat'): ?>
                                                        <p><?php echo e(($transaction->charge_fixed == 0) ? '-' : $transaction->charge_fixed); ?></p>
                                                    <?php else: ?>
                                                        <p><?php echo e(($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0) ? '-' : formatNumber(abs($transaction->total)-abs($transaction->subtotal))); ?></p>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Amount -->
                                                <?php if($transaction->transaction_type_id == Deposit): ?>
                                                    <?php if($transaction->subtotal > 0): ?>
                                                        <td class="text-left text-success"><p>+<?php echo e(formatNumber($transaction->subtotal)); ?></p></td>
                                                    <?php endif; ?>
                                                <?php elseif($transaction->transaction_type_id == Payment_Received): ?>
                                                    <?php if($transaction->subtotal > 0): ?>
                                                        <?php if($transaction->status == 'Refund'): ?>
                                                            <td class="text-left text-danger"><p>-<?php echo e(formatNumber($transaction->subtotal)); ?></p></td>
                                                        <?php else: ?>
                                                            <td class="text-left text-success"><p>+<?php echo e(formatNumber($transaction->subtotal)); ?></p></td>
                                                        <?php endif; ?>
                                                    <?php elseif($transaction->subtotal == 0): ?>
                                                        <td class="text-left">
                                                            <p><?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                        </td>
                                                    <?php elseif($transaction->subtotal < 0): ?>
                                                        <td class="text-left text-danger">
                                                            <p><?php echo e(formatNumber($transaction->subtotal)); ?></p>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if($transaction->total > 0): ?>
                                                        <td class="text-left text-success"><p><?php echo e($transaction->currency->type != 'fiat' ? "+".$transaction->total : "+".formatNumber($transaction->total)); ?></p></td>

                                                    <?php elseif($transaction->total == 0): ?>
                                                        <td class="text-left"><p><?php echo e(formatNumber($transaction->total)); ?></p></td>
                                                    <?php elseif($transaction->total < 0): ?>
                                                        <td class="text-left text-danger">
                                                            <p><?php echo e($transaction->currency->type != 'fiat' ? $transaction->total : formatNumber($transaction->total)); ?></p>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </tr>
                                            <tr id="collapseRow<?php echo e($key); ?>" class="collapse">
                                                <td colspan="8" class="">
                                                    <div class="row activity-details" id="loader_<?php echo e($transaction->id); ?>" style="min-height: 200px">

                                                        <div class="col-md-4 text-left" id="html_<?php echo e($key); ?>">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="right">
                                                                <?php if( $transaction->transaction_type_id == Payment_Sent && $transaction->status == 'Success' && !isset($transaction->dispute->id)): ?>
                                                                    <a id="dispute_<?php echo e($transaction->id); ?>" href="<?php echo e(url('/dispute/add/').'/'.$transaction->id); ?>" class="btn btn-secondary btn-sm"><?php echo app('translator')->get('message.dashboard.transaction.open-dispute'); ?></a>
                                                                <?php endif; ?>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <!--<td colspan="8"> <?php echo app('translator')->get('message.dashboard.left-table.no-transaction'); ?></td>-->
                                            <td colspan="8"> <img src="<?php echo e(url('public/images/nodata.png')); ?>" style="    margin: 35px 0px;"></td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <?php echo e($transactions->appends($_GET)->links('vendor.pagination.bootstrap-5')); ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

    <!--daterangepicker-->
    <script src="<?php echo e(asset('public/user_dashboard/js/daterangepicker.js')); ?>" type="text/javascript"></script>

    <?php echo $__env->make('user_dashboard.layouts.common.check-user-status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        $(window).on('load', function()
        {
            var sDate;
            var eDate;
            //Date range as a button
            $('#daterange-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    sDate = moment(start, 'MMMM D, YYYY').format('DD-MM-YYYY');
                    $('#startfrom').val(sDate);
                    eDate = moment(end, 'MMMM D, YYYY').format('DD-MM-YYYY');
                    $('#endto').val(eDate);
                    $('#daterange-btn span').html(sDate + ' - ' + eDate);
                }
            )

            var startDate = "<?php echo $from; ?>";
            var endDate = "<?php echo $to; ?>";
            if (startDate == '') {
                $('#daterange-btn span').html('<i class="fa fa-calendar"></i> <?php echo e(__('message.dashboard.transaction.date-range')); ?>');
            } else {
                $('#daterange-btn span').html(startDate + ' - ' + endDate);
            }
        });
    </script>

    <?php echo $__env->make('common.user-transactions-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/transactions/index.blade.php ENDPATH**/ ?>