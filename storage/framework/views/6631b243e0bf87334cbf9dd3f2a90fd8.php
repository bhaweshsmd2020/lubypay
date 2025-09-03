

<?php $__env->startSection('css'); ?>
<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<style>
    .balance-card {
        background: linear-gradient(135deg, #1976d2, #42a5f5);
        color: #fff;
        border-radius: 15px;
        padding: 20px;
    }
    .balance-card h2 {
        font-size: 2.2rem;
        font-weight: bold;
    }
    .latest-activities img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }
    .table th {
        font-weight: 600;
    }

    /* ✅ Force table header bg to #fafafa */
    .custom-table thead tr th {
        background-color: #fafafa !important;
        color: #000 !important;
    }

    .status-pending {
        color: #eccd66 !important;
        font-weight: 600;
    }
    .status-completed {
        color: #34b96b  !important;
        font-weight: 600;
    }
    .withdraw {
        color: #d32f2f;
        font-weight: 600;
    }
    .deposit {
        color: #2e7d32;
        font-weight: 600;
    }
    .custom-table th,
    .custom-table td {
        text-align: left !important;
    }
    .custom-table th,
    .custom-table td {
        border-left: none !important;
        border-right: none !important;
    }
     .modal-header {
      border-bottom: none;
    }
    .transaction-header {
      font-size: 1.2rem;
      font-weight: 600;
    }
    .transaction-amount {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .transaction-status {
      color: orange;
      font-weight: 500;
    }
    .account-details .row {
      margin-bottom: 8px;
    }
    .support-box {
      background-color: #f5f3ff;
      border-radius: 10px;
      padding: 15px;
      margin-top: 20px;
    }
    .support-box .btn {
      background: #6c63ff;
      color: #fff;
      border-radius: 8px;
    }
    .print-btn {
      background: #1a73e8;
      color: #fff;
      width: 100%;
      border-radius: 8px;
      font-weight: 500;
      margin-top: 15px;
    }
    .modal-content{
        border-radius: 10px !important;
    }
    .btn-primary {
        background-color: #0d6efd !important; /* Bootstrap blue */
        border-color: #0d6efd !important;
    }
    .btn-primary:hover {
        background-color: #0b5ed7 !important;
        border-color: #0a58ca !important;
    }
    .card-header {
        border-top: none;
    }
    .tran{
        background-color: #f8f8f8;
    }





    .mini-progress {
  position: relative;
  width: 50px;   /* icon size */
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mini-progress svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg); /* start from top */
}

.mini-progress circle {
  fill: none;
  stroke-width: 6;
  r: 20;      /* radius */
  cx: 25;     /* center X */
  cy: 25;     /* center Y */
}

.mini-progress circle.bg {
  stroke: #eee;  /* background circle */
}

.mini-progress circle.progress {
  stroke: #6c63ff; /* progress color */
  stroke-linecap: round;
  stroke-dasharray: 125; /* circumference (2πr ≈ 125) */
  stroke-dashoffset: calc(125 - (125 * var(--percent)) / 100);
  transition: stroke-dashoffset 0.6s ease;
}

