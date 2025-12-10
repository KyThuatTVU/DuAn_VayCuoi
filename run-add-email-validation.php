<?php
require_once 'includes/config.php';
require_once 'includes/email-validator.php';

echo "<h2>Thêm cột xác thực email vào bảng lien_he</h2>";

try {
    // Kiểm tra xem cột đã tồn tại chưa
    $result = $conn->query("SHOW COLUMNS FROM lien_he LIKE 'email_is_valid'");
    
    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Các cột xác thực email đã tồn tại!</p>";
    } else {
        // Thêm các cột mới
        $sql = "ALTER TABLE lien_he 
                ADD COLUMN email_is_valid TINYINT(1) DEFAULT 1 COMMENT 'Email có đúng format không',
                ADD COLUMN email_is_real TINYINT(1) DEFAULT 1 COMMENT 'Email có thật không (kiểm tra DNS, MX record)',
                ADD COLUMN email_validation_reason VARCHAR(255) DEFAULT NULL COMMENT 'Lý do kết quả xác thực',
                ADD COLUMN email_validation_details TEXT DEFAULT NULL COMMENT 'Chi tiết xác thực (JSON)'";
        
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Thêm cột thành công!</p>";
            echo "<p>Đã thêm: email_is_valid, email_is_real, email_validation_reason, email_validation_details</p>";
        } else {
            echo "<p style='color: red;'>❌ Lỗi: " . $conn->error . "</p>";
        }
    }
    
    // Cập nhật các bản ghi cũ chưa được kiểm tra
    echo "<h3>Cập nhật email cho các liên hệ cũ...</h3>";
    
    $result = $conn->query("SELECT id, email FROM lien_he WHERE email_validation_reason IS NULL OR email_validation_reason = ''");
    $updated = 0;
    $fake_count = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $validation = validateEmailAdvanced($row['email']);
            $email_is_valid = $validation['is_valid'] ? 1 : 0;
            $email_is_real = $validation['is_real'] ? 1 : 0;
            $email_validation_reason = $validation['reason'];
            $email_validation_details = json_encode($validation['details'], JSON_UNESCAPED_UNICODE);
            
            $update_stmt = $conn->prepare("UPDATE lien_he SET email_is_valid = ?, email_is_real = ?, email_validation_reason = ?, email_validation_details = ? WHERE id = ?");
            $update_stmt->bind_param("iissi", $email_is_valid, $email_is_real, $email_validation_reason, $email_validation_details, $row['id']);
            $update_stmt->execute();
            $updated++;
            
            if (!$email_is_valid || !$email_is_real) {
                $fake_count++;
                echo "<p style='color: orange;'>⚠️ ID {$row['id']}: {$row['email']} - {$email_validation_reason}</p>";
            }
        }
        
        echo "<p style='color: green;'>✅ Đã cập nhật {$updated} liên hệ!</p>";
        if ($fake_count > 0) {
            echo "<p style='color: orange;'>⚠️ Phát hiện {$fake_count} email có thể giả/không hợp lệ</p>";
        }
    } else {
        echo "<p>Không có liên hệ nào cần cập nhật.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

$conn->close();
echo "<hr><p><a href='admin-contacts.php'>← Quay về Quản lý liên hệ</a></p>";
