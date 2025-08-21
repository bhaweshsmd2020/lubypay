

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/user_dashboard/css/intl-tel-input-13.0.0/build/css/intlTelInput.css')); ?>">
    <style>
        @media only screen and (max-width: 508px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }

        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <?php if(session('status')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('warning')): ?>
                        <div class="alert alert-warning">
                            <?php echo e(session('warning')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li class="active"><a href="<?php echo e(url('/profile')); ?>"><?php echo app('translator')->get('message.dashboard.setting.title'); ?></a></li>
                                    <?php if($two_step_verification != 'disabled'): ?>
                                        <li><a href="<?php echo e(url('/profile/2fa')); ?>"><?php echo app('translator')->get('message.2sa.title-short-text'); ?></a></li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo e(url('/profile/personal-id')); ?>"><?php echo app('translator')->get('message.personal-id.title'); ?>
                                        <?php if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' ): ?>(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) <?php endif; ?>
                                        </a>
                                    </li>
                                    <li><a href="<?php echo e(url('/profile/personal-address')); ?>"><?php echo app('translator')->get('message.personal-address.title'); ?>
                                        <?php if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' ): ?>(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) <?php endif; ?>
                                        </a>
                                    </li>
                                    <li><a href="<?php echo e(url('/profile/personal-photo')); ?>"><?php echo app('translator')->get('message.personal-photo.title'); ?>
                                        </a>
                                    </li>
                                    <?php if(auth()->user()->type == 'merchant'): ?>
                                        <li>
                                            <a href="<?php echo e(url('/profile/business-verification')); ?>">
                                                Business Verification
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo e(url('/profile/upgrade')); ?>">
                                                Account Upgrade
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <?php if(!empty(Auth::user()->picture)): ?>
                                                <img src="<?php echo e(url('public/user_dashboard/profile/'.Auth::user()->picture)); ?>"
                                                     class="rounded-circle rounded-circle-custom-trans"
                                                     id="profileImage">
                                            <?php else: ?>
                                                <img src="<?php echo e(url('public/user_dashboard/images/avatar.jpg')); ?>"
                                                     class="rounded-circle rounded-circle-custom-trans"
                                                     id="profileImage">

                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <h4><?php echo app('translator')->get('message.dashboard.setting.change-avatar'); ?></h4>
                                            <p><?php echo app('translator')->get('message.dashboard.setting.change-avatar-here'); ?></p>
                                            <strong style="font-size: 12px !important;">*<?php echo e(__('Recommended Dimension')); ?>: 100 px * 100 px</strong>

                                            <input type="file" id="file" style="display: none"/>
                                            <input type="hidden" id="file_name"/>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="uploadAvatar">
                                                <a href="javascript:changeProfile()" id="changePicture"
                                                   class="btn btn-secondary btn-border btn-sm"
                                                   style="margin-top: 10px;">
                                                    <i class="fa fa-camera" aria-hidden="true"></i>
                                                    &nbsp; <?php echo app('translator')->get('message.dashboard.button.change-picture'); ?>
                                                </a>
                                                <span id="file-error" style="display: none;"></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            <h4><?php echo app('translator')->get('message.dashboard.setting.change-password'); ?></h4>
                                            <p><?php echo app('translator')->get('message.dashboard.setting.change-password-here'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-cust" data-toggle="modal"
                                                    data-target="#myModal" id="showmodal">
                                                <?php echo app('translator')->get('message.dashboard.button.change-password'); ?>
                                            </button>

                                            <!-- The Modal -->
                                            <div class="modal" id="myModal">
                                                <div class="modal-dialog">
                                                    <form method="post" action="<?php echo e(url('prifile/update_password')); ?>" id="reset_password">
                                                        <?php echo e(csrf_field()); ?>


                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title"><?php echo app('translator')->get('message.dashboard.setting.change-password'); ?></h4>
                                                                <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">

                                                                <div class="form-group">
                                                                    <label><?php echo app('translator')->get('message.dashboard.setting.old-password'); ?></label>
                                                                    <input class="form-control" name="old_password"
                                                                           id="old_password" type="password">
                                                                    <?php if($errors->has('old_password')): ?>
                                                                        <span class="error">
                                                                         <?php echo e($errors->first('old_password')); ?>

                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="clearfix"></div>

                                                                <div class="form-group">
                                                                    <label><?php echo app('translator')->get('message.dashboard.setting.new-password'); ?></label>
                                                                    <input class="form-control" name="password"
                                                                           id="password" type="password">
                                                                    <?php if($errors->has('password')): ?>
                                                                        <span class="error">
                                                                         <?php echo e($errors->first('password')); ?>

                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="form-group">
                                                                    <label><?php echo app('translator')->get('message.dashboard.setting.confirm-password'); ?></label>
                                                                    <input class="form-control" name="confirm_password"
                                                                           id="confirm_password" type="password">
                                                                    <?php if($errors->has('confirm_password')): ?>
                                                                        <span class="error">
                                                                         <?php echo e($errors->first('confirm_password')); ?>

                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <!-- Modal footer -->
                                                            <div class="modal-footer">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <button type="button" class="btn btn-cust closes" data-dismiss="modal"><?php echo app('translator')->get('message.form.close'); ?></button>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <button type="submit" class="btn btn-cust"><?php echo app('translator')->get('message.dashboard.button.submit'); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <?php if(empty($user->phone)): ?>

                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="row">
                                            <div class="col-md-2">
                                                <img src="<?php echo e(url('public/user_dashboard/images/phone-icon.png')); ?>" class="rounded-circle rounded-circle-custom-trans">
                                            </div>
                                            <div class="col-md-6">
                                                <h4 class="addPhoneTitle"><?php echo app('translator')->get('message.dashboard.setting.add-phone'); ?></h4>
                                                <p class="addPhoneBody"><?php echo app('translator')->get('message.dashboard.setting.add-phone-subhead1'); ?> <b>+</b> <?php echo app('translator')->get('message.dashboard.setting.add-phone-subhead2'); ?></p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="uploadAvatar">
                                                    <button type="button" class="btn btn-secondary btn-border btn-sm add" data-toggle="modal" data-target="#add" style="margin-top: 10px;">
                                                        <i class="fa fa-plus" id="modalTextSymbol"></i>
                                                        <span class="modalText">&nbsp; <?php echo app('translator')->get('message.dashboard.setting.add-phone'); ?></span>
                                                    </button>
                                                </div>

                                                <!-- Add Phone Modal -->
                                                <div class="modal" id="add">
                                                    <div class="modal-dialog">

                                                        <form method="POST" action="<?php echo e(url('profile/complete-phone-verification')); ?>" id="complete-phone-verification">
                                                            <?php echo e(csrf_field()); ?>

                                                            <input type="hidden" value="<?php echo e($is_sms_env_enabled); ?>" name="is_sms_env_enabled" id="is_sms_env_enabled" />
                                                            <input type="hidden" value="<?php echo e($checkPhoneVerification); ?>" name="checkPhoneVerification" id="checkPhoneVerification" />

                                                            <input type="hidden" value="<?php echo e($user->id); ?>" name="user_id" id="user_id" />
                                                            <input type="hidden" name="hasVerificationCode" id="hasVerificationCode" />

                                                            <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                                                            <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                                                            <input type="hidden" name="countryName" id="countryName" class="form-control">


                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title"><?php echo app('translator')->get('message.dashboard.setting.add-phone'); ?></h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">

                                                                    <div class="alert text-center" id="message" style="display: none"></div>

                                                                    <div class="form-group">
                                                                        <label id="subheader_text"><?php echo app('translator')->get('message.dashboard.setting.add-phone-subheadertext'); ?></label>
                                                                        <br>
                                                                        <div class="phone_group">
                                                                            <input type="tel" class="form-control" id="phone" name="phone">
                                                                        </div>
                                                                        <span id="phone-number-error"></span>
                                                                        <span id="tel-number-error"></span>

                                                                    </div>
                                                                    <div class="clearfix"></div>

                                                                    <div class="form-group">
                                                                        <label></label>
                                                                        <input id="phone_verification_code" type="text" maxlength="6" class="form-control" name="phone_verification_code"
                                                                        style="display: none;width: 46%;">
                                                                    </div>
                                                                    <div class="clearfix"></div>

                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <div style="margin-top: 6px;">
                                                                                <span id="static_phone_show" class="static_phone_show" style="display: none;"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-sm btn-cust edit" style="display: none;"><i class="fa fa-edit"></i></button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Modal footer -->
                                                                <div class="modal-footer">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <button type="button" class="btn btn-cust" data-dismiss="modal" id="close"><?php echo app('translator')->get('message.form.cancel'); ?></button>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <button type="button" class="btn btn-cust next" id="common_button"><?php echo app('translator')->get('message.dashboard.button.next'); ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="preloader" style="display: none;">
                                                        <div class="preloader-img"></div>
                                                    </div>
                                                    <div class="user-profile-qr-code">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-cust" id="qr-code-btn">
                                                    </button>
                                                    <br>
                                                    <br>
                                                    <a href="#" class="btn btn-cust mr-auto" id="print-qr-code-btn" style="display: none;width: 158px;">
                                                        
                                                            Print QR Code
                                                        
                                                    </a>
                                                    <!-- The Modal -->
                                                </div>
                                            </div>
                                        </div>
                                    
                                </div>
                                <hr>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <img src="<?php echo e(url('public/user_dashboard/images/phone-icon.png')); ?>" class="rounded-circle rounded-circle-custom-trans">
                                            </div>
                                            <div class="col-md-6">
                                                <h4 class="editPhoneTitle"><?php echo app('translator')->get('message.dashboard.setting.phone-number'); ?></h4>
                                                <p class="editPhoneBody"><?php echo e(auth()->user()->phone); ?></p>
                                            </div>
                                            <div class="col-md-2">
                                                <!--<div class="uploadAvatar">-->
                                                <!--    <button type="button" class="btn btn-secondary btn-border btn-sm editModal" data-toggle="modal" data-target="#editModal" style="margin-top: 10px;">-->
                                                <!--        <i class="fa fa-edit"></i>-->
                                                <!--        <span>&nbsp; <?php echo app('translator')->get('message.dashboard.setting.edit-phone'); ?></span>-->
                                                <!--    </button>-->

                                                <!--</div>-->
                                                <!-- The Modal -->
                                                <div class="modal" id="editModal">
                                                    <div class="modal-dialog">

                                                        <form method="POST" action="<?php echo e(url('profile/update-phone-number')); ?>" id="update-phone-number">
                                                            <?php echo e(csrf_field()); ?>

                                                            <input type="hidden" value="<?php echo e($is_sms_env_enabled); ?>" name="is_sms_env_enabled" id="is_sms_env_enabled">
                                                            <input type="hidden" value="<?php echo e($user->id); ?>" name="user_id" id="user_id">

                                                            <input type="hidden" value="<?php echo e($checkPhoneVerification); ?>" name="editCheckPhoneVerification" id="editCheckPhoneVerification" />
                                                            <input type="hidden" name="editHasVerificationCode" id="editHasVerificationCode" />

                                                            <input type="hidden" name="edit_defaultCountry" id="edit_defaultCountry" value="<?php echo e($user->defaultCountry); ?>">
                                                            <input type="hidden" name="edit_carrierCode" id="edit_carrierCode" value="<?php echo e($user->carrierCode); ?>">

                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title"><?php echo app('translator')->get('message.dashboard.setting.edit-phone'); ?></h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body editModalBody">
                                                                    <div class="alert text-center" id="message" style="display: none"></div>

                                                                    <div class="form-group">
                                                                        <label id="subheader_edit_text"><?php echo app('translator')->get('message.dashboard.setting.add-phone-subheadertext'); ?></label>
                                                                        <br>
                                                                        <div class="phone_group">
                                                                            <input type="tel" class="form-control" id="edit_phone" name="edit_phone" value="<?php echo e('+'.$user->carrierCode.$user->phone); ?>">
                                                                        </div>
                                                                        <span id="edit-phone-number-error"></span>
                                                                        <span id="edit-tel-number-error"></span>
                                                                    </div>
                                                                    <div class="clearfix"></div>

                                                                    <div class="form-group">
                                                                        <label></label>
                                                                        <input id="edit_phone_verification_code" type="text" maxlength="6" class="form-control" name="edit_phone_verification_code"
                                                                        style="display: none;width: 46%;">
                                                                    </div>
                                                                    <div class="clearfix"></div>

                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <div style="margin-top: 6px;">
                                                                                <span id="edit_static_phone_show" class="edit_static_phone_show" style="display: none;"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-sm btn-cust edit_button_edit" style="display: none;"><i class="fa fa-edit"></i></button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Modal footer -->
                                                                <div class="modal-footer">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <button type="button" class="btn btn-cust" data-dismiss="modal" id="close"><?php echo app('translator')->get('message.form.cancel'); ?></button>
                                                                        </div>

                                                                        <div class="col-md-6">

                                                                            <?php
                                                                                $bothDisabled = ($is_sms_env_enabled == false && $checkPhoneVerification == "Disabled");
                                                                            ?>

                                                                            <?php if($bothDisabled || $checkPhoneVerification == "Disabled"): ?>
                                                                                <button type="button" class="btn btn-cust edit_form_submit" id="common_button_update"><?php echo app('translator')->get('message.form.update'); ?></button>
                                                                            <?php else: ?>
                                                                                <button type="button" class="btn btn-cust update" id="common_button_update"><?php echo app('translator')->get('message.dashboard.button.next'); ?></button>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="preloader" style="display: none;">
                                                        <div class="preloader-img"></div>
                                                    </div>
                                                    <div class="user-profile-qr-code">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-cust update-qr-code" id="qr-code-btn">
                                                        <?php echo app('translator')->get('message.dashboard.button.update-qr-code'); ?>
                                                    </button>
                                                    <br>
                                                    <br>
                                                    <a href="#" class="btn btn-cust mr-auto" id="print-qr-code-btn" style="display: none;width: 158px;">
                                                        
                                                            Print QR Code
                                                       
                                                    </a>
                                                    <!-- The Modal -->
                                                </div>
                                            </div>
                                        </div>
                                    
                                </div>
                                <hr>
                            <?php endif; ?>

                            <div class="clearfix"></div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4><?php echo app('translator')->get('message.dashboard.setting.profile-information'); ?></h4>
                                    <hr>
                                        <form method="post" action="<?php echo e(url('prifile/update')); ?>" id="profile_update_form">
                                            <?php echo e(csrf_field()); ?>


                                            <input type="hidden" value="<?php echo e($user->id); ?>" name="id" id="id" />

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="first_name"><?php echo app('translator')->get('message.dashboard.setting.first-name'); ?>
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                                           value="<?php echo e($user->first_name); ?>">
                                                    <?php if($errors->has('first_name')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('first_name')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="last_name"><?php echo app('translator')->get('message.dashboard.setting.last-name'); ?>
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                                           value="<?php echo e($user->last_name); ?>">
                                                    <?php if($errors->has('last_name')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('last_name')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="email"><?php echo app('translator')->get('message.dashboard.setting.email'); ?>
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" id="email" class="form-control" value="<?php echo e($user->email); ?>" readonly>
                                                </div>
                                                <!-- Default Wallet -->
                                                <div class="form-group col-md-6">
                                                    <label for="email"><?php echo app('translator')->get('message.dashboard.setting.default-wallet'); ?>
                                                    </label>
                                                    <select class="form-control" name="default_wallet" id="default_wallet">
                                                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($wallet->id); ?>" <?php echo e($wallet->is_default == 'Yes' ? 'Selected' : ''); ?>><?php echo e($wallet->currency->code); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="address_1"><?php echo app('translator')->get('message.dashboard.setting.address1'); ?></label>
                                                    <textarea class="form-control" name="address_1"
                                                              id="address_1"><?php echo e($user->user_detail->address_1); ?></textarea>
                                                    <?php if($errors->has('address_1')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('address_1')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="address_2"><?php echo app('translator')->get('message.dashboard.setting.address2'); ?></label>
                                                    <textarea class="form-control" name="address_2"
                                                              id="address_2"><?php echo e($user->user_detail->address_2); ?></textarea>
                                                    <?php if($errors->has('address_2')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('address_2')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>


                                            

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="city"><?php echo app('translator')->get('message.dashboard.setting.city'); ?></label>

                                                    <input type="text" class="form-control" name="city" id="city"
                                                           value="<?php echo e($user->user_detail->city); ?>">
                                                    <?php if($errors->has('city')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('city')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="state"><?php echo app('translator')->get('message.dashboard.setting.state'); ?></label>
                                                    <input type="text" class="form-control" name="state" id="state" value="<?php echo e($user->user_detail->state); ?>">
                                                    <?php if($errors->has('state')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('state')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="postal_code"><?php echo app('translator')->get('message.dashboard.setting.postal_code'); ?></label>
                                                    <input type="text" class="form-control" name="postal_code" id="postal_code" value="<?php echo e($user->user_detail->zip_code); ?>">
                                                   
                                                    <?php if($errors->has('postal_code')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('postal_code')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="country_id"><?php echo app('translator')->get('message.dashboard.setting.country'); ?></label>
                                                    <select class="form-control" name="country_id" id="country_id">
                                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($country->id); ?>" <?= ($user->user_detail->country_id == $country->id) ? 'selected' : '' ?> ><?php echo e($country->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <?php if($errors->has('country_id')): ?>
                                                        <span class="error">
                                                           <?php echo e($errors->first('country_id')); ?>

                                                          </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <!--<div class="row">-->
                                            <!--    <div class="form-group col-md-6">-->
                                            <!--        <label for="dob"><?php echo app('translator')->get('message.dashboard.setting.dob'); ?></label>-->
                                            <!--        <div class="input-group date">-->
                                            <!--            <input type="date" class="form-control datepicker"-->
                                            <!--                   id="dob" name="dob"-->
                                            <!--                   value="<?php echo e($user->dob); ?>"-->
                                            <!--                   required="required">-->
                                            <!--        </div>-->
                                            <!--        <?php if($errors->has('dob')): ?>-->
                                            <!--            <span class="error">-->
                                            <!--               <?php echo e($errors->first('dob')); ?>-->
                                            <!--              </span>-->
                                            <!--        <?php endif; ?>-->
                                            <!--    </div>-->
                                            <!--    <div class="form-group col-md-6">-->
                                            <!--        <label for="timezone"><?php echo app('translator')->get('message.dashboard.setting.timezone'); ?></label>-->

                                            <!--        <select class="form-control" name="timezone" id="timezone">-->
                                            <!--            <?php $__currentLoopData = $timezones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>-->
                                            <!--              <option value="<?php echo e($timezone['zone']); ?>" <?php echo e(($user->user_detail->timezone == $timezone['zone']) ? 'selected' : ''); ?>>-->
                                            <!--                <?php echo e($timezone['diff_from_GMT'] . ' - ' . $timezone['zone']); ?>-->
                                            <!--              </option>-->
                                            <!--            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->
                                            <!--        </select>-->

                                            <!--        <?php if($errors->has('timezone')): ?>-->
                                            <!--            <span class="error">-->
                                            <!--               <?php echo e($errors->first('timezone')); ?>-->
                                            <!--              </span>-->
                                            <!--        <?php endif; ?>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            
                                            <!--<div class="row">-->
                                            <!--    <div class="form-group col-md-6">-->
                                            <!--        <label for="nationality"><?php echo app('translator')->get('message.dashboard.setting.nationality'); ?></label>-->
                                            <!--        <input type="text" class="form-control" name="nationality" id="nationality" value="<?php echo e($user->nationality); ?>">-->
                                            <!--        <?php if($errors->has('nationality')): ?>-->
                                            <!--            <span class="error">-->
                                            <!--               <?php echo e($errors->first('nationality')); ?>-->
                                            <!--              </span>-->
                                            <!--        <?php endif; ?>-->
                                            <!--    </div>-->
                                            <!--    <div class="form-group col-md-6">-->
                                            <!--        <label for="job_title"><?php echo app('translator')->get('message.dashboard.setting.job_title'); ?></label>-->
                                            <!--        <input type="text" class="form-control" name="job_title" id="job_title" value="<?php echo e($user->job_title); ?>">-->

                                            <!--        <?php if($errors->has('job_title')): ?>-->
                                            <!--            <span class="error">-->
                                            <!--               <?php echo e($errors->first('job_title')); ?>-->
                                            <!--              </span>-->
                                            <!--        <?php endif; ?>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            
                                            <!--<div class="row">-->
                                            <!--    <div class="form-group col-md-6">-->
                                            <!--        <label for="id_type"><?php echo app('translator')->get('message.dashboard.setting.id_type'); ?></label>-->
                                            <!--        <select name="id_type" id="id_type" class="form-control">-->
                                            <!--            <option value=""><?php echo app('translator')->get('message.personal-id.select-type'); ?></option>-->
                                            <!--            <option value="driving_license"-->
                                            <!--            <?php echo e(!empty($user->id_type) && $user->id_type == 'driving_license' ? 'selected' : ''); ?>>-->
                                            <!--                <?php echo app('translator')->get('message.personal-id.driving-license'); ?>-->
                                            <!--            </option>-->
                                            <!--            <option value="passport" <?php echo e(!empty($user->id_type) && $user->id_type == 'passport' ? 'selected' : ''); ?>><?php echo app('translator')->get('message.personal-id.passport'); ?></option>-->
                                            <!--            <option value="national_id" <?php echo e(!empty($user->id_type) && $user->id_type == 'national_id' ? 'selected' : ''); ?>><?php echo app('translator')->get('message.personal-id.national-id'); ?></option>-->
                                            <!--        </select>-->
                                            <!--        <?php if($errors->has('id_type')): ?>-->
                                            <!--            <span class="error">-->
                                            <!--               <?php echo e($errors->first('id_type')); ?>-->
                                            <!--              </span>-->
                                            <!--        <?php endif; ?>-->
                                            <!--    </div>-->
                                            <!--    <div class="form-group col-md-6">-->
                                            <!--        <label for="id_number"><?php echo app('translator')->get('message.dashboard.setting.id_number'); ?></label>-->
                                            <!--        <input type="number" name="id_number" class="form-control" value="<?php echo e(!empty($user->id_number) ? $user->id_number : ''); ?>">-->

                                            <!--        <?php if($errors->has('id_number')): ?>-->
                                            <!--            <span class="error">-->
                                            <!--               <?php echo e($errors->first('id_number')); ?>-->
                                            <!--              </span>-->
                                            <!--        <?php endif; ?>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            
                                            <div class="clearfix"></div>
                                            <br />

                                            

                                            <div class="row">
                                                <div class="form-group col-md-12" style="    text-align: center;">
                                                    <button type="submit" class="btn btn-cust ncust col-12" id="users_profile">
                                                        <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_profile_text"><?php echo app('translator')->get('message.dashboard.button.submit'); ?></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                </div>
                            </div>
                            <hr>
                            <div class="clearfix"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<script src="<?php echo e(asset('public/user_dashboard/js/jquery.validate.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/additional-methods.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/isValidPhoneNumber.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('public/user_dashboard/js/sweetalert/sweetalert-unpkg.min.js')); ?>" type="text/javascript"></script>
<script>

$(document).on('click','#showmodal',function(){
    $('#myModal').css('display','block');
});
$(document).on('click','.close,.closes',function(){
    $('#myModal').hide();
});
//////////////////QrCode Print - starts////////////////////////////////

$(document).on('click','#print-qr-code-btn',function(e)
    {
        e.preventDefault();
        let userId = <?php echo e(auth()->user()->id); ?>;
        let printQrCodeUrl = SITE_URL+'/profile/qr-code-print/'+userId+'/user';
        $(this).attr('href', printQrCodeUrl);
        window.open($(this).attr('href'), '_blank');
    });
    //////////////////QrCode Print - ends////////////////////////////////


    //////////////////QrCode - starts////////////////////////////////
            //show user's qr-code on window load
            $(window).on('load', function()
            {
                // $('.preloader').show();
                swal('Please Wait', 'Loading...', {
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    buttons: false,
                    timer: 2000,
                });
                var QrCodeSecret = '<?php echo e(!empty($QrCodeSecret) ? $QrCodeSecret : ''); ?>';
                if (QrCodeSecret != '')
                {
                    // setTimeout(function(){
                    //     $('.preloader').hide();
                    // },1000);
                    $('.user-profile-qr-code').html(`<img src="https://api.qrserver.com/v1/create-qr-code/?data=${QrCodeSecret}&amp;size=200x200"/>`);
                    $("#qr-code-btn").removeClass('add-qr-code').addClass('update-qr-code').text('Update QR Code');
                    $("#print-qr-code-btn").show();
                }
                else
                {
                    // $('.user-profile-qr-code').html(`<img src=""/>`);
                    $(".user-profile-qr-code").html(`<img class="" src="${SITE_URL}/public/images/profile/No-image-found.jpg" class="img-responsive"/>`);
                    $("#qr-code-btn").addClass('add-qr-code').text('Add QR Code');
                }

            });
            //

            function addOrUpdateQrCode()
            {
                let user_id = $('#user_id').val();

                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL + "/profile/qr-code/add-or-update",
                    dataType: "json",
                    data: {
                        'user_id': user_id,
                    },
                    beforeSend: function () {
                        // $('.preloader').show();
                        swal('Please Wait', 'Loading...', {
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            buttons: false,
                            timer: 2000,
                        });
                    },
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        // setTimeout(function(){
                        //     $('.preloader').hide();
                        // },1000);

                        // $('.preloader').hide();
                        $('.user-profile-qr-code').html(`<img src="https://api.qrserver.com/v1/create-qr-code/?data=${response.secret}&amp;size=200x200"/>`);
                    }
                })
                .fail(function(error)
                {
                    console.log(error);
                });
            }

            // UPDATE USER's QR CODE
            $(document).on('click', '.update-qr-code', function(e)
            {
                e.preventDefault();
                addOrUpdateQrCode();
            });

            // ADD USER's QR CODE
            $(document).on('click', '.add-qr-code', function(e)
            {
                e.preventDefault();
                addOrUpdateQrCode();
                $("#qr-code-btn").removeClass('add-qr-code').addClass('update-qr-code').text('Update QR Code');
            });
    //////////////////QrCode - ends////////////////////////////////

////////////////////////////////////////////////////////////////
        //Add
            //reload on close of phone add modal
            $('#add').on('hidden.bs.modal', function ()
            {
                if ($("#phone").val() != '')
                {
                    $(this).find("input").val('').end(); //reset input
                    $('#complete-phone-verification').validate().resetForm(); //reset validation messages
                    window.location.reload();
                }
            });

            /*
            intlTelInput - add
            */
            $(document).ready(function()
            {
                $("#phone").intlTelInput({
                    separateDialCode: true,
                    nationalMode: true,
                    preferredCountries: ["us"],
                    autoPlaceholder: "polite",
                    placeholderNumberType: "MOBILE",
                    utilsScript: "public/user_dashboard/js/intl-tel-input-13.0.0/build/js/utils.js"
                });

                var countryData = $("#phone").intlTelInput("getSelectedCountryData");
                $('#defaultCountry').val(countryData.iso2);
                $('#carrierCode').val(countryData.dialCode);

                $("#phone").on("countrychange", function(e, countryData)
                {
                    // log(countryData);
                    $('#defaultCountry').val(countryData.iso2);
                    $('#carrierCode').val(countryData.dialCode);

                    if ($.trim($(this).val()))
                    {
                        if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                        {
                            // alert('invalid');
                            $('#tel-number-error').addClass('error').html("<?php echo e(__("Please enter a valid International Phone Number.")); ?>").css({
                               'color' : 'red !important',
                               'font-size' : '14px',
                               'font-weight' : '800',
                               'padding-top' : '5px',
                            });
                            $('#common_button').prop('disabled',true);
                            $('#phone-number-error').hide();
                        }
                        else
                        {
                            $('#tel-number-error').html('');

                            var id = $('#id').val();
                            $.ajax({
                                headers:
                                {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                method: "POST",
                                url: SITE_URL+"/profile/duplicate-phone-number-check",
                                dataType: "json",
                                cache: false,
                                data: {
                                    'phone': $.trim($(this).val()),
                                    'carrierCode': $.trim(countryData.dialCode),
                                    'id': id,
                                }
                            })
                            .done(function(response)
                            {
                                if (response.status == true)
                                {
                                    $('#tel-number-error').html('');
                                    $('#phone-number-error').show();

                                    $('#phone-number-error').addClass('error').html(response.fail).css({
                                       'color' : 'red !important',
                                       'font-size' : '14px',
                                       'font-weight' : '800',
                                       'padding-top' : '5px',
                                    });
                                    $('#common_button').prop('disabled',true);
                                }
                                else if (response.status == false)
                                {
                                    $('#tel-number-error').show();
                                    $('#phone-number-error').html('');

                                    $('#common_button').prop('disabled',false);
                                }
                            });
                        }
                    }
                    else
                    {
                        $('#tel-number-error').html('');
                        $('#phone-number-error').html('');
                        $('#common_button').prop('disabled',false);
                    }
                });
            });
            /*
            intlTelInput - add
            */

            //Invalid Number Validation - add
            $(document).ready(function()
            {
                $("#phone").on('blur', function(e)
                {
                    if ($.trim($(this).val()))
                    {
                        if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                        {
                            // alert('invalid');
                            $('#tel-number-error').addClass('error').html("<?php echo e(__("Please enter a valid International Phone Number.")); ?>").css({
                               'color' : 'red !important',
                               'font-size' : '14px',
                               'font-weight' : '800',
                               'padding-top' : '5px',
                            });
                            $('#common_button').prop('disabled',true);
                            $('#phone-number-error').hide();
                        }
                        else
                        {
                            var id = $('#id').val();
                            var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
                            var phone = $(this).val().replace(/^0+/, ""); //replaces (leading zero - for BD phone number)
                            // log(phone);

                            var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;

                            if(phone.length == 0)
                            {
                                $('#phone-number-error').addClass('error').html("<?php echo e(__("This field is required.")); ?>").css({
                                   'color' : 'red !important',
                                   'font-size' : '14px',
                                   'font-weight' : '800',
                                   'padding-top' : '5px',
                                });
                                $('#common_button').prop('disabled',true);
                            }
                            else
                            {
                                $('#phone-number-error').hide();
                                $('#common_button').prop('disabled',false);
                            }

                            $.ajax({
                                headers:
                                {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                method: "POST",
                                url: SITE_URL+"/profile/duplicate-phone-number-check",
                                dataType: "json",
                                cache: false,
                                data: {
                                    'phone': phone,
                                    'id': id,
                                    'carrierCode': pluginCarrierCode,
                                }
                            })
                            .done(function(response)
                            {
                                $('#phone-number-error').show();
                                if (response.status == true)
                                {
                                    if(phone.length == 0)
                                    {
                                        $('#phone-number-error').html('');
                                    }
                                    else
                                    {
                                        $('#phone-number-error').addClass('error').html(response.fail).css({
                                           'color' : 'red !important',
                                           'font-size' : '14px',
                                           'font-weight' : '800',
                                           'padding-top' : '5px',
                                        });
                                        $('#common_button').prop('disabled',true);
                                    }
                                }
                                else if (response.status == false)
                                {
                                    $('#common_button').prop('disabled',false);
                                    $('#phone-number-error').html('');
                                }
                            });
                            $('#tel-number-error').html('');
                            $('#phone-number-error').show();
                            $('#common_button').prop('disabled',false);
                        }
                    }
                    else
                    {
                        $('#tel-number-error').html('');
                        $('#phone-number-error').html('');
                        $('#common_button').prop('disabled',false);
                    }
                });
            });


            //is_sms_env_enabled and phone verification check
            $(document).ready(function()
            {
                var is_sms_env_enabled = $('#is_sms_env_enabled').val();
                var checkPhoneVerification = $('#checkPhoneVerification').val();

                if ((is_sms_env_enabled != true && checkPhoneVerification != "Enabled") || checkPhoneVerification != "Enabled")
                {
                    $('.next').removeClass("next").addClass('form_submit').html("<?php echo e(__("Submit")); ?>");
                }
                else
                {
                    $('.next').removeClass("form_submit").addClass('next').html("<?php echo e(__("Next")); ?>");
                }
            });

            // next
            $(document).on('click', '.next', function()
            {
                var phone = $("input[name=phone]");
                if (phone.val() == '')
                {
                    $('#phone-number-error').addClass('error').html("<?php echo e(__("This field is required.")); ?>").css({
                       'color' : 'red !important',
                       'font-size' : '14px',
                       'font-weight' : '800',
                       'padding-top' : '5px',
                    });
                    return false;
                }
                else if(phone.hasClass('error'))
                {
                    return false;
                }
                else
                {
                    $('.modal-title').html("<?php echo e(__("Get Code")); ?>");
                    $('#subheader_text').html('<?php echo e(__('To make sure this number is yours, we will send you a verification code.')); ?>');
                    $('.phone_group').hide();
                    $('#static_phone_show').show();
                    $('.edit').show();

                    $(this).removeClass("next").addClass("get_code").html("<?php echo e(__("Get Code")); ?>");
                    var fullPhone = $("#phone").intlTelInput("getNumber");
                    $('#static_phone_show').html(fullPhone + '&nbsp;&nbsp;');
                    return true;
                }
            });

            //edit - add_phone
            $(document).on('click', '.edit', function()
            {
                $('.get_code').removeClass("get_code").addClass("next").html("<?php echo e(__("Next")); ?>");
                $('.static_phone_show').html('');
                $(this).hide();
                $('#subheader_text').html('<?php echo e(__('Enter the number you’d like to use')); ?>');
                $('.phone_group').show();
            });


            //get_code
            $(document).on('click', '.get_code', function()
            {
                $('.modal-title').html("<?php echo e(__("Verify Phone")); ?>");
                $('.phone_group').hide();
                $('.static_phone_show').html('');

                $('#subheader_text').html('We just sent you a SMS with a code.'+ '<br><br>' + 'Enter it to verify your phone.');

                $('#subheader_text').html('<?php echo e(__("We just sent you a SMS with a code.")); ?>'+ '<br><br>' + '<?php echo e(__("Enter it to verify your phone.")); ?>');

                $('.edit').hide();
                $('#phone_verification_code').show().val('');
                $(this).removeClass("get_code").addClass("verify").html("<?php echo e(__("Verify")); ?>");

                var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                var pluginPhone = $("#phone").intlTelInput("getNumber");

                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/profile/getVerificationCode",
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': pluginPhone,
                        'carrierCode': pluginCarrierCode,
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        $('#hasVerificationCode').val(response.message);
                    }
                });
            });

            //verify
            $(document).on('click', '.verify', function()
            {
                var classOfSubmit = $('#common_button');
                var phone_verification_code = $("#phone_verification_code").val();

                var pluginPhone = $("#phone").intlTelInput("getNumber");
                var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                var pluginDefaultCountry = $('#phone').intlTelInput('getSelectedCountryData').iso2;

                if (classOfSubmit.hasClass('verify'))
                {
                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: SITE_URL+"/profile/complete-phone-verification",
                        dataType: "json",
                        cache: false,
                        data: {
                            'phone': pluginPhone,
                            'defaultCountry': pluginDefaultCountry,
                            'carrierCode': pluginCarrierCode,
                            'phone_verification_code': phone_verification_code,
                        }
                    })
                    .done(function(data)
                    {
                        if (data.status == false || data.status == 500)
                        {
                            $('#message').css('display', 'block');
                            $('#message').html(data.message);
                            $('#message').addClass(data.error);
                        }
                        else
                        {

                            $('#message').removeClass('alert-danger');
                            $('#message').css('display', 'block');
                            $('#message').html(data.message);
                            $('#message').addClass(data.success);

                            $('#subheader_text').hide();
                            $('#phone_verification_code').hide();
                            $('#common_button').hide();
                            $('#close').hide();
                            $('.modal-title').hide();
                        }
                    });
                }
            });

            //form_submit
            $(document).on('click', '.form_submit', function()
            {
                var classOfSubmit = $('#common_button');
                if (classOfSubmit.hasClass('form_submit'))
                {
                    var pluginPhone = $("#phone").intlTelInput("getNumber");
                    var pluginDefaultCountry = $('#phone').intlTelInput('getSelectedCountryData').iso2;
                    var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;

                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: SITE_URL+"/profile/add-phone-number",
                        dataType: "json",
                        cache: false,
                        data: {
                            'phone': pluginPhone,
                            'defaultCountry': pluginDefaultCountry,
                            'carrierCode': pluginCarrierCode,
                        }
                    })
                    .done(function(data)
                    {
                        if (data.status == true)
                        {
                            $('#message').css('display', 'block');
                            $('#message').html(data.message);
                            $('#message').addClass(data.class_name);

                            $('#subheader_text').hide();
                            $('#common_button').hide();
                            $('#close').hide();
                            $('.phone_group').hide();
                        }
                    });
                }
            });
////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////
    //Update

        //clear inputs on close - edit modal
        $('#editModal').on('hidden.bs.modal', function () {
            if ($("#edit_phone").val() != '')
            {
                var OrginalUsercarrierCode = '<?php echo e($user->carrierCode); ?>';
                var OrginalUserphone = '<?php echo e($user->phone); ?>';
                $("#edit_phone").val(`+${OrginalUsercarrierCode}${OrginalUserphone}`)
                window.location.reload(); //need to reload - or validation message still exists.
            }
        });

         /*
        intlTelInput - edit
        */
        $(document).ready(function()
        {
            $("#edit_phone").intlTelInput({
                separateDialCode: true,
                nationalMode: true,
                preferredCountries: ["us"],
                autoPlaceholder: "polite",
                placeholderNumberType: "MOBILE",
                formatOnDisplay: false,
                utilsScript: "public/user_dashboard/js/intl-tel-input-13.0.0/build/js/utils.js"
            })
            .done(function()
            {
                let carrierCode = '<?php echo e(!empty($user->carrierCode) ? $user->carrierCode : NULL); ?>';
                let defaultCountry = '<?php echo e(!empty($user->defaultCountry) ? $user->defaultCountry : NULL); ?>';
                let formattedPhone = '<?php echo e(!empty($user->formattedPhone) ? $user->formattedPhone : NULL); ?>';
                if (formattedPhone !== null && carrierCode !== null && defaultCountry !== null) {
                    $("#edit_phone").intlTelInput("setNumber", formattedPhone);
                    $('#edit_defaultCountry').val(defaultCountry);
                    $('#edit_carrierCode').val(carrierCode);
                }
            });
        });

        var editCountryData = $("#edit_phone").intlTelInput("getSelectedCountryData");
        $("#edit_phone").on("countrychange", function(e, editCountryData)
        {
            // log(editCountryData);
            $('#edit_defaultCountry').val(editCountryData.iso2);
            $('#edit_carrierCode').val(editCountryData.dialCode);

            if ($.trim($(this).val()))
            {
                if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                {
                    // alert('invalid');
                    $('#edit-tel-number-error').addClass('error').html("<?php echo e(__("Please enter a valid International Phone Number.")); ?>").css({
                       'color' : 'red !important',
                       'font-size' : '14px',
                       'font-weight' : '800',
                       'padding-top' : '5px',
                    });
                    $('#common_button_update').prop('disabled',true);
                    $('#edit-phone-number-error').hide();
                }
                else
                {
                    $('#edit-tel-number-error').html('');

                    var id = $('#id').val();
                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: SITE_URL+"/profile/duplicate-phone-number-check",
                        dataType: "json",
                        cache: false,
                        data: {
                            'phone': $.trim($(this).val()),
                            'carrierCode': $.trim(countryData.dialCode),
                            'id': id,
                        }
                    })
                    .done(function(response)
                    {
                        if (response.status == true)
                        {
                            $('#edit-tel-number-error').html('');
                            $('#edit-phone-number-error').show();

                            $('#edit-phone-number-error').addClass('error').html(response.fail).css("font-weight", "bold");
                            $('#common_button_update').prop('disabled',true);
                        }
                        else if (response.status == false)
                        {
                            $('#edit-tel-number-error').show();
                            $('#edit-phone-number-error').html('');

                            $('#common_button_update').prop('disabled',false);
                        }
                    });
                }
            }
            else
            {
                $('#edit-tel-number-error').html('');
                $('#edit-phone-number-error').html('');
                $('#common_button_update').prop('disabled',false);
            }
        });

        //Invalid Number Validation - user edit
        $(document).ready(function()
        {
            $("#edit_phone").on('blur', function(e)
            {
                if ($.trim($(this).val()))
                {
                    if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                    {
                        // alert('invalid');
                        $('#edit-tel-number-error').addClass('error').html("<?php echo e(__("Please enter a valid International Phone Number.")); ?>").css({
                           'color' : 'red !important',
                           'font-size' : '14px',
                           'font-weight' : '800',
                           'padding-top' : '5px',
                        });
                        $('#common_button_update').prop('disabled',true);
                        $('#edit-phone-number-error').hide();
                    }
                    else
                    {
                        var id = $('#user_id').val();

                        var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
                        var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)

                        var pluginCarrierCode = $(this).intlTelInput('getSelectedCountryData').dialCode;

                        $.ajax({
                            headers:
                            {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: "POST",
                            url: SITE_URL+"/profile/duplicate-phone-number-check",
                            dataType: "json",
                            cache: false,
                            data: {
                                'id': id,
                                // 'phone': $.trim(phone),
                                'phone': phone,
                                'carrierCode': $.trim(pluginCarrierCode),
                            }
                        })
                        .done(function(response)
                        {
                            if (response.status == true)
                            {
                                if(phone.length == 0)
                                {
                                    $('#edit-phone-number-error').html('');
                                }
                                else
                                {
                                    $('#edit-phone-number-error').addClass('error').html(response.fail).css({
                                       'color' : 'red !important',
                                       'font-size' : '14px',
                                       'font-weight' : '800',
                                       'padding-top' : '5px',
                                    });
                                    $('#common_button_update').prop('disabled',true);
                                }
                            }
                            else if (response.status == false)
                            {
                                $('#common_button_update').prop('disabled',false);
                                $('#edit-phone-number-error').html('');
                            }
                        });
                        $('#edit-tel-number-error').html('');
                        $('#edit-phone-number-error').show();
                        $('#common_button_update').prop('disabled',false);
                    }
                }
                else
                {
                    $('#edit-tel-number-error').html('');
                    $('#edit-phone-number-error').html('');
                    $('#common_button_update').prop('disabled',false);
                }
            });
        });

        // Duplicate Validate phone via Ajax - update

         /*
        intlTelInput - edit
        */

        //when phone verificaiton is enabled
        $(document).on('click', '.update', function()
        {
            var phone = $("input[name=edit_phone]");
            if (phone.val() == '')
            {
                $('#edit-phone-number-error').addClass('error').html("<?php echo e(__("This field is required.")); ?>").css({
                   'color' : 'red !important',
                   'font-size' : '14px',
                   'font-weight' : '800',
                   'padding-top' : '5px',
                });
                return false;
            }
            else if(phone.hasClass('error'))
            {
                return false;
            }
            else
            {
                $('.modal-title').html("<?php echo e(__("Get Code")); ?>");

                $('#subheader_edit_text').html("<?php echo e(__("To make sure this number is yours, we will send you a verification code.")); ?>");

                $('.phone_group').hide();

                $('#edit_static_phone_show').show();

                $('.edit_button_edit').show();

                $(this).removeClass("update").addClass("edit_get_code").html("<?php echo e(__("Get Code")); ?>");

                var edit_phone = $("#edit_phone").intlTelInput("getNumber");
                $('#edit_static_phone_show').html(edit_phone + '&nbsp;&nbsp;');
                return true;
            }
        });

        // //edit_button_edit
        // $(document).on('click', '.edit_button_edit', function()
        // {
        //     $('.edit_get_code').removeClass("edit_get_code").addClass("update").html("<?php echo e(__("Next")); ?>");
        //     $('.edit_static_phone_show').html('');
        //     $(this).hide();
        //     $('#subheader_edit_text').html("<?php echo e(__("Enter the number you’d like to use")); ?>");
        //     $('.phone_group').show();
        // });

        //edit_get_code
        $(document).on('click', '.edit_get_code', function()
        {
            $('.modal-title').html("<?php echo e(__("Verify Phone")); ?>");
            $(this).removeClass("edit_get_code").addClass("edit_verify").html("<?php echo e(__("Verify")); ?>");
            $('.phone_group').hide();
            $('.edit_button_edit').hide();
            $('.edit_static_phone_show').html('');
            $('#subheader_edit_text').html('<?php echo e(__("We just sent you a SMS with a code.")); ?>'+ '<br><br>' + '<?php echo e(__("Enter it to verify your phone.")); ?>.');
            $('#edit_phone_verification_code').show().val('');

            var pluginPhone = $("#edit_phone").intlTelInput("getNumber");
            var pluginCarrierCode = $('#edit_phone').intlTelInput('getSelectedCountryData').dialCode;

            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/profile/editGetVerificationCode",
                dataType: "json",
                cache: false,
                data: {
                    'phone': pluginPhone,
                    'code': pluginCarrierCode,
                }
            })
            .done(function(response)
            {
                if (response.status == true)
                {
                    $('#editHasVerificationCode').val(response.message);
                }
            });
        });

        //edit_verify
        $(document).on('click', '.edit_verify', function()
        {
            var classOfSubmit = $('#common_button_update');

            var edit_phone_verification_code = $("#edit_phone_verification_code").val();

            var pluginPhone = $("#edit_phone").intlTelInput("getNumber");
            var pluginDefaultCountry = $('#edit_phone').intlTelInput('getSelectedCountryData').iso2;
            var pluginCarrierCode = $('#edit_phone').intlTelInput('getSelectedCountryData').dialCode;


            if (classOfSubmit.hasClass('edit_verify'))
            {
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/profile/edit-complete-phone-verification",
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': pluginPhone,
                        'flag': pluginDefaultCountry,
                        'code': pluginCarrierCode,
                        'edit_phone_verification_code': edit_phone_verification_code,
                    }
                })
                .done(function(data)
                {
                    if (data.status == false || data.status == 500)
                    {
                        $('#message').css('display', 'block');
                        $('#message').html(data.message);
                        $('#message').addClass(data.error);
                    }
                    else
                    {
                        $('#message').removeClass('alert-danger');
                        $('#message').css('display', 'block');
                        $('#message').html(data.message);
                        $('#message').addClass(data.success);

                        $('#subheader_edit_text').hide();
                        $('#edit_phone_verification_code').hide();
                        $('#common_button_update').hide();
                        $('#close').hide();
                        $('.modal-title').hide();
                    }
                });
            }
        });

        //when phone verificaiton is disabled
        $(document).on('click', '.edit_form_submit', function()
        {
            var classOfSubmit = $('#common_button_update');
            if (classOfSubmit.hasClass('edit_form_submit'))
            {
                var pluginPhone = $("#edit_phone").intlTelInput("getNumber");
                var pluginDefaultCountry = $('#edit_phone').intlTelInput('getSelectedCountryData').iso2;
                var pluginCarrierCode = $('#edit_phone').intlTelInput('getSelectedCountryData').dialCode;

                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/profile/update-phone-number",
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': pluginPhone,
                        'flag': pluginDefaultCountry,
                        'code': pluginCarrierCode,
                    }
                })
                .done(function(data)
                {
                    if (data.status == true)
                    {
                        $('#message').css('display', 'block');
                        $('#message').html(data.message);
                        $('#message').addClass(data.class_name);

                        $('#subheader_edit_text').hide();
                        $('#common_button_update').hide();
                        $('#close').hide();
                        $('.phone_group').hide();
                        $('.modal-title').hide();
                    }
                });
            }
        });

