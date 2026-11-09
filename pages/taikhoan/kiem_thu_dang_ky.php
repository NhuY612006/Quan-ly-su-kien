
<?php
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$db = getDB();
$id_nguoi_dung = (int) $_SESSION['user_id'];

try {
    // Lấy các sự kiện chưa bắt đầu và chưa bị hủy
    // Đồng thời đếm số người đã đăng ký thành công
    $stmt = $db->prepare("
        SELECT
            s.ID,
            s.TenSuKien,
            s.DiaDiem,
            s.ThoiGianBatDau,
            s.SoLuongToiDa,
            s.TrangThai,
            COUNT(d.ID) AS SoNguoiDaDangKy
        FROM sukien s
        LEFT JOIN dangkysukien d
            ON d.ID_SuKien = s.ID
            AND d.TrangThai = 'da_dang_ky'
        WHERE s.ThoiGianBatDau > NOW()
          AND s.TrangThai = 'sap_dien_ra'
        GROUP BY
            s.ID,
            s.TenSuKien,
            s.DiaDiem,
            s.ThoiGianBatDau,
            s.SoLuongToiDa,
            s.TrangThai
        ORDER BY s.ThoiGianBatDau ASC
    ");

    $stmt->execute();
    $danh_sach_su_kien = $stmt->fetchAll();

} catch (PDOException $e) {
    die('Lỗi truy vấn dữ liệu sự kiện: ' . e($e->getMessage()));
}

$pageTitle = 'Kiểm thử đăng ký sự kiện';

require __DIR__ . '/../../components/top.php';
require __DIR__ . '/../../components/header.php';
?>

<style>
    .test-container {
        max-width: 950px;
        margin: 35px auto;
        padding: 20px;
    }

    .test-container h1 {
        text-align: center;
        margin-bottom: 10px;
    }

    .test-note {
        background: #fff8df;
        border: 1px solid #f0d98c;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
    }

    .test-event {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 18px;
        background: #fff;
    }

    .test-event h3 {
        margin-top: 0;
    }

    .test-event p {
        margin: 8px 0;
    }

    .test-button {
        border: none;
        border-radius: 5px;
        padding: 10px 18px;
        background: #2563eb;
        color: white;
        cursor: pointer;
    }

    .test-button:hover {
        background: #1e40af;
    }

    .test-link {
        display: inline-block;
        margin-top: 15px;
        color: #2563eb;
    }
</style>

<div class="test-container">

    <h1>KIỂM THỬ ĐĂNG KÝ SỰ KIỆN</h1>

    <div class="test-note">
        <strong>Trang kiểm thử tạm thời</strong>
        <p>
            Trang này chỉ dùng để kiểm tra chức năng đăng ký.
            Không phải trang danh sách sự kiện chính thức.
        </p>
        <p>
            Sau khi đăng ký, bạn có thể vào trang Vé của tôi
            để kiểm tra mã vé và thử hủy đăng ký.
        </p>
    </div>

    <?php if (empty($danh_sach_su_kien)): ?>

        <p>Hiện không có sự kiện sắp diễn ra để kiểm thử.</p>

    <?php else: ?>

        <?php foreach ($danh_sach_su_kien as $su_kien): ?>

            <?php
                $so_cho_con_lai =
                    (int) $su_kien['SoLuongToiDa']
                    - (int) $su_kien['SoNguoiDaDangKy'];
            ?>

            <div class="test-event">

                <h3><?= e($su_kien['TenSuKien']) ?></h3>

                <p>
                    <strong>Mã sự kiện:</strong>
                    <?= (int) $su_kien['ID'] ?>
                </p>

                <p>
                    <strong>Địa điểm:</strong>
                    <?= e($su_kien['DiaDiem']) ?>
                </p>

                <p>
                    <strong>Thời gian:</strong>
                    <?= e($su_kien['ThoiGianBatDau']) ?>
                </p>

                <p>
                    <strong>Số lượng tối đa:</strong>
                    <?= (int) $su_kien['SoLuongToiDa'] ?>
                </p>

                <p>
                    <strong>Đã đăng ký:</strong>
                    <?= (int) $su_kien['SoNguoiDaDangKy'] ?>
                </p>

                <p>
                    <strong>Số chỗ còn lại:</strong>
                    <?= max(0, $so_cho_con_lai) ?>
                </p>

                <form
                    action="<?= BASE_URL ?>/backend/registrations/register.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="ID_SuKien"
                        value="<?= (int) $su_kien['ID'] ?>"
                    >

                    <?= csrfField() ?>

                    <button type="submit" class="test-button">
                        Thử đăng ký sự kiện này
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <a
        class="test-link"
        href="<?= BASE_URL ?>/pages/taikhoan/ve_cua_toi.php"
    >
        Đi đến trang Vé của tôi
    </a>

</div>

</main>

<?php require __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
