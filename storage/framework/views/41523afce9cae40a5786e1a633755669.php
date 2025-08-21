<div class="box box-info box_info">
    <div class="panel-body">
        <h4 class="all_settings">
            Manage Settings
        </h4>
        <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">

             <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_general_setting')): ?>
                <li <?= $sub_sub_menu == 'general' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="settings" href="<?php echo e(url('admin/settings')); ?>">
                        <i class="glyphicon glyphicon-cog">
                        </i>
                        <span>
                            General
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_social_links')): ?>
                <li <?= $sub_sub_menu == 'social_links' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/social_links')); ?>">
                        <i class="fa fa-share-alt">
                        </i>
                        <span>
                            Social Links
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant_group')): ?>
                <li <?= $sub_sub_menu == 'merchant_group' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/merchant-group')); ?>">
                        <i class="fa fa-user-secret"></i>
                        <span>
                            Merchant Packages
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant_group')): ?>
                <li <?= $sub_sub_menu == 'merchant_document' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/merchant-document')); ?>">
                        <i class="fa fa-user-secret"></i>
                        <span>
                            Merchant Document
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_group')): ?>
                <li <?= $sub_sub_menu == 'user_role' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/user_role')); ?>">
                        <i class="fa fa-object-group"></i>
                        <span>
                            User Groups
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_role')): ?>
                <li <?= $sub_sub_menu == 'role' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="permissions_roles" href="<?php echo e(url('admin/settings/roles')); ?>">
                        <i class="fa fa-key"></i>
                        <span>
                            Roles &amp; Permissions
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_database_backup')): ?>
                <li <?= $sub_sub_menu == 'backup' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="<?php echo e(url('admin/settings/backup')); ?>">
                        <i class="fa fa-database">
                        </i>
                        <span>
                            Database Backup
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_meta')): ?>
                <li <?= $sub_sub_menu == 'meta' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="metas" href="<?php echo e(url('admin/settings/metas')); ?>">
                        <i class="glyphicon glyphicon-info-sign">
                        </i>
                        <span>
                            Metas
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_page')): ?>
                <li <?= $sub_sub_menu == 'pages' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="metas" href="<?php echo e(url('admin/settings/pages')); ?>">
                        <i class="fa fa-pagelines"></i>
                        <span>
                            Pages
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_enable_woocommerce')): ?>
                <li <?= $sub_sub_menu == 'enablewoocommerce' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="preference" href="<?php echo e(url('admin/settings/enable-woocommerce')); ?>">
                        <i class="fa fa-shopping-cart"></i>
                        <span>
                            Enable WooCommerce
                        </span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_preference')): ?>
                <li <?= $sub_sub_menu == 'preference' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="preference" href="<?php echo e(url('admin/settings/preference')); ?>">
                        <i class="fa fa-cogs">
                        </i>
                        <span>
                            Preferences
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_utility_setting')): ?>
                <li <?= $sub_sub_menu == 'all_utility' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="preference" href="<?php echo e(url('admin/settings/utility')); ?>">
                        <i class="fa fa-cogs">
                        </i>
                        <span>
                            Utility Setting
                        </span>
                    </a>
                </li>
            <?php endif; ?>
          
            <?php if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_revenue_sharing')): ?>
            <li <?= $sub_sub_menu == 'revenues' ? ' class="treeview active"' : 'treeview'?>>
                <a data-group="brands" href="<?php echo e(url('admin/settings/revenues')); ?>">
                    <i class="fa fa-bitcoin">
                    </i>
                    <span>
                       Revenues
                    </span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php /**PATH /home/lubypay/public_html/test/resources/views/admin/common/settings_bar.blade.php ENDPATH**/ ?>