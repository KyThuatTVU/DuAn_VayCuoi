<?php
/**
 * Debug script để kiểm tra và test thông báo khi user reply comment
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';

echo "<html><head><title>Debug Comment Notification</title>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
h2 { color: #e91e63; border-bottom: 2px solid #e91e63; padding-bottom: 10px; }
h3 { color: #333; margin-top: 25px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background: #f5f5f5; }
pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
.btn { display: inline-block; padding: 10px 20px; background: #e91e63; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
.btn:hover { background: #c2185b; }
.box { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style></head><body>";

echo "<h2>🔔 Debug Comment Notification System</h2>";

// 1. Kiểm tra bảng thong_bao
echo "<h3>1. Kiểm tra bảng thong_bao</h3>";
$check = $conn->query("SHOW TABLES LIKE 'thong_bao'");
if ($check && $check->num_rows > 0) {
    echo "<span class='success'>✅ Bảng thong_bao TỒN TẠI</span><br>";
    
    // Hiển thị cấu trúc
    $columns = $conn->query("DESCRIBE thong_bao");
    echo "<details><summary>Xem cấu trúc bảng</summary><pre>";
    while ($col = $columns->fetch_assoc()) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }
    echo "</pre></details>";
} else {
    echo "<span class='error'>❌ Bảng thong_bao KHÔNG TỒN TẠI</span><br>";
    echo "<a href='run-create-thong-bao.php' class='btn'>Tạo bảng thong_bao</a>";
}

// 2. Kiểm tra bảng bình luận
echo "<h3>2. Kiểm tra bảng bình luận</h3>";
$tables = ['binh_luan_san_pham', 'binh_luan_bai_viet'];
foreach ($tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        echo "<div class='box'>";
        echo "<strong>$table:</strong> <span class='success'>✅ TỒN TẠI</span><br>";
        
        // Kiểm tra các cột quan trọng
        $col_check = $conn->query("SHOW COLUMNS FROM $table LIKE 'is_admin_reply'");
        $has_admin = $col_check && $col_check->num_rows > 0;
        echo "- Cột is_admin_reply: " . ($has_admin ? "<span class='success'>CÓ</span>" : "<span class='warning'>KHÔNG CÓ</span>") . "<br>";
        
        $col_check2 = $conn->query("SHOW COLUMNS FROM $table LIKE 'reply_to_id'");
        $has_reply_to = $col_check2 && $col_check2->num_rows > 0;
        echo "- Cột reply_to_id: " . ($has_reply_to ? "<span class='success'>CÓ</span>" : "<span class='warning'>KHÔNG CÓ</span>") . "<br>";
        
        $col_check3 = $conn->query("SHOW COLUMNS FROM $table LIKE 'nguoi_dung_id'");
        $has_user = $col_check3 && $col_check3->num_rows > 0;
        echo "- Cột nguoi_dung_id: " . ($has_user ? "<span class='success'>CÓ</span>" : "<span class='error'>KHÔNG CÓ</span>") . "<br>";
        echo "</div>";
    } else {
        echo "<span class='error'>❌ Bảng $table KHÔNG TỒN TẠI</span><br>";
    }
}

// 3. Kiểm tra users
echo "<h3>3. Danh sách Users (để test)</h3>";
$users = $conn->query("SELECT id, ho_ten, email FROM nguoi_dung ORDER BY id LIMIT 10");
if ($users && $users->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Họ tên</th><th>Email</th></tr>";
    $user_list = [];
    while ($u = $users->fetch_assoc()) {
        $user_list[] = $u;
        echo "<tr><td>{$u['id']}</td><td>" . htmlspecialchars($u['ho_ten']) . "</td><td>" . htmlspecialchars($u['email']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<span class='error'>Không có user nào trong database</span>";
}

// 4. Kiểm tra thông báo comment_reply hiện có
echo "<h3>4. Thông báo comment_reply hiện có</h3>";
$notifs = $conn->query("SELECT * FROM thong_bao WHERE loai = 'comment_reply' ORDER BY created_at DESC LIMIT 10");
if ($notifs && $notifs->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>User ID</th><th>Tiêu đề</th><th>Nội dung</th><th>Link</th><th>Đã đọc</th><th>Thời gian</th></tr>";
    while ($n = $notifs->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$n['id']}</td>";
        echo "<td>{$n['nguoi_dung_id']}</td>";
        echo "<td>" . htmlspecialchars($n['tieu_de']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($n['noi_dung'], 0, 50)) . "...</td>";
        echo "<td>" . htmlspecialchars($n['link'] ?? '') . "</td>";
        echo "<td>" . ($n['da_doc'] ? '✅' : '❌') . "</td>";
        echo "<td>{$n['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<span class='warning'>⚠️ Chưa có thông báo comment_reply nào</span><br>";
    echo "<p>Điều này có nghĩa là chưa có ai reply bình luận hoặc hệ thống chưa hoạt động đúng.</p>";
}

// 5. Test gửi thông báo thủ công
echo "<h3>5. Test gửi thông báo thủ công</h3>";

if (isset($_GET['test_notify']) && count($user_list) >= 2) {
    $owner_id = (int)$user_list[0]['id'];
    $replier_id = (int)$user_list[1]['id'];
    $replier_name = $user_list[1]['ho_ten'];
    
    echo "<div class='box'>";
    echo "<strong>Testing notifyCommentReply():</strong><br>";
    echo "- Owner (người nhận thông báo): User ID $owner_id ({$user_list[0]['ho_ten']})<br>";
    echo "- Replier (người trả lời): User ID $replier_id ($replier_name)<br>";
    
    $result = notifyCommentReply(
        $conn,
        $owner_id,
        $replier_id,
        $replier_name,
        'product',
        1,
        'Sản phẩm Test',
        'Nội dung test reply lúc ' . date('H:i:s d/m/Y')
    );
    
    if ($result) {
        echo "<br><span class='success'>✅ GỬI THÔNG BÁO THÀNH CÔNG!</span><br>";
        echo "User ID $owner_id sẽ nhận được thông báo trong hộp thông báo.";
    } else {
        echo "<br><span class='error'>❌ GỬI THÔNG BÁO THẤT BẠI</span><br>";
        echo "MySQL Error: " . $conn->error;
    }
    echo "</div>";
}

if (count($user_list) >= 2) {
    echo "<a href='?test_notify=1' class='btn'>🧪 Test gửi thông báo</a>";
} else {
    echo "<span class='warning'>Cần ít nhất 2 user để test</span>";
}

// 6. Kiểm tra bình luận có parent_id
echo "<h3>6. Bình luận có parent_id (replies) gần đây</h3>";
$replies = $conn->query("
    SELECT bl.id, bl.nguoi_dung_id as replier_id, bl.parent_id, bl.noi_dung, bl.created_at,
           nd.ho_ten as replier_name,
           parent_bl.nguoi_dung_id as owner_id,
           owner_nd.ho_ten as owner_name
    FROM binh_luan_san_pham bl
    LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
    LEFT JOIN binh_luan_san_pham parent_bl ON bl.parent_id = parent_bl.id
    LEFT JOIN nguoi_dung owner_nd ON parent_bl.nguoi_dung_id = owner_nd.id
    WHERE bl.parent_id IS NOT NULL
    ORDER BY bl.created_at DESC
    LIMIT 10
");

if ($replies && $replies->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Reply ID</th><th>Người reply</th><th>Parent ID</th><th>Chủ comment gốc</th><th>Nội dung</th><th>Thời gian</th></tr>";
    while ($r = $replies->fetch_assoc()) {
        $should_notify = ($r['replier_id'] != $r['owner_id']) && $r['owner_id'];
        echo "<tr>";
        echo "<td>{$r['id']}</td>";
        echo "<td>ID {$r['replier_id']} ({$r['replier_name']})</td>";
        echo "<td>{$r['parent_id']}</td>";
        echo "<td>ID {$r['owner_id']} ({$r['owner_name']})</td>";
        echo "<td>" . htmlspecialchars(substr($r['noi_dung'], 0, 30)) . "...</td>";
        echo "<td>{$r['created_at']}</td>";
        echo "</tr>";
        
        if ($should_notify) {
            // Kiểm tra xem đã có thông báo chưa
            $check_notif = $conn->query("SELECT id FROM thong_bao WHERE nguoi_dung_id = {$r['owner_id']} AND loai = 'comment_reply' AND created_at >= '{$r['created_at']}' LIMIT 1");
            if (!$check_notif || $check_notif->num_rows == 0) {
                echo "<tr style='background: #fff3cd;'><td colspan='6'>⚠️ Comment này chưa có thông báo tương ứng cho User {$r['owner_id']}</td></tr>";
            }
        }
    }
    echo "</table>";
} else {
    echo "<span class='warning'>Chưa có reply bình luận nào trong sản phẩm</span><br>";
}

// 7. Kiểm tra hàm tồn tại
echo "<h3>7. Kiểm tra hàm notification</h3>";
echo "- notifyCommentReply(): " . (function_exists('notifyCommentReply') ? "<span class='success'>✅ TỒN TẠI</span>" : "<span class='error'>❌ KHÔNG TỒN TẠI</span>") . "<br>";
echo "- createNotification(): " . (function_exists('createNotification') ? "<span class='success'>✅ TỒN TẠI</span>" : "<span class='error'>❌ KHÔNG TỒN TẠI</span>") . "<br>";

// 8. Session info
echo "<h3>8. Thông tin Session</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<span class='success'>✅ Đang đăng nhập với User ID: {$_SESSION['user_id']}</span>";
    if (isset($_SESSION['user_name'])) {
        echo " ({$_SESSION['user_name']})";
    }
} else {
    echo "<span class='warning'>⚠️ Chưa đăng nhập</span>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Quay về trang chủ</a> | <a href='notifications.php'>Xem trang thông báo</a> | <a href='test-notifications.php'>Test notifications cũ</a></p>";

echo "</body></html>";
?>
