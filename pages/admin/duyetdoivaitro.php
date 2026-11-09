<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValid()) {
    $idYc     = (int)($_POST['id'] ?? 0);
    $hanhDong = $_POST['hanh_dong'] ?? '';

    if ($idYc > 0 && in_array($hanhDong, ['duyet', 'tu_choi'], true)) {
        try {
            $db->beginTransaction();

            // 1. Lấy yêu cầu đang chờ duyệt (khóa dòng để tránh duyệt 2 lần)
            $st = $db->prepare("SELECT ID_NguoiDung FROM yeucau_organizer WHERE ID = ? AND TrangThai = 'cho_duyet' FOR UPDATE");
            $st->execute([$idYc]);
            $row = $st->fetch();

            if ($row) {
                // 2. Xác định trạng thái mới
                $trangThai = $hanhDong === 'duyet' ? 'da_duyet' : 'tu_choi';

                // 3. Cập nhật yêu cầu
                $db->prepare("UPDATE yeucau_organizer
                              SET TrangThai = ?, NgayXuLy = NOW(), ID_NguoiDuyet = ?
                              WHERE ID = ?")
                   ->execute([$trangThai, currentUser()['id'], $idYc]);

                // 4. Nếu duyệt thì đổi vai trò (chỉ với user thường)
                if ($trangThai === 'da_duyet') {
                    $db->prepare("UPDATE nguoidung SET VaiTro = 'organizer' WHERE ID = ? AND VaiTro = 'user'")
                    ->execute([$row['ID_NguoiDung']]);

                    guiThongBao($db, (int)$row['ID_NguoiDung'],
                        'Yêu cầu làm nhà tổ chức đã được duyệt',
                        'Chúc mừng! Tài khoản của bạn đã trở thành nhà tổ chức. Bạn có thể bắt đầu đăng sự kiện.',
                        'organizer_duyet');
                } else {
                    guiThongBao($db, (int)$row['ID_NguoiDung'],
                        'Yêu cầu làm nhà tổ chức bị từ chối',
                        'Rất tiếc, yêu cầu của bạn chưa được chấp nhận. Bạn có thể gửi lại yêu cầu mới với thông tin đầy đủ hơn.',
                        'organizer_tuchoi');
                }
            }
            $db->commit();
        } catch (Throwable $ex) {
            if ($db->inTransaction()) $db->rollBack();
                exit('Loi xu ly yeu cau: ' . $ex->getMessage() . ' (dong ' . $ex->getLine() . ' - ' . basename($ex->getFile()) . ')');
        }
    }
    header('Location: ' . BASE_URL . '/pages/admin/duyetdoivaitro.php');
    exit;
}

$ds = $db->query("
    SELECT y.*, n.HoTen, n.Email
    FROM yeucau_organizer y
    JOIN nguoidung n ON n.ID = y.ID_NguoiDung
    WHERE y.TrangThai = 'cho_duyet'
    ORDER BY y.NgayGui ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8">
<title>Duyệt nhà tổ chức</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../pages/taikhoan/taikhoan.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <?php require __DIR__ . '/../../components/top.php';
    require __DIR__ . '/../../components/header.php'; ?>
    <main class="main-content"> 
        <h2>Yêu cầu làm nhà tổ chức đang chờ duyệt</h2>
        <?php if (!$ds): ?>
            <p>Hiện không có yêu cầu nào.</p>
        <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>Người dùng</th><th>Email</th><th>Giới thiệu và lý do</th><th>Ngày gửi</th><th>Xử lý</th></tr>
            <?php foreach ($ds as $r): ?>
            <tr>
            <td><?= e($r['HoTen']) ?></td>
            <td><?= e($r['Email']) ?></td>
            <td><?= nl2br(e($r['LyDo'])) ?></td>
            <td><?= e($r['NgayGui']) ?></td>
            <td>
                <form method="post">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$r['ID'] ?>">
                <button name="hanh_dong" value="duyet">Duyệt</button>
                <button name="hanh_dong" value="tu_choi">Từ chối</button>
                </form>
            </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </main>
    <script src="../../assets/js/components.js"></script>
    <?php require __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>