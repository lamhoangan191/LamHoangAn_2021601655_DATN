<?php
session_start();
require_once "../CSDL/db.php";

$donnn_id = $_GET['id'] ?? 0;

$sql = "DELETE FROM donnhannuoi WHERE donnn_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donnn_id);

if ($stmt->execute())
{
    header("Location: donnhannuoi_kh.php?tab=chờ duyệt");
    exit;
}
else
{
    echo "❌ Có lỗi khi xóa đơn nhận nuôi!";
}
?>