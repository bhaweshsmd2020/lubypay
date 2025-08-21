<?php

    $breadcrumb = [
        [
            'icon' => 'fa fa-home',
            'href' => url('admin/home'),
            'name' => 'Dashboard'
        ]
    ];

?>



<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('page_content'); ?>

<style>
    .small-box .icon{
        font-size: 50px;
    }
    
    .small-box:hover .icon{
        font-size: 70px;
    }
</style>

<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box box-info">
                    <div class="box-header with-border">
                      <div id="row">
                        <div class="col-md-12">
                          <div class="text-center">
                           <strong>Users</strong>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="box box-body">
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_user')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/users')); ?>" style="color: #fff;">
                                    <div class="small-box bg-yellow">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-users"></i>
                                            </div>
                                            <p class="cardTitle">Total Users</p>
                                            <h3><?php echo e($totalUser); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-person-add"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/users')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_user')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/users')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green" style="background-color: #e91e63 !important;">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <p class="cardTitle">Today’s Registered</p>
                                            <h3><?php echo e($todayregistered); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/users')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_transaction')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/transactions')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green" style="background-color: #795548 !important;">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-money"></i>
                                            </div>
                                            <p class="cardTitle">Today’s Transactions</p>
                                            <h3><?php echo e($todaytransactions); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/transactions')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_deposit')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/deposits')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green" >
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-money"></i>
                                            </div>
                                            <p class="cardTitle">Today’s Deposit</p>
                                            <h3><?php echo e($todaydeposit); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/deposits')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_withdrawal')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/withdrawals')); ?>" style="color: #fff;">
                                    <div class="small-box bg-red">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-money"></i>
                                            </div>
                                            <p class="cardTitle">Today’s Payout</p>
                                            <h3><?php echo e($todaypayout); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/withdrawals')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_tickets')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/tickets/list')); ?>" style="color: #fff;">
                                    <div class="small-box bg-aqua">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-envelope-o"></i>
                                            </div>
                                            <p class="cardTitle">Total Tickets</p>
                                            <h3><?php echo e($totalTicket); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/tickets/list')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_disputes')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/disputes')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green" style="background-color: #ab185a !important;">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-ticket"></i>
                                            </div>
                                            <p class="cardTitle">Total Dispute</p>
                                            <h3><?php echo e($totalDispute); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/disputes')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box box-info">
                    <div class="box-header with-border">
                      <div id="row">
                        <div class="col-md-12">
                          <div class="text-center">
                           <strong>Merchants</strong>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="box box-body">
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/merchant-list')); ?>" style="color: #fff;">
                                    <div class="small-box bg-red">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-suitcase"></i>
                                            </div>
                                            <p class="cardTitle">Total Merchants</p>
                                            <h3><?php echo e($totalMerchant); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-person-add"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/merchant-list')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_stores')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/store-list')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-ticket"></i>
                                            </div>
                                            <p class="cardTitle">Total Stores</p>
                                            <h3><?php echo e($totalStore); ?></h3>
                                        </div>
                                        <div class="icon">
                                          <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/store-list')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/merchant-list')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green" style="background-color: #e91e63 !important;">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <p class="cardTitle">Today’s Registered</p>
                                            <h3><?php echo e($mpos_todayregistered); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/merchant-list')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_transactions')): ?>
                            <div class="col-md-3">
                                <a href="<?php echo e(url('admin/mpos')); ?>" style="color: #fff;">
                                    <div class="small-box bg-green" style="background-color: #795548 !important;">
                                        <div class="inner" style="padding: 10px;">
                                            <div class="icon">
                                                <i class="fa fa-money"></i>
                                            </div>
                                            <p class="cardTitle">Today’s Transactions</p>
                                            <h3><?php echo e($mpos_todaytransactions); ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>
                                        <a href="<?php echo e(url('admin/mpos')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box box-info">
                    <div class="box-header with-border">
                      <div id="row">
                        <div class="col-md-12">
                          <div class="text-center">
                           <strong>Last 30 days</strong>
                          </div>
                        </div>
                      </div>
        
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    
                    <div class="box-body">
                      <div class="chart">
                        <canvas id="lineChart" style="height: 246px; width: 1069px;" height="246" width="1069"></canvas>
                      </div>
                    </div>
                    
                    <div class="box-footer with-border">
                      <div id="row">
                        <div class="col-md-3">
                          <div class="row">
                            <div class="col-md-1">
                              <div id="deposit">
                              </div>
                            </div>
                            <div class="col-md-8 scp">
                              Deposit
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="row">
                            <div class="col-md-1">
                              <div id="withdrawal">
                              </div>
                            </div>
                            <div class="col-md-8 scp">
                              Payout
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="row">
                            <div class="col-md-1">
                            <div id="transfer">
                            </div>
                            </div>
                            <div class="col-md-8 scp">
                              Transfer
                            </div>
                          </div>
                        </div>
        
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
     <div class="col-md-8">
        <div class="box box-info">
          <div class="box box-body">

           <!-- Custom Tabs (Pulled to the right) -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">This Week</a></li>
              <li><a href="#tab_2" data-toggle="tab">Last Week</a></li>
              <li><a href="#tab_3" data-toggle="tab">This Month</a></li>
              <li><a href="#tab_4" data-toggle="tab">Last Month</a></li>
            </ul>

            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                  <div class="box-header with-border">
                    <h3 class="box-title"><span style="margin-left: 14px;">Total Profit</span><span style="margin-left: 72px;"><?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_week_revenue))); ?></span></h3>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <div>
                        <span class="progress-label col-md-3"><strong>Deposit Profit</strong></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-deposit" role="progressbar" aria-valuenow="<?php echo e($this_week_deposit_percentage); ?>" aria-valuemin="0" aria-valuemax="100"
                          style='width:<?php  echo $this_week_deposit_percentage ?>%'>
                            <span class="">

                              <?php if($this_week_deposit_percentage >= 12.5): ?>
                                <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_week_deposit))); ?>

                              <?php else: ?>
                                <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($this_week_deposit))); ?>

                              <?php endif; ?>

                              </span>
                           </div>
                        </div>
                    </div>
                    <div>
                        <span class="progress-label col-md-3"><strong>Payout Profit</strong></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-withdrawal" role="progressbar" aria-valuenow="<?php echo e($this_week_withdrawal_percentage); ?>" aria-valuemin="0" aria-valuemax="100"
                          style='width:<?php  echo $this_week_withdrawal_percentage ?>%'>
                            <span class="">
                              <?php if($this_week_withdrawal_percentage >= 12.5): ?>
                                <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_week_withdrawal))); ?>

                              <?php else: ?>
                                <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($this_week_withdrawal))); ?>

                              <?php endif; ?>
                            </span>
                           </div>
                        </div>
                    </div>
                    <div>
                        <span class="progress-label col-md-3"><strong>Transfer Profit</strong></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-transfer" role="progressbar" aria-valuenow="<?php echo e($this_week_transfer_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $this_week_transfer_percentage ?>%'>
                            <span class="">
                              <?php if($this_week_transfer_percentage >= 12.5): ?>
                                <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_week_transfer))); ?>

                              <?php else: ?>
                                <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($this_week_transfer))); ?>

                              <?php endif; ?>
                            </span>
                           </div>
                        </div>
                    </div>


                  </div>
                  <!-- /.box-body -->
              <!-- /.box -->
              </div>


              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                  <div class="box-header with-border">
                    <h3 class="box-title"><span style="margin-left: 14px;">Total Profit</span><span style="margin-left: 72px;"><?php echo e(moneyFormat($defaultCurrency->symbol, formatNumber($last_week_revenue))); ?></span></h3>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <div>
                      <span class="progress-label col-md-3"><strong>Deposit Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-deposit" role="progressbar" aria-valuenow="<?php echo e($last_week_deposit_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $last_week_deposit_percentage ?>%'>

                          <span class="">
                            <?php if($last_week_deposit_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($last_week_deposit))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($last_week_deposit))); ?>

                            <?php endif; ?>
                          </span>

                         </div>
                      </div>
                  </div>
                  <div>
                      <span class="progress-label col-md-3"><strong>Payout Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-withdrawal" role="progressbar" aria-valuenow="<?php echo e($last_week_withdrawal_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $last_week_withdrawal_percentage ?>%'>
                          <span class="">
                            <?php if($last_week_withdrawal_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($last_week_withdrawal))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($last_week_withdrawal))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                  <div>
                      <span class="progress-label col-md-3"><strong>Transfer Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-transfer" role="progressbar" aria-valuenow="<?php echo e($last_week_transfer_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $last_week_transfer_percentage ?>%'>
                          <span class="">
                            <?php if($last_week_transfer_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($last_week_transfer))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($last_week_transfer))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                  </div>
                  <!-- /.box-body -->
          <!-- /.box -->
        </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_3">
                <div class="box-header with-border">
                  <h3 class="box-title"><span style="margin-left: 14px;">Total Profit</span><span style="margin-left: 72px;"><?php echo e(moneyFormat($defaultCurrency->symbol, formatNumber($this_month_revenue))); ?></span></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div>
                      <span class="progress-label col-md-3"><strong>Deposit Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-deposit" role="progressbar" aria-valuenow="<?php echo e($this_month_deposit_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $this_month_deposit_percentage ?>%'>
                          <span class="">
                            <?php if($this_month_deposit_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_month_deposit))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($this_month_deposit))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                  <div>
                      <span class="progress-label col-md-3"><strong>Payout Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-withdrawal" role="progressbar" aria-valuenow="<?php echo e($this_month_withdrawal_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $this_month_withdrawal_percentage ?>%'>
                          <span class="">
                            <?php if($this_month_withdrawal_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_month_withdrawal))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($this_month_withdrawal))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                  <div>
                      <span class="progress-label col-md-3"><strong>Transfer Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-transfer" role="progressbar" aria-valuenow="<?php echo e($this_month_transfer_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $this_month_transfer_percentage ?>%'>

                          <span class="">
                            <?php if($this_month_transfer_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($this_month_transfer))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($this_month_transfer))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                </div>
                <!-- /.box-body -->
          <!-- /.box -->
        </div>
          <!-- /.tab-pane -->

           <div class="tab-pane" id="tab_4">
                <div class="box-header with-border">
                  <h3 class="box-title"><span style="margin-left: 14px;">Total Profit</span><span style="margin-left: 72px;"><?php echo e(moneyFormat($defaultCurrency->symbol, formatNumber($last_month_revenue))); ?></span></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div>
                      <span class="progress-label col-md-3"><strong>Deposit Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-deposit" role="progressbar" aria-valuenow="<?php echo e($last_month_deposit_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $last_month_deposit_percentage ?>%'>
                          <span class="">
                            <?php if($last_month_deposit_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($last_month_deposit))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($last_month_deposit))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                  <div>
                      <span class="progress-label col-md-3"><strong>Payout Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-withdrawal" role="progressbar" aria-valuenow="<?php echo e($last_month_withdrawal_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $last_month_withdrawal_percentage ?>%'>
                          <span class="">
                            <?php if($last_month_withdrawal_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($last_month_withdrawal))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($last_month_withdrawal))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                  <div>
                      <span class="progress-label col-md-3"><strong>Transfer Profit</strong></span>
                      <div class="progress">
                        <div class="progress-bar progress-bar-transfer" role="progressbar" aria-valuenow="<?php echo e($last_month_transfer_percentage); ?>" aria-valuemin="0" aria-valuemax="100" style='width:<?php  echo $last_month_transfer_percentage ?>%'>
                          <span class="">
                            <?php if($last_month_transfer_percentage >= 12.5): ?>
                              <?php echo e(moneyFormat($defaultCurrency->symbol,formatNumber($last_month_transfer))); ?>

                            <?php else: ?>
                              <?php echo e(moneyFormatForDashboardProgressBars($defaultCurrency->symbol,formatNumber($last_month_transfer))); ?>

                            <?php endif; ?>
                          </span>
                         </div>
                      </div>
                  </div>
                </div>
                <!-- /.box-body -->
          <!-- /.box -->
        </div>
      </div>
      <!-- /.tab-content -->
    </div>
    <!-- nav-tabs-custom -->
  </div>
 </div>

 

   <div class="box box-info">
        <div class="box-header text-center">
          <h4 class="text-info text-justify"><b>Latest Ticket</b></h4>
        </div>
            <div class="box box-body">
              <?php if(!empty($latestTicket)): ?>
              <div class="table-responsive">
              <table class="table table-bordered">
                  <thead class="text-left">
                    <tr>
                    <th>Subject</th>
                    <th>User</th>
                    <th>Priority</th>
                    <th>Created Date</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $__currentLoopData = $latestTicket; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr class="text-left">
                    <td style="width: 35%;"><a href='<?php echo e(url("admin/tickets/reply/$item->id")); ?>'><?php echo e($item->subject); ?></a></td>
                    <td style="width: 20%;"><a href='<?php echo e(url("admin/users/edit/$item->user_id")); ?>'><?php echo e($item->first_name.' '.$item->last_name); ?></a></td>
                    <td style="width: 10%;"><?php echo e($item->priority); ?></td>
                    <td style="width: 20%;"><?php echo e(dateFormat($item->created_at)); ?></td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
              </table>
            </div>
              <?php else: ?>
              <h4 class="text-center">No Latest Ticket</h4>
              <?php endif; ?>
            </div>
        </div>

        <div class="box box-info">
        <div class="box-header text-center">
          <h4 class="text-info text-justify"><b>Latest Dispute</b></h4>
        </div>
            <div class="box box-body">
              <?php if(!empty($latestDispute)): ?>
              <div class="table-responsive">
              <table class="table table-bordered">
                  <thead class="text-left">
                    <tr>
                      <th>Dispute</th>
                      <th>Claimant</th>
                      <th>Created Date</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $__currentLoopData = $latestDispute; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-left">
                      <td style="width: 40%;"><a href='<?php echo e(url("admin/dispute/discussion/$item->id")); ?>'><?php echo e($item->title); ?></a></td>
                      <td style="width: 30%;"><a href='<?php echo e(url("admin/users/edit/$item->claimant_id")); ?>'><?php echo e($item->first_name.' '.$item->last_name); ?></a></td>
                      <td style="width: 30%;"><?php echo e(dateFormat($item->created_at)); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
              </table>
            </div>
              <?php else: ?>
              <h4 class="text-center">No Latest Dispute</h4>
              <?php endif; ?>
            </div>
        </div>
 </div>


    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header">
                <div style="font-weight:bold; font-size:20px;" class="text-info">
                    Wallet Balance
                </div>
            </div>
            <div class="box box-body">
                <?php if(!empty($wallets)): ?>
                    <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code=>$wallet_amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($wallet_amount != 0.00000000): ?>
                            <div class="row" style="background: #000!important; border-radius: 5px; padding: 7px 0px; color: #fff; font-size: 23px; margin: 7px 0px;">
                                <div class="col-md-4">
                                    <?php echo e($code); ?>

                                </div>
                                <div class="col-md-8 text-right">
                                    <?php echo e($wallet_amount); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <h5 class="text-center">No Wallet Balance</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<script src="<?php echo e(asset('public/backend/chart.js/Chart.min.js')); ?>" type="text/javascript"></script>

