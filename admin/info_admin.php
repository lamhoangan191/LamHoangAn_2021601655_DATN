<?php
session_start();
include("../CSDL/db.php");

// Giả sử session đã có hoten admin
$hoten = isset($_SESSION['hoten']) ? $_SESSION['hoten'] : "Admin";

$message = "";
$user_id = $_SESSION['user_id'];

// Lấy thông tin admin từ DB
$sql = "SELECT hoten, email, sdt FROM taikhoan WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $sdt   = trim($_POST['sdt']);

    $sql = "UPDATE taikhoan SET hoten=?, email=?, sdt=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $hoten, $email, $sdt, $user_id);
    if ($stmt->execute()) {
        $message = "✅ Cập nhật thành công!";
        // Lấy lại dữ liệu mới
        $sql = "SELECT hoten, email, sdt FROM taikhoan WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message = "❌ Có lỗi xảy ra!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thông tin cá nhân (Admin)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    .info-panel{
        background-color: beige;
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<!-- Header -->
<?php include ("navbar_admin.php") ?>

<!-- Body -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="info-panel card shadow rounded-3 border-2 border-success">
        <div class="card-header bg-success bg-gradient text-white text-center">
            <h3 class="text-center"><b><i class="fa-solid fa-user-gear"></i> Thông tin cá nhân</b></h3>
        </div>
        <div class="card-body">
          <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label"><b>Tên admin</b></label>
              <input type="text" class="form-control border-secondary" name="hoten" value="<?= htmlspecialchars($user['hoten']) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Email</b></label>
              <input type="email" class="form-control border-secondary" name="email" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Số điện thoại</b></label>
              <input type="text" class="form-control border-secondary" name="sdt" value="<?= htmlspecialchars($user['sdt']) ?>">
            </div>
            <button type="submit" class="btn btn-success fw-bold w-100">Cập nhật</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Footer -->
<?php include ("footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
