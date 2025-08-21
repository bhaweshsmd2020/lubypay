
@extends('user_dashboard.layouts.app')
@section('content')
<section class="section-06 history padding-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12 mb20">
                @include('user_dashboard.layouts.common.alert')
                
                <div class="clearfix"></div>
                <div class="card">
                    <table class="table recent_activity" style="background-color:#FFFFFF;">
                            <thead>
                                <tr>
                                    <td class="text-left" width="50%"><strong>@lang('message.dashboard.ticket.ticket-no')</strong></td>
                                    <td class="text-right"><strong>Pay Cable Bill</strong></td>
                                    
                                </tr>
                            </thead>
                            
                        </table>
                    
                        <table class="table recent_activity">
                            <thead>
                                <tr>
                                    <td class="text-left" width="16%"><strong>@lang('message.dashboard.ticket.ticket-no')</strong></td>
                                    <td class="text-left"><strong>@lang('message.dashboard.ticket.subject')</strong></td>
                                    <td width="15%"><strong>@lang('message.dashboard.ticket.status')</strong></td>
                                    <td width="6%"><strong>@lang('message.dashboard.ticket.priority')</strong></td>
                                    <td width="15%"><strong>@lang('message.dashboard.ticket.date')</strong></td>
                                    <td width="6%"><strong>@lang('message.dashboard.ticket.action')</strong></td>
                                </tr>
                            </thead>
                            
                        </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
</script>
@endsection
