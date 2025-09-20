
-- Tạo CSDL
CREATE DATABASE IF NOT EXISTS pet_db;
USE pet_db;

-- Bảng TAIKHOAN
CREATE TABLE TAIKHOAN (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    vaitro ENUM('admin', 'khachhang') NOT NULL,
    hoten VARCHAR(50) NOT NULL,
    tuoi INT,
    gioitinh VARCHAR(10),
    email VARCHAR(50),
    sdt VARCHAR(20),
    diachi VARCHAR(50)
);

-- Bảng LOAITHUCUNG
CREATE TABLE LOAITHUCUNG_COSO (
    loaics_id INT PRIMARY KEY AUTO_INCREMENT,
    tenloai VARCHAR(100) NOT NULL
);

-- Bảng THUCUNG
CREATE TABLE THUCUNG_COSO (
    petcs_id INT PRIMARY KEY AUTO_INCREMENT,
    tentc VARCHAR(50) NOT NULL,
    loaics_id INT NOT NULL,
    tuoi VARCHAR(20),
    gioitinh VARCHAR(10),
    mausac VARCHAR(50),
    cannang FLOAT,
    tinhtrang VARCHAR(100),
    ngaycuuho DATE,
    anh VARCHAR(255),
    thongtin VARCHAR(255),
    ghichu VARCHAR(255),
    FOREIGN KEY (loaics_id) REFERENCES LOAITHUCUNG_COSO(loaics_id)
);

-- Bảng DONNHANNUOI
CREATE TABLE DONNHANNUOI (
    donnn_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    petcs_id INT NOT NULL,
    trangthai VARCHAR(100),
    phanhoi VARCHAR(255),
    ngaygui DATETIME,
    ngayduyet DATETIME,
    FOREIGN KEY (user_id) REFERENCES TAIKHOAN(user_id),
    FOREIGN KEY (petcs_id) REFERENCES THUCUNG_COSO(petcs_id)
);

-- Bảng DONCHAMSOC
CREATE TABLE DONCHAMSOC (
  doncs_id   INT AUTO_INCREMENT PRIMARY KEY,
  kh_id      INT NOT NULL,   -- user_id của KH
  admin_id   INT NOT NULL,   -- user_id của admin
  petcs_id     INT NULL,
  petncs_id     INT NULL,
  trangthai     VARCHAR(255),
  ngaygui    DATETIME,
  ngayhoanthanh    DATETIME,

  FOREIGN KEY (kh_id)   REFERENCES TAIKHOAN(user_id),
  FOREIGN KEY (admin_id)REFERENCES TAIKHOAN(user_id),
  FOREIGN KEY (petcs_id)  REFERENCES THUCUNG_COSO(petcs_id)
  FOREIGN KEY (petncs_id)  REFERENCES THUCUNG_NGOAICOSO(petncs_id)
);
-- Bảng TINHTRANGPET
CREATE TABLE TINHTRANGPET (
  ttpet_id INT AUTO_INCREMENT PRIMARY KEY,
  doncs_id        INT NOT NULL,
  tinhtrang       VARCHAR(100) NOT NULL,       -- vd: 'tỉa lông'
  trangthai       VARCHAR(255),

  FOREIGN KEY (doncs_id) REFERENCES DONCHAMSOC(doncs_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Bảng LOAITHUCUNG_NGOAICOSO
CREATE TABLE LOAITHUCUNG_NGOAICOSO (
    loaincs_id INT PRIMARY KEY AUTO_INCREMENT,
    tenloai VARCHAR(100) NOT NULL
);

-- Bảng THUCUNG_NGOAICOSO
CREATE TABLE THUCUNG_NGOAICOSO (
    petncs_id INT PRIMARY KEY AUTO_INCREMENT,
    tentc VARCHAR(50) NOT NULL,
    loaincs_id INT NOT NULL,
    tuoi VARCHAR(20),
    gioitinh VARCHAR(10),
    mausac VARCHAR(50),
    cannang FLOAT,
    anh VARCHAR(255),
    thongtin VARCHAR(255),
    FOREIGN KEY (loaincs_id) REFERENCES LOAITHUCUNG_NGOAICOSO(loaincs_id)
);

-- Bảng THONGKE
CREATE TABLE THONGKE (
    thongke_id INT PRIMARY KEY AUTO_INCREMENT,
    petcs_id INT NOT NULL,
    ngaycuuho DATE,
    ngayduyet DATE,
    FOREIGN KEY (petcs_id) REFERENCES THUCUNG_COSO(petcs_id)
);
