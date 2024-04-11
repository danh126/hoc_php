<!-- Quên mật khẩu -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

// Chuyển tên tab theo tưng file mảng $data -> function layout -> toán tử 3 ngôi header
$data = [
    'pageTile' => 'Khôi phục tài khoản'
];

layout('header_login', $data);
//Kiểm tra token trên thanh URL mà người dùng click vào link active 
$token = filter()['token'];
// echo '<pre>';
// print_r($token);
// echo '</pre>';
// die();
if (!empty($token)) {
    // Truy vấn để kiểm tra token với database
    $tokenQuery = oneRaw("SELECT id, fullname, email FROM users WHERE forgotToken = '$token' ");
    if (!empty($tokenQuery)) {
        if (isPost()) {
            $filterAll = filter();
            $errors = []; //mảng chứa lỗi
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
            if (empty($errors)) {
                $userId = $tokenQuery['id']; //gán id bằng id đã truy vấn để update 
                //Set thời gian theo giờ Việt Nam
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                //Xử lý việc update mật khẩu
                $passwordHash = password_hash($filterAll['password'], PASSWORD_DEFAULT); //Mã hóa mật khẩu
                $dataUpdate = [
                    'password' => $passwordHash,
                    'forgotToken' => null,
                    'update_at' => date('Y-m-d H:i:s')
                ];
                //Gọi lệnh update
                $updateStatus = update('users', $dataUpdate, "id=$userId");
                if ($updateStatus) {
                    setFlashData('smg', 'Thay đổi mật khẩu thành công!');
                    setFlashData('smg_type', 'success');
                    redirect('?module=auth&action=login');
                } else {
                    setFlashData('smg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau!');
                    setFlashData('smg_type', 'danger');
                }
            } else {
                setFlashData('smg', 'Vui lòng kiểm tra dữ liệu!');
                setFlashData('smg_type', 'danger');
                setFlashData('errors', $errors);
                redirect('?module=auth&action=reset&token=' . $token);
            }
        }
        // Dùng hàm getFalshData đọc xong rồi xóa session
        $smg = getFlashData('smg');
        $smg_type = getFlashData('smg_type');
        $errors = getFlashData('errors'); // hiển thị lỗi, chỉ sử dụng được 1 lần 
?>
        <!-- Form đặt lại mật khẩu  -->
        <div class="row">
            <div class="col-4" style="margin: 100px auto;">
                <h2 class="text-center text-uppercase">Đặt lại mật khẩu</h2>
                <!--Thông báo lỗi đăng ký -->
                <?php
                //Kiểm tra biến $smg có tồn tại hay không
                if (!empty($smg)) {
                    getSmg($smg, $smg_type);
                }
                ?>
                <form action="" method="post">
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
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <button type="submit" class="mg-btn btn btn-primary btn-block">Gửi</button>
                    <hr>
                    <p class="text-center"><a href="?module=auth&action=login">Đăng nhập tài khoản</a></p>
                </form>

            </div>

        </div>
<?php
    } else {
        getSmg('Liên kết không tồn tại hoặc đã hết hạn.', 'danger');
    }
} else {
    getSmg('Liên kết không tồn tại hoặc đã hết hạn.', 'danger');
}


layout('footer_login');
