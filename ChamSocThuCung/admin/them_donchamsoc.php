<?php
include '../CSDL/db.php';
session_start();

// Xử lý thêm đơn chăm sóc
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_don'])) {
    $kh_id   = intval($_POST['kh_id']);
    $admin_id = $_SESSION['user_id'];
    $petcs_id  = !empty($_POST['petcs_id']) ? intval($_POST['petcs_id']) : null;
    $petncs_id = !empty($_POST['petncs_id']) ? intval($_POST['petncs_id']) : null;
    $ngaygui = date('Y-m-d H:i:s');
    $trangthai = "Đang chăm sóc";
    
    if ($petcs_id == null) {
        $tentc    = $_POST['tentc'];
        $tenloai  = trim($_POST['tenloai']);
        $tuoi     = $_POST['tuoi'];
        $gioitinh = $_POST['gioitinh'];
        $mausac   = $_POST['mausac'];
        $cannang  = floatval($_POST['cannang']);
        $thongtin = $_POST['thongtin'];
        $anh      = null;

        // --- xử lý species (loài) ---
        $loaincs_id = null;
        $check_sql = "SELECT loaincs_id FROM loaithucung_ngoaicoso WHERE tenloai = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $tenloai);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $loaincs_id = $res->fetch_assoc()['loaincs_id'];
        } else {
            $insert_sql = "INSERT INTO loaithucung_ngoaicoso (tenloai) VALUES (?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("s", $tenloai);
            if ($stmt->execute()) {
                $loaincs_id = $stmt->insert_id;
            }
        }

        // --- xử lý upload ảnh ---
        if (isset($_FILES['anh']) && $_FILES['anh']['error'] == 0) {
            $target_dir = "../images/NCS/";  
            $filename = basename($_FILES["anh"]["name"]);

            // Nếu admin chọn loài thì tự tạo thư mục cats/ hoặc dogs/
            if (stripos($tenloai, "chó") !== false) {
                $target_dir .= "dogs/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $anh = "images/NCS/dogs/" . $filename;
            } elseif (stripos($tenloai, "mèo") !== false) {
                $target_dir .= "cats/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $anh = "images/NCS/cats/" . $filename;
            } else {
                $anh = "images/" . $filename;
            }

            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES["anh"]["tmp_name"], $target_file)) {
                // $anh đã có đường dẫn đúng để lưu DB
            }
        }
        // insert thú cưng ngoài cơ sở
        $sql = "INSERT INTO thucung_ngoaicoso (tentc, loaincs_id, tuoi, gioitinh, mausac, cannang, anh, thongtin) 
                VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssdss", $tentc, $loaincs_id, $tuoi, $gioitinh, $mausac, $cannang, $anh, $thongtin);
        if ($stmt->execute()) {
            $petncs_id = $stmt->insert_id;
        }
    }
    
    // Insert vào bảng donchamsoc (1 trong 2 FK, cái còn lại để null)
    $sql = "INSERT INTO donchamsoc (kh_id, admin_id, petcs_id, petncs_id, ngaygui, trangthai)
            VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiss", $kh_id, $admin_id, $petcs_id, $petncs_id, $ngaygui, $trangthai);
    $stmt->execute();
    $doncs_id = $stmt->insert_id;
    
    // Insert các tình trạng chăm sóc
    if (!empty($_POST['tinhtrang'])) {
        foreach ($_POST['tinhtrang'] as $i => $tinhtrang) {
            if (!empty(trim($tinhtrang))) {
                $tt = $conn->real_escape_string($tinhtrang);
                $trangthai_tt = $conn->real_escape_string($_POST['trangthai'][$i]);
                $cp = isset($_POST['chiphi'][$i]) ? floatval($_POST['chiphi'][$i]) : 0.00;;
                $gc = $conn->real_escape_string($_POST['ghichu'][$i]);
                $sql2 = "INSERT INTO tinhtrangpet (doncs_id, tinhtrang, trangthai, chiphi, ghichu) VALUES (?,?,?,?,?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("issds", $doncs_id, $tt, $trangthai_tt, $cp, $gc);
                $stmt2->execute();
            }
        }
    }

    header("Location: donchamsoc_admin.php?tab=dangchamsoc");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm đơn chăm sóc</title>
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
        <h3><i class="fa-solid fa-hand-holding-medical"></i> <b>Thêm đơn chăm sóc</b></h3>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <!-- Chia trên -->
        <div class="row mb-4">
          <!-- Trái -->
          <div class="col-md-6">
              <div class="d-flex gap-2 w-100">
                <button type="button" class="btn btn-primary mb-2 flex-fill fw-bold" onclick="chonCoSo()">Thú cưng của cơ sở</button>
                <button type="button" class="btn btn-secondary mb-2 flex-fill fw-bold" onclick="chonNgoaiCoSo()">Thú cưng ngoài cơ sở</button>
              </div>
              <div id="chon_pet"></div>
          </div>

          <!-- Phải -->
          <div class="col-md-6">
              <div class="card mb-3">
                <div class="card-body ">
                    <b>Ngày gửi: </b><?php echo date('d - m - Y'); ?>
                </div>
              </div>
              <div class="mb-3">
                <b>Tìm khách hàng bằng SĐT:</b>
                <div class="input-group">
                  <input type="text" id="sdt_search" class="form-control border-secondary border-end-0" placeholder="Nhập số điện thoại">
                  <button type="button" class="btn btn-outline-primary fw-bold" onclick="timKH()">Tìm kiếm</button>
                </div>
                <div id="info_kh" class="mt-2"></div>
              </div>
          </div>
        </div>

        <input type="hidden" name="kh_id" id="kh_id">
        <input type="hidden" name="petcs_id" id="petcs_id">
        <input type="hidden" name="petncs_id" id="petncs_id">

        <!-- Dưới: Bảng chăm sóc -->
        <div class="row">
          <!-- Bảng tình trạng -->
          <div class="col-md-9">
            <table class="table table-bordered border-secondary" id="bang_tt">
              <tr class="table-secondary border-secondary">
                <th>Dịch vụ chăm sóc/Khám chữa bệnh</th>
                <th style="width:20%">Trạng thái</th>
                <th style="width:15%">Chi phí</th>
                <th style="width:30%">Ghi chú/Phản hồi</th>
                <th>Xóa</th>
              </tr>
              <tr>
                <td><input type="text" name="tinhtrang[]" class="form-control border-secondary"></td>
                <td>
                    <select name="trangthai[]" class="form-select border-secondary">
                      <option value="Chưa xử lý">Chưa xử lý</option>
                      <option value="Đang xử lý">Đang xử lý</option>
                      <option value="Đã xử lý">Đã xử lý</option>
                    </select>
                </td>
                <td><input type="number" name="chiphi[]" class="form-control border-secondary"></td>
                <td><textarea name="ghichu[]" class="form-control border-secondary" rows="1"></textarea></td>
                <td><button type="button" class="btn btn-danger" onclick="xoaRow(this)">Xóa</button></td>
              </tr>
            </table>
            <button type="button" class="btn btn-outline-success fw-bold" onclick="themRow()">Thêm hàng mới</button>
          </div>

          <!-- Nút thêm -->
          <div class="col-md-3 d-flex align-items-start">
            <button type="submit" name="them_don" class="btn btn-success w-100 fw-bold">Thêm</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Nút quay lại -->
