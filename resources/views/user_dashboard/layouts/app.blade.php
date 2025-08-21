<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = getCompanyLogoWithoutSession();
    $persona_kyc = DB::table('kycdatastores')->where('user_id', auth()->user()->id)->where('status', 'COMPLETED')->first();
    $manual_kyc = DB::table('users')->where('id', auth()->user()->id)->where('photo_verified', '1')->where('address_verified', '1')->where('identity_verified', '1')->first();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{!isset($exception) ? meta(Route::current()->uri(),'description'):$exception->description}}">
        <meta name="keywords" content="{{!isset($exception) ? meta(Route::current()->uri(),'keyword'):$exception->keyword}}">

        @hasSection('title')
            <title> @yield('title')</title>
        @else
            <title>{{!isset($exception) ? meta(Route::current()->uri(),'title'):$exception->title}} <?= isset($additionalTitle)?'| '.$additionalTitle :'' ?></title>
        @endif

        @include('user_dashboard.layouts.common.style')

        <link rel="javascript" href="{{asset('public/user_dashboard/js/respond.js')}}">

        @if (!empty(getfavicon()))
            <link rel="shortcut icon" href="{{asset('public/images/logos/'.getfavicon())}}" style="background-color:#fff;" />
        @endif

        <script type="text/javascript">
            var SITE_URL = "{{url('/')}}";
        </script>

        <style>
            .skin-blue .wrapper, .skin-blue .main-sidebar, .skin-blue .left-side {
                background-color: #fff !important;
            }
            .skin-blue .sidebar-menu>li:hover>a, .skin-blue .sidebar-menu>li.active>a, .skin-blue .sidebar-menu>li.menu-open>a {
                background-color: #000 !important;
                color: #fff !important;
                border-left: 3px solid #ecf0f500!important;
            }
            .sidebar-menu{    
                margin-top: -35px!important;
            }
            .text-success {
                color: #f7aa32!important;
            }
            .skin-blue .sidebar-menu>li>a {
                color: #000 !important;
            }
            .skin-blue .sidebar a {
                color:#ffffff!important;
            }
            .main-sidebar {
                background-color: #fff !important;
                top: 65px!important;
            }
            
            .sidebar-menu > li > a {
                padding: 12px 5px 12px 15px!important;
                display: block;
            }
            
            .btn-default {
                background-color: #675a5a00!important;
                border-color: #fff0!Important;
            }
            #image-dropdown {
              display: inline-block;
              border: 1px solid;
            }
            #image-dropdown {
              height: 30px;
              overflow: hidden;
            }
            /*#image-dropdown:hover {} */

            #image-dropdown .img_holder {
              cursor: pointer;
            }
            #image-dropdown img.flagimgs {
              height: 30px;
            }
            #image-dropdown span.iTEXT {
              position: relative;
              top: -8px;
            }
            .navbar.navbar-expand-lg.navbar-dark.bg-primary.toogleMenuDiv{
                padding:0 !important;
            }
        </style>

        <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/AdminLTE.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/skins/_all-skins.min.css') }}">
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div id="scroll-top-area">
            <a href="{{url()->current()}}#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
        </div>

        <div class="wrapper_custom">
            <header id="js-header-old">
                <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #000; height: 65px; border-radius: 0px 0px 25px 25px; padding-top: 2px;">
                    <div class="container-fluid">
                        @if (isset($logo))
                            <a href="{{url('/')}}">
                                <img src="{{ url('public/frontend/images/logomain.png') }}" alt="logo" class="img-responsive" style="background-color: #fff; border-radius: 10px; padding: 5px; width: 116px; height: 50px; margin-left: 30px;">
                            </a>
                        @else
                            <a href="{{url('/')}}">
                                <img src="{{ url('public/frontend/images/logomain.png') }}" alt="logo" class="img-responsive" style="background-color: #fff; border-radius: 10px; padding: 5px; width: 116px; height: 50px; margin-left: 30px;">
                            </a>
                        @endif
            
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="collapse navbar-collapse navbar-toggler-right" id="navbarSupportedContent">
                            <ul class="navbar-nav mr-auto d-lg-none">
                                <li class="nav-item"><a href="{{url('/dashboard')}}" class="nav-link">@lang('message.dashboard.nav-menu.dashboard')</a></li>
            
                                @if(Common::has_permission(auth()->id(),'manage_transaction'))
                                    <li class="nav-item"><a href="{{url('/transactions')}}" class="nav-link">@lang('message.dashboard.nav-menu.transactions')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_deposit'))
                                    <li class="nav-item"><a href="{{url('/deposit')}}" class="nav-link">@lang('message.dashboard.button.deposit')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_transfer'))
                                    <li class="nav-item"><a href="{{url('/moneytransfer')}}" class="nav-link">@lang('message.dashboard.nav-menu.send-req')</a></li>
                                @elseif(Common::has_permission(auth()->id(),'manage_request_payment'))
                                    <li class="nav-item"><a href="{{url('/request_payment/add')}}" class="nav-link">@lang('message.dashboard.nav-menu.send-req')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_exchange'))
                                    <li class="nav-item"><a href="{{url('/exchange')}}" class="nav-link">@lang('message.dashboard.nav-menu.exchange')</a></li>
                                @endif
                                
                                @if(Common::has_permission(auth()->id(),'manage_deposit'))
                                    <li class="nav-item"><a href="{{url('/utility/cable')}}" class="nav-link">Cable</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_merchant'))
                                    <li class="nav-item"><a href="{{url('/merchants')}}" class="nav-link">@lang('message.dashboard.nav-menu.merchants')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_withdrawal'))
                                    <li class="nav-item"><a href="{{url('/payouts')}}" class="nav-link">@lang('message.dashboard.nav-menu.payout')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_dispute'))
                                    <li class="nav-item"><a href="{{url('/disputes')}}" class="nav-link">@lang('message.dashboard.nav-menu.disputes')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_ticket'))
                                    <li class="nav-item"><a href="{{url('/tickets')}}" class="nav-link">@lang('message.dashboard.nav-menu.tickets')</a></li>
                                @endif
            
                                @if(Common::has_permission(auth()->id(),'manage_setting'))
                                    <li class="nav-item"><a href="{{url('/profile')}}" class="nav-link">@lang('message.dashboard.nav-menu.settings')</a></li>
                                @endif
                                <li class="nav-item"><a href="{{url('/logout')}}" class="nav-link">@lang('message.dashboard.nav-menu.logout')</a></li>
                            </ul>
            
                            <!--@if(auth()->user()->type == 'merchant')-->
                            <!--    <a class="btn btn-cust col-md-2 pull-right" style="    border-radius: 35px;margin: unset;margin-left:auto; margin-right:5px ;">-->
                            <!--        {{auth()->user()->package}}-->
                            <!--    </a>-->
                            <!--@endif-->
                        </div>
            
                        <div class="d-none d-lg-block">
                            <div class="row">
                                <div class="col-md-3" style="padding-top: 10px">
                                    @if(Auth::user()->picture)
                                        <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                                    @else
                                        <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                                    @endif
                                </div>
            
                                @php
                                    $fullName = strlen($user->first_name.' '.$user->last_name) > 20 ? substr($user->first_name.' '.$user->last_name,0,20)."..." : $user->first_name.' '.$user->last_name; //change in pm_v2.1
                                @endphp
                                
                                <div class="col-md-9 username text-left">
                                    <a href="#" class="dropdown-toggle text-light" data-toggle="dropdown"><span id="subStringUserName" title="{{$user->first_name.' '.$user->last_name}}">{{$fullName}}-<b>{{$user->carib_id}}</b></span></a>
            
                                    <ul class="dropdown-menu" style="color:#545b62;min-width: 155px;">
                                        @if(Common::has_permission(auth()->id(),'manage_setting'))
                                            <li class="" style="padding: 0px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">
                                                <i class="fa fa-cog"></i><a style="line-height: 0;color:#7d95b6" href="{{url('/profile')}}" class="btn btn-default btn-flat">@lang('message.dashboard.nav-menu.settings')</a>
                                            </li>
                                            
                                            @if(empty($persona_kyc) && !empty($manual_kyc))
                                                <!--<li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">-->
                                                <!--    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="#" class="btn btn-default btn-flat">KYC Updated</a>-->
                                                <!--</li>-->
                                            @elseif(!empty($persona_kyc) && empty($manual_kyc))
                                                <!--<li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">-->
                                                <!--    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="#" class="btn btn-default btn-flat">KYC Updated</a>-->
                                                <!--</li>-->
                                            @elseif(!empty($persona_kyc) && !empty($manual_kyc))
                                                <!--<li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">-->
                                                <!--    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="#" class="btn btn-default btn-flat">KYC Updated</a>-->
                                                <!--</li>-->
                                            @else
                                                <li class="" style="padding: 5px 14px 5px 14px; text-align: left; border-bottom: 1px solid #dae1e9">
                                                    <i class="fa fa-address-card-o"></i><a style="line-height: 0;color:#7d95b6" href="{{url('/kyc')}}" class="btn btn-default btn-flat">KYC</a>
                                                </li>
                                            @endif
                                            
                                        @endif
                                        <li class="" style="padding: 5px 14px 5px 14px; text-align: left;">
                                            <i class="fa fa-sign-out"></i><a style="line-height: 0;color:#7d95b6" href="{{url('/logout')}}" class="btn btn-default btn-flat">@lang('message.dashboard.nav-menu.logout')</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            
            <aside class="main-sidebar" style="background-color: #fff !important;">
                <section class="sidebar">
                   @include('user_dashboard.layouts.common.header')
                </section>
            </aside>

            <div class="content-wrapper">
                <section class="content">
                    @yield('content')
                </section>
            </div>

            <footer class="main-footer">
                @include('admin.layouts.partials.footer')
            </footer>
            <div class="control-sidebar-bg"></div>
        </div>

        @include('user_dashboard.layouts.common.script')
        @yield('js')
    </body>
</html>


