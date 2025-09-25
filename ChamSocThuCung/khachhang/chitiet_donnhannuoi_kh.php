<?php
include("../CSDL/db.php");
session_start();

if (!isset($_GET['id'])) die("❌ Không có đơn.");

$user_id = $_SESSION['user_id'] ?? 0;
$donnn_id = (int)$_GET['id'];

// Lấy thông tin đơn, pet, KH, admin
$sql = "SELECT d.*, t.*, l.tenloai
        FROM donnhannuoi d
        JOIN thucung_coso t ON d.petcs_id = t.petcs_id
        JOIN loaithucung_coso l ON t.loaics_id = l.loaics_id
        WHERE d.donnn_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donnn_id);
$stmt->execute();
$don = $stmt->get_result()->fetch_assoc();

if (!$don) die("❌ Không tìm thấy đơn.");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi tiết đơn nhận nuôi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/blur.png") center center/cover no-repeat;
    }
    .adopt-panel{
      background-color: lavenderblush;
    }
    .pet-image-wrap { 
      min-height: 220px; display:flex; align-items:center; justify-content:center;
    }
    .pet-image { max-width:100%; max-height:300px; border-radius:8px; }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Header -->
<?php include("navbar_kh.php"); ?>

<div class="container mt-4">
  <div class="adopt-panel card shadow-sm border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3><i class="fa-solid fa-heart"></i> <b>Chi tiết đơn nhận nuôi</b></h3>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <!-- Trái: Thông tin pet -->
        <div class="col-md-6 border-end">
          <h4 class="text-center mb-3">Thông tin thú cưng</h4>
          <hr>
          <div class="d-flex align-items-start mb-3">
            <div class="pet-image-wrap me-3" style="flex:0 0 45%;">
              <?php if (!empty($don['anh']) && file_exists("../".$don['anh'])): ?>
                <img src="../<?= htmlspecialchars($don['anh']) ?>" class="pet-image img-fluid rounded shadow-sm" alt="Ảnh thú cưng">
              <?php else: ?>
                <span class="text-muted">No Image</span>
              <?php endif; ?>
            </div>
            <div>
              <h4 class="fw-bold"><?= htmlspecialchars($don['tentc']) ?></h4>
              <p class="mb-1"><b>Giống:</b> <?= htmlspecialchars($don['tenloai']) ?></p>
              <p class="mb-1"><b>Tuổi:</b> <?= htmlspecialchars($don['tuoi']) ?></p>
              <p class="mb-1"><b>Giới tính:</b> <?= htmlspecialchars($don['gioitinh']) ?></p>
              <p class="mb-1"><b>Màu sắc:</b> <?= htmlspecialchars($don['mausac']) ?></p>
              <p class="mb-1"><b>Cân nặng:</b> <?= htmlspecialchars($don['cannang']) ?> kg</p>
              <p class="mb-1"><b>Tình trạng:</b> <?= htmlspecialchars($don['tinhtrang']) ?></p>
              <p class="mb-1"><b>Ngày cứu hộ:</b> <?= $don['ngaycuuho'] ? date("d-m-Y", strtotime($don['ngaycuuho'])) : "Chưa có" ?></p>
            </div>
          </div>
          <hr>
          <p class="mb-2"><b>Thông tin thêm:</b> <?= htmlspecialchars($don['thongtin']) ?></p>
          <p class="mb-0"><b>Tìm hiểu:</b> <?= htmlspecialchars($don['ghichu']) ?></p>
        </div>

        <!-- Phải: Thông tin đơn -->
        <div class="col-md-6">
          <h4 class="text-center mb-3">Thông tin đơn</h4>
          <hr>
          <div class="mb-3">
            <p class="mb-1"><b>Ngày gửi:</b> <?= $don['ngaygui'] ? date("d-m-Y", strtotime($don['ngaygui'])) : "Chưa có" ?></p>
          </div>

        <div class="mb-3">
          <p><b>Phản hồi của admin:</b></p>
          <?php if ($don['trangthai']=='đã duyệt'): ?>
            <div class="border p-3 bg-light rounded" style="min-height: 180px;">
              <?= htmlspecialchars($don['phanhoi']) ?>
            </div>
          <?php else: ?>
            <div class="border p-3 text-muted rounded" style="min-height: 180px;">
              Chưa có phản hồi.
            </div>
          <?php endif; ?>
        </div>

        <div class="text-center">
          <?php if ($don['trangthai']=='chờ duyệt'): ?>
            <div class="d-flex justify-content-between gap-2">
              <span class="badge bg-warning text-dark flex-fill d-flex align-items-center justify-content-center py-3 fs-6">
                <i class="fa-solid fa-circle-notch fa-spin"></i> Chờ duyệt
              </span>
              <a href="xoa_donnhannuoi_kh.php?id=<?= $don['donnn_id'] ?>" 
                 onclick="return confirm('Bạn chắc chắn muốn xoá?')" 
                 class="btn btn-danger fw-bold flex-fill py-3 fs-6">
                 <i class="fa-solid fa-trash"></i> Xóa đơn
              </a>
            </div>
          <?php else: ?>
            <span class="badge bg-success text-white px-4 py-3 fs-6 w-100 d-block">
              <i class="fa-solid fa-circle-check"></i>
              Đã duyệt (<?= $don['ngayduyet'] ? date("d-m-Y", strtotime($don['ngayduyet'])) : "Chưa có" ?>)
            </span>
          <?php endif; ?>
        </div>

        </div>
        <?php if ($don['trangthai']=='đã duyệt'): ?>
        <hr>
        <h4 class="text-success text-center"><i class="fa-solid fa-drumstick-bite"></i> Gợi ý chăm sóc & dinh dưỡng <i class="fa-solid fa-bone"></i></h4>
        <div class="border p-3 bg-light rounded">
          <?php
            // Xác định loại pet
            $species = strtolower($don['tenloai']);
            $query = "pet food"; // mặc định

            if (strpos($species, "mèo") !== false) {
                $query = "kitten food"; // hoặc "cat food"
            } elseif (strpos($species, "chó") !== false) {
                $query = "puppy food"; // hoặc "dog food"
            }

            // Gọi API OpenFoodFacts
            $apiUrl = "https://world.openfoodfacts.org/cgi/search.pl?search_terms="
                      . urlencode($query) . "&search_simple=1&action=process&json=1&page_size=1";
            $apiResp = @file_get_contents($apiUrl);
            $foodData = $apiResp ? json_decode($apiResp, true) : null;

            // Tính nhu cầu năng lượng (MER)
            $weight = floatval($don['cannang']); // kg
            $age    = strtolower($don['tuoi']);
            $RER = 70 * pow($weight, 0.75); // công thức Resting Energy Requirement
            $factor = (strpos($age,"nhí")!==false || strpos($age,"con")!==false) ? 2.5 : 1.6;
            $MER = round($RER * $factor);

            if ($foodData && !empty($foodData['products'][0]['product_name'])) {
                $product = $foodData['products'][0];
                $name  = $product['product_name'] ?? "Thức ăn thú cưng";
                $kcal  = $product['nutriments']['energy-kcal_100g'] ?? 350; // fallback
                $gram  = round(($MER / $kcal) * 100);

                echo "<p><b>Thức ăn gợi ý:</b> {$name}</p>";
                echo "<p><b>Nhu cầu năng lượng:</b> ~{$MER} kcal/ngày</p>";
                echo "<p><b>Lượng thức ăn khuyến nghị:</b> khoảng {$gram} g/ngày (tham khảo)</p>";
                if (!empty($product['image_url'])) {
                    echo '<img src="'.$product['image_url'].'" class="img-fluid rounded my-2" style="max-height:150px;">';
                }
                if (!empty($product['url'])) {
                    echo '<p><a href="'.$product['url'].'" target="_blank" class="text-decoration-none fw-bold">Xem sản phẩm</a></p>';
                }
            } else {
                echo "<p class='text-muted'>Chưa tìm thấy gợi ý dinh dưỡng phù hợp.</p>";
            }
          ?>
        </div>
      <?php endif; ?>

      </div>
    </div>
  </div>

  <!-- Quay lại -->
  <div class="text-center mt-3">
    <a href="donnhannuoi_kh.php?tab=<?= $don['trangthai']=='chờ duyệt'?'chờ duyệt':'đã duyệt' ?>" class="btn btn-secondary fw-bold">
      ⬅ Quay lại
    </a>
  </div>
</div>

<!-- Footer -->
<?php include("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
