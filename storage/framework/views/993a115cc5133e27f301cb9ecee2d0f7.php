<script type="text/javascript">
    /**
     * Check User Status
     */
    function checkUserSuspended(event)
    {
        let userStatus = '<?php echo e(auth()->user()->status); ?>';
        if (userStatus == 'Suspended')
        {
            event.stopPropagation();
            window.location.href="<?php echo e(url('check-user-status')); ?>";
            return false;
        }
    }
</script><?php /**PATH /home/lubypay/public_html/develop/resources/views/user_dashboard/layouts/common/check-user-status.blade.php ENDPATH**/ ?>