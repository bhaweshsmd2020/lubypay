@extends('admin.layouts.master')
@section('title', 'Social Settings')

@section('page_content')

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.settings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Virtual Cards Limit</h3>
                </div>

                <form action="{{ url('admin/settings/update-virtual-card') }}" method="post" class="form-horizontal" id="updateVirtualCard">
                {!! csrf_field() !!}

                <!-- box-body -->
                    <div class="box-body">
                    @foreach($result as $row)
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Min Limit</label>
                            <div class="col-sm-6">
                                <input type="text" name="min_limit" class="form-control" value="{{ $row->min_limit }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Max Limit</label>
                            <div class="col-sm-6">
                                <input type="text" name="max_limit" class="form-control" value="{{ $row->max_limit }}">
                            </div>
                        </div>
                    @endforeach
                    </div>
                    <!-- /.box-body -->

                    <!-- box-footer -->
                    <div class="box-footer">
                        <button class="btn btn-primary btn-flat pull-right" type="submit">Submit</button>
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>

@endsection

@push('extra_body_scripts')

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
