<?php
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';

echo "<h2>Debug Notification System</h2>";

// 1. Kiểm tra cấu trúc bảng thong_bao
echo "<h3>1. Cấu trúc bảng thong_bao:</h3>";
$result = $conn->query("DESCRIBE thong_bao");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "❌ Bảng thong_bao chưa tồn tại! Lỗi: " . $conn->error;
}

// 2. Xem tất cả thông báo hiện có
echo "<h3>2. Thông báo hiện có:</h3>";
$result = $conn->query("SELECT * FROM thong_bao ORDER BY created_at DESC LIMIT 10");
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>User ID</th><th>Loại</th><th>Tiêu đề</th><th>Nội dung</th><th>Link</th><th>Đã đọc</th><th>Ngày tạo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nguoi_dung_id']}</td>";
            echo "<td>{$row['loai']}</td>";
            echo "<td>" . htmlspecialchars($row['tieu_de']) . "</td>";
            echo "<td>" . htmlspecialchars($row['noi_dung']) . "</td>";
            echo "<td>" . htmlspecialchars($row['link'] ?? '') . "</td>";
            echo "<td>{$row['da_doc']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ Chưa có thông báo nào trong bảng.";
    }
} else {
    echo "❌ Lỗi: " . $conn->error;
}

// 3. Lấy danh sách users
echo "<h3>3. Users hiện có:</h3>";
$result = $conn->query("SELECT id, ho_ten, email FROM nguoi_dung LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Họ tên</th><th>Email</th></tr>";
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
        echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['ho_ten']) . "</td><td>" . htmlspecialchars($row['email']) . "</td></tr>";
    }
    echo "</table>";
    
    // 4. Test insert trực tiếp
    if (count($users) >= 1) {
        $test_user = $users[0];
        echo "<h3>4. Test insert trực tiếp vào bảng thong_bao cho user ID {$test_user['id']}:</h3>";
        $test_sql = "INSERT INTO thong_bao (nguoi_dung_id, loai, tieu_de, noi_dung, link) VALUES (?, 'system', 'Test notification', 'Đây là test thông báo', 'index.php')";
        $stmt = $conn->prepare($test_sql);
        if ($stmt) {
            $stmt->bind_param("i", $test_user['id']);
            if ($stmt->execute()) {
                echo "✅ Insert trực tiếp thành công, ID = " . $conn->insert_id;
            } else {
                echo "❌ Lỗi execute: " . $stmt->error;
            }
        } else {
            echo "❌ Lỗi prepare: " . $conn->error;
        }
        
        // 5. Test hàm createNotification
        echo "<h3>5. Test hàm createNotification():</h3>";
        $result = createNotification(
            $conn,
            $test_user['id'],
            'comment_reply',
            'Test - Ai đó trả lời bình luận',
            'Nội dung test notification',
            'product-detail.php?id=1#comments',
            1,
            'comment_product'
        );
        if ($result) {
            echo "✅ createNotification() thành công!";
        } else {
            echo "❌ createNotification() thất bại!";
        }
        
        // 6. Test hàm notifyCommentReply nếu có ít nhất 2 users
        if (count($users) >= 2) {
            $owner = $users[0];
            $replier = $users[1];
            echo "<h3>6. Test hàm notifyCommentReply() - User {$replier['id']} reply cho User {$owner['id']}:</h3>";
            $result = notifyCommentReply(
                $conn,
                $owner['id'],      // owner
                $replier['id'],    // replier
                $replier['ho_ten'], // replier name
                'product',          // type
                1,                  // item_id
                'Váy cưới test',    // item_name
                'Đây là nội dung bình luận test'
            );
            if ($result) {
                echo "✅ notifyCommentReply() thành công!";
            } else {
                echo "❌ notifyCommentReply() thất bại!";
            }
        }
    }
} else {
    echo "⚠️ Không có user nào trong database.";
}

// 7. Kiểm tra lại thông báo sau khi test
echo "<h3>7. Thông báo sau khi test:</h3>";
$result = $conn->query("SELECT * FROM thong_bao ORDER BY created_at DESC LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>User ID</th><th>Loại</th><th>Tiêu đề</th><th>Ngày tạo</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nguoi_dung_id']}</td>";
        echo "<td>{$row['loai']}</td>";
        echo "<td>" . htmlspecialchars($row['tieu_de']) . "</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "⚠️ Không có thông báo nào.";
}
?>
