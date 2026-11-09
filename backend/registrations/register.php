
<?php
require_once __DIR__ . '/../../includes/auth.php';

// Bắt buộc đăng nhập
requireLogin();

// Chỉ xử lý khi form gửi bằng POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/sukien/sukien.php');
    exit;
}

// Kiểm tra CSRF theo chuẩn chung của nhóm
if (!csrfValid()) {
    die('Yêu cầu không hợp lệ. Vui lòng tải lại trang và thử lại.');
}

$id_nguoi_dung = (int) $_SESSION['user_id'];
$id_su_kien = (int) ($_POST['ID_SuKien'] ?? 0);

if ($id_su_kien <= 0) {
    header(
        'Location: ' . BASE_URL . '/pages/taikhoan/ve_cua_toi.php?error=' .
        urlencode('Sự kiện không hợp lệ.')
    );
    exit;
}

$db = getDB();

try {
    $db->beginTransaction();

    // Khóa dòng sự kiện để tránh đăng ký vượt số lượng
    $stmt = $db->prepare("
        SELECT ID, ThoiGianBatDau, SoLuongToiDa, TrangThai
        FROM sukien
        WHERE ID = ?
        FOR UPDATE
    ");
    $stmt->execute([$id_su_kien]);
    $su_kien = $stmt->fetch();

    if (!$su_kien) {
        throw new Exception('Không tìm thấy sự kiện.');
    }

    if ($su_kien['TrangThai'] === 'da_huy') {
        throw new Exception('Sự kiện đã bị hủy.');
    }

    if ($su_kien['TrangThai'] === 'da_ket_thuc') {
        throw new Exception('Sự kiện đã kết thúc.');
    }

    if (strtotime($su_kien['ThoiGianBatDau']) <= time()) {
        throw new Exception('Sự kiện đã bắt đầu, không thể đăng ký.');
    }

    // Kiểm tra người dùng đã đăng ký sự kiện này chưa
    $stmt = $db->prepare("
        SELECT ID, TrangThai
        FROM dangkysukien
        WHERE ID_NguoiDung = ?
          AND ID_SuKien = ?
        LIMIT 1
    ");
    $stmt->execute([$id_nguoi_dung, $id_su_kien]);
    $dang_ky_cu = $stmt->fetch();

    if ($dang_ky_cu && $dang_ky_cu['TrangThai'] === 'da_dang_ky') {
        throw new Exception('Bạn đã đăng ký sự kiện này rồi.');
    }

    // Đếm số lượt đăng ký còn hiệu lực
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM dangkysukien
        WHERE ID_SuKien = ?
          AND TrangThai = 'da_dang_ky'
    ");
    $stmt->execute([$id_su_kien]);
    $so_nguoi_da_dang_ky = (int) $stmt->fetchColumn();

    // Kiểm tra số lượng chỗ còn lại
    if ($so_nguoi_da_dang_ky >= (int) $su_kien['SoLuongToiDa']) {
        throw new Exception('Sự kiện đã hết chỗ.');
    }

    // Tạo mã vé mới
    $ma_ve = bin2hex(random_bytes(16));

    if ($dang_ky_cu) {
        // Nếu trước đây đã hủy thì cập nhật lại đăng ký
        $stmt = $db->prepare("
            UPDATE dangkysukien
            SET NgayDangKy = NOW(),
                TrangThai = 'da_dang_ky',
                MaVe = ?,
                TrangThaiCheckIn = 'chua_checkin',
                ThoiGianCheckIn = NULL
            WHERE ID = ?
        ");
        $stmt->execute([$ma_ve, $dang_ky_cu['ID']]);

    } else {
        // Nếu đăng ký lần đầu thì thêm bản ghi mới
        $stmt = $db->prepare("
            INSERT INTO dangkysukien
                (ID_NguoiDung, ID_SuKien, NgayDangKy,
                 TrangThai, MaVe, TrangThaiCheckIn)
            VALUES (?, ?, NOW(), 'da_dang_ky', ?, 'chua_checkin')
        ");
        $stmt->execute([
            $id_nguoi_dung,
            $id_su_kien,
            $ma_ve
        ]);
    }

    $db->commit();

    header('Location: ' . BASE_URL . '/pages/taikhoan/ve_cua_toi.php?registered=1');
    exit;

} catch (Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    header(
    'Location: ' . BASE_URL . '/pages/taikhoan/ve_cua_toi.php?error=' .
    urlencode($e->getMessage())
);
    exit;
}
