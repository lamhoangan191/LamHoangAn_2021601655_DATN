<?php
session_start();
include("../CSDL/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $vaitro   = trim($_POST['vaitro']);

    $sql = "SELECT * FROM taikhoan WHERE username = ? AND vaitro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $vaitro);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['vaitro']   = $row['vaitro'];
            $_SESSION['hoten']    = $row['hoten'];

            // Điều hướng theo vai trò
            if ($row['vaitro'] === "khachhang") {
                header("Location: ../khachhang/trangchu_kh.php");
            } elseif ($row['vaitro'] === "admin") {
                header("Location: ../admin/trangchu_admin.php");
            } else {
                $message = "❌ Vai trò không hợp lệ!";
            }
            exit();
        } else {
            $message = "❌ Sai mật khẩu!";
        }
    } else {
        $message = "❌ Sai tên đăng nhập hoặc vai trò!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/auth3.jpg") center center/cover no-repeat;
    }
    .half-screen {
        height: 100vh;
    }
    .right-panel {
        margin: 50px 0; /* nếu muốn cách trên dưới một chút */
    }
    .login-panel{
        background-color: beige;
    }
    .petland-title {
        margin-bottom: 10px; /* chỉnh con số này để đẩy chữ PETLAND lên cao */
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

    <!-- Cột phải: Form Đăng nhập -->
      <div class="col-md-6 right-panel d-flex justify-content-center">
        <div class="login-panel card shadow-lg w-75 border-2 border-light">
          <div class="card-header bg-primary text-white text-center">
            <h4><b><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</b></h4>
          </div>
          <div class="card-body">
            <?php if (!empty($message)) echo "<div class='alert alert-danger'>$message</div>"; ?>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label"><b>Tên đăng nhập</b></label>
                <input type="text" name="username" class="form-control border-secondary" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><b>Mật khẩu</b></label>
                <input type="password" name="password" class="form-control border-secondary" required>
              </div>
              <div class="mb-3 text-center">
                <div class="d-flex justify-content-center gap-3">
                  <input type="radio" class="btn-check" name="vaitro" id="khachhang" value="khachhang" required>
                  <label class="btn btn-outline-primary fw-bold px-4 border-2 border-primary" for="khachhang">Khách hàng</label>
                  <input type="radio" class="btn-check" name="vaitro" id="admin" value="admin" required>
                  <label class="btn btn-outline-danger fw-bold px-5 border-2 border-danger" for="admin">Admin</label>
                </div>
              </div>
              <button type="submit" class="btn btn-success fw-bold w-100">Đăng nhập</button>
            </form>
            <p class="mt-3 text-center">Chưa có tài khoản? <a href="register.php" class="text-decoration-none">Đăng ký</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>