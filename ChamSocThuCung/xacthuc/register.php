<?php
session_start();
include("../CSDL/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $hoten    = trim($_POST['hoten']);
    $tuoi     = trim($_POST['tuoi']);
    $gioitinh = trim($_POST['gioitinh']);
    $email    = trim($_POST['email']);
    $sdt      = trim($_POST['sdt']);
    $diachi   = trim($_POST['diachi']);

    // role mặc định khách hàng
    $vaitro = "khachhang";

    $sql = "INSERT INTO taikhoan (username, password, vaitro, hoten, tuoi, gioitinh, email, sdt, diachi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $username, $password, $vaitro, $hoten, $tuoi, $gioitinh, $email, $sdt, $diachi);

    if ($stmt->execute()) {
        $message = "✅ Đăng ký thành công! Hãy đăng nhập.";
    } else {
        $message = "❌ Lỗi: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
/*    .half-screen {
        height: 100vh;
    }*/
    body{
        background: url("../deco photos/auth3.jpg") center center/cover no-repeat;
    }
    .left-panel{
        margin-bottom: 400px;
    }
    .right-panel {
        margin: 50px 0;
    }
    .register-panel{
        background-color: beige;
    }
    .petland-title {
        margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row half-screen d-flex align-items-center">
      
    <!-- Cột trái: Chữ PETLAND -->
    <div class="left-panel col-md-6 text-center">

        <!-- Logo PETLAND -->
        <h1 class="petland-title display-1 fw-bold">
          <i class="fa-solid fa-paw fa-beat" style="color: #FFD43B;"></i> PETLAND
        </h1>

        <!-- Ảnh + đoạn văn ngang hàng -->
        <div class="row align-items-center mt-3">
          <!-- Ảnh (1/3) -->
          <div class="col-md-4">
              <img src="../deco photos/auth2.jpg" width="170" height="170" class="img-fluid rounded object-fit-cover" alt="alt"/>
          </div>

          <!-- Văn bản (2/3) -->
          <div class="col-md-8 d-flex align-items-center border-start">
            <p class="lead m-0">
              Tại <b>PETLAND</b>, chúng mình tin rằng mỗi người bạn lắm lông đều xứng đáng có một mái ấm.  
              Hãy đồng hành cùng chúng mình để tìm kiếm một gia đình yêu thương cho thú cưng cơ nhỡ  
              hoặc gửi gắm bé cưng yêu quý của bạn để được chăm sóc tận tình, chu đáo nhất.
            </p>
          </div>
        </div>

        <!-- Icon -->
        <p class="text-white fa-2x mt-3">
          <i class="fa-solid fa-shield-dog" style="color: black;"></i>
          <i class="fa-solid fa-shield-cat" style="color: black;"></i>
        </p>
    </div>

      <!-- Cột phải: Form đăng ký -->
      <div class="col-md-6 right-panel d-flex justify-content-center">
        <div class="register-panel card shadow-lg w-75 border-2 border-light">
          <div class="card-header bg-primary text-white text-center">
            <h4><b><i class="fa-solid fa-user-plus"></i> Đăng ký tài khoản</b></h4>
          </div>
          <div class="card-body">
            <?php if (!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>
            <form method="POST">
              <div class="mb-3">
                  <label class="form-label"><b>Tên đăng nhập <span class="text-danger">*</span></b></label>
                <input type="text" name="username" class="form-control border-secondary" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Mật khẩu <span class="text-danger">*</span></b></label>
                <input type="password" name="password" class="form-control border-secondary" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Họ tên <span class="text-danger">*</span></b></label>
                <input type="text" name="hoten" class="form-control border-secondary" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Tuổi</b></label>
                <input type="number" name="tuoi" class="form-control border-secondary">
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Giới tính</b></label>
                <select name="gioitinh" class="form-select border-secondary">
                  <option value="">-- Chọn --</option>
                  <option value="Nam">Nam</option>
                  <option value="Nữ">Nữ</option>
                  <option value="Khác">Khác</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Email</b></label>
                <input type="email" name="email" class="form-control border-secondary">
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Số điện thoại</b></label>
                <input type="text" name="sdt" class="form-control border-secondary">
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Địa chỉ</b></label>
                <input type="text" name="diachi" class="form-control border-secondary">
              </div>
              <button type="submit" class="btn btn-success fw-bold w-100">Đăng ký</button>
            </form>
            <p class="mt-3 text-center">Đã có tài khoản? <a href="login.php" class="text-decoration-none">Đăng nhập</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
