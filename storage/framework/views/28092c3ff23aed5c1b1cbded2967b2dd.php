<div class="box box-info box_info">
    <div class="panel-body">
        <h4 class="all_settings">
            Manage Settings
        </h4>
        <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
            
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_appstore_credentials')): ?>
                <li <?= $sub_sub_menu == 'app-store-credentials' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/app-store-credentials')); ?>">
                        <i class="fa fa-key">
                        </i>
                        <span>
                            App Store Credentials
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_api_credentials')): ?>
                <li <?= $sub_sub_menu == 'api_informations' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/api_informations')); ?>">
                        <i class="fa fa-key">
                        </i>
                        <span>
                            API Credentials
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_services')): ?>
                <li <?= $sub_sub_menu == 'services' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="brands" href="<?php echo e(url('admin/settings/services/view')); ?>">
                        <i class="fa fa-key">
                        </i>
                        <span>
                           Services
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_nfc_credentials')): ?>
                <li <?= $sub_sub_menu == 'nfc_credentials' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="brands" href="<?php echo e(url('admin/settings/nfc')); ?>">
                        <i class="fa fa-key">
                        </i>
                        <span>
                           NFC Credentials
                        </span>
                    </a>
                </li>
            <?php endif; ?>
                
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_keys')): ?>
                <li <?= $sub_sub_menu == 'key_informations' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="brands" href="<?php echo e(url('admin/settings/key_informations')); ?>">
                        <i class="fa fa-key">
                        </i>
                        <span>
                           Keys
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            
            <li <?= $sub_sub_menu == 'app_pages' ? ' class="treeview active"' : 'treeview'?>>
                <a data-group="brands" href="<?php echo e(url('admin/apppages')); ?>">
                    <i class="fa fa-key">
                    </i>
                    <span>
                       App Pages
                    </span>
                </a>
            </li>
            
            <li <?= $sub_sub_menu == 'app_versions' ? ' class="treeview active"' : 'treeview'?>>
                <a data-group="brands" href="<?php echo e(url('admin/settings/appversions')); ?>">
                    <i class="fa fa-key">
                    </i>
                    <span>
                       App Versions
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php /**PATH /home/ticktappay/public_html/paymoney/resources/views/admin/common/appsettings_bar.blade.php ENDPATH**/ ?>