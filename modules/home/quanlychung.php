<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

$data = [
    'pageTile' => 'Quản lý chung'
];

// Thêm header vào login
layout('header', $data);

//Kiểm tra trạng thái đăng nhập 
if (!isLogin()) {
    redirect('?module=auth&action=login');
}
// echo getSession('LoginToken');
?>









<?php
//Thêm footer vào login
layout('footer');
?>
