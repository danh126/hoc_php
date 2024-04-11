<?php
/*Chứa các hàm xử lý project với cơ sở dữ liệu*/

//Kiểm tra truy cập bằng 
// Hàm defined kiểm tra hằng số có tồn tại hay không

if (!defined('_CODE')) {
    die('Truy cập không hợp lệ !');
}

// Hàm dùng chung cho project connect với database (thêm, sửa, xóa)
function query($sql, $data = [], $check = false)
{
    global $conn;
    $ketqua = false;
    //Kiểm tra lỗi
    // echo $sql;
    // die();
    try {
        /* Gán biến để kết nói với file connect và sau đó 
    connect -> prepare (giúp quá trình insert an toàn hơn)*/
        $sta = $conn->prepare($sql);
        if (!empty($data)) {
            $ketqua = $sta->execute($data);
        } else {
            $ketqua = $sta->execute();
        }
    } catch (Exception $exception) {
        echo $exception->getMessage() . '<br>';
        echo 'File' . $exception->getFile() . '<br>';
        echo 'Line' . $exception->getLine();
        die();
    }
    //Nếu $check = true  (dùng tham số check để select dữ liệu)
    if ($check) {
        return $sta; // trả về dữ liệu
    }
    return $ketqua;
}

// Hàm insert database

function insert($table, $data)
{
    $key = array_keys($data); // lấy các key trong mảng $data
    $truong = implode(',', $key); // thêm dấu (,) vào giữa các key
    $valueTb = ':' . implode(',:', $key); // thêm dấu : phía trước các key khi VALUES
    //Câu kệnh sql phải có dấu nháy trống 'INSERT INTO '
    $sql = 'INSERT INTO ' . $table . '(' . $truong . ')' . 'VALUES(' . $valueTb . ')';
    //Gán biến $kq = hàm quey để chạy dữ liệu
    $kq = query($sql, $data);
    //Trả vể kết quả
    return $kq;
}

// Hàm update 
function update($table, $data, $condition = '')
{
    // Gán biến $update để lưu key và giá trị truyền vào 
    $update = '';
    //Dùng forech để duyệt mảng đa chiều $data
    foreach ($data as $key => $value) {
        // Nối biến $update với các key 
        $update .= $key . '= :' . $key . ',';
    }
    // Dùng trim để xóa dấu (,) cuối khi lập vòng lập foreach
    $update = trim($update, ',');
    // Kiểm tra điều kiện có tồn tại hay không $condition
    if (!empty($condition)) {
        // Viết sql UPDATE có diều kiện WHERE
        $sql = 'UPDATE ' . $table . ' SET ' . $update . ' WHERE ' . $condition;
    } else {
        // Viết sql UPDATE không có điều kiện
        $sql = 'UPDATE ' . $table . ' SET ' . $update;
    }
    $kq = query($sql, $data);
    return $kq;
}

// Hàm delete
function delete($table, $condition)
{
    // Kiểm tra điều kiện xóa có tồn tại hay không
    if (empty($condition)) {
        // Không tồn tại thì xóa hết bảng
        $sql = 'DELETE FROM ' . $table;
    } else {
        // Nếu tồn tại thì xóa theo điều kiện
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $condition;
    }
    $kq = query($sql);
    return $kq;
}

// Hàm lấy nhiều dòng dữ liệu
function getRaw($sql)
{
    // $data không dùng phải để rỗng ('')
    $kq = query($sql, '', true);
    //Kiểm tra biến kết quả có phải là object hay không
    if (is_object($kq)) {
        // Gán biến show dữ liệu
        $dataFetch = $kq->fetchAll(PDO::FETCH_ASSOC);
    }
    // Trả về biến lưu dữ liệu
    return $dataFetch;
}

// Hàm lấy 1 dòng theo thứ tự từ trên xuống
function oneRaw($sql)
{
    // $data không dùng phải để rỗng ('')
    $kq = query($sql, '', true);
    //Kiểm tra biến kết quả có phải là object hay không
    if (is_object($kq)) {
        // Gán biến show dữ liệu
        $dataFetch = $kq->fetch(PDO::FETCH_ASSOC);
    }
    // Trả về biến lưu dữ liệu
    return $dataFetch;
}

// Hàm đếm số dòng dữ liệu (rowCount)

function getRaws($sql)
{
    $kq = query($sql, '', true);
    // Kiểm tra biến $kq có giá trị hay không
    if (!empty($kq)) {
        return $kq->rowCount();
    }
}
