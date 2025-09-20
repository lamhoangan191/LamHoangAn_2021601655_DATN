<?php
include '../CSDL/db.php';
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_GET['sdt']) || empty($_GET['sdt'])) {
    echo json_encode(["success" => false, "msg" => "Thiếu số điện thoại"]);
    exit;
}

$sdt = $conn->real_escape_string($_GET['sdt']);
$sql = "SELECT user_id, hoten, tuoi, gioitinh, email, sdt, diachi FROM taikhoan WHERE sdt='$sdt' LIMIT 1";
$rs  = $conn->query($sql);  

if ($rs && $rs->num_rows > 0) {
    $row = $rs->fetch_assoc();
    echo json_encode([
        "success" => true,
        "user_id" => $row['user_id'],
        "hoten"   => $row['hoten'],
        "tuoi"   => $row['tuoi'],
        "gioitinh"   => $row['gioitinh'],
        "email"   => $row['email'],
        "sdt"   => $row['sdt'],
        "diachi"   => $row['diachi']
    ]);
} else {
    echo json_encode(["success" => false, "msg" => "Không tìm thấy khách hàng"]);
}
