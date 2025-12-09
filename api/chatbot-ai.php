<?php
/**
 * Chatbot AI API - Sử dụng Groq AI (Llama/Mixtral)
 * Trả lời thông minh cho cửa hàng váy cưới
 */

session_start();

// Load environment variables
require_once __DIR__ . '/../includes/env.php';
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Lấy user_id và session_id cho lưu lịch sử
$chatUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$chatSessionId = isset($_SESSION['chatbot_session_id']) ? $_SESSION['chatbot_session_id'] : null;
if (!$chatSessionId) {
    $chatSessionId = $chatUserId ? 'user_' . $chatUserId : 'guest_' . session_id() . '_' . time();
    $_SESSION['chatbot_session_id'] = $chatSessionId;
}
if ($chatUserId && strpos($chatSessionId, 'user_') === false) {
    $chatSessionId = 'user_' . $chatUserId;
    $_SESSION['chatbot_session_id'] = $chatSessionId;
}

/**
 * Lưu tin nhắn vào database
 */
function saveChatMessage($conn, $userId, $sessionId, $from, $message, $metadata = null) {
    $metadataJson = $metadata ? json_encode($metadata) : null;
    $stmt = $conn->prepare("
        INSERT INTO lich_su_chatbot (user_id, session_id, message_from, message, metadata)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $userId, $sessionId, $from, $message, $metadataJson);
    $stmt->execute();
    $stmt->close();
}

// Groq API Configuration - Đọc từ .env
$GROQ_API_KEY = getenv('GROQ_API_KEY') ?: '';
$GROQ_MODEL = getenv('GROQ_MODEL') ?: 'llama-3.3-70b-versatile';
$GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';

// Kiểm tra API key
if (empty($GROQ_API_KEY)) {
    echo json_encode([
        'success' => false,
        'error' => 'GROQ_API_KEY chưa được cấu hình trong file .env',
        'fallback' => true
    ]);
    exit;
}

// Lấy tin nhắn từ request
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim($input['message']) : '';
$conversationHistory = isset($input['history']) ? $input['history'] : [];

// Lấy thông tin người dùng đã đăng nhập
$userData = isset($input['user']) ? $input['user'] : null;
$isLoggedIn = $userData && isset($userData['isLoggedIn']) && $userData['isLoggedIn'];
$userName = $isLoggedIn && isset($userData['userName']) ? $userData['userName'] : null;
$userEmail = $isLoggedIn && isset($userData['userEmail']) ? $userData['userEmail'] : null;

