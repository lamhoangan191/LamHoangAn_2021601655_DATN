-- -- Dữ liệu mẫu cho bảng TAIKHOAN
-- INSERT INTO TAIKHOAN (username, password, vaitro, hoten, tuoi, gioitinh, email, sdt, diachi) VALUES 
-- ('a1', '$2y$10$4Hq3tya3Y1hIg3YE.FXTd.X.6XJQ4IKEXz15YNlCMgo4ZPeU4fNjq', 'admin', 'Admin1', '', '', 'admin1@example.com', '0987654321', ''),
-- ('a2', '$2y$10$4Hq3tya3Y1hIg3YE.FXTd.X.6XJQ4IKEXz15YNlCMgo4ZPeU4fNjq', 'admin', 'Admin2', '', '', 'admin2@example.com', '0912345678', '');

-- Dữ liệu mẫu cho bảng LOAITHUCUNG
INSERT INTO LOAITHUCUNG (tenloai) VALUES
('Mèo ta'),
('Chó ta'),
('Chó lai'),
('Chó phốc'),
('Mèo tây'),
('Chó tây');

-- Dữ liệu mẫu cho bảng THUCUNG
INSERT INTO THUCUNG (tentc, species_id, tuoi, gioitinh, mausac, cannang, tinhtrang, ngaycuuho, anh, thongtin, ghichu) VALUES
('Fami', 1, 'nhí', 'cái', 'trắng đen', 0.6, 'Khỏe mạnh', '2025-07-01', 'images/cats/9.jpeg', 'Đã tiêm phòng', 'Bị bỏ khi còn mèo sữa, nhóm gửi đi ghép đàn'),
('Pháo', 2, 'già', 'đực', 'trắng xám', 15.5, 'Cần chăm sóc', '2025-06-15', 'images/dogs/6.jpeg', 'Rất thân thiện', 'Được cứu ở Hà Nội'),
('Bully', 3, 'trưởng thành', 'đực', 'nâu đỏ', 20, 'Khỏe mạnh', '2025-08-01', 'images/dogs/8.jpeg', 'Đã triệt sản và tiêm phòng', 'Được cứu kịp thời khi phát hiện bị dính bả'),
('Misa', 1, 'trẻ', 'cái', 'xám tro', 2.5, 'Cần điều trị', '2025-05-20', 'images/cats/3.jpeg', 'Rất hiền lành', 'Được cứu khi bị bỏ rơi ngoài chợ'),
('Lucky', 1, 'trưởng thành', 'đực', 'trắng cam', 3, 'Khỏe mạnh', '2025-07-25', 'images/cats/8.jpeg', 'Thân thiện với trẻ em', 'Chạy lạc từ Hà Nam, được người dân báo tin'),
('Lily', 1, 'trưởng thành', 'cái', 'tam thể', 3.1, 'Khỏe mạnh', '2025-05-16', 'images/cats/4.jpeg', 'Đã triệt sản, rất hiền lành', 'Đi lạc từ Nam Định, được người dân cứu hộ'),
('Bo', 4, 'trưởng thành', 'đực', 'nâu', 7, 'Đang bị ốm', '2025-01-02', 'images/dogs/3.jpeg', 'Rất quấn người, hiếu động', 'Đi lạc không ai nhận là chủ'),
('Nabi', 5, 'trẻ', 'cái', 'trắng xám', 1.5, 'Khỏe mạnh', '2025-03-17', 'images/cats/6.jpeg', 'Chế độ ăn hạt và pate', 'Bé bị liệt 2 chân sau, không chữa khỏi được'),
('Mít', 6, 'trẻ', 'cái', 'trắng', 5, 'Khỏe mạnh', '2025-04-23', 'images/dogs/7.jpeg', 'Đi vệ sinh đúng chỗ', 'Bị bỏ ở ngoài đường cùng các anh chị em khác'),
('Nick', 2, 'trẻ', 'đực', 'vàng', 10.4, 'Khỏe mạnh', '2025-05-14', 'images/dogs/10.jpeg', 'Đã tiêm dại, thân thiện với người', 'Chủ cũ không nuôi nữa, gửi trung tâm');