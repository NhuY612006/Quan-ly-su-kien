-- Cơ sở dữ liệu cho trang Sự kiện (MySQL / MariaDB)
-- Chạy file này trong database của bạn (phpMyAdmin > Import, hoặc: mysql -u user -p ten_db < sukien.sql)

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS events (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title        VARCHAR(200)  NOT NULL,
    category     VARCHAR(50)   NOT NULL,
    event_date   DATE          NOT NULL,
    event_time   TIME          NOT NULL,
    location     VARCHAR(200)  NOT NULL,
    description  TEXT          NOT NULL,
    capacity     INT UNSIGNED  NOT NULL DEFAULT 0,
    is_published TINYINT(1)    NOT NULL DEFAULT 1,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_events_date (event_date),
    KEY idx_events_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_registrations (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id   INT UNSIGNED NOT NULL,
    name       VARCHAR(80)  NOT NULL,
    email      VARCHAR(150) NOT NULL,
    phone      VARCHAR(15)  NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_event_email (event_id, email),   -- mỗi email chỉ đăng ký 1 lần cho 1 sự kiện
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu: các sự kiện âm nhạc nổi bật cuối 2026 tại Việt Nam
-- Nguồn: VnExpress (3/11/2026), VTV (28/10/2026), Báo Gia Lai, Ticketgo, Blog Thơ Fleur.
-- Ngày + địa điểm + tên chương trình lấy theo tin tức. Giờ diễn (trừ đêm Thái Nguyên) và sức chứa
-- khi báo chí không nêu là giá trị giả định để demo; hãy đối chiếu với nhà tổ chức trước khi dùng thật.
INSERT INTO events (title, category, event_date, event_time, location, description, capacity) VALUES
('Tự hào là người Việt Nam 2026 – Nối vòng tay lớn', 'Đại nhạc hội', '2026-11-01', '20:10', 'Sân vận động Thái Nguyên', 'Concert quốc gia hướng tới Ngày hội Đại đoàn kết toàn dân tộc, phục vụ khoảng 30.000 khán giả.', 30000),
('HIEUTHUHAI – Mắt Nhắm Mắt Mở (Đêm 3)', 'Concert', '2026-11-01', '19:00', 'Sân Bắc, Trung tâm Triển lãm Việt Nam (VEC), Hà Nội', 'Đêm thứ ba trong chuỗi concert Mắt Nhắm Mắt Mở của HIEUTHUHAI.', 20000),
('Concert Phương Mỹ Chi', 'Concert', '2026-11-07', '19:00', 'Trung tâm Triển lãm Việt Nam (VEC), Hà Nội', 'Đêm diễn của Phương Mỹ Chi, nổi bật với cách đưa chất liệu truyền thống vào trình diễn hiện đại.', 20000),
('Diễm Xưa giữa Mùa thu Hà Nội', 'Đêm nhạc', '2026-11-13', '20:00', 'Hà Nội', 'Đêm nhạc về âm nhạc Trịnh Công Sơn giữa mùa thu Hà Nội.', 800),
('Tinh Hà Say Hi – Hà Nội (Đêm 1)', 'Concert', '2026-11-14', '19:00', 'Trung tâm Triển lãm Việt Nam (VEC), Hà Nội', 'Concert quy tụ các nghệ sĩ từ chương trình truyền hình. Hai đêm tại TP.HCM hồi tháng 10 thu hút khoảng 80.000 khán giả.', 30000),
('Tinh Hà Say Hi – Hà Nội (Đêm 2)', 'Concert', '2026-11-15', '19:00', 'Trung tâm Triển lãm Việt Nam (VEC), Hà Nội', 'Đêm thứ hai của Tinh Hà Say Hi tại Hà Nội.', 30000),
('Phan Mạnh Quỳnh – Bon Nghiêm – 14 Casper | Live in DaLat', 'Đêm nhạc', '2026-11-14', '19:30', 'Đà Lạt, Lâm Đồng', 'Đêm nhạc ngoài trời của ba nghệ sĩ trẻ tại thành phố cao nguyên.', 3000),
('Mỹ Tâm – See The Light 2', 'Concert', '2026-11-21', '19:30', 'Khu Vạn Phúc, Hà Nội', 'Live concert quy mô lớn của Mỹ Tâm. Một đợt 10.000 vé trước đó đã hết chỉ sau 5 phút mở bán.', 10000),
('Ký ức Hà Nội – Đêm 2', 'Đêm nhạc', '2026-11-22', '20:00', 'Hà Nội', 'Đêm nhạc của Thanh Lam, Mỹ Linh, Ngọc Anh và Hà Trần.', 1500),
('Concert chào mừng Ngày Văn hóa Việt Nam', 'Đại nhạc hội', '2026-11-24', '19:30', 'Sân vận động Mỹ Đình, Hà Nội', 'Chương trình nghệ thuật và concert mừng Ngày Văn hóa Việt Nam, quy mô khoảng 60.000 khán giả, vé mời dự kiến miễn phí.', 60000),
('SKYNOTE 2026 – The Reflection (Day 2) | Quốc Thiên', 'Concert', '2026-11-29', '19:30', 'Trung tâm Triển lãm Việt Nam (VEC), Hà Nội', 'Live concert của Quốc Thiên cùng UNISON Orchestra và nhóm bè Cadillac.', 8000),
('Anh trai vượt ngàn chông gai 2026 – Hưng Yên (Đêm 1)', 'Concert', '2026-12-05', '19:00', 'Hưng Yên', 'Sau hai đêm thu hút gần 100.000 khán giả tại TP.HCM hồi tháng 10, chương trình ra Hưng Yên.', 40000),
('Anh trai vượt ngàn chông gai 2026 – Hưng Yên (Đêm 2)', 'Concert', '2026-12-06', '19:00', 'Hưng Yên', 'Đêm thứ hai của chương trình tại Hưng Yên.', 40000);