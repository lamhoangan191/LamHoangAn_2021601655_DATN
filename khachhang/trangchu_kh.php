<?php
session_start();
include ("../CSDL/db.php");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <style>
        body{
            background: url("../deco photos/blur.png") center center/cover no-repeat;
        }
        .banner {
          background: url("../deco photos/edit.png") center center/cover no-repeat;
          min-height: 350px; /* chiều cao tối thiểu */
          display: flex;
          align-items: center;
          justify-content: center;
          padding-bottom: 240px; 
        }
        .title{
            color: firebrick;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
<!-- Header -->
<?php include ("navbar_kh.php") ?>

<!-- Body -->
  <!-- Hero Section -->
  <div class="banner shadow-sm border-2 border-bottom border-success">
    <div class="container-fluid py-5 text-center">
      <h1 class="display-5 fw-bold">Chào mừng đến với <span class="title">PETLAND</span> 🐾</h1>
      <p class="col-lg-8 mx-auto fs-5">
        Nơi kết nối bạn với những người bạn lắm lông đáng yêu.  
        Nhận nuôi, chăm sóc và đồng hành cùng thú cưng dễ dàng hơn bao giờ hết.
      </p>
      <p class="text-white fa-2x"><i class="fa-solid fa-shield-dog" style="color: black;"></i><i class="fa-solid fa-shield-cat"  style="color: black;"></i></p>
    </div>
  </div>

    <div class="container mt-4">
    <!-- Thẻ dịch vụ nổi bật -->
        <div class="row text-center g-4">
          <div class="col-md-6">
            <div class="card shadow-sm h-100 border-danger bg-light">
              <div class="card-body">
                <i class="fa-solid fa-heart fa-2x text-danger mb-3"></i>
                <h5 class="card-title fw-bold">Nhận nuôi thú cưng</h5>
                <p class="card-text">Kết nối bạn với những bé thú cưng đang cần một mái ấm yêu thương.</p>
                <a href="donnhannuoi_kh.php" class="btn btn-outline-danger">Xem đơn nhận nuôi</a>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card shadow-sm h-100 border-success bg-light">
              <div class="card-body">
                <i class="fa-solid fa-hand-holding-medical fa-2x text-success mb-3"></i>
                <h5 class="card-title fw-bold">Chăm sóc thú cưng</h5>
                <p class="card-text">Gửi gắm bé yêu để được chăm sóc tận tình, an toàn và chu đáo nhất.</p>
                <a href="donchamsoc_kh.php" class="btn btn-outline-success">Xem đơn chăm sóc</a>
              </div>
            </div>
          </div>
        </div>
      <!-- Danh sách thú cưng -->
      <div class="mt-5">
        <h3 class="fw-bold text-center mb-4">🐶 Thú cưng đang chờ được nhận nuôi 🐱</h3>
        <?php include("timkiempet_kh.php"); ?>
      </div>
    </div>
  
<!-- Footer -->
<?php include ("footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
