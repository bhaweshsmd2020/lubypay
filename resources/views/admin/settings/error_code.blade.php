@extends('admin.layouts.master')
@section('title', 'Social Settings')
@section('head_style') 
    <!-- dataTables -->
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection
@section('page_content')

    <!-- Main content -->
    <div class="row">
       
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible" style="width: fit-content;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success!</strong> {{ Session::get('success') }}
        </div>
       
        @endif
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.settings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">All Error Codes</h3>
                </div>
               @if($errors->any())
                    <div class="alert alert-danger">
                       <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif
                  <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab_1">
                            <div class="box-body" >
                                @if($service->count() > 0)
                                    <table id="example" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>S. No</th>
                                                <th>Error Code</th>
                                                <th>Error Message</th>
                                                <th>Status</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <tr>  
                                            <td>1</td>  
                                            <td>200</td> 
                                            <td>Thanks</td> 
                                            <td>Active</td> 
                                          </tr>  
                                             </tbody>
                                      
                                    </table>
                                @else
                                    <h5 style="padding: 15px 20px; ">Utility not found!</h5>
                                @endif
                            </div>
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
<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        }
    });

    $('#social_links').validate({
        rules: {
            facebook: {
                // required: true,
                url: true,
            },
            google_plus: {
                // required: true,
                url: true,
            },
            twitter: {
                // required: true,
                url: true,
            },
            linkedin: {
                // required: true,
                url: true,
            },
            pinterest: {
                // required: true,
                url: true,
            },
            youtube: {
                // required: true,
                url: true,
            },
            instagram: {
                // required: true,
                url: true,
            },
        },
    });

</script>

@endpush
