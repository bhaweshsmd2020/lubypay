<!DOCTYPE html>
<html>
    <head>
        <title>{{ $subject }}</title>
    </head>
    <body>
        <div class="wrapper" style="background-color: #f2f2f2;">
            <table class="layout layout--no-gutter" style="border-collapse: collapse; table-layout: fixed; margin-left: auto; margin-right: auto; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;" align="center">
                <tbody>
                    <tr>
                        <td class="column" style="padding: 0; text-align: left; vertical-align: top; color: #60666d; font-size: 14px; line-height: 21px; font-family: sans-serif; width: 600px;">
                            <div class="wrapper" style="background-color: #f2f2f2; text-align: left;">
                                <img style="height: auto; width: 150px; margin-bottom: 20px; margin-top: 20px;" src="{{ asset('public/images/logos/logo-light.png') }}" alt="" />
                            </div>
                            <div style="margin-left: 20px; margin-right: 20px;">
                                <h3 style="color: #ff8400; font-weight: bold;">{{ $subject }}</h3>
                                <?=$content?>
                            </div>
                            <div style="margin-left: 20px; margin-right: 20px;">
                                <p class="size-14" style="margin-top: 0; margin-bottom: 0; font-size: 14px; line-height: 21px;">Trân trọng,<br /><strong>LubyPay</strong></p>
                            </div>
                            <div>
                                <table style="margin-top: 20px; background: #000 ;max-width: 900px; color: #fff;" width="100%" cellspacing="0" cellpadding="0" border="0" >
                                    <tbody>
                                        <tr>
                                            <td style="padding:20px 40px 0px;" align="center">
                                                <h1 style="text-align: center;font-family: 'Open sans', Arial, sans-serif; font-size: 28px;">HÃY KẾT NỐI</h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table align="center">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center" width="30%" style="vertical-align: top;">
                                                                <a href="https://www.facebook.com/lubyall" target="_blank">
                                                                <img src="{{ asset('public/images/tickfb.png') }}" style="border: 1px solid #fff; background: #fff; color: #000; padding: 5px; border-radius: 20px; width: 20px;">
                                                                </a>
                                                            </td>
                                                            <td align="center" class="margin" width="30%" style="vertical-align: top;">
                                                                <a href="https://www.linkedin.com/company/lubyall" target="_blank">
                                                                <img src="{{ asset('public/images/tickli.png') }}" style="border: 1px solid #fff; background: #fff; color: #000; padding: 5px; border-radius: 20px; width: 20px;">
                                                                </a>
                                                            </td>
                                                            <td align="center" class="margin" width="30%" style="vertical-align: top;">
                                                                <a href="https://twitter.com/lubyall" target="_blank"> 
                                                                <img src="{{ asset('public/images/ticktx.png') }}" style="border: 1px solid #fff; background: #fff; color: #000; padding: 5px; border-radius: 20px; width: 20px;">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:20px 40px;" align="center">
                                                <p>Nếu bạn có bất kỳ câu hỏi nào, hãy truy cập trang hỗ trợ của chúng tôi tại <a href="https://www.lubypay.com/">https://www.lubypay.com/</a>, liên hệ với chúng tôi tại <a href="mailto:help@lubypay.com">help@lubypay.com</a></p>
                                                <p>Email này là email bảo mật. Nó cũng có thể được bảo mật về mặt pháp lý. Nếu bạn không phải là người nhận, bạn không được phép sao chép, chuyển tiếp, tiết lộ hoặc sử dụng bất kỳ phần nào của email này. Nếu bạn nhận được email này do nhầm lẫn, vui lòng xóa email này và tất cả các bản sao khỏi hệ thống của bạn, đồng thời thông báo ngay cho người gửi bằng email phản hồi. Chúng tôi không thể đảm bảo việc truyền thông qua Internet sẽ kịp thời, an toàn, không có lỗi hoặc không có vi-rút. Người gửi không chịu trách nhiệm cho bất kỳ lỗi hoặc thiếu sót nào.</p>
                                                <p>"TIẾT KIỆM GIẤY - SUY NGHĨ TRƯỚC KHI IN!"</p>
                                                <p>© Bản quyền 2024. Bảo lưu mọi quyền.</p>
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
</html>