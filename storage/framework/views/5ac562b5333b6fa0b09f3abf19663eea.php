    <section class="welcome-area image-bg">
        <div class="overlay-banner"> </div>
        <div class="overlay-text"> </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8">

                    <?php echo $__env->make('frontend.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="welcome-text">
                        <h1><?php echo app('translator')->get('message.home.banner.title',['br'=>'</br>']); ?></h1>
                        <p style="padding: 0px;
    padding-left: 58px;"><?php echo app('translator')->get('message.home.banner.des'); ?></p>
                    </div>
                    <div class="banner-text">
                        <div class="row">
                            <div class="col-md-4">
                                    <div class="feature-icon">
                                        <div><span><i class="fa fa-credit-card" aria-hidden="true"></i></span></div>
                                        <h2><?php echo app('translator')->get('message.home.banner.sub-title1',['br'=>'</br>']); ?></h2>
                                    </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="feature-icon">
                                        <div>
                                            <span><i class="fa fa-usd" aria-hidden="true"></i></span></div>
                                        <h2><?php echo app('translator')->get('message.home.banner.sub-title2',['br'=>'</br>']); ?></h2>
                                    </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="feature-icon">
                                        <div><span><i class="fa fa-shield" aria-hidden="true"></i></span></div>
                                        <h2><?php echo app('translator')->get('message.home.banner.sub-title3',['br'=>'</br>']); ?></h2>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><?php /**PATH C:\xampp\htdocs\lubypay\resources\views/frontend/layouts/common/banner.blade.php ENDPATH**/ ?>