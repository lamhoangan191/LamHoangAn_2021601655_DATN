<?php
// Giả sử session đã có hoten (lấy từ bảng TAIKHOAN khi đăng nhập)
$hoten = isset($_SESSION['hoten']) ? $_SESSION['hoten'] : "Khách hàng";
$current = basename($_SERVER['PHP_SELF']); 
?>

<style>
    .navbar{
        position: sticky;
        top: 0;
        z-index: 5;
    }
    .navbar .nav-link:hover{
        color: white;
        border-bottom: 3px solid #0d6efd;
    }
    .navbar .nav-link.active {
        color: yellow !important;        
        border-top: 3px solid #0d6efd;
        font-weight: bold;
    }
    .dropdown-menu{
        background-color: beige;
    }
    .dropdown-menu .dropdown-item:hover {
        background-color: #0d6efd;
        color: yellow;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-3" href="trangchu_kh.php"><i class="fa-solid fa-paw" style="color: #FFD43B;"></i> PETLAND</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?= ($current=='donnhannuoi_kh.php' || $current == 'chitiet_donnhannuoi_kh.php' ? 'active' : '') ?>" href="donnhannuoi_kh.php">Đơn yêu cầu nhận nuôi</a></li>
        <li class="nav-item"><a class="nav-link <?= ($current=='donchamsoc_kh.php' || $current == 'chitiet_donchamsoc_kh.php' ? 'active' : '') ?>" href="donchamsoc_kh.php">Đơn chăm sóc</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Chào sen <?php echo $hoten; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="info_kh.php"><i class="fa-solid fa-user"></i> Thông tin cá nhân</a></li>
            <li><a class="dropdown-item" href="doimatkhau.php"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</a></li>
            <li><a class="dropdown-item" href="../xacthuc/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>