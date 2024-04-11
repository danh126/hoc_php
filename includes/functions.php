<!-- Các hàm xử lý chung -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Xây dựng hàm layout (thêm header và footer thông qua function)

function layout($layoutName = 'header', $data = []) //$data dạng mảng để chuyển tên theo tab
{
    if (file_exists('templates/layout/' . $layoutName . '.php')) {
        require_once('templates/layout/' . $layoutName . '.php');
    }
}

// Hàm gửi mail sử dụng thư viện PHP Mailer
/*
- $to : địa chỉ gmail người nhận
- $subject : Tiêu dể mail
- $content : Nội dung mail 
*/

function phpmailer($to, $subject, $content)
{

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'danhnt126@gmail.com';                     //SMTP username
        $mail->Password   = 'ejkcdraxaonbydlg';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('danhnt126@gmail.com', 'NTD');
        $mail->addAddress($to);     //Gán biến $to

        //Content
        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject; //Gán biến subject
        $mail->Body    = $content; //Gán biến content

        //Bảo mật SMTP 
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Kiểm tra biến $sendMail bên phần register có tồn tại hay không
        $sendMail = $mail->send();
        if ($sendMail) {
            return $sendMail;
        }
        // echo 'Gửi thành công!';
    } catch (Exception $e) {
        echo "Gửi mail thất bại!. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Hàm kiểm tra phương thức get (dùng để lọc dữ liệu)

function isGet()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}

// Hàm kiểm tra phương thức post (dùng để lọc dữ liệu)

function isPost()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

// Hàm fillter lọc dữ liệu FILTER_SANITIZE_SPECIAL_CHARS xóa các ký tự đặc biệt

function filter()
{
    $filterArr = []; //mảng chứa giá trị lọc
    // Lọc dữ liệu phương thức get
    if (isGet()) {
        // Lọc dữ liệu trước khi hiển thị ra
        if (!empty($_GET)) {
            // Dùng vòng lập foreach để lọc dữ liệu
            foreach ($_GET as $key => $value) {
                //Lọc dữ liệu mảng
                $key = strip_tags($key); //strip_tags loại bỏ các thẻ html 
                if (is_array($value)) {
                    //Nếu $value (giá trị) ở dạng mảng thì dùng hằng FILTER_REQUIRE_ARRAY để lọc mảng
                    $filterArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    $filterArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }

    // Lọc dữ liệu phương thức post
    if (isPost()) {
        // Lọc dữ liệu trước khi hiển thị ra
        if (!empty($_POST)) {
            // Dùng vòng lập foreach để lọc dữ liệu
            foreach ($_POST as $key => $value) {
                //lọc dữ liệu mảng
                $key = strip_tags($key);
                if (is_array($value)) {
                    $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    // Trả vể giá trị mảng sau khi lọc
    return $filterArr;
}


// Hàm kiểm tra email (validate)

function isEmail($email)
{
    $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    return $checkEmail;
}

// Hàm kiểm tra số nguyên (INT)
function isNumberInt($number)
{
    $checkNumberInt = filter_var($number, FILTER_VALIDATE_INT);
    return $checkNumberInt;
}

// Hàm kiểm tra số thực (FLOAT)

function isNumberFloat($number)
{
    $checkNumberFloat = filter_var($number, FILTER_VALIDATE_FLOAT);
    return $checkNumberFloat;
}

// Hàm kiểm tra định dạng số điện thoại

function isPhone($phone)
{
    //Số dt phải bắt đầu bằng số 0 và sau nó là 9 chữ số còn lại
    $checkZero = false;

    //Điều kiện 1: Ký tự đầu tiên là số 0
    if ($phone[0] == '0') {
        $checkZero = true;
        $phone = substr($phone, 1); //Xóa số 0 đầu tiên để kiểm tra 9 số còn lại
    }

    //Điều kiện 2 : Sau nó có 9 số và là số nguyên
    $checkNumber = false;
    if (isNumberInt($phone) && (strlen($phone) == 9)) {
        $checkNumber = true;
    }
    if ($checkZero && $checkNumber) {
        return true;
    }
    return false;
}

// Hàm css thông báo lỗi
// $type là có lỗi hoặc không có lỗi 
// Nếu có lỗi css màu danger ngược lại css màu success(màu xanh)

function getSmg($smg, $type = 'success')
{
    echo '<div class= "alert alert-' . $type . '">';
    echo $smg;
    echo '</div>';
}

//Hàm chuyển hướng

function redirect($path = 'index.php') //$path đường dẫn muốn chuyển hướng đến
{
    header("location: $path");
    exit;
}

//Hàm thông báo lỗi trong form
//Biến $errors phải khai báo trước các thẻ html 

function form_error($fileName, $errors, $beforeHtml = '', $afterHtml = '')
{
    //Hàm reset lấy giá trị đầu tiên trong key 
    return (!empty($errors[$fileName])) ?   $beforeHtml . reset($errors[$fileName]) . $afterHtml : null;
}

//Hàm hiển thị dữ liệu cũ trong form 
function old($filename, $oldData, $default = null)
{
    return (!empty($oldData[$filename])) ? $oldData[$filename] : $default;
}

//Hàm kiểm tra trạng thái đăng nhập

function isLogin()
{
    $checkLogin = false;
    if (getSession('logintoken')) {
        $tokenLogin = getSession('logintoken'); //

        //Kiểm tra token có nằm trong bảng logintokrn hay không
        $queryToken = oneRaw("SELECT user_id FROM logintoken WHERE token = '$tokenLogin' ");
        if (!empty($queryToken)) {
            $checkLogin = true;
        } else {
            removeSession('logintoken');
        }
    }
    return $checkLogin;
}
