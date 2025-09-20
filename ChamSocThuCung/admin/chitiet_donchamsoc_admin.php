<?php
include '../CSDL/db.php';
session_start();

$doncs_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin đơn
$sql = "SELECT d.*,
            tc.tentc  AS tentc_coso, tc.anh AS anh_coso, tc.tuoi AS tuoi_coso, tc.gioitinh AS gioitinh_coso,
            tc.mausac AS mausac_coso, tc.cannang AS cannang_coso, tc.tinhtrang AS tinhtrang_coso,
            tc.ngaycuuho, tc.thongtin AS thongtin_coso, tc.ghichu,
            lcs.tenloai AS tenloai_coso,
            tn.tentc  AS tentc_ncs,  tn.anh AS anh_ncs,  tn.tuoi AS tuoi_ncs, tn.gioitinh AS gioitinh_ncs,
            tn.mausac AS mausac_ncs, tn.cannang AS cannang_ncs, tn.thongtin AS thongtin_ncs,
            lncs.tenloai AS tenloai_ncs,
            kh.hoten  AS ten_kh, kh.tuoi AS tuoi_kh, kh.gioitinh AS gioitinh_kh, kh.sdt, kh.email, kh.diachi
        FROM donchamsoc d
        LEFT JOIN thucung_coso tc ON d.petcs_id = tc.petcs_id
        LEFT JOIN loaithucung_coso lcs ON tc.loaics_id = lcs.loaics_id
        LEFT JOIN thucung_ngoaicoso tn ON d.petncs_id = tn.petncs_id
        LEFT JOIN loaithucung_ngoaicoso lncs ON tn.loaincs_id = lncs.loaincs_id
        JOIN taikhoan kh ON d.kh_id = kh.user_id
        WHERE d.doncs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doncs_id);
$stmt->execute();
$don = $stmt->get_result()->fetch_assoc();

if (!$don) { echo "Không tìm thấy đơn chăm sóc."; exit; }

// ====== XỬ LÝ POST ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cập nhật/Thêm các dòng tình trạng
    if (isset($_POST['capnhat'])) {
        if (isset($_POST['tinhtrang'])) {
            $arr_tinhtrang = $_POST['tinhtrang'];
            $arr_trangthai = $_POST['trangthai'] ?? [];
            $arr_chiphi = $_POST['chiphi'] ?? [];
            $arr_ghichu = $_POST['ghichu'] ?? [];
            $arr_id        = $_POST['tt_id'] ?? [];

            foreach ($arr_tinhtrang as $i => $tt_txt) {
                $tt_txt = trim($tt_txt);
                $st_txt = isset($arr_trangthai[$i]) ? trim($arr_trangthai[$i]) : '';
                $cp_txt = isset($arr_chiphi[$i]) ? trim($arr_chiphi[$i]) : '';
                $gc_txt = isset($arr_ghichu[$i]) ? trim($arr_ghichu[$i]) : '';
                $id_val = isset($arr_id[$i]) ? trim($arr_id[$i]) : '';

                // Có id -> UPDATE; không id -> INSERT nếu có nhập gì đó
                if ($id_val !== '') {
                    $sql_up = "UPDATE tinhtrangpet SET tinhtrang=?, trangthai=?, chiphi=?, ghichu=? WHERE ttpet_id=?";
                    $stmt_up = $conn->prepare($sql_up);
                    $stmt_up->bind_param("ssdsi", $tt_txt, $st_txt, $cp_txt, $gc_txt, $id_val);
                    $stmt_up->execute();
                } else {
                    if ($tt_txt !== '' || $st_txt !== '') {
                        $sql_ins = "INSERT INTO tinhtrangpet (doncs_id, tinhtrang, trangthai, chiphi, ghichu) VALUES (?,?,?,?,?)";
                        $stmt_ins = $conn->prepare($sql_ins);
                        $stmt_ins->bind_param("issds", $doncs_id, $tt_txt, $st_txt, $cp_txt, $gc_txt);
                        $stmt_ins->execute();
                    }
                }
            }
        }
        $_SESSION['success'] = "✅ Cập nhật thành công!";
        header("Location: chitiet_donchamsoc_admin.php?id=".$doncs_id);
        exit;
    }

    // Hoàn tất đơn (có check server-side)
    if (isset($_POST['hoantat'])) {
        $sql_ck = "SELECT 
                     COUNT(*) AS total,
                     SUM(CASE WHEN trangthai != 'Đã xử lý' THEN 1 ELSE 0 END) AS not_done_count
                   FROM tinhtrangpet WHERE doncs_id=?";
        $stm_ck = $conn->prepare($sql_ck);
        $stm_ck->bind_param("i", $doncs_id);
        $stm_ck->execute();
        $ck = $stm_ck->get_result()->fetch_assoc();

        if ($ck && intval($ck['not_done_count']) === 0 && intval($ck['total']) > 0) {
            $sql_done = "UPDATE donchamsoc SET trangthai='Hoàn thành', ngayhoanthanh=NOW() WHERE doncs_id=?";
            $stm_done = $conn->prepare($sql_done);
            $stm_done->bind_param("i", $doncs_id);
            $stm_done->execute();
            header("Location: chitiet_donchamsoc_admin.php?id=$doncs_id&tab=hoanthanh");
            exit;
        } else {
            // Không cho hoàn tất nếu còn trống
            $_SESSION['err'] = "Vui lòng điền đầy đủ TRẠNG THÁI là 'Đã xử lý' và ấn Cập nhật trước khi hoàn tất.";
            header("Location: chitiet_donchamsoc_admin.php?id=".$doncs_id);
            exit;
        }
    }
}

