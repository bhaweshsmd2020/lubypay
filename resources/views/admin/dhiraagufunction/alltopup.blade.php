@extends('user_dashboard.layouts.app')
@section('title','All Bill')
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@section('content')
    <div class="row">
        <div class="col-md-12">
          
                
                    <div class="box-header with-border text-center">
                      <h3 class="box-title">All Prepaid Payments Transactions</h3>
                       <div style="float:left;"><a style="background-color: #800000!important;
                        border: none;
                        font-weight: 400;
                        height: 28px;
                        font-size:small;
                        padding:4px 10px 8px 8px;"class="btn btn-success" href="{{ url('add-dhiragu-add-prepaid') }}">New Prepaid</a></div>
                      <input id="myInput" type="text" class="form-control" placeholder="Search.." style="width: 15%;
                    float: right;">
                    </div>
                   
                    <div class="box box-body">
                     <div class="table-responsive">
                      <table  class="display table" width="100%" >
                        <thead class="text-left">
                            <th>Sr No</th>
                            <th>Bill Number</th>
                            <th>Customer Name</th>
                            <th>Amount</th>
                            <th>Transaction ID</th>
                            <th>Status</th>
                            <th>Date & Time</th>
                          </thead>  
                        <tbody id="myTable">  
                       
                          <tr>  
                            <td>1</td>  
                            <td>12345</td> 
                             <td>Shubham Kumar</td> 
                            <td>100</td>  
                            <td>TRAN123</td>  
                            <td>Pending</td>  
                            <td><?php echo date('d-m-Y h:i:s')?></td>  
                          </tr>  
                         
                        </tbody>  
                      </table> 
                    </div>
            </div>
       
    </div>
@endsection

@section('js')

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>

<!-- read-file-on-change -->
@include('common.read-file-on-change')

<script>
$(document).ready(function(){
   // $('#myTable').dataTable();
});
</script>
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
@endsection



