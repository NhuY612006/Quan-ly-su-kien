<?php
// Chinh duong dan neu thu muc components/includes cua ban khac
require_once __DIR__ . '/../includes/auth.php';

$role = currentRole();      // guest | user | organizer | admin
$user = currentUser();
$B    = BASE_URL;
?>
<div class="top_container">
    <div class="top_left">
        <i class="fa-solid fa-location-dot"></i> 208/5 3/2, Phường Ninh Kiều, TP Cần Thơ |
        <i class="fa-solid fa-phone"></i> 0772474933 |
        <i class="fa-solid fa-clock"></i> 8:00 - 22:00
    </div>

    <div class="top_right">
        <span id="user-area">
        <?php if ($role === 'guest'): ?>
            <a href="<?= $B ?>/pages/taikhoan/dangky.php">
                <i class="fa-solid fa-user-plus"></i> Đăng ký
            </a>
            <span> | </span>
            <a href="<?= $B ?>/pages/taikhoan/dangnhap.php">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
            </a>
        <?php else: ?>
            <div class="user-menu">
                <button type="button" class="user-menu-toggle" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-circle-user"></i>
                    <?= e($user['name']) ?>
                    <i class="fa-solid fa-caret-down"></i>
                </button>

                <div class="user-dropdown" hidden>
                    <a href="<?= $B ?>/pages/taikhoan/taikhoan.php">
                        <i class="fa-solid fa-user"></i> Tài khoản của tôi
                    </a>
                    <a href="<?= $B ?>/pages/taikhoan/doimatkhau.php">
                        <i class="fa-solid fa-key"></i> Đổi mật khẩu
                    </a>

                    <?php if ($role === 'user'): ?>
                        <a href="<?= $B ?>/pages/taikhoan/yeucau_organizer.php">
                            <i class="fa-solid fa-bullhorn"></i> Đăng ký làm người tổ chức
                        </a>
                    <?php elseif ($role === 'organizer'): ?>
                        <a href="<?= $B ?>/pages/sukien/quanly.php">
                            <i class="fa-solid fa-calendar-plus"></i> Chuyển sang trang đăng sự kiện
                        </a>
                    <?php elseif ($role === 'admin'): ?>
                        <a href="<?= $B ?>/pages/admin/sukien.php">
                            <i class="fa-solid fa-gear"></i> Trang quản trị
                        </a>
                    <?php endif; ?>

                    <form method="post" action="<?= $B ?>/pages/taikhoan/dangxuat.php">
                        <?= csrfField() ?>
                        <button type="submit">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        </span>
    </div>
</div>