@extends('admin.layouts.master')
@section('title', 'Email Templates')

@section('head_style')
  <!-- Quill Editor CSS -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <style>
    .quill-editor {
        background: white;
        min-height: 300px;
    }
  </style>
@endsection

@section('page_content')
<div class="row">
  <div class="col-md-3">
     @include('admin.common.mail_menu')
  </div>
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          @switch($tempId)
              @case(1) Compose Transferred Template @break
              @case(2) Compose Received Template @break
              @case(3) Compose Bank Transfer Template @break
              @case(21) Compose Identity/Address Verification Template @break
              @case(19) Compose 2-Factor Authentication Template @break
              @case(4) Compose Request Creation Template @break
              @case(5) Compose Request Acceptance Template @break
              @case(6) Compose Transfer Status Change Template @break
              @case(7) Compose Bank Transfer Status Change Template @break
              @case(8) Compose Request Payment Status Change Template @break
              @case(10) Compose Payout Status Change Template @break
              @case(11) Compose Ticket Template @break
              @case(12) Compose Ticket Reply Template @break
              @case(16) Compose Request Payment Status Change Template @break
              @case(17) Compose User Verification Template @break
              @case(18) Compose Password Reset Template @break
              @case(13) Compose Dispute Reply Template @break
              @case(14) Compose Merchant Payment Status Change Template @break
              @case(29) Compose User Status Change Template @break
              @case(23) Compose Deposit Notification Template @break
              @case(24) Compose Payout Notification Template @break
              @case(25) Compose Exchange Notification Template @break
              @case(26) Compose Transfer Notification Template @break
              @case(27) Compose Request Acceptance Notification Template @break
              @case(28) Compose Payment Notification Template @break
              @case(67) Maintenance Break Template @break
              @case(66) Card Subscription Renew Template @break
              @case(68) Card Subscription Expiry Reminder Template @break
              @case(69) Card Subscription Template @break
              @case(70) Card Status Template @break
              @case(71) Card Status Template @break
              @case(72) Card Subscription Renew Template @break
              @case(73) Card Subscription Upgrade Template @break
              @case(74) Card Subscription Upgrade Template @break
              @case(75) ACH Transfer Request Template @break
              @case(76) ACH Transfer Request Template @break
              @case(77) ACH Transfer Complete Template @break
              @case(78) ACH Transfer Complete Template @break
          @endswitch
        </h3>
      </div>

      <form action='{{ url("admin/template_update/$tempId") }}' method="post" id="template">
        {!! csrf_field() !!}

        <div class="box-body">
            <div class="box-group" id="accordion">
                @foreach($languages as $language)
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                              <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $language->id }}" aria-expanded="false" class="collapsed">
                                {{ $language->name }}
                              </a>
                            </h4>
                        </div>

                        @foreach($temp_Data as $temp)
                            @if($language->id == $temp->language_id)
                                <div id="collapse{{ $language->id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <input class="form-control" name="{{ $language->short_name }}[subject]" type="text" value="{{ $temp->subject }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Body</label>
                                            <div id="editor-{{ $language->short_name }}" class="quill-editor">{!! $temp->body !!}</div>
                                            <input type="hidden" name="{{ $language->short_name }}[body]" id="input-{{ $language->short_name }}">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

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
    </div>
  </div>
</div>
@endsection

@push('extra_body_scripts')
<!-- QuillJS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    $(document).ready(function () {
        const editors = {};

        @foreach($languages as $language)
            let quillEditor{{ $language->id }} = new Quill("#editor-{{ $language->short_name }}", {
                theme: 'snow',
                placeholder: 'Compose your email content here...',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        ['link', 'blockquote', 'code-block'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['clean']
                    ]
                }
            });

            editors['{{ $language->short_name }}'] = quillEditor{{ $language->id }};
        @endforeach

        $('#template').on('submit', function () {
            Object.keys(editors).forEach(function (lang) {
                let html = editors[lang].root.innerHTML;
                $('#input-' + lang).val(html);
            });
        });
    });
</script>
@endpush
