            <div class="chart-list float-left">
            <ul>
                <li class="tabc <?=($menu=='my_store') ? 'active' :''?> ">
                  <a href='<?php echo e(url("mystore")); ?>'>Store</a>
                </li>
                <li class="tabc <?=($menu=='categories') ? 'active' :''?>">
                  <a href="<?php echo e(url("categories")); ?>">Category</a>
                </li>
                <li class="tabc <?=($menu=='product') ? 'active' :''?>">
                  <a href="<?php echo e(url("products")); ?>">Products</a>
                </li>
                <li class="tabc <?=($menu=='orders') ? 'active' :''?>">
                  <a href="<?php echo e(url("orders")); ?>">Orders</a>
                </li>
                <li class="tabc <?=($menu=='customers') ? 'active' :''?>">
                  <a href="<?php echo e(url("customers")); ?>">Customers</a>
                </li>
            </ul>
            </div>
            <br><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/layouts/common/tab.blade.php ENDPATH**/ ?>