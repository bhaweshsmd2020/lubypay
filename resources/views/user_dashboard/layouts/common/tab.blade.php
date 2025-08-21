            <div class="chart-list float-left">
            <ul>
                <li class="tabc <?=($menu=='my_store') ? 'active' :''?> ">
                  <a href='{{url("mystore")}}'>Store</a>
                </li>
                <li class="tabc <?=($menu=='categories') ? 'active' :''?>">
                  <a href="{{url("categories")}}">Category</a>
                </li>
                <li class="tabc <?=($menu=='product') ? 'active' :''?>">
                  <a href="{{url("products")}}">Products</a>
                </li>
                <li class="tabc <?=($menu=='orders') ? 'active' :''?>">
                  <a href="{{url("orders")}}">Orders</a>
                </li>
                <li class="tabc <?=($menu=='customers') ? 'active' :''?>">
                  <a href="{{url("customers")}}">Customers</a>
                </li>
            </ul>
            </div>
            <br>