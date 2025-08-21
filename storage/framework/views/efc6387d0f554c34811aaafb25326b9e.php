<?php
/**
 * Created By: TechVillage.net
 * Start Date: 22-Jan-2018
 */
$logo = getCompanyLogoWithoutSession();
//dd($logo);
?>

<!DOCTYPE html>
<html>
<head>
       <style>
       .login-box, .register-box {
    width: 420px;
    margin: 7% auto;
    margin-bottom: 200px!important;
}
        .form-control {
    border-radius: 4px!important;
    box-shadow: none;
    border-color: #d2d6de;
}
.btn.btn-flat {
    border-radius: 4px!important;}
.login-logo, .register-logo {
    font-size: 35px;
    text-align: center;
    margin-bottom: 15px!important;
    font-weight: 300;
}
    </style>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="parvez">
    <title>Admin</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/bootstrap.min.css')); ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/font-awesome/css/font-awesome.min.css')); ?>">

    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/dist/css/AdminLTE.min.css')); ?>">
 <!---favicon-->
        <link rel="shortcut icon" href="<?php echo e(url('public/frontend/images/favicon.png')); ?>" />
</head>

<body class="hold-transition login-page" style="    background-image: '<?php echo e(url('/public/frontend/images/architecture-blur-building-business-449559.jpg')); ?>';
    background-size: cover;
    background-position: center;">
<div class="login-box">
    

    <div class="login-box-body" style="padding:16px 18px 40px 18px  ;   border-radius: 8px; box-shadow:0 0 5px #121212;">
<div class="login-logo">
        <?php if(!empty($logo)): ?>
            <a href="<?php echo e(url('admin/')); ?>"><img src='<?php echo e(url('public/frontend/images/logomain.png')); ?>' class="img-responsive" width="282" height="63" style="width: 40%;
   
    margin: auto;"></a>
        <?php else: ?>
            <img src='<?php echo e(url('public/frontend/images/logomain.png')); ?>' class="img-responsive" width="282" height="63" style="width: 40%;
    
    margin: auto;">
        <?php endif; ?>
    </div><!-- /.login-logo -->
        <?php if(Session::has('message')): ?>
            <div class="alert <?php echo e(Session::get('alert-class')); ?> text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong><?php echo e(Session::get('message')); ?></strong>
            </div>
        <?php endif; ?>

        <div class="alert alert-danger text-center" id="error_message_div" style="margin-bottom:0px;display:none;" role="alert">
            <p><a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a></p>
            <p id="error_message"></p>
        </div>
        <!-- /.Flash Message  -->

        <form action="<?php echo e(url('admin/forget-password')); ?>" method="post" id="forget-password-form">
            <?php echo e(csrf_field()); ?>


            <div class="form-group has-feedback <?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                <label class="control-label sr-only" for="inputSuccess2">Email</label>
                <input type="email" class="form-control" placeholder="Email" name="email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                <?php if($errors->has('email')): ?>
                    <span class="help-block"><strong><?php echo e($errors->first('email')); ?></strong></span>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-xs-5">
                    <button type="submit" class="btn btn-primary btn-block btn-flat" id="admin-forget-password-submit-btn">
                        <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                        <span id="admin-forget-password-submit-btn-text" style="font-weight: bolder;">Submit</span>
                    </button>
                </div>
                <div class="col-xs-2">
                </div>
                <div class="col-xs-5">
                    <a href="<?php echo e(url('admin')); ?>" class="btn btn-primary btn-block btn-flat">Back To Login</a><br>
                </div>
            </div>
        </form>
    <!-- /.social-auth-links -->
        
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<footer style="
    text-align: center;
    padding: 10px;
    color: black;
">
                
<strong style="font-size: 12px;">Copyright © 2023 <a href="https://ewallet.ticktappay.com/" target="_blank">TickTapPay</a> |  Powered By <a href="https://www.quickewallet.com" target="_blank">Quickewallet</a></strong>
      </footer>

<!-- jQuery 3 -->
<script src="<?php echo e(asset('public/backend/jquery/dist/jquery.min.js')); ?>" type="text/javascript"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?php echo e(asset('public/backend/bootstrap/dist/js/bootstrap.min.js')); ?>" type="text/javascript"></script>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>

<script>
    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
    });

    $('#forget-password-form').validate({
        errorClass: "has-error",
        rules: {
            email: {
                required: true,
                email: true,
            },
        },
        submitHandler: function(form)
        {
            $("#admin-forget-password-submit-btn").attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });
            $(".fa-spin").show();
            $("#admin-forget-password-submit-btn-text").text("Submitting..");
            form.submit();
        }
    });
</script>
</body>
<?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/auth/forgetPassword.blade.php ENDPATH**/ ?>