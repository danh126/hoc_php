<!-- Kết nối project với database -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}
//Dùng try catch bắt lỗi ngoại lệ

try {
    if (class_exists('PDO')) {
        //$dsn cú pháp mặc định
        $dsn = 'mysql:dbname=' . _DB . ';host=' . _HOST;
        //Mảng cố định 
        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', // Set utf8 hỗ trợ tiếng Việt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // tạo thông báo ngoại lệ khi gặp lỗi
        ];
        //Khai báo biến $connect gán nó bằng class PDO
        $conn = new PDO($dsn, _USER, _PASS, $options);
        //Kiểm tra kết nối 
        // var_dump($connect);
        // if ($connect) {
        //     echo 'Kết nối thành công';
        // }
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . '<br>';
    die(); //Thoát chương trình khi gặp lỗi
}
