
<?php
require_once __DIR__ . '/../includes/auth.php';

$role = currentRole();
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

        <a class="page-header__logo-link"
           href="<?= $B ?>/pages/sukien/sukien.php">
            <img
                class="page-header__logo-link img"
                src="<?= $B ?>/assets/images/Music%20for%20life.png"
                alt="Music for life"
            >
        </a>

        <?php if (in_array($role, ['user', 'organizer'], true)): ?>

            <a class="page-header__link"
               href="<?= $B ?>/pages/sukien/sukien.php">
                Sự kiện
            </a>

            <a class="page-header__link"
               href="<?= $B ?>/pages/muave/muave.php">
                Mua vé
            </a>

            <a class="page-header__link"
               href="<?= $B ?>/pages/taikhoan/taikhoan.php">
                Tài khoản
            </a>

            <a class="page-header__link"
               href="<?= $B ?>/pages/taikhoan/ve_cua_toi.php">
                Vé của tôi
            </a>

        <?php elseif ($role === 'guest'): ?>

            <a class="page-header__link"
               href="<?= $B ?>/pages/sukien/sukien.php">
                Sự kiện
            </a>

        <?php endif; ?>

        <?php
        // Các trang quản lý dành cho organizer/admin
        // sẽ được thêm lại khi nhóm hoàn thiện các trang tương ứng.
        ?>

    </nav>
</header>

<main class="container">
