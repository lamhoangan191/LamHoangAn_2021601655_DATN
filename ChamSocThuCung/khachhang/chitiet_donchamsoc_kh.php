<?php
include '../CSDL/db.php';
session_start();

$doncs_id = $_GET['id'] ?? 0;

// Lấy thông tin đơn
$sql = "SELECT d.*, 
            tc.tentc AS tentc_coso, tc.anh AS anh_coso, tc.tuoi AS tuoi_coso, tc.gioitinh AS gioitinh_coso, tc.mausac AS mausac_coso, tc.cannang AS cannang_coso, tc.tinhtrang AS tinhtrang_coso, tc.ngaycuuho, tc.thongtin AS thongtin_coso, tc.ghichu,
            lcs.tenloai AS tenloai_coso,
            tn.tentc AS tentc_ncs, tn.anh AS anh_ncs, tn.tuoi AS tuoi_ncs, tn.gioitinh AS gioitinh_ncs, tn.mausac AS mausac_ncs, tn.cannang AS cannang_ncs, tn.thongtin AS thongtin_ncs,
            lncs.tenloai AS tenloai_ncs,
            ad.hoten AS ten_admin, ad.sdt AS sdt_admin, ad.email AS email_admin
        FROM donchamsoc d
        LEFT JOIN thucung_coso tc ON d.petcs_id = tc.petcs_id
        LEFT JOIN loaithucung_coso lcs ON tc.loaics_id = lcs.loaics_id
        LEFT JOIN thucung_ngoaicoso tn ON d.petncs_id = tn.petncs_id
        LEFT JOIN loaithucung_ngoaicoso lncs ON tn.loaincs_id = lncs.loaincs_id
        JOIN taikhoan ad ON d.admin_id = ad.user_id
        WHERE d.doncs_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doncs_id);
$stmt->execute();
$don = $stmt->get_result()->fetch_assoc();

if (!$don) {
    echo "Không tìm thấy đơn chăm sóc.";
    exit;
}

// Lấy danh sách tình trạng pet
$sql_tt = "SELECT * FROM tinhtrangpet WHERE doncs_id=?";
$stmt = $conn->prepare($sql_tt);
$stmt->bind_param("i", $doncs_id);
$stmt->execute();
$rs_tt = $stmt->get_result();

