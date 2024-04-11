<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

/* Xây dựng các hàm session cho project */

// Hàm gán session

function setSession($key, $value)
{
    return $_SESSION[$key] = $value;
}

// Hàm đọc session

function getSession($key = '')
{
    //Nếu $key rỗng trả về giá trị session trống
    if (empty($key)) {
        return $_SESSION;
    } else {
        //Nếu $key có giá trị, trả về session giá trị dựa vào $key
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
}

// Hàm xóa session

function removeSession($key = '')
{
    if (empty($key)) {
        session_destroy(); // xóa tấ cả session
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            //Nếu $_SESSION có $key thì dùng hàm unset xóa $key session
            unset($_SESSION[$key]);
            return true;
        }
    }
}

// Hàm gán flash Data (khi đọc dữ liệu xong session tự động xóa)

function setFlashData($key, $value)
{
    //Gán biến $key thêm 'flash_' để phân biệt biến $key ở session bình thường
    $key = 'flash_' . $key;
    // Trả về kết quả setSession
    return setSession($key, $value);
}

// Hàm đọc xong rồi xóa dữ liệu flash data
function getFlashData($key)
{
    $key = 'flash_' . $key;
    //Gán biến $data để lưu dữ liệu 
    $data = getSession($key);
    //Gọi function xóa session
    removeSession($key);
    // Trả vể kết quả đã lưu ở biến $data (lúc này session đã bị xóa)
    return $data;
}
