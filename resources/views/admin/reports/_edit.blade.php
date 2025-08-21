@extends('admin.layouts.master')

@section('title', 'Deposits')

@section('head_style')
    <!-- Bootstrap daterangepicker -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">

    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

    <!-- jquery-ui-1.12.1 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.css')}}">
@endsection

@section('page_content')
    <div class="box">
        <div class="box-body">
        
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Expenses</div>
                </div>
                <div class="col-md-2 pull-right">
                        <a href="{{ url('admin/report') }}" class="btn btn-success btn-flat"><span class="fa fa-backward"> &nbsp;</span>Back</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php
        use App\Models\Setting;
        
        $transactional = Setting::where('id', '37')->first();
        $operational   = Setting::where('id', '38')->first();
        $operational_a = Setting::where('id', '39')->first();
        $operational_b = Setting::where('id', '40')->first();
        
        $revenue = ($tansactions->charge_percentage + $tansactions->charge_fixed); 
        $transactional_revenue = ($transactional->value*$revenue)/100;
        $operational_revenue = ($operational->value*$revenue)/100;
        $operationala_revenue = ($operational_a->value*$operational_revenue)/100;
        $operationalb_revenue = ($operational_b->value*$operational_revenue)/100;
    ?>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3 col-md-offset-3">
                    <div class="panel panel-primary">
                        <div class="panel-body text-center" style="padding:5px; margin-bottom: 0;">
                            <span class="text-info" style="font-size: 15px">Transactional Expenses</span>
                            @if(!empty($revenues))
                                @foreach ($currencies as $currency)
                                    @if($currency->id == $revenues->currency_id)
                                        <strong><h4>{{ moneyFormat($currency->code , formatNumber($revenues->transactional)) }}</h4></strong>
                                    @endif
                                @endforeach
                            @else
                                @foreach ($currencies as $currency)
                                    @if($currency->id == $tansactions->currency_id)
                                        <strong><h4>{{ moneyFormat($currency->code , formatNumber($transactional_revenue)) }}</h4></strong>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                      
                <div class="col-md-3">
                    <div class="panel panel-primary">
                        <div class="panel-body text-center" style="padding:5px; margin-bottom: 0;">
                            <span class="text-info" style="font-size: 15px">Operational Expenses</span>
                            @if(!empty($revenues))
                                @foreach ($currencies as $currency)
                                    @if($currency->id == $revenues->currency_id)
                                        <strong><h4>{{ moneyFormat($currency->code , formatNumber($revenues->operational)) }}</h4></strong>
                                    @endif
                                @endforeach
                            @else
                                @foreach ($currencies as $currency)
                                    @if($currency->id == $tansactions->currency_id)
                                        <strong><h4>{{ moneyFormat($currency->code , formatNumber($operational_revenue)) }}</h4></strong>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="panel-title">All Operational Expenses</h3>
                      </div>
                  </div>
                </div>
                <div class="panel-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Operational Expenses A</th>
                          <th scope="col">Operational Expenses B</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($currencies as $currency)
                            @if($currency->id == $tansactions->currency_id)
                                <tr>
                                  <th scope="row">1</th>
                                    @if(!empty($revenues))
                                        @foreach ($currencies as $currency)
                                            @if($currency->id == $revenues->currency_id)
                                                <td>{{ moneyFormat($currency->code , formatNumber($revenues->operational_a)) }}</td>
                                                <td>{{ moneyFormat($currency->code , formatNumber($revenues->operational_b)) }}</td>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($currencies as $currency)
                                            @if($currency->id == $tansactions->currency_id)
                                                <td>{{ moneyFormat($currency->code , formatNumber($operationala_revenue)) }}</td>
                                                <td>{{ moneyFormat($currency->code , formatNumber($operationalb_revenue)) }}</td>
                                            @endif
                                        @endforeach
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
    
        </div>
    </div>


@endsection

@push('extra_body_scripts')

<!-- Bootstrap daterangepicker -->
<script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

<!-- jquery-ui-1.12.1 -->
<script src="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    $(".select2").select2({});


</script>

@endpush
