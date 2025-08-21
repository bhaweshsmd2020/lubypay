
<?php $__env->startSection('title', 'Social Settings'); ?>

<?php $__env->startSection('page_content'); ?>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
    <!-- Main content -->
    <div class="row">
       
        <?php if(Session::has('success')): ?>
        <div class="alert alert-success alert-dismissible" style="width: fit-content;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success!</strong> <?php echo e(Session::get('success')); ?>

        </div>
       
        <?php endif; ?>
        <div class="col-md-3 settings_bar_gap">
            <?php echo $__env->make('admin.common.settings_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Utility Settings</h3>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_1">
                        <div class="box-body" >
                            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $service; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($value->service_name); ?> </td>
                                            <?php if($value->is_active== 0): ?>
                                                <td><span class="label label-danger">Disable</span></td>
                                            <?php else: ?>
                                                <td><span class="label label-success">Enable</span></td>
                                            <?php endif; ?>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_utility_setting')): ?>
                                                <td>
                                                    <a href="" data-toggle="modal" data-target="#myModal_<?php echo e($value->service_id); ?>" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <div id="myModal_<?php echo e($value->service_id); ?>" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                            
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">Update <?php echo e($value->service_name); ?></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?php echo e(url('admin/settings/update_utility')); ?>" method="post" enctype="multipart/form-data" >
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="service_id" value="<?php echo e($value->service_id); ?>">
                                                            <input type="hidden" name="old_logo" value="<?php echo e($value->logo); ?>">
                                                            <input type="hidden" name="old_inactive_icon" value="<?php echo e($value->inactive_icon); ?>">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                  <div class="form-group">
                                                                     <label  for="notification_type_name">Service Name</label>
                                                                       <input type="text" name="service_name" class="form-control" value="<?php echo e($value->service_name); ?>" id="notification_type_name" >
                                                                            <span id="type_error"></span>
                                                                            <?php if($errors->has('notification_type_name')): ?>
                                                                                <span class="help-block">
                                                                                    <strong class="text-danger"><?php echo e($errors->first('notification_type_name')); ?></strong>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                     </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                   <div class="form-group">
                                                                      <label  for="notification_type_name">Service Slug</label>
                                                                         <input type="text" name="service_slug" class="form-control" value="<?php echo e($value->service_slug); ?>" readonly id="notification_type_name" >
                                                                            <span id="type_error"></span>
                                                                            <?php if($errors->has('notification_type_name')): ?>
                                                                                <span class="help-block">
                                                                                    <strong class="text-danger"><?php echo e($errors->first('notification_type_name')); ?></strong>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                     <div class="form-group">
                                                                      <label  for="notification_type_name">Service Icon</label>
                                                                         <input type="file" name="service_icon" class="form-control" >
                                                                            <span id="type_error"></span>
                                                                            <?php if($errors->has('notification_type_name')): ?>
                                                                                <span class="help-block">
                                                                                    <strong class="text-danger"><?php echo e($errors->first('notification_type_name')); ?></strong>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                     </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                     <div class="form-group">
                                                                        <label for="notification_type_status">Status</label>
                                                                          <select class="form-control" name="service_status" id="choose_<?php echo e($value->service_id); ?>">
                                                                                <option value='1' <?php echo e(isset($value->is_active)  == '1' ? 'selected':""); ?>> Enable</option>
                                                                                <option value='0' <?php echo e(isset($value->is_active) == '0' ? 'selected':""); ?>>Disable</option>
                                                                            </select>
                                                                            <?php if($errors->has('notification_type_status')): ?>
                                                                                <span class="help-block">
                                                                                    <strong class="text-danger"><?php echo e($errors->first('notification_type_status')); ?></strong>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                     </div>
                                                                </div>
                                                                <div class="col-md-6 newhide_<?php echo e($value->service_id); ?>">
                                                                     <div class="form-group">
                                                                      <label  for="notification_type_name">Inactive Message</label>
                                                                         <textarea  name="service_inactive_message" class="form-control" ><?php echo e($value->inactive_message??''); ?></textarea>
                                                                            <span id="type_error"></span>
                                                                            <?php if($errors->has('notification_type_name')): ?>
                                                                                <span class="help-block">
                                                                                    <strong class="text-danger"><?php echo e($errors->first('notification_type_name')); ?></strong>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                     </div>
                                                                </div>
                                                                <div class="col-md-6 newhide_<?php echo e($value->service_id); ?>">
                                                                     <div class="form-group">
                                                                      <label  for="notification_type_name">Inactive Icon</label>
                                                                         <input type="file" name="service_inactive_icon" class="form-control"  >
                                                                            <span id="type_error"></span>
                                                                            <?php if($errors->has('notification_type_name')): ?>
                                                                                <span class="help-block">
                                                                                    <strong class="text-danger"><?php echo e($errors->first('notification_type_name')); ?></strong>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                     </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="submit" class="btn btn-success" value="Update">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <script>
                                                  $(document).ready(function (){
                                                      $('.newhide_<?php echo e($value->service_id); ?>').hide();
                                                      $('#choose_<?php echo e($value->service_id); ?>').change('on',function(){
                                                          var status = $('#choose_<?php echo e($value->service_id); ?>').val();
                                                          // alert(status);
                                                          if(status == '1')
                                                          {
                                                              $('.newhide_<?php echo e($value->service_id); ?>').hide();
                                                          }else
                                                          {
                                                              $('.newhide_<?php echo e($value->service_id); ?>').show();
                                                          }
                                                      });
                                                  });
                                                </script>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/settings/allutility.blade.php ENDPATH**/ ?>