<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = getCompanyLogoWithoutSession();
    $persona_kyc = DB::table('kycdatastores')->where('user_id', auth()->user()->id)->where('status', 'COMPLETED')->first();
    $manual_kyc = DB::table('users')->where('id', auth()->user()->id)->where('photo_verified', '1')->where('address_verified', '1')->where('identity_verified', '1')->first();
?>

<ul class="sidebar-menu">
    <li class="<?= isset($menu) && ($menu == 'dashboard') ? 'active' : '' ?>">
        <a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i>@lang('message.dashboard.nav-menu.dashboard')</a>
    </li>
    @if(Common::has_permission(auth()->id(),'manage_merchant'))
        <!--<li class="<?= isset($menu) && ($menu == 'merchant') ? 'active' : '' ?>">-->
        <!--    <a href="{{url('/merchants')}}"><i class="fa fa-user"></i>@lang('message.dashboard.nav-menu.merchants')</a>-->
        <!--</li>-->
    @endif
    
    @if(auth()->user()->type == 'merchant')                    
        <li class="<?= isset($menu) && ($menu == 'my_store') ? 'active' : '' ?>">                        
             <a href="{{url('/mystore')}}" class="nav-link"> <i class="fa fa-spinner"></i> My Store </a>                    
        </li> 
    @endif
    
    @if(Common::has_permission(auth()->id(),'manage_transaction'))
        <li class="<?= isset($menu) && ($menu == 'transactions') ? 'active' : '' ?>">
            <a href="{{url('/transactions')}}"><i class="fa fa-file"></i>@lang('message.dashboard.nav-menu.transactions')</a>
        </li>
    @endif
        
    @if((empty($persona_kyc) && !empty($manual_kyc)) || (!empty($persona_kyc) && empty($manual_kyc)) || (!empty($persona_kyc) && !empty($manual_kyc)))
        @if(auth()->user()->role_id == 2)   
            <li class="<?= isset($menu) && ($menu == 'deposit') ? 'active' : '' ?>">
                @if(Common::has_permission(auth()->id(),'manage_deposit'))
                    <a href="{{url('deposit')}}"><i class="fa fa-money"></i>@lang('message.dashboard.button.deposit')</a>
                @endif
            </li>
        @endif
    
        <li class="<?= isset($menu) && ($menu == 'payouts') ? 'active' : '' ?>">
            @if(Common::has_permission(auth()->id(),'manage_withdrawal'))
                <a href="{{url('payouts')}}"><i class="fa fa-usd"></i>&nbsp;@lang('message.dashboard.button.payout')</a>
            @endif
        </li>
        
        @if(auth()->user()->role_id == 2)  
            <li class="<?= isset($menu) && ($menu == 'exchange') ? 'active' : '' ?>">
                @if(Common::has_permission(auth()->id(),'manage_exchange'))
                    <a href="{{url('exchange')}}"><i class="fa fa-exchange"></i> @lang('message.dashboard.button.exchange')</a>
                @endif
            </li>
          
            <li class="<?= isset($menu) && ($menu == 'payouts') ? 'active' : '' ?>">
                @if(Common::has_permission(auth()->id(),'manage_withdrawal'))
                    <a href="{{url('giftcard')}}"><i class="fa fa-usd"></i>Gift Card</a>
                @endif
            </li>
        
            @if(Common::has_permission(auth()->id(),'manage_deposit'))
                <li>
                    <a href="{{ url('/ding-connect') }}"><i class="fa fa-mobile"></i><span>Mobile Reload</span></a>
                </li>
            @endif
            
            @if(Common::has_permission(auth()->id(),'manage_transfer'))
                <li class="<?= isset($menu) && ($menu == 'send_receive') ? 'active' : '' ?>">
                    <a href="{{url('/moneytransfer')}}"><i class="fa fa-share-square"></i>@lang('message.dashboard.nav-menu.send-req')</a>
                </li>
            @elseif(Common::has_permission(auth()->id(),'manage_request_payment'))
                <li class="<?= isset($menu) && ($menu == 'request_payment') ? 'active' : '' ?>">
                    <a href="{{url('/request_payment/add')}}"><i class="fa fa-exchange"></i>@lang('message.dashboard.nav-menu.send-req')</a>
                </li>
            @endif
            
            @if(Common::has_permission(auth()->id(),'manage_dispute'))
                <li class="<?= isset($menu) && ($menu == 'dispute') ? 'active' : '' ?>">
                    <a href="{{url('/disputes')}}"><i class="fa fa-ticket"></i>@lang('message.dashboard.nav-menu.disputes')</a>
                </li>
            @endif
        @endif
        
        @if(Common::has_permission(auth()->id(),'manage_ticket'))
            <li class="<?= isset($menu) && ($menu == 'ticket') ? 'active' : '' ?>">
                <a href="{{url('/tickets')}}"><i class="fa fa-question-circle"></i>@lang('message.dashboard.nav-menu.tickets')</a>
            </li>
        @endif
    @endif
</ul>