<?php
session_start();
include("../CSDL/db.php");

$message = "";

$user_id = $_SESSION['user_id'];

// Hàm lấy thông tin user
function getUserData($conn, $user_id) {
    $sql = "SELECT hoten, tuoi, gioitinh, email, sdt, diachi FROM taikhoan WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$user = getUserData($conn, $user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten    = $_POST['hoten'];
    $tuoi     = $_POST['tuoi'];
    $gioitinh = $_POST['gioitinh'];
    $email    = $_POST['email'];
    $sdt      = $_POST['sdt'];
    $diachi   = $_POST['diachi'];

    $sql = "UPDATE taikhoan SET hoten=?, tuoi=?, gioitinh=?, email=?, sdt=?, diachi=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssi", $hoten, $tuoi, $gioitinh, $email, $sdt, $diachi, $user_id);
    if ($stmt->execute()) {
        $message = "✅ Cập nhật thành công!";
        // Lấy lại dữ liệu mới nhất ngay sau khi update
        $user = getUserData($conn, $user_id);
    } else {
        $message = "❌ Lỗi khi cập nhật!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thông tin cá nhân (Khách hàng)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/blur.png") center center/cover no-repeat;
    }
    .info-panel{
        background-color: beige;
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100">
<!-- Header -->
<?php include ("navbar_kh.php") ?>

<!-- Body -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="info-panel card shadow rounded-3 border-2 border-success">
        <div class="card-header bg-success bg-gradient text-white text-center">
            <h3 class="text-center mb-2"><b><i class="fa-solid fa-user"></i> Thông tin cá nhân</b></h3>
        </div>
        <div class="card-body">
          <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label"><b>Tên khách hàng</b></label>
              <input type="text" class="form-control border-secondary" name="hoten" value="<?= htmlspecialchars($user['hoten']) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Tuổi</b></label>
              <input type="number" class="form-control border-secondary" name="tuoi" value="<?= htmlspecialchars($user['tuoi']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Giới tính</b></label>
              <select name="gioitinh" class="form-select border-secondary">
                <option value="Nam" <?= ($user['gioitinh'] == "Nam") ? "selected" : "" ?>>Nam</option>
                <option value="Nữ" <?= ($user['gioitinh'] == "Nữ") ? "selected" : "" ?>>Nữ</option>
                <option value="Khác" <?= ($user['gioitinh'] == "Khác") ? "selected" : "" ?>>Khác</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Email</b></label>
              <input type="email" class="form-control border-secondary" name="email" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Số điện thoại</b></label>
              <input type="text" class="form-control border-secondary" name="sdt" value="<?= htmlspecialchars($user['sdt']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Địa chỉ</b></label>
              <input type="text" class="form-control border-secondary" name="diachi" value="<?= htmlspecialchars($user['diachi']) ?>">
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
