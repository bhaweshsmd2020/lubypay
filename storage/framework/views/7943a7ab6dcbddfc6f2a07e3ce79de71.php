<?php
    $app_name_long   = getCompanyName();
    $company_logo   = getCompanyLogoWithoutSession();
    if (trim($app_name_long) && strpos($app_name_long, ' ') !== false)
    {
        $word = explode(' ',$app_name_long);
        $app_name_short = ucfirst($word[0][0]).ucfirst($word[1][0]);
    }
    else
    {
        $app_name_short = ucfirst($app_name_long[0]);
    }

    if(!empty(Auth::guard('admin')->user()->picture))
    {
      $picture = Auth::guard('admin')->user()->picture;
      $admin_image = asset('public/uploads/userPic/'.$picture);
    }
    else
    {
      $admin_image = asset('public/uploads/userPic/default-image.png');
    }
    $admin_name = Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name;
    $admin_email = Auth::guard('admin')->user()->email;
    $admin_id = Auth::guard('admin')->user()->id;
    
    $count_noti = DB::table('notifications')->where([['notification_to', $admin_id], ['clicked', '0']])->count();
    
    $unread_noti = DB::table('notifications')->where([['notification_to', $admin_id], ['clicked', '0']])->orderByRaw('created_at DESC')->get();
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="MTS">
        <title> <?php echo e($app_name_long); ?> | <?php echo $__env->yieldContent('title'); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"><!-- for ajax -->

        <script type="text/javascript">
            var SITE_URL = "<?php echo e(url('/')); ?>";
        </script>

         <!---favicon-->
    <?php if(!empty(getfavicon())): ?>
        <link rel="shortcut icon" href="<?php echo e(url('public/images/logos/'.getfavicon())); ?>" />
    <?php else: ?>
        <link rel="shortcut icon" href="<?php echo e(url('public/frontend/images/favicon.png')); ?>" />
    <?php endif; ?>
        
        <style>
             #csv{
                  background-color: green;
                  color:#fff;
             }
              #pdf{
                 background-color: red;
                  color:#fff;
             }
             #users_cancel ,#cancel_anchor{
                     margin: 0% 37%;
             }
             #users_create ,#create_ticket{
                     margin: -2.8% 44% 0% 0%;
             }
             
            .main-sidebar {
                width: 280px !important;
            }
             
            .content-wrapper {
                margin-left: 280px !important;
            }
            
            .skin-blue .main-header .navbar .nav>li>a:hover{
                color: #fff !important;
                background-color: transparent !important;
            }
            
            #delete-warning-modal{
                color: #000 !important;
            }
            
            #notifications-warning-modal{
                color: #000 !important;
            }
        </style>
        <?php echo $__env->make('admin.layouts.partials.head_style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('admin.layouts.partials.head_script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </head>

    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper_custom">
            <?php echo $__env->make('admin.layouts.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <!-- sidebar -->
            <aside class="main-sidebar" style="top:25px!important;background-color:#ffffff!important">
                <section class="sidebar">
                    <?php echo $__env->make('admin.layouts.partials.sidebar_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </section>
            </aside>

            <div class="content-wrapper">
                <!-- Main content -->
                <section class="content">
                    <!-- Flash Message  -->
                    <div class="box-body" style="padding : 10px 0px;">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(Session::has('message')): ?>
                                    <div class="alert <?php echo e(Session::get('alert-class')); ?> text-center" style="margin-bottom:0px;" role="alert">
                                      <?php echo e(Session::get('message')); ?>

                                      <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
                                    </div>
                                <?php endif; ?>
                                <div class="alert alert-success text-center" id="success_message_div" style="margin-bottom:0px;display:none;" role="alert">
                                    <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
                                    <p id="success_message"></p>
                                </div>
                            
                                <div class="alert alert-danger text-center" id="error_message_div" style="margin-bottom:0px;display:none;" role="alert">
                                    <p><a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a></p>
                                    <p id="error_message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.Flash Message  -->
                    <?php echo $__env->yieldContent('page_content'); ?>
                </section>
            </div>

            <!-- footer -->
            <footer class="main-footer">
                <?php echo $__env->make('admin.layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </footer>
            <div class="control-sidebar-bg"></div>
        </div>

        <!-- body_script -->
        <?php echo $__env->make('admin.layouts.partials.body_script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldPushContent('extra_body_scripts'); ?>
        
        <div class="modal fade" id="logoutWarningModal" tabindex="-1" role="dialog" aria-labelledby="logoutWarningLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="margin-top: 110px;">
                    <div class="modal-header" style="background-color: #f8d7da; color: #721c24;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="logoutWarningLabel">Session Expired</h4>
                    </div>
                    <div class="modal-body" style="font-size: 16px; text-align: center; padding: 20px;">
                        <p style="margin-bottom: 20px;">You have been inactive for a while. You will be logged out soon.</p>
                        <p><strong>Please login again to continue!</strong></p>
                    </div>
                    <div class="modal-footer" style="border-top: none; justify-content: center; text-align: center;">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                            OK, Got it
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            let inactivityTime = 5 * 60 * 1000; // 1 minute
            let warningTime = inactivityTime;
            let timeout, warning;
            let warningShown = false;
            
            // Function to reset the inactivity timer
            const resetTimer = () => {
                clearTimeout(timeout);
                clearTimeout(warning);
            
                if (!warningShown) {
                    // Set a new timer for showing the modal
                    warning = setTimeout(() => {
                        showModal();
                    }, warningTime);
                }
            };
            
            // Function to show the modal
            const showModal = () => {
                warningShown = true;
                $('#logoutWarningModal').modal('show');
            };
            
            // Event listener for click and keypress to log out after modal is shown
            document.addEventListener('click', () => {
                if (warningShown) {
                    window.location.href = "<?php echo e(route('adminlogout')); ?>";
                }
            });
            
            document.addEventListener('keypress', () => {
                if (warningShown) {
                    window.location.href = "<?php echo e(route('adminlogout')); ?>";
                }
            });
            
            // Event listener for resetting the timer (includes mouse movements)
            document.addEventListener('mousemove', resetTimer); // Detect mouse movements
            document.addEventListener('click', resetTimer);     // Detect clicks
            document.addEventListener('keypress', resetTimer);  // Detect key presses
            
            // Initialize the inactivity timer
            resetTimer();


        </script>
    </body>
</html>
<?php /**PATH /home/lubypay/public_html/develop/resources/views/admin/layouts/master.blade.php ENDPATH**/ ?>