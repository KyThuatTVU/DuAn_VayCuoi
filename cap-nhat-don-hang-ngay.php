<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Bảng Đơn Hàng</title>
    <style>
        body { font-family: Arial; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        .step { background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .step h2 { color: #667eea; margin-bottom: 15px; }
        .btn { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; margin: 10px 5px; font-weight: bold; text-align: center; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; color: #e83e8c; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Cập Nhật Bảng Đơn Hàng</h1>
        
        <?php
        $success_count = 0;
        $error_count = 0;
        $skip_count = 0;
        
        // Lấy danh sách cột hiện có
        $existing = $conn->query("DESCRIBE don_hang");
        $existing_columns = [];
        while ($row = $existing->fetch_assoc()) {
            $existing_columns[] = $row['Field'];
        }
        
        echo "<div class='step'>
                <h2>📋 Cột hiện có: " . count($existing_columns) . " cột</h2>
                <p>" . implode(', ', array_map(function($col) { return "<code>$col</code>"; }, $existing_columns)) . "</p>
              </div>";
        
        // Danh sách cột cần thêm
        $columns = [
            [
                'name' => 'ma_don_hang',
                'sql' => "ADD COLUMN ma_don_hang VARCHAR(50) UNIQUE AFTER id",
                'desc' => 'Mã đơn hàng duy nhất'
            ],
            [
                'name' => 'ho_ten',
                'sql' => "ADD COLUMN ho_ten VARCHAR(255) NOT NULL DEFAULT '' AFTER nguoi_dung_id",
                'desc' => 'Họ tên người nhận'
            ],
            [
                'name' => 'so_dien_thoai',
                'sql' => "ADD COLUMN so_dien_thoai VARCHAR(30) NOT NULL DEFAULT '' AFTER ho_ten",
                'desc' => 'Số điện thoại người nhận'
            ],
            [
                'name' => 'dia_chi',
                'sql' => "ADD COLUMN dia_chi TEXT NULL AFTER so_dien_thoai",
                'desc' => 'Địa chỉ nhận váy'
            ],
            [
                'name' => 'ghi_chu',
                'sql' => "ADD COLUMN ghi_chu TEXT NULL AFTER dia_chi",
                'desc' => 'Ghi chú đơn hàng'
            ],
            [
                'name' => 'phuong_thuc_thanh_toan',
                'sql' => "ADD COLUMN phuong_thuc_thanh_toan VARCHAR(50) DEFAULT 'qr_code' AFTER trang_thai",
                'desc' => 'Phương thức thanh toán'
            ],
            [
                'name' => 'trang_thai_thanh_toan',
                'sql' => "ADD COLUMN trang_thai_thanh_toan ENUM('pending','paid','failed','expired') DEFAULT 'pending' AFTER phuong_thuc_thanh_toan",
                'desc' => 'Trạng thái thanh toán'
            ],
            [
                'name' => 'updated_at',
                'sql' => "ADD COLUMN updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at",
                'desc' => 'Thời gian cập nhật'
            ]
        ];
        
        echo "<div class='step'><h2>➕ Đang thêm các cột...</h2>";
        
        foreach ($columns as $col) {
            if (in_array($col['name'], $existing_columns)) {
                echo "<div class='info'>⚠ <code>{$col['name']}</code> - Đã tồn tại</div>";
                $skip_count++;
            } else {
                $sql = "ALTER TABLE don_hang " . $col['sql'];
                
                if ($conn->query($sql)) {
                    echo "<div class='success'>✓ <code>{$col['name']}</code> - {$col['desc']}</div>";
                    $success_count++;
                } else {
                    echo "<div class='error'>✗ <code>{$col['name']}</code> - Lỗi: " . $conn->error . "</div>";
                    $error_count++;
                }
            }
        }
        
        echo "</div>";
        
        // Thêm index
        echo "<div class='step'><h2>🔍 Thêm index...</h2>";
        
        $indexes = [
            "CREATE INDEX idx_ma_don_hang ON don_hang(ma_don_hang)",
            "CREATE INDEX idx_trang_thai ON don_hang(trang_thai)",
            "CREATE INDEX idx_trang_thai_thanh_toan ON don_hang(trang_thai_thanh_toan)"
        ];
        
        foreach ($indexes as $idx_sql) {
            if ($conn->query($idx_sql)) {
                echo "<div class='success'>✓ Đã tạo index</div>";
            } else {
                // Bỏ qua lỗi nếu index đã tồn tại
                if (strpos($conn->error, 'Duplicate key name') === false) {
                    echo "<div class='info'>⚠ Index có thể đã tồn tại</div>";
                }
            }
        }
        
        echo "</div>";
        
        // Kiểm tra lại
        echo "<div class='step'><h2>✅ Kiểm tra lại cấu trúc</h2>";
        
        $final = $conn->query("DESCRIBE don_hang");
        echo "<table style='width: 100%; border-collapse: collapse;'>
                <tr style='background: #667eea; color: white;'>
                    <th style='padding: 10px; text-align: left;'>Cột</th>
                    <th style='padding: 10px; text-align: left;'>Kiểu</th>
                    <th style='padding: 10px; text-align: left;'>Null</th>
                    <th style='padding: 10px; text-align: left;'>Default</th>
                </tr>";
        
        while ($row = $final->fetch_assoc()) {
            $is_new = !in_array($row['Field'], $existing_columns);
            $style = $is_new ? "background: #d4edda;" : "";
            
            echo "<tr style='$style'>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd;'><code>{$row['Field']}</code>" . ($is_new ? " <strong style='color: #28a745;'>(MỚI)</strong>" : "") . "</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['Type']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['Null']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . ($row['Default'] ?? 'NULL') . "</td>
                  </tr>";
        }
        echo "</table></div>";
        
        // Tổng kết
        echo "<div class='step'><h2>📊 Tổng kết</h2>";
        echo "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0;'>";
        echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; text-align: center;'>
                <div style='font-size: 36px; font-weight: bold;'>$success_count</div>
                <div>Đã thêm</div>
              </div>";
        echo "<div style='background: #ffc107; color: #333; padding: 20px; border-radius: 10px; text-align: center;'>
                <div style='font-size: 36px; font-weight: bold;'>$skip_count</div>
                <div>Đã có</div>
              </div>";
        echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; text-align: center;'>
                <div style='font-size: 36px; font-weight: bold;'>$error_count</div>
                <div>Lỗi</div>
              </div>";
        echo "</div>";
        
        if ($error_count == 0) {
            echo "<div class='success'>
                    <h3 style='margin-bottom: 10px;'>🎉 Hoàn tất!</h3>
                    <p>Bảng <code>don_hang</code> đã được cập nhật thành công!</p>
                    <p>Bây giờ bạn có thể sử dụng chức năng thanh toán QR code.</p>
                  </div>";
            
            echo "<div style='text-align: center; margin-top: 30px;'>
                    <a href='checkout.php' class='btn' style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%);'>💳 Đi đến Thanh Toán</a>
                    <a href='cart.php' class='btn'>🛒 Xem Giỏ Hàng</a>
                    <a href='products.php' class='btn'>🛍️ Váy Cưới</a>
                  </div>";
        } else {
            echo "<div class='error'>
                    <h3>⚠️ Có lỗi xảy ra!</h3>
                    <p>Vui lòng kiểm tra lại các lỗi ở trên.</p>
                  </div>";
            
            echo "<div style='text-align: center; margin-top: 20px;'>
                    <a href='cap-nhat-don-hang-ngay.php' class='btn' style='background: #dc3545;'>🔄 Chạy lại</a>
                  </div>";
        }
        
        echo "</div>";
        
        $conn->close();
        ?>
    </div>
</body>
</html>
