
<?php $__env->startSection('title', 'Users KYC'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/backend/bootstrap/dist/css/daterangepicker.css')); ?>">
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">KYC Status</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>User ID</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Document Verification Status</th>
                                    <th>KYC Status</th>
                                    <th>KYC Method</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        if(!empty($user->kycstatus) && $user->kycstatus->status =='completed'){
                                            $kyc_method = "Auto KYC";
                                        }else{
                                            $kyc_method = "Manual KYC";
                                        }
                                        
                                        $user_proof = DB::table('kycdatastores')->where('user_id', $user->id)->first();
                                        
                                        if ($user->document_verification->count() > 0)
                                        {
                                            foreach ($user->document_verification as $document)
                                            {
                                                if ($document->user->address_verified && $document->user->identity_verified &&  $document->user->photo_verified &&  $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.
                                                    '<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.
                                                    '<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';;
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.
                                                    '<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    }
                                                }
                                                
                                                
                                                elseif ($document->user->address_verified && !$document->user->identity_verified && !$document->user->photo_verified  && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-info">Address Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-info">Address Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-info">Address Verified</span>';
                                                    }
                                                }
                                                elseif ($document->user->address_verified && $document->user->identity_verified && !$document->user->photo_verified  && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>';
                                                    }
                                                }
                                                elseif ($document->user->address_verified && !$document->user->identity_verified && $document->user->photo_verified  && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-info">Address Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    }
                                                }
                                                elseif (!$document->user->address_verified && $document->user->identity_verified && !$document->user->photo_verified && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>';
                                                    }
                                                }
                                                elseif (!$document->user->address_verified && $document->user->identity_verified && $document->user->photo_verified && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.'<br>'.'<span class="label label-default">Photo Verified</span>';
                                                    }
                                                }
                                                elseif ($document->user->address_verified && $document->user->identity_verified && !$document->user->photo_verified && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.'<br>'.'<span class="label label-info">Address Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.'<br>'.'<span class="label label-info">Address Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-primary">Identity Verified</span>'.'<br>'.'<span class="label label-info">Address Verified</span>';
                                                    }
                                                }
                                                elseif (!$document->user->address_verified && !$document->user->identity_verified && $document->user->photo_verified && $document->status == 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>'.'<br>'.'<span class="label label-primary">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>'.'<br>'.'<span class="label label-primary">Photo Verified</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>'.'<br>'.'<span class="label label-primary">Photo Verified</span>';
                                                    }
                                                }
                                                elseif (!$document->user->address_verified && !$document->user->identity_verified &&  !$document->user->photo_verified && $document->status != 'approved')
                                                {
                                                    if ($document->user->status == 'Active') {
                                                        $status = '<span class="label label-success">Active</span>';
                                                    } else if ($document->user->status == 'Inactive') {
                                                        $status = '<span class="label label-danger">Inactive</span>';
                                                    } else if ($document->user->status == 'Suspended') {
                                                        $status = '<span class="label label-warning">Suspended</span>';
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $status = '<span class="label label-warning">KYC Pending</span>';
                                        }
                                        
                                        if(!empty($user->kycstatus) && $user->kycstatus->status =='completed'){
                                            $kycstatus = '<span class="label label-success">Completed</span>';
                                        }else{
                                            if (!empty($document->user) && $document->user->address_verified == '1' && $document->user->identity_verified == '1' &&  $document->user->photo_verified == '1'){
                                                $kycstatus = '<span class="label label-success">Completed</span>';
                                            }else{
                                                $kycstatus = '<span class="label label-danger">Not Completed</span>';
                                            }
                                        }
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td><?php echo e($user->carib_id); ?></td>
                                        <td><a href="<?php echo e(url('admin/users/edit/' . $user->id)); ?>"><?php echo e($user->first_name.' '.$user->last_name); ?></a></td>
                                        <td><a href="<?php echo e(url('admin/users/edit/' . $user->id)); ?>"><?php echo e($user->email); ?></a></td>
                                        <td>
                                            <?php 
                                                if(!empty($status)){
                                                    echo $status; 
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if(!empty($kycstatus)){
                                                    echo $kycstatus; 
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo e($kyc_method); ?></td>
                                        <td>
                                            <?php if($kyc_method == 'Manual KYC'): ?>
                                                <a href="<?php echo e(url('admin/users/photoproof/' . $user->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Documents!"></i></a>&nbsp;
                                            <?php elseif($kyc_method == 'Auto KYC'): ?>
                                                <a href="<?php echo e(url('admin/users/photoproof/' . $user->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                                <a href="<?php echo e(url('admin/kyc/view/' . $user_proof->proof_id)); ?>" class="label label-success"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;
                                                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_kyc')): ?>
                                                    <a href="<?php echo e(url('admin/kyc/delete/' . $user_proof->id)); ?>" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="<?php echo e(url('admin/users/photoproof/' . $user->id)); ?>" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Documents!"></i></a>&nbsp;
                                            <?php endif; ?>
                                        </td>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/test/resources/views/admin/users/userskyc.blade.php ENDPATH**/ ?>