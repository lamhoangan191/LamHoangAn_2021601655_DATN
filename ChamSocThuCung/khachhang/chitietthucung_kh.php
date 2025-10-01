<?php
session_start();
include("../CSDL/db.php");

// Lấy toàn bộ GET hiện tại trừ id
$back_params = $_GET;
unset($back_params['id']);
$back_url = "trangchu_kh.php";
if (!empty($back_params)) {
    $back_url .= "?" . http_build_query($back_params) . "#pet-list";
} else {
    $back_url .= "#pet-list";
}

// Lấy id thú cưng từ URL
$petcs_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin thú cưng
$sql = "SELECT t.*, l.tenloai 
        FROM thucung_coso t 
        JOIN loaithucung_coso l ON t.loaics_id = l.loaics_id
        WHERE t.petcs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $petcs_id);
$stmt->execute();
$result = $stmt->get_result();
$pet = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi tiết thú cưng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/blur.png") center center/cover no-repeat;
    }
    .pet-panel{
        background-color: beige;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
    
<!-- Header -->
<?php include ("navbar_kh.php") ?>
    
<div class="container mt-5">
  <?php if ($pet): ?>
    <div class="pet-panel card shadow border-2 border-success">
      <div class="card-header bg-success bg-gradient text-white text-center">
          <h3 class="text-center"><b><i class="fa-solid fa-dog fa-rotate-by"></i> Thông tin thú cưng <i class="fa-solid fa-cat fa-flip-horizontal"></i></b></h3>
      </div>
      <div class="card-body">
        <div class="row">
          <!-- Ảnh thú cưng -->
          <div class="col-md-4 text-center">
            <?php if (!empty($pet['anh']) && file_exists("../" . $pet['anh'])): ?>
              <img src="../<?php echo $pet['anh']; ?>" class="img-fluid rounded" alt="Ảnh thú cưng">
            <?php else: ?>
              <div class="d-flex align-items-center justify-content-center bg-white" style="height:340px; border:2px dashed #ccc;">
                <span class="text-muted">No Image</span>
              </div>
            <?php endif; ?>
            <?php
            $back_param = urlencode($_SERVER['REQUEST_URI']); // giữ nguyên query (filter) hiện tại
            ?>
            <!-- nút Nhận nuôi -->
            <a href="gui_donnhannuoi.php?id=<?= $pet['petcs_id'] ?>&back=<?= $back_param ?>" class="btn btn-primary mt-3 w-75 fw-bold">Nhận nuôi</a>
          </div>

          <!-- Thông tin chi tiết -->
          <div class="col-md-8">
            <h2 class="mb-3"><b><?php echo htmlspecialchars($pet['tentc']); ?></b></h2>
            <hr>
            <p><strong>Giống:</strong> <?php echo htmlspecialchars($pet['tenloai']); ?></p>
            <p><strong>Tuổi:</strong> <?php echo htmlspecialchars($pet['tuoi']); ?></p>
            <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($pet['gioitinh']); ?></p>
            <p><strong>Màu sắc:</strong> <?php echo htmlspecialchars($pet['mausac']); ?></p>
            <p><strong>Cân nặng:</strong> <?php echo htmlspecialchars($pet['cannang']); ?> kg</p>
            <p><strong>Tình trạng:</strong> <?php echo htmlspecialchars($pet['tinhtrang']); ?></p>
            <?php
              $ngaycuuho = $pet['ngaycuuho'] ? date("d - m - Y", strtotime($pet['ngaycuuho'])) : "Chưa có";
            ?>
            <p><strong>Ngày cứu hộ:</strong> <?php echo $ngaycuuho; ?></p>
          </div>
        </div>

        <!-- Thông tin thêm -->
        <hr>
        <h5>Thông tin thêm</h5>
        <p><?php echo nl2br(htmlspecialchars($pet['thongtin'])); ?></p>
        <h5>Tìm hiểu về thú cưng</h5>
        <p><?php echo nl2br(htmlspecialchars($pet['ghichu'])); ?></p>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger">Không tìm thấy thú cưng.</div>
  <?php endif; ?>

  <!-- Nút quay lại -->
  <div class="text-center mt-3">
    <a href="<?= $back_url ?>" class="btn btn-secondary fw-bold">⬅ Quay lại</a>
  </div>
</div>

<!-- Footer -->
<?php include ("footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
