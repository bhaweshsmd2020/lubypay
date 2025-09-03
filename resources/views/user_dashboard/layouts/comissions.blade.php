@extends('user_dashboard.layouts.app')

@section('css')
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
        height: 285px;
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
        font-size: 5px;
        /* font-weight: bold; */
        color: var(--clr);
    }
    .table tr th {
     background: none !important;
    }

    .text-success {
    color:  !important;
}
table thead {
     border-bottom: 0px solid #dee2e6;
}
.table td, 
.table th {
    border-left: none !important;
    border-right: none !important;
}

</style>
@endsection

@section('content')
<section class="section-06 history padding-30">
    <div class="container">

        <!-- Top Commission Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="fa fa-trophy" style="font-size: 30px;color:#573aef; background: #f2f0fe; padding: 14px; border-radius: 40px;"></i>
                    <h5>Champion</h5>
                    <p>ab 904</p>
                        <div class="commission-value" style="position: absolute; bottom: 18px;">€100.000</div>
                        {{-- <img src="public\comissions_images\image.png" class="img-fluid" alt="..." style="position: absolute; bottom: 18px;"> --}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="bi bi-award" style="font-size: 30px;color:#e7b731; background: #fef8ef; padding: 11px 13px; border-radius: 40px;"></i>
                    <h5>Master</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                        {{-- <div class="progress-circle" style="--percent:40; --clr:#6f42c1;">40%</div> --}}
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="bi bi-award" style="font-size: 30px;color: #44b07e; background: #eff9f6; padding: 11px 13px; border-radius: 40px;"></i>
                    <h5>Elite</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="fa fa-file-text" style="font-size: 30px;color: #f7763b; background: #fbf3ec; padding: 14px; border-radius: 40px;"></i>
                    <h5>Expert</h5>
                    <p>ab 904</p>
                    <span class="d-inline" style="position: absolute; bottom: 18px;">
                        <div class="commission-value">€100.000</div>
                        {{-- <div class="progress-circle" style="--percent:40; --clr:#6f42c1;">40%</div> --}}
                    </span>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <td><p><b>COMMISSION LEVEL</b></p></th>
                        <td><p><b>POTENTIAL EARNING/MONTHS</b></p></th>
                        <td><p><b>PERCENT</b></p></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <div style="background: #baeada; color: #306855; padding: 9px; border-radius: 22px;"><b>1</b></div>
                                </div>
                                <div class="col-6 col-md-3 text-start">
                                    <p class="mb-0">Shrimp</p>
                                    <p class="mb-0">3-12</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-success" style="background: #baeada; color: #306855 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">(74,75€ - 999€)</span></td>
                        <td><span class="text-success" style="background: #baeada; color: #306855 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">15%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <div style="background: #ffd4de; color: #ad687c; padding: 9px; border-radius: 22px;"><b>2</b></div>
                                </div>
                                <div class="col-6 col-md-3 text-start">
                                    <p class="mb-0">Small Fish</p>
                                    <p class="mb-0">3-12</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-success" style="background: #ffd4de; color: #ad687c !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">(74,75€ - 999€)</span></td>
                        <td><span class="text-success" style="background: #ffd4de; color: #ad687c !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">15%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <div style="background: #ffded0; color: #a86860; padding: 9px; border-radius: 22px;"><b>4</b></div>
                                </div>
                                <div class="col-6 col-md-3 text-start">
                                    <p class="mb-0">Dolphin</p>
                                    <p class="mb-0">3-12</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-success" style="background: #ffded0; color: #a86860 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">(74,75€ - 999€)</span></td>
                        <td><span class="text-success" style="background: #ffded0; color: #a86860 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">15%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <div style="background: #e6b3e5; color: #795b88; padding: 9px; border-radius: 22px;"><b>3</b></div>
                                </div>
                                <div class="col-6 col-md-3 text-start">
                                    <p class="mb-0">Shark</p>
                                    <p class="mb-0">3-12</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-success" style="background: #e6b3e5; color: #795b88 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">(74,75€ - 999€)</span></td>
                        <td><span class="text-success" style="background: #e6b3e5; color: #795b88 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">15%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <div style="background: #baeada; color: #306855; padding: 9px; border-radius: 22px;"><b>5</b></div>
                                </div>
                                <div class="col-6 col-md-3 text-start">
                                    <p class="mb-0">Shrimp</p>
                                    <p class="mb-0">3-12</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-success" style="background: #baeada; color: #306855 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">(74,75€ - 999€)</span></td>
                        <td><span class="text-success" style="background: #baeada; color: #306855 !important;  padding: 6px 15px; border-radius: 5px; font-size: 13px;">15%</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>
</section>
@endsection

@section('js')


<script src="{{asset('public/user_dashboard/js/sweetalert/sweetalert-unpkg.min.js')}}"></script>
@include('user_dashboard.layouts.common.check-user-status')
@include('common.user-transactions-scripts')
@endsection
