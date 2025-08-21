@extends('admin.layouts.master')
@section('title', 'Notification Templates')

@section('head_style')
  <!-- wysihtml5 -->
  <link rel="stylesheet" type="text/css" href="{{  asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
@endsection


@section('page_content')
    <div class="row">
      <div class="col-md-3">
         @include('admin.common.notification_menu')
      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
                @if($tempId == 1)
                    Deposit Template
                @elseif($tempId == 2)
                    Payout Template
                @elseif($tempId == 3)
                    Send Money Template
                @elseif($tempId == 4)
                    Request Money Receiver Template
                @elseif($tempId == 5)
                    Exchange Money Template
                @elseif($tempId == 6)
                    Gift Card Template
                @elseif($tempId == 7)
                    Topup Template
                @elseif($tempId == 12)
                    Approve Request Money Sender Template
                @elseif($tempId == 8)
                    Approve Request Money Receiver Template
                @elseif($tempId == 13)
                    Reject Request Money Sender Template
                @elseif($tempId == 9)
                    Reject Request Money Receiver Template
                @elseif($tempId == 10)
                    Money Received Template
                @elseif($tempId == 11)
                    Request Money Sender Template
                @elseif($tempId == 14)
                    QR Store Payment Template
                @elseif($tempId == 15)
                    New Store Template
                @elseif($tempId == 16)
                    New Product Template
                @elseif($tempId == 17)
                    New Payment Template
                @elseif($tempId == 18)
                    Photo Verification Template
                @elseif($tempId == 19)
                    Address Verification Template
                @elseif($tempId == 20)
                    Identity Verification Template
                @elseif($tempId == 21)
                    Payout Request Template
                @elseif($tempId == 22)
                    Ticket Reply Template
                @elseif($tempId == 23)
                    Manual KYC Template
                @elseif($tempId == 24)
                    Auto KYC Template
                @elseif($tempId == 25)
                    Create Ticket Template
                @elseif($tempId == 33)
                    Clear Device Template
                @elseif($tempId == 34)
                    Clear Device Template
                @elseif($tempId == 35)
                    Video Verification Template
                @elseif($tempId == 36)
                    Card Subscription Template
                @elseif($tempId == 37)
                    Card Subscription Expiry Reminder Template
                @elseif($tempId == 31)
                    Card Subscription Renew Template
                @elseif($tempId == 26)
                    Card Request Template
                @elseif($tempId == 27)
                    Card Reload Template
                @elseif($tempId == 38)
                    Card Status Template
                @elseif($tempId == 39)
                    Card Status Template
                @elseif($tempId == 40)
                    Approve KYC Template
                @elseif($tempId == 41)
                    Card Approve Template
                @elseif($tempId == 42)
                    ACH Transfer Request Template
                @elseif($tempId == 43)
                    ACH Transfer Complete Template
                @endif
            </h3>
          </div>

        <form action='{{url('admin/notification/template_update/'.$tempId)}}' method="post" id="template">
            {!! csrf_field() !!}

            <!-- /.box-header -->
            <div class="box-body">
                <div class="box-group" id="accordion">
                    @foreach($languages as $language)
                        <div class="panel box box-primary">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$language->id}}" aria-expanded="false" class="collapsed">
                                    {{$language->name}}
                                  </a>
                                </h4>
                            </div>
                            
                            @foreach($temp_Data as $temp)
                                @if($language->id == $temp->language_id)
                                    <div id="collapse{{$language->id}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input class="form-control" name="{{$language->short_name}}[title]" type="text" value="{{$temp->title}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Sub Header</label>
                                                <input class="form-control" name="{{$language->short_name}}[subheader]" type="text" value="{{$temp->subheader}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Content</label>
                                                <input class="form-control" name="{{$language->short_name}}[content]" type="text" value="{{$temp->content}}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="pull-right">
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_email_template'))
                      <button type="submit" class="btn btn-primary btn-flat" id="email_edit">
                          <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="email_edit_text">Update</span>
                      </button>
                    @endif
                </div>
            </div>
        </form>
          <!-- /.box-footer -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<!-- wysihtml5 -->
<script src="{{ asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>

<script>
    $(function () {
      $(".editor").wysihtml5();
    });

    $('#template').validate({
        rules: {
            subject: {
                required: true
            },
            content:{
               required: true
            }
        },
        submitHandler: function(form)
        {
            $("#email_edit").attr("disabled", true);
            $(".fa-spin").show();
            $("#email_edit_text").text('Updating...');
            form.submit();
        }
    });
</script>

@endpush