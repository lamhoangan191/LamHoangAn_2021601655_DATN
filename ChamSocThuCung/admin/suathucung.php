<?php
include("../CSDL/db.php");
session_start();

// Lấy toàn bộ GET hiện tại trừ id
$back_params = $_GET;
unset($back_params['id']);
$back_url = "trangchu_admin.php";
if (!empty($back_params)) {
    $back_url .= "?" . http_build_query($back_params) . "#pet-list";
} else {
    $back_url .= "#pet-list";
}

if (!isset($_GET['id'])) die("❌ Không có ID thú cưng.");
$petcs_id = (int)$_GET['id'];

// Lấy dữ liệu thú cưng
$sql = "SELECT t.*, l.tenloai
        FROM thucung_coso  t 
        JOIN loaithucung_coso  l ON t.loaics_id = l.loaics_id 
        WHERE petcs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $petcs_id);
$stmt->execute();
$pet = $stmt->get_result()->fetch_assoc();
if (!$pet) die("❌ Không tìm thấy thú cưng.");

$message = "";

// Cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $tentc = $_POST['tentc'];
    $tuoi = $_POST['tuoi'];
    $gioitinh = $_POST['gioitinh'];
    $mausac = $_POST['mausac'];
    $cannang = $_POST['cannang'];
    $tinhtrang = $_POST['tinhtrang'];
    $ngaycuuho = $_POST['ngaycuuho'];
    $anh = $pet['anh'];
    $thongtin = $_POST['thongtin'];
    $ghichu = $_POST['ghichu'];
    $tenloai = trim($_POST['tenloai']);
    
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

    $sql_update = "UPDATE thucung_coso
                   SET tentc=?, tuoi=?, gioitinh=?, mausac=?, cannang=?, tinhtrang=?, ngaycuuho=?, anh=?, thongtin=?, ghichu=?, loaics_id=?
                   WHERE petcs_id=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssdsssssii", $tentc, $tuoi, $gioitinh, $mausac, $cannang, $tinhtrang, $ngaycuuho, $anh, $thongtin, $ghichu, $loaics_id, $petcs_id);

    if ($stmt->execute()) {
        $message = "✅ Cập nhật thành công!";
        // load lại dữ liệu
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $petcs_id);
        $stmt->execute();
        $pet = $stmt->get_result()->fetch_assoc();
    } else {
        $message = "❌ Lỗi khi cập nhật.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa thú cưng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    .update-panel{
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
  <div class="update-panel card shadow rounded-3 border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3 class="text-center"><b><i class="fa-solid fa-pen-to-square"></i> Sửa thông tin thú cưng</b></h3>
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
                <img id="preview" 
                     src="<?php echo (!empty($pet['anh']) && file_exists("../" . $pet['anh'])) ? "../" . $pet['anh'] : ''; ?>" 
                     alt="Ảnh thú cưng" 
                     style="<?php echo (!empty($pet['anh']) && file_exists("../" . $pet['anh'])) ? '' : 'display:none;'; ?>">

                <div id="no-image" 
                     class="d-flex align-items-center justify-content-center w-100 h-100 text-muted"
                     style="<?php echo (!empty($pet['anh']) && file_exists("../" . $pet['anh'])) ? 'display:none;' : ''; ?>">
                  <!--No Image-->
                </div>

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
              <input type="text" class="form-control border-secondary" name="tentc" value="<?= htmlspecialchars($pet['tentc']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Giống</b></label>
              <input type="text" class="form-control border-secondary" name="tenloai" value="<?= htmlspecialchars($pet['tenloai']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Tuổi</b></label>
              <select name="tuoi" class="form-select border-secondary">
                <option value="Nhí" <?= ($pet['tuoi'] == "Nhí") ? "selected" : "" ?>>Nhí</option>
                <option value="Trẻ" <?= ($pet['tuoi'] == "Trẻ") ? "selected" : "" ?>>Trẻ</option>
                <option value="Trưởng thành" <?= ($pet['tuoi'] == "Trưởng thành") ? "selected" : "" ?>>Trưởng thành</option>
                <option value="Già" <?= ($pet['tuoi'] == "Già") ? "selected" : "" ?>>Già</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Giới tính</b></label>
              <select name="gioitinh" class="form-select border-secondary">
                <option value="Đực" <?= ($pet['gioitinh'] == "Đực") ? "selected" : "" ?>>Đực</option>
                <option value="Cái" <?= ($pet['gioitinh'] == "Cái") ? "selected" : "" ?>>Cái</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Màu sắc</b></label>
              <input type="text" class="form-control border-secondary" name="mausac" value="<?= htmlspecialchars($pet['mausac']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Cân nặng</b></label>
              <input type="number" step="0.1" class="form-control border-secondary" name="cannang" value="<?= htmlspecialchars($pet['cannang']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Tình trạng</b></label>
              <input type="text" class="form-control border-secondary" name="tinhtrang" value="<?= htmlspecialchars($pet['tinhtrang']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Ngày cứu hộ</b></label>
              <input type="date" class="form-control border-secondary" name="ngaycuuho" value="<?= htmlspecialchars($pet['ngaycuuho']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Thông tin thêm</b></label>
              <input type="text" class="form-control border-secondary" name="thongtin" value="<?= htmlspecialchars($pet['thongtin']) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label"><b>Tìm hiểu về thú cưng</b></label>
              <textarea class="form-control border-secondary" name="ghichu" rows="5"><?= htmlspecialchars($pet['ghichu']) ?></textarea>
            </div>

            <!-- Nút -->
            <div class="d-flex justify-content-between mt-3 gap-2">
              <button type="submit" name="update" class="btn btn-warning flex-fill fw-bold">Cập nhật</button>
              <a href="xoathucung.php?id=<?= $pet['petcs_id'] ?>" onclick="return confirm('Bạn chắc chắn muốn xoá?')" class="btn btn-danger flex-fill fw-bold">Xóa</a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
    <!-- Nút quay lại -->
    <div class="text-center mt-3">
      <a href="<?= $back_url ?>" class="btn btn-secondary fw-bold">⬅ Quay lại</a>
    </div>
</div>

<script>
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('preview');
  const noImageDiv = document.getElementById('no-image');
  
  if (input.files && input.files[0]) {
    // Ẩn div "No Image"
    if (noImageDiv) {
      noImageDiv.style.display = "none";
    }
    
    // Hiển thị và cập nhật ảnh preview
    preview.style.display = "block";
    preview.src = URL.createObjectURL(input.files[0]);
  }
}
</script>
<!-- Footer -->
<?php include ("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
