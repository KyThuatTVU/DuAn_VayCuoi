<?php
/**
 * Chatbot History API
 * Quản lý lịch sử chat cho mỗi tài khoản người dùng
 */

session_start();
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Lấy user_id từ session (nếu đã đăng nhập)
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// Lấy hoặc tạo session_id cho guest users
$sessionId = isset($_SESSION['chatbot_session_id']) ? $_SESSION['chatbot_session_id'] : null;
if (!$sessionId) {
    $sessionId = 'guest_' . session_id() . '_' . time();
    $_SESSION['chatbot_session_id'] = $sessionId;
}

// Nếu user đã đăng nhập, sử dụng user_id làm session identifier
if ($userId) {
    $sessionId = 'user_' . $userId;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_history':
        // Lấy lịch sử chat
        getHistory($conn, $userId, $sessionId);
        break;
    
    case 'save_message':
        // Lưu tin nhắn mới
        saveMessage($conn, $userId, $sessionId);
        break;
    
    case 'clear_history':
        // Làm mới đoạn chat (xóa lịch sử và tạo session mới)
        clearHistory($conn, $userId, $sessionId);
        break;
    
    case 'get_sessions':
        // Lấy danh sách các phiên chat (chỉ cho user đã đăng nhập)
        getSessions($conn, $userId);
        break;
    
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

/**
 * Lấy lịch sử chat
 */
function getHistory($conn, $userId, $sessionId) {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    // Nếu có chỉ định session cụ thể
    $targetSession = isset($_GET['session_id']) ? $_GET['session_id'] : $sessionId;
    
    // Chỉ cho phép xem session của chính mình
    if ($userId) {
        // User đã đăng nhập: chỉ xem session của user_id này
        $stmt = $conn->prepare("
            SELECT id, message_from, message, metadata, created_at 
            FROM lich_su_chatbot 
            WHERE user_id = ? AND session_id = ?
            ORDER BY created_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("isii", $userId, $targetSession, $limit, $offset);
    } else {
        // Guest: chỉ xem session hiện tại
        $stmt = $conn->prepare("
            SELECT id, message_from, message, metadata, created_at 
            FROM lich_su_chatbot 
            WHERE session_id = ? AND user_id IS NULL
            ORDER BY created_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("sii", $sessionId, $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['id'],
            'from' => $row['message_from'],
            'message' => $row['message'],
            'metadata' => $row['metadata'] ? json_decode($row['metadata'], true) : null,
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'session_id' => $targetSession,
        'messages' => $messages,
        'count' => count($messages)
    ]);
}

/**
 * Lưu tin nhắn mới
 */
function saveMessage($conn, $userId, $sessionId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $messageFrom = isset($input['from']) ? $input['from'] : '';
    $message = isset($input['message']) ? trim($input['message']) : '';
    $metadata = isset($input['metadata']) ? json_encode($input['metadata']) : null;
    
    if (!in_array($messageFrom, ['user', 'bot'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid message_from value']);
        return;
    }
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Message is required']);
        return;
    }
    
    $stmt = $conn->prepare("
        INSERT INTO lich_su_chatbot (user_id, session_id, message_from, message, metadata)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $userId, $sessionId, $messageFrom, $message, $metadata);
    
    if ($stmt->execute()) {
        $insertId = $stmt->insert_id;
        $stmt->close();
        
        echo json_encode([
            'success' => true,
            'message_id' => $insertId,
            'session_id' => $sessionId
        ]);
    } else {
        $stmt->close();
        echo json_encode(['success' => false, 'error' => 'Failed to save message']);
    }
}

/**
 * Làm mới đoạn chat - Tạo session mới
 */
function clearHistory($conn, $userId, $sessionId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        return;
    }
    
    // Tạo session mới
    $newSessionId = $userId ? 'user_' . $userId . '_' . time() : 'guest_' . session_id() . '_' . time();
    $_SESSION['chatbot_session_id'] = $newSessionId;
    
    echo json_encode([
        'success' => true,
        'message' => 'Chat history cleared',
        'new_session_id' => $newSessionId
    ]);
}

/**
 * Lấy danh sách các phiên chat (chỉ cho user đã đăng nhập)
 */
function getSessions($conn, $userId) {
    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'Bạn cần đăng nhập để xem lịch sử chat']);
        return;
    }
    
    $stmt = $conn->prepare("
        SELECT session_id, 
               MIN(created_at) as started_at, 
               MAX(created_at) as last_message_at,
               COUNT(*) as message_count
        FROM lich_su_chatbot 
        WHERE user_id = ?
        GROUP BY session_id
        ORDER BY last_message_at DESC
        LIMIT 20
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sessions = [];
    while ($row = $result->fetch_assoc()) {
        // Lấy tin nhắn đầu tiên của phiên để hiển thị preview
        $previewStmt = $conn->prepare("
            SELECT message FROM lich_su_chatbot 
            WHERE session_id = ? AND message_from = 'user'
            ORDER BY created_at ASC
            LIMIT 1
        ");
        $previewStmt->bind_param("s", $row['session_id']);
        $previewStmt->execute();
        $previewResult = $previewStmt->get_result();
        $preview = $previewResult->fetch_assoc();
        $previewStmt->close();
        
        $sessions[] = [
            'session_id' => $row['session_id'],
            'started_at' => $row['started_at'],
            'last_message_at' => $row['last_message_at'],
            'message_count' => $row['message_count'],
            'preview' => $preview ? mb_substr($preview['message'], 0, 50) . '...' : 'Chưa có tin nhắn'
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'sessions' => $sessions
    ]);
}
?>