.mini-progress .value {
  position: absolute;
  font-size: 12px;
  font-weight: 600;
  color: #6c63ff;
}
.formBg{
    background-color: #fafafc;
}
.fontThirteen{
    font-size: 13px !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section-06 history padding-30">
    <div class="container-fluid p-4">
        <div class="row">
            <h4 class="mb-3"><b>Wallet</b></h4>
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="balance-card text-center mb-4" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <p>Balance</p>
                    <h2>1280.70 <span style="font-size: 1rem;">EUR</span></h2>
                    <p>Available</p>
                    <button class="btn btn-light w-100">Withdraw</button>
                    <small class="d-block mt-2">You have to verify your personal and address information to withdraw your earnings</small>
                </div>
                <div class="card latest-activities mb-4">
                    <div class="card-header fw-bold">Latest Activities</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/35?img=1" class="me-2"> Jane Cooper
                            </div>
                            <span class="text-danger">-€2400.70</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/35?img=2" class="me-2"> Kathryn Murphy
                            </div>
                            <span class="text-danger">-€5400.70</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/35?img=3" class="me-2"> Bessie Cooper
                            </div>
                            <span class="text-success">+€1500.70</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="https://i.pravatar.cc/35?img=4" class="me-2"> Leslie Alexander
                            </div>
                            <span class="text-danger">-€400.70</span>
                        </li>
                    </ul>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">Reports</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select">
                                <option>All Transaction</option>
                                <option>Withdraw</option>
                                <option>Deposit</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Date Range</label>
                            <input type="date" class="form-control mb-2">
                            <input type="date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                
                <div class="card">
                    <h5 class="p-3 mb-0 tran"><b>Transaction</b></h5>
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <input type="date" class="form-control d-inline-block w-auto"> -
                            <input type="date" class="form-control d-inline-block w-auto">
                        </div>
                        <div>
                            <input type="text" placeholder="Search..." class="form-control">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="withdraw" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="bi bi-arrow-down-left-circle" style="color: #52b581; vertical-align: middle; margin-right: 8px;"></i> Withdraw</td>
                                    <td>30 Oct 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-pending ">Pending</td>
                                </tr>
                                <tr>
                                    <td class="deposit"> <i class="bi bi-arrow-up-right-circle" style="color: #bf627d; vertical-align: middle; margin-right: 8px;"></i> Deposit</td>
                                    <td>20 Mar 2022</td>
                                    <td>€2200.00</td>
                                    <td class="status-pending">Pending</td>
                                </tr>
                                <tr>
                                    <td class="withdraw">  <i class="bi bi-arrow-down-left-circle" style="color: #52b581; vertical-align: middle; margin-right: 8px;"></i> Withdraw</td>
                                    <td>14 Mar 2022</td>
                                    <td>€2300.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="withdraw">  <i class="bi bi-arrow-down-left-circle" style="color: #52b581; vertical-align: middle; margin-right: 8px;"></i> Withdraw</td>
                                    <td>13 Mar 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="deposit">  <i class="bi bi-arrow-up-right-circle" style="color: #bf627d; vertical-align: middle; margin-right: 8px;"></i> Deposit</td>
                                    <td>9 Mar 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="withdraw"> <i class="bi bi-arrow-down-left-circle" style="color: #52b581; vertical-align: middle; margin-right: 8px;"></i> Withdraw</td>
                                    <td>7 Mar 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="deposit">  <i class="bi bi-arrow-up-right-circle" style="color: #bf627d; vertical-align: middle; margin-right: 8px;"></i> Deposit</td>
                                    <td>3 Mar 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="withdraw">  <i class="bi bi-arrow-down-left-circle" style="color: #52b581; vertical-align: middle; margin-right: 8px;"></i> Withdraw</td>
                                    <td>30 Jan 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="withdraw">  <i class="bi bi-arrow-down-left-circle" style="color: #52b581; vertical-align: middle; margin-right: 8px;"></i> Withdraw</td>
                                    <td>30 Jan 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                                <tr>
                                    <td class="deposit">  <i class="bi bi-arrow-up-right-circle" style="color: #bf627d; vertical-align: middle; margin-right: 8px;"></i> Deposit</td>
                                    <td>30 Jan 2022</td>
                                    <td>€3200.00</td>
                                    <td class="status-completed">Completed</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 1 -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                       <h1 class="modal-title fs-5" id="staticBackdropLabel"><b>Transactions Details</b></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                            <div>20 June 2020 20:39 UTC +1</div>
                            <small class="text-muted">Trans ID: 0xbn23274351446142L</small>
                            </div>
                            <div class="text-end">
                            <div class="transaction-amount">€4883.25</div>
                            <span class="transaction-status">Pending</span>
                            </div>
                        </div>
                        <hr>
                        <h6 class="fw-bold mb-3">Account details</h6>
                        <div class="account-details">
                            <div class="row">
                            <div class="col-6 text-muted">Bank</div>
                            <div class="col-6 text-end">Citi Bank</div>
                            </div>
                            <div class="row">
                            <div class="col-6 text-muted">Account Holder</div>
                            <div class="col-6 text-end">Peter Brandstetter</div>
                            </div>
                            <div class="row">
                            <div class="col-6 text-muted">IBAN</div>
                            <div class="col-6 text-end text-primary">
                                <a href="#">0xde7551208001f245126199</a>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-muted">BIC</div>
                                <div class="col-6 text-end">2992271781</div>
                            </div>
                        </div>
                        <div class="support-box d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">Need help?</h6>
                                <small class="fontThirteen">If there is a problem with the transactions make sure to contact your support.</small>
                            </div>
                            <button class="btn d-flex align-items-center"><i class="bi bi-headset me-1"></i> Support</button>
                        </div>
                        <div class="modal-footer border-0 flex-column pb-0">
                            <button class="btn btn-primary w-100 rounded-3 mb-2" onclick="window.print()" data-bs-toggle="modal" data-bs-target="#successMessage">Print Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 2 -->
        <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 shadow p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Withdraw Money</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="fw-semibold mb-1">Add payment method</h6>
                    <p class="text-muted small">Lorem Ipsum is simply dummy text of the printing.</p>
                    <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="card border rounded-3 p-3 h-100 option-card">
                        <div class="mb-2">
                            <i class="bi bi-bank fs-1 text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Bank Account</h6>
                        <p class="text-muted small mb-0">Wire money directly to your bank account</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border rounded-3 p-3 h-100 option-card">
                        <div class="mb-2">
                            <i class="bi bi-paypal fs-1 text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Paypal</h6>
                        <p class="text-muted small mb-0">Transfer your earnings to your PayPal account</p>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn print-btn btn-primary" data-bs-toggle="modal" data-bs-target="#bankModal"> Continue</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Modal 3 -->
        <div class="modal fade" id="bankModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel"><b>Withdraw Money</b></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 mb-2">
                            <div class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#withdrawMoneyModal">
                                <i class="bi bi-bank fs-3 text-primary me-2"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Afsar Hossen</h6>
                                    <small class="text-muted">2183XXX001</small>
                                </div>
                            </div>
                                <button class="btn btn-link text-danger fw-semibold p-0" data-bs-toggle="modal" data-bs-target="#withdrawMoneyModal">Remove</button>
                            </div>
                            <h6 class="fw-semibold mb-0">Enter bank details</h6>
                            <p class="text-muted fontThirteen">
                            You can choose to save the following information for faster withdrawal next time.
                            Your information is securely stored by Business Masterclass following GDPR regulations.
                            </p>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control formBg" placeholder="Citi Bank">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Account Holder</label>
                                    <input type="text" class="form-control formBg" placeholder="Enter account holder name">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                    <label class="form-label">IBAN</label>
                                    <input type="text" class="form-control formBg" placeholder="Enter IBAN">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    <label class="form-label">BIC</label>
                                    <input type="text" class="form-control formBg" placeholder="Enter BIC">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button class="btn btn-primary">Add Account</button>
                                    <button class="btn btn-secondary py-2" disabled>Continue</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
        <!-- Modal 4 -->
        <div class="modal fade" id="withdrawMoneyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3 rounded-4">
                    <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Withdraw Money</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <hr class="my-0">
                    <div class="modal-body">
                    <p class="mb-1 text-muted">To</p>
                    <div class="border rounded-3 p-3 d-flex align-items-center" style="background: #f4f8fe;">
                        <div class="me-3">
                        <i class="bi bi-bank fs-1 text-primary"></i>
                        </div>
                        <div>
                        <h6 class="mb-0 fw-bold">Afsar Hossen</h6>
                        <small class="text-muted">2183XXXXXXXX001</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6 class="fw-bold">Amount</h6>
                        <p class="text-muted">How much would you like to withdraw?</p>
                        <h2 class="fw-bold text-center my-3">€ 0.00</h2>
                        <p class="text-center fw-semibold text-muted">
                        Available Balance: <span class="text-dark">€ 7200.50</span>
                        </p>
                    </div>
                    </div>
                    <div class="d-grid gap-2 mt-3" data-bs-toggle="modal" data-bs-target="#withdrawMoneyModalTwo">
                        <button class="btn btn-primary">Withdraw</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 5 -->
        <div class="modal fade" id="withdrawMoneyModalTwo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Withdraw Money</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <p class="mb-1 text-muted px-5">To</p>
                    <div class="border rounded-3 d-flex align-items-center  mx-5 p-2" style="background: #f4f8fe;">
                        <i class="bi bi-bank fs-1 text-primary me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Afsar Hossen</h6>
                            <small class="text-muted">2183XXXXXXX001</small>
                        </div>
                    </div>
                    <div class="mt-4 px-5">
                    <h6 class="fw-bold">Amount</h6>
                    <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted mb-0 fontThirteen">
                        The amount will be transferred to your account
                    </p>
                     <h2 class="fw-bold mb-0">€ 500</h2>
                    </div>
                    </div>
                    <div class="py-4 px-5" style="background: #fafafa; border-top: 1px solid #f1eded;">
                    <h6 class="fw-bold">Enter Verification Code</h6>
                    <p class="text-muted mb-1 fontThirteen">
                        We have sent a 6-digit code to your phone number as stated below
                        <a href="tel:+564883783987" class="text-primary fw-semibold">+564 883 783 987</a>.
                        The code will expire in 15 minutes. Contact your sponsor if you have trouble getting the code.
                    </p>
                    <div class="d-flex justify-content-between my-3">
                        <input type="text" maxlength="1" class="form-control text-center fs-4 mx-1" style="width:50px;">
                        <input type="text" maxlength="1" class="form-control text-center fs-4 mx-1" style="width:50px;">
                        <input type="text" maxlength="1" class="form-control text-center fs-4 mx-1" style="width:50px;">
                        <input type="text" maxlength="1" class="form-control text-center fs-4 mx-1" style="width:50px;">
                        <input type="text" maxlength="1" class="form-control text-center fs-4 mx-1" style="width:50px;">
                        <input type="text" maxlength="1" class="form-control text-center fs-4 mx-1" style="width:50px;">
                    </div>
                    <p class="text-center text-muted mb-0">Code expires in <span class="fw-semibold">15:00</span></p>
                    </div>
                </div>
                <div class="modal-footer border-0 flex-column pt-0 pb-2" style="background: #fafafa;">
                    <button class="btn btn-primary w-100 rounded-3 mb-2" data-bs-toggle="modal" data-bs-target="#successMessage">Submit</button>
                    <p class="text-muted small mb-0">Didn’t get a code? <a href="#" class="text-primary fw-semibold">Resend</a></p>
                </div>
                </div>
            </div>
        </div>
        <!-- Modal 6 -->
        <div class="modal fade" id="successMessage" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3 rounded-4">
                <div class="modal-header border-0 py-0">
                    <h5 class="modal-title fw-bold">Withdraw Money</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <hr class="my-0">
                <div class="modal-body text-center">
                    <div class="">
                        <i class="bi bi-check2-circle text-success fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Congratulations</h4>
                    <p class="text-muted">
                    Your withdrawal request is taken into account and your money will be transferred to your account in 1-2 business days
                    </p>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <div class="text-start">
                        <strong>20 June 2020 20:38 UTC +1</strong><br>
                        <small class="text-muted">Trans ID: 0xbn23274351446142L <i class="bi bi-clipboard ms-1"></i></small>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0">€4883.25</h5>
                        <span class="badge bg-warning text-dark">Pending</span>
                    </div>
                    </div>
                    <div class="text-start mb-4">
                    <h6 class="fw-bold">Account details</h6>
                    <div class="d-flex justify-content-between">
                        <span>Bank</span><span>Citi Bank</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Account Holder</span><span>Peter Brandstetter</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>IBAN</span>
                        <span class="text-primary">0xde7551208001f245126199 <i class="bi bi-clipboard ms-1"></i></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>BIC</span><span>2992271781</span>
                    </div>
                    </div>
                    <div class="p-3 bg-light rounded-3 border mb-3 text-start">
                        <h6 class="fw-bold">Need help?</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0 fontThirteen">
                            If there is a problem with the transactions make sure to contact your support.
                            </p>
                            <button class="btn btn-primary btn-sm ms-3">
                            <i class="bi bi-headset me-1"></i> Support
                            </button>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mt-3" data-bs-toggle="modal" data-bs-target="#withdrawMoneyModalTwo">
                        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-2"></i> Print Details</button>
                    </div>
                </div>
                </div>
            </div>
        </div>


        




    </div>
</section>
<?php $__env->stopSection(); ?>
<script>
  document.querySelectorAll(".mini-progress").forEach(el => {
    const percent = el.getAttribute("data-percent");
    el.style.setProperty("--percent", percent);
    el.querySelector(".value").textContent = percent + "%";
  });
</script>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('public/user_dashboard/js/sweetalert/sweetalert-unpkg.min.js')); ?>"></script>
<?php echo $__env->make('user_dashboard.layouts.common.check-user-status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.user-transactions-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\lubypay\resources\views/user_dashboard/layouts/wallet.blade.php ENDPATH**/ ?>