<?php
include("../CSDL/db.php");

// --- Lấy dữ liệu option cho select ---
$species = $conn->query("SELECT DISTINCT tenloai FROM loaithucung_coso");
$ages    = $conn->query("SELECT DISTINCT tuoi FROM thucung_coso ORDER BY tuoi");
$genders = $conn->query("SELECT DISTINCT gioitinh FROM thucung_coso");
$colors  = $conn->query("SELECT DISTINCT mausac FROM thucung_coso");

// --- Xử lý lọc ---
$where = [];
if (!empty($_GET['tenloai']) && $_GET['tenloai'] !== 'all') {
    $loai = $conn->real_escape_string($_GET['tenloai']);
    $where[] = "l.tenloai = '$loai'";
}
if (!empty($_GET['tuoi']) && $_GET['tuoi'] !== 'all') {
    $tuoi = $conn->real_escape_string($_GET['tuoi']);
    $where[] = "t.tuoi = '$tuoi'";
}
if (!empty($_GET['gioitinh']) && $_GET['gioitinh'] !== 'all') {
    $gioitinh = $conn->real_escape_string($_GET['gioitinh']);
    $where[] = "t.gioitinh = '$gioitinh'";
}
if (!empty($_GET['mausac']) && $_GET['mausac'] !== 'all') {
    $mausac = $conn->real_escape_string($_GET['mausac']);
    $where[] = "t.mausac = '$mausac'";
}
if (!empty($_GET['tentc'])) {
    $tentc = $conn->real_escape_string($_GET['tentc']);
    $where[] = "t.tentc LIKE '%$tentc%'";
}

$sql_base = "SELECT t.*, l.tenloai 
             FROM thucung_coso t 
             JOIN loaithucung_coso l ON t.loaics_id = l.loaics_id 
             WHERE NOT EXISTS (
             SELECT 1 FROM donnhannuoi d
             WHERE d.petcs_id = t.petcs_id
             )";

if (count($where) > 0) {
    $sql_base .= " AND " . implode(" AND ", $where);
}
?>

<body class="bg-light">

<div class="container mt-4">
  <!-- Form tìm kiếm -->
  <form method="GET" action="#pet-list" class="mb-4">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-bold">Giống</label>
        <select name="tenloai" class="form-select border-2 border-success">
          <option value="all">Tất cả</option>
          <?php while($row = $species->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['tenloai']) ?>"
              <?= (isset($_GET['tenloai']) && $_GET['tenloai'] == $row['tenloai']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['tenloai']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-bold">Tuổi</label>
        <select name="tuoi" class="form-select border-2 border-success">
          <option value="all">Tất cả</option>
          <?php while($row = $ages->fetch_assoc()): ?>
            <option value="<?= $row['tuoi'] ?>"
              <?= (isset($_GET['tuoi']) && $_GET['tuoi'] == $row['tuoi']) ? 'selected' : '' ?>>
              <?= $row['tuoi'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-bold">Giới tính</label>
        <select name="gioitinh" class="form-select border-2 border-success">
          <option value="all">Tất cả</option>
          <?php while($row = $genders->fetch_assoc()): ?>
            <option value="<?= $row['gioitinh'] ?>"
              <?= (isset($_GET['gioitinh']) && $_GET['gioitinh'] == $row['gioitinh']) ? 'selected' : '' ?>>
              <?= $row['gioitinh'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-bold">Màu sắc</label>
        <select name="mausac" class="form-select border-2 border-success">
          <option value="all">Tất cả</option>
          <?php while($row = $colors->fetch_assoc()): ?>
            <option value="<?= $row['mausac'] ?>"
              <?= (isset($_GET['mausac']) && $_GET['mausac'] == $row['mausac']) ? 'selected' : '' ?>>
              <?= $row['mausac'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-bold">Tên thú cưng</label>
        <input type="text" name="tentc" class="form-control border-2 border-success" 
               value="<?= isset($_GET['tentc']) ? htmlspecialchars($_GET['tentc']) : '' ?>">
      </div>

      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
      </div>
    </div>
  </form>

  <!-- Hiển thị thú cưng -->
  <?php include("hienthithucung_kh.php"); ?>
</div>
</body>
</html>
