            <div class="chart-list float-left">
            <!--<ul class="nav nav-pills nav-justified" >-->
            <ul>
                <li class="tabc <?=($menu=='my_store') ? 'active' :''?> ">
                  <a href='<?php echo e(url("mystore")); ?>'>Store</a>
                </li>

                <li class="tabc <?=($menu=='categories') ? 'active' :''?>">
                  <a href="<?php echo e(url("categories")); ?>">Category</a>
                </li>
                
                <li class="tabc <?=($menu=='attributes') ? 'active' :''?>">
                  <a href="<?php echo e(url("attributes")); ?>">Attributes</a>
                </li>
                <!-- <li class="tabc <?=($menu=='shipping_cost') ? 'active' :''?>">-->
                <!--  <a href="<?php echo e(url("shipping_cost")); ?>">Shipping</a>-->
                <!--</li>-->
                <li class="tabc <?=($menu=='product') ? 'active' :''?>">
                  <a href="<?php echo e(url("products")); ?>">Products</a>
                </li>
               <li class="tabc <?=($menu=='packeging') ? 'active' :''?>">
                  <a href="<?php echo e(url("packeging")); ?>">Packaging</a>
                  
                </li>
                <li class="tabc <?=($menu=='orders') ? 'active' :''?>">
                  <a href="<?php echo e(url("orders")); ?>">Orders</a>
                </li>
                <li class="tabc <?=($menu=='customers') ? 'active' :''?>">
                  <a href="<?php echo e(url("customers")); ?>">Customers</a>
                </li>
                </ul>
                </div>
                <br><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/user_dashboard/layouts/common/tab.blade.php ENDPATH**/ ?>