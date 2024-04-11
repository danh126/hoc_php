<!-- Đăng ký tài khoản -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

// Chuyển tên tab theo tưng file mảng $data -> function layout -> toán tử 3 ngôi header
$data = [
    'pageTile' => 'Đăng ký tài khoản'
];

if (isPost()) {
    $filterAll = filter(); //$filterAll chính là mảng chứa các dữ $_POST
    $errors = []; // Mảng chứa lỗi

    //Validate full name
    if (empty($filterAll['fullname'])) {
        $errors['fullname']['required'] = 'Bắt buộc phải nhập vào họ và tên.';
    } else {
        //Kiểm tra đội dài fullname (fullnam > 6 ký tự)
        if (mb_strlen($filterAll['fullname']) < 6) {
            $errors['fullname']['min_lenght'] = 'Độ dài họ và tên phải từ 6 ký tự trở lên.';
        }
    }
    // Validate email : bắt buộc phải nhập, đúng dịnh dạng, có tồn tại trong csdl chưa
    if (empty($filterAll['email'])) {
        $errors['email']['required'] = 'Bắt buộc phải nhập Email.';
    } else {
        $email = $filterAll['email'];
        $sql = "SELECT id FROM users WHERE email = '$email' ";
        if (getRaws($sql) > 0) {
            $errors['email']['unique'] = 'Email đã tồn tại.';
        }
    }
    // Validate phone : bắt buộc phải nhập , kiểm tra định dạng sdt bằng function
    if (empty($filterAll['phone'])) {
        $errors['phone']['required'] = 'Bắt buộc phải nhập số điện thoại.';
    } else {
        if (!isPhone($filterAll['phone'])) {
            $errors['phone']['isPhone'] = 'Số điện thoại không hợp lệ.';
        }
    }

    //Validate password : bắt buộc phải nhập, lớn hơn bằng 8 ký tự
    if (empty($filterAll['password'])) {
        $errors['password']['required'] = 'Bắt buộc phải nhập mật khẩu.';
    } else {
        if (mb_strlen($filterAll['password']) < 8) {
            $errors['password']['min_lenght'] = 'Độ dài mật khẩu phải bằng hoặc lớn hơn 8 ký tự.';
        }
    }

    // Validate password confirm : bắt buộc phải nhập, password confirm phải giống password
    if (empty($filterAll['password_confirm'])) {
        $errors['password_confirm']['required'] = 'Bắt buộc phải nhập lại mật khẩu.';
    } else {
        if (($filterAll['password']) != ($filterAll['password_confirm'])) {
            $errors['password_confirm']['match'] = 'Mật khẩu bạn nhập lại không đúng.';
        }
    }
    //Nếu không có lỗi xảy ra thì tiến hành insert
    if (empty($errors)) {
        //Gán hàm $activeToken dùng sha1 mã hóa thời gian 
        //uniqid có tác dụng tạo một chuỗi có giá trị duy nhất dựa vào time()
        $activeToken = sha1(uniqid() . time());
        //Set thời gian theo giờ Việt Nam
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $dataInsert = [
            //Các key truyền vào phải đúng với bảng users trên database
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            'password' => password_hash($filterAll['password'], PASSWORD_DEFAULT),
            'activeToken' => $activeToken,
            'creat_at' => date('Y-m-d H:i:s')

        ];
        // echo '<pre>';
        // print_r($dataInsert);
        // echo '</pre>';
        // die();

        $insertStatus = insert('users', $dataInsert);
        if ($insertStatus) {
            //Tạo link active tài khoản
            $linkActive = _WEB_HOST . '?module=auth&action=active&token=' . $activeToken;
            //Thiết lập gửi mail
            $subject = $filterAll['fullname'] . ' Vui lòng kích hoạt tài khoản!!!';
            $content = 'Chào ' . $filterAll['fullname'] . '<br>';
            $content .= 'Vui lòng click vào link dưới đậy để kích hoạt tài khoản: ' . '<br>';
            $content .= $linkActive . '<br>';
            $content .= 'Trân trọng cảm ơn!';

            //Tiến hành gủi mail
            $sendMail = phpmailer($filterAll['email'], $subject, $content);
            //check lỗi khi phần if($sendMaill) không hiện ra thông báo 
            // var_dump($sendMail);
            // die();
            if ($sendMail) {
                setFlashData('smg', 'Đăng ký thành công, vui lòng kiểm tra Email để kích hoạt tài khoản!!!');
                //smg_type dùng để css cho thông báo 
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau!!!');
                //smg_type dùng để css cho thông báo 
                setFlashData('smg_type', 'danger');
            }
        } else {
            setFlashData('smg', 'Đăng ký không thành công!');
            //smg_type dùng để css cho thông báo 
            setFlashData('smg_type', 'danger');
        }
        //Sau khi đăng ký thành công điều hướng đến trang login
        redirect('?module=auth&action=register');
    } else {
        //Dùng hàm setflashData trong session để đọc dữ liệu
        setFlashData('smg', 'Vui lòng kiểm tra lại thông tin đăng ký!');
        //smg_type dùng để css cho thông báo 
        setFlashData('smg_type', 'danger');
        //Gán $errors vào biến lỗi để hiện thị dưới mỗi ô input
        setFlashData('errors', $errors);
        //lưu lại dữ liệu cũ khi nhập vào form
        setFlashData('old', $filterAll);
        //Khi phát hiện lỗi reset lại trang đăng ký
        redirect('?module=auth&action=register');
    }
}

