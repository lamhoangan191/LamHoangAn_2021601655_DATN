<?php
session_start();
include ("../CSDL/db.php");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 bg-warning bg-gradient bg-opacity-50">
<!-- Header -->
<?php include ("navbar_admin.php") ?>

<!-- Body -->
  <!-- Hero Section -->
  <div class="bg-success bg-gradient text-white shadow-sm">
    <div class="container-fluid py-5 text-center">
      <h1 class="display-5 fw-bold">Chào mừng <span class="text-warning">Admin</span>
          <i class="fa-solid fa-gears" style="color: #ff7300;"></i>
      </h1>
      <p class="col-lg-8 mx-auto fs-5">
        Trang quản trị hệ thống PETLAND – nơi quản lý thú cưng, đơn nhận nuôi và chăm sóc.  
      </p>
      <p><i class="fa-solid fa-gear fa-2x"></i><i class="fa-solid fa-screwdriver-wrench fa-2x"></i></p>
    </div>
  </div>
<div class="container mt-4">

  <!-- Thẻ dịch vụ nổi bật -->
  <div class="row text-center g-4">
    <div class="col-md-3">
      <div class="card shadow-sm h-100 border-warning bg-light">
        <div class="card-body">
          <i class="fa-solid fa-dog fa-2x text-warning mb-3"></i>
          <h5 class="card-title fw-bold">Quản lý thú cưng</h5>
          <p class="card-text">Thêm, chỉnh sửa, xóa và quản lý thông tin thú cưng trong hệ thống.</p>
          <a href="themthucung.php" class="btn btn-outline-warning">Thêm thú cưng</a>
        </div>
      </div>
    </div>

      <!-- Quản lý đơn nhận nuôi -->
  <div class="col-md-3">
    <div class="card shadow-sm h-100 border-danger bg-light">
      <div class="card-body">
        <i class="fa-solid fa-heart fa-2x text-danger mb-3"></i>
        <h5 class="card-title fw-bold">Quản lý đơn nhận nuôi</h5>
        <p class="card-text">Theo dõi và xử lý các đơn yêu cầu nhận nuôi thú cưng từ khách hàng.</p>
        <a href="donnhannuoi_admin.php" class="btn btn-outline-danger">Xem đơn nhận nuôi</a>
      </div>
    </div>
  </div>

  <!-- Quản lý đơn chăm sóc -->
  <div class="col-md-3">
    <div class="card shadow-sm h-100 border-success bg-light">
      <div class="card-body">
        <i class="fa-solid fa-hand-holding-medical fa-2x text-success mb-3"></i>
        <h5 class="card-title fw-bold">Quản lý đơn chăm sóc</h5>
        <p class="card-text">Quản lý dịch vụ chăm sóc thú cưng và xử lý các yêu cầu từ khách hàng.</p>
        <a href="donchamsoc_admin.php" class="btn btn-outline-success">Xem đơn chăm sóc</a>
      </div>
    </div>
  </div>

  <!-- Thống kê -->
  <div class="col-md-3">
    <div class="card shadow-sm h-100 border-primary bg-light">
      <div class="card-body">
        <i class="fa-solid fa-chart-column fa-2x text-primary mb-3"></i>
        <h5 class="card-title fw-bold">Thống kê</h5>
        <p class="card-text">Xem báo cáo, thống kê về thú cưng được cứu hộ, nhận nuôi và chăm sóc.</p>
        <a href="thongke.php" class="btn btn-outline-primary">Xem thống kê</a>
      </div>
    </div>
  </div>
  </div>

  <!-- Danh sách thú cưng -->
  <div class="mt-5">
    <h3 class="fw-bold text-center mb-4">🐶 Thú cưng đang chờ được nhận nuôi 🐱</h3>
    <?php include("timkiempet_admin.php"); ?>
  </div>
</div>

<!-- Footer -->
<?php include ("footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
