<!DOCTYPE html>
<html>
    <head>
        <title><?php echo e($subject); ?></title>
    </head>
    <body>
        <div class="wrapper" style="background-color: #f2f2f2;">
            <table class="layout layout--no-gutter" style="border-collapse: collapse; table-layout: fixed; margin-left: auto; margin-right: auto; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;" align="center">
                <tbody>
                    <tr>
                        <td class="column" style="padding: 0; text-align: left; vertical-align: top; color: #60666d; font-size: 14px; line-height: 21px; font-family: sans-serif; width: 600px;">
                            <div class="wrapper" style="background-color: #f2f2f2; text-align: left;">
                                <img style="height: auto; width: 150px; margin-bottom: 20px; margin-top: 20px;" src="<?php echo e(asset('public/images/logos/1702998023_logo.jpg')); ?>" alt="" />
                            </div>
                            <div style="margin-left: 20px; margin-right: 20px;">
                                <h3 style="color: #ff8400; font-weight: bold;"><?php echo e($subject); ?></h3>
                                <?=$content?>
                            </div>
                            <div style="margin-left: 20px; margin-right: 20px;">
                                <p class="size-14" style="margin-top: 0; margin-bottom: 0; font-size: 14px; line-height: 21px;">Regards,<br /><strong>LubyPay</strong></p>
                            </div>
                            <div>
                                <table style="margin-top: 20px; background: #000 ;max-width: 900px; color: #fff;" width="100%" cellspacing="0" cellpadding="0" border="0" >
                                    <tbody>
                                        <tr>
                                            <td style="padding:20px 40px 0px;" align="center">
                                                <h1 style="text-align: center;font-family: 'Open sans', Arial, sans-serif; font-size: 28px;">LETS CONNECT</h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table align="center">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center" width="30%" style="vertical-align: top;">
                                                                <a href="https://www.facebook.com/lubyall" target="_blank">
                                                                <img src="<?php echo e(asset('public/images/tickfb.png')); ?>" style="border: 1px solid #fff; background: #fff; color: #000; padding: 5px; border-radius: 20px; width: 20px;">
                                                                </a>
                                                            </td>
                                                            <td align="center" class="margin" width="30%" style="vertical-align: top;">
                                                                <a href="https://www.linkedin.com/company/lubyall" target="_blank">
                                                                <img src="<?php echo e(asset('public/images/tickli.png')); ?>" style="border: 1px solid #fff; background: #fff; color: #000; padding: 5px; border-radius: 20px; width: 20px;">
                                                                </a>
                                                            </td>
                                                            <td align="center" class="margin" width="30%" style="vertical-align: top;">
                                                                <a href="https://twitter.com/lubyall" target="_blank"> 
                                                                <img src="<?php echo e(asset('public/images/ticktx.png')); ?>" style="border: 1px solid #fff; background: #fff; color: #000; padding: 5px; border-radius: 20px; width: 20px;">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:20px 40px;" align="center">
                                                <p>If you have any questions, visit our support site at <a href="https://www.lubypay.com/">https://www.lubypay.com/</a>, contact us at <a href="mailto:help@lubypay.com">help@lubypay.com</a></p>
                                                <p>This e-mail is confidential. It may also be legally privileged. If you are not the addressee you may not copy, forward, disclose or use any part of it. If you have received this message in error, please delete it and all copies from your system and notify the sender immediately by return e-mail. Internet communications cannot be guaranteed to be timely, secure, error or virus-free. The sender does not accept liability for any errors or omissions.</p>
                                                <p>"SAVE PAPER - THINK BEFORE YOU PRINT!"</p>
                                                <p>© Copyright 2024. All Rights Reserved.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html><?php /**PATH /home/lubypay/public_html/sandbox/resources/views/emails/sendmail.blade.php ENDPATH**/ ?>