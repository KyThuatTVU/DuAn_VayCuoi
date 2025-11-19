<?php
session_start();
require_once 'includes/config.php';

// Trang test để kiểm tra session
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Session - Debug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .debug-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #e91e63;
            border-bottom: 2px solid #e91e63;
            padding-bottom: 10px;
        }
        .info-row {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .avatar-preview {
            margin-top: 20px;
            text-align: center;
        }
        .avatar-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #e91e63;
            object-fit: cover;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #e91e63;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #c2185b;
        }
    </style>
</head>
<body>
    <div class="debug-box">
        <h2>🔍 Debug Session Information</h2>
        
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <div class="info-row">
                <span class="label">Trạng thái đăng nhập:</span>
                <span class="value">✅ Đã đăng nhập</span>
            </div>
            
            <div class="info-row">
                <span class="label">User ID:</span>
                <span class="value"><?php echo htmlspecialchars($_SESSION['user_id'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">Tên người dùng:</span>
                <span class="value"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">Avatar URL:</span>
                <span class="value" style="word-break: break-all;">
                    <?php 
                    if (!empty($_SESSION['user_avatar'])) {
                        echo htmlspecialchars($_SESSION['user_avatar']);
                    } else {
                        echo '<span style="color: red;">❌ Không có avatar</span>';
                    }
                    ?>
                </span>
            </div>
            
            <?php if (!empty($_SESSION['user_avatar'])): ?>
                <div class="avatar-preview">
                    <h3>Preview Avatar:</h3>
                    <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" 
                         alt="Avatar Preview"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display: none; color: red; margin-top: 10px;">
                        ❌ Không thể load ảnh từ URL này
                    </div>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
                <h3>Kiểm tra Database:</h3>
                <?php
                $user_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT avt FROM nguoi_dung WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                ?>
                <div class="info-row">
                    <span class="label">Avatar trong DB:</span>
                    <span class="value" style="word-break: break-all;">
                        <?php echo !empty($user['avt']) ? htmlspecialchars($user['avt']) : '<span style="color: red;">❌ Trống</span>'; ?>
                    </span>
                </div>
            </div>
            
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <h3>❌ Chưa đăng nhập</h3>
                <p>Vui lòng đăng nhập để xem thông tin session</p>
                <a href="login.php" class="btn">Đăng nhập</a>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php" class="btn">← Về trang chủ</a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="logout.php" class="btn" style="background: #666;">Đăng xuất</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="debug-box">
        <h2>📋 Full Session Data</h2>
        <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;">
<?php print_r($_SESSION); ?>
        </pre>
    </div>
</body>
</html>
