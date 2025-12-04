<?php
/**
 * Helper functions để lấy và cập nhật cài đặt hệ thống
 */

// Cache cài đặt để tránh query nhiều lần
$_settings_cache = null;

/**
 * Lấy tất cả cài đặt từ database
 */
function getAllSettings($conn) {
    global $_settings_cache;
    
    if ($_settings_cache !== null) {
        return $_settings_cache;
    }
    
    $_settings_cache = [];
    
    try {
        $result = $conn->query("SELECT setting_key, setting_value FROM cai_dat");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $_settings_cache[$row['setting_key']] = $row['setting_value'];
            }
        }
    } catch (Exception $e) {
        // Nếu bảng chưa tồn tại, trả về mảng rỗng
        $_settings_cache = [];
    }
    
    return $_settings_cache;
}

/**
 * Lấy một cài đặt theo key
 */
function getSetting($conn, $key, $default = '') {
    $settings = getAllSettings($conn);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Lấy cài đặt theo nhóm
 */
function getSettingsByGroup($conn, $group) {
    $settings = [];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM cai_dat WHERE setting_group = ? ORDER BY sort_order ASC");
        $stmt->bind_param("s", $group);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $settings[] = $row;
        }
    } catch (Exception $e) {
        $settings = [];
    }
    
    return $settings;
}

/**
 * Cập nhật một cài đặt
 */
function updateSetting($conn, $key, $value) {
    global $_settings_cache;
    
    try {
        $stmt = $conn->prepare("UPDATE cai_dat SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $value, $key);
        $result = $stmt->execute();
        
        // Clear cache
        $_settings_cache = null;
        
        return $result;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Cập nhật nhiều cài đặt cùng lúc
 */
function updateSettings($conn, $settings) {
    global $_settings_cache;
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("UPDATE cai_dat SET setting_value = ? WHERE setting_key = ?");
        
        foreach ($settings as $key => $value) {
            $stmt->bind_param("ss", $value, $key);
            $stmt->execute();
        }
        
        $conn->commit();
        
        // Clear cache
        $_settings_cache = null;
        
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Lấy tất cả nhóm cài đặt
 */
function getSettingGroups($conn) {
    $groups = [];
    
    try {
        $result = $conn->query("SELECT DISTINCT setting_group FROM cai_dat ORDER BY setting_group");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $groups[] = $row['setting_group'];
            }
        }
    } catch (Exception $e) {
        $groups = [];
    }
    
    return $groups;
}

/**
 * Lấy nhãn tiếng Việt cho nhóm cài đặt
 */
function getGroupLabel($group) {
    $labels = [
        'contact' => 'Thông Tin Liên Hệ',
        'working' => 'Giờ Làm Việc',
        'social' => 'Mạng Xã Hội',
        'bank' => 'Thông Tin Ngân Hàng',
        'general' => 'Thông Tin Chung'
    ];
    
    return isset($labels[$group]) ? $labels[$group] : ucfirst($group);
}

/**
 * Lấy icon cho nhóm cài đặt
 */
function getGroupIcon($group) {
    $icons = [
        'contact' => 'fas fa-address-card',
        'working' => 'fas fa-clock',
        'social' => 'fas fa-share-alt',
        'bank' => 'fas fa-university',
        'general' => 'fas fa-cog'
    ];
    
    return isset($icons[$group]) ? $icons[$group] : 'fas fa-cog';
}
