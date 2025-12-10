<?php
/**
 * Email Validator Helper
 * Kiểm tra email thật/giả và xác thực tên miền
 */

/**
 * Kiểm tra email có thật không
 * @param string $email Địa chỉ email cần kiểm tra
 * @return array ['is_valid' => bool, 'is_real' => bool, 'reason' => string, 'details' => array]
 */
function validateEmailAdvanced($email) {
    $result = [
        'is_valid' => false,
        'is_real' => false,
        'reason' => '',
        'details' => []
    ];
    
    // Kiểm tra format cơ bản
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result['reason'] = 'Email không đúng định dạng';
        return $result;
    }
    
    $result['is_valid'] = true;
    
    // Tách domain từ email
    list($user, $domain) = explode('@', $email);
    
    // Kiểm tra domain có tồn tại không
    if (!checkdnsrr($domain, 'ANY')) {
        $result['is_real'] = false;
        $result['reason'] = 'Tên miền không tồn tại';
        $result['details']['domain_exists'] = false;
        return $result;
    }
    
    $result['details']['domain_exists'] = true;
    
    // Kiểm tra MX record (Mail Exchange)
    $mx_records = [];
    if (!getmxrr($domain, $mx_records)) {
        // Nếu không có MX record, có thể vẫn là email hợp lệ nếu domain có A record
        if (checkdnsrr($domain, 'A')) {
            $result['details']['mx_records'] = false;
            $result['details']['has_a_record'] = true;
            $result['is_real'] = true;
            $result['reason'] = 'Email có thể nhận được (qua A record)';
        } else {
            $result['is_real'] = false;
            $result['reason'] = 'Tên miền không có máy chủ email';
            $result['details']['mx_records'] = false;
            $result['details']['has_a_record'] = false;
            return $result;
        }
    } else {
        $result['details']['mx_records'] = true;
        $result['details']['mx_count'] = count($mx_records);
        $result['is_real'] = true;
        $result['reason'] = 'Email hợp lệ và có thể nhận được';
    }
    
    // Kiểm tra các dịch vụ email tạm thời/giả phổ biến
    $temp_domains = [
        'tempmail.com', 'guerrillamail.com', '10minutemail.com', 
        'throwaway.email', 'mailinator.com', 'maildrop.cc',
        'temp-mail.org', 'getnada.com', 'trashmail.com',
        'fakeinbox.com', 'yopmail.com', 'sharklasers.com',
        'guerrillamail.info', 'grr.la', 'guerrillamail.biz',
        'guerrillamail.de', 'spam4.me', 'mailnesia.com',
        'mohmal.com', 'emailondeck.com', 'tempinbox.com'
    ];
    
    if (in_array(strtolower($domain), $temp_domains)) {
        $result['is_real'] = false;
        $result['reason'] = 'Email tạm thời/giả (dịch vụ disposable email)';
        $result['details']['is_temp_email'] = true;
        return $result;
    }
    
    $result['details']['is_temp_email'] = false;
    
    // Kiểm tra các nhà cung cấp email phổ biến (thêm độ tin cậy)
    $trusted_domains = [
        'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com',
        'icloud.com', 'aol.com', 'mail.com', 'protonmail.com',
        'zoho.com', 'yandex.com', 'gmx.com', 'live.com',
        'msn.com', 'facebook.com', 'googlemail.com'
    ];
    
    if (in_array(strtolower($domain), $trusted_domains)) {
        $result['details']['is_trusted_provider'] = true;
        $result['reason'] = 'Email từ nhà cung cấp uy tín';
    } else {
        $result['details']['is_trusted_provider'] = false;
    }
    
    // Kiểm tra độ dài username có hợp lý không (phát hiện email random)
    if (strlen($user) > 30 || preg_match('/^[a-z0-9]{20,}$/i', $user)) {
        $result['details']['suspicious_username'] = true;
        $result['reason'] .= ' (Tên người dùng đáng ngờ)';
    } else {
        $result['details']['suspicious_username'] = false;
    }
    
    return $result;
}

/**
 * Kiểm tra đơn giản chỉ trả về true/false
 */
function isRealEmail($email) {
    $validation = validateEmailAdvanced($email);
    return $validation['is_valid'] && $validation['is_real'];
}

/**
 * Lấy icon và màu cho badge dựa trên kết quả kiểm tra
 */
function getEmailBadgeInfo($is_valid, $is_real) {
    if (!$is_valid) {
        return [
            'icon' => 'fa-times-circle',
            'class' => 'bg-red-100 text-red-700 border-red-300',
            'text' => 'Email không hợp lệ'
        ];
    }
    
    if (!$is_real) {
        return [
            'icon' => 'fa-exclamation-triangle',
            'class' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'text' => 'Email giả/tạm thời'
        ];
    }
    
    return [
        'icon' => 'fa-check-circle',
        'class' => 'bg-green-100 text-green-700 border-green-300',
        'text' => 'Email hợp lệ'
    ];
}

/**
 * Tạo mailto link nếu email thật
 */
function getEmailAction($email, $is_valid, $is_real) {
    if ($is_valid && $is_real) {
        return "href='mailto:" . htmlspecialchars($email) . "'";
    }
    return "href='#' onclick='alert(\"Email này có thể không thật. Vui lòng kiểm tra kỹ trước khi liên hệ.\"); return false;'";
}
