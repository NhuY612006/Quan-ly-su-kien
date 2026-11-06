
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tài khoản | Musicforlife</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="taikhoan.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <?php
    require_once __DIR__ . '/../../config/app.php';
    require_once ROOT_PATH . '/config/database.php';
    ?>
    <?php include __DIR__ . '/../../components/top.html'; ?>
    <?php include ROOT_PATH . '/components/header.php'; ?>

    <main class="main-content">

        <h1>Tài khoản của tôi</h1>

        <!-- PHẦN 1: THÔNG TIN TÀI KHOẢN -->
        <section class="account-info">

            <h2>Thông tin cá nhân</h2>

            <form action="" method="post">

                <div class="form-row">
                    <label for="name">Họ và tên</label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                    >
                </div>

                <div class="form-row">
                    <label for="phonenumber">Số điện thoại</label>

                    <input
                        type="text"
                        name="phonenumber"
                        id="phonenumber"
                    >
                </div>

                <div class="form-row">
                    <label for="Email">Email</label>

                    <input
                        type="email"
                        name="Email"
                        id="Email"
                    >
                </div>

                <div class="form-row">
                    <label for="DoB">Ngày tháng năm sinh</label>

                    <input
                        type="date"
                        name="DoB"
                        id="DoB"
                    >
                </div>

                <div class="form-button">
                    <button type="submit">
                        Cập nhật thông tin
                    </button>
                </div>

            </form>

        </section>


        <!-- PHẦN 2: SỰ KIỆN ĐÃ ĐĂNG KÝ -->
        <section class="account-events">

            <h2>Sự kiện đã đăng ký</h2>

            <table class="event-table">

                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sự kiện</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Mã vé</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="5">
                            Chưa có sự kiện đăng ký.
                        </td>
                    </tr>
                </tbody>

            </table>

        </section>

    </main>

    <?php include ROOT_PATH . '/components/footer.php'; ?>
    <script src="../../assets/js/components.js"></script>

</body>
</html>
