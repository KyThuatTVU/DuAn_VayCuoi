<?php
/**
 * Script tạo ảnh placeholder tự động
 * Chạy file này 1 lần để tạo tất cả ảnh cần thiết
 */

// Tạo thư mục nếu chưa có
$imageDir = 'assets/images';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
    echo "✓ Đã tạo thư mục: $imageDir<br>";
}

// Danh sách ảnh cần tạo
$images = [
    // Logo
    ['name' => 'logo.png', 'width' => 200, 'height' => 60, 'text' => 'LOGO'],
    
    // Hero
    ['name' => 'hero-1.jpg', 'width' => 1920, 'height' => 600, 'text' => 'Hero Banner'],
    
    // Categories
    ['name' => 'cat-princess.jpg', 'width' => 400, 'height' => 300, 'text' => 'Công Chúa'],
    ['name' => 'cat-mermaid.jpg', 'width' => 400, 'height' => 300, 'text' => 'Đuôi Cá'],
    ['name' => 'cat-aline.jpg', 'width' => 400, 'height' => 300, 'text' => 'Chữ A'],
    ['name' => 'cat-modern.jpg', 'width' => 400, 'height' => 300, 'text' => 'Hiện Đại'],
    
    // Promo
    ['name' => 'promo-dress.png', 'width' => 500, 'height' => 400, 'text' => 'Promo'],
    
    // Blog
    ['name' => 'blog-featured.jpg', 'width' => 800, 'height' => 500, 'text' => 'Featured'],
];

// Tạo ảnh váy (1-12)
for ($i = 1; $i <= 12; $i++) {
    $images[] = ['name' => "dress-$i.jpg", 'width' => 400, 'height' => 500, 'text' => "Váy $i"];
}

// Tạo ảnh blog (1-9)
for ($i = 1; $i <= 9; $i++) {
    $images[] = ['name' => "blog-$i.jpg", 'width' => 400, 'height' => 250, 'text' => "Blog $i"];
}

// Tạo ảnh khách hàng (1-3)
for ($i = 1; $i <= 3; $i++) {
    $images[] = ['name' => "customer-$i.jpg", 'width' => 100, 'height' => 100, 'text' => "KH $i"];
}

// Tạo ảnh thanh toán
$payments = ['visa', 'mastercard', 'momo', 'vnpay'];
foreach ($payments as $payment) {
    $images[] = ['name' => "payment-$payment.png", 'width' => 60, 'height' => 30, 'text' => strtoupper($payment)];
}

// Màu sắc
$colors = [
    ['bg' => [212, 165, 116], 'text' => [255, 255, 255]], // Gold
    ['bg' => [245, 230, 211], 'text' => [44, 44, 44]],    // Cream
    ['bg' => [200, 155, 109], 'text' => [255, 255, 255]], // Dark Gold
    ['bg' => [250, 248, 245], 'text' => [44, 44, 44]],    // Light
];

$count = 0;
foreach ($images as $index => $img) {
    $filepath = "$imageDir/{$img['name']}";
    
    // Tạo ảnh
    $image = imagecreatetruecolor($img['width'], $img['height']);
    
    // Chọn màu
    $colorSet = $colors[$index % count($colors)];
    $bgColor = imagecolorallocate($image, $colorSet['bg'][0], $colorSet['bg'][1], $colorSet['bg'][2]);
    $textColor = imagecolorallocate($image, $colorSet['text'][0], $colorSet['text'][1], $colorSet['text'][2]);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Thêm text
    $fontSize = min($img['width'], $img['height']) / 10;
    $text = $img['text'];
    $bbox = imagettfbbox($fontSize, 0, __DIR__ . '/arial.ttf', $text);
    
    // Nếu không có font, dùng imagestring
    if (!file_exists(__DIR__ . '/arial.ttf')) {
        $x = ($img['width'] - strlen($text) * 10) / 2;
        $y = ($img['height'] - 20) / 2;
        imagestring($image, 5, $x, $y, $text, $textColor);
    } else {
        $x = ($img['width'] - ($bbox[2] - $bbox[0])) / 2;
        $y = ($img['height'] - ($bbox[1] - $bbox[7])) / 2;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, __DIR__ . '/arial.ttf', $text);
    }
    
    // Lưu file
    if (strpos($img['name'], '.png') !== false) {
        imagepng($image, $filepath);
    } else {
        imagejpeg($image, $filepath, 90);
    }
    
    imagedestroy($image);
    $count++;
    echo "✓ Đã tạo: {$img['name']}<br>";
}

echo "<hr>";
echo "<h2 style='color: green;'>✓ Hoàn thành!</h2>";
echo "<p>Đã tạo <strong>$count</strong> ảnh placeholder.</p>";
echo "<p><a href='test.php' style='background: #d4a574; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Test Kết Nối Database</a></p>";
echo "<p><a href='index.php' style='background: #2c2c2c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;'>Vào Trang Chủ</a></p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}
</style>
