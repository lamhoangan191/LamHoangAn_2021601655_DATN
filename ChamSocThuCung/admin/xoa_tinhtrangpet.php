<?php
include '../CSDL/db.php';
session_start();

$ttpet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$doncs_id = isset($_GET['don']) ? intval($_GET['don']) : 0;

if ($ttpet_id > 0 && $doncs_id > 0) {
    // Xóa tình trạng pet
    $sql = "DELETE FROM tinhtrangpet WHERE ttpet_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ttpet_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Xóa tình trạng thành công!";
    } else {
        $_SESSION['err'] = "Không thể xóa tình trạng: " . $conn->error;
    }
} else {
    $_SESSION['err'] = "Thiếu thông tin để xóa tình trạng.";
}

// Quay lại chi tiết đơn
header("Location: chitiet_donchamsoc_admin.php?id=" . $doncs_id);
exit;