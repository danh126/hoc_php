<?php
//Muốn khởi tạo project đầu tiên phải chạy session 
session_start();

//Kết nối đến file chứa các hằng số (config)
require_once("config.php");
require_once('../quan_ly_nguoi_dung/includes/connect.php');

//Thư viện phpmailer
require_once('../quan_ly_nguoi_dung/includes/PHPMailer/Exception.php');
require_once('../quan_ly_nguoi_dung/includes/PHPMailer/PHPMailer.php');
require_once('../quan_ly_nguoi_dung/includes/PHPMailer/SMTP.php');

require_once('../quan_ly_nguoi_dung/includes/functions.php');
require_once('../quan_ly_nguoi_dung/includes/database.php');
require_once('../quan_ly_nguoi_dung/includes/session.php');


// phpmailer('danhcg126@gmail.com', 'Test mail', 'Xin chào');
//Gán hằng số 

$module = _MODULE;
$action = _ACTION;

//Điều hướng module 

//Kiểm tra giá trị module có tồn tại hay không
if (!empty($_GET['module'])) {
    // Kiểm tra xem module có phải dạng chuỗi hay không
    if (is_string($_GET['module'])) {
        $module = trim($_GET['module']);
    }
}

//Kiểm tra giá trị module có tồn tại hay không
if (!empty($_GET['action'])) {
    // Kiểm tra xem module có phải dạng chuỗi hay không
    if (is_string($_GET['action'])) {
        $action = trim($_GET['action']);
    }
}


// Ghép nối $module và $action lại => $path

//Đường dẫn đến file muốn chạy

$path = 'modules/' . $module . '/' . $action . '.php';

//Dùng hàm file_exists kiểm tra file $path có tồn tại hay không
if (file_exists($path)) {
    require_once($path);
} else {
    require_once 'modules/error/404.php';
}
