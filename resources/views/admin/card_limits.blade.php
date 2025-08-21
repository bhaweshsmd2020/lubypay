@extends('admin.layouts.master')

@section('title', 'User Card')

@section('head_style')
    <!-- Bootstrap daterangepicker -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
  
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

    <!-- jquery-ui-1.12.1 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.css')}}">
@endsection

@section('page_content')
   <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Card Limits</div>
                </div>

                
              
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <!--<div class="panel-heading">-->
                        <!--    <div class="row">-->
                        <!--        <div class="col-md-8">-->
                        <!--            <h3 class="panel-title"></h3>-->
                        <!--        </div>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
                    
                        
                       
                        <div class="panel-body">
                            <div class="table-responsive">
    <table id="example" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        
                                        <tr>
                                            <th>S. No</th>
                                            <th>Card Number</th>
                                            <th>Card Limit($)</th>
                                            <th>Available Limit($)</th>
                                            <th>Duration</th>
                                            <th>Status </th>
                                            <th>Created On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user_view_card as $k=>$card)
                                            <?php
                                                $limit = DB::table('virtual_card_transactions')->where('card_token',$card->token)->sum('amount');
                                            ?>
                                            <tr>  
                                                <td class="text-center">{{ $card->id }}</td>
                                                <td class="text-right">{{ $card->last_four_digit }}</td>
                                                <td class="text-right">{{ bcdiv($card->spend_limit, 1, 2) }}</td>
                                                <td class="text-right">{{ bcdiv($card->spend_limit - $limit, 1, 2) }}</td>
                                                <td>{{$card->spend_limit_duration}}</td></td>
                                                <td>{{$card->card_state}}</td>
                                                <td>{{ Carbon\Carbon::parse($card->created_at)->format('d-M-Y') }}</td>
                                            </tr>
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

<script type="text/javascript">
  $(document).ready(function() {
$('#example').DataTable();
} );
</script>

<!-- Bootstrap daterangepicker -->
<script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

<!-- jquery-ui-1.12.1 -->
<script src="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.js') }}" type="text/javascript"></script>



<script type="text/javascript">

</script>

@endpush
