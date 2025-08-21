<?php $__env->startSection('content'); ?>

<style>
    .small-box .icon{
        font-size: 50px;
    }
    
    .small-box:hover .icon{
        font-size: 70px;
    }
</style>

<section class="content">
      <div class="row">
        <!--Graph Line Chart last 30 days start-->
          <div class="col-md-12">
          <!-- LINE CHART -->

                        <div class="box box-body">
                            <div class="row" >
                            <?php $__currentLoopData = $product; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $products): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             <div class="col-md-3" style=" margin-bottom:10px">
                                 <a href="<?php echo e(url('/giftcarddetails/'.$products['productId'])); ?>" >
                                <!-- small box -->
                                <div class="small-box bg-yellow" style="border: 1px solid;border-radius: 5px;">
                                     <img src="<?php echo e($products['logoUrls']['0']); ?>" ></img>
                                </div>
                                <h3 style="text-align: center;"><?php echo e($products['productName']); ?></h3>
                                <div <div style="margin: 10px; padding-top: 5px; padding-bottom: 5px; text-align: center; border-radius: 10px; border: 1px solid;">
                                    View Details
                                </div>
                                </a>
                            </div>
                            <br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <br>
                        </div>


          </div>
      </div>
      
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/user_dashboard/giftcard/giftcard.blade.php ENDPATH**/ ?>