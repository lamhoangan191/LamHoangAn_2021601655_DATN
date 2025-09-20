<?php
// Yêu cầu: trước khi include file này, phải có biến $sql_base (SELECT từ thucung_coso JOIN loaithucung_coso ...)

include("../CSDL/db.php");

// --- Phân trang ---
$limit = 8; // số thú cưng mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- Đếm tổng số bản ghi ---
$sql_count = "SELECT COUNT(*) as total FROM (" . $sql_base . ") AS tmp";
$total_result = $conn->query($sql_count);
$total = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// --- Lấy thú cưng phân trang (THÊM ORDER BY) ---
$sql = $sql_base . " ORDER BY t.petcs_id ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<div id="pet-list" class="row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($pet = $result->fetch_assoc()): ?>
      <div class="col-md-3 mb-4">
        <div class="card-pet card h-100 bg-light border-2 border-dark">
            <?php
            // Giữ lại toàn bộ GET hiện tại (filter, page,...)
            $query_params = $_GET;
            // Thêm id của thú cưng hiện tại vào query string
            $query_params['id'] = $pet['petcs_id'];
            // Tạo link đến suathucung.php với đủ tham số
            $link = "suathucung.php?" . http_build_query($query_params);
            ?>
          <a href="<?= $link ?>" class="text-decoration-none">
            <?php if (!empty($pet['anh']) && file_exists("../" . $pet['anh'])): ?>
            <img src="../<?php echo $pet['anh']; ?>" class="card-img-top" alt="Ảnh thú cưng">
            <?php else: ?>
            <div class="d-flex align-items-center justify-content-center bg-light" style="height:255px; border:1px solid #ddd;">
              <span class="text-muted">No Image</span>
            </div>
            <?php endif; ?>
          </a>
          <div class="card-body">
            <h5 class="card-title">
              <a href="<?= $link ?>" class="text-decoration-none"><?php echo $pet['tentc']; ?></a>
            </h5>
            <hr>
            <p class="card-text"><b>Tuổi: </b><?php echo $pet['tuoi']; ?></p>
            <p class="card-text"><b>Giới tính: </b><?php echo $pet['gioitinh']; ?></p>
            <p class="card-text"><b>Tình trạng: </b><?php echo $pet['tinhtrang']; ?></p>
          </div>
            <div class="card-footer text-center">
            <a href="<?= $link ?>" class="btn btn-warning btn-sm fw-bold">Sửa</a>
            <a href="xoathucung.php?id=<?= $pet['petcs_id'] ?>" onclick="return confirm('Bạn chắc chắn muốn xoá?')" class="btn btn-danger btn-sm fw-bold">Xóa</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center">Không có thú cưng nào.</p>
  <?php endif; ?>
</div>

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
  <nav>
    <ul class="pagination justify-content-center mt-4">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link fw-bold border-dark" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>#pet-list">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
<?php endif; ?>
