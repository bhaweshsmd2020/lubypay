@extends('user_dashboard.layouts.app')

@section('title', 'Virtual Card')

@section('content')

<?php
  $virtual_card_limit = DB::table('virtual_card_limit')->orderBy('created_at', 'asc')->first();
?>
                  
<style>
    /* CREDIT CARD IMAGE STYLING */
    .preload * {
        -webkit-transition: none !important;
        -moz-transition: none !important;
        -ms-transition: none !important;
        -o-transition: none !important;
    }
    
    #ccsingle {
        position: absolute;
        right: 15px;
        top: 20px;
    }
    
    #ccsingle svg {
        width: 100px;
        max-height: 60px;
    }
    
    .creditcard svg#cardfront,
    .creditcard svg#cardback {
        width: 100%;
        -webkit-box-shadow: 1px 5px 6px 0px black;
        box-shadow: 1px 5px 6px 0px black;
        border-radius: 22px;
    }
    
    #generatecard{
        cursor: pointer;
        float: right;
        font-size: 12px;
        color: #fff;
        padding: 2px 4px;
        background-color: #909090;
        border-radius: 4px;
        cursor: pointer;
        float:right;
    }
    
    /* CHANGEABLE CARD ELEMENTS */
    .creditcard .lightcolor,
    .creditcard .darkcolor {
        -webkit-transition: fill .5s;
        transition: fill .5s;
    }
    
    .creditcard .lightblue {
        fill: #03A9F4;
    }
    
    .creditcard .lightbluedark {
        fill: #0288D1;
    }
    
    .creditcard .red {
        fill: #ef5350;
    }
    
    .creditcard .reddark {
        fill: #d32f2f;
    }
    
    .creditcard .purple {
        fill: #ab47bc;
    }
    
    .creditcard .purpledark {
        fill: #7b1fa2;
    }
    
    .creditcard .cyan {
        fill: #26c6da;
    }
    
    .creditcard .cyandark {
        fill: #0097a7;
    }
    
    .creditcard .green {
        fill: #66bb6a;
    }
    
    .creditcard .greendark {
        fill: #388e3c;
    }
    
    .creditcard .lime {
        fill: #d4e157;
    }
    
    .creditcard .limedark {
        fill: #afb42b;
    }
    
    .creditcard .yellow {
        fill: #ffeb3b;
    }
    
    .creditcard .yellowdark {
        fill: #f9a825;
    }
    
    .creditcard .orange {
        fill: #ff9800;
    }
    
    .creditcard .orangedark {
        fill: #ef6c00;
    }
    
    .creditcard .grey {
        fill: #800000!important;
    }
    
    .creditcard .greydark {
        fill: #9e9e9e;
    }
    
    /* FRONT OF CARD */
    #svgname {
        text-transform: uppercase;
    }
    
    #cardfront .st2 {
        fill: #FFFFFF;
    }
    
    #cardfront .st3 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 600;
    }
    
    #cardfront .st4 {
        font-size: 54.7817px;
    }
    
    #cardfront .st5 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 400;
    }
    
    #cardfront .st6 {
        font-size: 33.1112px;
    }
    
    #cardfront .st7 {
        opacity: 0.6;
        fill: #FFFFFF;
    }
    
    #cardfront .st8 {
        font-size: 24px;
    }
    
    #cardfront .st9 {
        font-size: 36.5498px;
    }
    
    #cardfront .st10 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 300;
    }
    
    #cardfront .st11 {
        font-size: 16.1716px;
    }
    
    #cardfront .st12 {
        fill: #4C4C4C;
    }
    
    /* BACK OF CARD */
    #cardback .st0 {
        fill: none;
        stroke: #0F0F0F;
        stroke-miterlimit: 10;
    }
    
    #cardback .st2 {
        fill: #111111;
    }
    
    #cardback .st3 {
        fill: #F2F2F2;
    }
    
    #cardback .st4 {
        fill: #D8D2DB;
    }
    
    #cardback .st5 {
        fill: #C4C4C4;
    }
    
    #cardback .st6 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 400;
    }
    
    #cardback .st7 {
        font-size: 27px;
    }
    
    #cardback .st8 {
        opacity: 0.6;
    }
    
    #cardback .st9 {
        fill: #FFFFFF;
    }
    
    #cardback .st10 {
        font-size: 24px;
    }
    
    #cardback .st11 {
        fill: #EAEAEA;
    }
    
    #cardback .st12 {
        font-family: 'Rock Salt', cursive;
    }
    
    #cardback .st13 {
        font-size: 37.769px;
    }
    
    /* FLIP ANIMATION */
    .container {
        perspective: 1000px;
    }
    
    .creditcard {
        width: 100%;
        max-width: 400px;
        -webkit-transform-style: preserve-3d;
        transform-style: preserve-3d;
        transition: -webkit-transform 0.6s;
        -webkit-transition: -webkit-transform 0.6s;
        transition: transform 0.6s;
        transition: transform 0.6s, -webkit-transform 0.6s;
        cursor: pointer;
    }
    
    .creditcard .front,
    .creditcard .back {
        position: absolute;
        width: 100%;
        max-width: 400px;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        color: #47525d;
    }
    
    .creditcard .back {
        -webkit-transform: rotateY(180deg);
        transform: rotateY(180deg);
    }
    
    .creditcard.flipped {
        -webkit-transform: rotateY(180deg);
        transform: rotateY(180deg);
    }
