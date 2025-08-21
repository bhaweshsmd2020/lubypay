<header class="main-header">
    <!-- Logo -->
    <div class="full-width">

    <a href="{{ route('dashboard') }}" class="logo" style="background-color: #000!important; border-radius: 0px 0px 0px 25px;">
        <span class="logo-mini"><b>{{$app_name_short}}</b></span>

        @if (!empty($company_logo))
            <img src="{{asset('public/images/logos/'.$company_logo)}}" alt="logo" width="180" height="59" class="company-logo" style="background-color: #fff;
             border-radius: 10px;padding: 5px;">
            <!--<img src="{{ url('public/frontend/images/logomain.png') }}" width="180" height="59" class="company-logo">-->
         @else
            <img src="{{ url('public/frontend/images/logomain.png') }}" width="180" height="59" class="company-logo" style="background-color: #fff;
             border-radius: 10px;padding: 5px;">
         @endif
    </a>
    </div>

    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="mobile-width">
            <a href="{{ route('dashboard') }}" class="mobile-logo">
                <span class="logo-lg" style="font-size: 13px;"><b>{{$app_name_long}}</b></span>
            </a>
        </div>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                {{-- @include('admin.layouts.partials.nav_language') --}}
                @include('admin.layouts.partials.nav_notifications_menu')
            </ul>
            <ul class="nav navbar-nav">
                {{-- @include('admin.layouts.partials.nav_language') --}}
                @include('admin.layouts.partials.nav_user-menu')
            </ul>
            
        </div
       
    </nav>
</header>


