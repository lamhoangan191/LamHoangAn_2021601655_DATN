<?php
include("../CSDL/db.php");
session_start();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $user_id = $_SESSION['user_id'] ?? 0;         // đã dùng session user_id (duy nhất)
    $petcs_id  = (int)($_POST['petcs_id'] ?? 0);      // lấy từ input hidden trong form
    $today   = date('Y-m-d H:i:s');

    // Chặn gửi trùng cùng 1 pet khi đơn đang tồn tại (chờ duyệt/đã duyệt)
    $check = $conn->prepare("SELECT donnn_id FROM donnhannuoi WHERE user_id=? AND petcs_id=? AND trangthai IN ('chờ duyệt','đã duyệt') LIMIT 1");
    $check->bind_param("ii", $user_id, $petcs_id);
    $check->execute();
    $has = $check->get_result()->num_rows > 0;

    if (!$has) {
        $ins = $conn->prepare("INSERT INTO donnhannuoi (user_id, petcs_id, trangthai, ngaygui) VALUES (?, ?, 'chờ duyệt', ?)");
        $ins->bind_param("iis", $user_id, $petcs_id, $today);
        $ins->execute();
    }

    // Sau khi gửi xong chuyển sang danh sách đơn (tab chờ duyệt)
    header("Location: donnhannuoi_kh.php?tab=chờ duyệt");
    exit();
}

/* Dùng user_id trong session nếu có; nếu chưa có thì tạm lấy 1 (để không báo lỗi) */
$user_id = $_SESSION['user_id'];

/* Lấy ID thú cưng */
if (!isset($_GET['id'])) die("❌ Không có ID thú cưng.");
$petcs_id = (int)$_GET['id'];

/* Lấy dữ liệu thú cưng */
$sql = "SELECT t.*, l.tenloai 
        FROM thucung_coso t 
        JOIN loaithucung_coso l ON t.loaics_id = l.loaics_id 
        WHERE petcs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $petcs_id);
$stmt->execute();
$pet = $stmt->get_result()->fetch_assoc();
if (!$pet) die("❌ Không tìm thấy thú cưng.");

/* Lấy thông tin KH: */
$sql_kh = "SELECT hoten, tuoi, gioitinh, email, sdt, diachi 
           FROM taikhoan WHERE user_id = ?";
$stmt = $conn->prepare($sql_kh);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$khachhang = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Gửi đơn nhận nuôi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/blur.png") center center/cover no-repeat;
    }
    .send-panel{
        background-color: lavenderblush;
    }
    /* Ảnh chỉ thu nhỏ, KHÔNG cắt */
    .pet-image-wrap{
      /*background:#f8f9fa;*/
      border-radius:8px;
      height:auto;
      min-height:240px; /* để khối nhìn cân hơn khi ảnh nhỏ */
      display:flex;
      align-items:center;
      justify-content:center;
      padding:8px;
    }
    .pet-image{
      max-width:100%;
      max-height:320px;
      width:auto;
      height:auto;     /* giữ tỉ lệ, không crop */
      border-radius:8px;
      display:block;
    }
  </style>
</head>

<?php include("navbar_kh.php"); ?>