<div class="text-center mt-3">
  <a href="donchamsoc_admin.php?tab=dangchamsoc" class="btn btn-secondary fw-bold">⬅ Quay lại</a>
</div>
<script>
function themRow() {
  let table = document.getElementById("bang_tt");
  let row = table.insertRow(-1);
  row.innerHTML = `
    <td><input type="text" name="tinhtrang[]" class="form-control border-secondary"></td>
    <td>
        <select name="trangthai[]" class="form-select border-secondary">
            <option value="Chưa xử lý">Chưa xử lý</option>
            <option value="Đang xử lý">Đang xử lý</option>
            <option value="Đã xử lý">Đã xử lý</option>
        </select>
    </td>
    <td><input type="number" name="chiphi[]" class="form-control border-secondary"></td>
    <td><textarea type="text" name="ghichu[]" class="form-control border-secondary" rows="1"></textarea></td>
    <td><button type="button" class="btn btn-danger" onclick="xoaRow(this)">Xóa</button></td>
  `;
}
function xoaRow(btn) {
  btn.closest("tr").remove();
}
function timKH() {
  let sdt = document.getElementById("sdt_search").value;
  if (!sdt) return alert("Nhập SĐT trước!");
  fetch("timkiem_infokh.php?sdt="+sdt)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
          let infoHTML =
            `<div class="card mb-3 border-secondary bg-light">
                <div class="card-body row">
                    <h5>Thông tin khách hàng</h5><hr>
                    <div class="mb-3"><b>Họ tên:</b> ${data.hoten}</div>
                    <div class="mb-3"><b>Tuổi:</b> ${data.tuoi}</div>
                    <div class="mb-3"><b>Giới tính:</b> ${data.gioitinh}</div>
                    <div class="mb-3"><b>SĐT:</b> ${data.sdt}</div>
                    <div class="mb-3"><b>Email:</b> ${data.email}</div>
                    <div class="mb-3"><b>Địa chỉ:</b> ${data.diachi}</div>
                </div>
            </div>`;
        document.getElementById("info_kh").innerHTML = infoHTML;
        document.getElementById("kh_id").value = data.user_id;
      } else {
        document.getElementById("info_kh").innerHTML = "Không tìm thấy khách hàng!";
      }
    });
}
function chonCoSo() {
  let kh_id = document.getElementById("kh_id").value;
  if (!kh_id) {
    alert("Bạn phải tìm và chọn khách hàng trước!");
    return;
  }
  fetch("chonpet_coso.php?kh_id="+kh_id)
    .then(res => res.text())
    .then(html => {
      document.getElementById("chon_pet").innerHTML = html;
    });
}
function chonPetCoSo(pet_id, tentc, anh, tenloai, tuoi, gioitinh, mausac, cannang, tinhtrang, ngaycuuho, thongtin, ghichu) {
  document.getElementById("petcs_id").value = pet_id;
  document.getElementById("petncs_id").value = "";

  // Định dạng ngày nếu có
  let ngaycuuho_formatted = ngaycuuho ? new Date(ngaycuuho).toLocaleDateString("vi-VN") : "Chưa có";

  let infoHTML =
    `<div class="card mb-3 border-secondary bg-light">
        <div class="card-body row">
        <div class="col-md-6">
          <img src="../${anh}" class="img-fluid rounded mb-2" alt="pet">
        </div>
        <div class="col-md-6">
            <h2 class="mb-3">${tentc}</h2>
            <hr>
            <p class="mb-2"><b>Giống: </b>${tenloai}</p>
            <p class="mb-2"><b>Tuổi: </b>${tuoi}</p>
            <p class="mb-2"><b>Giới tính: </b>${gioitinh}</p>
            <p class="mb-2"><b>Màu sắc: </b>${mausac}</p>
            <p class="mb-2"><b>Cân nặng: </b>${cannang} kg</p>
            <p class="mb-2"><b>Tình trạng: </b>${tinhtrang}</p>
            <p class="mb-2"><b>Ngày cứu hộ: </b>${ngaycuuho_formatted}</p>
        </div>
        <div class="card-body border-top">
            <p class="mb-1"><b>Thông tin thêm: </b>${thongtin}</p>
            <p class="mb-0"><b>Tìm hiểu: </b>${ghichu}</p>
        </div>
      </div>
    </div>`;
  document.getElementById("chon_pet").innerHTML = infoHTML;
}
function chonNgoaiCoSo() {
     // Reset petcs_id khi chuyển sang pet ngoài cơ sở
  document.getElementById("petcs_id").value = "";
  fetch("nhappet_ngoaicoso.php")
    .then(res => res.text())
    .then(html => {
      document.getElementById("chon_pet").innerHTML = html;
    });
}
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('preview');
  if (input.files && input.files[0]) {
    preview.src = URL.createObjectURL(input.files[0]);
  }
}
function setPetNgoaiCoSo(pet_id, tentc) {
  document.getElementById("petncs_id").value = pet_id;
  document.getElementById("petcs_id").value = ""; // reset lại pet cơ sở
  document.getElementById("chon_pet").innerHTML = `<div class="alert alert-success">Đã thêm mới thú cưng ngoài cơ sở: <strong>${tentc}</strong></div>`;
}
</script>
<?php include ("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
