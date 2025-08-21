<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Ticktap Pay - Registration Successful</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.min.css'><link rel="stylesheet" href="./style.css">
  <style>
        html,
        body {
          font-family: sans-serif;
          height: 100%;
          width: 100%;
          background: rgba(200,200,200,0.2);
        }
        .container-720 {
          max-width: 720px;
          margin: auto;
          background: #fff;
        }
  </style>
</head>
<body style="padding-top: 50px;">
<!-- partial:index.partial.html -->
<div class="container-720">
  <div class="heading" style="background: #ab185a;display:flex; align-items: center;justify-content: space-between;margin-left:-20px;margin-right: -20px;box-shadow: 0 5px 10px -5px green;padding-left: 30px;padding-right:30px;">
    <h1 style="line-height: 50px;padding-left: 10px;color:white">Registration Successfull</h1>
    <img src="https://techfest.org/2018/logo-main.png" alt="" style="height: 50px;">
  </div>
  <main style="padding: 10px;">
    <p>Hello {{$user_detail->first_name}} {{$user_detail->last_name}},</p>
    <p>You have successfully registered as a <b>Merchant.</b> on our portal.</p>
    <p>Your merchant id is <strong>{{$user_detail->carib_id}}</strong></p>

    <p><i>
      Kindly note that your registration is not yet complete. <br>
      Login to our app and do KYC to enjoy our services. 
    </i></p>
    <p> To download our App, click on the buttons below. </p>
    <p>
        @foreach($app_store as $app)
            <a href="{{$app->link}}" target="_blank"> 
                <img src="{{ asset('public/uploads/app-store-logos/'.$app->logo) }}"> 
            </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
    </p>
    <p>Once the KYC is done, you can enjoy our merchant services.</p>
  </main>
  <footer style="padding:20px 10px 20px 10px;font-size: 0.9em;border-top:2px double #1f3f1f;">
      
        <?php
      		$company_name = getCompanyName();
      	?>
        <p class="copyright" style="text-align: center;">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp; {{ $company_name }}. &nbsp;All rights reserved. | Powered By &nbsp;<a href="https://quickewallet.com" target="_blank">Quickewallet</a></p>
        <p style="text-align: center;"> All brand names and logos are the property of their respective owners. The images and logos are used for identification purposes only, and do not imply product endorsement or affiliation with Luby Pay </p>
  </footer>
</div>
<!-- partial -->
  
</body>
</html>