if (empty($userMessage)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// Tạo thông tin khách hàng cho AI
$customerInfo = "";
if ($isLoggedIn && $userName) {
    $customerInfo = "
THÔNG TIN KHÁCH HÀNG ĐANG CHAT:
- Tên: {$userName}
- Email: {$userEmail}
- Trạng thái: Đã đăng nhập (khách hàng thân thiết)
- Lưu ý: Hãy gọi khách bằng tên \"{$userName}\" để tạo sự thân thiện. Có thể gợi ý khách xem lại đơn hàng, lịch sử thuê váy.
";
} else {
    $customerInfo = "
THÔNG TIN KHÁCH HÀNG ĐANG CHAT:
- Trạng thái: Khách vãng lai (chưa đăng nhập)
- Lưu ý: Gọi khách là \"chị/anh\". Có thể gợi ý khách đăng ký tài khoản để nhận ưu đãi.
";
}

// System prompt - Định hình chatbot
$systemPrompt = <<<PROMPT
Bạn là Trà My, tư vấn viên xinh đẹp và thân thiện của cửa hàng "Váy Cưới Thiên Thần" (Garden Home). 
{$customerInfo}
THÔNG TIN CỬA HÀNG:
- Tên: Váy Cưới Thiên Thần - Garden Home
- Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM
- Hotline: 078.797.2075
- Zalo: 0787972075
- Website: vaycuoithienthan.com
- Giờ mở cửa: 8h00 - 21h00 (Thứ 2 - Chủ nhật)

DỊCH VỤ:
1. Cho thuê váy cưới (2.000.000đ - 15.000.000đ/bộ)
2. May đo váy cưới theo yêu cầu (5.000.000đ - 50.000.000đ)
3. Cho thuê vest chú rể (500.000đ - 3.000.000đ/bộ)
4. Trang điểm cô dâu (1.500.000đ - 5.000.000đ)
5. Chụp ảnh cưới trọn gói (8.000.000đ - 30.000.000đ)
6. Phụ kiện cưới (khăn voan, vương miện, hoa cầm tay...)

BỘ SƯU TẬP VÁY CƯỚI:
- Váy cưới đuôi cá: Ôm sát body, tôn dáng, phù hợp cô dâu có body chuẩn
- Váy cưới công chúa (ballgown): Bồng bềnh, sang trọng, phù hợp đám cưới hoành tráng
- Váy cưới chữ A: Dễ mặc, phù hợp mọi dáng người
- Váy cưới tối giản (minimalist): Đơn giản, hiện đại, thanh lịch
- Váy cưới ren vintage: Cổ điển, lãng mạn
- Áo dài cưới: Truyền thống Việt Nam, đa dạng màu sắc

CÁCH TƯ VẤN:
- Luôn xưng hô "em" (là Trà My) và gọi khách là "chị/anh"
- Thân thiện, nhiệt tình, dùng emoji phù hợp 💕👰✨
- Trả lời ngắn gọn, dễ hiểu, không quá 3-4 câu
- Khi tư vấn váy, hỏi: chiều cao, cân nặng, số đo 3 vòng, phong cách yêu thích
- Luôn gợi ý khách đặt lịch hẹn để được tư vấn trực tiếp
- Nếu không biết thông tin, hướng dẫn khách gọi hotline

LINK QUAN TRỌNG:
- Xem váy cưới: products.php
- Đặt lịch hẹn: booking.php
- Liên hệ: contact.php
- Bài viết/Blog: blog.php

Hãy trả lời câu hỏi của khách hàng một cách tự nhiên, thân thiện như đang chat với bạn bè. Không sử dụng markdown, chỉ dùng text thuần và emoji.
PROMPT;

// Xây dựng messages cho API
$messages = [
    ['role' => 'system', 'content' => $systemPrompt]
];

// Thêm lịch sử hội thoại (giới hạn 10 tin nhắn gần nhất)
if (!empty($conversationHistory)) {
    $recentHistory = array_slice($conversationHistory, -10);
    foreach ($recentHistory as $msg) {
        $messages[] = [
            'role' => $msg['role'],
            'content' => $msg['content']
        ];
    }
}

// Thêm tin nhắn hiện tại
$messages[] = ['role' => 'user', 'content' => $userMessage];

// Gọi Groq API
$data = [
    'model' => $GROQ_MODEL,
    'messages' => $messages,
    'temperature' => 0.7,
    'max_tokens' => 500,
    'top_p' => 0.9,
    'stream' => false
];

$ch = curl_init($GROQ_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $GROQ_API_KEY
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Xử lý response
if ($curlError) {
    echo json_encode([
        'success' => false,
        'error' => 'Connection error: ' . $curlError,
        'fallback' => true
    ]);
    exit;
}

$result = json_decode($response, true);

if ($httpCode !== 200 || isset($result['error'])) {
    $errorMsg = isset($result['error']['message']) ? $result['error']['message'] : 'API Error';
    echo json_encode([
        'success' => false,
        'error' => $errorMsg,
        'fallback' => true
    ]);
    exit;
}

// Lấy câu trả lời từ AI
$aiResponse = isset($result['choices'][0]['message']['content']) 
    ? $result['choices'][0]['message']['content'] 
    : '';

if (empty($aiResponse)) {
    echo json_encode([
        'success' => false,
        'error' => 'Empty response from AI',
        'fallback' => true
    ]);
    exit;
}

// Lưu tin nhắn user vào database
saveChatMessage($conn, $chatUserId, $chatSessionId, 'user', $userMessage);

// Lưu tin nhắn bot vào database
saveChatMessage($conn, $chatUserId, $chatSessionId, 'bot', $aiResponse, [
    'model' => $result['model'] ?? 'llama-3.3-70b-versatile'
]);

// Thêm các link HTML nếu cần
$aiResponse = preg_replace('/products\.php/', '<a href="products.php" class="text-pink-500 underline font-medium">Bộ sưu tập</a>', $aiResponse);
$aiResponse = preg_replace('/booking\.php/', '<a href="booking.php" class="text-pink-500 underline font-medium">Đặt lịch hẹn</a>', $aiResponse);
$aiResponse = preg_replace('/contact\.php/', '<a href="contact.php" class="text-pink-500 underline font-medium">Liên hệ</a>', $aiResponse);
$aiResponse = preg_replace('/blog\.php/', '<a href="blog.php" class="text-pink-500 underline font-medium">Blog</a>', $aiResponse);

echo json_encode([
    'success' => true,
    'message' => $aiResponse,
    'model' => $result['model'] ?? 'llama-3.3-70b-versatile',
    'session_id' => $chatSessionId
]);
