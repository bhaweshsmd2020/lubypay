@extends('admin.layouts.master')
@section('title', 'Merchants KYC')
@section('page_content')

    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">
    
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
                                    <th>Country</th>
                                    <th>KYC Status</th>
                                    <th>KYC Method</th>
                                    <th>Submitted On</th>
                                    <th>Action</th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index=>$user)
                                    <?php
                                        if(!empty($user->kycstatus) && $user->kycstatus->status =='completed'){
                                            $kyc_method = "Auto KYC";
                                        }else{
                                            $kyc_method = "Manual KYC";
                                        }
                                        
                                        $user_proof = DB::table('kycdatastores')->where('user_id', $user->id)->first();
                                        
                                        if ($user->document_verification->count() > 0)
                                        {
                                            if($user->address_verified == '1'){
                                                $address = '<span class="label label-info">Address Verified</span><br>';
                                            }else{
                                                $address = '';
                                            }
                                            
                                            if($user->identity_verified == '1'){
                                                $identity = '<span class="label label-primary">Identity Verified</span><br>';
                                            }else{
                                                $identity = '';
                                            }
                                            
                                            if($user->photo_verified == '1'){
                                                $photo = '<span class="label label-warning">Photo Verified</span><br>';
                                            }else{
                                                $photo = '';
                                            } 
                                            
                                            if($user->video_verified == '1'){
                                                $video = '<span class="label label-default">Video Verified</span>';
                                            }else{
                                                $video = '';
                                            }
                                            
                                            $status = $address.$identity.$photo.$video;
                                        }
                                        else
                                        {
                                            $status = '<span class="label label-warning">KYC Pending</span>';
                                        }
                                        
                                        if(!empty($user->kycstatus) && $user->kycstatus->status =='completed'){
                                            $kycstatus = '<span class="label label-success">Completed</span>';
                                        }else{
                                            if ($user->address_verified == '1' && $user->identity_verified == '1' &&  $user->photo_verified == '1' &&  $user->video_verified == '1'){
                                                $kycstatus = '<span class="label label-success">Completed</span>';
                                            }else{
                                                $kycstatus = '<span class="label label-danger">Not Completed</span>';
                                            }
                                        }
                                        
                                        $user_doc = DB::table('document_verifications')->where('user_id', $user->id)->first();
                                        if(!empty($user_doc)){
                                            $updated_date = Carbon\Carbon::parse($user->created_at)->format('d-M-Y');
                                        }else{
                                            $updated_date = 'N/A';
                                        }
                                    ?>
                                    
                                    <tr>
                                        <td>{{++$index}}</td>
                                        <td>{{$user->carib_id}}</td>
                                        <td><a href="{{ url('admin/users/edit/' . $user->id) }}">{{$user->first_name.' '.$user->last_name}}</a></td>
                                        <td><a href="{{ url('admin/users/edit/' . $user->id) }}">{{$user->email}}</a></td>
                                        <td>{{$user->defaultCountry}}</td>
                                        <td>
                                            <?php 
                                                if(!empty($kycstatus)){
                                                    echo $kycstatus; 
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            {{$kyc_method}} <br>
                                            
                                            @if($kyc_method == 'Manual KYC')
                                                @if(!empty($status))
                                                    <?php echo $status; ?>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $updated_date }}</td>
                                        <td>
                                            @if($kyc_method == 'Manual KYC')
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_mpos_kyc'))
                                                    <a href="{{ url('admin/users/kyc-verications/' . $user->id)}}" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Documents!"></i></a>&nbsp;
                                                @endif
                                            @elseif($kyc_method == 'Auto KYC')
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_mpos_kyc'))
                                                    <a href="{{ url('admin/users/kyc-verications/' . $user->id) }}" class="label label-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                                @endif
                                                
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_mpos_kyc'))
                                                    <a href="{{ url('admin/kyc/view/' . $user_proof->proof_id) }}" class="label label-success"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;
                                                @endif
                                                
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_mpos_kyc'))
                                                    <a href="{{ url('admin/kyc/delete/' . $user_proof->id) }}" class="label label-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;
                                                @endif
                                            @else
                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_mpos_kyc'))
                                                    <a href="{{ url('admin/users/kyc-verications/' . $user->id)}}" class="label label-primary"><i class="glyphicon glyphicon-edit" data-toggle="tooltip" title="Documents!"></i></a>&nbsp;
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
@endsection