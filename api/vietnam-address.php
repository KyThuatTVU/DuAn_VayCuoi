<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// require_once '../includes/config.php'; // Not needed for static data

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'provinces':
        getProvinces();
        break;
    case 'districts':
        $provinceCode = $_GET['province_code'] ?? '';
        getDistricts($provinceCode);
        break;
    case 'wards':
        $districtCode = $_GET['district_code'] ?? '';
        getWards($districtCode);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function getProvinces() {
    // Fetch từ API công khai
    $url = 'https://provinces.open-api.vn/api/p/';
    $response = file_get_contents($url);
    if ($response === false) {
        echo json_encode(['success' => false, 'message' => 'Không thể kết nối đến API']);
        return;
    }
    $data = json_decode($response, true);
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu API không hợp lệ']);
        return;
    }
    
    $provinces = [];
    foreach ($data as $province) {
        $provinces[] = [
            'code' => str_pad($province['code'], 2, '0', STR_PAD_LEFT), // Đảm bảo code là string 2 chữ số
            'name' => $province['name']
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $provinces]);
}

function getDistricts($provinceCode) {
    // Fetch từ API công khai
    $provinceId = intval($provinceCode); // API dùng số
    $url = "https://provinces.open-api.vn/api/p/{$provinceId}?depth=2";
    $response = file_get_contents($url);
    if ($response === false) {
        echo json_encode(['success' => false, 'message' => 'Không thể kết nối đến API']);
        return;
    }
    $data = json_decode($response, true);
    if ($data === null || !isset($data['districts'])) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu API không hợp lệ']);
        return;
    }
    
    $districts = [];
    foreach ($data['districts'] as $district) {
        $districts[] = [
            'code' => strval($district['code']), // Giữ nguyên code từ API
            'name' => $district['name'],
            'province_code' => $provinceCode
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $districts]);
}

function getWards($districtCode) {
    // Fetch từ API công khai
    $districtId = intval($districtCode); // API dùng số
    $url = "https://provinces.open-api.vn/api/d/{$districtId}?depth=2";
    $response = file_get_contents($url);
    if ($response === false) {
        echo json_encode(['success' => false, 'message' => 'Không thể kết nối đến API']);
        return;
    }
    $data = json_decode($response, true);
    if ($data === null || !isset($data['wards'])) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu API không hợp lệ']);
        return;
    }
    
    $wards = [];
    foreach ($data['wards'] as $ward) {
        $wards[] = [
            'code' => strval($ward['code']), // Giữ nguyên code từ API
            'name' => $ward['name'],
            'district_code' => $districtCode
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $wards]);
}
?>