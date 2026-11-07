<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mua vé sự kiện</title>

    <link rel="stylesheet" href="muave.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <?php
    require_once __DIR__ . '/../../config/app.php';
    require_once ROOT_PATH . '/config/database.php';
    ?>
    <?php include __DIR__ . '/../../components/top.php'; ?>
    <?php include ROOT_PATH . '/components/header.php'; ?>
    <main class="container">

        <h1>Mua vé sự kiện</h1>

        <p class="subtitle">
            Lựa chọn sự kiện và đặt vé tham dự ngay hôm nay.
        </p>

        <div class="content">

            <!-- Thông tin sự kiện -->
            <section class="event-info">

                <img
                    src="https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=900"
                    alt="Sự kiện âm nhạc"
                    class="event-image"
                >

                <div class="event-detail">

                    <span class="category">Âm nhạc</span>

                    <h2>Đêm nhạc hội mùa hè 2026</h2>

                    <p>
                        Một đêm nhạc sôi động với sự tham gia
                        của nhiều ca sĩ và nghệ sĩ nổi tiếng.
                    </p>

                    <div class="detail-item">
                        <span>📅 Ngày tổ chức:</span>
                        <strong>20/12/2026</strong>
                    </div>

                    <div class="detail-item">
                        <span>🕒 Thời gian:</span>
                        <strong>18:00 - 22:00</strong>
                    </div>

                    <div class="detail-item">
                        <span>📍 Địa điểm:</span>
                        <strong>Cần Thơ</strong>
                    </div>

                </div>
            </section>

            <!-- Form mua vé -->
            <section class="booking">

                <h2>Thông tin đặt vé</h2>

                <form action="#" method="post" id="bookingForm">

                    <div class="form-group">

                        <label for="event">Chọn sự kiện</label>

                        <select id="event" name="ID_SuKien" required>
                            <option value="1">
                                Đêm nhạc hội mùa hè 2026
                            </option>

                            <option value="2">
                                Hội thảo công nghệ 2026
                            </option>

                            <option value="3">
                                Ngày hội việc làm
                            </option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label for="MaVe">Loại vé</label>

                        <select id="MaVe" name="MaVe" required>
                            <option value="1" data-price="200000">
                                Vé thường - 200.000 VNĐ
                            </option>

                            <option value="2" data-price="500000">
                                Vé VIP - 500.000 VNĐ
                            </option>

                            <option value="3" data-price="1000000">
                                Vé VVIP - 1.000.000 VNĐ
                            </option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label for="SoLuong">Số lượng vé</label>

                        <input
                            type="number"
                            id="SoLuong"
                            name="SoLuong"
                            min="1"
                            max="10"
                            value="1"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="name">Họ và tên người mua</label>

                        <input
                            type="text"
                            id="name"
                            name="HoTen"
                            placeholder="Nhập họ và tên"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="email">Email</label>

                        <input
                            type="email"
                            id="email"
                            name="Email"
                            placeholder="Nhập email"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="phone">Số điện thoại</label>

                        <input
                            type="tel"
                            id="phone"
                            name="SoDienThoai"
                            placeholder="Nhập số điện thoại"
                            required
                        >

                    </div>

                    <!-- Tổng tiền -->
                    <div class="total">

                        <span>Tổng tiền dự kiến</span>

                        <strong id="totalPrice">200.000 VNĐ</strong>

                    </div>

                    <button type="submit" class="btn-buy">
                        Xác nhận mua vé
                    </button>

                    <p class="note">
                        Vui lòng kiểm tra thông tin trước khi xác nhận.
                    </p>

                </form>

            </section>

        </div>

    </main>

    <script src="../../assets/js/components.js"></script>
    <script src="muave.js"></script>

</body>
</html>
```