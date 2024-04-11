<!-- Các hằng số của project -->
<?php

// tạo hằng số dùng const

const _MODULE = 'home';
const _ACTION = 'quanlychung';


const _CODE = true; //Kiểm soát truy cập của người dùng có hợp lệ không

//Thiết lập host

define('_WEB_HOST', 'http://' . $_SERVER['HTTP_HOST'] . '/hoc_php/quan_ly_nguoi_dung');
define('_WEB_HOST_TEMPLATES', _WEB_HOST . '/templates');

//Thiết lập path
// define('_WEB_PATH', __DIR__);
// define('_WEB_PATH_TEMPLATES', _WEB_PATH . '/templates');

//Thông tin kết nối với database

const _HOST = 'localhost';
const _DB = 'thuc_hanh_php';
const _USER = 'root';
const _PASS = '';
