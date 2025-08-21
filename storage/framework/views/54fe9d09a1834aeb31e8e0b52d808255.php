
<?php $__env->startSection('title','Account upgrade'); ?>
<?php $__env->startSection('css'); ?>
    <style>
        @media only screen and (max-width: 508px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
        label.error{
            margin-bottom: 0px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- personal_address -->
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    <?php echo $__env->make('user_dashboard.layouts.common.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li><a href="<?php echo e(url('/profile')); ?>"><?php echo app('translator')->get('message.dashboard.setting.title'); ?></a></li>
                                    <?php if($two_step_verification != 'disabled'): ?>
                                        <li><a href="<?php echo e(url('/profile/2fa')); ?>"><?php echo app('translator')->get('message.2sa.title-short-text'); ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <li><a href="<?php echo e(url('/profile/personal-id')); ?>"><?php echo app('translator')->get('message.personal-id.title'); ?>
                                            <?php if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' ): ?>
                                                (<span style="color: green"><i class="fa fa-check"
                                                                               aria-hidden="true"></i>Verified</span>
                                                ) <?php endif; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(url('/profile/personal-address')); ?>"><?php echo app('translator')->get('message.personal-address.title'); ?>
                                            <?php if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' ): ?>
                                                (<span style="color: green"><i class="fa fa-check"
                                                                               aria-hidden="true"></i>Verified</span>
                                                ) <?php endif; ?>
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
                                        <li class="active">
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
<div class="col-6">
                            <?php if(collect($requestPackage)->count()): ?>
                                <div class="alert alert-danger fade in alert-dismissible show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="font-size:20px">×</span>
                                    </button>
                                    Your request to upgrade <?php echo e(auth()->user()->package); ?> to <?php echo e($requestPackage->MerchantGroup->name); ?> is in <?php echo e($requestPackage->status); ?>.
                                </div>
                            <?php endif; ?>

                                <h4>Current package :
                                    
                                </h4>
                                <div>
                                    <p style="    line-height: 22px;
    color: grey;
    padding: 10px 0px;
">If you need to upgrade your current package. You can upgrade your package anytime by click on Business </p><br>
                                    <a class="btn btn-tertiary text-white btn-lg btn-block btn btn-cust" style="margin: unset;">
                                        <?php echo e(auth()->user()->package); ?>

                                    </a></div>
</div>

<div class="col-6">
                            <div class="mt-3" style="    margin-top: 0px!important;">
                                <?php if(!is_numeric(request('package'))): ?>
                                    <div id="kyc-opts_title">
                                        <div class="row ml-2">
                                            <h4 style="color:orange">Upgrade to Business Verified</h4>
                                        </div>
                                        <div class="row ml-2">
                                          <p style="    line-height: 22px;
    color: grey;
    padding: 10px 0px;    letter-spacing: initial;    font-size: 15px!important;
">Select
                                                    the structure of your Business and then submit the required
                                                    information.</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3" id="lv3-kyc-opts">
                                        <?php if($merchantGroups->count()): ?>
                                            <?php $__currentLoopData = $merchantGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(auth()->user()->packageid != $group->id): ?>
                                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                                        <a href="<?php echo e(url('/profile/upgrade?').http_build_query(['package'=>$group->id])); ?>"
                                                           class="btn btn-tertiary text-white btn-lg btn-block btn btn-cust">
                                                            <?php echo e($group->name); ?>

                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div id="kyc-opts_title">
                                        <div class="row ml-2">
                                            <h4 class="">Upgrade your business account
                                                <?php if($merchantGroups->count()): ?>
                                                    <?php echo e(auth()->user()->package); ?>

                                                    to <?php echo e($merchantGroups->where('id',request('package'))->first()->name); ?>

                                                <?php endif; ?> </h4>
                                        </div>
                                        <div class="row ml-2">
                                            <h4 class="text-left"><small class="text-muted" style="font-size: 1.0rem">
                                                    Please submit the required information below.
                                                </small></h4>
                                        </div>
                                    </div>

                                    <div class="row ml-1 mr-1 mt-3 text-left animated fadeIn" id="upgrade-verify"
                                         style="">
                                        <div class="col-12" id="lc-docs" style="">
                                            <div class="alert alert-info" style="margin:0;" role="alert">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h5 class="alert-heading"><i class="fa fa-info"></i> Press
                                                            <strong>"Browse"</strong> to upload the following documents:
                                                        </h5>
                                                    </div>
                                                </div>
                                                <hr class="mt-0">
                                                <div class="row">
                                                    <?php if($merchantGroupDocuments->count()): ?>
                                                        <?php $__currentLoopData = $merchantGroupDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="col-12">
                                                                <span><?php echo e($key+1); ?>. <?php echo e($doc->name); ?></span>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                </div>

                                                
                                            </div>
                                        </div>
                                        <!--  HERE  -->
                                        <form action="<?php echo e(url('profile/upgrade-update')); ?>"
                                              enctype="multipart/form-data" id="upgrade-verify_form" method="POST"
                                              style="width:100%;" class="was-validated">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="package_id" value="<?php echo e(request('package')); ?>">
                                            <?php if(collect($businessDetail)->count()): ?>
                                            <input type="hidden" name="detail_id" value="<?php echo e($businessDetail->id); ?>">
                                            <?php endif; ?>
                                            <div class="col-12" style="display:none;">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="structure"
                                                           id="structure" maxlength="256"
                                                           aria-describedby="business structure"
                                                           placeholder="Business Structure">
                                                </div>
                                            </div>
                                    
                                            <!-- document sections -->
                                            <div class="col-12 mt-4 ">
                                                <div class="voucher_option alert alert-warning animated fadeIn"
                                                     style="padding:5px;" role="alert">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div id="documents_prepend" class="input-group-text"
                                                                 style="height: 39.5938px;">Files:<span
                                                                        style="color:transparent;">.</span><span
                                                                        id="num_files">0</span></div>
                                                        </div>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="documents"
                                                                   name="document[]" multiple=""
                                                                   <?php if(!collect($businessDetail)->count()): ?>
                                                                   required="required"
                                                                   <?php endif; ?>
                                                            >
                                                            <label class="custom-file-label" for="documents">Choose
                                                                Files...</label>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <span><i class="fa fa-info"></i> You <strong>can</strong> upload multiple files at once.</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- business information -->
                                            <div class="col-12 mt-3 text-left">
                                                <div class="form-group">
                                                    <label for="category">What is your business type?</label>
                                                    <select id="business_type" class="custom-select" name="business_type"
                                                            required="required">
                                                        <option value="" selected="">Please select. . .</option>
                                                        <?php if(collect(config('businessdetails.business_type'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.business_type')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option <?php echo e(@$businessDetail->business_type == $val ?  'selected' :''); ?>><?php echo e($val); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="business_name">What is your business name?</label>
                                                    <input type="text" class="form-control" name="business_name" id="business_name"
                                                           maxlength="255" aria-describedby="business name"
                                                           value="<?php echo e(@$businessDetail->business_name); ?>"
                                                           placeholder="Business name" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="trade_name">Registered trading name (where it differs
                                                        from business name)</label>
                                                    <input type="text" class="form-control" name="trading_name"
                                                           id="trading_name" maxlength="255" aria-describedby="trade name"
                                                           value="<?php echo e(@$businessDetail->trading_name); ?>"
                                                           placeholder="Trade name" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="website_url">Website URL</label>
                                                    <input type="text" class="form-control" name="website_url"
                                                           id="website_url" maxlength="255" aria-describedby="website url"
                                                           value="<?php echo e(@$businessDetail->website_url); ?>"
                                                           placeholder="Website URL" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="business_no">What is your business registration number?</label>
                                                    <input type="text" class="form-control" name="business_no" id="business_no"
                                                           maxlength="255" aria-describedby="business_no name"
                                                           value="<?php echo e(@$businessDetail->business_no); ?>"
                                                           placeholder="Business Registration Number" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="company_phone">What is the official phone number
                                                        for your company?</label>
                                                    <input type="text" class="form-control" name="official_phone"
                                                           id="official_phone" aria-describedby="company phone"
                                                           value="<?php echo e(@$businessDetail->official_phone); ?>"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                             placeholder="XXXXXXX">
                                                            <!--pattern="[\d]{10}"-->
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="company_statement_phone">What is the official  customer statement phone number
                                                        for your company?</label>
                                                    <input type="text" class="form-control" name="customer_statement_phone"
                                                           id="customer_statement_phone" aria-describedby="customer statement phone"
                                                           value="<?php echo e(@$businessDetail->customer_statement_phone); ?>"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                            placeholder="XXXXXXX">
                                                            <!--pattern="[\d]{7}"-->
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="street">On what Street is your business located?</label>
                                                    <input type="text" class="form-control" name="street" id="street"
                                                           maxlength="255" aria-describedby="business street"
                                                           value="<?php echo e(@$businessDetail->street); ?>"
                                                           placeholder="Street" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="town_city">In what Town/City is your business
                                                        located?</label>
                                                    <input type="text" class="form-control" name="city"
                                                           id="city" maxlength="100"
                                                           value="<?php echo e(@$businessDetail->city); ?>"
                                                           aria-describedby="business town or city"
                                                           placeholder="Town/City" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="region">In what Region is your business located?</label>
                                                    <input type="text" class="form-control" name="region" id="region"
                                                           maxlength="255" aria-describedby="business region"
                                                           value="<?php echo e(@$businessDetail->region); ?>"
                                                           placeholder="Region" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="country">In what Country is your business located?</label>
                                                    <input type="text" class="form-control" name="country" id="country"
                                                           maxlength="255" aria-describedby="business country"
                                                           value="<?php echo e(@$businessDetail->country); ?>"
                                                           placeholder="Country" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="postcode">What is the Postal Code of your business location?</label>
                                                    <input type="text" class="form-control" name="postcode" id="country"
                                                           maxlength="10" aria-describedby="business postcode"
                                                           value="<?php echo e(@$businessDetail->postcode); ?>"
                                                           placeholder="Postal Code" required="required">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="description">Please provide a brief description of your
                                                        products and/or services:</label>
                                                    <textarea name="description" id="description" class="form-control"
                                                              rows="3" style="width:100%;resize:none;" maxlength="255"
                                                              form="upgrade-verify_form"
                                                              placeholder="Details described here. . ."
                                                              required="required"><?php echo e(@$businessDetail->description); ?></textarea>
                                                    <small class="float-right mb-2"><span><strong
                                                                    id="chars_remain">255</strong></span> Characters
                                                        left</small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="established">What date was your business
                                                        established?</label>
                                                    <div class="input-group date">
                                                        <input type="date" class="form-control datepicker"
                                                               id="establish_date" name="establish_date"
                                                               value="<?php echo e(@$businessDetail->establish_date); ?>"
                                                               required="required">
                                                    </div>
                                                    <label id="establish_date-error" class="error" for="establish_date" style="display: none;"></label>
                                                </div>
                                            </div>
                                            
                                            <!-- optional fields -->
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="days_deliver">In general, how many days does it take to provide your product/service?</label>
                                                    <input type="text" class="form-control" name="days_deliver"
                                                           id="days_deliver" aria-describedby="days_deliver"
                                                           value="<?php echo e(@$businessDetail->days_deliver); ?>"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                           pattern="[\d]{3}" placeholder="7">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-3 text-left">
                                                <div class="form-group">
                                                    <label for="charged">When are your customers charged?</label>
                                                    <select id="when_charged" class="custom-select" name="when_charged">
                                                        <option value="" selected="">Please select. . .</option>
                                                        <?php if(collect(config('businessdetails.when_charged'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.when_charged')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option <?php echo e(@$businessDetail->when_charged == $val ?  'selected' :''); ?>><?php echo e($val); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="based">Where is your company based?</label>
                                                    <input type="text" class="form-control" name="based" id="based"
                                                           maxlength="10" aria-describedby="business based"
                                                           value="<?php echo e(@$businessDetail->based); ?>"
                                                           placeholder="Base Location">
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="target_country">Which countries in Eastern Caribbean are you looking to sell?</label>
                                                    <input type="text" class="form-control" name="target_country" id="target_country"
                                                           maxlength="50" aria-describedby="target_country"
                                                           value="<?php echo e(@$businessDetail->target_country); ?>"
                                                           placeholder="Target Country">
                                                </div>
                                            </div>
                                            
                                            <!-- <div class="col-12 mt-3 text-left">
                                                <div class="form-group">
                                                    <label for="category">What is the nature of your business?</label>
                                                    <select id="business_nature" class="custom-select" name="business_nature"
                                                            required="required">
                                                        <option value="" selected="">Please select. . .</option>
                                                        <?php if(collect(config('businessdetails.business_nature'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.business_nature')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option <?php echo e(@$businessDetail->business_nature == $val ?  'selected' :''); ?>><?php echo e($val); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="product_service">Does your business sell products and/or
                                                        provide services?</label>
                                                    <select id="sell" class="custom-select"
                                                            name="sell" required="required">
                                                        <option value="" selected="">Please select. . .</option>
                                                        <?php if(collect(config('businessdetails.sell'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.sell')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option <?php echo e(@$businessDetail->sell == $val ?  'selected' :''); ?>><?php echo e($val); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4 text-left">
                                                <label>What type of customers does your business have?</label>
                                                <br>
                                                <div class="col-12  text-left">
                                                    <div class="row mt-2">
                                                        <?php if(collect(config('businessdetails.sell'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.customer_type')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="col-xs-12 col-sm-6 mt-2">
                                                                    <div>
                                                                        <label for="3ct" style="margin:0;">
                                                                            <input type="checkbox"
                                                                                   name="customer_type"
                                                                                   value="<?php echo e($val); ?>"
                                                                                   <?php echo e(@$businessDetail->customer_type == $val ?  'checked' :''); ?>

                                                                                   required="">
                                                                            <?php echo e($val); ?>

                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <label id="customer_type-error" class="error" for="customer_type" style="display: none;"></label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <div class="form-group">
                                                    <label for="avg_trxn">What is your business' expected average
                                                        transaction value? (per transaction)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">$</div>
                                                        </div>
                                                        <input min="0.01" type="number" class="form-control"
                                                               id="average_transaction" name="average_transaction" value="<?php echo e(@$businessDetail->average_transaction); ?>"
                                                               placeholder="Average transaction">
                                                    </div>
                                                    <label id="average_transaction-error" class="error" for="average_transaction" style="display: none;"></label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="turnover">What is your business' expected annual
                                                        turnover?</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">$</div>
                                                        </div>
                                                        <input min="0.01" type="number" class="form-control"
                                                               id="annual_turnover" name="annual_turnover" value="<?php echo e(@$businessDetail->annual_turnover); ?>"
                                                               placeholder="Annual turnover">
                                                    </div>
                                                    <label id="annual_turnover-error" class="error" for="annual_turnover" style="display: none;"></label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="number_employees">How many employees does your business
                                                        employ, including yourself?</label>
                                                    <div class="input-group">
                                                        <input min="1" step="1" type="number" class="form-control"
                                                               id="employees" name="employees" value="<?php echo e(@$businessDetail->employees); ?>"
                                                               placeholder="Number of employees">
                                                    </div>
                                                    <label id="employees-error" class="error" for="employees" style="display: none;"></label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-3 text-left">
                                                <div class="form-group">
                                                    <label>Will your business be expecting to recieve foreign currency
                                                        payments regularly from other businesses/entities/indiviuals
                                                        outside of St. Lucia?</label>
                                                    <br>
                                                    <div class="col-12 mt-3 text-left">
                                                        <div class="row">
                                                            <?php if(collect(config('businessdetails.foreign_currency_payment'))->count()): ?>
                                                                <?php $__currentLoopData = collect(config('businessdetails.foreign_currency_payment')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div class="col-xs-6 col-sm-3 mt-2">
                                                                        <div>
                                                                            <label for="3ct" style="margin:0;">
                                                                                <input type="radio"
                                                                                       name="foreign_currency_payment"
                                                                                       value="<?php echo e($val); ?>"
                                                                                       <?php echo e(@$businessDetail->foreign_currency_payment == $val ?  'checked' :''); ?>

                                                                                       required="">
                                                                                <?php echo e($val); ?>

                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <label id="foreign_currency_payment-error" class="error" for="foreign_currency_payment" style="display: none;"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="premises">Where does your business operate from?</label>
                                                    <select id="operate_from" class="custom-select" name="operate_from"
                                                            required="required">
                                                        <option value="" selected="">Please select. . .</option>
                                                        <?php if(collect(config('businessdetails.operate_from'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.operate_from')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option <?php echo e(@$businessDetail->operate_from == $val ?  'selected' :''); ?>><?php echo e($val); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4 text-left">
                                                <div class="form-group">
                                                    <label for="signup_reason">What influenced your decision to use
                                                        WiPay?</label>
                                                    <select id="use_caribPay" class="custom-select"
                                                            name="use_caribPay">
                                                        <option value="" selected="">Please select. . .</option>
                                                        <?php if(collect(config('businessdetails.use_caribPay'))->count()): ?>
                                                            <?php $__currentLoopData = collect(config('businessdetails.use_caribPay')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option <?php echo e(@$businessDetail->use_caribPay == $val ?  'selected' :''); ?>><?php echo e($val); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>-->
                                            
                                            <div class="col-6 offset-3">
                                                <button id="form_submit"
                                                        class="btn btn-lg btn-block btn-tertiary btn btn-cust"
                                                        type="submit">Verify
                                                </button>
                                            </div>
                                        </form>
                                    </div>







                                <?php endif; ?>
                            </div>
                            </div>
                            </div>
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

    <script type="text/javascript">

        jQuery.extend(jQuery.validator.messages, {
            required: "<?php echo e(__('This field is required.')); ?>",
        })

        $(document).on({
            'change': function (event) {
                var len = $(this).get(0).files.length;
                $('#num_files').text(len);
                if(len == 0){
                    $(this).siblings('label').text('Choose Files...');
                }
            },
        }, '#documents');
        $(document).on({
            'keyup keydown': function (event) {
                $("#chars_remain").text(255 - $(this).val().length);
            },
        }, '#description');

        $('#upgrade-verify_form').validate({
            submitHandler: function (form) {
                $("#form_submit").attr("disabled", true);
                $(".spinner").show();
                $("#personal_address_submit_text").text('Submitting...');
                form.submit();
            }
        });

                $('#establish_date').click(function(event){
           $('#establish_date ').data("DateTimePicker").show();
        });     
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user_dashboard.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/users/profile_upgrade.blade.php ENDPATH**/ ?>