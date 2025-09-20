<?php
include '../CSDL/db.php';
session_start();
?>

<style>
    .image-container {
        position:relative;
        width:100%;
        height:200px;
        background:#f0f0f0;
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        border-radius:5px;
    }
    .image-container img {
        width:100%;
        height:100%;
        object-fit:cover;
        transition: 0.3s ease;
        border-radius:5px;
    }
    .overlay {
        position:absolute;
        top:0;  left:0;
        width:100%;height:100%;
        background:rgba(0,0,0,0.4);
        display:flex;
        align-items:center;
        justify-content:center;
        opacity: 0;
        transition: 0.3s ease;
        border-radius:5px;
    }
    .image-container:hover .overlay {
      opacity: 1;
    }
</style>
<!-- Form nhập pet ngoài cơ sở -->
<div class="p-3 border rounded border-secondary bg-light">
  <h5 class="text-center fw-bold">Nhập thông tin thú cưng ngoài cơ sở</h5>
  <hr>
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

    <!-- Tentc + Tenloai -->
    <div class="col-md-8">
      <div class="mb-2">
        <label class="form-label fw-bold">Tên thú cưng</label>
        <input type="text" name="tentc" class="form-control border-secondary" required>
      </div>
      <div class="mb-2">
        <label class="form-label fw-bold">Giống</label>
        <input type="text" name="tenloai" class="form-control border-secondary" required>
      </div>
    </div>
  </div>

  <!-- Thông tin khác -->
  <div class="row mt-2">
    <div class="col">
      <label class="form-label fw-bold">Tuổi</label>
      <select name="tuoi" class="form-select border-secondary">
        <option value="Nhí">Nhí</option>
        <option value="Trẻ">Trẻ</option>
        <option value="Trưởng thành">Trưởng thành</option>
        <option value="Già">Già</option>
      </select>
    </div>
    <div class="col">
      <label class="form-label fw-bold">Giới tính</label>
      <select name="gioitinh" class="form-select border-secondary">
        <option value="Đực">Đực</option>
        <option value="Cái">Cái</option>
      </select>
    </div>
  </div>
  <div class="row mt-2">
    <div class="col">
      <label class="form-label fw-bold">Màu sắc</label>
      <input type="text" name="mausac" class="form-control border-secondary">
    </div>
    <div class="col">
      <label class="form-label fw-bold">Cân nặng (kg)</label>
      <input type="number" step="0.1" name="cannang" class="form-control border-secondary">
    </div>
  </div>
      <div class="mb-2">
      <label class="form-label fw-bold">Thông tin thêm</label>
      <input type="text" name="thongtin" class="form-control border-secondary">
    </div>
</div>