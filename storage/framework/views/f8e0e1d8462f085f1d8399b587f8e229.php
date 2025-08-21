<?php if($paginator->hasPages()): ?>
    <div class="mt-3">
        <nav class="pagi-nav f-13 gilroy-regular d-flex justify-content-between align-items-center"
            aria-label="...">
            <ul class="pagination mb-0 r-pagi">
                <?php if($paginator->onFirstPage()): ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0)">
                            <?php echo svgIcons('left_angle_sm'); ?>

                            <?php echo e(__('Prev')); ?>

                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>">
                            <?php echo svgIcons('left_angle_sm'); ?>

                            <?php echo e(__('Prev')); ?>

                        </a>
                    </li>
                <?php endif; ?>

                <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php if(is_string($element)): ?>
                        <li class="page-item disabled" aria-current="page">
                            <a class="page-link" href="javascript:void(0)"><?php echo e($element); ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if(is_array($element)): ?>
                        <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $paginator->currentPage()): ?>
                                <li class="page-item active"><a class="page-link" href="javascript:void(0)"><?php echo e($page); ?></a></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                <?php if($paginator->hasMorePages()): ?>
                    <li class="page-item">
                        <a class="page-link dark-A0" href="<?php echo e($paginator->nextPageUrl()); ?>">
                            <?php echo e(__('Next')); ?>

                            <?php echo svgIcons('right_angle_sm'); ?>

                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link dark-A0" href="javascript:void(0)"> 
                            <?php echo e(__('Next')); ?>

                            <?php echo svgIcons('right_angle_sm'); ?>

                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div>
                <p class="mb-0 text-gray-100 tran-title page-limite"><?php echo __('Showing'); ?>: <?php echo e($paginator->firstItem()); ?> - <?php echo e($paginator->lastItem()); ?> <?php echo e(__('of')); ?> <?php echo e($paginator->total()); ?></p>
            </div>
        </nav>
    </div>
<?php endif; ?>
<?php /**PATH /home/lubypay/public_html/develop/resources/views/vendor/pagination/bootstrap-5.blade.php ENDPATH**/ ?>