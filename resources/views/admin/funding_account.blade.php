@extends('admin.layouts.master')

@section('title', 'User Card')

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

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Funding  Accounts</div>
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
                                              <th>User id</th>
                                            <th>Name</th>
                                            <th>Account name</th>
                                            <th>Account STATE</th>
                                            <th>Type </th>
                                            <th>last four</th>
                                            <th>Action</th>
                 </tr>
                                    </thead>
                                   <tbody>
                                       <?php $i = 0 ?>
                                   @foreach($virtual_cards_funding as $card)
                                    <?php $i++ ?>
                                       <tr>  
                                           <td>{{ $i}}</td>
                                            <td>{{$card->id}}</td>
                                            <td>{{$card->nickname}}</td>
                                           <td>{{$card->account_name}}</td>
                                            <td>{{$card->account_state}}</td>
                                           <td>{{$card->type}}</td>
                                           <td>**** **** **** {{$card->last_four}}</td>
                                           <td>
                                                 <a href="{{url('admin/card/card-transactions')}}"<span class="dtr-data"><span class="btn btn-xs btn-info">Edit</span></span></a>
                                             </td>
                                           

                                             
                                             
                                             
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
