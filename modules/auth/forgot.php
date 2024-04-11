<!-- Quên mật khẩu -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}
//Kiểm tra trạng thái đăng nhập
if (isLogin()) {
    redirect('?module=home&action=quanlychung');
}
// Chuyển tên tab theo tưng file mảng $data -> function layout -> toán tử 3 ngôi header
$data = [
    'pageTile' => 'Quên mật khẩu'
];
layout('header_login', $data);

if (isPost()) {
    $filterAll = filter();
    //Kiểm tra email có tồn tại hay không
    if (!empty($filterAll['email'])) {
        $email = $filterAll['email'];
        //Truy vấn database
        $queryUser = oneRaw("SELECT id FROM users WHERE email = '$email'");
        if (!empty($queryUser)) {
            //Gán biến $userIt chứa id truy vấn
            $userId = $queryUser['id'];
            //tạo forgottoken
            $forgotToken = sha1(uniqid() . time());
            //Set thời gian theo giờ Việt Nam
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            //Update vào bảng users
            $dataUpdate = [
                'forgotToken' => $forgotToken
            ];
            // Gọi hàm update
            $updateToken = update('users', $dataUpdate, "id=$userId");
            if ($updateToken) {
                //Tạo link reset khôi phục mật khẩu
                $linkReset = _WEB_HOST . '?module=auth&action=reset&token=' . $forgotToken;
                //Gửi mail cho người dùng
                $subject = 'Yêu cầu khôi phục mật khẩu.';
                $content = 'Chào bạn!' . '<br>';
                $content .= 'Chúng tôi nhận được yêu càu khôi phục mật khẩu từ bạn.
                Vui lòng click vào link sau để đặt lại mật khẩu:' . '<br>';
                $content .= $linkReset . '<br>';

                $sendMaill = phpmailer($email, $subject, $content);

                if ($sendMaill) {
                    setFlashData('smg', 'Vui lòng kiểm tra email để khôi phục mật khẩu!');
                    setFlashData('smg_type', 'success');
                } else {
                    setFlashData('smg', 'Hệ thống gửi Mail đang gặp sự cố, vui lòng thử lại sau!');
                    setFlashData('smg_type', 'danger');
                }
            } else {
                setFlashData('smg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau!');
                setFlashData('smg_type', 'danger');
            }
        } else {
            setFlashData('smg', 'Địa chỉ email không tồn tại trong hệ thống.');
            setFlashData('smg_type', 'danger');
        }
    } else {
        setFlashData('smg', 'Vui lòng nhập địa chỉ email.');
        setFlashData('smg_type', 'danger');
    }
    redirect('?module=auth&action=forgot');
}

$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');

?>
<div class="row">
    <div class="col-4" style="margin: 100px auto;">
        <h2 class="text-center text-uppercase">Quên mật khẩu</h2>
        <?php
        //Kiểm tra biến $smg có tồn tại hay không
        if (!empty($smg)) {
            getSmg($smg, $smg_type);
        }
        ?>
        <form action="" method="post">
            <label for="">Email</label>
            <div class="form-group mg-form">
                <input name="email" type="Email" class="form-control" placeholder="Nhập vảo Email">
            </div>
            <button type="submit" class="mg-btn btn btn-primary btn-block">Gửi</button>
            <hr>
            <p class="text-center"><a href="?module=auth&action=login">Đăng nhập</a></p>
            <p class="text-center"><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
        </form>
    </div>
</div>
<?php
layout('footer_login');
