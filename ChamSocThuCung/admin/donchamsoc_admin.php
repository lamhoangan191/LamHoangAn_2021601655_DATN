<?php
include '../CSDL/db.php';
session_start();

$tab = $_GET['tab'] ?? 'dangchamsoc';
$statusFilter = ($tab === 'hoanthanh') ? 'Hoàn thành' : 'Đang chăm sóc';

// Đếm toàn hệ thống
$cnt_dangchamsoc = 0; $cnt_hoanthanh = 0;
$rs = $conn->query("SELECT trangthai, COUNT(*) c FROM donchamsoc GROUP BY trangthai");
while ($r = $rs->fetch_assoc()) {
  if ($r['trangthai'] === 'Đang chăm sóc')   $cnt_dangchamsoc  = (int)$r['c'];
  if ($r['trangthai'] === 'Hoàn thành')    $cnt_hoanthanh = (int)$r['c'];
}

$sql = "SELECT d.*, 
               tc.tentc AS tentc_coso, tc.anh AS anh_coso,
               tn.tentc AS tentc_ncs, tn.anh AS anh_ncs,
               kh.hoten AS ten_kh
        FROM donchamsoc d
        LEFT JOIN thucung_coso tc ON d.petcs_id = tc.petcs_id
        LEFT JOIN thucung_ngoaicoso tn ON d.petncs_id = tn.petncs_id
        JOIN taikhoan kh ON d.kh_id = kh.user_id
        WHERE d.trangthai = ?
        ORDER BY d.ngaygui DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $statusFilter);
$stmt->execute();
$list = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý đơn chăm sóc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    /*.top-switch { position: sticky; top: 0; z-index: 5; }*/
    .don-card {
      width: 66%;
      height: 150px;
      margin: 20px auto;
      display: flex;
      border: 2px solid #ddd;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      background-color: mintcream;
    }
    .don-section { flex: 1; margin:10px auto; padding: 16px; display:flex; flex-direction:column; justify-content:center; align-items:center; }
    .pet-thumb { width:100px;height:100px;object-fit:contain;display:block;border-radius:10px;background:#f6f6f6; }
    .no-img { width:100px;height:100px;display:flex;align-items:center;justify-content:center;background:#f1f1f1;border-radius:10px;color:#888;font-weight:600;margin:8px auto; }
    .status-dang { background:#ffc107; color:#000; padding:6px 10px; border-radius:6px; }
    .status-done{ background:#28a745; color:#fff; padding:6px 10px; border-radius:6px; }
    @media (max-width: 992px){ .don-card{ width: 96%; } }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<!-- Header -->
<?php include("navbar_admin.php") ?>
<div class="container py-1">
    <div class="d-flex align-items-center justify-content-between p-2 rounded-3">
        <a href="them_donchamsoc.php" 
   class="btn fw-bold bg-success d-flex align-items-center gap-2 shadow-sm text-white">
   <i class="fa-solid fa-plus-circle"></i> Thêm đơn mới
</a>

        <div class="container">
          <ul class="nav nav-pills justify-content-center gap-2 bg-dark-subtle rounded-3 p-2">
            <li class="nav-item">
              <a class="nav-link <?php echo ($tab==='dangchamsoc'?'active':''); ?>" href="?tab=dangchamsoc">
                <b>Đang chăm sóc</b> <span class="badge text-bg-secondary"><?php echo $cnt_dangchamsoc; ?></span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo ($tab==='hoanthanh'?'active':''); ?>" href="?tab=hoanthanh">
                <b>Hoàn thành chăm sóc</b> <span class="badge text-bg-secondary"><?php echo $cnt_hoanthanh; ?></span>
              </a>
            </li>
          </ul>
        </div>
    </div>
</div>

<div class="container mt-3 mb-5">
  <?php if ($list->num_rows === 0): ?>
    <p class="text-center mt-4">Không có đơn nào trong mục này.</p>
  <?php endif; ?>
    
    <?php while($row = $list->fetch_assoc()): ?>
      <div class="don-card">
        <!-- Ảnh thú cưng -->
        <div class="don-section text-center">
          <img src="../<?php echo $row['anh_coso'] ?: $row['anh_ncs']; ?>" class="pet-thumb">
          <div><strong><?php echo $row['tentc_coso'] ?: $row['tentc_ncs']; ?></strong></div>
        </div>
        <!-- Khách hàng -->
        <div class="don-section text-center border-start">
          <div><strong>Khách hàng</strong></div>
          <div><?php echo $row['ten_kh']; ?></div>
        </div>
        <!-- Ngày + trạng thái -->
        <div class="don-section text-center border-start">
          <div class="mb-4"><strong>Ngày gửi: </strong><?php echo date('d - m - Y', strtotime($row['ngaygui'])); ?></div>
          <div>
            <?php if ($row['trangthai']=='Đang chăm sóc'): ?>
              <span class="status-dang badge bg-warning text-dark fs-6">
                  <i class="fa-solid fa-circle-notch fa-spin"></i> Thú cưng đang được chăm sóc
              </span>
            <?php else: ?>
              <span class="status-done badge bg-success fs-6">
                <i class="fa-solid fa-circle-check"></i> Thú cưng đã được chăm sóc xong<br>(<?php echo date('d - m - Y', strtotime($row['ngayhoanthanh'])); ?>)
              </span>
            <?php endif; ?>
          </div>
        </div>
        <!-- Nút -->
        <div class="don-section border-start">
          <a href="chitiet_donchamsoc_admin.php?id=<?php echo $row['doncs_id']; ?>" class="btn btn-info mb-2 fw-bold">Chi tiết</a>
          <?php if ($row['trangthai']=='Đang chăm sóc'): ?>
            <a href="xoa_donchamsoc.php?id=<?php echo $row['doncs_id']; ?>" class="btn btn-danger fw-bold w-50" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
</div>
<!-- Footer -->
<?php include ("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
