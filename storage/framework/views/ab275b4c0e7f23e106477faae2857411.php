<?php
$company_name = getCompanyName();
?>
<div class="row" id="footerId">
      <div  class="col-sm-2">
         <div class="full-width text-center">

       <a href="<?php echo e(route('dashboard')); ?>" class="logo" style="background-color: #0A4E92!important; border-radius: 0px 0px 0px 25px;height: 74px;">
        <span class="logo-mini"><b><?php echo e($app_name_short??''); ?></b></span>

          <?php if(!empty($company_logo)): ?>
            <img src="<?php echo e(asset('public/images/logos/'.$company_logo)); ?>" alt="logo" width="180" height="59" class="company-logo" style="background-color: #fff;
             border-radius: 10px;padding: 5px;">
            <!--<img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" width="180" height="59" class="company-logo">-->
         <?php else: ?>
            <img src="<?php echo e(url('public/frontend/images/logomain.png')); ?>" width="180" height="59" class="company-logo" style="background-color: #fff;
             border-radius: 10px;padding: 5px;">
         <?php endif; ?>
     
    </a>
    </div>
    </div>
    <div class="col-sm-6 text-center">
        <strong>Copyright &copy; <?php echo e(date("Y")); ?> &nbsp;<a href="<?php echo e(route('dashboard')); ?>" target="_blank" style="color:#fff"><?php echo e($company_name); ?></a>&nbsp; |&nbsp; <!-- <?php echo app('translator')->get('message.footer.copyright-text'); ?>-->
             Powered By <a href="#" target="_blank" style="color:#fff">Quickewallet</a><?php 
             
            //  if(auth()->user()->id)
            //  {
                 
            //     $dal = DB::table('activity_logs')
            //     ->where('user_id',auth()->user()->id)
            //     ->orderBy('id', 'desc')LubyAll LLC
            //     ->skip(1)
            //     ->take(1)
            //     ->first();
            //     if(isset($dal))
            //     {
            //         echo "Last Login :".$dal->created_at;
            //     }
               
            //  }
             
              ?>
             </strong>
        </div>
       <div class="col-sm-4 text-center">
       <div class="hidden-xs">
        <b>Version</b> 2.7
       </div>
       </div> 
</div>


<!-- Delete Modal for buttons-->
<div class="modal fade" id="confirmDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirm">Yes</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal for href-->
<div class="modal fade" id="delete-warning-modal" role="dialog" style="z-index:1060; color: light blue;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:100%;height:100%; background-color: aliceblue;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p><strong>Are you sure you want to delete?</strong></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" id="delete-modal-yes" href="javascript:void(0)">Yes</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Modal for href-->
<div class="modal fade" id="notifications-warning-modal" role="dialog" style="z-index:1060; color: light blue;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:100%;height:100%; background-color: aliceblue;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Notifications</h4>
            </div>
            <div class="modal-body">
                <p><strong>Are you sure you want to mark all notifications as read?</strong></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" id="notifications-modal-yes" href="javascript:void(0)">Yes</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/lubypay/public_html/test/resources/views/admin/layouts/partials/footer.blade.php ENDPATH**/ ?>