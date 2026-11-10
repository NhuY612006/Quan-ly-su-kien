<footer class="page-footer">
    <a class="page-footer__logo" href="<?= BASE_URL ?>/pages/sukien/sukien.php">
        <img src="../../assets/images/Music%20for%20life.png" alt="Music for life">
    </a>
    
    <div class="page-footer__columns">
        
        <div class="page-footer__col">
            <h3>LIÊN HỆ</h3>
            <p><i class="fa-solid fa-location-dot"></i> 208/5 3/2, Ninh Kiều, Cần Thơ</p>
            <p><i class="fa-solid fa-phone"></i> 0772474933</p>
            <p><i class="fa-solid fa-envelope"></i> musicforlife@gmail.com</p>
        </div>

        <div class="page-footer__col">
            <h3>LIÊN KẾT NHANH</h3>
            <?php if ($role === 'user' || $role === 'organizer'): ?>
            <a class="page-header__link" href="<?= $B ?>/pages/sukien/sukien.php">Sự kiện</a>
            <a class="page-header__link" href="<?= $B ?>/pages/muave/muave.php">Mua vé</a>
            <a class="page-header__link" href="<?= $B ?>/pages/taikhoan/ve_cua_toi.php">Vé của tôi</a>
            <a class="page-header__link" href="<?= $B ?>/pages/taikhoan/taikhoan.php">Tài khoản</a>

            <?php endif; ?>
            <?php if ($role === 'guest'): ?>
                <a class="page-header__link" href="<?= $B ?>/pages/sukien/sukien.php">Sự kiện</a>
            <?php endif; ?>
            <?php if ($role === 'organizer'): ?>
                <a class="page-header__link" href="<?= $B ?>/pages/sukien/quanly.php">Sự kiện của tôi</a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a class="page-header__link" href="<?= $B ?>/pages/admin/sukien.php">Quản lý sự kiện</a>
                <a class="page-header__link" href="<?= $B ?>/pages/admin/nguoidung.php">Quản lý người dùng</a>
                <a class="page-header__link" href="<?= $B ?>/pages/admin/duyetdoivaitro.php">Duyệt người tổ chức</a>
            <?php endif; ?>
        </div>

        <div class="page-footer__col">
            <h3>KẾT NỐI</h3>
            <div class="page-footer__social-icons">
                <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <i class="fa-brands fa-facebook"></i>
                </a>

                <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>

                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="page-footer__legal">
        <p>© 2026 Music For Life.</p>
    </div>
</footer>