// ====== LẤY DỮ LIỆU BẢNG TÌNH TRẠNG + CHECK CHO NÚT HOÀN TẤT ======
$sql_tt = "SELECT * FROM tinhtrangpet WHERE doncs_id=? ORDER BY ttpet_id ASC";
$stmt_tt = $conn->prepare($sql_tt);
$stmt_tt->bind_param("i", $doncs_id);
$stmt_tt->execute();
$rs_tt = $stmt_tt->get_result();

// Tính tổng chi phí
$total_cost = 0;
$sql_sum = "SELECT SUM(chiphi) AS tong FROM tinhtrangpet WHERE doncs_id=?";
$stmt_sum = $conn->prepare($sql_sum);
$stmt_sum->bind_param("i", $doncs_id);
$stmt_sum->execute();
$res_sum = $stmt_sum->get_result()->fetch_assoc();
$total_cost = $res_sum['tong'] ?? 0;

// check để disable/enable nút Hoàn tất (dựa DB -> buộc phải ấn Cập nhật mới bật)
$can_finish = false;
if ($don['trangthai'] === 'Đang chăm sóc') {
    $sql_ck = "SELECT 
                 COUNT(*) AS total,
                 SUM(CASE WHEN trangthai != 'Đã xử lý' THEN 1 ELSE 0 END) AS not_done_count
               FROM tinhtrangpet WHERE doncs_id=?";
    $stm_ck = $conn->prepare($sql_ck);
    $stm_ck->bind_param("i", $doncs_id);
    $stm_ck->execute();
    $ck = $stm_ck->get_result()->fetch_assoc();
    $can_finish = ($ck && intval($ck['total']) > 0 && intval($ck['not_done_count']) === 0);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi tiết đơn chăm sóc (Admin)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <style>
    .care-panel{
      background-color: mintcream;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<?php include("navbar_admin.php"); ?>
<div class="container mt-4">
  <div class="care-panel card shadow-sm border-2 border-success">
    <div class="card-header bg-success bg-gradient text-white text-center">
        <h3><i class="fa-solid fa-hand-holding-medical"></i> <b>Chi tiết đơn chăm sóc</b></h3>
    </div>
      <?php if(!empty($_SESSION['err'])): ?>
    <div class="alert alert-warning"><?php echo $_SESSION['err']; unset($_SESSION['err']); ?></div>
  <?php endif; ?>
  <?php if(!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
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

        <!-- Phải: Thông tin khách hàng -->
        <div class="col-md-6">
          <h4 class="text-center mb-3">Thông tin khách hàng</h4>
          <hr>
          <p class="mb-1"><b>Họ tên:</b> <?= $don['ten_kh'] ?></p>
          <p class="mb-1"><b>Tuổi:</b> <?= $don['tuoi_kh'] ?></p>
          <p class="mb-1"><b>Giới tính:</b> <?= $don['gioitinh_kh'] ?></p>
          <p class="mb-1"><b>SĐT:</b> <?= $don['sdt'] ?></p>
          <p class="mb-1"><b>Email:</b> <?= $don['email'] ?></p>
          <p class="mb-4"><b>Địa chỉ:</b> <?= $don['diachi'] ?></p>
          <br><br><hr>
          <p class="mb-1"><b>Ngày gửi:</b> <?= date("d-m-Y", strtotime($don['ngaygui'])) ?></p>
        </div>
      </div>

      <!-- Bảng tình trạng -->
      <hr>
        <!-- Bảng tình trạng + Cụm nút bên phải -->
        <form method="post">
          <div class="row">
            <!-- Bảng (to) -->
            <div class="col-md-9">
              <table class="table table-bordered border-secondary">
                <tr class="table-secondary border-secondary">
                  <th>Dịch vụ chăm sóc/Khám chữa bệnh</th>
                  <th style="width:20%">Trạng thái</th>
                  <th style="width:15%">Chi phí</th>
                  <th style="width:30%">Ghi chú/Phản hồi</th>
                  <?php if($don['trangthai']=="Đang chăm sóc"): ?><th>Xóa</th><?php endif; ?>
                </tr>
                <?php while($row = $rs_tt->fetch_assoc()): ?>
                  <tr>
                    <td>
                      <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                        <input type="hidden" name="tt_id[]" class=" border-secondary" value="<?= $row['ttpet_id'] ?>">
                        <input type="text" name="tinhtrang[]" class="form-control border-secondary" value="<?= htmlspecialchars($row['tinhtrang']) ?>">
                      <?php else: ?>
                        <?= htmlspecialchars($row['tinhtrang']) ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                        <select name="trangthai[]" class="form-select border-secondary">
                          <option value="Chưa xử lý" <?= ($row['trangthai'] == 'Chưa xử lý') ? 'selected' : '' ?>>Chưa xử lý</option>
                          <option value="Đang xử lý" <?= ($row['trangthai'] == 'Đang xử lý') ? 'selected' : '' ?>>Đang xử lý</option>
                          <option value="Đã xử lý" <?= ($row['trangthai'] == 'Đã xử lý') ? 'selected' : '' ?>>Đã xử lý</option>
                        </select>
                      <?php else: ?>
                        <?= htmlspecialchars($row['trangthai']) ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                        <input type="number" name="chiphi[]" class="form-control border-secondary" value="<?= htmlspecialchars($row['chiphi']) ?>">
                      <?php else: ?>
                        <?= number_format($row['chiphi'], 0, ',', ',') ?> đ
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                        <textarea name="ghichu[]" class="form-control border-secondary" rows="1"><?= htmlspecialchars($row['ghichu']) ?></textarea>
                      <?php else: ?>
                        <div class="text-break"><?= htmlspecialchars($row['ghichu']) ?></div>
                      <?php endif; ?>
                    </td>
                    <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                      <td>
                        <a class="btn btn-danger btn-sm"
                           onclick="return confirm('Xóa tình trạng này?')"
                           href="xoa_tinhtrangpet.php?id=<?= $row['ttpet_id'] ?>&don=<?= $doncs_id ?>">Xóa</a>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endwhile; ?>
                  <?php if($total_cost > 0): ?>
                  <tr class="table-warning border-secondary" id="row-tongchiphi">
                    <td colspan="2" class="text-end fw-bold">Tổng chi phí:</td>
                    <td colspan="3" class="fw-bold text-danger"><?= number_format($total_cost, 0, ',', ',') ?> đ</td>
                  </tr>
                  <?php endif; ?>
              </table>

              <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                <button type="button" class="btn btn-outline-success fw-bold" onclick="themHang()">Thêm hàng mới</button>
              <?php endif; ?>
            </div>

            <!-- Cụm nút (nhỏ, bên phải) -->
            <div class="col-md-3 d-flex flex-column gap-2">
              <?php if($don['trangthai']=="Đang chăm sóc"): ?>
                <div class="d-flex flex-row gap-2">
                <button type="submit" name="capnhat" class="btn btn-warning flex-fill fw-bold">Cập nhật</button>
                <a href="xoa_donchamsoc.php?id=<?= $doncs_id ?>"
                   onclick="return confirm('Xóa đơn chăm sóc này?')"
                   class="btn btn-danger flex-fill fw-bold">Xóa đơn</a>
                </div>
                <button type="submit" name="hoantat"
                        class="btn btn-success mt-2 fw-bold"
                        <?= $can_finish ? '' : 'disabled style="opacity:.65;cursor:not-allowed;"' ?>>
                  Hoàn tất đơn. Bàn giao lại thú cưng cho khách hàng
                </button>
                <?php if(!$can_finish): ?>
                  <small class="text-muted">Điền đầy đủ <b>Trạng thái</b> cho mọi dòng là 'Đã xử lý' và ấn <b>Cập nhật</b> để bật nút này.</small>
                <?php endif; ?>
              <?php else: ?>
                <div class="alert bg-success text-white text-center fw-bold">
                    <i class="fa-solid fa-circle-check"></i> Đã hoàn thành chăm sóc
                </div>
              <?php endif; ?>
            </div>
          </div>
        </form>
    </div>
  </div>

  <!-- Quay lại -->
  <div class="text-center mt-3">
    <a href="donchamsoc_admin.php?tab=<?= $don['trangthai']=='Đang chăm sóc'?'dangchamsoc':'hoanthanh' ?>" 
       class="btn btn-secondary fw-bold">⬅ Quay lại</a>
  </div>
</div>


<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Thêm 1 hàng mới (chưa có tt_id -> khi Cập nhật sẽ INSERT)
function themHang() {
  const table = document.querySelector(".table");

  // Xác định hàng tổng chi phí
  const tongRow = document.querySelector("#row-tongchiphi");

  let row;
  if (tongRow) {
    // Nếu có hàng tổng -> chèn TRƯỚC nó
    row = table.insertRow(tongRow.rowIndex);
  } else {
    // Nếu chưa có hàng tổng -> chèn cuối
    row = table.insertRow(-1);
  }

  const c1 = row.insertCell(0);
  const c2 = row.insertCell(1);
  const c3 = row.insertCell(2);
  const c4 = row.insertCell(3);
  const c5 = row.insertCell(4);

  c1.innerHTML = '<input type="hidden" name="tt_id[]" value="">' +
                 '<input type="text" name="tinhtrang[]" class="form-control border-secondary">';
  c2.innerHTML = `<select name="trangthai[]" class="form-select border-secondary">
                      <option value="Chưa xử lý">Chưa xử lý</option>
                      <option value="Đang xử lý">Đang xử lý</option>
                      <option value="Đã xử lý">Đã xử lý</option>
                  </select>`;
  c3.innerHTML = '<input type="number" name="chiphi[]" class="form-control border-secondary">';
  c4.innerHTML = '<textarea name="ghichu[]" class="form-control border-secondary" rows="1"></textarea>';
  c5.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove()">Xóa</button>';
}

</script>
</body>
</html>