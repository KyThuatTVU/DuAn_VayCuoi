<?php
require_once '../includes/config.php';
require_once '../includes/number-to-words.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['price'])) {
    $price = floatval($_POST['price']);
    $words = numberToWords($price);
    echo json_encode(['words' => $words]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>