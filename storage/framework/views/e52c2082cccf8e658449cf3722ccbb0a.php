
<?php $__env->startSection('title', 'Activity Logs'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box">
       <div class="panel-body">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                  <a href='<?php echo e(url("admin/users/edit/$users->id")); ?>'>Profile</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/transactions/$users->id")); ?>">Transactions</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/wallets/$users->id")); ?>">Wallets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/tickets/$users->id")); ?>">Tickets</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/disputes/$users->id")); ?>">Disputes</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/kyc-verications/$users->id")); ?>">KYC Verifications</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/bankdetails/$users->id")); ?>">Bank Details</a>
                </li>
                <li>
                  <a href="<?php echo e(url("admin/users/address_edit/$users->id")); ?>">Address</a>
                </li>
                <li class="active">
                  <a href="<?php echo e(url("admin/users/activity-logs/$users->id")); ?>">Activity Logs</a>
                </li>
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>
    
    <?php if($users->status == 'Inactive'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-danger">Inactive</span></h3>
    <?php elseif($users->status == 'Suspended'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-warning">Suspended</span></h3>
    <?php elseif($users->status == 'Active'): ?>
        <h3><?php echo e($users->first_name.' '.$users->last_name); ?>&nbsp;( <?php echo e($users->carib_id); ?> )&nbsp;<span class="label label-success">Active</span></h3>
    <?php endif; ?>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>User Type</th>
                                    <th>Username</th>
                                    <th>City/Country</th>
                                    <th>IP Address</th>
                                    <th>Browser/Platform</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$activityLog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td><?php echo e(dateFormat($activityLog->created_at)); ?></td>
                                        <td>
                                            <?php if($activityLog->type == 'User'): ?>
                                                Customer
                                            <?php else: ?>
                                                <?php echo e($activityLog->type); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($activityLog->type == 'Admin'): ?>
                                                <?php
                                                    $admin = $activityLog->admin->first_name.' '. $activityLog->admin->last_name;
                                                ?>
                                                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_admin')): ?>
                                                    <a href="<?php echo e(url('admin/admin-user/edit/' . $activityLog->admin->id)); ?>"><?php echo e($admin); ?></a>
                                                <?php else: ?>
                                                    <?php echo e($admin); ?>

                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php
                                                    $user = $activityLog->user->first_name.' '. $activityLog->user->last_name;
                                                ?>
                                                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')): ?>
                                                    <a href="<?php echo e(url('admin/users/edit/' . $activityLog->user->id)); ?>"><?php echo e($user); ?></a>
                                                <?php else: ?>
                                                    <?php echo e($user); ?>

                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php
                                                 // Fetch the last location of the user
                                                $last_location = DB::table('users_login_location')->where('user_id', $users->id)->first();
                                                
                                                // Initialize the country variable
                                                $country = '';
                                            
                                                // Only attempt to fetch the country name if a last location is found
                                                if ($last_location && !empty($last_location->country)) {
                                                    $country_record = DB::table('countries')->where('short_name', $last_location->country)->first();
                                                    $country = $country_record->name ?? ''; // Fetch the country name
                                                }
                                            ?>
                                        <td>
                                                <?php echo e($activityLog->city); ?> | <?php echo e($activityLog->country); ?>

                                        </td>
                                        <td><?php echo e($activityLog->ip_address); ?></td>
                                        <td><?php echo e($activityLog->browser_agent); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/pdfmake.min.js" ></script>
    <script type="text/javascript"  src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
    
    <script type="text/javascript">
    	$(document).ready(function() {
    		$('#example').DataTable(
    			{
    				"dom": '<"dt-buttons"Bf><"clear">lirtp',
    				"paging": true,
    				"autoWidth": true,
    				"buttons": [
    					'colvis',
    					'copyHtml5',
    	                'csvHtml5',
    					'excelHtml5',
    	                'pdfHtml5',
    					'print'
    				]
    			}
    		);
    	});
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/admin/users/activitylogs.blade.php ENDPATH**/ ?>