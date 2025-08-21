
<!-- temp-9, temp-15 and temp-20 - not in database, can be used later-->

<!-- start temp ID = 1 and ending temp-22, we should add from temp-23-->

<div class="box box-primary">

  {{-- normal template --}}
  <div class="box-header with-border">
    <h3 class="box-title underline">Email Templates</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">
        
      <li {{ isset($list_menu) &&  $list_menu == 'menu-61' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/61")}}">New User</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-17' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/17")}}">Email Verification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-19' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/19")}}">2-Factor Authentication</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-21' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/21")}}">Identity/Address Verification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-18' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/18")}}">Password Reset</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-33' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/33")}}">Deposit Notification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-1' ? 'class=active' : ''}} ><!--1-->
        <a href="{{ URL::to("admin/template/1")}}">Transferred Payments</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-2' ? 'class=active' : ''}} ><!--2-->
        <a href="{{ URL::to("admin/template/2")}}">Received Payments</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-48' ? 'class=active' : ''}} ><!--4-->
        <a href="{{ URL::to("admin/template/48")}}">Request Payment Sender</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-4' ? 'class=active' : ''}} ><!--4-->
        <a href="{{ URL::to("admin/template/4")}}">Request Payment Receiver</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-5' ? 'class=active' : ''}} ><!--5-->
        <a href="{{ URL::to("admin/template/5")}}">Request Payment Acceptance (Requestor)</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-49' ? 'class=active' : ''}} ><!--5-->
        <a href="{{ URL::to("admin/template/49")}}">Request Payment Acceptance (Acceptor)</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-50' ? 'class=active' : ''}} ><!--5-->
        <a href="{{ URL::to("admin/template/50")}}">Request Payment Cancellation (Requestor)</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-51' ? 'class=active' : ''}} ><!--5-->
        <a href="{{ URL::to("admin/template/51")}}">Request Payment Cancellation (Acceptor)</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-44' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/44")}}">Payout Notification</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-45' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/45")}}">Exchange Notification</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-46' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/46")}}">Mobile Reload</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-47' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/47")}}">Gift Card</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-11' ? 'class=active' : ''}} ><!--11-->
        <a href="{{ URL::to("admin/template/11")}}">Ticket</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-12' ? 'class=active' : ''}} ><!--12-->
        <a href="{{ URL::to("admin/template/12")}}">Ticket Reply</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-13' ? 'class=active' : ''}} ><!--13-->
        <a href="{{ URL::to("admin/template/13")}}">Dispute Reply</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-36' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/36")}}">Place Order</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-65' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/65")}}">Card Subscription</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-68' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/68")}}">Subscription Expiry</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-66' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/66")}}">Renew Subscription</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-73' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/73")}}">Upgrade Subscription</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-54' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/54")}}">Card Request</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-55' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/55")}}">Card Reload</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-70' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/70")}}">Card Status</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-67' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/67")}}">Maintenance Break</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-75' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/75")}}">ACH Transfer Request</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-77' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/77")}}">ACH Transfer Complete</a>
      </li>

    </ul>
  </div>
</div>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title underline">Email Templates of Admin actions</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-29' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/29")}}">User Status Change</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-14' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/14")}}">Merchant Payment</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-10' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/10")}}">Payout</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-6' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/6")}}">Transfers</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-8' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/8")}}">Request Payments (Success/Refund)</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-16' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/16")}}">Request Payments (Cancel/Pending)</a>
      </li>

    </ul>
  </div>
</div>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title underline">Admin Notifications</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">
        
      <li {{ isset($list_menu) &&  $list_menu == 'menu-63' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/63")}}">Ewallet New User</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-64' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/64")}}">mPOS New User</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-23' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/23")}}">Deposit Notification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-24' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/24")}}">Payout Notification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-25' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/25")}}">Exchange Notification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-26' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/26")}}">Transfer Notification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-27' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/27")}}">Request Acceptance Notification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-28' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/28")}}">Payment Notification</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-30' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/30")}}">Gift Card</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-31' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/31")}}">Mobile Reload</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-32' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/32")}}">User Verification</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-53' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/53")}}">Create Merchant</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-38' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/38")}}">Create Store</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-39' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/39")}}">Add Product</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-40' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/40")}}">Place Order</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-69' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/69")}}">Card Subscription</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-72' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/72")}}">Renew Subscription</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-74' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/74")}}">Upgrade Subscription</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-57' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/57")}}">Card Request</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-58' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/58")}}">Card Reload</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-71' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/71")}}">Card Status</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-76' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/76")}}">ACH Transfer Request</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-78' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/78")}}">ACH Transfer Complete</a>
      </li>
    </ul>
  </div>
</div>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title underline">mPOS Notifications</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">
        
      <li {{ isset($list_menu) &&  $list_menu == 'menu-62' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/62")}}">New User</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-52' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/52")}}">Create Merchant</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-41' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/41")}}">Create Store</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-42' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/42")}}">Add Product</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-43' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/43")}}">Place Order</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-37' ? 'class=active' : ''}} >
        <a href="{{ URL::to("admin/template/37")}}">Push Notifications</a>
      </li>
      
    </ul>
  </div>
</div>