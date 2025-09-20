<?php
include("../CSDL/db.php");
session_start();

$tab = $_GET['tab'] ?? 'chờ duyệt';
$statusFilter = ($tab === 'đã duyệt') ? 'đã duyệt' : 'chờ duyệt';

// Đếm toàn hệ thống
$cnt_pending = 0; $cnt_approved = 0;
$rs = $conn->query("SELECT trangthai, COUNT(*) c FROM donnhannuoi GROUP BY trangthai");
while ($r = $rs->fetch_assoc()) {
  if ($r['trangthai'] === 'chờ duyệt')   $cnt_pending  = (int)$r['c'];
  if ($r['trangthai'] === 'đã duyệt')    $cnt_approved = (int)$r['c'];
}

// Lấy danh sách theo tab
$sql = "SELECT d.*, t.tentc, t.anh, u.hoten
        FROM donnhannuoi d
        JOIN thucung_coso t ON d.petcs_id = t.petcs_id
        JOIN taikhoan u ON d.user_id = u.user_id
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
  <title>Quản lý đơn nhận nuôi</title>
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
      background-color: lavenderblush;
    }
    .don-section { flex: 1; margin:10px auto; padding: 16px; display:flex; flex-direction:column; justify-content:center; align-items:center; }
    .pet-thumb { width:100px;height:100px;object-fit:contain;display:block;border-radius:10px;background:#f6f6f6; }
    .no-img { width:100px;height:100px;display:flex;align-items:center;justify-content:center;background:#f1f1f1;border-radius:10px;color:#888;font-weight:600;margin:8px auto; }
    .status-pending { background:#ffc107; color:#000; padding:6px 10px; border-radius:6px; }
    .status-approved{ background:#28a745; color:#fff; padding:6px 10px; border-radius:6px; }
    @media (max-width: 992px){ .don-card{ width: 96%; } }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<!-- Header -->
<?php include ("navbar_admin.php") ?>
<!-- Thanh chuyển mục -->
<div class="container top-switch mt-3">
  <ul class="nav nav-pills justify-content-center gap-2 bg-dark-subtle rounded-3 p-2">
    <li class="nav-item">
      <a class="nav-link <?php echo ($tab==='chờ duyệt'?'active':''); ?>" href="?tab=chờ duyệt">
          <b>Chờ duyệt</b> <span class="badge text-bg-secondary"><?php echo $cnt_pending; ?></span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($tab==='đã duyệt'?'active':''); ?>" href="?tab=đã duyệt">
        <b>Đã duyệt</b> <span class="badge text-bg-secondary"><?php echo $cnt_approved; ?></span>
      </a>
    </li>
  </ul>
</div>

<div class="container mt-3 mb-5">
  <?php if ($list->num_rows === 0): ?>
    <p class="text-center mt-4">Không có đơn nào trong mục này.</p>
  <?php endif; ?>

  <?php while($row = $list->fetch_assoc()): ?>
    <div class="don-card">
      <!-- 1: Ảnh + tên pet -->
      <div class="don-section text-center">
        <?php
          $hasImg = !empty($row['anh']) && file_exists("../".$row['anh']);
          if ($hasImg): ?>
          <img src="../<?php echo $row['anh']; ?>" alt="Pet" class="pet-thumb">
        <?php else: ?>
          <div class="no-img">No image</div>
        <?php endif; ?>
        <strong class="mt-1"><?php echo htmlspecialchars($row['tentc']); ?></strong>
      </div>
      <!-- 2: Khách hàng -->
      <div class="don-section text-center border-start">
          <div><b>Khách hàng</b></div>
        <?php echo htmlspecialchars($row['hoten']); ?>
      </div>
      <!-- 3: Ngày + Trạng thái -->
      <div class="don-section text-center border-start">
        <?php
        // Định dạng ngày gửi
        $ngaygui = $row['ngaygui'] ? date("d - m - Y", strtotime($row['ngaygui'])) : "Chưa có";
        ?>
        <div class="mb-4">
            <b>Ngày gửi: </b><br><?php echo $ngaygui; ?>
        </div>
        <?php if ($row['trangthai'] === 'chờ duyệt'): ?>
          <div class="status-pending badge bg-warning text-dark fs-6">
            <i class="fa-solid fa-circle-notch fa-spin"></i> Chờ duyệt
          </div>
        <?php else: ?>
          <?php
          // Định dạng ngày duyệt
          $ngayduyet = $row['ngayduyet'] ? date("d - m - Y", strtotime($row['ngayduyet'])) : "Chưa có";
          ?>
          <div class="status-approved badge bg-success text-white fs-6">
            <i class="fa-solid fa-circle-check"></i> Đã duyệt<br>(<?php echo $ngayduyet ?>)
          </div>
        <?php endif; ?>
      </div>
      <!-- 4: Nút -->
      <div class="don-section border-start">
        <a href="chitiet_donnhannuoi_admin.php?id=<?php echo $row['donnn_id']; ?>" class="btn btn-info mb-2 fw-bold">Chi tiết</a>
        <?php if ($row['trangthai'] === 'chờ duyệt'): ?>
          <a href="xoa_donnhannuoi_admin.php?id=<?php echo $row['donnn_id']; ?>" onclick="return confirm('Bạn chắc chắn muốn xoá?')" class="btn btn-danger fw-bold w-50">Xóa</a>
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
