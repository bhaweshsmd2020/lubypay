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
   <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                 <a href="#">Open</a>
                </li>
                
                <li>
                  <a href="">Pause</a>
                </li>
                <li>
                  <a href="">Delete</a>
                </li>
                <li class="active">
                 <a href="{{url('admin/card/user-transactions')}}/22">Transaction</a>
                </li>

           </ul>
          <div class="clearfix"></div>
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
                                <table class="table" >
                                    <thead>
                                        
                                         <tr>
                                             <th>S. No</th>
                                            <th>Funding Account</th>
                                            <th>Update card</th>
                                            <th>Transaction</th>
                                            <th>Enroll </th>
                                            <th>Update</th>
                                            <th>Transaction limit</th>
                                         </tr>
                                    </thead>
                                   <tbody>
                                       
                                        <tr>
                                             <td>1</td>
                                             <td>Manish</td>
                                             <td>Last name</td>
                                             <td>raj@gmail.com</td>
                                             <td>3232232323</td>
                                             <td>09/12/2020</td>
                                             <td>01</td>
                                            
                                        </tr>
                                        
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

</script>

@endpush
