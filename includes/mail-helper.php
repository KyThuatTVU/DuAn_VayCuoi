<?php
/**
 * Mail Helper - Gửi email OTP xác nhận đăng ký
 * Sử dụng SMTP trực tiếp (không cần PHPMailer)
 */

/**
 * Gửi email OTP
 * @param string $to_email Email người nhận
 * @param string $to_name Tên người nhận
 * @param string $otp_code Mã OTP 6 số
 * @return array ['success' => bool, 'message' => string]
 */
function sendOTPEmail($to_email, $to_name, $otp_code) {
    // Lấy cấu hình SMTP từ .env
    $smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $smtp_port = (int)(getenv('SMTP_PORT') ?: 587);
    $smtp_user = getenv('SMTP_USER') ?: '';
    $smtp_pass = getenv('SMTP_PASS') ?: '';
    $smtp_from_email = getenv('SMTP_FROM_EMAIL') ?: $smtp_user;
    $smtp_from_name = getenv('SMTP_FROM_NAME') ?: 'Váy Cưới Thiên Thần';
    
    // Kiểm tra cấu hình SMTP
    if (empty($smtp_user) || empty($smtp_pass)) {
        return ['success' => false, 'message' => 'Chưa cấu hình SMTP trong file .env'];
    }
    
    // Tạo nội dung email
    $subject = 'Mã xác nhận đăng ký tài khoản - ' . $smtp_from_name;
    $body = getOTPEmailTemplate($to_name, $otp_code);
    
    // Gửi email bằng SMTP
    return sendEmailSMTP($smtp_host, $smtp_port, $smtp_user, $smtp_pass, $smtp_from_email, $smtp_from_name, $to_email, $to_name, $subject, $body);
}

/**
 * Gửi email qua SMTP
 */
function sendEmailSMTP($host, $port, $user, $pass, $from_email, $from_name, $to_email, $to_name, $subject, $body) {
    $error_msg = '';
    
    try {
        // Kết nối SMTP với TLS
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $socket = @stream_socket_client(
            "tcp://$host:$port",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            return ['success' => false, 'message' => "Không thể kết nối SMTP: $errstr ($errno)"];
        }
        
        // Đọc greeting
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            fclose($socket);
            return ['success' => false, 'message' => 'SMTP greeting failed: ' . $response];
        }
        
        // EHLO
        fwrite($socket, "EHLO localhost\r\n");
        $response = readSMTPResponse($socket);
        
        // STARTTLS
        fwrite($socket, "STARTTLS\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            fclose($socket);
            return ['success' => false, 'message' => 'STARTTLS failed: ' . $response];
        }
        
        // Enable TLS
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return ['success' => false, 'message' => 'Không thể bật TLS'];
        }
        
        // EHLO again after TLS
        fwrite($socket, "EHLO localhost\r\n");
        $response = readSMTPResponse($socket);
        
        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '334') {
            fclose($socket);
            return ['success' => false, 'message' => 'AUTH LOGIN failed: ' . $response];
        }
        
        // Username
        fwrite($socket, base64_encode($user) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '334') {
            fclose($socket);
            return ['success' => false, 'message' => 'Username rejected: ' . $response];
        }
        
        // Password
        fwrite($socket, base64_encode($pass) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '235') {
            fclose($socket);
            return ['success' => false, 'message' => 'Xác thực thất bại. Kiểm tra lại SMTP_USER và SMTP_PASS'];
        }
        
        // MAIL FROM
        fwrite($socket, "MAIL FROM:<$from_email>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            fclose($socket);
            return ['success' => false, 'message' => 'MAIL FROM failed: ' . $response];
        }
        
        // RCPT TO
        fwrite($socket, "RCPT TO:<$to_email>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            fclose($socket);
            return ['success' => false, 'message' => 'RCPT TO failed: ' . $response];
        }
        
        // DATA
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '354') {
            fclose($socket);
            return ['success' => false, 'message' => 'DATA failed: ' . $response];
        }
        
        // Build email headers and body
        $boundary = md5(uniqid(time()));
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($from_name) . "?= <$from_email>\r\n";
        $headers .= "To: =?UTF-8?B?" . base64_encode($to_name) . "?= <$to_email>\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "Message-ID: <" . md5(uniqid()) . "@" . parse_url(getenv('SITE_URL') ?: 'localhost', PHP_URL_HOST) . ">\r\n";
        
        $email_content = $headers . "\r\n" . chunk_split(base64_encode($body));
        
        // Send email content
        fwrite($socket, $email_content . "\r\n.\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            fclose($socket);
            return ['success' => false, 'message' => 'Gửi email thất bại: ' . $response];
        }
        
        // QUIT
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return ['success' => true, 'message' => 'Email đã được gửi thành công'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
    }
}

/**
 * Đọc response từ SMTP server
 */
function readSMTPResponse($socket) {
    $response = '';
    while ($line = fgets($socket, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) == ' ') {
            break;
        }
    }
    return $response;
}

/**
 * Template HTML cho email OTP
 */
function getOTPEmailTemplate($name, $otp_code) {
    $site_name = getenv('SITE_NAME') ?: 'Váy Cưới Thiên Thần';
    
    return '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px 10px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px;">' . htmlspecialchars($site_name) . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 20px; color: #333333; font-size: 24px;">Xác nhận đăng ký tài khoản</h2>
                            <p style="margin: 0 0 20px; color: #666666; font-size: 16px; line-height: 1.6;">
                                Xin chào <strong>' . htmlspecialchars($name) . '</strong>,
                            </p>
                            <p style="margin: 0 0 30px; color: #666666; font-size: 16px; line-height: 1.6;">
                                Cảm ơn bạn đã đăng ký tài khoản. Vui lòng sử dụng mã OTP bên dưới để hoàn tất đăng ký:
                            </p>
                            <div style="text-align: center; margin: 30px 0;">
                                <div style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 40px; border-radius: 10px;">
                                    <span style="font-size: 36px; font-weight: bold; color: #ffffff; letter-spacing: 8px;">' . $otp_code . '</span>
                                </div>
                            </div>
                            <p style="margin: 30px 0 0; color: #999999; font-size: 14px; text-align: center;">
                                Mã có hiệu lực trong <strong>5 phút</strong>
                            </p>
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">
                            <p style="margin: 0; color: #999999; font-size: 13px; line-height: 1.6;">
                                Nếu bạn không yêu cầu đăng ký tài khoản, vui lòng bỏ qua email này.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 40px; background-color: #f8f9fa; border-radius: 0 0 10px 10px; text-align: center;">
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                © ' . date('Y') . ' ' . htmlspecialchars($site_name) . '. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

/**
 * Tạo mã OTP ngẫu nhiên 6 số
 */
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}
?>
