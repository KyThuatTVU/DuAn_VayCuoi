<?php
/**
 * Chuyển đổi số thành chữ tiếng Việt
 * @param int|float $number Số cần chuyển đổi
 * @return string Chuỗi tiếng Việt
 */
function numberToWords($number) {
    $number = (int)$number; // Làm tròn xuống nếu có phần thập phân

    if ($number == 0) {
        return 'không đồng';
    }

    $units = ['', 'nghìn', 'triệu', 'tỷ'];
    $words = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];

    $result = '';
    $unitIndex = 0;

    while ($number > 0) {
        $chunk = $number % 1000;
        if ($chunk > 0) {
            $chunkWords = convertHundreds($chunk, $words);
            $result = $chunkWords . ' ' . $units[$unitIndex] . ' ' . $result;
        }
        $number = (int)($number / 1000);
        $unitIndex++;
    }

    return trim($result) . ' đồng';
}

function convertHundreds($number, $words) {
    $result = '';

    $hundreds = (int)($number / 100);
    $tens = (int)(($number % 100) / 10);
    $units = $number % 10;

    if ($hundreds > 0) {
        $result .= $words[$hundreds] . ' trăm ';
    }

    if ($tens > 0) {
        if ($tens == 1) {
            $result .= 'mười ';
        } else {
            $result .= $words[$tens] . ' mươi ';
        }
    }

    if ($units > 0) {
        if ($tens > 0 && $units == 1) {
            $result .= 'mốt ';
        } elseif ($tens > 0 && $units == 5) {
            $result .= 'lăm ';
        } else {
            $result .= $words[$units] . ' ';
        }
    }

    return trim($result);
}
?>