<script>

  $(function () {
   'use strict';
      var areaChartData = {
        labels: jQuery.parseJSON('<?php echo $date; ?>'),
        datasets: [
          {
            label: "Deposit" + " " + "(<?php echo $defaultCurrency->symbol; ?>)",
            // fillColor: "rgba(66,155,206, 1)",
            // strokeColor: "rgba(66,155,206, 1)",
            // pointColor: "rgba(66,155,206, 1)",

            fillColor: "#78BEE6",
            strokeColor: "#78BEE6",
            pointColor: "#78BEE6",

            pointStrokeColor: "#429BCE",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(66,155,206, 1)",
            data: <?php echo $depositArray; ?>

          },
          {
            label: "Payout" + " " + "(<?php echo $defaultCurrency->symbol; ?>)",

            // fillColor: "rgba(255,105,84,1)",
            // strokeColor: "rgba(255,105,84,1)",
            // pointColor: "#F56954",

            fillColor: "#FBB246",
            strokeColor: "#FBB246",
            pointColor: "#FBB246",

            pointStrokeColor: "rgba(255,105,84,1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(255,105,84,1)",
            data: <?php echo $withdrawalArray; ?>

          },
          {
            label: "Transfer" + " " + "(<?php echo $defaultCurrency->symbol; ?>)",

            // fillColor: "rgba(47, 182, 40,0.9)",
            // strokeColor: "rgba(47, 182, 40,0.8)",
            // pointColor: "#2FB628",

            fillColor: "#67FB4A",
            strokeColor: "#67FB4A",
            pointColor: "#67FB4A",

            pointStrokeColor: "rgba(47, 182, 40,1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(47, 182, 40,1)",
            data : <?php echo $transferArray; ?>

          }
        ]
      };

      var areaChartOptions = {
        //Boolean - If we should show the scale at all
        showScale: true,
        //Boolean - Whether grid lines are shown across the chart
        scaleShowGridLines: false,
        //String - Colour of the grid lines
        scaleGridLineColor: "rgba(0,0,0,.05)",
        //Number - Width of the grid lines
        scaleGridLineWidth: 1,
        //Boolean - Whether to show horizontal lines (except X axis)
        scaleShowHorizontalLines: true,
        //Boolean - Whether to show vertical lines (except Y axis)
        scaleShowVerticalLines: true,
        //Boolean - Whether the line is curved between points
        bezierCurve: true,
        //Number - Tension of the bezier curve between points
        bezierCurveTension: 0.3,
        //Boolean - Whether to show a dot for each point
        pointDot: false,
        //Number - Radius of each point dot in pixels
        pointDotRadius: 4,
        //Number - Pixel width of point dot stroke
        pointDotStrokeWidth: 1,
        //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
        pointHitDetectionRadius: 20,
        //Boolean - Whether to show a stroke for datasets
        datasetStroke: true,
        //Number - Pixel width of dataset stroke
        datasetStrokeWidth: 2,
        //Boolean - Whether to fill the dataset with a color
        datasetFill: true,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
        //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: true,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true
      };
      //-------------
      //- LINE CHART -
      //--------------
      var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
      var lineChart = new Chart(lineChartCanvas);
      var lineChartOptions = areaChartOptions;
      lineChartOptions.datasetFill = false;
      lineChart.Line(areaChartData, lineChartOptions);
    });
  </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', $breadcrumb, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>