<body class="d-flex flex-column min-vh-100">
<div class="container mt-4">
<div class="send-panel card shadow-sm border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3 class="text-center"><b><i class="fa-solid fa-heart-circle-plus"></i> Gửi yêu cầu nhận nuôi</b></h3>
    </div>
  <div class="row g-0">
    <!-- Trái: Thông tin thú cưng -->
    <div class="col-md-6 border-end">
      <div class="p-3">
        <h4 class="mb-3 text-center">Thông tin thú cưng</h4>
        <hr>
        <div class="d-flex align-items-start mb-3">
          <div class="pet-image-wrap me-3" style="flex:0 0 40%;">
            <?php if (!empty($pet['anh']) && file_exists("../".$pet['anh'])): ?>
              <img src="../<?= htmlspecialchars($pet['anh']) ?>" class="pet-image" alt="Ảnh thú cưng">
            <?php else: ?>
              <div class="d-flex align-items-center justify-content-center bg-white" style="width:200px; height:200px; border:2px dashed #ccc;">
                <span class="text-muted">No Image</span>
              </div>
            <?php endif; ?>
          </div>
          <div>
            <h4 class="mb-2"><b><?= htmlspecialchars($pet['tentc']) ?></b></h4>
            <p class="mb-1"><b>Giống:</b> <?= htmlspecialchars($pet['tenloai']) ?></p>
            <p class="mb-1"><b>Tuổi:</b> <?= htmlspecialchars($pet['tuoi']) ?></p>
            <p class="mb-1"><b>Giới tính:</b> <?= htmlspecialchars($pet['gioitinh']) ?></p>
            <p class="mb-1"><b>Màu sắc:</b> <?= htmlspecialchars($pet['mausac']) ?></p>
            <p class="mb-1"><b>Cân nặng:</b> <?= htmlspecialchars($pet['cannang']) ?> kg</p>
            <p class="mb-1"><b>Tình trạng:</b> <?= htmlspecialchars($pet['tinhtrang']) ?></p>
            <p class="mb-2"><b>Ngày cứu hộ:</b> <?= $pet['ngaycuuho'] ? date("d-m-Y", strtotime($pet['ngaycuuho'])) : "" ?></p>
          </div>
        </div>
        <hr>
        <p class="mb-2"><b>Thông tin thêm:</b> <?= htmlspecialchars($pet['thongtin']) ?></p>
        <p class="mb-0"><b>Tìm hiểu:</b> <?= htmlspecialchars($pet['ghichu']) ?></p>
      </div>
    </div>

    <!-- Phải: Thông tin khách hàng -->
    <div class="col-md-6">
      <div class="p-3">
        <h4 class="text-center mb-3">Thông tin khách hàng</h4>
        <hr>
        <?php if ($khachhang): ?>
          <p class="mb-2"><b>Họ tên:</b> <?= htmlspecialchars($khachhang['hoten']) ?></p>
          <p class="mb-2"><b>Tuổi:</b> <?= htmlspecialchars($khachhang['tuoi']) ?></p>
          <p class="mb-2"><b>Giới tính:</b> <?= htmlspecialchars($khachhang['gioitinh']) ?></p>
          <p class="mb-2"><b>Email:</b> <?= htmlspecialchars($khachhang['email']) ?></p>
          <p class="mb-2"><b>Số điện thoại:</b> <?= htmlspecialchars($khachhang['sdt']) ?></p>
          <p class="mb-2"><b>Địa chỉ:</b> <?= htmlspecialchars($khachhang['diachi']) ?></p>
          <br><br>
        <?php else: ?>
          <div class="alert alert-warning">Không tìm thấy thông tin khách hàng (user_id: <?= $user_id ?>).</div>
        <?php endif; ?>
        <hr>
        <form method="post">
          <input type="hidden" name="petcs_id" value="<?php echo $pet['petcs_id']; ?>">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="commit" onchange="toggleButton()">
            <label class="form-check-label" for="commit">
              Tôi xin cam kết sẽ luôn quan tâm, chăm sóc thú cưng một cách chu đáo, tận tình và có trách nhiệm, đảm bảo cho chúng một cuộc sống an toàn, khỏe mạnh và hạnh phúc.
            </label>
          </div>
          <button type="submit" name="submit" id="submitBtn" 
                  onclick="window.location.href='donnhannuoi_kh.php?id=<?php echo $pet['petcs_id']; ?>'" 
                  class="btn btn-primary w-100 mt-3 fw-bold" disabled>
            Gửi đơn nhận nuôi
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

    <!-- Nút quay lại -->
    <div class="text-center mt-3">
        <?php
        // sau khi đã lấy $pet và $page
        $back = isset($_GET['back']) ? urldecode($_GET['back']) : "chitietthucung_kh.php?id={$pet['petcs_id']}&page={$page}";
        ?>
        <!-- Nút Quay lại dùng $back -->
        <a href="<?= htmlspecialchars($back) ?>" class="btn btn-secondary fw-bold">⬅ Quay lại</a>
    </div>
</div>

<?php include("footer.php"); ?>
<script>
function toggleButton() {
  document.getElementById('submitBtn').disabled = !document.getElementById('commit').checked;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
