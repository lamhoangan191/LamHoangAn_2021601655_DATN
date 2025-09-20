<?php
include '../CSDL/db.php';
session_start();

// Xác định năm được chọn
$year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");

// Lấy danh sách năm có dữ liệu
$years = [];
$resY = $conn->query("SELECT DISTINCT YEAR(ngaycuuho) as y FROM thucung_coso 
                      UNION SELECT DISTINCT YEAR(ngayduyet) FROM donnhannuoi 
                      UNION SELECT DISTINCT YEAR(ngaygui) FROM donchamsoc 
                      ORDER BY y DESC");
while($row = $resY->fetch_assoc()) { 
    if (!empty($row['y'])) {   // bỏ năm rỗng/null
        $years[] = $row['y']; 
    }
}

// Lấy số thú cưng cứu hộ theo tháng
$sql1 = "SELECT MONTH(ngaycuuho) AS thang, COUNT(*) AS soluong
         FROM thucung_coso
         WHERE YEAR(ngaycuuho) = $year
         GROUP BY thang";
$res1 = $conn->query($sql1);
$cuuho = array_fill(1, 12, 0);
while($row = $res1->fetch_assoc()) {
    $cuuho[intval($row['thang'])] = intval($row['soluong']);
}

// Lấy số thú cưng được nhận nuôi theo tháng
$sql2 = "SELECT MONTH(ngayduyet) AS thang, COUNT(*) AS soluong
         FROM donnhannuoi
         WHERE trangthai='đã duyệt' AND YEAR(ngayduyet) = $year
         GROUP BY thang";
$res2 = $conn->query($sql2);
$nhannuoi = array_fill(1, 12, 0);
while($row = $res2->fetch_assoc()) {
    $nhannuoi[intval($row['thang'])] = intval($row['soluong']);
}

// Lấy số đơn chăm sóc theo tháng
$sql3 = "SELECT MONTH(ngaygui) AS thang, COUNT(*) AS soluong
         FROM donchamsoc
         WHERE YEAR(ngaygui) = $year
         GROUP BY thang";
$res3 = $conn->query($sql3);
$chamsoc = array_fill(1, 12, 0);
while($row = $res3->fetch_assoc()) {
    $chamsoc[intval($row['thang'])] = intval($row['soluong']);
}

// Xuất dữ liệu JSON cho JS
$data = [
    "cuuho" => array_values($cuuho),
    "nhannuoi" => array_values($nhannuoi),
    "chamsoc" => array_values($chamsoc)
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thống kê</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
<!-- Header -->
<?php include("navbar_admin.php") ?>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><i class="fa-solid fa-chart-column"></i>
        Thống kê trong năm <?= $year ?>
      </h2>
    <form method="get">
      <select name="year" class="form-select border-secondary" onchange="this.form.submit()">
        <?php foreach($years as $y): ?>
          <option value="<?= $y ?>" <?= ($y==$year?'selected':'') ?>><?= $y ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <canvas id="bieudo" height="120"></canvas>
</div>

<script>
  const data = <?php echo json_encode($data); ?>;
  const ctx = document.getElementById('bieudo').getContext('2d');

  new Chart(ctx, {
    type: 'bar', // có thể đổi 'line', 'bar', 'pie'
    data: {
      labels: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
      datasets: [
        {
          label: 'Cứu hộ',
          data: data.cuuho,
          borderColor: 'blue',
          backgroundColor: 'blue',
          pointBackgroundColor: 'blue',
          pointRadius: 5,
          fill: false,
          tension: 0.3
        },
        {
          label: 'Nhận nuôi',
          data: data.nhannuoi,
          borderColor: 'red',
          backgroundColor: 'red',
          pointBackgroundColor: 'red',
          pointRadius: 5,
          fill: false,
          tension: 0.3
        },
        {
          label: 'Đơn chăm sóc',
          data: data.chamsoc,
          borderColor: 'green',
          backgroundColor: 'green',
          pointBackgroundColor: 'green',
          pointRadius: 5,
          fill: false,
          tension: 0.3
        }
      ]
    },
    options: {
  responsive: true,
  plugins: {
    legend: { 
      position: 'top',
      labels: {
        font: { weight: 'bold', size: 14 },
        color: '#000'
      }
    },
    title: { 
      display: true, 
      text: 'Thống kê số lượng theo tháng',
      font: { size: 18, weight: 'bold' },
      color: '#000'
    }
  },
  scales: {
    x: {
      ticks: { 
        color: '#000',
        font: { weight: 'bold', size: 13 }
      },
      grid: {
        color: 'rgba(0,0,0,0.3)',
        lineWidth: 1.5
      },
      border: { color: '#000', width: 2 }
    },
    y: {
      ticks: { 
        stepSize: 1, 
        precision: 0,
        color: '#000',
        font: { weight: 'bold', size: 13 }
      },
      grid: {
        color: 'rgba(0,0,0,0.3)',
        lineWidth: 1.5
      },
      border: { color: '#000', width: 2 },
      beginAtZero: true
    }
  }
}

  });
</script>
<!-- Footer -->
<?php include ("footer.php") ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
