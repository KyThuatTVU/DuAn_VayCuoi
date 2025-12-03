<?php
/**
 * API Địa chỉ Việt Nam - 63 Tỉnh/Thành phố
 * Endpoint: api/vietnam-address.php
 * 
 * Params:
 * - action: provinces | districts | wards
 * - province_code: Mã tỉnh (khi lấy danh sách huyện)
 * - district_code: Mã huyện (khi lấy danh sách xã)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? 'provinces';
$province_code = $_GET['province_code'] ?? '';
$district_code = $_GET['district_code'] ?? '';

// Dữ liệu 63 tỉnh thành Việt Nam
$provinces = [
    ['code' => '01', 'name' => 'Hà Nội'],
    ['code' => '02', 'name' => 'Hà Giang'],
    ['code' => '04', 'name' => 'Cao Bằng'],
    ['code' => '06', 'name' => 'Bắc Kạn'],
    ['code' => '08', 'name' => 'Tuyên Quang'],
    ['code' => '10', 'name' => 'Lào Cai'],
    ['code' => '11', 'name' => 'Điện Biên'],
    ['code' => '12', 'name' => 'Lai Châu'],
    ['code' => '14', 'name' => 'Sơn La'],
    ['code' => '15', 'name' => 'Yên Bái'],
    ['code' => '17', 'name' => 'Hòa Bình'],
    ['code' => '19', 'name' => 'Thái Nguyên'],
    ['code' => '20', 'name' => 'Lạng Sơn'],
    ['code' => '22', 'name' => 'Quảng Ninh'],
    ['code' => '24', 'name' => 'Bắc Giang'],
    ['code' => '25', 'name' => 'Phú Thọ'],
    ['code' => '26', 'name' => 'Vĩnh Phúc'],
    ['code' => '27', 'name' => 'Bắc Ninh'],
    ['code' => '30', 'name' => 'Hải Dương'],
    ['code' => '31', 'name' => 'Hải Phòng'],
    ['code' => '33', 'name' => 'Hưng Yên'],
    ['code' => '34', 'name' => 'Thái Bình'],
    ['code' => '35', 'name' => 'Hà Nam'],
    ['code' => '36', 'name' => 'Nam Định'],
    ['code' => '37', 'name' => 'Ninh Bình'],
    ['code' => '38', 'name' => 'Thanh Hóa'],
    ['code' => '40', 'name' => 'Nghệ An'],
    ['code' => '42', 'name' => 'Hà Tĩnh'],
    ['code' => '44', 'name' => 'Quảng Bình'],
    ['code' => '45', 'name' => 'Quảng Trị'],
    ['code' => '46', 'name' => 'Thừa Thiên Huế'],
    ['code' => '48', 'name' => 'Đà Nẵng'],
    ['code' => '49', 'name' => 'Quảng Nam'],
    ['code' => '51', 'name' => 'Quảng Ngãi'],
    ['code' => '52', 'name' => 'Bình Định'],
    ['code' => '54', 'name' => 'Phú Yên'],
    ['code' => '56', 'name' => 'Khánh Hòa'],
    ['code' => '58', 'name' => 'Ninh Thuận'],
    ['code' => '60', 'name' => 'Bình Thuận'],
    ['code' => '62', 'name' => 'Kon Tum'],
    ['code' => '64', 'name' => 'Gia Lai'],
    ['code' => '66', 'name' => 'Đắk Lắk'],
    ['code' => '67', 'name' => 'Đắk Nông'],
    ['code' => '68', 'name' => 'Lâm Đồng'],
    ['code' => '70', 'name' => 'Bình Phước'],
    ['code' => '72', 'name' => 'Tây Ninh'],
    ['code' => '74', 'name' => 'Bình Dương'],
    ['code' => '75', 'name' => 'Đồng Nai'],
    ['code' => '77', 'name' => 'Bà Rịa - Vũng Tàu'],
    ['code' => '79', 'name' => 'TP. Hồ Chí Minh'],
    ['code' => '80', 'name' => 'Long An'],
    ['code' => '82', 'name' => 'Tiền Giang'],
    ['code' => '83', 'name' => 'Bến Tre'],
    ['code' => '84', 'name' => 'Trà Vinh'],
    ['code' => '86', 'name' => 'Vĩnh Long'],
    ['code' => '87', 'name' => 'Đồng Tháp'],
    ['code' => '89', 'name' => 'An Giang'],
    ['code' => '91', 'name' => 'Kiên Giang'],
    ['code' => '92', 'name' => 'Cần Thơ'],
    ['code' => '93', 'name' => 'Hậu Giang'],
    ['code' => '94', 'name' => 'Sóc Trăng'],
    ['code' => '95', 'name' => 'Bạc Liêu'],
    ['code' => '96', 'name' => 'Cà Mau']
];


// Dữ liệu quận/huyện theo tỉnh (mẫu cho một số tỉnh phổ biến)
$districts = [
    // Hà Nội
    '01' => [
        ['code' => '001', 'name' => 'Quận Ba Đình'],
        ['code' => '002', 'name' => 'Quận Hoàn Kiếm'],
        ['code' => '003', 'name' => 'Quận Tây Hồ'],
        ['code' => '004', 'name' => 'Quận Long Biên'],
        ['code' => '005', 'name' => 'Quận Cầu Giấy'],
        ['code' => '006', 'name' => 'Quận Đống Đa'],
        ['code' => '007', 'name' => 'Quận Hai Bà Trưng'],
        ['code' => '008', 'name' => 'Quận Hoàng Mai'],
        ['code' => '009', 'name' => 'Quận Thanh Xuân'],
        ['code' => '016', 'name' => 'Quận Hà Đông'],
        ['code' => '017', 'name' => 'Quận Nam Từ Liêm'],
        ['code' => '018', 'name' => 'Quận Bắc Từ Liêm'],
        ['code' => '019', 'name' => 'Huyện Sóc Sơn'],
        ['code' => '020', 'name' => 'Huyện Đông Anh'],
        ['code' => '021', 'name' => 'Huyện Gia Lâm'],
        ['code' => '250', 'name' => 'Huyện Thanh Trì'],
        ['code' => '268', 'name' => 'Huyện Hoài Đức'],
        ['code' => '269', 'name' => 'Huyện Quốc Oai'],
        ['code' => '271', 'name' => 'Huyện Thạch Thất'],
        ['code' => '272', 'name' => 'Huyện Chương Mỹ'],
        ['code' => '273', 'name' => 'Huyện Thanh Oai'],
        ['code' => '274', 'name' => 'Huyện Thường Tín'],
        ['code' => '275', 'name' => 'Huyện Phú Xuyên'],
        ['code' => '276', 'name' => 'Huyện Ứng Hòa'],
        ['code' => '277', 'name' => 'Huyện Mỹ Đức'],
        ['code' => '278', 'name' => 'Huyện Đan Phượng'],
        ['code' => '279', 'name' => 'Huyện Ba Vì'],
        ['code' => '280', 'name' => 'Huyện Phúc Thọ'],
        ['code' => '281', 'name' => 'Thị xã Sơn Tây'],
        ['code' => '282', 'name' => 'Huyện Mê Linh']
    ],
    // TP. Hồ Chí Minh
    '79' => [
        ['code' => '760', 'name' => 'Quận 1'],
        ['code' => '761', 'name' => 'Quận 12'],
        ['code' => '764', 'name' => 'Quận Gò Vấp'],
        ['code' => '765', 'name' => 'Quận Bình Thạnh'],
        ['code' => '766', 'name' => 'Quận Tân Bình'],
        ['code' => '767', 'name' => 'Quận Tân Phú'],
        ['code' => '768', 'name' => 'Quận Phú Nhuận'],
        ['code' => '769', 'name' => 'TP. Thủ Đức'],
        ['code' => '770', 'name' => 'Quận 3'],
        ['code' => '771', 'name' => 'Quận 10'],
        ['code' => '772', 'name' => 'Quận 11'],
        ['code' => '773', 'name' => 'Quận 4'],
        ['code' => '774', 'name' => 'Quận 5'],
        ['code' => '775', 'name' => 'Quận 6'],
        ['code' => '776', 'name' => 'Quận 8'],
        ['code' => '777', 'name' => 'Quận Bình Tân'],
        ['code' => '778', 'name' => 'Quận 7'],
        ['code' => '783', 'name' => 'Huyện Củ Chi'],
        ['code' => '784', 'name' => 'Huyện Hóc Môn'],
        ['code' => '785', 'name' => 'Huyện Bình Chánh'],
        ['code' => '786', 'name' => 'Huyện Nhà Bè'],
        ['code' => '787', 'name' => 'Huyện Cần Giờ']
    ],
    // Đà Nẵng
    '48' => [
        ['code' => '490', 'name' => 'Quận Liên Chiểu'],
        ['code' => '491', 'name' => 'Quận Thanh Khê'],
        ['code' => '492', 'name' => 'Quận Hải Châu'],
        ['code' => '493', 'name' => 'Quận Sơn Trà'],
        ['code' => '494', 'name' => 'Quận Ngũ Hành Sơn'],
        ['code' => '495', 'name' => 'Quận Cẩm Lệ'],
        ['code' => '497', 'name' => 'Huyện Hòa Vang'],
        ['code' => '498', 'name' => 'Huyện Hoàng Sa']
    ],
    // Cần Thơ
    '92' => [
        ['code' => '916', 'name' => 'Quận Ninh Kiều'],
        ['code' => '917', 'name' => 'Quận Ô Môn'],
        ['code' => '918', 'name' => 'Quận Bình Thủy'],
        ['code' => '919', 'name' => 'Quận Cái Răng'],
        ['code' => '923', 'name' => 'Quận Thốt Nốt'],
        ['code' => '924', 'name' => 'Huyện Vĩnh Thạnh'],
        ['code' => '925', 'name' => 'Huyện Cờ Đỏ'],
        ['code' => '926', 'name' => 'Huyện Phong Điền'],
        ['code' => '927', 'name' => 'Huyện Thới Lai']
    ],
    // Trà Vinh
    '84' => [
        ['code' => '842', 'name' => 'Thành phố Trà Vinh'],
        ['code' => '844', 'name' => 'Huyện Càng Long'],
        ['code' => '845', 'name' => 'Huyện Cầu Kè'],
        ['code' => '846', 'name' => 'Huyện Tiểu Cần'],
        ['code' => '847', 'name' => 'Huyện Châu Thành'],
        ['code' => '848', 'name' => 'Huyện Cầu Ngang'],
        ['code' => '849', 'name' => 'Huyện Trà Cú'],
        ['code' => '850', 'name' => 'Huyện Duyên Hải'],
        ['code' => '851', 'name' => 'Thị xã Duyên Hải']
    ],
    // Hải Phòng
    '31' => [
        ['code' => '303', 'name' => 'Quận Hồng Bàng'],
        ['code' => '304', 'name' => 'Quận Ngô Quyền'],
        ['code' => '305', 'name' => 'Quận Lê Chân'],
        ['code' => '306', 'name' => 'Quận Hải An'],
        ['code' => '307', 'name' => 'Quận Kiến An'],
        ['code' => '308', 'name' => 'Quận Đồ Sơn'],
        ['code' => '309', 'name' => 'Quận Dương Kinh'],
        ['code' => '311', 'name' => 'Huyện Thủy Nguyên'],
        ['code' => '312', 'name' => 'Huyện An Dương'],
        ['code' => '313', 'name' => 'Huyện An Lão'],
        ['code' => '314', 'name' => 'Huyện Kiến Thụy'],
        ['code' => '315', 'name' => 'Huyện Tiên Lãng'],
        ['code' => '316', 'name' => 'Huyện Vĩnh Bảo'],
        ['code' => '317', 'name' => 'Huyện Cát Hải'],
        ['code' => '318', 'name' => 'Huyện Bạch Long Vĩ']
    ],
    // Bình Dương
    '74' => [
        ['code' => '718', 'name' => 'Thành phố Thủ Dầu Một'],
        ['code' => '719', 'name' => 'Huyện Bàu Bàng'],
        ['code' => '720', 'name' => 'Huyện Dầu Tiếng'],
        ['code' => '721', 'name' => 'Thị xã Bến Cát'],
        ['code' => '722', 'name' => 'Huyện Phú Giáo'],
        ['code' => '723', 'name' => 'Thị xã Tân Uyên'],
        ['code' => '724', 'name' => 'Thành phố Dĩ An'],
        ['code' => '725', 'name' => 'Thành phố Thuận An'],
        ['code' => '726', 'name' => 'Huyện Bắc Tân Uyên']
    ],
    // Đồng Nai
    '75' => [
        ['code' => '731', 'name' => 'Thành phố Biên Hòa'],
        ['code' => '732', 'name' => 'Thành phố Long Khánh'],
        ['code' => '734', 'name' => 'Huyện Tân Phú'],
        ['code' => '735', 'name' => 'Huyện Vĩnh Cửu'],
        ['code' => '736', 'name' => 'Huyện Định Quán'],
        ['code' => '737', 'name' => 'Huyện Trảng Bom'],
        ['code' => '738', 'name' => 'Huyện Thống Nhất'],
        ['code' => '739', 'name' => 'Huyện Cẩm Mỹ'],
        ['code' => '740', 'name' => 'Huyện Long Thành'],
        ['code' => '741', 'name' => 'Huyện Xuân Lộc'],
        ['code' => '742', 'name' => 'Huyện Nhơn Trạch']
    ]
];


// Dữ liệu phường/xã theo huyện (mẫu cho một số huyện phổ biến)
$wards = [
    // Quận 1 - HCM
    '760' => [
        ['code' => '26734', 'name' => 'Phường Tân Định'],
        ['code' => '26737', 'name' => 'Phường Đa Kao'],
        ['code' => '26740', 'name' => 'Phường Bến Nghé'],
        ['code' => '26743', 'name' => 'Phường Bến Thành'],
        ['code' => '26746', 'name' => 'Phường Nguyễn Thái Bình'],
        ['code' => '26749', 'name' => 'Phường Phạm Ngũ Lão'],
        ['code' => '26752', 'name' => 'Phường Cầu Ông Lãnh'],
        ['code' => '26755', 'name' => 'Phường Cô Giang'],
        ['code' => '26758', 'name' => 'Phường Nguyễn Cư Trinh'],
        ['code' => '26761', 'name' => 'Phường Cầu Kho']
    ],
    // Quận Ba Đình - Hà Nội
    '001' => [
        ['code' => '00001', 'name' => 'Phường Phúc Xá'],
        ['code' => '00004', 'name' => 'Phường Trúc Bạch'],
        ['code' => '00006', 'name' => 'Phường Vĩnh Phúc'],
        ['code' => '00007', 'name' => 'Phường Cống Vị'],
        ['code' => '00008', 'name' => 'Phường Liễu Giai'],
        ['code' => '00010', 'name' => 'Phường Nguyễn Trung Trực'],
        ['code' => '00013', 'name' => 'Phường Quán Thánh'],
        ['code' => '00016', 'name' => 'Phường Ngọc Hà'],
        ['code' => '00019', 'name' => 'Phường Điện Biên'],
        ['code' => '00022', 'name' => 'Phường Đội Cấn'],
        ['code' => '00025', 'name' => 'Phường Ngọc Khánh'],
        ['code' => '00028', 'name' => 'Phường Kim Mã'],
        ['code' => '00031', 'name' => 'Phường Giảng Võ'],
        ['code' => '00034', 'name' => 'Phường Thành Công']
    ],
    // TP Trà Vinh
    '842' => [
        ['code' => '29542', 'name' => 'Phường 4'],
        ['code' => '29545', 'name' => 'Phường 1'],
        ['code' => '29548', 'name' => 'Phường 3'],
        ['code' => '29551', 'name' => 'Phường 2'],
        ['code' => '29554', 'name' => 'Phường 5'],
        ['code' => '29557', 'name' => 'Phường 6'],
        ['code' => '29560', 'name' => 'Phường 7'],
        ['code' => '29563', 'name' => 'Phường 8'],
        ['code' => '29566', 'name' => 'Phường 9'],
        ['code' => '29569', 'name' => 'Xã Long Đức']
    ],
    // Huyện Càng Long - Trà Vinh
    '844' => [
        ['code' => '29572', 'name' => 'Thị trấn Càng Long'],
        ['code' => '29575', 'name' => 'Xã Mỹ Cẩm'],
        ['code' => '29578', 'name' => 'Xã An Trường A'],
        ['code' => '29581', 'name' => 'Xã An Trường'],
        ['code' => '29584', 'name' => 'Xã Huyền Hội'],
        ['code' => '29587', 'name' => 'Xã Tân An'],
        ['code' => '29590', 'name' => 'Xã Tân Bình'],
        ['code' => '29593', 'name' => 'Xã Bình Phú'],
        ['code' => '29596', 'name' => 'Xã Phương Thạnh'],
        ['code' => '29599', 'name' => 'Xã Đại Phúc'],
        ['code' => '29602', 'name' => 'Xã Đại Phước'],
        ['code' => '29605', 'name' => 'Xã Nhị Long Phú'],
        ['code' => '29608', 'name' => 'Xã Nhị Long'],
        ['code' => '29611', 'name' => 'Xã Đức Mỹ']
    ],
    // Huyện Cầu Kè - Trà Vinh
    '845' => [
        ['code' => '29614', 'name' => 'Thị trấn Cầu Kè'],
        ['code' => '29617', 'name' => 'Xã Hòa Ân'],
        ['code' => '29620', 'name' => 'Xã Châu Điền'],
        ['code' => '29623', 'name' => 'Xã An Phú Tân'],
        ['code' => '29626', 'name' => 'Xã Hoà Tân'],
        ['code' => '29629', 'name' => 'Xã Ninh Thới'],
        ['code' => '29632', 'name' => 'Xã Phong Phú'],
        ['code' => '29635', 'name' => 'Xã Phong Thạnh'],
        ['code' => '29638', 'name' => 'Xã Tam Ngãi'],
        ['code' => '29641', 'name' => 'Xã Thông Hòa'],
        ['code' => '29644', 'name' => 'Xã Thạnh Phú']
    ],
    // Quận Hải Châu - Đà Nẵng
    '492' => [
        ['code' => '20194', 'name' => 'Phường Thanh Bình'],
        ['code' => '20195', 'name' => 'Phường Thuận Phước'],
        ['code' => '20197', 'name' => 'Phường Thạch Thang'],
        ['code' => '20198', 'name' => 'Phường Hải Châu I'],
        ['code' => '20200', 'name' => 'Phường Hải Châu II'],
        ['code' => '20203', 'name' => 'Phường Phước Ninh'],
        ['code' => '20206', 'name' => 'Phường Hòa Thuận Tây'],
        ['code' => '20207', 'name' => 'Phường Hòa Thuận Đông'],
        ['code' => '20209', 'name' => 'Phường Nam Dương'],
        ['code' => '20212', 'name' => 'Phường Bình Hiên'],
        ['code' => '20215', 'name' => 'Phường Bình Thuận'],
        ['code' => '20218', 'name' => 'Phường Hòa Cường Bắc'],
        ['code' => '20221', 'name' => 'Phường Hòa Cường Nam']
    ],
    // Quận Ninh Kiều - Cần Thơ
    '916' => [
        ['code' => '31117', 'name' => 'Phường Cái Khế'],
        ['code' => '31120', 'name' => 'Phường An Hòa'],
        ['code' => '31123', 'name' => 'Phường Thới Bình'],
        ['code' => '31126', 'name' => 'Phường An Nghiệp'],
        ['code' => '31129', 'name' => 'Phường An Cư'],
        ['code' => '31132', 'name' => 'Phường An Hội'],
        ['code' => '31135', 'name' => 'Phường Tân An'],
        ['code' => '31138', 'name' => 'Phường An Lạc'],
        ['code' => '31141', 'name' => 'Phường An Phú'],
        ['code' => '31144', 'name' => 'Phường Xuân Khánh'],
        ['code' => '31147', 'name' => 'Phường Hưng Lợi'],
        ['code' => '31149', 'name' => 'Phường An Khánh'],
        ['code' => '31150', 'name' => 'Phường An Bình']
    ]
];

// Xử lý request
switch ($action) {
    case 'provinces':
        echo json_encode([
            'success' => true,
            'data' => $provinces
        ]);
        break;
        
    case 'districts':
        if (empty($province_code)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã tỉnh/thành phố'
            ]);
            break;
        }
        
        $result = $districts[$province_code] ?? [];
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
        break;
        
    case 'wards':
        if (empty($district_code)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng cung cấp mã quận/huyện'
            ]);
            break;
        }
        
        $result = $wards[$district_code] ?? [];
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
        break;
        
    case 'search':
        // Tìm kiếm tỉnh theo tên
        $keyword = mb_strtolower($_GET['keyword'] ?? '', 'UTF-8');
        if (empty($keyword)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng cung cấp từ khóa tìm kiếm'
            ]);
            break;
        }
        
        $results = array_filter($provinces, function($p) use ($keyword) {
            return mb_strpos(mb_strtolower($p['name'], 'UTF-8'), $keyword) !== false;
        });
        
        echo json_encode([
            'success' => true,
            'data' => array_values($results)
        ]);
        break;
        
    case 'get_names':
        // Lấy tên đầy đủ từ mã
        $p_code = $_GET['province_code'] ?? '';
        $d_code = $_GET['district_code'] ?? '';
        $w_code = $_GET['ward_code'] ?? '';
        
        $province_name = '';
        $district_name = '';
        $ward_name = '';
        
        // Tìm tên tỉnh
        foreach ($provinces as $p) {
            if ($p['code'] === $p_code) {
                $province_name = $p['name'];
                break;
            }
        }
        
        // Tìm tên huyện
        if (isset($districts[$p_code])) {
            foreach ($districts[$p_code] as $d) {
                if ($d['code'] === $d_code) {
                    $district_name = $d['name'];
                    break;
                }
            }
        }
        
        // Tìm tên xã
        if (isset($wards[$d_code])) {
            foreach ($wards[$d_code] as $w) {
                if ($w['code'] === $w_code) {
                    $ward_name = $w['name'];
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'province' => $province_name,
                'district' => $district_name,
                'ward' => $ward_name,
                'full_address' => trim("$ward_name, $district_name, $province_name", ', ')
            ]
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Action không hợp lệ'
        ]);
}