///////////////////////////////////////////////////////////////

        //start - ajax image upload
            function changeProfile() {
                $('#file').click();
            }
            $('#file').change(function () {
                if ($(this).val() != '') {
                    upload(this);

                }
            });
            function upload(img) {
                var form_data = new FormData();
                form_data.append('file', img.files[0]);
                form_data.append('user_id', '<?php echo e($user->id); ?>');
                form_data.append('_token', '<?php echo e(csrf_token()); ?>');
                $('#loading').css('display', 'block');
                $.ajax({
                    url: "<?php echo e(url('profile-image-upload')); ?>",
                    data: form_data,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function (data) {
                        if (data.fail) {
                            $('#profileImage').attr('src', '<?php echo e(asset('public/user_dashboard/images/avatar.jpg')); ?>');
                            // alert(data.errors['file']);
                            // alert(data.errors);
                            // log(data.errors.file);
                            $('#file-error').show().addClass('error').html(data.errors.file).css({
                               'color' : 'red !important',
                               'font-size' : '14px',
                               'font-weight' : '800',
                               'padding-top' : '5px',
                            });
                        }
                        else {
                            $('#file-error').hide();
                            $('#file_name').val(data);
                            $('#profileImage').attr('src', '<?php echo e(asset('public/user_dashboard/profile')); ?>/' + data);
                            $('#profileImageHeader').attr('src', '<?php echo e(asset('public/user_dashboard/profile')); ?>/' + data);
                        }
                        $('#loading').css('display', 'none');
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                        $('#profileImage').attr('src', '<?php echo e(asset('public/user_dashboard/images/avatar.jpg')); ?>');
                    }
                });
            }
        //end - ajax image upload

           jQuery.extend(jQuery.validator.messages, {
                required: "<?php echo e(__('This field is required.')); ?>",
                minlength: $.validator.format( "<?php echo e(__("Please enter at least")); ?>"+" {0} "+"<?php echo e(__("characters.")); ?>" ),
            })
        //validation -rest
            $("#reset_password").validate({
                rules: {
                    old_password: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    confirm_password: {
                        equalTo: "#password",
                        minlength: 6,
                    }
                },
                messages: {
                    password: {
                        required: "<?php echo e(__('This field is required.')); ?>",
                    },
                    confirm_password: {
                        equalTo: "<?php echo e(__('Please enter the same value again.')); ?>",
                    },
                }
            });

            // jQuery.validator.addMethod("letters_with_spaces_and_dot", function(value, element)
            // {
            //     return this.optional(element) || /^[A-Za-z. ]+$/i.test(value); //letters + dot(.) symbol
            // }, "<?php echo e(__("Please enter letters & only dot(.) symbol is allowed!")); ?>");

            // jQuery.validator.addMethod("letters_with_spaces", function(value, element)
            // {
            //     return this.optional(element) || /^[A-Za-z ]+$/i.test(value); //only letters
            // }, "<?php echo e(__("Please enter letters only!")); ?>");

            $('#profile_update_form').validate({
                rules: {
                    first_name: {
                        required: true,
                        // letters_with_spaces_and_dot: true,
                    },
                    last_name: {
                        required: true,
                        // letters_with_spaces: true,
                    },
                },
                submitHandler: function(form)
                {
                    $("#users_profile").attr("disabled", true);
                    $(".spinner").show();
                    $("#users_profile_text").text('Submitting...');
                    form.submit();
                }
            });

        ///////////////////////////////////////////////////////////////
        //Delete

            //onclick - delete
            // $(document).on('click', '.delete', function()
            // {
            //     // alert('clicked');
            //     var defaultCountry = $('#defaultCountry').val();
            //     var carrierCode = $('#carrierCode').val();
            //     var phone = $("input[name=edit_phone]").val();

            //     swal({
            //       title: "Are you sure you want to delete?",
            //       text: "You won't be able to revert this!",
            //       type: "warning",
            //       showCancelButton: true,
            //       // confirmButtonColor: 'rgb(221, 51, 51)',
            //       confirmButtonText: "Yes, delete it!",
            //       // cancelButtonColor: '#d33',
            //       cancelButtonText: "Cancel",
            //       closeOnConfirm: false,
            //       showLoaderOnConfirm: true,
            //       closeOnCancel: true
            //     },
            //     function(isConfirm)
            //     {
            //         if (!isConfirm) return;

            //         if (isConfirm)
            //         {
            //             $.ajax({
            //                 headers:
            //                 {
            //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //                 },
            //                 method: "POST",
            //                 url: SITE_URL+"/profile/delete-phone-number",
            //                 dataType: "json",
            //                 cache: false,
            //                 data: {
            //                     'defaultCountry': defaultCountry,
            //                     'phone': phone,
            //                     'carrierCode': carrierCode,
            //                 }
            //             })
            //             .done(function(response)
            //             {
            //                 swal({title: "Deleted!", text: response.message, type:response.status},
            //                     function(){
            //                        window.location.reload();
            //                     }
            //                 );
            //             })
            //             .fail(function(){
            //                 swal('Oops...', 'Something went wrong with ajax !', 'error');
            //             });
            //         }
            //         else
            //         {
            //             swal("Cancelled", "You Cancelled", "error");
            //         }
            //     });
            // });
///////////////////////////////////////////////////////////////
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/users/profile.blade.php ENDPATH**/ ?>