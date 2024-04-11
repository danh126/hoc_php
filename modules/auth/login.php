<!-- Đăng nhập -->
<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

// Chuyển tên tab theo tưng file mảng $data -> function layout -> toán tử 3 ngôi header
$data = [
    'pageTile' => 'Đăng nhập tài khoản'
];

//Kiểm tra trạng thái đăng nhập
if (isLogin()) {
    redirect('?module=home&action=quanlychung');
}
if (isPost()) {
    $filterAll = filter();
    //Kiểm tra xem $filterAll có tồn tại dữ liệu email và password không
    if (!empty(trim($filterAll['email'])) && !empty(trim($filterAll['password']))) {
        //Kiểm tra đăng nhập
        $email = $filterAll['email']; //Gán biến $email
        $password = $filterAll['password']; //Gán biến password
        //Truy vấn láy thông tin
        $userQuery = oneRaw("SELECT password , id FROM users WHERE email = '$email'");

        //test
        // echo '<pre>';
        // print_r($userQuery);
        // echo '</pre>';
        // die();
        if (!empty($userQuery)) {
            $passwordHash = $userQuery['password'];
            $userId = $userQuery['id'];
            //Kiểm tra mật khẩu đăng nhập vào mật khẩu trên database có giống nhay không
            if (password_verify($password, $passwordHash)) {
                //Xử lý phần đăng nhập

                //Tạo token login
                $tokenLogin = sha1(uniqid() . time());
                // Insert vào bảng login token
                //Set thời gian theo giờ Việt Nam
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $dataInsert = [
                    'user_id' => $userId,
                    'token' => $tokenLogin,
                    'create_at_token' => date('Y-m-d H:i:s')
                ];
                $insertStatus = insert('logintoken', $dataInsert);
                if ($insertStatus) {
                    //Insert thành công

                    //Lưu tokenLogin vào session
                    setSession('logintoken', $tokenLogin);

                    // chuyển hướng đến trang quản lý chung
                    redirect('?module=home&action=quanlychung');
                } else {
                    setFlashData('smg', 'Không thể đăng nhập vui lòng thử lại sau.');
                    setFlashData('smg_type', 'danger');
                }
            } else {
                setFlashData('smg', 'Mật khẩu không chính xác!');
                setFlashData('smg_type', 'danger');
                // redirect('?module=auth&action=login');
            }
        } else {
            setFlashData('smg', 'Email không tồn tại!');
            setFlashData('smg_type', 'danger');
            // redirect('?module=auth&action=login');
        }
    } else {
        setFlashData('smg', 'Vui lòng nhập email và mật khẩu!!! ');
        setFlashData('smg_type', 'danger');
        // redirect('?module=auth&action=login');
    }
    redirect('?module=auth&action=login');
}

$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');

// Thêm header vào login
layout('header_login', $data);
?>
<div class="row">
    <div class="col-4" style="margin: 100px auto;">
        <h2 class="text-center text-uppercase">Đăng nhập quản lý user</h2>
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
            <label for="">Password</label>
            <div class="form-group mg-form">
                <input name="password" type="password" class="form-control" placeholder="Nhập vào mật khẩu">
            </div>
            <button type="submit" class="mg-btn btn btn-primary btn-block">Đăng nhập</button>
            <hr>
            <p class="text-center"><a href="?module=auth&action=forgot">Quên mật khẩu</a></p>
            <p class="text-center"><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
        </form>
    </div>
</div>
<?php
//Thêm footer vào login
layout('footer_login');
?>