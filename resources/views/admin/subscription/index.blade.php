@extends('admin.layouts.master')
@section('title', 'Subscriptions List')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <style>
        #example_wrapper{
            overflow: scroll;
        }
    </style>

    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="top-bar-title padding-bottom pull-left">Subscriptions List</div>
                </div>
                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_subscriptions'))
                    <div class="col-md-3 text-right">
                        <a href="{{url('admin/add-subscription')}}" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Subscription</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $k=>$val)
                                    <tr>
                                        <td>{{++$k}}.</td>
                                        <td>{{$val->title}}</td>
                                        <td>{{$val->price}}</td>
                                        <td>
                                            @if($val->status==0)
                                            <span class="badge badge-pill badge-danger">{{__('Inactive')}}</span>
                                            @elseif($val->status==1)
                                            <span class="badge badge-pill badge-success">{{__('Active')}}</span>
                                            @endif
                                        </td>
                                        <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                        <td>
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_subscriptions'))
                                                <a href="{{url('admin/edit-subscription/'.$val->id)}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                            @endif
                                            
                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_subscriptions'))
                                                <a href="{{url('admin/delete-subscription/'.$val->id)}}" class="btn btn-xs btn-danger delete-warning" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                                            @endif

                                            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_subscriptions'))
                                                <button 
                                                    class="btn btn-xs btn-info openFeeModal" 
                                                    data-subscription-id="{{ $val->id }}" 
                                                    data-subscription-title="{{ $val->title }}" 
                                                    data-toggle="modal" 
                                                    data-target="#feeDetailsModal" 
                                                    title="Fee Details">
                                                    <i class="fa fa-edit"></i> Fees
                                                </button>
                                            @endif
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

    <div class="modal fade" id="feeDetailsModal" tabindex="-1" role="dialog" aria-labelledby="feeDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="feeDetailsModalLabel">
                        <strong>Fee Details - <span id="modalSubscriptionName"></span></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="feeDetailsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Symbol</th>
                                <th>Logo</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $index => $value)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->symbol }}</td>
                                <td><img src="{{ url('public/uploads/currency_logos/'.$value->logo) }}" style="width: 100px; height: 50px;"></td>
                                <td>
                                    <a href="#" class="label label-primary fee-link" data-currency-id="{{ $value->id }}">Fees</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/pdfmake.min.js" ></script>
    <script type="text/javascript"  src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable({
                "dom": '<"dt-buttons"Bf><"clear">lirtp',
                "paging": true,
                "autoWidth": true,
                "buttons": ['colvis','copyHtml5','csvHtml5','excelHtml5','pdfHtml5','print']
            });

            $('#feeDetailsModal').on('shown.bs.modal', function () {
                if (! $.fn.DataTable.isDataTable('#feeDetailsTable')) {
                    $('#feeDetailsTable').DataTable({
                        "paging": true,
                        "searching": true,
                        "autoWidth": false
                    });
                }
            });
        
            let subscriptionId = null;
            let subscriptionTitle = null;

            $('.openFeeModal').on('click', function() {
                subscriptionId   = $(this).data('subscription-id');
                subscriptionTitle = $(this).data('subscription-title');
                $('#modalSubscriptionInfo').text(subscriptionTitle + ' (ID: ' + subscriptionId + ')');
                $('#modalSubscriptionName').text(subscriptionTitle);
            });

            $('#feeDetailsModal').on('shown.bs.modal', function () {
                $('#feeDetailsTable .fee-link').each(function() {
                    let currencyId = $(this).data('currency-id');
                    let newUrl = "{{ url('admin/settings/feeslimit/subscription') }}/" + subscriptionId + "/" + currencyId;
                    $(this).attr('href', newUrl);
                });

                if (! $.fn.DataTable.isDataTable('#feeDetailsTable')) {
                    $('#feeDetailsTable').DataTable({
                        "paging": true,
                        "searching": true,
                        "autoWidth": false
                    });
                }
            });
        });
    </script>
@endsection