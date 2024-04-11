<?php
//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

//Kiểm tra trạng thái đăng nhập 
if (!isLogin()) {
    redirect('?module=auth&action=login');
}
// Chuyển tên tab theo tưng file mảng $data -> function layout -> toán tử 3 ngôi header
$data = [
    'pageTile' => 'Danh sách người dùng'
];
layout('header', $data);

//Truy vấn vào bảng users

$listUsers = getRaw("SELECT * FROM users ORDER BY creat_at "); // sắp xếp theo thời gian thêm vào 

// echo '<pre>';
// print_r($listUsers);
// echo '</pre>';
// die();
//Dùng hàm getFalshData đọc xong rồi xóa session
$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');
?>

<div class="container">
    <hr>
    <h2>Quản lý người dùng</h2>
    <p>
        <a href="?module=users&action=add" class="btn btn-success btn-sm">Thêm người dùng <i class="fa-solid fa-plus"></i></a>
    </p>
    <?php
    //Kiểm tra biến $smg có tồn tại hay không
    if (!empty($smg)) {
        getSmg($smg, $smg_type);
    }
    ?>
    <table class="table table-bordered">
        <thead>
            <th>STT</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Trạng thái</th>
            <th width="5%">Sửa</th>
            <th width="5%">Xóa</th>
        </thead>
        <tbody>
            <?php
            if (!empty($listUsers)) : //Nếu $listUsers có giá trị 
                $count = 0; //Biến đếm stt
                //Dùng vòng lập foreach đọc tất cả giá trị trong mảng
                foreach ($listUsers as $item) :
                    //Mỗi lần lập stt tăng lên 1
                    $count++;
                    // echo key vào thẻ td
            ?>
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $item['fullname']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                        <td><?php echo $item['phone']; ?></td>
                        <!-- Dùng toán tử ba ngôi để hiện trạng thái -->
                        <td><?php echo $item['status'] == 1 ? '<button class = "btn btn-success btn-sm">Đã kích hoạt</button>' :
                                '<button class = "btn btn-danger btn-sm">Chưa kích hoạt</button>'; ?></td>
                        <td><a href="<?php echo _WEB_HOST; ?>?module=users&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-regular fa-pen-to-square"></i></a></td>
                        <td><a href="<?php echo _WEB_HOST; ?>?module=users&action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-user-minus"></i></a></td>
                    </tr>
                <?php
                endforeach;
            else : //Nếu không có người dùng 
                ?>
                <tr>
                    <td colspan="7">
                        <div class="alert alert-danger text-center">Không có người dùng nào.</div>
                    </td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>
    </table>
</div>

<?php
layout('footer');
?>