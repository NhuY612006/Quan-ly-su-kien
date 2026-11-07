<?php
require_once __DIR__ . '/../../config/database.php';

$errors = [];
$old = ['ho_ten' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $errors[] = 'Phiên làm việc không hợp lệ, vui lòng thử lại.';
    }

    $ho_ten   = trim($_POST['ho_ten'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $old = compact('ho_ten', 'email');

    if ($ho_ten === '') $errors[] = 'Vui lòng nhập họ tên.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu tối thiểu 6 ký tự.';
    if ($password !== $confirm) $errors[] = 'Mật khẩu xác nhận không khớp.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT ID FROM NguoiDung WHERE Email = :e LIMIT 1');
        $stmt->execute([':e' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email này đã được đăng ký.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO NguoiDung (HoTen, Email, MatKhau) VALUES (:n, :e, :h)');
        $stmt->execute([
            ':n' => $ho_ten,
            ':e' => $email,
            ':h' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        header('Location: dangnhap.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký - Music4Life</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div id="top"></div>
<div id="header"></div>

<main class="auth-box">
    <h2>Đăng ký tài khoản</h2>

    <?php foreach ($errors as $err): ?>
        <p class="error"><?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <label>Họ và tên</label>
        <input type="text" name="ho_ten" value="<?= htmlspecialchars($old['ho_ten']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>

        <label>Mật khẩu</label>
        <input type="password" name="password" required>

        <label>Nhập lại mật khẩu</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>
</main>

<div id="footer"></div>
<script src="../../assets/js/components.js"></script>
<script src="../../assets/js/main.js"></script>
</body>
</html>