</style>

<section class="section-06 history padding-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12 col-sm-12 mb20 marginTopPlus">
                <div class="flash-container">
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="float-left trans-inline">Virtual Card</h4>
                    </div>
                    <div style="margin: 15px 15px 15px 10px; height: 250px !important;">
                        <div class="row">
                            @if(count($virtualCardsList) > 0)
                                @foreach($virtualCardsList as $cardDetails)
                                    <div class="col-lg-4">
                                        <div class="container preload">
                                            <div class="creditcard">
                                                <div class="front">
                                                    <div id="ccsingle"></div>
                                                    <svg version="1.1" id="cardfront" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        x="0px" y="0px" viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;" xml:space="preserve">
                                                        <g id="Front">
                                                            <g id="CardBackground">
                                                                <g id="Page-1_1_">
                                                                    <g id="amex_1_">
                                                                        <path id="Rectangle-1_1_" class="lightcolor grey" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                                                                C0,17.9,17.9,0,40,0z" />
                                                                    </g>
                                                                </g>
                                                                <path class="darkcolor greydark" d="M750,431V193.2c-217.6-57.5-556.4-13.5-750,24.9V431c0,22.1,17.9,40,40,40h670C732.1,471,750,453.1,750,431z" />
                                                            </g>
                                                            <text transform="matrix(1 0 0 1 60.106 295.0121)" id="svgnumber" class="st2 st3 st4">XXXX XXXX XXXX {{$cardDetails->last_four_digit}}</text>
                                                            <text transform="matrix(1 0 0 1 54.1064 428.1723)" id="svgname" class="st2 st5 st6">{{$cardDetails->memo}}</text>
                                                            <text transform="matrix(1 0 0 1 54.1074 389.8793)" class="st7 st5 st8">cardholder name</text>
                                                            <text transform="matrix(1 0 0 1 479.7754 388.8793)" class="st7 st5 st8">expiration</text>
                                                            <text transform="matrix(1 0 0 1 65.1054 241.5)" class="st7 st5 st8">card number</text>
                                                            <g>
                                                                <text transform="matrix(1 0 0 1 574.4219 433.8095)" id="svgexpire" class="st2 st5 st9">{{$cardDetails->exp_month}}/{{$cardDetails->exp_year}}</text>
                                                                <text transform="matrix(1 0 0 1 479.3848 417.0097)" class="st2 st10 st11">VALID</text>
                                                                <text transform="matrix(1 0 0 1 479.3848 435.6762)" class="st2 st10 st11">THRU</text>
                                                                <polygon class="st2" points="554.5,421 540.4,414.2 540.4,427.9 		" />
                                                            </g>
                                                            <g id="cchip">
                                                                <g>
                                                                    <path class="st2" d="M168.1,143.6H82.9c-10.2,0-18.5-8.3-18.5-18.5V74.9c0-10.2,8.3-18.5,18.5-18.5h85.3
                                                            c10.2,0,18.5,8.3,18.5,18.5v50.2C186.6,135.3,178.3,143.6,168.1,143.6z" />
                                                                </g>
                                                                <g>
                                                                    <g>
                                                                        <rect x="82" y="70" class="st12" width="1.5" height="60" />
                                                                    </g>
                                                                    <g>
                                                                        <rect x="167.4" y="70" class="st12" width="1.5" height="60" />
                                                                    </g>
                                                                    <g>
                                                                        <path class="st12" d="M125.5,130.8c-10.2,0-18.5-8.3-18.5-18.5c0-4.6,1.7-8.9,4.7-12.3c-3-3.4-4.7-7.7-4.7-12.3
                                                                c0-10.2,8.3-18.5,18.5-18.5s18.5,8.3,18.5,18.5c0,4.6-1.7,8.9-4.7,12.3c3,3.4,4.7,7.7,4.7,12.3
                                                                C143.9,122.5,135.7,130.8,125.5,130.8z M125.5,70.8c-9.3,0-16.9,7.6-16.9,16.9c0,4.4,1.7,8.6,4.8,11.8l0.5,0.5l-0.5,0.5
                                                                c-3.1,3.2-4.8,7.4-4.8,11.8c0,9.3,7.6,16.9,16.9,16.9s16.9-7.6,16.9-16.9c0-4.4-1.7-8.6-4.8-11.8l-0.5-0.5l0.5-0.5
                                                                c3.1-3.2,4.8-7.4,4.8-11.8C142.4,78.4,134.8,70.8,125.5,70.8z" />
                                                                    </g>
                                                                    <g>
                                                                        <rect x="82.8" y="82.1" class="st12" width="25.8" height="1.5" />
                                                                    </g>
                                                                    <g>
                                                                        <rect x="82.8" y="117.9" class="st12" width="26.1" height="1.5" />
                                                                    </g>
                                                                    <g>
                                                                        <rect x="142.4" y="82.1" class="st12" width="25.8" height="1.5" />
                                                                    </g>
                                                                    <g>
                                                                        <rect x="142" y="117.9" class="st12" width="26.2" height="1.5" />
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </g>
                                                        <g id="Back">
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="back">
                                                    <svg version="1.1" id="cardback" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        x="0px" y="0px" viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;" xml:space="preserve">
                                                        <g id="Front">
                                                            <line class="st0" x1="35.3" y1="10.4" x2="36.7" y2="11" />
                                                        </g>
                                                        <g id="Back">
                                                            <g id="Page-1_2_">
                                                                <g id="amex_2_">
                                                                    <path id="Rectangle-1_2_" class="darkcolor greydark" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                                                            C0,17.9,17.9,0,40,0z" />
                                                                </g>
                                                            </g>
                                                            <rect y="61.6" class="st2" width="750" height="78" />
                                                            <g>
                                                                <path class="st3" d="M701.1,249.1H48.9c-3.3,0-6-2.7-6-6v-52.5c0-3.3,2.7-6,6-6h652.1c3.3,0,6,2.7,6,6v52.5
                                                        C707.1,246.4,704.4,249.1,701.1,249.1z" />
                                                                <rect x="42.9" y="198.6" class="st4" width="664.1" height="10.5" />
                                                                <rect x="42.9" y="224.5" class="st4" width="664.1" height="10.5" />
                                                                <path class="st5" d="M701.1,184.6H618h-8h-10v64.5h10h8h83.1c3.3,0,6-2.7,6-6v-52.5C707.1,187.3,704.4,184.6,701.1,184.6z" />
                                                            </g>
                                                            <text transform="matrix(1 0 0 1 621.999 227.2734)" id="svgsecurity" class="st6 st7">985</text>
                                                            <g class="st8">
                                                                <text transform="matrix(1 0 0 1 518.083 280.0879)" class="st9 st6 st10">security code</text>
                                                            </g>
                                                            <rect x="58.1" y="378.6" class="st11" width="375.5" height="13.5" />
                                                            <rect x="58.1" y="405.6" class="st11" width="421.7" height="13.5" />
                                                            <text transform="matrix(1 0 0 1 59.5073 228.6099)" id="svgnameback" class="st12 st13">John Doe</text>
                                                        </g>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                         @if($cardDetails->card_state == 'OPEN' && !empty($cardDetails->cvv) && !empty($cardDetails->exp_year) && !empty($cardDetails->exp_month) && !empty($cardDetails->pan)) 
                                         <span class="badge badge-pill badge-success" style="margin: .25rem 1.5rem;">{{'Active'}}</span>
                                         @elseif($cardDetails->card_state == 'CLOSED')
                                         <span class="badge badge-pill badge-danger" style="margin: .25rem 1.5rem;">{{'CLOSED'}}</span>
                                         @else
                                         <span class="badge badge-pill badge-danger" style="margin: .25rem 1.5rem;">{{'Inactive'}}</span>
                                         @endif 
                                        
                                        <a data-toggle="modal" data-target="#modal-more{{$cardDetails->id}}" href="" class="dropdown-item"><i class="fa fa-id-card" aria-hidden="true"></i> Card Details</a>
                                        <a href="{{url('user/virtualtransactions')}}/{{$cardDetails->token}}" class="dropdown-item"><i class="fa fa-exchange"></i> Transactions</a>
                                        @if($cardDetails->card_state != 'CLOSED')
                                        <a data-toggle="modal" data-target="#updatecard-model{{$cardDetails->id}}" href="" class="dropdown-item"><i class="fa fa-credit-card"></i> Update Card</a>
                                        @endif
                                        @if($cardDetails->card_state == 'PAUSED')
                                        <a data-toggle="modal" data-target="#opencard-model{{$cardDetails->id}}" href="" class="dropdown-item"><i class="fa fa-pause-circle"></i> Unpause</a>
                                        @endif
                                        @if($cardDetails->card_state == 'OPEN')
                                        <a data-toggle="modal" data-target="#pausecard-model{{$cardDetails->id}}" href="" class="dropdown-item"><i class="fa fa-pause-circle"></i> Pause</a>
                                        @endif
                                         @if($cardDetails->card_state != 'CLOSED')
                                        <a data-toggle="modal" data-target="#closecard-model{{$cardDetails->id}}" href="" class="dropdown-item"><i class="fa fa-trash"></i> Close</a>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <a data-toggle="modal" data-target="#modal-formx" href="" class="btn btn-success btn-flat" style="margin-left: 15px;"><span class="fa fa-plus"> &nbsp;</span>Create Card</a>
                            @endif
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal-formx" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
      <div class="modal-dialog modal- modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card bg-white border-0 mb-0">
              <div class="card-header">
                <h3 class="mb-0 font-weight-bolder">New Virtual Card</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
                <p class="form-text text-xs">Card creation charge is USD 0.80 Maximum cash a card can hold upto USD {{ $virtual_card_limit->max_limit }}.</p>
              </div>
              <div class="card-body">
                <form method="post" action="{{route('user.create_new')}}">
                    @csrf
                  <!--div class="form-group row">
                    <label class="col-form-label col-lg-12">Amount</label>
                    <div class="col-lg-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" name="amount" class="form-control" min="3000" max="10000" required="">
                        </div>
                    </div>
                  </div-->
                  <div class="form-group row">
                    <label class="col-form-label col-lg-12">Nice Name</label>
                    <div class="col-lg-12">
                      <input type="text" name="name_on_card" class="form-control" placeholder="Name on Card" required>
                    </div>
                  </div> 
                   
                  <div class="form-group row">
                    <label class="col-form-label col-lg-12">Spend Limit</label>
                    <div class="col-lg-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                          <input type="number" name="card_limit" class="form-control" min="{{ $virtual_card_limit->min_limit }}" max="{{ $virtual_card_limit->max_limit }}" placeholder="Card extend limit e.i 100" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
    
                        </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-12">Spend Type</label>
                    <div class="col-lg-12">
                      <select class="form-control" name="spend_limit_duration" required>
                          <option value="">Select Limit Duration</option>
                         
                         <option value="MONTHLY">Monthly</option>
                         <option value="ANNUALLY">Annually</option>
                         <option value="FOREVER">Forever</option>
                         <option value="TRANSACTION">Per Transaction</option>
                        </select>  
                    </div>
                  </div>
                  <div class="form-group row">
                    
                    <input type="radio" class="form-control-input" style="-webkit-appearance:auto!important;margin-left:16px" name="card_type" value="SINGLE_USE"><label class="col-form-label col-lg-12" style="margin-top:-32px;margin-left:20px">Single Use (Closes shortly after first use)</label>
                    <!--div class="col-lg-12">
                      <select class="form-control" name="card_type" required>
                          <option value="">Select Card Type</option>
                         <option value="SINGLE_USE">Single Use</option>
                         
                        </select>  
                    </div-->
                  </div>
                  <!--div class="form-group row">
                    <label class="col-form-label col-lg-12">Zip code</label>
                    <div class="col-lg-12">
                      <input type="number" name="zip_code" class="form-control" required="">
                    </div>
                  </div-->                 
                  <div class="text-right">
                    <button type="submit" class="btn btn-neutral btn-block my-4">Create Card</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> 
    
    @if(count($virtualCardsList) > 0)
        @foreach($virtualCardsList as $cardDetails)
            <div class="modal fade" id="modal-more{{$cardDetails->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                <div class="modal-dialog modal- modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <div class="card bg-white border-0 mb-0">
                                <div class="card-header">
                                    <h3 class="mb-0 font-weight-bolder">
                                        {{$cardDetails->memo}} Card Details
                                         @if($cardDetails->card_state == 'OPEN' && !empty($cardDetails->cvv) && !empty($cardDetails->exp_year) && !empty($cardDetails->exp_month) && !empty($cardDetails->pan)) 
                                         <span class="badge badge-pill badge-success">{{'Active'}}</span>
                                         @elseif($cardDetails->card_state == 'CLOSED')
                                         <span class="badge badge-pill badge-danger">{{'CLOSED'}}</span>
                                         @else
                                         <span class="badge badge-pill badge-danger">{{'Inactive'}}</span>
                                         @endif 
                                    </h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <!--<div class="container preload mt-4" style="width: 250px;">-->
                                <!--    <div class="creditcard">-->
                                <!--        <div class="front">-->
                                <!--            <div id="ccsingle"></div>-->
                                <!--            <svg version="1.1" id="cardfront" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"-->
                                <!--                x="0px" y="0px" viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;" xml:space="preserve">-->
                                <!--                <g id="Front">-->
                                <!--                    <g id="CardBackground">-->
                                <!--                        <g id="Page-1_1_">-->
                                <!--                            <g id="amex_1_">-->
                                <!--                                <path id="Rectangle-1_1_" class="lightcolor grey" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40-->
                                <!--                        C0,17.9,17.9,0,40,0z" />-->
                                <!--                            </g>-->
                                <!--                        </g>-->
                                <!--                        <path class="darkcolor greydark" d="M750,431V193.2c-217.6-57.5-556.4-13.5-750,24.9V431c0,22.1,17.9,40,40,40h670C732.1,471,750,453.1,750,431z" />-->
                                <!--                    </g>-->
                                <!--                    <text transform="matrix(1 0 0 1 60.106 295.0121)" id="svgnumber" class="st2 st3 st4">XXXX XXXX XXXX {{$cardDetails->last_four_digit}}</text>-->
                                <!--                    <text transform="matrix(1 0 0 1 54.1064 428.1723)" id="svgname" class="st2 st5 st6">{{$cardDetails->memo}}</text>-->
                                <!--                    <text transform="matrix(1 0 0 1 54.1074 389.8793)" class="st7 st5 st8">cardholder name</text>-->
                                <!--                    <text transform="matrix(1 0 0 1 479.7754 388.8793)" class="st7 st5 st8">expiration</text>-->
                                <!--                    <text transform="matrix(1 0 0 1 65.1054 241.5)" class="st7 st5 st8">card number</text>-->
                                <!--                    <g>-->
                                <!--                        <text transform="matrix(1 0 0 1 574.4219 433.8095)" id="svgexpire" class="st2 st5 st9">{{$cardDetails->exp_month}}/{{$cardDetails->exp_year}}</text>-->
                                <!--                        <text transform="matrix(1 0 0 1 479.3848 417.0097)" class="st2 st10 st11">VALID</text>-->
                                <!--                        <text transform="matrix(1 0 0 1 479.3848 435.6762)" class="st2 st10 st11">THRU</text>-->
                                <!--                        <polygon class="st2" points="554.5,421 540.4,414.2 540.4,427.9 		" />-->
                                <!--                    </g>-->
                                <!--                    <g id="cchip">-->
                                <!--                        <g>-->
                                <!--                            <path class="st2" d="M168.1,143.6H82.9c-10.2,0-18.5-8.3-18.5-18.5V74.9c0-10.2,8.3-18.5,18.5-18.5h85.3-->
                                <!--                    c10.2,0,18.5,8.3,18.5,18.5v50.2C186.6,135.3,178.3,143.6,168.1,143.6z" />-->
                                <!--                        </g>-->
                                <!--                        <g>-->
                                <!--                            <g>-->
                                <!--                                <rect x="82" y="70" class="st12" width="1.5" height="60" />-->
                                <!--                            </g>-->
                                <!--                            <g>-->
                                <!--                                <rect x="167.4" y="70" class="st12" width="1.5" height="60" />-->
                                <!--                            </g>-->
                                <!--                            <g>-->
                                <!--                                <path class="st12" d="M125.5,130.8c-10.2,0-18.5-8.3-18.5-18.5c0-4.6,1.7-8.9,4.7-12.3c-3-3.4-4.7-7.7-4.7-12.3-->
                                <!--                        c0-10.2,8.3-18.5,18.5-18.5s18.5,8.3,18.5,18.5c0,4.6-1.7,8.9-4.7,12.3c3,3.4,4.7,7.7,4.7,12.3-->
                                <!--                        C143.9,122.5,135.7,130.8,125.5,130.8z M125.5,70.8c-9.3,0-16.9,7.6-16.9,16.9c0,4.4,1.7,8.6,4.8,11.8l0.5,0.5l-0.5,0.5-->
                                <!--                        c-3.1,3.2-4.8,7.4-4.8,11.8c0,9.3,7.6,16.9,16.9,16.9s16.9-7.6,16.9-16.9c0-4.4-1.7-8.6-4.8-11.8l-0.5-0.5l0.5-0.5-->
                                <!--                        c3.1-3.2,4.8-7.4,4.8-11.8C142.4,78.4,134.8,70.8,125.5,70.8z" />-->
                                <!--                            </g>-->
                                <!--                            <g>-->
                                <!--                                <rect x="82.8" y="82.1" class="st12" width="25.8" height="1.5" />-->
                                <!--                            </g>-->
                                <!--                            <g>-->
                                <!--                                <rect x="82.8" y="117.9" class="st12" width="26.1" height="1.5" />-->
                                <!--                            </g>-->
                                <!--                            <g>-->
                                <!--                                <rect x="142.4" y="82.1" class="st12" width="25.8" height="1.5" />-->
                                <!--                            </g>-->
                                <!--                            <g>-->
                                <!--                                <rect x="142" y="117.9" class="st12" width="26.2" height="1.5" />-->
                                <!--                            </g>-->
                                <!--                        </g>-->
                                <!--                    </g>-->
                                <!--                </g>-->
                                <!--                <g id="Back">-->
                                <!--                </g>-->
                                <!--            </svg>-->
                                <!--        </div>-->
                                <!--        <div class="back">-->
                                <!--            <svg version="1.1" id="cardback" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"-->
                                <!--                x="0px" y="0px" viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;" xml:space="preserve">-->
                                <!--                <g id="Front">-->
                                <!--                    <line class="st0" x1="35.3" y1="10.4" x2="36.7" y2="11" />-->
                                <!--                </g>-->
                                <!--                <g id="Back">-->
                                <!--                    <g id="Page-1_2_">-->
                                <!--                        <g id="amex_2_">-->
                                <!--                            <path id="Rectangle-1_2_" class="darkcolor greydark" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40-->
                                <!--                    C0,17.9,17.9,0,40,0z" />-->
                                <!--                        </g>-->
                                <!--                    </g>-->
                                <!--                    <rect y="61.6" class="st2" width="750" height="78" />-->
                                <!--                    <g>-->
                                <!--                        <path class="st3" d="M701.1,249.1H48.9c-3.3,0-6-2.7-6-6v-52.5c0-3.3,2.7-6,6-6h652.1c3.3,0,6,2.7,6,6v52.5-->
                                <!--                C707.1,246.4,704.4,249.1,701.1,249.1z" />-->
                                <!--                        <rect x="42.9" y="198.6" class="st4" width="664.1" height="10.5" />-->
                                <!--                        <rect x="42.9" y="224.5" class="st4" width="664.1" height="10.5" />-->
                                <!--                        <path class="st5" d="M701.1,184.6H618h-8h-10v64.5h10h8h83.1c3.3,0,6-2.7,6-6v-52.5C707.1,187.3,704.4,184.6,701.1,184.6z" />-->
                                <!--                    </g>-->
                                <!--                    <text transform="matrix(1 0 0 1 621.999 227.2734)" id="svgsecurity" class="st6 st7">985</text>-->
                                <!--                    <g class="st8">-->
                                <!--                        <text transform="matrix(1 0 0 1 518.083 280.0879)" class="st9 st6 st10">security code</text>-->
                                <!--                    </g>-->
                                <!--                    <rect x="58.1" y="378.6" class="st11" width="375.5" height="13.5" />-->
                                <!--                    <rect x="58.1" y="405.6" class="st11" width="421.7" height="13.5" />-->
                                <!--                    <text transform="matrix(1 0 0 1 59.5073 228.6099)" id="svgnameback" class="st12 st13">John Doe</text>-->
                                <!--                </g>-->
                                <!--            </svg>-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->
                                
                                <div class="modal-body">
                                  <div class="form-group">
                                    <label for="email1">Nick Name</label>
                                    <input type="text" class="form-control" value="{{$cardDetails->memo}}" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label for="password1">Card Number</label>
                                    <input type="text" class="form-control" value="{{$cardDetails->pan}}" readonly>
                                  </div>
                                  <label for="password1">Virtual Card Limit</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="text" class="form-control" value="{{$cardDetails->spend_limit}}" readonly>
                                  </div>
                                </div>
                              
                                <div class="row" style="width:60%;margin:20px auto">
                                    <div class="col-md-6">
                                        @if($cardDetails->card_state == 'PAUSED')
                                        <a data-toggle="modal" data-target="#opencard-model{{$cardDetails->id}}" href="" class="dropdown-item" style="color: grey;"><i class="fa fa-pause-circle"></i>&nbsp;<strong>Unpause</strong></a>
                                        @endif
                                        @if($cardDetails->card_state == 'OPEN')
                                        <a data-toggle="modal" data-target="#pausecard-model{{$cardDetails->id}}" href="" class="dropdown-item" style="color: grey;"><i class="fa fa-pause-circle"></i>&nbsp;<strong>Pause</strong></a>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($cardDetails->card_state != 'CLOSED')
                                        <a data-toggle="modal" data-target="#closecard-model{{$cardDetails->id}}" href="" class="dropdown-item" style="color: grey;"><i class="fa fa-trash"></i>&nbsp;<strong>Close</strong></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
      
        <div class="modal fade" id="limitexceeed-formx" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal- modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body p-0">
                <div class="card bg-white border-0 mb-0">
                  <div class="card-header">
                    <h3 class="mb-0 font-weight-bolder">Limit Exceeded</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <h3>Please upgrade to business!</h3>
                  </div>
                  <div class="card-body">
                    <form method="post" action="{{route('user.open_virtual_card')}}">
                        @csrf
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Amount</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="amount" class="form-control" min="3000" max="10000" required="">
                            </div>
                        </div>
                      </div-->
                    
                        <input type="hidden" name="card_token" value="{{$cardDetails->token}}">    
                      
                       
                      <br>
                                    
                      
                    </form>
                    <div class="text-center">
                        <a href="{{url('user/upgrade')}}"  class="btn btn-success">Upgrade Now</a>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          
        <div class="modal fade" id="opencard-model{{$cardDetails->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal- modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body p-0">
                <div class="card bg-white border-0 mb-0">
                  <div class="card-header">
                    <h3 class="mb-0 font-weight-bolder">Unpause Your Card</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <h3>Are you sure do you want to unpause it?</h3>
                  </div>
                  <div class="card-body">
                    <form method="post" action="{{route('user.open_virtual_card')}}">
                        @csrf
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Amount</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="amount" class="form-control" min="3000" max="10000" required="">
                            </div>
                        </div>
                      </div-->
                    
                        <input type="hidden" name="card_token" value="{{$cardDetails->token}}">    
                      
                       
                      <br>
                                    
                      <div class="text-center">
                        <button type="submit" class="btn btn-success">Unpause Now</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          
        <div class="modal fade" id="closecard-model{{$cardDetails->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal- modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body p-0">
                <div class="card bg-white border-0 mb-0">
                  <div class="card-header">
                    <h3 class="mb-0 font-weight-bolder">Close Your Card</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <h3>Are you sure do you want to close it?</h3>
                  </div>
                  <div class="card-body">
                    <form method="post" action="{{route('user.close_virtual_card')}}">
                        @csrf
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Amount</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="amount" class="form-control" min="3000" max="10000" required="">
                            </div>
                        </div>
                      </div-->
                    
                        <input type="hidden" name="card_token" value="{{$cardDetails->token}}">    
                      
                       
                      <br>
                                    
                      <div class="text-center">
                        <button type="submit" class="btn btn-success">Close Now</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          
        <div class="modal fade" id="pausecard-model{{$cardDetails->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal- modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body p-0">
                <div class="card bg-white border-0 mb-0">
                  <div class="card-header">
                    <h3 class="mb-0 font-weight-bolder">Pause Virtual Card</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <h3>Are you sure do you want to pause it?</h3>
                  </div>
                  <div class="card-body">
                    <form method="post" action="{{route('user.pause_virtual_card')}}">
                        @csrf
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Amount</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="amount" class="form-control" min="3000" max="10000" required="">
                            </div>
                        </div>
                      </div-->
                    
                        <input type="hidden" name="card_token" value="{{$cardDetails->token}}">    
                      
                       
                      <br>
                                    
                      <div class="text-center">
                        <button type="submit" class="btn btn-success">Pause Now</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          
        <div class="modal fade" id="updatecard-model{{$cardDetails->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal- modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body p-0">
                <div class="card bg-white border-0 mb-0">
                  <div class="card-header">
                    <h3 class="mb-0 font-weight-bolder">Update Virtual Card</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <p class="form-text text-xs">Card creation charge is 5.7% of amount entitled to card. Maximum cash a card can hold is USD10,000.</p>
                  </div>
                  <div class="card-body">
                    <form method="post" action="{{route('user.update_virtual_card')}}">
                        @csrf
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Amount</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="amount" class="form-control" min="3000" max="10000" required="">
                            </div>
                        </div>
                      </div-->
                      <div class="form-group row">
                        <label class="col-form-label col-lg-12">Nice Name (Name on Card)</label>
                        <div class="col-lg-12">
                        <input type="hidden" name="card_token" value="{{$cardDetails->token}}">    
                          <input type="text" name="name_on_card" class="form-control" placeholder="Name on Card" value="{{$cardDetails->memo}}" required>
                        </div>
                      </div> 
                       
                      <div class="form-group row">
                        <label class="col-form-label col-lg-12">Monthly Limit</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                              <input type="text" name="card_limit" class="form-control" min="10" max="5000" value="{{$cardDetails->spend_limit}}" placeholder="Card extend limit e.i 100" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
    
                            </div>
                        </div>
                      </div>
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Spend Limit Duration</label>
                        <div class="col-lg-12">
                          <select class="form-control" name="spend_limit_duration" required>
                              <option value="">Select Limit Duration</option>
                             <option value="TRANSACTION" @if($cardDetails->spend_limit_duration == 'TRANSACTION'){{'Selected'}}@endif>TRANSACTION</option>
                             <option value="MONTHLY" @if($cardDetails->spend_limit_duration == 'MONTHLY'){{'Selected'}}@endif>MONTHLY</option>
                             <option value="ANNUALLY" @if($cardDetails->spend_limit_duration == 'ANNUALLY'){{'Selected'}}@endif>ANNUALLY</option>
                             <option value="FOREVER" @if($cardDetails->spend_limit_duration == 'FOREVER'){{'Selected'}}@endif>FOREVER</option>
                            </select>  
                        </div>
                      </div-->
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Card Type</label>
                        <div class="col-lg-12">
                          <select class="form-control" name="card_type" required>
                              <option value="">Select Card Type</option>
                             <option value="SINGLE_USE" @if($cardDetails->type == 'SINGLE_USE'){{'Selected'}}@endif>SINGLE USE</option>
                             
                            </select>  
                        </div>
                      </div-->
                      <!--div class="form-group row">
                        <label class="col-form-label col-lg-12">Zip code</label>
                        <div class="col-lg-12">
                          <input type="number" name="zip_code" class="form-control" required="">
                        </div>
                      </div-->                 
                      <div class="text-right">
                        <button type="submit" class="btn btn-neutral btn-block my-4">Update Card</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    @endif
</section>

@endsection