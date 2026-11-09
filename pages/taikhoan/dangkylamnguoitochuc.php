<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();   // đã đồng bộ user_role từ DB

$user = currentUser();
$db   = getDB();
$loi = ''; $ok = '';

if (currentRole() === 'user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        $loi = 'Phien lam viec khong hop le, vui long thu lai.';
    } else {
        $lyDo = trim($_POST['ly_do'] ?? '');

        $st = $db->prepare("SELECT 1 FROM yeucau_organizer WHERE ID_NguoiDung = ? AND TrangThai = 'cho_duyet'");
        $st->execute([$user['id']]);

        if ($st->fetch()) {
            $loi = 'Ban da co mot yeu cau dang cho duyet.';
        } elseif ($lyDo === '') {
            $loi = 'Vui long nhap ly do.';
        } else {
            $st = $db->prepare("INSERT INTO yeucau_organizer (ID_NguoiDung, LyDo) VALUES (?, ?)");
            $st->execute([$user['id'], $lyDo]);
            $ok = 'Da gui yeu cau. Vui long cho admin duyet.';
        }
    }
}

// Yêu cầu gần nhất của user này
$st = $db->prepare("SELECT * FROM yeucau_organizer WHERE ID_NguoiDung = ? ORDER BY ID DESC LIMIT 1");
$st->execute([$user['id']]);
$yc = $st->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8">
<title>Đăng ký làm nhà tổ chức</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/pages/taikhoan/taikhoan.css">
<link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <?php require __DIR__ . '/../../components/top.php';
    require __DIR__ . '/../../components/header.php'; ?>
    <main class="main-content">
        <h2>Đăng ký làm nhà tổ chức sự kiện</h2>

        <?php if ($ok):  ?><p style="color:green"><?= e($ok) ?></p><?php endif; ?>
        <?php if ($loi): ?><p style="color:red"><?= e($loi) ?></p><?php endif; ?>

        <?php if (currentRole() !== 'user'): ?>
            <p>Tài khoản của bạn hiện là <b><?= e(currentRole()) ?></b>, không cần đăng ký thêm.</p>

        <?php elseif ($yc && $yc['TrangThai'] === 'cho_duyet'): ?>
            <p>Yêu cầu gửi lúc <?= e($yc['NgayGui']) ?> đang <b>chờ admin duyệt</b>.</p>

        <?php else: ?>
            <form method="post">
                <?= csrfField() ?>
                <label>Giới thiệu về bạn / tổ chức và lý do muốn tổ chức sự kiện</label><br>
                <textarea 
                    class="form-textarea"
                    name="ly_do" 
                    rows="10" required>
                </textarea>
                <br>
                <button type="submit">Gửi yêu cầu</button>
            </form>
        <?php endif; ?>
    </main>
    <script src="../../assets/js/components.js"></script>
    <?php require __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>