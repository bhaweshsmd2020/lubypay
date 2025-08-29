

<?php $__env->startSection('css'); ?>
<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<style>
    .commission-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 20px;
        /* text-align: center; */
        background: #fff;
        position: relative;
        height: 250px;
    }
    .commission-card h5 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .commission-value {
        font-size: 22px;
        font-weight: bold;
        margin-top: 10px;
    }
    /* Circular Progress */
    .progress-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: conic-gradient(var(--clr) calc(var(--percent) * 1%), #eee 0);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 10px auto;
        font-size: 14px;
        font-weight: bold;
        color: var(--clr);
    }
    .table tr th {
     background: none !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section-06 history padding-30">
    <div class="container">

        <!-- Top Commission Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="commission-card">
                    <i class="fa fa-trophy" style="font-size: 30px;color:#573aef; background: #f2f0fe; padding: 14px; border-radius: 40px;"></i>
                    <h5>Champion</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                        
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card">
                    <i class="bi bi-award" style="font-size: 30px;color:#e7b731; background: #fef8ef; padding: 11px 13px; border-radius: 40px;"></i>
                    <h5>Master</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                        
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card">
                    <i class="bi bi-award" style="font-size: 30px;color: #44b07e; background: #eff9f6; padding: 11px 13px; border-radius: 40px;"></i>
                    <h5>Elite</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                        
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card">
                    <i class="fa fa-file-text" style="font-size: 30px;color: #f7763b; background: #fbf3ec; padding: 14px; border-radius: 40px;"></i>
                    <h5>Expert</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                        
                    </span>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <td>Commission Level</th>
                        <td>Potential Earning / Months</th>
                        <td>Percent</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-2">
                                    <div style="background: #baeada; color: #306855; margin-inline: 10px; padding: 9px; border-radius: 22px;"><b>5</b></div>
                                </div>
                                <div class="col-3 text-start">
                                    <p class="mb-0">Shrimp</p>
                                    <p class="mb-0">3-12</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-success">(74,75€ - 999€)</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>6 - Small Fish <br><small>14 to 30</small></td>
                        <td><span class="text-danger">(1.000 - 2.499€)</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>7 - Dolphin <br><small>31 to 111</small></td>
                        <td><span class="text-warning">(54,65€ - 799€)</span></td>
                        <td><div class="progress-circle" style="--percent:30; --clr:#ffc107;">30%</div></td>
                    </tr>
                    <tr>
                        <td>8 - Shark <br><small>2 to 13</small></td>
                        <td><span class="text-primary">(54,65€ - 799€)</span></td>
                        <td><div class="progress-circle" style="--percent:35; --clr:#0d6efd;">35%</div></td>
                    </tr>
                    <tr>
                        <td>9 - Shark <br><small>2 to 133333sdfghjkdfghjsdfgh</small></td>
                        <td><span class="text-primary">(54,65€ - 799€)</span></td>
                        <td><div class="progress-circle" style="--percent:35; --clr:#0d6efd;">35%</div></td>
                    </tr>
                    <tr>
                        <td>10 - Blue Whale <br><small>2 to 13</small></td>
                        <td><span class="text-success">(74,75€ - 999€)</span></td>
                        <td><div class="progress-circle" style="--percent:40; --clr:#20c997;">40%</div></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('public/user_dashboard/js/sweetalert/sweetalert-unpkg.min.js')); ?>"></script>
<?php echo $__env->make('user_dashboard.layouts.common.check-user-status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.user-transactions-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/layouts/comissions.blade.php ENDPATH**/ ?>