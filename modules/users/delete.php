<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

//Kiểm tra id trong database -> tồn tại -> tiến hành xóa
//Xóa dữ liệu bảng longtoken -> xóa dữ liệu bảng users 

$filterAll = filter(); // lọc dữ liệu
if (!empty($filterAll['id'])) {
    $userId = $filterAll['id'];
    $userDetail = getRaw("SELECT * FROM users WHERE id='$userId'");
    if ($userDetail > 0) {
        //Nếu người dùng có dữ liệu trong database tiến hành xóa logintoken
        $deleteToken = delete('logintoken', "user_id='$userId'");
        if ($deleteToken) {
            //Nếu xóa xong token tiến hành xóa user
            $deleteUser = delete('users', "id='$userId'");
            if ($deleteUser) {
                setFlashData('smg', 'Xóa người dùng thành công!');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'Hệ thống đang gặp lỗi.');
                setFlashData('smg_type', 'danger');
            }
        }
    } else {
        setFlashData('smg', 'Người dùng không tồn tại trong hệ thống.');
        setFlashData('smg_type', 'danger');
    }
} else {
    setFlashData('smg', 'Liên kết không tồn tại.');
    setFlashData('smg_type', 'danger');
}

redirect('?module=users&action=list');
