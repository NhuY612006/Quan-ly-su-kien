<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../pages/taikhoan/mail.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL . 'pages/sukien/sukien.php'); exit; }

$err = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        $err = 'Phiên làm việc không hợp lệ, vui lòng thử lại.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err = 'Email không hợp lệ.';
        } else {
            $db = getDB();
            $st = $db->prepare('SELECT ID, HoTen FROM nguoidung WHERE Email = ? AND TrangThai = "hoat_dong"');
            $st->execute([$email]);
            $u = $st->fetch();

            if ($u) {
                // Gioi han: moi 60 giay chi tao 1 ma
                $c = $db->prepare('SELECT 1 FROM datlai_matkhau WHERE ID_NguoiDung = ? AND NgayTao > (NOW() - INTERVAL 60 SECOND)');
                $c->execute([$u['ID']]);
                if (!$c->fetch()) {
                    // Vo hieu cac ma cu
                    $db->prepare('UPDATE datlai_matkhau SET DaDung = 1 WHERE ID_NguoiDung = ?')->execute([$u['ID']]);

                    $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $db->prepare('INSERT INTO datlai_matkhau (ID_NguoiDung, MaXacNhan, HetHan)
                                  VALUES (?, ?, NOW() + INTERVAL 10 MINUTE)')
                       ->execute([$u['ID'], password_hash($code, PASSWORD_DEFAULT)]);

                    sendMail($email, 'Mã xác nhận đặt lại mật khẩu Music4Life',
                        "Xin chào {$u['HoTen']},\n\nMã xác nhận của bạn là: $code\nMã có hiệu lực trong 10 phút.\n\nNếu bạn không yêu cầu, hãy bỏ qua email này.");
                }
            }
            // Luon chuyen sang buoc 2, khong tiet lo email co ton tai hay khong
            $_SESSION['reset_email'] = $email;
            header('Location: datlaimatkhau.php');
            exit;
        }
    }
}

$pageTitle = 'Quên mật khẩu - Music4Life';
require __DIR__ . '/../../components/top.php';
require __DIR__ . '/../../components/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/pages/taikhoan/taikhoan.css">
<div class="main-content">
    <form class="auth-card" method="post">
        <h2 class="auth-title">Quên mật khẩu</h2>
        <p>Nhập email đã đăng ký, chúng tôi sẽ gửi mã xác nhận gồm 6 chữ số.</p>
        <?php if ($err): ?><p class="alert error"><?= e($err) ?></p><?php endif; ?>
        <?= csrfField() ?>
        <div class="form-row">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div class="form-button">
            <button class="btn" type="submit">Gửi mã</button>
        </div>
        <p class="form-link"><a href="dangnhap.php">Quay lại đăng nhập</a></p>
    </form>
</div>
<script src="../../assets/js/components.js"></script>
<?php require __DIR__ . '/../../components/footer.php'; ?>
