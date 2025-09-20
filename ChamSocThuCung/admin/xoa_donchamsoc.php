<?php
include '../CSDL/db.php';
session_start();

$doncs_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($doncs_id > 0) {
    // Xóa tình trạng pet trước
    $sql_del_tt = "DELETE FROM tinhtrangpet WHERE doncs_id=?";
    $stmt_tt = $conn->prepare($sql_del_tt);
    $stmt_tt->bind_param("i", $doncs_id);
    $stmt_tt->execute();

    // Xóa đơn chăm sóc
    $sql_del_don = "DELETE FROM donchamsoc WHERE doncs_id=?";
    $stmt_don = $conn->prepare($sql_del_don);
    $stmt_don->bind_param("i", $doncs_id);

    if ($stmt_don->execute()) {
        header("Location: donchamsoc_admin.php?tab=dangchamsoc");
        exit;
    } else {
        $_SESSION['err'] = "Không thể xóa đơn chăm sóc: " . $conn->error;
    }
} else {
    $_SESSION['err'] = "Thiếu thông tin đơn chăm sóc để xóa.";
}

