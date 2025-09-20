<?php
include '../CSDL/db.php';
session_start();

// Lấy id đơn
$donnn_id = $_GET['id'] ?? 0;

// Lấy thông tin đơn
$sql = "SELECT d.*,
        t.tentc, t.tuoi AS tuoi_pet, t.gioitinh AS gioitinh_pet, t.mausac, t.cannang, t.tinhtrang, t.ngaycuuho, t.thongtin, t.anh, t.ghichu,
        l.tenloai, 
        u.hoten, u.tuoi AS tuoi_kh, u.gioitinh AS gioitinh_kh, u.sdt, u.email, u.diachi
        FROM donnhannuoi d
        JOIN thucung_coso t ON d.petcs_id = t.petcs_id
        JOIN loaithucung_coso l ON t.loaics_id = l.loaics_id
        JOIN taikhoan u ON d.user_id = u.user_id
        WHERE d.donnn_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donnn_id);
$stmt->execute();
$don = $stmt->get_result()->fetch_assoc();

// Nếu không tìm thấy đơn
if (!$don) {
    echo "Không tìm thấy đơn.";
    exit;
}

// Xử lý duyệt đơn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duyet'])) {
    $phanhoi = $_POST['phanhoi'];
    $sql = "UPDATE donnhannuoi SET trangthai='đã duyệt', ngayduyet=NOW(), phanhoi=? WHERE donnn_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $phanhoi, $donnn_id);
    $stmt->execute();
    header("Location: chitiet_donnhannuoi_admin.php?id=$donnn_id&tab=đã duyệt");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi tiết đơn nhận nuôi (Admin)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    .adopt-panel{
      background-color: lavenderblush;
    }
    .pet-image-wrap { 
      min-height: 220px; display:flex; align-items:center; justify-content:center;
    }
    .pet-image { max-width:100%; max-height:300px; border-radius:8px; }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<!-- Header -->
<?php include("navbar_admin.php"); ?>
<div class="container mt-4">
  <div class="adopt-panel card shadow-sm border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3><i class="fa-solid fa-heart"></i> <b>Chi tiết đơn nhận nuôi</b></h3>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <!-- Trái: Pet -->
        <div class="col-md-6 border-end">
          <h4 class="text-center mb-3">Thông tin thú cưng</h4>
          <hr>
          <div class="d-flex align-items-start mb-3">
            <div class="pet-image-wrap me-3" style="flex:0 0 45%;">
              <?php if (!empty($don['anh']) && file_exists("../".$don['anh'])): ?>
                <img src="../<?= htmlspecialchars($don['anh']) ?>" class="pet-image img-fluid rounded shadow-sm" alt="Ảnh thú cưng">
              <?php else: ?>
                <span class="text-muted">No Image</span>
              <?php endif; ?>
            </div>
            <div>
              <h4 class="fw-bold"><?= htmlspecialchars($don['tentc']) ?></h4>
              <p class="mb-1"><b>Giống:</b> <?= $don['tenloai'] ?></p>
              <p class="mb-1"><b>Tuổi:</b> <?= $don['tuoi_pet'] ?></p>
              <p class="mb-1"><b>Giới tính:</b> <?= $don['gioitinh_pet'] ?></p>
              <p class="mb-1"><b>Màu sắc:</b> <?= $don['mausac'] ?></p>
              <p class="mb-1"><b>Cân nặng:</b> <?= $don['cannang'] ?> kg</p>
              <p class="mb-1"><b>Tình trạng:</b> <?= $don['tinhtrang'] ?></p>
              <p class="mb-1"><b>Ngày cứu hộ:</b> <?= $don['ngaycuuho'] ? date("d-m-Y", strtotime($don['ngaycuuho'])) : "Chưa có" ?></p>
            </div>
          </div>
          <hr>
          <p class="mb-2"><b>Thông tin thêm:</b> <?= htmlspecialchars($don['thongtin']) ?></p>
          <p class="mb-0"><b>Tìm hiểu:</b> <?= htmlspecialchars($don['ghichu']) ?></p>
        </div>

        <!-- Phải: KH + đơn -->
        <div class="col-md-6">
          <h4 class="text-center mb-3">Thông tin khách hàng</h4>
          <hr>
          <p class="mb-2"><b>Họ tên:</b> <?= $don['hoten'] ?></p>
          <p class="mb-2"><b>Tuổi:</b> <?= $don['tuoi_kh'] ?></p>
          <p class="mb-2"><b>Giới tính:</b> <?= $don['gioitinh_kh'] ?></p>
          <p class="mb-2"><b>SĐT:</b> <?= $don['sdt'] ?></p>
          <p class="mb-2"><b>Email:</b> <?= $don['email'] ?></p>
          <p class="mb-2"><b>Địa chỉ:</b> <?= $don['diachi'] ?></p>
          <br><br>
          <hr>
          <p><b>Ngày gửi:</b> <?= $don['ngaygui'] ? date("d-m-Y", strtotime($don['ngaygui'])) : "Chưa có" ?></p>
        </div>
        
          <!-- Phần phản hồi & duyệt -->
        <div class="card p-3 mb-3 bg-light">
          <form method="post">
            <div class="row">
              <!-- Trái: Phản hồi -->
              <div class="col-md-6">
                <h6 class="fw-bold mb-2">Phản hồi của admin:</h6>
                <?php if ($don['trangthai']=='chờ duyệt'): ?>
                    <textarea class="form-control mb-3" name="phanhoi" rows="5" placeholder="Nhập phản hồi..."></textarea>
                <?php else: ?>
                  <div class="border p-3 bg-light rounded" style="min-height:150px;">
                    <?= nl2br(htmlspecialchars($don['phanhoi'])) ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Phải: Trạng thái + nút -->
              <div class="col-md-6 border-start d-flex align-items-center">
                <?php if ($don['trangthai']=='chờ duyệt'): ?>
                  <div class="d-flex gap-2 w-100">
                    <button type="submit" name="duyet" class="btn btn-warning flex-fill fw-bold py-3">
                      <i class="fa-solid fa-circle-notch fa-spin"></i> Duyệt đơn
                    </button>
                    <a href="xoa_donnhannuoi_admin.php?id=<?= $don['donnn_id']; ?>" 
                       onclick="return confirm('Bạn chắc chắn muốn xoá?')" 
                       class="btn btn-danger flex-fill fw-bold py-3">
                      <i class="fa-solid fa-trash"></i> Xóa đơn
                    </a>
                  </div>
                <?php else: ?>
                  <div class="p-3 bg-success text-white rounded text-center fw-bold w-100">
                    <i class="fa-solid fa-circle-check"></i> 
                    Đã duyệt (<?= $don['ngayduyet'] ? date("d-m-Y", strtotime($don['ngayduyet'])) : "Chưa có" ?>)
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </form>
        </div>

    </div>
  </div>
</div>

  <!-- Quay lại -->
  <div class="text-center mt-3">
    <a href="donnhannuoi_admin.php?tab=<?= $don['trangthai']=='chờ duyệt'?'chờ duyệt':'đã duyệt' ?>" class="btn btn-secondary fw-bold">
      ⬅ Quay lại
    </a>
  </div>
</div>

<!-- Footer -->
<?php include ("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