// Tính tổng chi phí
$total_cost = 0;
$sql_sum = "SELECT SUM(chiphi) AS tong FROM tinhtrangpet WHERE doncs_id=?";
$stmt_sum = $conn->prepare($sql_sum);
$stmt_sum->bind_param("i", $doncs_id);
$stmt_sum->execute();
$res_sum = $stmt_sum->get_result()->fetch_assoc();
$total_cost = $res_sum['tong'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi tiết đơn chăm sóc (KH)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    body{
        background: url("../deco photos/blur.png") center center/cover no-repeat;
    }
    .care-panel {
      background-color: mintcream;
    }
    .pet-image-wrap { 
      min-height: 220px; display:flex; align-items:center; justify-content:center;
    }
    .pet-image { max-width:100%; max-height:300px; border-radius:8px; }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include("navbar_kh.php"); ?>

<div class="container mt-4">
  <div class="care-panel card shadow-sm border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3><i class="fa-solid fa-hand-holding-medical"></i> <b>Chi tiết đơn chăm sóc</b></h3>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <!-- Trái: Thông tin thú cưng -->
        <div class="col-md-6 border-end">
          <h4 class="text-center mb-3">Thông tin thú cưng</h4>
          <hr>
          <div class="d-flex align-items-start mb-3">
            <div class="pet-image-wrap me-3" style="flex:0 0 45%;">
              <img src="../<?= $don['anh_coso'] ?: $don['anh_ncs'] ?>" 
                   class="pet-image img-fluid rounded shadow-sm" alt="Ảnh thú cưng">
            </div>
            <div>
              <h4 class="fw-bold"><?= $don['tentc_coso'] ?: $don['tentc_ncs'] ?></h4>
              <p class="mb-1"><b>Giống:</b> <?= $don['tenloai_coso'] ?: $don['tenloai_ncs'] ?></p>
              <p class="mb-1"><b>Tuổi:</b> <?= $don['tuoi_coso'] ?: $don['tuoi_ncs'] ?></p>
              <p class="mb-1"><b>Giới tính:</b> <?= $don['gioitinh_coso'] ?: $don['gioitinh_ncs'] ?></p>
              <p class="mb-1"><b>Màu sắc:</b> <?= $don['mausac_coso'] ?: $don['mausac_ncs'] ?></p>
              <p class="mb-1"><b>Cân nặng:</b> <?= $don['cannang_coso'] ?: $don['cannang_ncs'] ?> kg</p>
              <?php if ($don['petcs_id']): // Thú cưng cơ sở ?>
              <p class="mb-1"><b>Tình trạng:</b> <?= $don['tinhtrang_coso'] ?></p>
              <p class="mb-1"><b>Ngày cứu hộ:</b> <?= $don['ngaycuuho'] ? date("d - m - Y", strtotime($don['ngaycuuho'])) : "Chưa có" ?></p>
              <?php elseif ($don['petncs_id']): // Thú cưng ngoài cơ sở ?>
              <p></p>
              <?php endif; ?>
            </div>
          </div>
          <hr>
          <p class="mb-2"><b>Thông tin thêm:</b> <?= $don['thongtin_coso'] ?: $don['thongtin_ncs'] ?></p>
          <?php if ($don['petcs_id']): // Thú cưng cơ sở ?>
          <p class="mb-0"><b>Tìm hiểu:</b> <?= $don['ghichu'] ?></p>
          <?php elseif ($don['petncs_id']): // Thú cưng ngoài cơ sở ?>
          <p></p>
          <?php endif; ?>
        </div>

        <!-- Phải: Thông tin đơn -->
        <div class="col-md-6">
          <h4 class="text-center mb-3">Thông tin đơn</h4>
          <hr>
          <div class="mb-3">
            <p><b>Admin phụ trách:</b></p>
            <p class="mb-1"><b>Họ tên:</b> <?= $don['ten_admin'] ?></p>
            <p class="mb-1"><b>SĐT:</b> <?= $don['sdt_admin'] ?></p>
            <p class="mb-3"><b>Email:</b> <?= $don['email_admin'] ?></p>
          </div>
          <br><br><hr>
          <div class="mb-3">
            <p class="mb-1"><b>Ngày gửi:</b> <?= date("d-m-Y", strtotime($don['ngaygui'])) ?></p>
          </div>
          <hr>
          <div class="text-center mb-3">
            <?php if($don['trangthai']=="Đang chăm sóc"): ?>
              <span class="badge bg-warning text-dark fs-6 px-4 py-3 w-100">
                <i class="fa-solid fa-circle-notch fa-spin"></i> Thú cưng đang được chăm sóc
              </span>
            <?php else: ?>
              <span class="badge bg-success text-white fs-6 px-4 py-3 w-100">
                <i class="fa-solid fa-circle-check"></i> Thú cưng đã được chăm sóc xong
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Bảng tình trạng -->
      <hr>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle border-secondary">
          <thead class="table-secondary border-secondary">
            <tr>
              <th>Dịch vụ chăm sóc/Khám chữa bệnh</th>
              <th style="width:20%">Trạng thái</th>
              <th style="width:15%">Chi phí</th>
              <th style="width:30%">Ghi chú/Phản hồi</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row=$rs_tt->fetch_assoc()): ?>
            <tr>
              <td><?= $row['tinhtrang'] ?></td>
              <td><?= $row['trangthai'] ?></td>
              <td><?= number_format($row['chiphi'], 0, ',', ',') ?> đ</td>
              <td class="text-break"><?= $row['ghichu'] ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if($total_cost > 0): ?>
            <tr class="table-warning fw-bold border-secondary">
              <td colspan="2" class="text-end">Tổng chi phí:</td>
              <td colspan="2" class="text-danger"><?= number_format($total_cost, 0, ',', ',') ?> đ</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Quay lại -->
  <div class="text-center mt-3">
    <a href="donchamsoc_kh.php?tab=<?= $don['trangthai']=='Đang chăm sóc'?'dangchamsoc':'hoanthanh' ?>" 
       class="btn btn-secondary fw-bold">⬅ Quay lại</a>
  </div>
</div>

<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
