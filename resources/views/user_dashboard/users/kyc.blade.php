@extends('user_dashboard.layouts.app')

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    
                    @include('user_dashboard.layouts.common.alert')

                    <div class="clearfix"></div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class=""> Account Verification </h4>
                            <div class="text-center">
                                <img src="{{ asset('public/images/kyc.jpg') }}" style="max-width: 300px; margin: 50px;"><br>
                                <a href="https://ticktappay.withpersona.com/verify?inquiry-template-id=itmpl_zqJtwdgxFxziYaq8EZvt5X8p&environment=sandbox&reference-id={{$user->carib_id}}&languagen-US=&fields[name-first]={{$user->first_name}}&redirect-uri={{route('user.kyc')}}">
                                    <button class="btn btn-primary">Auto Verification</button>
                                </a>
                                
                                <a href="{{url('/profile/personal-id')}}">
                                    <button class="btn btn-primary">Manual Verification</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
@endsection
