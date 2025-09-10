@extends('user_dashboard.layouts.app')
@section('title', 'Comissions')

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
        /* height: 285px; */
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
    .cardCircle {
    align-items: center;
    width: fit-content;
    float: inline-end;
    }
    .cardCircle .percent {
    position: relative;
    }

    .cardCircle svg {
    position: relative;
    width: 90px;
    height: 90px;
    transform: rotate(-90deg);
    }

    .cardCircle svg circle {
    width: 100%;
    height: 100%;
    fill: none;
    stroke: #f0f0f0;
    stroke-width: 1;
    stroke-linecap: round;
    }

    .cardCircle svg circle:last-of-type {
    stroke-dasharray: 625px;
    stroke-dashoffset: calc(625px - (625px * var(--percent)) / 100);
    stroke: #3498db; 
    }

    .cardCircle .number {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    }

    .cardCircle .number h3 {
    font-weight: 200;
    font-size: 3.5rem;
    }

    .cardCircle .number h3 span {
    font-size: 2rem;
    }

    .cardCircle .title h2 {
    margin: 25px 0 0;
    }

    .cardCircle:nth-child(1) svg circle:last-of-type {
    stroke: #f39c12;
    }

    .cardCircle:nth-child(2) svg circle:last-of-type {
    stroke: #2ecc71;
    }

    .cardCircle svg {
    position: relative;
    width: 95px; 
    height: 95px;
    transform: rotate(-90deg);
    }

    .cardCircle svg circle {
    fill: none;
    stroke: #f0f0f0;
    stroke-width: 5;
    stroke-linecap: round;
    }
    .cardCircle svg circle:last-of-type {
    stroke-dasharray: 314px; 
    stroke-dashoffset: calc(314px - (314px * var(--percent)) / 100);
    stroke: #3498db;
    }
    .cardCircle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: fit-content;
    background: #ffffff;
    border: none;
    }

    .cardCircle .percent {
    position: relative;
    }

    .cardCircle svg {
    display: block;
    width: 60px;
    height: 60px;
    transform: rotate(-90deg);
    }

    .cardCircle svg circle {
    fill: none;
    stroke: #f0f0f0;
    stroke-width: 5;
    stroke-linecap: round;
    }
    .cardCircle svg circle:last-of-type {
    stroke-dasharray: 157px; 
    stroke-dashoffset: calc(157px - (157px * var(--percent)) / 100);
    stroke: #3498db;
    }
    .cardCircle .number {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 14px;
    font-weight: bold;
    color: #333;
    }
    .cardCircle svg circle {
    fill: none;
    stroke: #f0f0f0;
    stroke-width: 5;
    stroke-linecap: round;
    }
    .commission-card:nth-child(1) .cardCircle svg circle:last-of-type {
    stroke: #573aef; 
    }

    .commission-card:nth-child(2) .cardCircle svg circle:last-of-type {
    stroke: #44b07e !important; 
    }
    .commission-card:nth-child(3) .cardCircle svg circle:last-of-type {
    stroke: #e7b731; 
    }
    .commission-card:nth-child(4) .cardCircle svg circle:last-of-type {
    stroke: #f7763b; 
    }

    .table td, 
    .table th {
    text-align: left !important;
    }

</style>
@endsection

@section('content')
<section class="section-06 history padding-30">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="fa fa-trophy" style="font-size: 30px;color:#573aef; background: #f2f0fe; padding: 14px; border-radius: 40px;"></i>
                    <h5>Champion</h5>
                    <p>ab 904</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="commission-value">€100.000</div>
                        <div class="card cardCircle p-0">
                            <div class="percent">
                            <svg viewBox="0 0 60 60">
                                <circle cx="30" cy="30" r="25"></circle>
                                <circle cx="30" cy="30" r="25" style="--percent: 30"></circle>
                            </svg>
                            <div class="number">
                                <p class="mb-0">30%</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="fa fa-trophy" style="font-size: 30px; color: #44b07e; background: #eff9f6; padding: 14px; border-radius: 40px;"></i>
                    <h5>Master</h5>
                    <p>ab 904</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="commission-value">€100.000</div>
                        <div class="card cardCircle p-0">
                            <div class="percent">
                            <svg viewBox="0 0 60 60">
                                <circle cx="30" cy="30" r="25"></circle>
                                <circle cx="30" cy="30" r="25" style="--percent: 40"></circle>
                            </svg>
                            <div class="number">
                                <p class="mb-0">40%</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="fa fa-trophy" style="font-size: 30px; color:#e7b731; background: #fef8ef; padding: 14px; border-radius: 40px;"></i>
                    <h5>Elite</h5>
                    <p>ab 904</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="commission-value">€100.000</div>
                        <div class="card cardCircle p-0">
                            <div class="percent">
                            <svg viewBox="0 0 60 60">
                                <circle cx="30" cy="30" r="25"></circle>
                                <circle cx="30" cy="30" r="25" style="--percent: 50"></circle>
                            </svg>
                            <div class="number">
                                <p class="mb-0">50%</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="commission-card mb-3">
                    <i class="fa fa-file-text" style="font-size: 30px;color: #f7763b; background: #fbf3ec; padding: 14px; border-radius: 40px;"></i>
                    <h5>Expert</h5>
                    <p>ab 904</p>
                     <div class="d-flex justify-content-between align-items-center">
                        <div class="commission-value">€100.000</div>
                        <div class="card cardCircle p-0">
                            <div class="percent">
                            <svg viewBox="0 0 60 60">
                                <circle cx="30" cy="30" r="25"></circle>
                                <circle cx="30" cy="30" r="25" style="--percent: 80"></circle>
                            </svg>
                            <div class="number">
                                <p class="mb-0">80%</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <td class=""><p><b>COMMISSION LEVEL</b></p></th>
                        <td><p><b>POTENTIAL EARNING/MONTHS</b></p></th>
                        <td><p><b>PERCENT</b></p></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <div style="background: #baeada; color: #306855; padding: 9px; border-radius: 22px; text-align: center;"><b>1</b></div>
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
                                    <div style="background: #ffd4de; color: #ad687c; padding: 9px; border-radius: 22px; text-align: center;"><b>2</b></div>
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
                                    <div style="background: #ffded0; color: #a86860; padding: 9px; border-radius: 22px; text-align: center;"><b>4</b></div>
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
                                    <div style="background: #e6b3e5; color: #795b88; padding: 9px; border-radius: 22px; text-align: center;"><b>3</b></div>
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
                                    <div style="background: #baeada; color: #306855; padding: 9px; border-radius: 22px; text-align: center;"><b>5</b></div>
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
