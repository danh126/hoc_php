<!-- Đăng xuất -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}
$data = [
    'pageTile' => 'Đăng xuất tài khoản'
];

// layout('header_login', $data);

//Kiểm tra xem có đang đăng nhập không
if (isLogin()) {
    // var_dump($_SESSION);
    // die();
    $token = getSession('logintoken');
    // var_dump($token);
    // die();
    delete('logintoken', "token='$token'"); // lưu ý biến $token phải đặt trong dấu nháy đơn ''
    removeSession('logintoken');
    redirect('?module=auth&action=login');
}
?>












<?php
// layout('footer');