// echo '<pre>';
// print_r($filterAll);
// echo '</pre>';

// Thêm header vào login
layout('header_login', $data);

//Dùng hàm getFalshData đọc xong rồi xóa session
$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');
$errors = getFlashData('errors'); // hiển thị lỗi, chỉ sử dụng được 1 lần 
$old = getFlashData('old'); // Gán biến $old lưu dữ liệu cũ khi nhập vào form
// echo '<pre>';
// print_r($old);
// echo '</pre>';

?>
<div class="row">
    <div class="col-4" style="margin: 100px auto;">
        <h2 class="text-center text-uppercase">Đăng ký tài khoản user</h2>
        <!--Thông báo lỗi đăng ký -->
        <?php
        //Kiểm tra biến $smg có tồn tại hay không
        if (!empty($smg)) {
            getSmg($smg, $smg_type);
        }
        ?>
        <form action="" method="post">
            <label for="">Họ tên</label>
            <div class="form-group mg-form">
                <input name="fullname" type="fullname" class="form-control" placeholder="Nhập vào họ tên" value="<?php echo old('fullname', $old) ?>">
                <?php
                echo form_error('fullname', $errors, ' <span class="error">', '</span> ');
                ?>
            </div>
            <label for="">Email</label>
            <div class="form-group mg-form">
                <input name="email" type="Email" class="form-control" placeholder="Nhập vảo Email" value="<?php echo old('email', $old) ?>">
                <?php
                echo form_error('email', $errors, ' <span class="error">', '</span> ');
                ?>
            </div>
            <label for="">Số điện thoại</label>
            <div class="form-group mg-form">
                <input name="phone" type="phone" class="form-control" placeholder="Nhập vảo số điện thoại" value="<?php echo old('phone', $old) ?>">
                <?php
                echo form_error('phone', $errors, ' <span class="error">', '</span> ');
                ?>
            </div>
            <label for="">Password</label>
            <div class="form-group mg-form">
                <input name="password" type="text" class="form-control" placeholder="Nhập vào mật khẩu">
                <?php
                echo form_error('password', $errors, ' <span class="error">', '</span> ');
                ?>
            </div>
            <label for="">Nhập lại password</label>
            <div class="form-group mg-form">
                <input name="password_confirm" type="password" class="form-control" placeholder="Nhập lại mật khẩu">
                <?php
                echo form_error('password_confirm', $errors, ' <span class="error">', '</span> ');
                ?>
            </div>
            <button type="submit" class="mg-btn btn btn-primary btn-block">Đăng ký</button>
            <hr>
            <p class="text-center"><a href="?module=auth&action=login">Đăng nhập tài khoản</a></p>
        </form>

    </div>

</div>


<?php
//Thêm footer vào login
layout('footer_login');
?>