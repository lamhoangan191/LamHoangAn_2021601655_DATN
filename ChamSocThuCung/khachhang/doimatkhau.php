<?php
session_start();
include("../CSDL/db.php");

// Giả sử session đã có hoten (lấy từ bảng TAIKHOAN khi đăng nhập)
$hoten = isset($_SESSION['hoten']) ? $_SESSION['hoten'] : "Khách hàng";


$user_id = $_SESSION['user_id'];
$vaitro   = $_SESSION['vaitro'];
$message = "";

// Khi người dùng submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_pass = trim($_POST['old_pass']);
    $new_pass = trim($_POST['new_pass']);
    $re_pass  = trim($_POST['re_pass']);

    // Kiểm tra nhập lại mật khẩu
    if ($new_pass !== $re_pass) {
        $message = "❌ Mật khẩu mới nhập lại không khớp.";
    } else {
        // Lấy mật khẩu hiện tại trong DB
        $sql = "SELECT password FROM taikhoan WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && password_verify($old_pass, $row['password'])) {
            // Hash mật khẩu mới
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

            // Cập nhật DB
            $sql = "UPDATE taikhoan SET password=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed_new_pass, $user_id);

            if ($stmt->execute()) {
                $message = "✅ Đổi mật khẩu thành công!";
            } else {
                $message = "❌ Lỗi: " . $stmt->error;
            }
        } else {
            $message = "❌ Mật khẩu cũ không đúng!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đổi mật khẩu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/blur.png") center center/cover no-repeat;
    }
    .password-panel{
        background-color: beige;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Header --> 
<?php include ("navbar_kh.php") ?>

<main class="flex-grow-1">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="password-panel card shadow rounded-3 border-2 border-success">
        <div class="card-header bg-success bg-gradient text-white text-center">
            <h3 class="text-center mb-2"><b><i class="fa-solid fa-lock"></i> Đổi mật khẩu</b></h3>
        </div>
        <div class="card-body">
          <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label"><b>Mật khẩu cũ</b></label>
              <input type="password" class="form-control border-secondary" name="old_pass" required>
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Mật khẩu mới</b></label>
              <input type="password" class="form-control border-secondary" name="new_pass" required>
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Nhập lại mật khẩu mới</b></label>
              <input type="password" class="form-control border-secondary" name="re_pass" required>
            </div>
            <button type="submit" class="btn btn-success fw-bold w-100">Đổi mật khẩu</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</main>

<!-- FOOTER -->
<?php include ("footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
