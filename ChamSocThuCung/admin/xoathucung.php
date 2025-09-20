<?php
session_start();
require_once "../CSDL/db.php";

$petcs_id = $_GET['id'] ?? 0;

$sql = "DELETE FROM thucung_coso WHERE petcs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $petcs_id);

if ($stmt->execute())
{
    header("Location: trangchu_admin.php");
    exit;
}
else
{
    echo "❌ Có lỗi khi xóa thú cưng!";
}
?>
