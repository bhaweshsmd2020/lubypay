
<?php $__env->startSection('title', 'Banners'); ?>
<?php $__env->startSection('page_content'); ?>

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">App Offers</div>
                </div>

                <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_offer')): ?>
                    <div class="col-md-2 pull-right">
                        <button type="button" class="btn btn-success btn-flat waves-effect waves-light collapsed"><span class="fa fa-plus"> &nbsp;</span>Add New Offer</button>
                    </div>
                <?php endif; ?>  
                
                <form action="<?php echo e(url('admin/sendpushsms')); ?>" class="form-horizontal" id="user_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token" id="token">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">          
                                <div class="form-group">
                                    <label class="col-sm-8" for="title">
                                        Offer Title
                                    </label>
                                    <div class="col-sm-12">
                                        <input  type="text"  id="title" class="form-control" name="title" placeholder="enter title of the message...">
                                       
                                        <?php if($errors->has('title')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('title')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8 require" for="title">
                                        Offer Start Date & Time
                                    </label>
                                    <div class="col-sm-12">
                                        <input required type="datetime-local" id="txtDatedd" format="MM-DD-YYYY HH:mm"class="form-control" name="date_time_start">
                                       
                                        <?php if($errors->has('title')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('title')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8 require" for="title">
                                        Offer End Date & Time
                                    </label>
                                    <div class="col-sm-12">
                                        <input required type="datetime-local"  id="txtDate_end" class="form-control" name="date_time">
                                       <label id="txtDate_end-error" class="error" style="display:none;" >Offer end date should be greather than start date !<?php  date('Y-m-d')?></label>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8 require" for="app_redirect">Select Redirect Type</label>
                                    <div class="col-sm-12">
                                        <select class="select2" name="app_redirect" id="app_redirect">
                                            <option value='0'>None</option>
                                            <option value='1'>App Page</option>
                                            <option value='2'>Redirect URL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8" for="inputEmail3">
                                        App Page
                                    </label>
                                    <div class="col-sm-12">
                                        <select class="form-control select2" name="app_page" id="app_page">
                                            <option value='Top'>Select App Page</option>
                                            <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value='<?php echo e($page->id); ?>'><?php echo e($page->page_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8" for="inputEmail3">
                                        Redirect URL
                                    </label>
                                   <div class="col-sm-12">
                                        <input  type="text" id="url" class="form-control" name="add_url" placeholder="enter url for this offer...">
                                       
                                        <?php if($errors->has('title')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('title')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8 require" for="title">
                                        Add Image
                                    </label>
                                    <div class="col-md-6">
                                        <input required type="file" name="offer_image" class="js_image" />
                                    </div>
                                </div>
                            </div>
                            
                            <?php 
                                $language=DB::table('languages')->where('status','Active')->get();
                            ?>
                            
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8" for="inputEmail3">
                                       Language
                                    </label>
                                    <div class="col-sm-12">
                                        <select class="form-control select2" name="language" id="language" required>
                                            <?php $__currentLoopData = $language; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value='<?php echo e($page->id); ?>'><?php echo e($page->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label class="col-sm-8" for="inputEmail3">
                                       Platform
                                    </label>
                                    <div class="col-sm-12">
                                        <select class="form-control select2" name="platform" id="platform" required>
                                            <option value='ewallet'>Ewallet</option>
                                            <option value='mpos'>Mpos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">  
                                <div class="form-group">
                                    <label class="col-sm-3" for="message">
                                        Message
                                    </label>
                                    <div class="col-sm-12">
                                        <textarea  name="message" id="message" class="form-control" rows="6" placeholder="enter  message..."></textarea>
                                        <?php if($errors->has('message')): ?>
                                            <span class="error">
                                                <?php echo e($errors->first('message')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </div>
                </form>
              
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
                                    <th>S. No</th>
                                    <th>Image</th>
                                    <th>Offer Title</th>
                                    <th>Description</th>
                                    <th>App Name</th>
                                    <th>Offer URL</th>
                                    <th>Language</th>
                                    <th>Platform</th>
                                    <th>Start Date & Time</th>
                                    <th>Expire Date & Time</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $offer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <td><img src="<?php echo e(asset('public/')); ?>/<?php echo e($value->offer_image); ?>" style="width:100px;height:50px"></td>
                                        <td><?php echo e($value->offer_title); ?></td>
                                        <td><?php echo e($value->offer_desc); ?></td>
                                        <td>
                                            <?php
                                                $check_page = DB::table('app_pages')->where('id', $value->app_page)->where('status', 'Active')->first();
                                            ?>
                                           
                                            <?php if(!empty($check_page)): ?>
                                               <?php echo e($check_page->app_page); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($value->offer_url); ?></td>
                                        <td>
                                            <?php 
                                                $language=DB::table('languages')->where('id',$value->language)->where('status','Active')->first()->name??'';
                                            ?>
                                            <?php echo e($language); ?>

                                        </td>
                                        <td>
                                            <?php if($value->platform == 'ewallet'): ?>
                                                Ewallet
                                            <?php else: ?>
                                                Mpos
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($value->start_time??'--:--'); ?></td>
                                        <td><?php echo e($value->expire_date_time??'--:--'); ?></td>
                                        <td>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_offer')): ?>
                                                <a href="<?php echo e(url('admin/delete-offer',$value->id)); ?>" class="label label-danger delete-warning"><i class="fa fa-trash"></i></a>&nbsp;
                                            <?php endif; ?>
                                            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_offer')): ?>
                                                <a href="<?php echo e(url('admin/edit-offer',$value->id)); ?>" class="label label-primary"><i class="fa fa-edit"></i></a>
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
    		document.title='Offers';
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
    
    <script>
    	$(document).ready(function(){
    		$('#user_form').hide();
    	  $(".collapsed").click(function(){
    		 
    		$("#user_form").slideToggle();
    	  });
    	});
        
        $(document).ready(function(){
        
        	var $modal = $('#modal');
        
        	var image = document.getElementById('sample_image');
        
        	var cropper;
        
        	$('#upload_image').change(function(event){
        		var files = event.target.files;
        
        		var done = function(url){
        			image.src = url;
        			$modal.modal('show');
        		};
        
        		if(files && files.length > 0)
        		{
        			reader = new FileReader();
        			reader.onload = function(event)
        			{
        				done(reader.result);
        			};
        			reader.readAsDataURL(files[0]);
        		}
        	});
        
        	$modal.on('shown.bs.modal', function() {
        		cropper = new Cropper(image, {
        			aspectRatio: 1,
        			viewMode: 3,
        			preview:'.preview'
        		});
        	}).on('hidden.bs.modal', function(){
        		cropper.destroy();
           		cropper = null;
        	});
        
        	$('#crop').click(function(){
        		canvas = cropper.getCroppedCanvas({
        			width:400,
        			height:400
        		});
        
        		canvas.toBlob(function(blob){
        			url = URL.createObjectURL(blob);
        			var reader = new FileReader();
        			reader.readAsDataURL(blob);
        			reader.onloadend = function(){
        				var base64data = reader.result;
        				$.ajax({
                               type:"POST",
                               url:"<?php echo e(url('admin/add-offer-image')); ?>",
                               data : { 
                                    _token: "<?php echo e(csrf_token()); ?>" ,image:base64data
                                },
                                success:function(res){  
                                    $('.js_image').val(res);
                                    $modal.modal('hide');
                                    var Imgurl = "https://ewallet.xpay.mv/public/"+res;
                                    $('.my_image1').show();
                                    $('.my_image1').attr('src',Imgurl);
        						    //$('#uploaded_image').attr('src', data);
                                console.log(Imgurl);
                              }
        				 });
        			};
        		});
        	});
        	
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/admin/notifications/pushsms.blade.php ENDPATH**/ ?>