

<?php $__env->startSection('title', 'KYC Details'); ?>

<?php $__env->startSection('head_style'); ?>
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_content'); ?>
    <div class="row"> 
    <div class="col-md-8">
          <div class="box mt-4">
      <div class="box-body">
          <h4 class="mb-2"><strong>Verification/Selfie</strong></h4>
        <div class="row text-center">
            <div class="col-md-4">
                <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->left_photo_url); ?>" alt="left_photo_url"  width="100%" height="250" width="100%">
                <h4>Left</h4>
            </div>
            <div class="col-md-4">
                <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->center_photo_url); ?>" alt="center_photo_url"  width="100%" height="250" width="100%">
                <h4>Center</h4>
            </div>
            <div class="col-md-4">
                <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->right_photo_url); ?>" alt="right_photo_url" width="100%" height="250" width="100%">
                            <h4>Right</h4>
            </div>
        </div>
      </div>
    </div>
     <div class="box mt-3">
      <div class="box-body">
          <h4 class="mb-2"><strong>Verification/Government Id</strong></h4>
        <div class="row text-center">
            <div class="col-md-4">
                <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->front_photo_url); ?>" alt="front_photo_url"  height="250" width="100%">
                <h4>Front</h4>
            </div>
            <div class="col-md-4">
                <?php if(!empty($details->back_photo_url)): ?>
                    <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->back_photo_url); ?>" alt=""  height="250" width="100%">
                    <h4>Back</h4>
                <?php else: ?>
                    <h4>Not Available</h4>         
                <?php endif; ?>            
            </div>
            <div class="col-md-4">
                <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->selfie_photo_url); ?>" alt="selfie_photo_url" height="250" width="100%">
                <h4>Selfie</h4>
            </div>
        </div>
        <hr>
        <h4 class="mb-2"><strong>Attributes</strong></h4>
        <div class="row">
            <div class="col-md-4">
                  <h5><strong>NAME</strong></h5>
                 <h5><?php echo e($details->name_first); ?>&nbsp;&nbsp;<?php echo e($details->name_middle); ?>&nbsp;&nbsp;<?php echo e($details->name_last); ?></h5>
            </div>
            <div class="col-md-4">
                  <h5><strong>GOVERNMENT ID NUMBER</strong></h5>
                 <h5><?php echo e($details->identification_number); ?></h5>
            </div>
            <div class="col-md-4">
                  <h5><strong>BIRTHDATE</strong></h5>
                 <h5><?php echo e($details->birthdate); ?></h5>
            </div>
        </div>
         <div class="row">
            <div class="col-md-4">
                  <h5><strong>ADDRESS</strong></h5>
                 <h5><?php echo e($details->addressstreet1); ?>&nbsp;&nbsp;<?php echo e($details->addressstreet2); ?>&nbsp;&nbsp;<?php echo e($details->address_city); ?>,<?php echo e($details->address_subdivision_abb); ?>&nbsp;<?php echo e($details->address_postal_code_abbr); ?></h5>
            </div>
            <div class="col-md-4">
                  <h5><strong>EMAIL ADDRESS</strong></h5>
                 <h5><?php echo e($details->email_address??'No email address collected'); ?></h5>
            </div>
            <div class="col-md-4">
                  <h5><strong>PHONE NUMBER</strong></h5>
                 <h5><?php echo e($details->phone_number??'No phone number collected'); ?></h5>
            </div>
        </div>
          <div class="row">
            <div class="col-md-4">
                  <h5><strong><strong>SELECTED COUNTRY CODE</strong></strong></h5>
                 <h5><?php echo e($details->selected_country_code); ?></h5>
            </div>
            <div class="col-md-4">
                  <h5><strong>SELECTED ID CLASS</strong></h5>
                 <h5><?php echo e($details->selected_id_class); ?></h5>
            </div>
        </div>
      </div>
    </div>
   
      
    <!-- <div class="box mt-4">-->
    <!--  <div class="box-body">-->
    <!--      <h4 class="mb-2">Document/Government-Id</h4>-->
    <!--    <div class="row text-center">-->
    <!--        <div class="col-md-4">-->
    <!--            <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->front_photo); ?>" alt="front_photo"  height="250">-->
    <!--        </div>-->
    <!--        <div class="col-md-4">-->
    <!--            <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->back_photo); ?>" alt="back_photo"  height="250">-->
    <!--        </div>-->
    <!--        <div class="col-md-4">-->
    <!--            <img src="<?php echo e(url('public/kyc_documents/').'/'.$details->selfie_photo); ?>" alt="selfie_photo" height="250">-->
    <!--        </div>-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</div>-->
    </div>
  <div class="col-md-4">
    <div class="box mt-3">
      <div class="box-body">
          <h4 class="mb-2"><strong>INFO</strong></h4>
        <div class="row">
             <div class="col-md-12">
                <h5><strong>STATUS</strong></h5>
                 <h5><?php echo e(ucfirst($details->status)); ?></h5>
            </div>
            <div class="col-md-12">
                <h5><strong>INQUIRY ID</strong></h5>
                 <h5><?php echo e($details->proof_id); ?></h5>
            </div>
            <div class="col-md-12">
                <h5><strong>REFERENCE ID</strong></h5>
                 <h5><?php echo e($details->reference_id); ?></h5>
            </div>
            <div class="col-md-12">
                <h5><strong>ACCOUNT ID</strong></h5>
                 <h5><?php echo e($details->account_id); ?></h5>
            </div>
            <div class="col-md-12">
                <h5><strong>CREATED AT</strong></h5>
                 <h5><?php echo e($details->created_at); ?></h5>
            </div>
        </div>
      </div>
    </div>
   <div class="box mt-4">
      <div class="box-body">
          <h4 class="mb-2"><strong>Location</strong></h4>
            <div class="row">
             <div class="col-md-12">
                <div><?php echo e($details->region_name); ?></div>
                <div><?php echo e($details->country_name); ?></div>
                <div><strong>DEVICE</strong> (<?php echo e($details->os_name); ?>  <?php echo e($details->os_full_version); ?> <?php echo e($details->device_name); ?>)</div>
             </div>
            </div>
      </div>
    </div>
   <div class="box mt-4">
      <div class="box-body">
          <h4 class="mb-2"><strong>Network  Details</strong></h4>
            <div class="row">
             <div class="col-md-12">
                <strong>IP ADDRESS:</strong>	<?php echo e($details->ip_address); ?>

            </div>
            </div>
            <div class="row">
            <div class="col-md-12">
                <strong>NETWORK THREAT LEVEL:</strong>	<?php echo e($details->threat_level); ?>

             </div>
             <div class="col-md-12">
                  <strong>LATITUDE:</strong>	<?php echo e($details->latitude); ?>

             </div>
            </div>
            <div class="row">
            <div class="col-md-12">
               <strong>LONGITUDE:</strong>	<?php echo e($details->longitude); ?>

             </div>
             <div class="col-md-12">
              <strong>DEVICE TYPE:</strong>	<?php echo e($details->os_name); ?>,<?php echo e($details->os_full_version); ?>,<?php echo e($details->device_name); ?>

             </div>
            </div>
            <div class="row">
              <div class="col-md-12">
             <strong> DEVICE OS:</strong>	<?php echo e($details->os_name); ?>

             </div>
             <div class="col-md-12">
                  <strong>BROWSER:</strong> <?php echo e($details->browser_name); ?>

             </div>
            </div>
      </div>
    </div>

    </div>
    </div>
    
  
    
    
<?php $__env->stopSection(); ?>

<?php $__env->startPush('extra_body_scripts'); ?>

<!-- jquery.validate -->
<script src="<?php echo e(asset('public/dist/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js')); ?>" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="<?php echo e(asset('public/dist/js/isValidPhoneNumber.js')); ?>" type="text/javascript"></script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/test/resources/views/admin/kyc/kyc_details.blade.php ENDPATH**/ ?>