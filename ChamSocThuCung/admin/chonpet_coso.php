<?php
include '../CSDL/db.php';
session_start();

if (!isset($_GET['kh_id']) || empty($_GET['kh_id'])) {
    echo "<div class='text-danger'>Chưa có khách hàng để chọn thú cưng</div>";
    exit;
}

$kh_id = intval($_GET['kh_id']);

// Lấy danh sách pet cơ sở mà KH này đã nhận nuôi (trạng thái đã duyệt)
$sql = "SELECT t.*, l.tenloai
        FROM thucung_coso t
        JOIN loaithucung_coso l ON t.loaics_id = l.loaics_id
        JOIN donnhannuoi d ON t.petcs_id = d.petcs_id
        LEFT JOIN donchamsoc cs 
            ON cs.petcs_id = t.petcs_id AND cs.trangthai = 'Đang chăm sóc'
        WHERE d.user_id = $kh_id 
          AND d.trangthai = 'đã duyệt'
          AND cs.doncs_id IS NULL"; 
$rs = $conn->query($sql);

if (!$rs || $rs->num_rows == 0) {
    echo "<div class='text-muted'>Không có thú cưng nào</div>";
    exit;
}

echo "<div class='row'>";
while ($row = $rs->fetch_assoc()) {
    echo "
    <div class='col-md-4 text-center mb-3'>
        <img src='../{$row['anh']}' class='img-fluid rounded' style='max-height:120px'><br>
        <strong>{$row['tentc']}</strong><br>
        <button type='button' class='btn btn-sm btn-outline-primary mt-1 fw-bold' 
        onclick='chonPetCoSo({$row['petcs_id']}, 
                            \"{$row['tentc']}\", 
                            \"{$row['anh']}\", 
                            \"{$row['tenloai']}\",
                            \"{$row['tuoi']}\",
                            \"{$row['gioitinh']}\",
                            \"{$row['mausac']}\",
                            \"{$row['cannang']}\",
                            \"{$row['tinhtrang']}\",
                            \"{$row['ngaycuuho']}\",
                            \"{$row['thongtin']}\",
                            \"{$row['ghichu']}\")'>Chọn</button>
    </div>
    ";
}
echo "</div>";
