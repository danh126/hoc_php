<!-- Kích hoạt tài khoản -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

// Chuyển tên tab theo tưng file mảng $data -> function layout -> toán tử 3 ngôi header
$data = [
    'pageTile' => 'Kích hoạt tài khoản'
];

layout('header_login', $data);

//Không cần xây dựng giao diện

//Kiểm tra token trên thanh URL mà người dùng click vào link active 
$token = filter()['token'];
//Nếu biến $token có giá trị
if (!empty($token)) {
    // Truy vấn để kiểm tra token với database
    $tokenQuery = oneRaw("SELECT id FROM users WHERE activeToken = '$token' ");
    if (!empty($tokenQuery)) {
        //Xử lý active
        $userId = $tokenQuery['id']; //gán id bằng id đã truy vấn để update 
        $dataUpdate = [
            'status' => 1,
            'activeToken' => null
        ];
        // Gọi lệnh update
        $updatStatus = update('users', $dataUpdate, "id = $userId ");
        if ($dataUpdate) {
            setFlashData('smg', 'Kích hoạt thành công, bạn có thể đăng nhập vào hệ thống!');
            setFlashData('smg_type', 'success');
        } else {
            setFlashData('smg', 'Kích hoạt không thành công, vui lòng liên hệ quản trị viên!');
            setFlashData('smg_type', 'danger');
        }
        redirect('?module=auth&action=login');
    } else {
        getSmg('Liên kết không tồn tại hoặc đã hết hạn!', 'danger');
    }
} else {
    getSmg('Liên kết không tồn tại hoặc đã hết hạn!', 'danger');
}

?>


<?php
layout('footer_login');
?>