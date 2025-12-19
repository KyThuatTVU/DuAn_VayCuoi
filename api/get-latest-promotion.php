<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

try {
    // Get latest active promotion from khuyen_mai table
    $query = $conn->prepare("
        SELECT 
            id,
            code as promo_code,
            title,
            description,
            type,
            value,
            min_order_amount,
            start_at as start_date,
            end_at as end_date,
            usage_limit
        FROM khuyen_mai 
        WHERE (start_at IS NULL OR start_at <= NOW()) 
        AND (end_at IS NULL OR end_at >= NOW())
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $promotion = $result->fetch_assoc();
        
        // Format discount value for display
        if ($promotion['type'] === 'percentage') {
            $promotion['discount_value'] = $promotion['value'] . '% OFF';
        } else {
            $promotion['discount_value'] = number_format($promotion['value'], 0, ',', '.') . 'đ OFF';
        }
        
        // Add subtitle if min order amount exists
        if ($promotion['min_order_amount'] > 0) {
            $promotion['subtitle'] = 'Áp dụng cho đơn hàng từ ' . number_format($promotion['min_order_amount'], 0, ',', '.') . 'đ';
        } else {
            $promotion['subtitle'] = 'Áp dụng cho tất cả đơn hàng';
        }
        
        echo json_encode([
            'success' => true,
            'promotion' => $promotion
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No active promotion found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
