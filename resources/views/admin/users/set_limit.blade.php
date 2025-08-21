@extends('admin.layouts.master')

@section('title', 'Set Limit')

@section('page_content') 

  
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          
          <h3 class="box-title label label-warning">Add Limit<small style="color: #fff;">(Without KYC)</small></h3>
        </div>
        <div class="box-body">
            <form action="{{ url('admin/store-kyc-limit') }}" method="post">
              <input type="hidden" name="without_kyc" value="0">
              <input type="hidden" name="type" value="Daily">
              <input type="hidden" name="month" id="max_month" value="{{$is_kyc_month->daily_limit}}">
                 {{ csrf_field() }}
            <div class="row">    
               <div class="col-md-5">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Daily Limit</label>
                      <div class="col-sm-6">
                        <input type="number" name="daily_limit" class="form-control" id="enter_daily" placeholder="Daily Limit..." >
                       <small class="daily_error"  style="color:red;">Daily limit should be less than monthly limit!</small>
                      </div>
                      
                    </div>
                    
               </div>
                <div class="col-md-5">
                   <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Select Currency</label>
                      <div class="col-sm-6">
                        <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">
                            @foreach($currency as $cur)
                            <option value="{{$cur->id}}">{{$cur->code}}</option>
                            @endforeach
                        </select>                   
                      </div>
                    </div> 
               </div>
              <div class="col-md-2">
                 <button type="submit" class="btn btn-primary submit">&nbsp; Save &nbsp;</button>
                 <b  class="btn btn-primary daily">&nbsp; View &nbsp;</b>
              </div>
           </div>
           </form>
          <script>
          $(document).ready(function (){
              $('.daily_error').hide();
              var month = $('#max_month').val();
              $('#enter_daily').on('keyup',function(){
                  var enter_daily = $('#enter_daily').val();
                  if(enter_daily > month)
                  {
                      $('.daily_error').show();
                     $('.submit').hide();
                  }else
                  {
                      $('.submit').show();
                        $('.daily_error').hide();
                      console.log('good');
                  }
              });
             // alert(month);
          });
          </script>
           <div class="row dailyshow">    
               <div class="col-md-3"></div>
                 <div class="col-md-6">
                   <center><strong>Daily Limit</strong></center>
                      <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">
                        <thead class="text-left">
                            <th>Currency</th>
                            <th>Value</th>
                           </thead>  
                        <tbody id="myTable">  
                        @foreach($is_kyc as $daily)
                        @if($daily->type == 'Daily')
                         <tr>  
                            <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  
                            <td>{{$daily->daily_limit}}</td> 
                          </tr> 
                          @else
                          @endif
                          @endforeach
                        </tbody>  
                      </table> 
                 </div>
              <div class="col-md-3"></div>
           </div>
            <script>
            $(document).ready(function(){
                $('.dailyshow').hide();
              $(".daily").click(function(){
                $(".dailyshow").slideToggle();
              });
            });
            </script>
            
            <!--Monthly Limit-->
            <form action="{{ url('admin/store-kyc-limit') }}" method="post">
              <input type="hidden" name="without_kyc" value="0">
              <input type="hidden" name="type" value="Monthly">
              <input type="hidden" name="month" id="max_daily" value="{{$is_kyc_daily->daily_limit}}">
              {{ csrf_field() }}
            <div class="row">    
               <div class="col-md-5">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Monthly Limit</label>
                      <div class="col-sm-6">
                        <input type="number" name="daily_limit"  id="enter_month" class="form-control" placeholder="Monthly Limit..." >
                         <small class="monthly_error"  style="color:red;">Monthly limit should be greather than daily limit!</small>
                      </div>
                    </div>
               </div>
                <div class="col-md-5">
                   <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Select Currency</label>
                      <div class="col-sm-6">
                        <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">
                            @foreach($currency as $cur)
                            <option value="{{$cur->id}}" >{{$cur->code}}</option>
                            @endforeach
                        </select>                   
                      </div>
                    </div> 
               </div>
              <div class="col-md-2">
                 <button type="submit" class="btn btn-primary subrr">&nbsp; Save &nbsp;</button>
                 <b  class="btn btn-primary month">&nbsp; View &nbsp;</b>
              </div>
           </div>
           </form>
           <script>
          $(document).ready(function (){
              $('.monthly_error').hide();
              var max_daily = $('#max_daily').val();
               //alert(max_daily);
              $('#enter_month').keyup(function(){
                  var enter_month = $('#enter_month').val();
                  // alert(enter_month);
                  if(parseInt(enter_month) < parseInt(max_daily))
                  {
                      $('.subrr').hide();
                      $('.monthly_error').show();
                      //$('#enter_month').val('');
                  }else
                  {
                        $('.subrr').show();
                        $('.monthly_error').hide();
                      console.log('good');
                  }
              });
            //alert(max_daily);
          });
          </script>
           <div class="row monthshow">    
               <div class="col-md-3"></div>
                 <div class="col-md-6">
                   <center><strong>Monthly Limit</strong></center>
                      <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">
                        <thead class="text-left">
                            <th>Currency</th>
                            <th>Value</th>
                           </thead>  
                        <tbody id="myTable">  
                        @foreach($is_kyc as $daily)
                        @if($daily->type == 'Monthly')
                         <tr>  
                            <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  
                            <td>{{$daily->daily_limit}}</td> 
                          </tr> 
                          @else
                          @endif
                          @endforeach
                        </tbody>  
                      </table> 
                 </div>
              <div class="col-md-3"></div>
           </div>
            <script>
            $(document).ready(function(){
                $('.monthshow').hide();
              $(".month").click(function(){
                $(".monthshow").slideToggle();
              });
            });
            </script>
            
             <!--Max Add Fund-->
    <!--        <form action="{{ url('admin/store-kyc-limit') }}" method="post">-->
    <!--          <input type="hidden" name="without_kyc" value="0">-->
    <!--          <input type="hidden" name="type" value="AddFund">-->
    <!--             {{ csrf_field() }}-->
    <!--        <div class="row">    -->
    <!--           <div class="col-md-5">-->
    <!--                <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Add Fund</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <input type="text" name="daily_limit" class="form-control" value="" placeholder="Add Fund..." >-->
                       
    <!--                  </div>-->
    <!--                </div>-->
    <!--           </div>-->
    <!--            <div class="col-md-5">-->
    <!--               <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
    <!--                        @foreach($currency as $cur)-->
    <!--                        <option value="{{$cur->id}}">{{$cur->code}}</option>-->
    <!--                        @endforeach-->
    <!--                    </select>                   -->
    <!--                  </div>-->
    <!--                </div> -->
    <!--           </div>-->
    <!--          <div class="col-md-2">-->
    <!--             <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
    <!--             <b  class="btn btn-primary fund">&nbsp; View &nbsp;</b>-->
    <!--          </div>-->
    <!--       </div>-->
    <!--       </form>-->
    <!--       <div class="row fundhow">    -->
    <!--           <div class="col-md-3"></div>-->
    <!--             <div class="col-md-6">-->
    <!--               <center><strong>Max Add Fund</strong></center>-->
    <!--                  <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
    <!--                    <thead class="text-left">-->
    <!--                        <th>Currency</th>-->
    <!--                        <th>Value</th>-->
    <!--                       </thead>  -->
    <!--                     <tbody id="myTable">  -->
    <!--                    @foreach($is_kyc as $daily)-->
    <!--                    @if($daily->type == 'AddFund')-->
    <!--                     <tr>  -->
    <!--                        <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
    <!--                        <td>{{$daily->daily_limit}}</td> -->
    <!--                      </tr> -->
    <!--                      @else-->
    <!--                      @endif-->
    <!--                      @endforeach-->
    <!--                    </tbody> -->
    <!--                  </table> -->
    <!--             </div>-->
    <!--          <div class="col-md-3"></div>-->
    <!--       </div>-->
    <!--        <script>-->
    <!--        $(document).ready(function(){-->
    <!--            $('.fundhow').hide();-->
    <!--          $(".fund").click(function(){-->
    <!--            $(".fundhow").slideToggle();-->
    <!--          });-->
    <!--        });-->
    <!--        </script>-->
           
           <!--Per Month Transaction-->
    <!--      <form action="{{ url('admin/store-kyc-limit') }}" method="post">-->
    <!--          <input type="hidden" name="without_kyc" value="0">-->
    <!--          <input type="hidden" name="type" value="Pmonthtrans">-->
    <!--             {{ csrf_field() }}-->
    <!--        <div class="row">    -->
    <!--           <div class="col-md-5">-->
    <!--                <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Per Month Trans</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <input type="text" name="daily_limit" class="form-control" value="" placeholder="Per Month Trans..." >-->
                       
    <!--                  </div>-->
    <!--                </div>-->
    <!--           </div>-->
    <!--            <div class="col-md-5">-->
    <!--               <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
    <!--                        @foreach($currency as $cur)-->
    <!--                        <option value="{{$cur->id}}">{{$cur->code}}</option>-->
    <!--                        @endforeach-->
    <!--                    </select>                   -->
    <!--                  </div>-->
    <!--                </div> -->
    <!--           </div>-->
    <!--          <div class="col-md-2">-->
    <!--             <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
    <!--             <b  class="btn btn-primary pmonth">&nbsp; View &nbsp;</b>-->
    <!--          </div>-->
    <!--       </div>-->
    <!--       </form>-->
    <!--       <div class="row permonth">    -->
    <!--           <div class="col-md-3"></div>-->
    <!--             <div class="col-md-6">-->
    <!--               <center><strong>Per Month Trans</strong></center>-->
    <!--                  <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
    <!--                    <thead class="text-left">-->
    <!--                        <th>Currency</th>-->
    <!--                        <th>Value</th>-->
    <!--                       </thead>  -->
    <!--                     <tbody id="myTable">  -->
    <!--                    @foreach($is_kyc as $daily)-->
    <!--                    @if($daily->type == 'Pmonthtrans')-->
    <!--                     <tr>  -->
    <!--                        <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
    <!--                        <td>{{$daily->daily_limit}}</td> -->
    <!--                      </tr> -->
    <!--                      @else-->
    <!--                      @endif-->
    <!--                      @endforeach-->
    <!--                    </tbody> -->
    <!--                  </table> -->
    <!--             </div>-->
    <!--          <div class="col-md-3"></div>-->
    <!--       </div>-->
    <!--        <script>-->
    <!--        $(document).ready(function(){-->
    <!--            $('.permonth').hide();-->
    <!--          $(".pmonth").click(function(){-->
    <!--            $(".permonth").slideToggle();-->
    <!--          });-->
    <!--        });-->
    <!--        </script>-->
           
            <!--Local Transaction-->
    <!--      <form action="{{ url('admin/store-kyc-limit') }}" method="post">-->
    <!--          <input type="hidden" name="without_kyc" value="0">-->
    <!--          <input type="hidden" name="type" value="Localtrans">-->
    <!--             {{ csrf_field() }}-->
    <!--        <div class="row">    -->
    <!--           <div class="col-md-5">-->
    <!--                <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Local Trans</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <input type="text" name="daily_limit" class="form-control" value="" placeholder="Local Trans..." >-->
                       
    <!--                  </div>-->
    <!--                </div>-->
    <!--           </div>-->
    <!--            <div class="col-md-5">-->
    <!--               <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
    <!--                        @foreach($currency as $cur)-->
    <!--                        <option value="{{$cur->id}}">{{$cur->code}}</option>-->
    <!--                        @endforeach-->
    <!--                    </select>                   -->
    <!--                  </div>-->
    <!--                </div> -->
    <!--           </div>-->
    <!--          <div class="col-md-2">-->
    <!--             <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
    <!--             <b  class="btn btn-primary local">&nbsp; View &nbsp;</b>-->
    <!--          </div>-->
    <!--       </div>-->
    <!--       </form>-->
    <!--       <div class="row localtrans">    -->
    <!--           <div class="col-md-3"></div>-->
    <!--             <div class="col-md-6">-->
    <!--               <center><strong>Local Trans</strong></center>-->
    <!--                  <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
    <!--                    <thead class="text-left">-->
    <!--                        <th>Currency</th>-->
    <!--                        <th>Value</th>-->
    <!--                       </thead>  -->
    <!--                    <tbody id="myTable">  -->
    <!--                    @foreach($is_kyc as $daily)-->
    <!--                    @if($daily->type == 'Localtrans')-->
    <!--                     <tr>  -->
    <!--                        <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
    <!--                        <td>{{$daily->daily_limit}}</td> -->
    <!--                      </tr> -->
    <!--                      @else-->
    <!--                      @endif-->
    <!--                      @endforeach-->
    <!--                    </tbody>  -->
    <!--                  </table> -->
    <!--             </div>-->
    <!--          <div class="col-md-3"></div>-->
    <!--       </div>-->
    <!--        <script>-->
    <!--        $(document).ready(function(){-->
    <!--            $('.localtrans').hide();-->
    <!--          $(".local").click(function(){-->
    <!--            $(".localtrans").slideToggle();-->
    <!--          });-->
    <!--        });-->
    <!--        </script>-->
            
              <!--International Transaction-->
    <!--       <form action="{{ url('admin/store-kyc-limit') }}" method="post">-->
    <!--          <input type="hidden" name="without_kyc" value="0">-->
    <!--          <input type="hidden" name="type" value="Intertrans">-->
    <!--             {{ csrf_field() }}-->
    <!--        <div class="row">    -->
    <!--           <div class="col-md-5">-->
    <!--                <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">International Trans</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <input type="text" name="daily_limit" class="form-control" value="" placeholder="International Trans..." >-->
                       
    <!--                  </div>-->
    <!--                </div>-->
    <!--           </div>-->
    <!--            <div class="col-md-5">-->
    <!--               <div class="form-group">-->
    <!--                  <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
    <!--                  <div class="col-sm-6">-->
    <!--                    <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
    <!--                        @foreach($currency as $cur)-->
    <!--                        <option value="{{$cur->id}}">{{$cur->code}}</option>-->
    <!--                        @endforeach-->
    <!--                    </select>                   -->
    <!--                  </div>-->
    <!--                </div> -->
    <!--           </div>-->
    <!--          <div class="col-md-2">-->
    <!--             <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
    <!--             <b  class="btn btn-primary inter">&nbsp; View &nbsp;</b>-->
    <!--          </div>-->
    <!--       </div>-->
    <!--       </form>-->
    <!--       <div class="row intertrans">    -->
    <!--           <div class="col-md-3"></div>-->
    <!--             <div class="col-md-6">-->
    <!--               <center><strong>Local Trans</strong></center>-->
    <!--                  <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
    <!--                    <thead class="text-left">-->
    <!--                        <th>Currency</th>-->
    <!--                        <th>Value</th>-->
    <!--                       </thead>  -->
    <!--                    <tbody id="myTable">  -->
    <!--                    @foreach($is_kyc as $daily)-->
    <!--                    @if($daily->type == 'Intertrans')-->
    <!--                     <tr>  -->
    <!--                        <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
    <!--                        <td>{{$daily->daily_limit}}</td> -->
    <!--                      </tr> -->
    <!--                      @else-->
    <!--                      @endif-->
    <!--                      @endforeach-->
    <!--                    </tbody> -->
    <!--                  </table> -->
    <!--             </div>-->
    <!--          <div class="col-md-3"></div>-->
    <!--       </div>-->
    <!--        <script>-->
    <!--        $(document).ready(function(){-->
    <!--            $('.intertrans').hide();-->
    <!--          $(".inter").click(function(){-->
    <!--            $(".intertrans").slideToggle();-->
    <!--          });-->
    <!--        });-->
    <!--        </script>-->
           
    <!--      </div>-->

    <!--  </div>-->
    <!--</div>-->
     
     
     <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border text-center">
          <h3 class="box-title label label-warning">Add Limit<small style="color: #fff;">(With KYC)</small></h3></div>
        <div class="box-body">
         
            <form action="{{ url('admin/store-with-kyc-limit') }}" method="post">
              <input type="hidden" name="with_kyc" value="1">
              <input type="hidden" name="type" value="Daily">
              <input type="hidden" name="" id="kyc_month" value="{{$without_kyc_month->daily_limit}}">
                 {{ csrf_field() }}
            <div class="row">    
               <div class="col-md-5">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Daily Limit</label>
                      <div class="col-sm-6">
                        <input type="number" name="daily_limit" id="kyc_enter_daily" class="form-control" value="" placeholder="Daily Limit..." >
                        <small class="kyc_monthly_error"  style="color:red;">Daily limit should be less than monthly limit!</small>
                      </div>
                    </div>
               </div>
                <div class="col-md-5">
                   <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Select Currency</label>
                      <div class="col-sm-6">
                        <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">
                            @foreach($currency as $cur)
                            <option value="{{$cur->id}}">{{$cur->code}}</option>
                            @endforeach
                        </select>                   
                      </div>
                    </div> 
               </div>
              <div class="col-md-2">
                 <button type="submit" class="btn btn-primary subr">&nbsp; Save &nbsp;</button>
                 <b  class="btn btn-primary with_daily">&nbsp; View &nbsp;</b>
              </div>
           </div>
           </form>
           <script>
          $(document).ready(function (){
              $('.kyc_monthly_error').hide();
              var kyc_month = $('#kyc_month').val();
               //alert(max_daily);
              $('#kyc_enter_daily').keyup(function(){
                  var kyc_enter_daily = $('#kyc_enter_daily').val();
                  // alert(enter_month);
                  if(parseInt(kyc_enter_daily) > parseInt(kyc_month))
                  {
                      $('.subr').hide();
                      $('.kyc_monthly_error').show();
                      //$('#enter_month').val('');
                  }else
                  {
                      $('.subr').show();
                        $('.kyc_monthly_error').hide();
                      console.log('good');
                  }
              });
            //alert(max_daily);
          });
          </script>
           <div class="row with_dailyshow">    
               <div class="col-md-3"></div>
                 <div class="col-md-6">
                   <center><strong>Daily Limit</strong></center>
                      <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">
                        <thead class="text-left">
                            <th>Currency</th>
                            <th>Value</th>
                           </thead>  
                        <tbody id="myTable">  
                        @foreach($with_kyc as $daily)
                        @if($daily->type == 'Daily')
                         <tr>  
                            <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  
                            <td>{{$daily->daily_limit}}</td> 
                          </tr> 
                          @else
                          @endif
                          @endforeach
                        </tbody>  
                      </table> 
                 </div>
              <div class="col-md-3"></div>
           </div>
            <script>
            $(document).ready(function(){
                $('.with_dailyshow').hide();
              $(".with_daily").click(function(){
                $(".with_dailyshow").slideToggle();
              });
            });
            </script>
            
            <!--Monthly Limit-->
            <form action="{{ url('admin/store-with-kyc-limit') }}" method="post">
              <input type="hidden" name="with_kyc" value="1">
              <input type="hidden" name="type" value="Monthly">
              <input type="hidden" name="" id="kyc_daily" value="{{$without_kyc_daily->daily_limit}}">
              {{ csrf_field() }}
            <div class="row">    
               <div class="col-md-5">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Monthly Limit</label>
                      <div class="col-sm-6">
                        <input type="number" name="daily_limit" id="kyc_enter_month" class="form-control" value="" placeholder="Monthly Limit..." >
                        <small class="kyc_daily_error"  style="color:red;">Monthly limit should be greather than daily limit!</small>
                      </div>
                    </div>
               </div>
                <div class="col-md-5">
                   <div class="form-group">
                      <label class="col-sm-3 control-label" for="user_id">Select Currency</label>
                      <div class="col-sm-6">
                        <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">
                            @foreach($currency as $cur)
                            <option value="{{$cur->id}}" >{{$cur->code}}</option>
                            @endforeach
                        </select>                   
                      </div>
                    </div> 
               </div>
              <div class="col-md-2">
                 <button type="submit" class="btn btn-primary sub">&nbsp; Save &nbsp;</button>
                 <b  class="btn btn-primary with_month">&nbsp; View &nbsp;</b>
              </div>
           </div>
           </form>
           <script>
          $(document).ready(function (){
              $('.kyc_daily_error').hide();
              var kyc_daily = $('#kyc_daily').val();
               //alert(max_daily);
              $('#kyc_enter_month').keyup(function(){
                  var kyc_enter_month = $('#kyc_enter_month').val();
                  // alert(enter_month);
                  if(parseInt(kyc_enter_month) < parseInt(kyc_daily))
                  {
                      //alert(enter_month);
                      $('.kyc_daily_error').show();
                      $('.sub').hide();
                      //$('#enter_month').val('');
                  }else
                  {
                       $('.sub').show();
                        $('.kyc_daily_error').hide();
                      console.log('good');
                  }
              });
            //alert(max_daily);
          });
          </script>
           <div class="row with_monthshow">    
               <div class="col-md-3"></div>
                 <div class="col-md-6">
                   <center><strong>Monthly Limit</strong></center>
                      <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">
                        <thead class="text-left">
                            <th>Currency</th>
                            <th>Value</th>
                           </thead>  
                        <tbody id="myTable">  
                        @foreach($with_kyc as $daily)
                        @if($daily->type == 'Monthly')
                         <tr>  
                            <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  
                            <td>{{$daily->daily_limit}}</td> 
                          </tr> 
                          @else
                          @endif
                          @endforeach
                        </tbody>  
                      </table> 
                 </div>
              <div class="col-md-3"></div>
           </div>
            <script>
            $(document).ready(function(){
                $('.with_monthshow').hide();
              $(".with_month").click(function(){
                $(".with_monthshow").slideToggle();
              });
            });
            </script>
            
             <!--Max Add Fund-->
         <!--   <form action="{{ url('admin/store-with-kyc-limit') }}" method="post">-->
         <!--     <input type="hidden" name="with_kyc" value="1">-->
         <!--     <input type="hidden" name="type" value="AddFund">-->
         <!--        {{ csrf_field() }}-->
         <!--   <div class="row">    -->
         <!--      <div class="col-md-5">-->
         <!--           <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Add Fund</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <input type="text" name="daily_limit" class="form-control" value="" placeholder="Add Fund..." >-->
                       
         <!--             </div>-->
         <!--           </div>-->
         <!--      </div>-->
         <!--       <div class="col-md-5">-->
         <!--          <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
         <!--                   @foreach($currency as $cur)-->
         <!--                   <option value="{{$cur->id}}">{{$cur->code}}</option>-->
         <!--                   @endforeach-->
         <!--               </select>                   -->
         <!--             </div>-->
         <!--           </div> -->
         <!--      </div>-->
         <!--     <div class="col-md-2">-->
         <!--        <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
         <!--        <b  class="btn btn-primary with_fund">&nbsp; View &nbsp;</b>-->
         <!--     </div>-->
         <!--  </div>-->
         <!--  </form>-->
         <!--  <div class="row with_fundhow">    -->
         <!--      <div class="col-md-3"></div>-->
         <!--        <div class="col-md-6">-->
         <!--          <center><strong>Max Add Fund</strong></center>-->
         <!--             <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
         <!--               <thead class="text-left">-->
         <!--                   <th>Currency</th>-->
         <!--                   <th>Value</th>-->
         <!--                  </thead>  -->
         <!--                <tbody id="myTable">  -->
         <!--               @foreach($with_kyc as $daily)-->
         <!--               @if($daily->type == 'AddFund')-->
         <!--                <tr>  -->
         <!--                   <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
         <!--                   <td>{{$daily->daily_limit}}</td> -->
         <!--                 </tr> -->
         <!--                 @else-->
         <!--                 @endif-->
         <!--                 @endforeach-->
         <!--               </tbody> -->
         <!--             </table> -->
         <!--        </div>-->
         <!--     <div class="col-md-3"></div>-->
         <!--  </div>-->
         <!--   <script>-->
         <!--   $(document).ready(function(){-->
         <!--       $('.with_fundhow').hide();-->
         <!--     $(".with_fund").click(function(){-->
         <!--       $(".with_fundhow").slideToggle();-->
         <!--     });-->
         <!--   });-->
         <!--   </script>-->
           
           <!--Per Month Transaction-->
         <!-- <form action="{{ url('admin/store-with-kyc-limit') }}" method="post">-->
         <!--     <input type="hidden" name="with_kyc" value="1">-->
         <!--     <input type="hidden" name="type" value="Pmonthtrans">-->
         <!--        {{ csrf_field() }}-->
         <!--   <div class="row">    -->
         <!--      <div class="col-md-5">-->
         <!--           <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Per Month Trans</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <input type="text" name="daily_limit" class="form-control" value="" placeholder="Per Month Trans..." >-->
                       
         <!--             </div>-->
         <!--           </div>-->
         <!--      </div>-->
         <!--       <div class="col-md-5">-->
         <!--          <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
         <!--                   @foreach($currency as $cur)-->
         <!--                   <option value="{{$cur->id}}">{{$cur->code}}</option>-->
         <!--                   @endforeach-->
         <!--               </select>                   -->
         <!--             </div>-->
         <!--           </div> -->
         <!--      </div>-->
         <!--     <div class="col-md-2">-->
         <!--        <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
         <!--        <b  class="btn btn-primary with_pmonth">&nbsp; View &nbsp;</b>-->
         <!--     </div>-->
         <!--  </div>-->
         <!--  </form>-->
         <!--  <div class="row with_permonth">    -->
         <!--      <div class="col-md-3"></div>-->
         <!--        <div class="col-md-6">-->
         <!--          <center><strong>Per Month Trans</strong></center>-->
         <!--             <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
         <!--               <thead class="text-left">-->
         <!--                   <th>Currency</th>-->
         <!--                   <th>Value</th>-->
         <!--                  </thead>  -->
         <!--                <tbody id="myTable">  -->
         <!--               @foreach($with_kyc as $daily)-->
         <!--               @if($daily->type == 'Pmonthtrans')-->
         <!--                <tr>  -->
         <!--                   <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
         <!--                   <td>{{$daily->daily_limit}}</td> -->
         <!--                 </tr> -->
         <!--                 @else-->
         <!--                 @endif-->
         <!--                 @endforeach-->
         <!--               </tbody> -->
         <!--             </table> -->
         <!--        </div>-->
         <!--     <div class="col-md-3"></div>-->
         <!--  </div>-->
         <!--   <script>-->
         <!--   $(document).ready(function(){-->
         <!--       $('.with_permonth').hide();-->
         <!--     $(".with_pmonth").click(function(){-->
         <!--       $(".with_permonth").slideToggle();-->
         <!--     });-->
         <!--   });-->
         <!--   </script>-->
           
            <!--Local Transaction-->
         <!--<form action="{{ url('admin/store-with-kyc-limit') }}" method="post">-->
         <!--     <input type="hidden" name="with_kyc" value="1">-->
         <!--     <input type="hidden" name="type" value="Localtrans">-->
         <!--        {{ csrf_field() }}-->
         <!--   <div class="row">    -->
         <!--      <div class="col-md-5">-->
         <!--           <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Local Trans</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <input type="text" name="daily_limit" class="form-control" value="" placeholder="Local Trans..." >-->
                       
         <!--             </div>-->
         <!--           </div>-->
         <!--      </div>-->
         <!--       <div class="col-md-5">-->
         <!--          <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
         <!--                   @foreach($currency as $cur)-->
         <!--                   <option value="{{$cur->id}}">{{$cur->code}}</option>-->
         <!--                   @endforeach-->
         <!--               </select>                   -->
         <!--             </div>-->
         <!--           </div> -->
         <!--      </div>-->
         <!--     <div class="col-md-2">-->
         <!--        <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
         <!--        <b  class="btn btn-primary with_local">&nbsp; View &nbsp;</b>-->
         <!--     </div>-->
         <!--  </div>-->
         <!--  </form>-->
         <!--  <div class="row with_localtrans">    -->
         <!--      <div class="col-md-3"></div>-->
         <!--        <div class="col-md-6">-->
         <!--          <center><strong>Local Trans</strong></center>-->
         <!--             <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
         <!--               <thead class="text-left">-->
         <!--                   <th>Currency</th>-->
         <!--                   <th>Value</th>-->
         <!--                  </thead>  -->
         <!--               <tbody id="myTable">  -->
         <!--               @foreach($with_kyc as $daily)-->
         <!--               @if($daily->type == 'Localtrans')-->
         <!--                <tr>  -->
         <!--                   <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
         <!--                   <td>{{$daily->daily_limit}}</td> -->
         <!--                 </tr> -->
         <!--                 @else-->
         <!--                 @endif-->
         <!--                 @endforeach-->
         <!--               </tbody>  -->
         <!--             </table> -->
         <!--        </div>-->
         <!--     <div class="col-md-3"></div>-->
         <!--  </div>-->
         <!--   <script>-->
         <!--   $(document).ready(function(){-->
         <!--       $('.with_localtrans').hide();-->
         <!--     $(".with_local").click(function(){-->
         <!--       $(".with_localtrans").slideToggle();-->
         <!--     });-->
         <!--   });-->
         <!--   </script>-->
            
              <!--International Transaction-->
         <!-- <form action="{{ url('admin/store-with-kyc-limit') }}" method="post">-->
         <!--     <input type="hidden" name="with_kyc" value="1">-->
         <!--     <input type="hidden" name="type" value="Intertrans">-->
         <!--        {{ csrf_field() }}-->
         <!--   <div class="row">    -->
         <!--      <div class="col-md-5">-->
         <!--           <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">International Trans</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <input type="text" name="daily_limit" class="form-control" value="" placeholder="International Trans..." >-->
                       
         <!--             </div>-->
         <!--           </div>-->
         <!--      </div>-->
         <!--       <div class="col-md-5">-->
         <!--          <div class="form-group">-->
         <!--             <label class="col-sm-3 control-label" for="user_id">Select Currency</label>-->
         <!--             <div class="col-sm-6">-->
         <!--               <select class="form-control wallet valid" name="daily_limit_currency" id="currencies" aria-invalid="false">-->
         <!--                   @foreach($currency as $cur)-->
         <!--                   <option value="{{$cur->id}}">{{$cur->code}}</option>-->
         <!--                   @endforeach-->
         <!--               </select>                   -->
         <!--             </div>-->
         <!--           </div> -->
         <!--      </div>-->
         <!--     <div class="col-md-2">-->
         <!--        <button type="submit" class="btn btn-primary">&nbsp; Save &nbsp;</button>-->
         <!--        <b  class="btn btn-primary with_inter">&nbsp; View &nbsp;</b>-->
         <!--     </div>-->
         <!--  </div>-->
         <!--  </form>-->
         <!--  <div class="row with_intertrans">    -->
         <!--      <div class="col-md-3"></div>-->
         <!--        <div class="col-md-6">-->
         <!--          <center><strong>Local Trans</strong></center>-->
         <!--             <table style="border: 2px solid;border-radius: 18px!important;border-collapse: inherit!important;" class="display table">-->
         <!--               <thead class="text-left">-->
         <!--                   <th>Currency</th>-->
         <!--                   <th>Value</th>-->
         <!--                  </thead>  -->
         <!--               <tbody id="myTable">  -->
         <!--               @foreach($with_kyc as $daily)-->
         <!--               @if($daily->type == 'Intertrans')-->
         <!--                <tr>  -->
         <!--                   <td>{{App\Models\Currency::find($daily->daily_limit_currency)->code}}</td>  -->
         <!--                   <td>{{$daily->daily_limit}}</td> -->
         <!--                 </tr> -->
         <!--                 @else-->
         <!--                 @endif-->
         <!--                 @endforeach-->
         <!--               </tbody> -->
         <!--             </table> -->
         <!--        </div>-->
         <!--     <div class="col-md-3"></div>-->
         <!--  </div>-->
         <!--   <script>-->
         <!--   $(document).ready(function(){-->
         <!--       $('.with_intertrans').hide();-->
         <!--     $(".with_inter").click(function(){-->
         <!--       $(".with_intertrans").slideToggle();-->
         <!--     });-->
         <!--   });-->
         <!--   </script>-->
           
         <!-- </div>-->

      </div>
    </div>
     
   
@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<!-- jquery.validate additional-methods -->
<script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>


@endpush