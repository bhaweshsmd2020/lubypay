<?php
$socialList = getSocialLink();
$menusFooter = getMenuContent('Footer');
?>

<section class="contact" id="contact">
    <div class="contact-content">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4">
                    <?php if(count($socialList) != 0): ?>
                        <div class="contact-detail">
                            <h2><?php echo app('translator')->get('message.footer.follow-us'); ?></h2>
                            <?php if(!empty($socialList)): ?>
                                <div class="social-icons">
                                    <?php $__currentLoopData = $socialList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($social->url)): ?>
                                            <a href="<?php echo e($social->url); ?>"><?php echo $social->icon; ?></a>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-sm-4">
                    <?php if(request()->path() != 'merchant/payment'): ?>
                        <div class="quick-link">
                            <h2 style="margin-left: 60px"><?php echo app('translator')->get('message.footer.related-link'); ?></h2>
                            <ul style="display: grid;grid-template-columns: 170px auto">
                                <li class="nav-item"><a href="<?php echo e(url('/')); ?>"
                                                        class="nav-link"><?php echo app('translator')->get('message.home.title-bar.home'); ?></a></li>
                                <li class="nav-item"><a href="<?php echo e(url('/send-money')); ?>"
                                                        class="nav-link"><?php echo app('translator')->get('message.home.title-bar.send'); ?></a></li>
                                <li class="nav-item"><a href="<?php echo e(url('/request-money')); ?>"
                                                        class="nav-link"><?php echo app('translator')->get('message.home.title-bar.request'); ?></a></li>
                                <?php if(!empty($menusFooter)): ?>
                                    <?php $__currentLoopData = $menusFooter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footer_navbar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="nav-item"><a href="<?php echo e(url($footer_navbar->url)); ?>"
                                                                class="nav-link"> <?php echo e($footer_navbar->name); ?></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                <li class="nav-item"><a href="<?php echo e(url('/developer')); ?>" class="nav-link"><?php echo app('translator')->get('message.home.title-bar.developer'); ?></a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-sm-4">
                    <form class="contact-form-area">
                        <h2><?php echo app('translator')->get('message.footer.language'); ?></h2>
                        <div class="form-group">
                            <select class="form-control" id="lang">
                                <?php $__currentLoopData = getLanguagesListAtFooterFrontEnd(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e(Session::get('dflt_lang') == $lang->short_name ? 'selected' : ''); ?> value='<?php echo e($lang->short_name); ?>'> <?php echo e($lang->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="playStore">
                            <?php $__currentLoopData = getAppStoreLinkFrontEnd(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($app->logo)): ?>
                                    <a href="<?php echo e($app->link); ?>"><img src="<?php echo e(url('public/uploads/app-store-logos/'.$app->logo)); ?>" class="img-responsive" width="125" height="50" style="padding-left:5px;padding-right: 5px;width:50%; float:left;height: 39px;"/></a>
                                <?php else: ?>
                                    <a href="#"><img src='<?php echo e(url('public/uploads/app-store-logos/default-logo.jpg')); ?>' class="img-responsive" width="120" height="90" style="height: 39px;width:50%; float:left;"/></a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/lubypay/public_html/develop/resources/views/frontend/layouts/common/footer_menu.blade.php ENDPATH**/ ?>