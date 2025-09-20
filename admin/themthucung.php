<?php
include("../CSDL/db.php");
session_start();

$message = "";

// Xử lý thêm mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $tentc = $_POST['tentc'];
    $tenloai = trim($_POST['tenloai']);
    $tuoi = $_POST['tuoi'];
    $gioitinh = $_POST['gioitinh'];
    $mausac = $_POST['mausac'];
    $cannang = $_POST['cannang'];
    $tinhtrang = $_POST['tinhtrang'];
    $ngaycuuho = $_POST['ngaycuuho'];
    $thongtin = $_POST['thongtin'];
    $ghichu = $_POST['ghichu'];

    // --- xử lý species (loài) ---
    $loaics_id = null;
    $check_sql = "SELECT loaics_id FROM loaithucung_coso WHERE tenloai = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $tenloai);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $loaics_id = $res->fetch_assoc()['loaics_id'];
    } else {
        $insert_sql = "INSERT INTO loaithucung_coso (tenloai) VALUES (?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("s", $tenloai);
        if ($stmt->execute()) {
            $loaics_id = $stmt->insert_id;
        }
    }

    // --- xử lý upload ảnh ---
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] == 0) {
        $target_dir = "../images/CS/";  
        $filename = basename($_FILES["anh"]["name"]);

        // Nếu admin chọn loài thì tự tạo thư mục cats/ hoặc dogs/
        if (stripos($tenloai, "chó") !== false) {
            $target_dir .= "dogs/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $anh = "images/CS/dogs/" . $filename;
        } elseif (stripos($tenloai, "mèo") !== false) {
            $target_dir .= "cats/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $anh = "images/CS/cats/" . $filename;
        } else {
            $anh = "images/" . $filename;
        }

        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["anh"]["tmp_name"], $target_file)) {
            // $anh đã có đường dẫn đúng để lưu DB
        }
    }

    $sql_insert = "INSERT INTO thucung_coso 
        (tentc, tuoi, gioitinh, mausac, cannang, tinhtrang, ngaycuuho, anh, thongtin, ghichu, loaics_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ssssdsssssi", $tentc, $tuoi, $gioitinh, $mausac, $cannang, $tinhtrang, $ngaycuuho, $anh, $thongtin, $ghichu, $loaics_id);

    if ($stmt->execute()) {
        $message = "✅ Thêm thú cưng thành công!";
    } else {
        $message = "❌ Lỗi khi thêm thú cưng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm thú cưng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    .create-panel{
        background-color: beige;
    }
    .image-container {
      position: relative;
      width: 100%;
      height: 350px;
      background-color: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-radius: 5px;
    }
    .image-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: 0.3s ease;
      border-radius: 5px;
    }
    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: 0.3s ease;
      border-radius: 5px;
    }
    .image-container:hover .overlay {
      opacity: 1;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<!-- Header -->
<?php include ("navbar_admin.php") ?>

<div class="container mt-5">
  <div class="create-panel card shadow rounded-3 border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3 class="text-center"><b><i class="fa-solid fa-file-pen"></i> Thêm thú cưng</b></h3>
    </div>
    <div class="card-body">
      <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="row">
          <!-- Ảnh -->
          <div class="col-md-4">
            <div class="image-container">
              <img id="preview">
              <div class="overlay">
                <label for="anh" class="btn btn-light fw-bold">Tải ảnh lên</label>
              </div>
              <input type="file" name="anh" id="anh" class="d-none" accept="image/*" onchange="previewImage(event)">
            </div>
          </div>

          <!-- Thông tin -->
          <div class="col-md-8">
            <div class="mb-2">
              <label class="form-label"><b>Tên thú cưng</b></label>
              <input type="text" class="form-control border-secondary" name="tentc" value="" required>
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Giống</b></label>
              <input type="text" class="form-control border-secondary" name="tenloai" value="" required>
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Tuổi</b></label>
              <select name="tuoi" class="form-select border-secondary">
                <option value="Nhí">Nhí</option>
                <option value="Trẻ">Trẻ</option>
                <option value="Trưởng thành">Trưởng thành</option>
                <option value="Già">Già</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Giới tính</b></label>
              <select name="gioitinh" class="form-select border-secondary">
                <option value="Đực">Đực</option>
                <option value="Cái">Cái</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Màu sắc</b></label>
              <input type="text" class="form-control border-secondary" name="mausac" value="">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Cân nặng</b></label>
              <input type="number" step="0.1" class="form-control border-secondary" name="cannang" value="">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Tình trạng</b></label>
              <input type="text" class="form-control border-secondary" name="tinhtrang" value="">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Ngày cứu hộ</b></label>
              <input type="date" class="form-control border-secondary" name="ngaycuuho" value="">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Thông tin thêm</b></label>
              <input type="text" class="form-control border-secondary" name="thongtin" value="">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Tìm hiểu về thú cưng</b></label>
              <textarea class="form-control border-secondary" name="ghichu" rows="5"></textarea>
            </div>

            <!-- Nút -->
              <button type="submit" name="add" class="btn btn-success fw-bold w-100">Thêm</button>
          </div>
        </div>
      </form>
    </div>
  </div>
    <!-- Nút quay lại -->
    <div class="text-center mt-3">
      <a href="trangchu_admin.php" class="btn btn-secondary fw-bold">⬅ Quay lại</a>
    </div>
</div>

<script>
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('preview');
  if (input.files && input.files[0]) {
    preview.src = URL.createObjectURL(input.files[0]);
  }
}
</script>
<!-- Footer -->
<?php include ("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
