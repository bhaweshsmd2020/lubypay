
@extends('admin.layouts.master')

@section('title', 'Dhiraagu Topup')

@section('head_style') 
    <!-- dataTables -->
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
 <!--MODEL POPUP START HERE-->
      <div class="modal fade" id="sessionlogout" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
             
            <div class="modal-body p-0">
             <h3 style="font-size: 21px;padding:12px;background-color:#800000;color:white;">You can topup by below service provider<button type="button" style="float:right;color:white" class="close" data-dismiss="modal">&times;</button></h3>
                <div class="card border-0 mb-0">
                <div class="card-body px-lg-5 py-lg-5">
                    
                  <div class="text-left mt-2 mb-3"></div> 
                    <div class="text-center">
                       <div class="row" >
                           <div class="col-md-6">
                               <p><b>Provider Name :</b> Dhiraagu</p>
                               <p><img src="{{asset('public/dhiraagu.png')}}" style="height: 110px;"> </p>
                                 <a class="btn btn-success" href="{{ url('admin/new-reload') }}"><span class="fa fa-plus"> &nbsp;</span>With Dhiraagu</a>
                           </div>
                       
                        <div class="col-md-6">
                           <p><b>Provider Name :</b> Raastas </p>
                           <p> <img src="{{asset('public/ooredoo.png')}}" style="height: 110px;"> </p>
                          <a class="btn btn-success" href="{{ url('admin/ooredoo-reload') }}" ><span class="fa fa-plus"> &nbsp;</span>With Ooredoo</a>
                        </div>
                       
                    </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<!--END MODEL-->
    <div class="nav">
        <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#home">Recharge</a>
        </li>
        <li class="nav-item">
            <a href="#profiles">Raastas</a>
        </li>
    </ul>
    </div>
    <!-- Main content -->
     <div id="home" class="tab-content">
      <div class="row">
        
        <div class="col-md-12">
            <div class="box box_info"> 
                  <div class="box-header">
                    <h3 class="box-title">Recharge Topup List</h3>
                     @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_user'))
                    <div style="float:right;"><a class="btn btn-success" data-toggle="modal" data-target="#sessionlogout"><span class="fa fa-plus"> &nbsp;</span>New Reload</a></div>
                    @endif
                  </div>
                  <hr>
                  <div class="box-body table-responsive">
                     <table id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Customer Name</th>
                                <th>Customer Number</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                             $num = 1;
                            @endphp
                            @foreach($details as $value)
                            <tr>
                                <td>{{$num}}</td>
                                <td>{{App\Models\User::find($value->customer_id)->first_name??'NA'}}</td>
                                <td>{{$value->reload_destinationNumber}}</td>
                                 <td>{{$value->reload_transaction_id}}</td>
                                <td>{{$value->reload_amount}}</td>
                               @if($value->reload_transactionStatus == 1)
                                <td>Success</td> 
                                @else
                                 <td>Failed</td> 
                                 @endif
                              <td>{{$value->created_at}}</td>
                            </tr>
                            @php
                             $num++;
                            @endphp
                            @endforeach
                             </tbody>
                      
                    </table>
                  </div>
            </div>
        </div>
    </div>
     </div>
     <div id="profiles" class="tab-content">
      <div class="row">
        
        <div class="col-md-12">
            <div class="box box_info"> 
                  <div class="box-header">
                    <h3 class="box-title">Raastas Topup List</h3>
                     @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_user'))
                    <div style="float:right;"><a class="btn btn-success" data-toggle="modal" data-target="#sessionlogout"><span class="fa fa-plus"> &nbsp;</span>New Reload</a></div>
                    @endif
                  </div>
                  <hr>
                  <div class="box-body table-responsive">
                     <table id="example1" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Customer Name</th>
                                <th>Customer Number</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                             $num = 1;
                            @endphp
                            @foreach($ooredoo as $value)
                            <tr>
                                <td>{{$num}}</td>
                                <td>{{App\Models\User::find($value->customer_id)->first_name??'NA'}}</td>
                                <td>{{$value->reload_destinationNumber}}</td>
                                 <td>{{$value->reload_transaction_id}}</td>
                                <td>{{$value->reload_amount}}</td>
                               @if($value->reload_transactionStatus == 1)
                                <td>Success</td> 
                                @else
                                 <td>Failed</td> 
                                 @endif
                              <td>{{$value->created_at}}</td>
                            </tr>
                            @php
                             $num++;
                            @endphp
                            @endforeach
                             </tbody>
                      
                    </table>
                  </div>
            </div>
        </div>
    </div>
     </div>
@endsection

@push('extra_body_scripts')

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
<script>
$(document).ready(function() {
    $('#example1').DataTable();
} );
</script>
<script>
    $(document).ready(function () {
    $('.tab-content:not(:first)').hide();
    $('.nav ul li a').click(function (event) {
        event.preventDefault();
        var content = $(this).attr('href');
        $(content).addClass("intro");
        //alert(content);
        $(content).show();
        $(content).siblings('.tab-content').hide();
    });
});
</script>
@endpush



