<?php
require_once __DIR__ . '/../includes/auth.php';
$role = currentRole();          // guest | user | organizer | admin
$user = currentUser();
$B = BASE_URL;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Music4Life') ?></title>
    <link rel="stylesheet" href="<?= $B ?>/assets/css/style.css">
    <script src="<?= $B ?>/assets/js/main.js" defer></script>
</head>
<body>
<header class="site-header">
    <nav class="page-header__navbar" id="mainNav">

        <?php if ($role === 'user' || $role === 'organizer'): ?>
            <a class="page-header__link" href="<?= $B ?>/pages/sukien/sukien.php">Sự kiện</a>
            <a class="page-header__link" href="<?= $B ?>/pages/muave/muave.php">Mua vé</a>
            <a class="page-header__link" href="<?= $B ?>/pages/taikhoan/taikhoan.php">Tài khoản</a>

        <?php endif; ?>

        <?php if ($role === 'organizer'): ?>
            <a class="page-header__link" href="<?= $B ?>/pages/sukien/quanly.php">Sự kiện của tôi</a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
            <a class="page-header__link" href="<?= $B ?>/pages/admin/sukien.php">Quản lý sự kiện</a>
            <a class="page-header__link" href="<?= $B ?>/pages/admin/nguoidung.php">Quản lý người dùng</a>
            <a class="page-header__link" href="<?= $B ?>/pages/admin/duyet_organizer.php">Duyệt người tổ chức</a>
        <?php endif; ?>
    </nav>

    <div class="auth-nav">
    <?php if ($role === 'guest'): ?>
    <?php else: ?>
        <div class="user-menu" id="userMenu">
            <!-- <button type="button" class="user-menu-toggle" id="userMenuToggle"
                    aria-haspopup="true" aria-expanded="false">
                <?= e($user['name']) ?> ▾
            </button> -->

            <div class="user-dropdown" id="userDropdown" hidden>
                <a class="page-header__link" href="<?= $B ?>/pages/taikhoan/taikhoan.php">Tài khoản của tôi</a>
                <a class="page-header__link" href="<?= $B ?>/pages/taikhoan/doimatkhau.php">Đổi mật khẩu</a>

                <?php if ($role === 'user'): ?>
                    <a class="page-header__link" href="<?= $B ?>/pages/taikhoan/yeucau_organizer.php">Đăng ký làm người tổ chức</a>
                <?php elseif ($role === 'organizer'): ?>
                    <a class="page-header__link" href="<?= $B ?>/pages/sukien/quanly.php">Chuyển sang trang đăng sự kiện</a>
                <?php elseif ($role === 'admin'): ?>
                    <a class="page-header__link" href="<?= $B ?>/pages/admin/sukien.php">Trang quản trị</a>
                <?php endif; ?>

                <form method="post" action="<?= $B ?>/pages/taikhoan/dangxuat.php">
                    <?= csrfField() ?>
                    <button type="submit">Đăng xuất</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
    </div>
</header>
<main class="container">